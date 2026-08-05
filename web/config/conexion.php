<?php
$server = "localhost";
$user = "root";
$pass = "";
$bd = "simar";

date_default_timezone_set("America/Bogota");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($server, $user, $pass, $bd);
$conn->set_charset("utf8mb4");
$conn->query("SET time_zone = '-05:00'");

if ($conn->connect_error) {
    die("Error de conexión con la base de datos.");
}
?>