<?php

// Obtiene la última lectura de temperatura registrada
function obtenerTemperatura($conn)
{
    $sql = "
        SELECT
            valor,
            fecha_hora
        FROM lecturas_ambientales
        WHERE id_sensor = (
            SELECT id_sensor
            FROM sensores
            WHERE tipo = 'Temperatura'
            LIMIT 1
        )
        ORDER BY fecha_hora DESC
        LIMIT 1
    ";

    $resultado = $conn->query($sql);

    $fila = $resultado->fetch_assoc();

    return $fila ?: [
        "valor" => 0,
        "fecha_hora" => null
    ];

}


// Obtiene la última lectura de humedad registrada
function obtenerHumedad($conn)
{
    $sql = "
        SELECT
            valor,
            fecha_hora
        FROM lecturas_ambientales
        WHERE id_sensor = (
            SELECT id_sensor
            FROM sensores
            WHERE tipo = 'Humedad'
            LIMIT 1
        )
        ORDER BY fecha_hora DESC
        LIMIT 1
    ";

    $resultado = $conn->query($sql);

    $fila = $resultado->fetch_assoc();

    return $fila ?: [
        "valor" => 0,
        "fecha_hora" => null
    ];
}


// Calcula la cantidad total de productos almacenados
function obtenerTotalProductos($conn)
{
    $sql = "
        SELECT
            SUM(cantidad) AS total
        FROM inventario
    ";

    $resultado = $conn->query($sql);

    return $resultado->fetch_assoc()["total"] ?? 0;
}


// Cuenta las alertas que actualmente están activas
function obtenerAlertasActivas($conn)
{
    $sql = "
        SELECT
            COUNT(*) AS total
        FROM alertas
        WHERE estado = 'Activa'
    ";

    $resultado = $conn->query($sql);

    return $resultado->fetch_assoc()["total"] ?? 0;
}


// Obtiene las últimas 20 lecturas de temperatura para la gráfica
function obtenerHistoricoTemperatura($conn)
{
    $sql = "
        SELECT
            DATE_FORMAT(fecha_hora,'%H:%i') AS hora,
            valor
        FROM lecturas_ambientales
        WHERE id_sensor = (
            SELECT id_sensor
            FROM sensores
            WHERE tipo = 'Temperatura'
            LIMIT 1
        )
        ORDER BY fecha_hora DESC
        LIMIT 20
    ";

    $resultado = $conn->query($sql);

    $datos = [];

    while($fila = $resultado->fetch_assoc()){
        $datos[] = $fila;
    }

    return array_reverse($datos);
}


// Obtiene las últimas 20 lecturas de humedad para la gráfica
function obtenerHistoricoHumedad($conn)
{
    $sql = "
        SELECT
            DATE_FORMAT(fecha_hora,'%H:%i') AS hora,
            valor
        FROM lecturas_ambientales
        WHERE id_sensor = (
            SELECT id_sensor
            FROM sensores
            WHERE tipo = 'Humedad'
            LIMIT 1
        )
        ORDER BY fecha_hora DESC
        LIMIT 20
    ";

    $resultado = $conn->query($sql);

    $datos = [];

    while($fila = $resultado->fetch_assoc()){
        $datos[] = $fila;
    }

    return array_reverse($datos);
}
// Obtiene las últimas 10 alertas registradas
function obtenerUltimasAlertas($conn)
{
    $sql = "
        SELECT
            fecha_hora,
            mensaje,
            estado
        FROM alertas
        ORDER BY fecha_hora DESC
        LIMIT 10
    ";

    $resultado = $conn->query($sql);

    $alertas = [];

    while ($fila = $resultado->fetch_assoc()) {
        $alertas[] = $fila;
    }

    return $alertas;
}


// Calcula los productos con menor vida útil restante
function obtenerProductosProximos($conn)
{
    $sql = "
        SELECT
            p.nombre,
            i.fecha_ingreso,
            i.vida_util_calculada
        FROM inventario i
        INNER JOIN productos p
            ON i.id_producto = p.id_producto
    ";

    $resultado = $conn->query($sql);

    $productos = [];

    while ($fila = $resultado->fetch_assoc()) {

        // Días transcurridos desde que el producto ingresó
        $diasTranscurridos = (time() - strtotime($fila["fecha_ingreso"])) / 86400;

        // Vida útil restante
        $diasRestantes = $fila["vida_util_calculada"] - $diasTranscurridos;

        // Evitar valores negativos
        if ($diasRestantes < 0) {
            $diasRestantes = 0;
        }

        // Fecha estimada de vencimiento
        $fechaVencimiento = date(
            "Y-m-d",
            strtotime(
                $fila["fecha_ingreso"] .
                " +" . $fila["vida_util_calculada"] . " days"
            )
        );

        $productos[] = [

            "producto" => $fila["nombre"],

            "vencimiento" => $fechaVencimiento,

            "dias" => round($diasRestantes, 1)

        ];
    }

    // Ordenar desde el producto más próximo a vencer
    usort($productos, function ($a, $b) {
        return $a["dias"] <=> $b["dias"];
    });

    // Mostrar únicamente los primeros 10
    return array_slice($productos, 0, 10);
}

// Revisa el estado real de los sensores registrados en la BD
function obtenerEstadoSensores($conn)
{
    $sql = "
        SELECT COUNT(*) AS problemas
        FROM sensores
        WHERE estado != 'Activo'
    ";

    $resultado = $conn->query($sql);
    $fila = $resultado->fetch_assoc();

    return $fila["problemas"] > 0 ? "error" : "ok";
}

// Revisa si el ESP32 sigue enviando lecturas recientes
function obtenerEstadoESP32($conn)
{
    $sql = "
        SELECT MAX(fecha_hora) AS ultima_lectura
        FROM lecturas_ambientales
    ";

    $resultado = $conn->query($sql);
    $fila = $resultado->fetch_assoc();

    if (!$fila["ultima_lectura"]) {
        return "error";
    }

    $minutosTranscurridos =
        (time() - strtotime($fila["ultima_lectura"])) / 60;

    return $minutosTranscurridos <= 5 ? "ok" : "error";
}

function obtenerEstadoIA($conn)
{
    $sql = "
        SELECT fecha_hora, estado
        FROM ia_ejecuciones
        ORDER BY fecha_hora DESC
        LIMIT 1
    ";

    $resultado = $conn->query($sql);
    $fila = $resultado->fetch_assoc();

    if (!$fila) {
        return "error";
    }

    if ($fila["estado"] === "Error") {
        return "error";
    }

    $minutosTranscurridos =
        (time() - strtotime($fila["fecha_hora"])) / 60;

    return $minutosTranscurridos <= 60 ? "ok" : "error"; 
}


// Reúne toda la información necesaria para el dashboard
function obtenerDashboard($conn)
{

    return [
    "estados" => [
        "bd" => "ok",
        "esp32" => obtenerEstadoESP32($conn),
        "sensores" => obtenerEstadoSensores($conn),
        "ia" => obtenerEstadoIA($conn)
        ],

        "temperatura" => obtenerTemperatura($conn),

        "humedad" => obtenerHumedad($conn),

        "productos" => obtenerTotalProductos($conn),

        "alertas" => obtenerAlertasActivas($conn),

        "graficaTemperatura" => obtenerHistoricoTemperatura($conn),

        "graficaHumedad" => obtenerHistoricoHumedad($conn),

        "ultimasAlertas" => obtenerUltimasAlertas($conn),

        "productosProximos" => obtenerProductosProximos($conn)

    ];

}
?>