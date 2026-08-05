    <?php

require_once("../../config/conexion.php");

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Método no permitido."
    ]);

    exit;
}

$idInventario = $_POST["id_inventario"] ?? null;
$cantidad = $_POST["cantidad"] ?? null;
$fechaIngreso = $_POST["fecha_ingreso"] ?? null;
$vidaUtil = $_POST["vida_util_calculada"] ?? null;

if (
    empty($idInventario) ||
    $cantidad === null ||
    empty($fechaIngreso) ||
    $vidaUtil === null
) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Todos los campos son obligatorios."
    ]);

    exit;
}

$sql = "
    UPDATE inventario
    SET
        cantidad = ?,
        fecha_ingreso = ?,
        vida_util_calculada = ?
    WHERE id_inventario = ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "No se pudo preparar la consulta."
    ]);

    exit;
}

$stmt->bind_param(
    "isii",
    $cantidad,
    $fechaIngreso,
    $vidaUtil,
    $idInventario
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Producto actualizado correctamente."
    ]);

} else {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "No se pudo actualizar el producto."
    ]);

}

$stmt->close();
$conn->close();