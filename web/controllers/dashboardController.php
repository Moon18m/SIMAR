<?php

require "../config/conexion.php";
require "dashboardService.php";

header("Content-Type: application/json");

try{

    $datos = obtenerDashboard($conn);

    echo json_encode([
        "success"=>true,
        "data"=>$datos
    ]); 

}catch(Exception $e){

    echo json_encode([
        "success"=>false,
        "mensaje"=>$e->getMessage()
    ]);

}

$conn->close();