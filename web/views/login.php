<?php

require "../middleware/cache.php"; // fuerza que el navegador no use la versión en caché

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si el usuario llega al login con una sesión activa (por ejemplo,
// usando la flecha "atrás" del navegador), se cierra la sesión.
if (isset($_SESSION["id_usuario"])) {
    session_unset();
    session_destroy();

    // Reinicia una sesión limpia para poder mostrar el formulario de login normalmente.
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/img/iconos/icono_simar.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <title>Login | SIMAR</title>
</head>

<body>

    <div class="login-container">

        <img src="../assets/img/logos/Logo_simar_blanco.jpeg" alt="Logo SIMAR" class="logo">

        <h2>Iniciar Sesión</h2>

        <?php if (isset($_GET["error"])): ?>

            <div class="alerta error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>
                    <?= htmlspecialchars($_GET["error"]) ?>
                </span>
            </div>

        <?php endif; ?>

        <form action="../controllers/loginController.php" method="POST">

            <div class="input-group">
                <label for="usuario">Correo electrónico</label>
                <input
                    type="email"
                    id="correo"
                    name="correo"
                    placeholder="Ingrese su correo electrónico"
                    required>
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Ingrese su contraseña"
                    required>
            </div>

            <button type="submit" class="btn-login">
                Iniciar sesion 
            </button>

        </form>

        <a href="index.php" class="volver">
            Volver al inicio
        </a>
        <br>
        <a href="registro.php" class="registrar">
            No tengo una cuenta, registrarme
        </a>

    </div>

</body>

</html>