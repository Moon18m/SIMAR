<?php
/**
 * ObtenerEstadoIAController.php
 * Devuelve la última fila de `ia_ejecuciones` en JSON, junto con `activo`
 * (true/false) calculado con la misma regla de "reciente" que usa camara.php,
 * para que el botón "Actualizar" y el primer render nunca se desincronicen.
 */

header("Content-Type: application/json; charset=utf-8");

require "../includes/funciones_ia.php";

$respuesta = [
    "ok" => false,
    "ejecucion" => null,
    "activo" => false,
    "mensaje" => null,
];

try {
    require "../config/conexion.php";

    $stmt = $conn->prepare(
        "SELECT estado, detalle, fecha_hora FROM ia_ejecuciones ORDER BY fecha_hora DESC LIMIT 1"
    );
    $stmt->execute();
    $ejecucion = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $respuesta["ok"] = true;
    $respuesta["ejecucion"] = $ejecucion; // null si aún no hay registros
    $respuesta["activo"] = esEjecucionReciente($ejecucion);
} catch (Exception $e) {
    $respuesta["mensaje"] = "No se pudo consultar el estado de la IA.";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

echo json_encode($respuesta);
