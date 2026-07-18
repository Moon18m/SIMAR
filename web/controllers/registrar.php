<?php

// Inicia la sesión para almacenar mensajes temporales
// que serán mostrados al usuario después de una redirección.
session_start();

// Incluye el archivo encargado de establecer la conexión
// con la base de datos MySQL.
require "../config/conexion.php";

// Verifica que el formulario haya sido enviado mediante
// el método POST para impedir el acceso directo al archivo.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtiene y limpia los datos enviados desde el formulario.
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["email"]);

    // Las contraseñas se reciben tal como fueron escritas
    // por el usuario.
    $password = $_POST["password"];
    $password2 = $_POST["password2"];

    // Almacena todos los errores encontrados durante
    // la validación del formulario.
    $errores = [];

    // Verifica que el nombre completo haya sido ingresado.
    if (empty($nombre)) {
        $errores[] = "El nombre completo es obligatorio.";
    }

    // Verifica que el correo electrónico haya sido ingresado.
    if (empty($correo)) {
        $errores[] = "El correo electrónico es obligatorio.";
    }
    // Comprueba que el correo electrónico tenga un formato válido.
    elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico debe tener un formato válido (ejemplo@dominio.com).";
    }

    // Verifica que la contraseña haya sido ingresada.
    if (empty($password)) {
        $errores[] = "La contraseña es obligatoria.";
    }
    // Comprueba que la contraseña cumpla la longitud mínima requerida.
    elseif (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    // Verifica que la confirmación de la contraseña
    // haya sido ingresada.
    if (empty($password2)) {
        $errores[] = "La confirmación de la contraseña es obligatoria.";
    }
    // Comprueba que la confirmación coincida con la contraseña.
    elseif ($password !== $password2) {
        $errores[] = "La contraseña y su confirmación deben coincidir.";
    }

    // Si existen errores de validación, se almacenan
    // en la sesión y se regresa al formulario.
    if (!empty($errores)) {

        $_SESSION["errores"] = $errores;
        header("Location: ../views/registro.php");
        exit();

    }

    // Consulta si ya existe un usuario registrado
    // con el mismo correo electrónico.
    $sql = "SELECT id_usuario
            FROM usuarios
            WHERE correo = ?";

    // Prepara la consulta para evitar ataques
    // de inyección SQL.
    $stmt = $conn->prepare($sql);

    // Asocia el correo electrónico al parámetro
    // de la consulta preparada.
    $stmt->bind_param("s", $correo);

    // Ejecuta la consulta.
    $stmt->execute();

    // Obtiene el resultado de la consulta.
    $resultado = $stmt->get_result();

    // Verifica si el correo electrónico ya se
    // encuentra registrado.
    if ($resultado->num_rows > 0) {

        $_SESSION["errores"] = [
            "El correo electrónico ya se encuentra registrado."
        ];

        header("Location: ../views/registro.php");
        exit();

    }

    // Genera un hash seguro de la contraseña antes
    // de almacenarla en la base de datos.
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Consulta para registrar el nuevo usuario.
    $sql = "INSERT INTO usuarios
            (
                nombre,
                correo,
                password_hash,
                activo,
                ultimo_acceso
            )
            VALUES
            (
                ?, ?, ?, 1, NULL
            )";

    // Prepara la consulta de inserción.
    $stmt = $conn->prepare($sql);

    // Asocia los datos del usuario a los parámetros
    // de la consulta preparada.
    $stmt->bind_param(
        "sss",
        $nombre,
        $correo,
        $passwordHash
    );

    // Ejecuta el registro del nuevo usuario.
    if ($stmt->execute()) {

        // Redirige al usuario a la página de inicio
        // de sesión cuando el registro es exitoso.
        header("Location: ../views/login.php");
        exit();

    } else {

        // Informa al usuario si ocurre un error
        // durante el proceso de registro.
        $_SESSION["errores"] = [
            "No fue posible completar el registro. Inténtalo nuevamente."
        ];

        header("Location: ../views/registro.php");
        exit();

    }

}