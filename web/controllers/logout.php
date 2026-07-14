<?php

session_start();

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Regresar al login
header("Location: ../views/login.php");
exit();

?>