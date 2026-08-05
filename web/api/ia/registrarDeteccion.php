<?php

header("Content-Type: application/json; charset=UTF-8");

require "../../config/conexion.php";
require "../../services/deteccionIAService.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "mensaje" => "Método no permitido."
    ]);

    exit;
}

$entrada = json_decode(
    file_get_contents("php://input"),
    true
);

if (!is_array($entrada)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "mensaje" => "Los datos recibidos no tienen un formato JSON válido."
    ]);

    exit;
}

$clase = trim($entrada["clase"] ?? "");
$confianza = $entrada["confianza"] ?? null;
$dispositivo = trim($entrada["dispositivo"] ?? "");

if (
    $clase === "" ||
    $confianza === null
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "mensaje" => "Faltan datos obligatorios."
    ]);

    exit;
}

if (!is_numeric($confianza)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "mensaje" => "La confianza debe ser un valor numérico."
    ]);

    exit;
}

$confianza = (float) $confianza;

if ($confianza < 0 || $confianza > 100) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "mensaje" => "La confianza debe estar entre 0 y 100."
    ]);

    exit;
}

try {

    $resultado = registrarDeteccionIA(
        $conn,
        $clase,
        $confianza,
        $dispositivo
    );

    echo json_encode([
        "success" => true,
        "mensaje" => "Detección registrada correctamente.",
        "data" => $resultado
    ]);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => $e->getMessage()
    ]);
}

$conn->close();
?>
