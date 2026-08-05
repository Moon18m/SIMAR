<?php

require "../config/conexion.php";


// Verifica si ya existe una alerta activa del mismo tipo
function existeAlertaActiva($conn, $tipo)
{
    $sql = "
        SELECT
            id_alerta
        FROM alertas
        WHERE tipo = ?
        AND estado = 'Activa'
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $tipo);

    $stmt->execute();

    $resultado = $stmt->get_result();

    return $resultado->num_rows > 0;
}


// Crea una nueva alerta
function crearAlerta($conn, $tipo, $nivel, $mensaje)
{
    // Si ya existe una alerta activa no se crea otra
    if (existeAlertaActiva($conn, $tipo)) {
        return false;
    }

    $sql = "
        INSERT INTO alertas
        (
            tipo,
            nivel,
            mensaje
        )
        VALUES
        (
            ?,
            ?,
            ?
        )
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sss",
        $tipo,
        $nivel,
        $mensaje
    );

    // Devuelve true si se insertó correctamente
    return $stmt->execute();
}

// Marca una alerta como resuelta
function resolverAlerta($conn, $id_alerta)
{
    $sql = "
        UPDATE alertas
        SET estado = 'Resuelta'
        WHERE id_alerta = ?
        AND estado = 'Activa'
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $id_alerta
    );

    return $stmt->execute();
}


// Obtiene todas las alertas
function obtenerAlertas($conn)
{
    $sql = "
        SELECT
            id_alerta,
            tipo,
            nivel,
            mensaje,
            estado,
            fecha_hora
        FROM alertas
        ORDER BY fecha_hora DESC
    ";

    $resultado = $conn->query($sql);

    $alertas = [];

    while ($fila = $resultado->fetch_assoc()) {

        $alertas[] = $fila;

    }

    return $alertas;
}


// Cuenta las alertas activas
function obtenerAlertasActivas($conn)
{
    $sql = "
        SELECT
            COUNT(*) AS total
        FROM alertas
        WHERE estado = 'Activa'
    ";

    $resultado = $conn->query($sql);

    return $resultado->fetch_assoc()["total"];
}

?>