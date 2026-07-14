<?php

require "cache.php";
require "timeout.php";

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../../view/login.php");
    exit;
}

?>