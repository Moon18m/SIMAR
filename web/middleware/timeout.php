<?php

// Si por alguna razón se incluye este archivo sin una sesión activa
// (por ejemplo, fuera de orden respecto a auth.php), no hace nada.
if (session_status() !== PHP_SESSION_ACTIVE) {
    return;
}

$tiempoMaximo = 30 * 60; // 30 minutos en segundos

if (isset($_SESSION["ultimo_acceso"])) {

    if (time() - $_SESSION["ultimo_acceso"] > $tiempoMaximo) {

        session_unset();
        session_destroy();

        header("Location: ../views/login.php");
        exit;
    }
}

$_SESSION["ultimo_acceso"] = time();
