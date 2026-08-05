<?php

require "../middleware/auth.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas | SIMAR</title>
    <link rel="icon" href="../assets/img/iconos/icono_simar.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/alertas.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>

    <div class="dashboard">

        <!-- Barra lateral principal. Conserva la navegación del Dashboard. -->
        <aside class="sidebar" aria-label="Navegación principal">

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
                        <li><a href="camara.php"><i class="fa-solid fa-camera"></i><span>Cámara IA</span></a></li>
                        <li class="active"><a href="alertas.php" aria-current="page"><i class="fa-solid fa-triangle-exclamation"></i><span>Alertas</span></a></li>
                        <?php if (($_SESSION["rol"] ?? "") === "Administrador"): ?>
                        <li><a href="reportes.php"><i class="fa-solid fa-file-lines"></i><span>Reportes</span></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>

            <div class="sidebar-footer">
                <span><?= htmlspecialchars($_SESSION["rol"] ?? "", ENT_QUOTES, "UTF-8") ?></span>
                <strong id="nombreUsuario"><?= htmlspecialchars($_SESSION["nombre"]) ?></strong>
            </div>

        </aside>

        <!-- Contenido del módulo. -->
        <main class="contenido" id="contenidoAlertas">

            <header class="topbar">
                <div>
                    <h1>Alertas</h1>
                    <p>Consulta y seguimiento de eventos que afectan la conservación de los alimentos.</p>
                </div>

                <div class="acciones">
                    <button type="button" id="btnActualizarAlertas" class="btn" data-action="actualizar-alertas">
                        <i class="fa-solid fa-rotate-right"></i>
                        Actualizar
                    </button>

                    <a href="../controllers/logoutController.php" class="logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Cerrar sesión
                    </a>
                </div>
            </header>

            <!-- Los valores se cargarán desde la tabla alertas. -->
            <section class="resumen-alertas" id="resumenAlertas" aria-label="Resumen de alertas">

                <article class="resumen-card resumen-card--total" data-resumen="total">
                    <i class="fa-solid fa-bell"></i>
                    <div>
                        <span>Total de alertas</span>
                        <strong id="totalAlertas" data-field="total">0</strong>
                    </div>
                </article>

                <article class="resumen-card resumen-card--activas" data-resumen="activas">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>
                        <span>Alertas activas</span>
                        <strong id="alertasActivas" data-field="activas">0</strong>
                    </div>
                </article>

                <article class="resumen-card resumen-card--atendidas" data-resumen="atendidas">
                    <i class="fa-solid fa-circle-check"></i>
                    <div>
                        <span>Alertas atendidas</span>
                        <strong id="alertasAtendidas" data-field="atendidas">0</strong>
                    </div>
                </article>

                <article class="resumen-card resumen-card--hoy" data-resumen="hoy">
                    <i class="fa-solid fa-calendar-day"></i>
                    <div>
                        <span>Generadas hoy</span>
                        <strong id="alertasHoy" data-field="hoy">0</strong>
                    </div>
                </article>

            </section>

            <!-- Formulario GET listo para construir filtros en una consulta SQL. -->
            <section class="panel filtros-panel" aria-labelledby="tituloFiltros">
                <div class="panel-encabezado">
                    <div>
                        <span class="panel-etiqueta">Consulta de registros</span>
                        <h2 id="tituloFiltros">Filtrar alertas</h2>
                    </div>

                    <button type="reset" class="btn btn--limpiar" id="btnLimpiarFiltros" form="formFiltrosAlertas">
                        <i class="fa-solid fa-eraser"></i>
                        Limpiar
                    </button>
                </div>

                <form class="filtros" id="formFiltrosAlertas" method="get" autocomplete="off">

                    <div class="campo campo--busqueda">
                        <label for="buscarAlerta">Buscar</label>
                        <div class="campo-icono">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            <input type="search" id="buscarAlerta" name="buscar" placeholder="Mensaje, producto o identificador">
                        </div>
                    </div>

                    <div class="campo">
                        <label for="filtroEstado">Estado</label>
                        <select id="filtroEstado" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="Activa">Activa</option>
                            <option value="Resuelta">Resuelta</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="filtroTipo">Tipo</label>
                        <select id="filtroTipo" name="tipo">
                            <option value="">Todos los tipos</option>
                            <!-- Las opciones restantes se cargarán desde la base de datos. -->
                        </select>
                    </div>

                    <div class="campo">
                        <label for="fechaDesde">Desde</label>
                        <input type="date" id="fechaDesde" name="fecha_desde">
                    </div>

                    <div class="campo">
                        <label for="fechaHasta">Hasta</label>
                        <input type="date" id="fechaHasta" name="fecha_hasta">
                    </div>

                    <button type="submit" class="btn-filtrar" id="btnAplicarFiltros">
                        <i class="fa-solid fa-filter"></i>
                        Aplicar filtros
                    </button>

                </form>
            </section>

            <!-- Tabla preparada para recibir resultados de PHP o JavaScript. -->
            <section class="panel tabla-panel" aria-labelledby="tituloListado">

                <div class="panel-encabezado panel-encabezado--tabla">
                    <div>
                        <span class="panel-etiqueta">Historial del sistema</span>
                        <h2 id="tituloListado">Registro de alertas</h2>
                    </div>

                    <span class="resultado-contador" id="contadorResultados" aria-live="polite">
                        <strong id="cantidadResultados">0</strong> registros
                    </span>
                </div>

                <div class="tabla-contenedor">
                    <table id="tablaAlertas">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Fecha y hora</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Producto</th>
                                <th scope="col">Descripción</th>
                                <th scope="col">Estado</th>
                                <th scope="col" class="columna-acciones">Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="cuerpoTablaAlertas" aria-live="polite">
                            <!-- Mensaje temporal. Debe reemplazarse al cargar los datos. -->
                            <tr id="filaCargaAlertas" class="fila-mensaje">
                                <td colspan="7">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                    Cargando alertas...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Estados alternos para respuestas vacías o errores de consulta. -->
                <div class="estado-tabla" id="estadoSinAlertas" hidden>
                    <i class="fa-regular fa-bell-slash"></i>
                    <h3>No se encontraron alertas</h3>
                    <p>Ajusta los filtros o consulta nuevamente.</p>
                </div>

                <div class="estado-tabla estado-tabla--error" id="estadoErrorAlertas" role="alert" hidden>
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <h3>No fue posible cargar la información</h3>
                    <p id="mensajeErrorAlertas">Intenta actualizar los registros.</p>
                </div>

                <!-- La paginación se construirá con el total devuelto por MySQL. -->
                <nav class="paginacion" id="paginacionAlertas" aria-label="Paginación de alertas" hidden>
                    <button type="button" class="paginacion__boton" id="paginaAnterior" data-action="pagina-anterior" disabled>
                        <i class="fa-solid fa-chevron-left"></i>
                        Anterior
                    </button>

                    <span id="informacionPagina">Página 1 de 1</span>

                    <button type="button" class="paginacion__boton" id="paginaSiguiente" data-action="pagina-siguiente" disabled>
                        Siguiente
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </nav>

            </section>

            <!-- Plantilla no visible para crear filas con datos obtenidos de MySQL. -->
            <template id="plantillaFilaAlerta">
                <tr class="fila-alerta" data-alerta-id="">
                    <td class="alerta-id" data-field="id_alerta"></td>
                    <td class="alerta-fecha" data-field="fecha_hora"></td>
                    <td class="alerta-tipo" data-field="tipo"></td>
                    <td class="alerta-producto" data-field="producto"></td>
                    <td class="alerta-mensaje" data-field="mensaje"></td>
                    <td>
                        <span class="estado-alerta" data-field="estado"></span>
                    </td>
                    <td class="acciones-tabla">
                        <button type="button" class="accion-icono accion-icono--consultar" data-action="consultar" aria-label="Consultar alerta">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <button type="button" class="accion-icono accion-icono--atender" data-action="atender" aria-label="Marcar alerta como atendida">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </td>
                </tr>
            </template>

        </main>

    </div>

    <script src="../assets/js/alertas.js"></script>
</body>

</html>
