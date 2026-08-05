<?php

require __DIR__ . "/../../middleware/admin.php";
require __DIR__ . "/../../config/conexion.php";
require __DIR__ . "/../../helpers/reportesHelpers.php";

$erroresReporte = [];
$periodo = $_GET["periodo"] ?? "mensual";

if (!in_array($periodo, ["diario", "semanal", "mensual", "personalizado"], true)) {
    $periodo = "mensual";
}

[$fechaInicialBase, $fechaFinalBase] = rangoPorPeriodo($periodo === "personalizado" ? "mensual" : $periodo);
$fechaInicial = trim($_GET["fecha_inicial"] ?? $fechaInicialBase);
$fechaFinal = trim($_GET["fecha_final"] ?? $fechaFinalBase);
$idSensor = filter_input(INPUT_GET, "sensor", FILTER_VALIDATE_INT) ?: 0;
$tipoMedicion = trim($_GET["tipo"] ?? "");
$estadoFiltro = trim($_GET["estado"] ?? "");

if (!fechaValida($fechaInicial) || !fechaValida($fechaFinal)) {
    $erroresReporte[] = "Las fechas seleccionadas no tienen un formato válido.";
    [$fechaInicial, $fechaFinal] = rangoPorPeriodo("mensual");
}

if ($fechaInicial > $fechaFinal) {
    $erroresReporte[] = "La fecha inicial no puede ser posterior a la fecha final.";
    [$fechaInicial, $fechaFinal] = [$fechaFinal, $fechaInicial];
}

$tiposPermitidos = ["", "Temperatura", "Humedad", "Magnético", "Corriente"];
$estadosPermitidos = ["", "Dentro del rango", "Por debajo del mínimo", "Por encima del máximo"];

if (!in_array($tipoMedicion, $tiposPermitidos, true)) {
    $tipoMedicion = "";
}

if (!in_array($estadoFiltro, $estadosPermitidos, true)) {
    $estadoFiltro = "";
}

$sensores = [];
$lecturas = [];
$resumen = [
    "temperatura_actual" => null,
    "humedad_actual" => null,
    "temperatura_minima" => null,
    "temperatura_maxima" => null,
    "promedio_temperatura" => null,
    "promedio_humedad" => null,
    "cantidad_lecturas" => 0,
    "cantidad_alertas" => 0
];
$graficas = [
    "temperatura" => ["etiquetas" => [], "valores" => []],
    "humedad" => ["etiquetas" => [], "valores" => []],
    "comparacion" => ["minimo" => [], "maximo" => [], "promedio" => []],
    "alertas" => ["etiquetas" => [], "valores" => []]
];

