<?php

/* SENSORES SERVICE*/
function obtenerResumenSensores($conn)
{
    $sql = "SELECT s.id_sensor, s.nombre, s.tipo, s.estado,
                   l.valor, l.fecha_hora
            FROM sensores s
            LEFT JOIN lecturas_ambientales l
                   ON l.id_lectura = (
                        SELECT l2.id_lectura
                        FROM lecturas_ambientales l2
                        WHERE l2.id_sensor = s.id_sensor
                        ORDER BY l2.fecha_hora DESC
                        LIMIT 1
                   )
            ORDER BY s.id_sensor ASC";

    $result = $conn->query($sql);

    $sensores = [];
    $conteo = ["activos" => 0, "error" => 0, "inactivos" => 0];

    while ($fila = $result->fetch_assoc()) {
        $sensores[] = $fila;

        switch ($fila["estado"]) {
            case "Activo":
                $conteo["activos"]++;
                break;
            case "Error":
                $conteo["error"]++;
                break;
            default:
                $conteo["inactivos"]++;
        }
    }

    return [
        "sensores" => $sensores,
        "conteo"   => $conteo,
    ];
}

/**
 * Últimas N lecturas registradas, sin importar el sensor,
 * para la tabla "Últimas lecturas".
 */
function obtenerUltimasLecturas($conn, $limite = 10)
{
    $sql = "SELECT s.nombre, s.tipo, l.valor, l.fecha_hora
            FROM lecturas_ambientales l
            INNER JOIN sensores s ON s.id_sensor = l.id_sensor
            ORDER BY l.fecha_hora DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limite);
    $stmt->execute();

    $result = $stmt->get_result();

    $lecturas = [];
    while ($fila = $result->fetch_assoc()) {
        $lecturas[] = $fila;
    }

    $stmt->close();

    return $lecturas;
}

/**
 * Historial de lecturas de UN tipo de sensor
 */
function obtenerHistorialSensor($conn, $tipo, $rango)
{
    $intervalos = [
        "1h"  => "1 HOUR",
        "6h"  => "6 HOUR",
        "24h" => "24 HOUR",
        "7d"  => "7 DAY",
    ];

    $intervalo = $intervalos[$rango] ?? "24 HOUR";

    $sql = "SELECT l.valor, l.fecha_hora
            FROM lecturas_ambientales l
            INNER JOIN sensores s ON s.id_sensor = l.id_sensor
            WHERE s.tipo = ?
              AND l.fecha_hora >= (NOW() - INTERVAL $intervalo)
            ORDER BY l.fecha_hora ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tipo);
    $stmt->execute();

    $result = $stmt->get_result();

    $datos = [];
    while ($fila = $result->fetch_assoc()) {
        $datos[] = $fila;
    }

    $stmt->close();

    return $datos;
}
