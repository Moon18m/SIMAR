<?php

$tiempoMaximo = 30 * 60; // 30 minutos en segundos

if (isset($_SESSION["ultimo_acceso"])) {

    if (time() - $_SESSION["ultimo_acceso"] > $tiempoMaximo) {

        session_unset();
        session_destroy();

        header("Location: ../../view/login.php");
        exit;
    }
}

$_SESSION["ultimo_acceso"] = time();

?>