try {
    // Estas comprobaciones evitan que el módulo falle si aún no se importaron las columnas nuevas.
    $tieneCodigo = columnaExiste($conn, "sensores", "codigo");
    $tieneUnidad = columnaExiste($conn, "sensores", "unidad_medida");
    $tieneMinimo = columnaExiste($conn, "sensores", "valor_minimo");
    $tieneMaximo = columnaExiste($conn, "sensores", "valor_maximo");
    $alertaTieneSensor = columnaExiste($conn, "alertas", "id_sensor");

    $codigoSensor = $tieneCodigo
        ? "COALESCE(NULLIF(s.codigo, ''), CONCAT('SEN-', LPAD(s.id_sensor, 3, '0')))"
        : "CONCAT('SEN-', LPAD(s.id_sensor, 3, '0'))";

    $unidadSensor = $tieneUnidad
        ? "COALESCE(NULLIF(s.unidad_medida, ''), CASE s.tipo WHEN 'Temperatura' THEN '°C' WHEN 'Humedad' THEN '%' WHEN 'Corriente' THEN 'A' ELSE 'Estado' END)"
        : "CASE s.tipo WHEN 'Temperatura' THEN '°C' WHEN 'Humedad' THEN '%' WHEN 'Corriente' THEN 'A' ELSE 'Estado' END";

    $minimoSensor = $tieneMinimo
        ? "COALESCE(s.valor_minimo, CASE s.tipo WHEN 'Temperatura' THEN 2 WHEN 'Humedad' THEN 65 ELSE 0 END)"
        : "CASE s.tipo WHEN 'Temperatura' THEN 2 WHEN 'Humedad' THEN 65 ELSE 0 END";

    $maximoSensor = $tieneMaximo
        ? "COALESCE(s.valor_maximo, CASE s.tipo WHEN 'Temperatura' THEN 8 WHEN 'Humedad' THEN 90 ELSE 100 END)"
        : "CASE s.tipo WHEN 'Temperatura' THEN 8 WHEN 'Humedad' THEN 90 ELSE 100 END";

    $sqlSensores = "SELECT
                        s.id_sensor,
                        $codigoSensor AS codigo,
                        s.nombre,
                        s.tipo
                    FROM sensores s
                    ORDER BY s.tipo, s.nombre";
    $resultadoSensores = $conn->query($sqlSensores);
    $sensores = $resultadoSensores->fetch_all(MYSQLI_ASSOC);

    $condiciones = ["la.fecha_hora BETWEEN ? AND ?"];
    $tipos = "ss";
    $parametros = [$fechaInicial . " 00:00:00", $fechaFinal . " 23:59:59"];

    if ($idSensor > 0) {
        $condiciones[] = "s.id_sensor = ?";
        $tipos .= "i";
        $parametros[] = $idSensor;
    }

    if ($tipoMedicion !== "") {
        $condiciones[] = "s.tipo = ?";
        $tipos .= "s";
        $parametros[] = $tipoMedicion;
    }

    $expresionEstado = "CASE
        WHEN la.valor < ($minimoSensor) THEN 'Por debajo del mínimo'
        WHEN la.valor > ($maximoSensor) THEN 'Por encima del máximo'
        ELSE 'Dentro del rango'
    END";

    if ($estadoFiltro !== "") {
        $condiciones[] = "($expresionEstado) = ?";
        $tipos .= "s";
        $parametros[] = $estadoFiltro;
    }

    $where = implode(" AND ", $condiciones);
    $sqlLecturas = "SELECT
            la.id_lectura,
            s.id_sensor,
            $codigoSensor AS codigo,
            s.nombre AS sensor,
            s.tipo,
            la.valor,
            $unidadSensor AS unidad_medida,
            la.fecha_hora,
            $expresionEstado AS estado_lectura
        FROM lecturas_ambientales la
        INNER JOIN sensores s ON s.id_sensor = la.id_sensor
        WHERE $where
        ORDER BY la.fecha_hora DESC";

    $stmt = $conn->prepare($sqlLecturas);
    $stmt->bind_param($tipos, ...$parametros);
    $stmt->execute();
    $lecturas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $resumen["cantidad_lecturas"] = count($lecturas);
    $valoresTemperatura = [];
    $valoresHumedad = [];
    $lecturasOrdenadas = array_reverse($lecturas);

    foreach ($lecturasOrdenadas as $lectura) {
        $etiqueta = date("d/m H:i", strtotime($lectura["fecha_hora"]));
        $valor = (float) $lectura["valor"];

        if ($lectura["tipo"] === "Temperatura") {
            $valoresTemperatura[] = $valor;
            $graficas["temperatura"]["etiquetas"][] = $etiqueta;
            $graficas["temperatura"]["valores"][] = $valor;
        }

        if ($lectura["tipo"] === "Humedad") {
            $valoresHumedad[] = $valor;
            $graficas["humedad"]["etiquetas"][] = $etiqueta;
            $graficas["humedad"]["valores"][] = $valor;
        }
    }

    if ($valoresTemperatura) {
        $resumen["temperatura_actual"] = end($valoresTemperatura);
        $resumen["temperatura_minima"] = min($valoresTemperatura);
        $resumen["temperatura_maxima"] = max($valoresTemperatura);
        $resumen["promedio_temperatura"] = array_sum($valoresTemperatura) / count($valoresTemperatura);
    }

    if ($valoresHumedad) {
        $resumen["humedad_actual"] = end($valoresHumedad);
        $resumen["promedio_humedad"] = array_sum($valoresHumedad) / count($valoresHumedad);
    }

    $graficas["comparacion"]["minimo"] = [
        $valoresTemperatura ? min($valoresTemperatura) : 0,
        $valoresHumedad ? min($valoresHumedad) : 0
    ];
    $graficas["comparacion"]["maximo"] = [
        $valoresTemperatura ? max($valoresTemperatura) : 0,
        $valoresHumedad ? max($valoresHumedad) : 0
    ];
    $graficas["comparacion"]["promedio"] = [
        $valoresTemperatura ? array_sum($valoresTemperatura) / count($valoresTemperatura) : 0,
        $valoresHumedad ? array_sum($valoresHumedad) / count($valoresHumedad) : 0
    ];

    $condicionesAlertas = ["a.fecha_hora BETWEEN ? AND ?"];
    $tiposAlertas = "ss";
    $parametrosAlertas = [$fechaInicial . " 00:00:00", $fechaFinal . " 23:59:59"];

    if ($idSensor > 0) {
        if ($alertaTieneSensor) {
            $condicionesAlertas[] = "a.id_sensor = ?";
            $tiposAlertas .= "i";
            $parametrosAlertas[] = $idSensor;
        } else {
            $sensorSeleccionado = null;
            foreach ($sensores as $sensor) {
                if ((int) $sensor["id_sensor"] === $idSensor) {
                    $sensorSeleccionado = $sensor;
                    break;
                }
            }

            if ($sensorSeleccionado) {
                $condicionesAlertas[] = "a.tipo = ?";
                $tiposAlertas .= "s";
                $parametrosAlertas[] = $sensorSeleccionado["tipo"];
            }
        }
    }

    if ($tipoMedicion !== "" && in_array($tipoMedicion, ["Temperatura", "Humedad"], true)) {
        $condicionesAlertas[] = "a.tipo = ?";
        $tiposAlertas .= "s";
        $parametrosAlertas[] = $tipoMedicion;
    }

    $sqlAlertas = "SELECT DATE(a.fecha_hora) AS fecha, COUNT(*) AS total
        FROM alertas a
        WHERE " . implode(" AND ", $condicionesAlertas) . "
        GROUP BY DATE(a.fecha_hora)
        ORDER BY fecha";

    $stmtAlertas = $conn->prepare($sqlAlertas);
    $stmtAlertas->bind_param($tiposAlertas, ...$parametrosAlertas);
    $stmtAlertas->execute();
    $alertasPorFecha = $stmtAlertas->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($alertasPorFecha as $alerta) {
        $graficas["alertas"]["etiquetas"][] = date("d/m", strtotime($alerta["fecha"]));
        $graficas["alertas"]["valores"][] = (int) $alerta["total"];
        $resumen["cantidad_alertas"] += (int) $alerta["total"];
    }
} catch (Throwable $error) {
    $detalleError = "Error en Reportes: " . $error->getMessage();

    file_put_contents(
        __DIR__ . "/../logs/reportes_error.log",
        date("Y-m-d H:i:s") . " | " . $detalleError . PHP_EOL,
        FILE_APPEND
    );

    $erroresReporte[] = $detalleError;
}

$queryDescarga = http_build_query([
    "periodo" => $periodo,
    "fecha_inicial" => $fechaInicial,
    "fecha_final" => $fechaFinal,
    "sensor" => $idSensor,
    "tipo" => $tipoMedicion,
    "estado" => $estadoFiltro
]);
