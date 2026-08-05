<?php
require_once __DIR__ . "/../middleware/admin.php";
require "../controllers/reportes/reportesController.php";
// Configura la hora de Colombia.
date_default_timezone_set("America/Bogota");
function mostrarNumero($valor, string $unidad = ""): string
{
    if ($valor === null) {
        return "Sin datos";
    }

    return number_format((float) $valor, 1, ",", ".") . $unidad;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes | SIMAR</title>
    <link rel="icon" href="../assets/img/iconos/icono_simar.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/reportes.css">
</head>
<body>
<div class="dashboard">
    <aside class="sidebar" aria-label="Navegación principal">
        <div>
            <a href="admin.php" class="logo">
                <img src="../assets/img/iconos/icono_simar.png" alt="SIMAR">
                <h2>S.I.M.A.R</h2>
            </a>
            <nav>
                <ul>
                    
                    <li class="active"><a href="reportes.php" aria-current="page"><i class="fa-solid fa-file-lines"></i><span>Reportes</span></a></li>
                    <li><a href="admin.php"><i class="fa-solid fa-user-gear"></i><span>Administración</span></a></li>
                </ul>
            </nav>
        </div>
        <div class="sidebar-footer">
            <span><?= limpiarReporte($_SESSION["rol"] ?? "") ?></span>
            <strong><?= limpiarReporte($_SESSION["nombre"] ?? "") ?></strong>
        </div>
    </aside>

    <main class="contenido">
        <header class="topbar reportes-topbar">
            <div>
                <h1>Reportes</h1>
                <p>Consulta el comportamiento de los sensores y sus fluctuaciones.</p>
            </div>
            <div class="acciones-reporte">
                <a class="btn" href="../controllers/reportes/descargarReporteController.php?<?= limpiarReporte($queryDescarga) ?>">
                    <i class="fa-solid fa-file-csv"></i> Descargar reporte
                </a>
                <button class="btn" id="btnImprimir" type="button">
                    <i class="fa-solid fa-print"></i> Imprimir
                </button>
                <a href="../controllers/logoutController.php" class="logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
                </a>
            </div>
        </header>

        <?php foreach ($erroresReporte as $error): ?>
            <div class="mensaje-reporte" role="alert">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= limpiarReporte($error) ?>
            </div>
        <?php endforeach; ?>

        <section class="panel filtros-panel">
            <div class="panel-titulo">
                <div>
                    <span>Consulta</span>
                    <h2>Filtros del reporte</h2>
                </div>
                <a class="limpiar-filtros" href="reportes.php">Limpiar filtros</a>
            </div>

            <form class="filtros-form" method="get" action="reportes.php" id="formFiltros">
                <div class="campo-reporte">
                    <label for="periodo">Periodo</label>
                    <select id="periodo" name="periodo">
                        <option value="diario" <?= $periodo === "diario" ? "selected" : "" ?>>Diario</option>
                        <option value="semanal" <?= $periodo === "semanal" ? "selected" : "" ?>>Semanal</option>
                        <option value="mensual" <?= $periodo === "mensual" ? "selected" : "" ?>>Mensual</option>
                        <option value="personalizado" <?= $periodo === "personalizado" ? "selected" : "" ?>>Personalizado</option>
                    </select>
                </div>

                <div class="campo-reporte">
                    <label for="sensor">Sensor</label>
                    <select id="sensor" name="sensor">
                        <option value="0" <?= $idSensor === 0 ? "selected" : "" ?>>Todos los sensores</option>
                        <?php foreach ($sensores as $sensor): ?>
                            <option value="<?= (int) $sensor["id_sensor"] ?>" <?= $idSensor === (int) $sensor["id_sensor"] ? "selected" : "" ?>>
                                <?= limpiarReporte($sensor["codigo"] . " | " . $sensor["nombre"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo-reporte">
                    <label for="tipo">Tipo de medición</label>
                    <select id="tipo" name="tipo">
                        <option value="">Todos</option>
                        <option value="Temperatura" <?= $tipoMedicion === "Temperatura" ? "selected" : "" ?>>Temperatura</option>
                        <option value="Humedad" <?= $tipoMedicion === "Humedad" ? "selected" : "" ?>>Humedad</option>
                        <option value="Magnético" <?= $tipoMedicion === "Magnético" ? "selected" : "" ?>>Magnético</option>
                        <option value="Corriente" <?= $tipoMedicion === "Corriente" ? "selected" : "" ?>>Corriente</option>
                    </select>
                </div>

                <div class="campo-reporte">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="">Todos</option>
                        <?php foreach ($estadosPermitidos as $estado): ?>
                            <?php if ($estado !== ""): ?>
                                <option value="<?= limpiarReporte($estado) ?>" <?= $estadoFiltro === $estado ? "selected" : "" ?>><?= limpiarReporte($estado) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo-reporte">
                    <label for="fecha_inicial">Fecha inicial</label>
                    <input type="date" id="fecha_inicial" name="fecha_inicial" value="<?= limpiarReporte($fechaInicial) ?>" required>
                </div>

                <div class="campo-reporte">
                    <label for="fecha_final">Fecha final</label>
                    <input type="date" id="fecha_final" name="fecha_final" value="<?= limpiarReporte($fechaFinal) ?>" required>
                </div>

                <button class="btn btn-filtrar" type="submit">
                    <i class="fa-solid fa-filter"></i> Aplicar filtros
                </button>
            </form>
        </section>

        <section class="resumen-reportes" aria-label="Resumen general">
            <article class="resumen-reporte temperatura"><i class="fa-solid fa-temperature-half"></i><span>Temperatura actual</span><strong><?= mostrarNumero($resumen["temperatura_actual"], " °C") ?></strong></article>
            <article class="resumen-reporte humedad"><i class="fa-solid fa-droplet"></i><span>Humedad actual</span><strong><?= mostrarNumero($resumen["humedad_actual"], " %") ?></strong></article>
            <article class="resumen-reporte"><i class="fa-solid fa-temperature-arrow-down"></i><span>Temperatura mínima</span><strong><?= mostrarNumero($resumen["temperatura_minima"], " °C") ?></strong></article>
            <article class="resumen-reporte"><i class="fa-solid fa-temperature-arrow-up"></i><span>Temperatura máxima</span><strong><?= mostrarNumero($resumen["temperatura_maxima"], " °C") ?></strong></article>
            <article class="resumen-reporte"><i class="fa-solid fa-chart-simple"></i><span>Promedio temperatura</span><strong><?= mostrarNumero($resumen["promedio_temperatura"], " °C") ?></strong></article>
            <article class="resumen-reporte"><i class="fa-solid fa-water"></i><span>Promedio humedad</span><strong><?= mostrarNumero($resumen["promedio_humedad"], " %") ?></strong></article>
            <article class="resumen-reporte"><i class="fa-solid fa-list-ol"></i><span>Cantidad de lecturas</span><strong><?= number_format($resumen["cantidad_lecturas"], 0, ",", ".") ?></strong></article>
            <article class="resumen-reporte alertas"><i class="fa-solid fa-triangle-exclamation"></i><span>Alertas generadas</span><strong><?= number_format($resumen["cantidad_alertas"], 0, ",", ".") ?></strong></article>
        </section>

        <section class="graficas-reportes">
            <article class="grafica-reporte"><h2>Temperatura por fecha y hora</h2><div class="canvas-contenedor"><canvas id="graficaTemperatura"></canvas></div></article>
            <article class="grafica-reporte"><h2>Humedad por fecha y hora</h2><div class="canvas-contenedor"><canvas id="graficaHumedad"></canvas></div></article>
            <article class="grafica-reporte"><h2>Mínimo, máximo y promedio</h2><div class="canvas-contenedor"><canvas id="graficaComparacion"></canvas></div></article>
            <article class="grafica-reporte"><h2>Alertas por día</h2><div class="canvas-contenedor"><canvas id="graficaAlertas"></canvas></div></article>
        </section>

        <section class="panel tabla-reporte-panel">
            <div class="panel-titulo">
                <div><span>Historial</span><h2>Lecturas ambientales</h2></div>
                <p><?= limpiarReporte($fechaInicial) ?> a <?= limpiarReporte($fechaFinal) ?></p>
            </div>

            <div class="tabla-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th><th>Sensor</th><th>Tipo</th><th>Valor</th><th>Unidad</th><th>Fecha</th><th>Hora</th><th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!$lecturas): ?>
                        <tr><td colspan="8" class="sin-resultados"><i class="fa-solid fa-inbox"></i><strong>No existen lecturas para los filtros seleccionados.</strong><span>Prueba otro sensor, estado o rango de fechas.</span></td></tr>
                    <?php else: ?>
                        <?php foreach ($lecturas as $lectura): ?>
                            <?php
                            $claseEstado = "normal";
                            if ($lectura["estado_lectura"] === "Por debajo del mínimo") $claseEstado = "bajo";
                            if ($lectura["estado_lectura"] === "Por encima del máximo") $claseEstado = "alto";
                            ?>
                            <tr>
                                <td><code><?= limpiarReporte($lectura["codigo"]) ?></code></td>
                                <td><?= limpiarReporte($lectura["sensor"]) ?></td>
                                <td><?= limpiarReporte($lectura["tipo"]) ?></td>
                                <td><?= number_format((float) $lectura["valor"], 2, ",", ".") ?></td>
                                <td><?= limpiarReporte($lectura["unidad_medida"]) ?></td>
                                <td><?= date("d/m/Y", strtotime($lectura["fecha_hora"])) ?></td>
                                <td><?= date("H:i:s", strtotime($lectura["fecha_hora"])) ?></td>
                                <td><span class="estado-lectura estado-lectura--<?= $claseEstado ?>"><?= limpiarReporte($lectura["estado_lectura"]) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<script>
window.SIMAR_REPORTES = <?= json_encode($graficas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/reportes.js"></script>
</body>
</html>
