<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Elimina todas las variables de sesión
session_unset();

// Destruye la sesión
session_destroy();

// Regresa al login
header("Location: ../views/login.php");
exit();
