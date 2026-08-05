<?php

require_once("../../config/conexion.php");
require "../../middleware/auth.php";

header("Content-Type: application/json");

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "ID inválido"]);
    exit;
}

$sql = "SELECT
            i.id_inventario,
            p.nombre,
            i.cantidad,
            i.fecha_ingreso,
            i.vida_util_calculada
        FROM inventario i
        INNER JOIN productos p
            ON p.id_producto = i.id_producto
        WHERE i.id_inventario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$producto = $resultado->fetch_assoc();

if (!$producto) {
    http_response_code(404);
    echo json_encode(["error" => "Producto no encontrado"]);
    exit;
}

echo json_encode($producto);