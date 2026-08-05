<?php
require "../middleware/auth.php";
require "../includes/funciones_ia.php";

$dispositivo = null;
$ultimaEjecucion = null;
$errorConexion = null;

$camaraActiva = $dispositivo !== null;
$iaActiva = esEjecucionReciente($ultimaEjecucion);


try {
    require "../config/conexion.php";

    $stmtDispositivo = $conn->prepare(
        "SELECT nombre, ip, puerto, estado FROM dispositivos WHERE estado = 'Activo' LIMIT 1"
    );
    $stmtDispositivo->execute();
    $dispositivo = $stmtDispositivo->get_result()->fetch_assoc();
    $stmtDispositivo->close();

    $stmtIA = $conn->prepare(
        "SELECT estado, detalle, fecha_hora FROM ia_ejecuciones ORDER BY fecha_hora DESC LIMIT 1"
    );
    $stmtIA->execute();
    $ultimaEjecucion = $stmtIA->get_result()->fetch_assoc();
    $stmtIA->close();
} catch (Exception $e) {
    $errorConexion = "No se pudo conectar con la base de datos.";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

$streamUrl = $dispositivo ? "http://{$dispositivo['ip']}:{$dispositivo['puerto']}/video" : null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cámara IA | SIMAR</title>
    <link rel="icon" href="../assets/img/iconos/icono_simar.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/camara.css">
</head>

<body>

    <div class="dashboard">

        <aside class="sidebar">
            <div>
                <a href="dashboard.php" class="logo">
                    <img src="../assets/img/iconos/icono_simar.png" alt="SIMAR">
                    <h2>S.I.M.A.R</h2>
                </a>

                <nav>
                    <ul>
                        <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a></li>
                        <li><a href="inventario.php"><i class="fa-solid fa-box"></i><span>Inventario</span></a></li>
                        <li><a href="sensores.php"><i class="fa-solid fa-microchip"></i><span>Sensores</span></a></li>
                        <li class="active"><a href="camara.php"><i class="fa-solid fa-camera"></i><span>Cámara IA</span></a></li>
                        <li><a href="alertas.php"><i class="fa-solid fa-triangle-exclamation"></i><span>Alertas</span></a></li>
                        <?php if (($_SESSION["rol"] ?? "") === "Administrador"): ?>
                        <li><a href="reportes.php"><i class="fa-solid fa-file-lines"></i><span>Reportes</span></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>

            <div class="sidebar-footer">
                <span><?= htmlspecialchars($_SESSION["rol"] ?? "", ENT_QUOTES, "UTF-8") ?></span>
                <strong><?= htmlspecialchars($_SESSION["nombre"]) ?></strong>
            </div>

        </aside>

        <main class="contenido">

            <header class="topbar">

                <div>
                    <h1>Cámara IA</h1>
                    <p>Monitoreo visual y reconocimiento automático de bebidas</p>
                </div>

                <div class="acciones">

                    <button id="btnActualizar" class="btn">
                        <i class="fa-solid fa-rotate-right"></i>
                        Actualizar
                    </button>

                    <a href="../controllers/logoutController.php" class="logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Cerrar sesión
                    </a>

                </div>

            </header>

            <section class="estado-sistema">

                <div class="estado <?= !$camaraActiva ? 'error' : '' ?>" id="estadoCamara">
                    <i class="fa-solid fa-camera"></i>
                    Cámara
                </div>

                <div class="estado ok" id="estadoIA">
                    <i class="fa-solid fa-brain"></i>
                    IA
                </div>

            </section>

            <?php if ($errorConexion): ?>
                <section class="panel panel-aviso panel-aviso--error">
                    <?= htmlspecialchars($errorConexion) ?>
                </section>
            <?php elseif (!$dispositivo): ?>
                <section class="panel panel-aviso panel-aviso--advertencia">
                    No hay ningún dispositivo de cámara activo registrado. Agrega uno en la tabla
                    <code>dispositivos</code> para habilitar el stream.
                </section>
            <?php endif; ?>

            <section class="vista-camara">

                <div class="panel panel-stream">
                    <h2>Transmisión en vivo</h2>

                    <div class="stream-marco" id="marcoStream">
                        <?php if ($streamUrl): ?>
                            <img
                                id="streamImg"
                                src="<?= htmlspecialchars($streamUrl) ?>"
                                alt="Transmisión en vivo de la cámara"
                            >
                        <?php else: ?>
                            <div class="stream-vacio">
                                <p>Sin transmisión disponible</p>
                            </div>
                        <?php endif; ?>

                        <div class="stream-overlay-error" id="overlayError" hidden>
                            <p>Cámara desconectada</p>
                            <span>Verificando conexión con <?= htmlspecialchars($dispositivo['ip'] ?? '—') ?>…</span>
                        </div>
                    </div>
                </div>

                <div class="panel panel-estado-ia">
                    <h2>Estado de IA</h2>

                    <div class="ultima-ejecucion" id="ultimaEjecucion">
                        <?php if ($ultimaEjecucion): ?>
                            <p class="ultima-ejecucion__fecha">
                                <?= htmlspecialchars($ultimaEjecucion['fecha_hora']) ?>
                            </p>
                            <p class="ultima-ejecucion__estado ultima-ejecucion__estado--<?= strtolower($ultimaEjecucion['estado']) ?>">
                                <?= htmlspecialchars($ultimaEjecucion['estado']) ?>
                            </p>
                            <?php if (!empty($ultimaEjecucion['detalle'])): ?>
                                <p class="ultima-ejecucion__detalle">
                                    <?= htmlspecialchars($ultimaEjecucion['detalle']) ?>
                                </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="ultima-ejecucion__vacio">Aún no hay ejecuciones registradas.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </section>

        </main>

    </div>

    <script src="../assets/js/camara.js"></script>

</body>

</html>
