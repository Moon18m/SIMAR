<?php
require "../middleware/auth.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SIMAR</title> 
    <link rel="icon" href="../assets/img/iconos/icono_simar.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>

    <div class="dashboard">

        <aside class="sidebar">

            <a href="dashboard.php" class="logo">
                <img src="../assets/img/iconos/icono_simar.png" alt="SIMAR">
                <h2>S.I.M.A.R.</h2>
            </a>

            <nav>
                <ul>
                    <li class="active"><a href="#"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a></li>
                    <li><a href="#"><i class="fa-solid fa-box"></i><span>Inventario</span></a></li>
                    <li><a href="#"><i class="fa-solid fa-microchip"></i><span>Sensores</span></a></li>
                    <li><a href="#"><i class="fa-solid fa-camera"></i><span>Cámara IA</span></a></li>
                    <li><a href="#"><i class="fa-solid fa-triangle-exclamation"></i><span>Alertas</span></a></li>
                    <li><a href="#"><i class="fa-solid fa-file-lines"></i><span>Reportes</span></a></li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <span>Operador</span>
                <strong><?= $_SESSION["nombre"] ?></strong>
            </div>

        </aside>

        <main class="contenido">

            <header class="topbar">

                <div>
                    <h1>Dashboard</h1>
                    <p>Centro de monitoreo inteligente de S.I.M.A.R.</p>
                </div>

                <div class="acciones">

                    <button id="btnActualizar" class="btn">
                        <i class="fa-solid fa-rotate-right"></i>
                        Actualizar
                    </button>

                    <a href="../controllers/logout.php" class="logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Cerrar sesión
                    </a>

                </div>

            </header>

            <section class="estado-sistema">

                <div class="estado ok" id="estadoESP">
                    <i class="fa-solid fa-wifi"></i>
                    ESP32
                </div>

                <div class="estado ok" id="estadoBD">
                    <i class="fa-solid fa-database"></i>
                    Base de datos
                </div>

                <div class="estado ok" id="estadoIA">
                    <i class="fa-solid fa-brain"></i>
                    IA
                </div>

                <div class="estado ok" id="estadoSensores">
                    <i class="fa-solid fa-microchip"></i>
                    Sensores
                </div>

            </section>

            <section class="cards">

                <div class="card temperatura">
                    <i class="fa-solid fa-temperature-half"></i>
                    <h3>Temperatura</h3>
                    <h2 id="temperaturaActual">-- °C</h2>
                    <span>Última lectura</span>
                </div>

                <div class="card humedad">
                    <i class="fa-solid fa-droplet"></i>
                    <h3>Humedad</h3>
                    <h2 id="humedadActual">-- %</h2>
                    <span>Última lectura</span>
                </div>

                <div class="card productos">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <h3>Productos</h3>
                    <h2 id="totalProductos">0</h2>
                    <span>Registrados</span>
                </div>

                <div class="card alertas">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <h3>Alertas</h3>
                    <h2 id="alertasActivas">0</h2>
                    <span>Activas</span>
                </div>

            </section>

            <section class="graficas">

                <div class="grafica">
                    <h2>Histórico de Temperatura</h2>
                    <canvas id="graficaTemperatura"></canvas>
                </div>

                <div class="grafica">
                    <h2>Histórico de Humedad</h2>
                    <canvas id="graficaHumedad"></canvas>
                </div>

            </section>

            <section class="paneles">

                <div class="panel">

                    <h2>Últimas alertas</h2>

                    <table>

                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody id="tablaAlertas">
                            <tr>
                                <td colspan="3">Cargando información...</td>
                            </tr>
                        </tbody>

                    </table>

                </div>

                <div class="panel">

                    <h2>Productos próximos a vencer</h2>

                    <table>

                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Vencimiento</th>
                                <th>Días</th>
                            </tr>
                        </thead>

                        <tbody id="tablaProductos">
                            <tr>
                                <td colspan="3">Cargando información...</td>
                            </tr>
                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

   
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    
    <script src="../assets/js/dashboard.js"></script>

</body>

</html>