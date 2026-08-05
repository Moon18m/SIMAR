<?php

require __DIR__ . "/reportesController.php";

$nombreArchivo = "reporte_simar_" . $fechaInicial . "_" . $fechaFinal . ".csv";
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$nombreArchivo\"");

echo "\xEF\xBB\xBF";
$salida = fopen("php://output", "w");
fputcsv($salida, ["Código", "Sensor", "Tipo", "Valor", "Unidad", "Fecha", "Hora", "Estado"]);

foreach ($lecturas as $lectura) {
    fputcsv($salida, [
        $lectura["codigo"],
        $lectura["sensor"],
        $lectura["tipo"],
        $lectura["valor"],
        $lectura["unidad_medida"],
        date("Y-m-d", strtotime($lectura["fecha_hora"])),
        date("H:i:s", strtotime($lectura["fecha_hora"])),
        $lectura["estado_lectura"]
    ]);
}

fclose($salida);
exit;
