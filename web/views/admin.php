<?php
// Permite el acceso únicamente a personas con rol Administrador.
require_once __DIR__ . "/../middleware/admin.php";

// Carga consultas, validaciones y operaciones del CRUD.
require_once __DIR__ . "/../controllers/adminUsuarios.php";

// Configura la hora de Colombia.
date_default_timezone_set("America/Bogota");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Administración | SIMAR</title>

    <link
        rel="icon"
        href="../assets/img/iconos/icono_simar.png"
        type="image/png"
    >

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/variables.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >
</head>

<body>
    <div class="dashboard">

        <!-- Menú principal del sistema. -->
        <aside
            class="sidebar"
            aria-label="Navegación principal"
        >
            <div>
                <a
                    href="admin.php"
                    class="logo"
                >
                    <img
                        src="../assets/img/iconos/icono_simar.png"
                        alt="SIMAR"
                    >

                    <h2>S.I.M.A.R.</h2>
                </a>

                <nav>
                    <ul>
                        
                        
                        
                        <li><a href="reportes.php"><i class="fa-solid fa-file-lines"></i><span>Reportes</span></a></li>
                        <li class="active">
                            <a href="admin.php" aria-current="page">
                                <i class="fa-solid fa-user-gear"></i>
                                <span>Administración</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="sidebar-footer">
                <span>
                    <?= limpiarSalida($_SESSION["rol"]) ?>
                </span>

                <strong>
                    <?= limpiarSalida($_SESSION["nombre"]) ?>
                </strong>
            </div>
        </aside>

        <!-- Contenido del módulo administrativo. -->
        <main class="contenido">

            <header class="topbar">
                <div>
                    <h1>Administración</h1>

                    <p>
                        Crea, consulta, actualiza y elimina usuarios del sistema.
                    </p>
                </div>

                <a
                    href="../controllers/logoutController.php"
                    class="logout"
                >
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Cerrar sesión
                </a>
            </header>

            <!-- Mensaje de resultado de cada operación. -->
            <?php if ($mensajeAdmin): ?>
                <div
                    class="mensaje mensaje--<?= limpiarSalida(
                        $mensajeAdmin["tipo"]
                    ) ?>"
                    role="alert"
                >
                    <i
                        class="fa-solid <?= $mensajeAdmin["tipo"] === "exito"
                            ? "fa-circle-check"
                            : "fa-circle-exclamation" ?>"
                    ></i>

                    <?= limpiarSalida($mensajeAdmin["texto"]) ?>
                </div>
            <?php endif; ?>

            <!-- Tarjetas con información rápida. -->
            <section
                class="resumen-admin"
                aria-label="Resumen de usuarios"
            >
                <article class="resumen-card">
                    <i class="fa-solid fa-users"></i>

                    <div>
                        <span>Total de usuarios</span>

                        <strong>
                            <?= $resumenUsuarios["total"] ?>
                        </strong>
                    </div>
                </article>

                <article class="resumen-card resumen-card--activo">
                    <i class="fa-solid fa-user-check"></i>

                    <div>
                        <span>Usuarios activos</span>

                        <strong>
                            <?= $resumenUsuarios["activos"] ?>
                        </strong>
                    </div>
                </article>

                <article class="resumen-card resumen-card--admin">
                    <i class="fa-solid fa-user-shield"></i>

                    <div>
                        <span>Administradores</span>

                        <strong>
                            <?= $resumenUsuarios["administradores"] ?>
                        </strong>
                    </div>
                </article>
            </section>

            <!-- Formulario para crear o editar un usuario. -->
            <section
                class="panel"
                id="formulario-usuario"
            >
                <div class="panel-encabezado">
                    <div>
                        <span class="panel-etiqueta">
                            <?= $modo === "editar"
                                ? "Actualizar registro"
                                : "Nuevo registro" ?>
                        </span>

                        <h2>
                            <?= $modo === "editar"
                                ? "Editar usuario"
                                : "Crear usuario" ?>
                        </h2>
                    </div>

                    <?php if ($modo === "editar"): ?>
                        <a
                            class="btn btn--secundario"
                            href="admin.php"
                        >
                            Cancelar edición
                        </a>
                    <?php endif; ?>
                </div>

                <form
                    class="form-usuario"
                    method="post"
                    action="admin.php"
                    autocomplete="off"
                >
                    <input
                        type="hidden"
                        name="csrf"
                        value="<?= limpiarSalida(
                            $_SESSION["csrf_admin"]
                        ) ?>"
                    >

                    <input
                        type="hidden"
                        name="accion"
                        value="<?= $modo === "editar"
                            ? "actualizar"
                            : "crear" ?>"
                    >

                    <?php if (
                        $modo === "editar" &&
                        $usuarioSeleccionado
                    ): ?>
                        <input
                            type="hidden"
                            name="id_usuario"
                            value="<?= (int) $usuarioSeleccionado[
                                "id_usuario"
                            ] ?>"
                        >
                    <?php endif; ?>

                    <div class="campo">
                        <label for="nombre">
                            Nombre completo
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            maxlength="100"
                            required
                            value="<?= limpiarSalida(
                                $modo === "editar"
                                    ? ($usuarioSeleccionado["nombre"] ?? "")
                                    : ""
                            ) ?>"
                            placeholder="Ej. Sofía Palacios"
                        >
                    </div>

                    <div class="campo">
                        <label for="correo">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            maxlength="100"
                            required
                            value="<?= limpiarSalida(
                                $modo === "editar"
                                    ? ($usuarioSeleccionado["correo"] ?? "")
                                    : ""
                            ) ?>"
                            placeholder="nombre@correo.com"
                        >
                    </div>

                    <div class="campo">
                        <label for="password">
                            Contraseña
                            <?= $modo === "editar" ? "opcional" : "" ?>
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            minlength="8"
                            <?= $modo === "editar" ? "" : "required" ?>
                            placeholder="Mínimo 8 caracteres"
                        >

                        <?php if ($modo === "editar"): ?>
                            <small>
                                Déjala vacía para conservar la contraseña actual.
                            </small>
                        <?php endif; ?>
                    </div>

                    <div class="campo">
                        <label for="rol">
                            Rol
                        </label>

                        <?php
                        $rolActual = $modo === "editar"
                            ? ($usuarioSeleccionado["rol"] ?? "Operador")
                            : "Operador";
                        ?>

                        <select
                            id="rol"
                            name="rol"
                            required
                        >
                            <option
                                value="Operador"
                                <?= $rolActual === "Operador"
                                    ? "selected"
                                    : "" ?>
                            >
                                Operador
                            </option>

                            <option
                                value="Administrador"
                                <?= $rolActual === "Administrador"
                                    ? "selected"
                                    : "" ?>
                            >
                                Administrador
                            </option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="activo">
                            Estado
                        </label>

                        <?php
                        $estadoActual = $modo === "editar"
                            ? (string) (
                                $usuarioSeleccionado["activo"] ?? "1"
                            )
                            : "1";
                        ?>

                        <select
                            id="activo"
                            name="activo"
                            required
                        >
                            <option
                                value="1"
                                <?= $estadoActual === "1"
                                    ? "selected"
                                    : "" ?>
                            >
                                Activo
                            </option>

                            <option
                                value="0"
                                <?= $estadoActual === "0"
                                    ? "selected"
                                    : "" ?>
                            >
                                Inactivo
                            </option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        class="btn btn--principal"
                    >
                        <i
                            class="fa-solid <?= $modo === "editar"
                                ? "fa-floppy-disk"
                                : "fa-user-plus" ?>"
                        ></i>

                        <?= $modo === "editar"
                            ? "Guardar cambios"
                            : "Crear usuario" ?>
                    </button>
                </form>
            </section>

           
            <?php if (
                $modo === "ver" &&
                $usuarioSeleccionado
            ): ?>

                <?php
                $tieneFechaCreacion = !empty(
                    $usuarioSeleccionado["fecha_creacion"]
                );

                $fechaCreacion = $tieneFechaCreacion
                    ? date(
                        "d/m/Y",
                        strtotime(
                            $usuarioSeleccionado["fecha_creacion"]
                        )
                    )
                    : "Sin fecha registrada";

                $horaCreacion = $tieneFechaCreacion
                    ? date(
                        "h:i A",
                        strtotime(
                            $usuarioSeleccionado["fecha_creacion"]
                        )
                    )
                    : "Sin hora registrada";

                $tieneUltimoAcceso = !empty(
                    $usuarioSeleccionado["ultimo_acceso"]
                );

                $fechaUltimoAcceso = $tieneUltimoAcceso
                    ? date(
                        "d/m/Y",
                        strtotime(
                            $usuarioSeleccionado["ultimo_acceso"]
                        )
                    )
                    : "Sin acceso registrado";

                $horaUltimoAcceso = $tieneUltimoAcceso
                    ? date(
                        "h:i A",
                        strtotime(
                            $usuarioSeleccionado["ultimo_acceso"]
                        )
                    )
                    : "Sin acceso registrado";
                ?>

                <section class="panel detalle-usuario">
                    <div class="panel-encabezado">
                        <div>
                            <span class="panel-etiqueta">
                                Consulta individual
                            </span>

                            <h2>Datos del usuario</h2>
                        </div>

                        <a
                            class="btn btn--secundario"
                            href="admin.php"
                        >
                            Cerrar consulta
                        </a>
                    </div>

                    <dl>
                        <div>
                            <dt>ID</dt>

                            <dd>
                                <?= (int) $usuarioSeleccionado[
                                    "id_usuario"
                                ] ?>
                            </dd>
                        </div>

                        <div>
                            <dt>Nombre</dt>

                            <dd>
                                <?= limpiarSalida(
                                    $usuarioSeleccionado["nombre"]
                                ) ?>
                            </dd>
                        </div>

                        <div>
                            <dt>Correo</dt>

                            <dd>
                                <?= limpiarSalida(
                                    $usuarioSeleccionado["correo"]
                                ) ?>
                            </dd>
                        </div>

                        <div>
                            <dt>Rol</dt>

                            <dd>
                                <?= limpiarSalida(
                                    $usuarioSeleccionado["rol"]
                                ) ?>
                            </dd>
                        </div>

                        <div>
                            <dt>Estado</dt>

                            <dd>
                                <?= (int) $usuarioSeleccionado["activo"] === 1
                                    ? "Activo"
                                    : "Inactivo" ?>
                            </dd>
                        </div>

                        <div>
                            <dt>Fecha de creación</dt>

                            <dd>
                                <?= limpiarSalida($fechaCreacion) ?>
                            </dd>
                        </div>

                        <div>
                            <dt>Hora de creación</dt>

                            <dd>
                                <?= limpiarSalida($horaCreacion) ?>
                            </dd>
                        </div>

                        <div>
                            <dt>Fecha del último acceso</dt>

                            <dd>
                                <?= limpiarSalida($fechaUltimoAcceso) ?>
                            </dd>
                        </div>

                        <div>
                            <dt>Hora del último acceso</dt>

                            <dd>
                                <?= limpiarSalida($horaUltimoAcceso) ?>
                            </dd>
                        </div>
                    </dl>
                </section>
            <?php endif; ?>

            <!-- Tabla general de registros. -->
            <section class="panel tabla-panel">
                <div class="panel-encabezado panel-encabezado--tabla">
                    <div>
                        <span class="panel-etiqueta">
                            Consulta general
                        </span>

                        <h2>Usuarios registrados</h2>
                    </div>

                    <form
                        class="buscador"
                        method="get"
                        action="admin.php"
                    >
                        <label
                            class="sr-only"
                            for="buscar"
                        >
                            Buscar usuario
                        </label>

                        <input
                            type="search"
                            id="buscar"
                            name="buscar"
                            value="<?= limpiarSalida($buscar) ?>"
                            placeholder="Buscar por nombre o correo"
                        >

                        <button
                            class="btn btn--principal"
                            type="submit"
                        >
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Consultar
                        </button>

                        <?php if ($buscar !== ""): ?>
                            <a
                                class="btn btn--secundario"
                                href="admin.php"
                            >
                                Limpiar
                            </a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="tabla-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha de creación</th>
                                <th>Hora de creación</th>
                                <th>Fecha del último acceso</th>
                                <th>Hora del último acceso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!$usuarios): ?>
                                <tr>
                                    <td
                                        colspan="10"
                                        class="tabla-vacia"
                                    >
                                        No se encontraron usuarios.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($usuarios as $usuario): ?>

                                <?php
                                $tieneFechaCreacionUsuario = !empty(
                                    $usuario["fecha_creacion"]
                                );

                                $fechaCreacionUsuario =
                                    $tieneFechaCreacionUsuario
                                        ? date(
                                            "d/m/Y",
                                            strtotime(
                                                $usuario["fecha_creacion"]
                                            )
                                        )
                                        : "Sin fecha";

                                $horaCreacionUsuario =
                                    $tieneFechaCreacionUsuario
                                        ? date(
                                            "h:i A",
                                            strtotime(
                                                $usuario["fecha_creacion"]
                                            )
                                        )
                                        : "Sin hora";

                                $tieneUltimoAccesoUsuario = !empty(
                                    $usuario["ultimo_acceso"]
                                );

                                $fechaUltimoAccesoUsuario =
                                    $tieneUltimoAccesoUsuario
                                        ? date(
                                            "d/m/Y",
                                            strtotime(
                                                $usuario["ultimo_acceso"]
                                            )
                                        )
                                        : "Sin acceso";

                                $horaUltimoAccesoUsuario =
                                    $tieneUltimoAccesoUsuario
                                        ? date(
                                            "h:i A",
                                            strtotime(
                                                $usuario["ultimo_acceso"]
                                            )
                                        )
                                        : "Sin acceso";
                                ?>

                                <tr>
                                    <td data-label="ID">
                                        <?= (int) $usuario["id_usuario"] ?>
                                    </td>

                                    <td data-label="Nombre">
                                        <?= limpiarSalida(
                                            $usuario["nombre"]
                                        ) ?>
                                    </td>

                                    <td data-label="Correo">
                                        <?= limpiarSalida(
                                            $usuario["correo"]
                                        ) ?>
                                    </td>

                                    <td data-label="Rol">
                                        <?= limpiarSalida(
                                            $usuario["rol"]
                                        ) ?>
                                    </td>

                                    <td data-label="Estado">
                                        <span
                                            class="estado estado--<?= 
                                                (int) $usuario["activo"] === 1
                                                    ? "activo"
                                                    : "inactivo"
                                            ?>"
                                        >
                                            <?= (int) $usuario["activo"] === 1
                                                ? "Activo"
                                                : "Inactivo" ?>
                                        </span>
                                    </td>

                                    <td data-label="Fecha de creación">
                                        <?= limpiarSalida(
                                            $fechaCreacionUsuario
                                        ) ?>
                                    </td>

                                    <td data-label="Hora de creación">
                                        <?= limpiarSalida(
                                            $horaCreacionUsuario
                                        ) ?>
                                    </td>

                                    <td data-label="Fecha del último acceso">
                                        <?= limpiarSalida(
                                            $fechaUltimoAccesoUsuario
                                        ) ?>
                                    </td>

                                    <td data-label="Hora del último acceso">
                                        <?= limpiarSalida(
                                            $horaUltimoAccesoUsuario
                                        ) ?>
                                    </td>

                                    <td data-label="Acciones">
                                        <div class="acciones-tabla">
                                            <a
                                                class="accion accion--ver"
                                                href="admin.php?modo=ver&id=<?= 
                                                    (int) $usuario["id_usuario"]
                                                ?>"
                                                title="Consultar"
                                            >
                                                <i class="fa-solid fa-eye"></i>
                                                <span>Consultar</span>
                                            </a>

                                            <a
                                                class="accion accion--editar"
                                                href="admin.php?modo=editar&id=<?= 
                                                    (int) $usuario["id_usuario"]
                                                ?>#formulario-usuario"
                                                title="Editar"
                                            >
                                                <i class="fa-solid fa-pen"></i>
                                                <span>Editar</span>
                                            </a>

                                            <form
                                                method="post"
                                                action="admin.php"
                                                class="form-eliminar"
                                                data-nombre="<?= limpiarSalida(
                                                    $usuario["nombre"]
                                                ) ?>"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="csrf"
                                                    value="<?= limpiarSalida(
                                                        $_SESSION["csrf_admin"]
                                                    ) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="accion"
                                                    value="eliminar"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="id_usuario"
                                                    value="<?= 
                                                        (int) $usuario[
                                                            "id_usuario"
                                                        ]
                                                    ?>"
                                                >

                                                <button
                                                    class="accion accion--eliminar"
                                                    type="submit"
                                                    title="Eliminar"
                                                    <?= 
                                                        (int) $usuario[
                                                            "id_usuario"
                                                        ] ===
                                                        (int) $_SESSION[
                                                            "id_usuario"
                                                        ]
                                                            ? "disabled"
                                                            : ""
                                                    ?>
                                                >
                                                    <i class="fa-solid fa-trash"></i>
                                                    <span>Eliminar</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>

</html>