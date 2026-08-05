<?php
// Verifica que el usuario haya iniciado sesión y tenga rol de administrador.
// Si no cumple estas condiciones, bloquea el acceso y lo envía al Dashboard.

require "auth.php";

// Permite continuar únicamente a las cuentas administradoras.
if (($_SESSION["rol"] ?? "") !== "Administrador") {
    header("Location: ../views/dashboard.php?acceso=denegado");
    exit;
}
