<?php

require "../config/conexion.php";
require "../services/alertasService.php";

$mensaje = "";
$tipoMensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $accion = $_POST["accion"] ?? "";

    if ($accion === "crear") {

        $resultado = crearAlerta(
            $conn,
            "Humedad",
            "Advertencia",
            "Alerta de prueba generada desde SIMAR."
        );

        if ($resultado) {
            $mensaje = "Alerta creada correctamente.";
            $tipoMensaje = "exito";
        } else {
            $mensaje = "No se creó la alerta. Puede que ya exista una alerta activa de este tipo.";
            $tipoMensaje = "error";
        }
    }

    if ($accion === "resolver") {

        $resultado = resolverAlerta(
            $conn,
            "Humedad"
        );

        if ($resultado) {
            $mensaje = "Alerta resuelta correctamente.";
            $tipoMensaje = "exito";
        } else {
            $mensaje = "No se pudo resolver la alerta.";
            $tipoMensaje = "error";
        }
    }
}

$alertas = obtenerAlertas($conn);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Prueba de Alertas | SIMAR</title>

</head>

<body>

    <h1>Prueba del sistema de alertas SIMAR</h1>

    <?php if ($mensaje): ?>

        <p>
            <?= htmlspecialchars($mensaje) ?>
        </p>

    <?php endif; ?>


    <h2>Acciones</h2>

    <form method="POST">

        <input
            type="hidden"
            name="accion"
            value="crear"
        >

        <button type="submit">
            Crear alerta de prueba
        </button>

    </form>


    <br>


    <form method="POST">

        <input
            type="hidden"
            name="accion"
            value="resolver"
        >

        <button type="submit">
            Resolver alerta de prueba
        </button>

    </form>


    <h2>Alertas registradas</h2>

    <table border="1">

        <thead>

            <tr>

                <th>ID</th>

                <th>Tipo</th>

                <th>Nivel</th>

                <th>Mensaje</th>

                <th>Estado</th>

                <th>Fecha</th>

            </tr>

        </thead>

        <tbody>

            <?php foreach ($alertas as $alerta): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($alerta["id_alerta"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($alerta["tipo"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($alerta["nivel"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($alerta["mensaje"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($alerta["estado"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($alerta["fecha_hora"]) ?>
                    </td>

                    <td>

                        <?php if ($alerta["estado"] === "Activa"): ?>

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="accion"
                                    value="resolver"
                                >

                                <input
                                    type="hidden"
                                    name="id_alerta"
                                    value="<?= $alerta["id_alerta"] ?>"
                                >

                                <button type="submit">
                                    Resolver
                                </button>

                            </form>

                        <?php else: ?>

                            Resuelta

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</body>

</html>