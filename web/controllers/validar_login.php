<?php


// INICIAR LA SESIÓN

// Permite guardar información del usuario mientras navega.
session_start();


// CONECTARSE A LA BASE DE DATOS

require "../config/conexion.php";



// VERIFICAR QUE EL FORMULARIO FUE ENVIADO POR POST

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    // OBTENER LOS DATOS DEL FORMULARIO
    

    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];


    
    // VALIDAR QUE LOS CAMPOS NO ESTÉN VACÍOS
   

    if (empty($correo) || empty($password)) {

        die("Todos los campos son obligatorios.");

    }


    
    // BUSCAR EL USUARIO POR SU CORREO


    $sql = "SELECT *
            FROM usuarios
            WHERE correo = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $correo);

    $stmt->execute();

    $resultado = $stmt->get_result();


    
    // ¿EL USUARIO EXISTE?
    

    if ($resultado->num_rows == 0) {

        die("El correo no está registrado.");

    }


  
    // OBTENER LOS DATOS DEL USUARIO
    

    $usuario = $resultado->fetch_assoc();


    
    // VERIFICAR SI EL USUARIO ESTÁ ACTIVO
    

    if ($usuario["activo"] == 0) {

        die("Esta cuenta está deshabilitada.");

    }


    
    // password_verify() compara la contraseña escrita
    // con el hash almacenado en la base de datos.

    if (password_verify($password, $usuario["password_hash"])) {

        
        // CREAR VARIABLES DE SESIÓN
        

        $_SESSION["id_usuario"] = $usuario["id_usuario"];

        $_SESSION["nombre"] = $usuario["nombre"];

        $_SESSION["correo"] = $usuario["correo"];

        $_SESSION["rol"] = $usuario["rol"];


       
        // ACTUALIZAR EL ÚLTIMO ACCESO
        

        $sql = "UPDATE usuarios
                SET ultimo_acceso = NOW()
                WHERE id_usuario = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("i", $usuario["id_usuario"]);

        $stmt->execute();


       

        header("Location: ../views/dashboard.php");

        exit();

    } else {

        

        die("Contraseña incorrecta.");

    }

}