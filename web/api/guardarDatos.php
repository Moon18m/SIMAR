<?php

require_once("../config/conexion.php");

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "estado" => "error",
        "mensaje" => "Solo se acepta POST",
        "metodo_recibido" => $_SERVER["REQUEST_METHOD"] ?? "desconocido"
    ]);

    exit;
}

$json = file_get_contents("php://input");

if (empty($json)) {

    http_response_code(400);

    echo json_encode([
        "estado" => "error",
        "mensaje" => "No llegó ningún dato"
    ]);

    exit;
}

$datos = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {

    http_response_code(400);

    echo json_encode([
        "estado" => "error",
        "mensaje" => "JSON inválido",
        "detalle" => json_last_error_msg()
    ]);

    exit;
}

if (!isset($datos["temperatura"]) || !isset($datos["humedad"])) {

    http_response_code(400);

    echo json_encode([
        "estado" => "error",
        "mensaje" => "Faltan temperatura o humedad"
    ]);

    exit;
}

$temperatura = (float) $datos["temperatura"];
$humedad = (float) $datos["humedad"];

$sql = "INSERT INTO lecturas_ambientales (id_sensor, valor)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "estado" => "error",
        "mensaje" => "Error preparando la consulta",
        "detalle" => $conn->error
    ]);

    exit;
}

$idSensor = 1;

$stmt->bind_param("id", $idSensor, $temperatura);

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "estado" => "error",
        "mensaje" => "Error guardando la temperatura",
        "detalle" => $stmt->error
    ]);

    $stmt->close();

    exit;
}

$idSensor = 2;

$stmt->bind_param("id", $idSensor, $humedad);

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "estado" => "error",
        "mensaje" => "Error guardando la humedad",
        "detalle" => $stmt->error
    ]);

    $stmt->close();

    exit;
}

$stmt->close();

echo json_encode([
    "estado" => "ok",
    "mensaje" => "Datos guardados",
    "temperatura" => $temperatura,
    "humedad" => $humedad
]);

?>
