<?php

require "auth.php";

if ($_SESSION["rol"] !== "Administrador") {

    header("Location: ../views/dashboard.php");
    exit;
}

?>