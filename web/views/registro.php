<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/registro.css">
<link rel="stylesheet" href="../assets/css/variables.css">
<link rel="icon" href="../assets/img/iconos/icono_simar.png" type="image/png">

</head>
<body>

<div class="book">
  <div class="book-inner">
    <div class="eyebrow">S.I.M.A.R</div>
    <h1>Registrate</h1>
    <p class="sub">Completa tus datos para crear una cuenta.</p>
      <?php if (isset($_SESSION["errores"])): ?>

        <div class="alerta error">
            <strong>No fue posible completar el registro.</strong>

            <ul>
                <?php foreach ($_SESSION["errores"] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php
        unset($_SESSION["errores"]);
        endif;
        ?>

      <form id="registroForm"action="../controllers/registrar.php"method="POST">
      <div class="row">
        <div class="field" data-field="nombre">
          <label for="nombre">Nombre completo</label>
          <input type="text" id="nombre" name="nombre" autocomplete="name" placeholder="Escribe tu nombre de usuario">
          <div class="error-msg">Escribe tu nombre completo.</div>
        </div>
      </div>

      <div class="field" data-field="email">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" autocomplete="email"placeholder="ejemplo@correo.com"> 
        <div class="error-msg">Ingresa un correo válido.</div>
      </div>

      <div class="row">
        <div class="field" data-field="password">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password"  autocomplete="new-password" placeholder="Escribe tu contraseña">
          <div class="error-msg">Debe tener al menos 8 caracteres.</div>
        </div>
        <div class="field" data-field="password2">
          <label for="password2">Confirmar contraseña</label>
          <input type="password" id="password2" name="password2" autocomplete="new-password" placeholder="Confirma tu contraseña">
          <div class="error-msg">Las contraseñas no coinciden.</div>
        </div>
      </div>

      <div class="submit-row">
        <button type="submit" class="stamp">Registrar</button>
      </div>

    </form>

    <a href="index.php" class="stamp volver">
            Volver al inicio
        </a>

  </div>
</div>
</body>
</html>