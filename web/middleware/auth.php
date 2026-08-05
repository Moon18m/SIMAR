<?php

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/",
        "httponly" => true,   // JS no puede leer la cookie -> mitiga robo por XSS
        "secure" => true,     // solo se envía por HTTPS (pon false si aún pruebas en http local)
        "samesite" => "Strict"
    ]);

    session_start();
}

require "cache.php";
require "timeout.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../views/login.php");
    exit;
}
