<?php

function obtenerUsuarioPorCorreo($conn, $correo)
{
    $sql = "
        SELECT *
        FROM usuarios
        WHERE correo = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $correo);

    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}


function actualizarUltimoAcceso($conn, $idUsuario)
{
    $sql = "
        UPDATE usuarios
        SET ultimo_acceso = NOW()
        WHERE id_usuario = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $idUsuario);

    $stmt->execute();
}


function iniciarSesion($conn, $correo, $password)
{
    if (empty($correo) || empty($password)) {

        return [
            "success" => false,
            "mensaje" => "Todos los campos son obligatorios."
        ];

    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        return [
            "success" => false,
            "mensaje" => "El correo electrónico no tiene un formato válido."
        ];

    }

    $usuario = obtenerUsuarioPorCorreo($conn, $correo);

    if (!$usuario) {

        return [
            "success" => false,
            "mensaje" => "El correo o la contraseña son incorrectos."
        ];

    }

    if ($usuario["activo"] == 0) {

        return [
            "success" => false,
            "mensaje" => "Esta cuenta está deshabilitada."
        ];

    }

    if (!password_verify($password, $usuario["password_hash"])) {

        return [
            "success" => false,
            "mensaje" => "El correo o la contraseña son incorrectos."
        ];

    }

    session_regenerate_id(true);

    $_SESSION["id_usuario"] = $usuario["id_usuario"];

    $_SESSION["nombre"] = $usuario["nombre"];

    $_SESSION["correo"] = $usuario["correo"];

    $_SESSION["rol"] = $usuario["rol"];

    actualizarUltimoAcceso(
        $conn,
        $usuario["id_usuario"]
    );

    return [
        "success" => true
    ];
}

?>  