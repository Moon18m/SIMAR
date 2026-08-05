<?php

require "../middleware/auth.php";
require "../services/dashboardService.php";

header("Content-Type: application/json");

try {

    require "../config/conexion.php";

    $datos = obtenerDashboard($conn);

    echo json_encode([
        "success" => true,
        "data" => $datos
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    error_log($e->getMessage()); // registra el detalle real en el log del servidor

    echo json_encode([
        "success" => false,
        "mensaje" => "Ocurrió un error al obtener los datos del dashboard."
    ]);

}

if (isset($conn)) {
    $conn->close();
}
    