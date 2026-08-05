<?php


// Valida los datos del formulario de registro
function validarRegistro($nombre, $correo, $password, $password2)
{
    $errores = [];

    if (empty($nombre)) {
        $errores[] = "El nombre completo es obligatorio.";
    }

    if (empty($correo)) {

        $errores[] = "El correo electrónico es obligatorio.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $errores[] = "El correo electrónico debe tener un formato válido.";

    }

    if (empty($password)) {

        $errores[] = "La contraseña es obligatoria.";

    } elseif (strlen($password) < 8) {

        $errores[] = "La contraseña debe tener al menos 8 caracteres.";

    }

    if (empty($password2)) {

        $errores[] = "La confirmación de la contraseña es obligatoria.";

    } elseif ($password != $password2) {

        $errores[] = "La contraseña y su confirmación deben coincidir.";

    }

    return $errores;
}


// Verifica si el correo ya está registrado
function correoExiste($conn, $correo)
{
    $sql = "
        SELECT
            id_usuario
        FROM usuarios
        WHERE correo = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $correo);

    $stmt->execute();

    $resultado = $stmt->get_result();

    return $resultado->num_rows > 0;
}


// Registra un nuevo usuario
function registrarUsuario($conn, $nombre, $correo, $password)
{
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "
        INSERT INTO usuarios
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
        )
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sss",
        $nombre,
        $correo,
        $passwordHash
    );

    return $stmt->execute();
}

?>