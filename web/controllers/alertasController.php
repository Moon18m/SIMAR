<?php

header("Content-Type: application/json; charset=UTF-8");

require "../config/conexion.php";
require "../services/alertasService.php";

$accion = $_GET["accion"] ?? "";

switch ($accion) {

    // Obtiene todas las alertas
    case "listar":

        echo json_encode([
            "success" => true,
            "data" => obtenerAlertas($conn)
        ]);

        break;


    // Obtiene el resumen de las alertas
    case "resumen":

        $alertas = obtenerAlertas($conn);

        $total = count($alertas);

        $activas = 0;
        $resueltas = 0;
        $hoy = 0;

        $fechaHoy = date("Y-m-d");

        foreach ($alertas as $alerta) {

            if ($alerta["estado"] === "Activa") {
                $activas++;
            }

            if ($alerta["estado"] === "Resuelta") {
                $resueltas++;
            }

            if (substr($alerta["fecha_hora"], 0, 10) === $fechaHoy) {
                $hoy++;
            }
        }

        echo json_encode([
            "success" => true,
            "data" => [
                "total" => $total,
                "activas" => $activas,
                "resueltas" => $resueltas,
                "hoy" => $hoy
            ]
        ]);

        break;


    // Devuelve la cantidad de alertas activas
    case "activas":

        echo json_encode([
            "success" => true,
            "data" => obtenerAlertasActivas($conn)
        ]);

        break;


    // Crea una nueva alerta
    case "crear":

        $tipo = $_POST["tipo"] ?? "";
        $nivel = $_POST["nivel"] ?? "";
        $mensaje = $_POST["mensaje"] ?? "";

        if (
            empty($tipo) ||
            empty($nivel) ||
            empty($mensaje)
        ) {

            echo json_encode([
                "success" => false,
                "mensaje" => "Todos los campos son obligatorios."
            ]);

            exit;
        }

        $resultado = crearAlerta(
            $conn,
            $tipo,
            $nivel,
            $mensaje
        );

        echo json_encode([
            "success" => $resultado
        ]);

        break;


    // Marca una alerta específica como resuelta
    case "resolver":

        $id_alerta = $_POST["id_alerta"] ?? null;

        if (!$id_alerta) {

            echo json_encode([
                "success" => false,
                "mensaje" => "No se recibió el ID de la alerta."
            ]);

            exit;
        }

        $resultado = resolverAlerta(
            $conn,
            $id_alerta
        );

        echo json_encode([
            "success" => $resultado
        ]);

        break;


    default:

        echo json_encode([
            "success" => false,
            "mensaje" => "Acción no válida."
        ]);

        break;
}