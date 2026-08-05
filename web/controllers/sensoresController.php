<?php

require "../middleware/auth.php";
require "../services/sensoresService.php";

header("Content-Type: application/json");

try {

    require "../config/conexion.php";

    // ?accion=resumen | historial | ultimas
    $accion = $_GET["accion"] ?? "resumen";

    switch ($accion) {

        case "resumen":
            $datos = obtenerResumenSensores($conn);
            break;

        case "ultimas":
            $datos = obtenerUltimasLecturas($conn, 10);
            break;

        case "historial":
            // Solo se aceptan estos dos tipos porque son los únicos
            // sensores realmente instalados (tabla "sensores").
            $tiposValidos = ["Temperatura", "Humedad"];
            $tipo = $_GET["tipo"] ?? "Temperatura";

            if (!in_array($tipo, $tiposValidos, true)) {
                throw new Exception("Tipo de sensor no válido.");
            }

            $rangosValidos = ["1h", "6h", "24h", "7d"];
            $rango = $_GET["rango"] ?? "24h";

            if (!in_array($rango, $rangosValidos, true)) {
                $rango = "24h";
            }

            $datos = obtenerHistorialSensor($conn, $tipo, $rango);
            break;

        default:
            throw new Exception("Acción no válida.");
    }

    echo json_encode([
        "success" => true,
        "data" => $datos
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    error_log($e->getMessage());

    echo json_encode([
        "success" => false,
        "mensaje" => "Ocurrió un error al obtener los datos de sensores."
    ]);

}

if (isset($conn)) {
    $conn->close();
}
