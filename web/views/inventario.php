<?php
require "../middleware/auth.php";
require "../config/conexion.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario · SIMAR</title>

    <link rel="icon" href="../assets/img/iconos/icono_simar.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/inventario.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">

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

                    <li class="active"><a href="inventario.php"><i class="fa-solid fa-box"></i><span>Inventario</span></a></li>

                    <li><a href="sensores.php"><i class="fa-solid fa-microchip"></i><span>Sensores</span></a></li>

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


        <header class="inventario-header">

            <div>
                <span class="subtitulo">Gestión inteligente del inventario</span>
                <h1>Inventario</h1>
                <p>Consulta y administra todas las bebidas registradas por SIMAR.</p>
            </div>

            <div class="header-actions">

                <button class="btn" id="btnAgregarProducto">
                    <i class="fa-solid fa-plus"></i>
                    Agregar producto
                </button>

                <a href="../controllers/inventario/exportarController" class="btn">
                    <i class="fa-solid fa-file-export"></i>
                    Exportar
                </a>

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

        <section class="cards">

            <article class="card">
                <span>Total</span>
                <h2 id="totalProductos">0</h2>
            </article> 

            <article class="card success">
                <span>Vigentes</span>
                <h2 id="productosVigentes">0</h2>
            </article>

            <article class="card warning">
                <span>Por vencer</span>
                <h2 id="productosVencer">0</h2>
            </article>

            <article class="card danger">
                <span>Vencidos</span>
                <h2 id="productosVencidos">0</h2>
            </article>

        </section>

        <section class="toolbar">

            <input
                type="search"
                id="buscarProducto"
                placeholder="Buscar producto...">

            <select id="estado">

                <option value="todos">Todos los estados</option>
                <option value="vigente">Vigentes</option>
                <option value="vencer">Por vencer</option>
                <option value="vencido">Vencidos</option>

            </select>

            <select id="orden">

                <option value="fecha">Fecha de ingreso</option>
                <option value="nombre">Nombre</option>
                <option value="vida">Vida útil</option>
                <option value="cantidad">Cantidad</option>

            </select>

        </section>
        
        <div id="cargandoInventario" class="loading">
            <div class="spinner"></div>
            <span>Cargando inventario...</span>
        </div>

        <section class="tabla-contenedor">

            <table>

                <thead>

                    <tr>

                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Ingreso</th>
                        <th>Vida útil</th>
                        <th>Estado</th>
                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody id="tablaInventario"></tbody>

            </table>

        </section>

        <div class="inventario-vacio" id="inventarioVacio" style="display:none;">

            <i class="fa-solid fa-box-open"></i>

            <h3>No hay productos registrados</h3>

            <p>Los productos detectados por SIMAR aparecerán aquí automáticamente.</p>

        </div>

        <div class="modal" id="modalVer">

            <div class="modal-content">

                <div class="modal-header">

                    <div>

                        <span class="subtitulo">
                            Información del inventario
                        </span>

                        <h2>
                            Detalle del producto
                        </h2>

                    </div>

                </div>

                <div class="modal-form" id="detalleProducto">

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn"
                        id="cerrarModalVer">

                        Cerrar

                    </button>

                </div>

            </div>

        </div>

        <div class="modal" id="modalEditar">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <span class="subtitulo">
                        Gestión de inventario
                    </span>

                    <h2>
                        Editar producto
                    </h2>

                </div>

                
            </div>

                    <form id="formEditarInventario">

                        <input
                            type="hidden"
                            id="editarIdInventario"
                            name="id_inventario">

                        <div class="campo">

                            <label for="editarNombre">
                                Producto
                            </label>

                            <input
                                type="text"
                                id="editarNombre"
                                readonly>

                        </div>

                        <div class="campo">

                            <label for="editarCantidad">
                                Cantidad
                            </label>

                            <input
                                type="number"
                                id="editarCantidad"
                                name="cantidad"
                                min="1"
                                required>

                        </div>

                        <div class="campo">

                            <label for="editarFechaIngreso">
                                Fecha de ingreso
                            </label>

                            <input
                                type="datetime-local"
                                id="editarFechaIngreso"
                                name="fecha_ingreso"
                                required>

                        </div>

                        <div class="campo">

                            <label for="editarVidaUtil">
                                Vida útil restante (horas)
                            </label>

                            <input
                                type="number"
                                id="editarVidaUtil"
                                name="vida_util_calculada"
                                min="0"
                                required>

                        </div>

                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn"
                                id="cancelarEdicion">

                                Cancelar

                            </button>

                            <button
                                type="submit"
                                class="btn btn-guardar">

                                <i class="fa-solid fa-floppy-disk"></i>

                                Guardar cambios

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        <div class="modal" id="modalAgregar">

            <div class="modal-content">

                <div class="modal-header">

                    <div>

                        <span class="subtitulo">
                            Gestión de inventario
                        </span>

                        <h2>
                            Agregar producto
                        </h2>

                    </div>

                </div>

                <form id="formAgregar">

                    <div class="campo">

                        <label for="agregarProducto">
                            Producto
                        </label>

                        <select
                            id="agregarProducto"
                            name="id_producto"
                            required>

                            <option value="">Selecciona uno...</option>

                            <?php
                            $productosDisponibles = $conn->query(
                                "SELECT id_producto, nombre FROM productos ORDER BY nombre"
                            );
                            while ($p = $productosDisponibles->fetch_assoc()):
                            ?>
                                <option value="<?= $p['id_producto'] ?>">
                                    <?= htmlspecialchars($p['nombre']) ?>
                                </option>
                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="campo">

                        <label for="agregarCantidad">
                            Cantidad
                        </label>

                        <input
                            type="number"
                            id="agregarCantidad"
                            name="cantidad"
                            min="1"
                            required>

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn"
                            id="cancelarAgregar">

                            Cancelar

                        </button>

                        <button
                            type="submit"
                            class="btn btn-guardar">

                            <i class="fa-solid fa-check"></i>

                            Guardar

                        </button>

                    </div>

                </form>

            </div>

        </div>

        </main>

    </div>

<script src="../assets/js/inventario.js"></script>

</body>

</html>
