<?php
    $server = "localhost";
    $user = "root";
    $pass = "";
    $bd = "simar";

    $conn= new mysqli($server, $user, $pass, $bd);

    if($conn->connect_error){
    die("Error de conexión");
}
?>