<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <title>Login | SIMAR</title>
</head>

<body>

    <div class="login-container">

        <img src="assets/img/logos/Logo_simar_blanco.jpeg" alt="Logo SIMAR" class="logo">

        <h2>Iniciar Sesión</h2>

        <form action="validar.php" method="POST">

            <div class="input-group">
                <label for="usuario">Usuario</label>
                <input
                    type="text"
                    id="usuario"
                    name="usuario"
                    placeholder="Ingrese su usuario"
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
                Iniciar seion 
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