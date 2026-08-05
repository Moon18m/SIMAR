<?php
require "../middleware/auth.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensores | SIMAR</title>
    <link rel="icon" href="../assets/img/iconos/icono_simar.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/sensores.css">
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
                        <li class="active"><a href="sensores.php"><i class="fa-solid fa-microchip"></i><span>Sensores</span></a></li>
                        <li><a href="camara.php"><i class="fa-solid fa-camera"></i><span>Cámara IA</span></a></li>
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
                    <h1>Sensores</h1>
                    <p>Monitoreo en tiempo real de los sensores instalados en el refrigerador.</p>
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

            <section class="conexion-sensores">
                <div class="estado ok" id="estadoConexion">
                    <i class="fa-solid fa-satellite-dish"></i>
                    Sistema conectado
                </div>

                <div class="ultima-actualizacion">
                    <i class="fa-regular fa-clock"></i>
                    Última actualización: <span id="tiempoActualizacion">cargando...</span>
                </div>
            </section>

            <!-- TARJETAS DE SENSORES -->
            <section class="sensor-cards">

                <div class="sensor-card temperatura" id="cardTemperatura">
                    <div class="sensor-card-top">
                        <div class="sensor-icono">
                            <i class="fa-solid fa-temperature-half"></i>
                        </div>
                        <span class="badge-estado" id="badgeTemperatura">Cargando...</span>
                    </div>

                    <h3>Temperatura</h3>
                    <p class="sensor-valor" id="valorTemperatura">-- <span>°C</span></p>

                    <span class="sensor-hora">
                        <i class="fa-regular fa-clock"></i>
                        Última lectura: <span id="horaTemperatura">--:--:--</span>
                    </span>
                </div>

                <div class="sensor-card humedad" id="cardHumedad">
                    <div class="sensor-card-top">
                        <div class="sensor-icono">
                            <i class="fa-solid fa-droplet"></i>
                        </div>
                        <span class="badge-estado" id="badgeHumedad">Cargando...</span>
                    </div>

                    <h3>Humedad</h3>
                    <p class="sensor-valor" id="valorHumedad">-- <span>%</span></p>

                    <span class="sensor-hora">
                        <i class="fa-regular fa-clock"></i>
                        Última lectura: <span id="horaHumedad">--:--:--</span>
                    </span>
                </div>

                <div class="sensor-card pendiente">
                    <div class="sensor-card-top">
                        <div class="sensor-icono">
                            <i class="fa-solid fa-door-closed"></i>
                        </div>
                        <span class="badge-estado badge-pendiente">⚪ Sin instalar</span>
                    </div>

                    <h3>Puerta</h3>
                    <p class="sensor-valor sensor-valor-texto">Sensor magnético</p>

                    <span class="sensor-hora">
                        <i class="fa-solid fa-circle-info"></i>
                        Pendiente de instalación
                    </span>
                </div>

                <div class="sensor-card pendiente">
                    <div class="sensor-card-top">
                        <div class="sensor-icono">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <span class="badge-estado badge-pendiente">⚪ Sin instalar</span>
                    </div>

                    <h3>Corriente</h3>
                    <p class="sensor-valor sensor-valor-texto">Sensor de corriente</p>

                    <span class="sensor-hora">
                        <i class="fa-solid fa-circle-info"></i>
                        Pendiente de instalación
                    </span>
                </div>

            </section>

            <!-- ESTADO DEL SISTEMA -->
            <section class="panel panel-estado">
                <h2>Estado del sistema</h2>

                <div class="resumen-sensores">
                    <div class="resumen-item ok">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <strong id="totalActivos">--</strong>
                            <span>Sensores activos</span>
                        </div>
                    </div>

                    <div class="resumen-item error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div>
                            <strong id="totalError">--</strong>
                            <span>Sensores con error</span>
                        </div>
                    </div>

                    <div class="resumen-item inactivo">
                        <i class="fa-solid fa-circle-minus"></i>
                        <div>
                            <strong id="totalInactivos">--</strong>
                            <span>Sensores inactivos</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- GRAFICO HISTORIAL -->
            <section class="grafica grafica-sensores">
                <div class="grafica-header">
                    <h2>Historial de lecturas</h2>

                    <div class="grafica-controles">
                        <select id="selectSensor">
                            <option value="Temperatura">Temperatura</option>
                            <option value="Humedad">Humedad</option>
                        </select>

                        <select id="selectRango">
                            <option value="1h">Última hora</option>
                            <option value="6h">Últimas 6 horas</option>
                            <option value="24h" selected>Últimas 24 horas</option>
                            <option value="7d">Últimos 7 días</option>
                        </select>
                    </div>
                </div>

                <div class="chart-container">
                    <canvas id="graficaSensores"></canvas>
                </div>
            </section>

            <!-- TABLA DE ULTIMAS LECTURAS -->
            <section class="panel">
                <h2>Últimas lecturas</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Sensor</th>
                            <th>Valor</th>
                            <th>Fecha / Hora</th>
                        </tr>
                    </thead>
                    <tbody id="tablaLecturas">
                        <tr>
                            <td colspan="3">Cargando información...</td>
                        </tr>
                    </tbody>
                </table>
            </section>

        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/sensores.js"></script>

</body>

</html>
