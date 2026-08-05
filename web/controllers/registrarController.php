<?php

require "../config/conexion.php";
require "../services/registroService.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    exit("Método no permitido.");
}

$nombre = trim($_POST["nombre"]);
$correo = trim($_POST["email"]);
$password = $_POST["password"];
$password2 = $_POST["password2"];

// Validar formulario
$errores = validarRegistro(
    $nombre,
    $correo,
    $password,
    $password2
);

if (!empty($errores)) {

    $_SESSION["errores"] = $errores;

    header("Location: ../views/registro.php");

    exit();
}

// Verificar si el correo ya existe
if (correoExiste($conn, $correo)) {

    $_SESSION["errores"] = [
        "El correo electrónico ya se encuentra registrado."
    ];

    header("Location: ../views/registro.php");

    exit();
}

// Registrar usuario
if (registrarUsuario($conn, $nombre, $correo, $password)) {

    header("Location: ../views/login.php");

    exit();
}

$_SESSION["errores"] = [
    "No fue posible completar el registro."
];

header("Location: ../views/registro.php");
exit();