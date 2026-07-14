<?php


// Este archivo crea la variable $conn, que representa
// la conexión con MySQL.
require "../config/conexion.php";

// Si alguien entra directamente a registrar.php desde
// el navegador, el código no continuará.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // trim() elimina espacios al inicio y al final.
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["email"]);

    // Las contraseñas no necesitan trim().
    $password = $_POST["password"];
    $password2 = $_POST["password2"];


    if (
        empty($nombre) ||
        empty($correo) ||
        empty($password) ||
        empty($password2)
    ) {

        die("Todos los campos son obligatorios.");

    }


   
    // filter_var() comprueba que el correo tenga un
    // formato correcto.
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        die("Correo electrónico inválido.");

    }




    if ($password != $password2) {

        die("Las contraseñas no coinciden.");

    }


  
    if (strlen($password) < 8) {

        die("La contraseña debe tener mínimo 8 caracteres.");

    }


    // Usamos consultas preparadas para evitar SQL Injection.

    $sql = "SELECT id_usuario
            FROM usuarios
            WHERE correo = ?";

    // Preparamos la consulta.
    $stmt = $conn->prepare($sql);

    // Reemplazamos el ? por el correo.
    // La letra "s" significa STRING.
    $stmt->bind_param("s", $correo);

    // Ejecutamos la consulta.
    $stmt->execute();

    // Guardamos el resultado.
    $resultado = $stmt->get_result();



    if ($resultado->num_rows > 0) {

        die("Este correo ya está registrado.");

    }


    
    // ENCRIPTAR LA CONTRASEÑA
    
    // Nunca debemos guardar la contraseña original.
    // password_hash() genera una versión segura.
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);


    
    // INSERTAR EL NUEVO USUARIO
    
    // No insertamos:
    // id_usuario -> AUTO_INCREMENT
    // fecha_creacion -> CURRENT_TIMESTAMP
    // rol -> usará el valor por defecto (Operador)
    // activo -> lo colocamos en 1
    // ultimo_acceso -> NULL

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

    // Preparamos nuevamente la consulta.
    $stmt = $conn->prepare($sql);

    // Reemplazamos los tres signos ?
    $stmt->bind_param(
        "sss",
        $nombre,
        $correo,
        $passwordHash
    );


    // EJECUTAR EL INSERT
    

    if ($stmt->execute()) {

        // Si todo salió bien...
        // Redirigimos al login.

        header("Location: ../views/login.php");
        exit();

    } else {

        // Si ocurrió un error,
        // mostramos el mensaje de MySQL.

        echo "Error al registrar: " . $conn->error;

    }

}