<?php

// Convierte el texto antes de mostrarlo en la página.
function limpiarReporte(?string $texto): string
{
    return htmlspecialchars($texto ?? "", ENT_QUOTES, "UTF-8");
}

// Confirma que la fecha tenga el formato usado por el formulario.
function fechaValida(string $fecha): bool
{
    $objeto = DateTime::createFromFormat("Y-m-d", $fecha);
    return $objeto && $objeto->format("Y-m-d") === $fecha;
}

// Entrega el rango inicial según el periodo elegido.
function rangoPorPeriodo(string $periodo): array
{
    $hoy = new DateTime("today");
    $inicio = clone $hoy;

    if ($periodo === "diario") {
        return [$hoy->format("Y-m-d"), $hoy->format("Y-m-d")];
    }

    if ($periodo === "semanal") {
        $inicio->modify("-6 days");
        return [$inicio->format("Y-m-d"), $hoy->format("Y-m-d")];
    }

    $inicio->modify("first day of this month");
    return [$inicio->format("Y-m-d"), $hoy->format("Y-m-d")];
}

// Revisa si una columna existe. Así el módulo funciona con la base original y con la ampliada.
function columnaExiste(mysqli $conn, string $tabla, string $columna): bool
{
    $sql = "SELECT COUNT(*) AS total
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $tabla, $columna);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();

    return (int) ($fila["total"] ?? 0) > 0;
}
