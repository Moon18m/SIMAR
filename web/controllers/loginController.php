<?php

require "../config/conexion.php";
require "../services/loginService.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Método no permitido.");
}

$correo = trim($_POST["correo"]);
$password = $_POST["password"];

$resultado = iniciarSesion(
    $conn,
    $correo,
    $password
);

if ($resultado["success"]) {

    if ($_SESSION["rol"] === "Administrador") {

        header("Location: ../views/admin.php");
        exit();

    }

    header("Location: ../views/dashboard.php");
    exit();
}

header("Location: ../views/login.php?error=" . urlencode($resultado["mensaje"]));
exit();