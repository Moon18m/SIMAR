<?php
function esEjecucionReciente(?array $ejecucion, int $minutos = 5): bool {
    if (!$ejecucion || $ejecucion['estado'] !== 'Exitoso') {
        return false;
    }
    $minutosTranscurridos = (strtotime('now') - strtotime($ejecucion['fecha_hora'])) / 60;
    return $minutosTranscurridos <= $minutos;
}