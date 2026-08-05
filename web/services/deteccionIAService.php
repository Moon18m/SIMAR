<?php

function registrarDeteccionIA(
    mysqli $conn,
    string $clase,
    float $confianza,
    string $dispositivo = ""
): array {

    $conn->begin_transaction();

    try {

        $detalle = "Detección: {$clase} | Confianza: " .
            number_format($confianza, 2) . "%";

        if ($dispositivo !== "") {
            $detalle .= " | Dispositivo: {$dispositivo}";
        }

        $estado = "Exitoso";

        $stmtEjecucion = $conn->prepare(
            "INSERT INTO ia_ejecuciones (estado, detalle)
             VALUES (?, ?)"
        );

        if (!$stmtEjecucion) {
            throw new Exception(
                "No se pudo preparar el registro de la ejecución."
            );
        }

        $stmtEjecucion->bind_param(
            "ss",
            $estado,
            $detalle
        );

        if (!$stmtEjecucion->execute()) {
            throw new Exception(
                "No se pudo guardar la ejecución."
            );
        }

        $idEjecucion = $stmtEjecucion->insert_id;

        $stmtEjecucion->close();


        $stmtDeteccion = $conn->prepare(
            "INSERT INTO detecciones_ia (
                id_ejecucion,
                clase_detectada,
                confianza,
                cantidad_detectada
            )
            VALUES (?, ?, ?, ?)"
        );

        if (!$stmtDeteccion) {
            throw new Exception(
                "No se pudo preparar el registro de la detección."
            );
        }

        $cantidad = 1;

        $stmtDeteccion->bind_param(
            "isdi",
            $idEjecucion,
            $clase,
            $confianza,
            $cantidad
        );

        if (!$stmtDeteccion->execute()) {
            throw new Exception(
                "No se pudo guardar la detección."
            );
        }

        $idDeteccion = $stmtDeteccion->insert_id;

        $stmtDeteccion->close();

        $conn->commit();

        return [
            "id_ejecucion" => $idEjecucion,
            "id_deteccion" => $idDeteccion,
            "clase" => $clase,
            "confianza" => $confianza,
            "cantidad" => $cantidad,
            "dispositivo" => $dispositivo,
            "fecha_hora" => date("Y-m-d H:i:s")
        ];

    } catch (Exception $e) {

        $conn->rollback();

        throw $e;
    }
}
?>