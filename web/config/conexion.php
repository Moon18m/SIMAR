<?php
    $server = "localhost";
    $user = "root";
    $pass = "";
    $bd = "simar";

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $conn = new mysqli($server, $user, $pass, $bd);

    if($conn->connect_error){
    die("Error de conexión");
}
?>