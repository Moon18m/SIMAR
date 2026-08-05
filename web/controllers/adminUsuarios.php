<?php
// Este controlador contiene las operaciones del módulo de usuarios.
require_once __DIR__ . "/../config/conexion.php";

// Crea una clave que protege los formularios enviados desde la sesión actual.
if (empty($_SESSION["csrf_admin"])) {
    $_SESSION["csrf_admin"] = bin2hex(random_bytes(32));
}

// Limpia texto antes de mostrarlo dentro del HTML.
function limpiarSalida(?string $texto): string
{
    return htmlspecialchars($texto ?? "", ENT_QUOTES, "UTF-8");
}

// Guarda un mensaje y regresa al listado.
function volverAdministracion(string $tipo, string $mensaje): void
{
    $_SESSION["mensaje_admin"] = ["tipo" => $tipo, "texto" => $mensaje];
    header("Location: ../views/admin.php");
    exit;
}

// Comprueba los campos compartidos por crear y editar.
function validarUsuario(array $datos, bool $esEdicion = false): array
{
    $errores = [];
    $nombre = trim($datos["nombre"] ?? "");
    $correo = trim($datos["correo"] ?? "");
    $password = $datos["password"] ?? "";
    $rol = $datos["rol"] ?? "";
    $activo = $datos["activo"] ?? "";

    if ($nombre === "" || mb_strlen($nombre) > 100) {
        $errores[] = "Escribe un nombre de máximo 100 caracteres.";
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || mb_strlen($correo) > 100) {
        $errores[] = "Escribe un correo válido de máximo 100 caracteres.";
    }

    if ((!$esEdicion || $password !== "") && strlen($password) < 8) {
        $errores[] = "La contraseña debe tener mínimo 8 caracteres.";
    }

    if (!in_array($rol, ["Administrador", "Operador"], true)) {
        $errores[] = "Selecciona un rol válido.";
    }

    if (!in_array((string) $activo, ["0", "1"], true)) {
        $errores[] = "Selecciona un estado válido.";
    }

    return $errores;
}

// Atiende únicamente las acciones que cambian información.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf"] ?? "";

    if (!hash_equals($_SESSION["csrf_admin"], $token)) {
        volverAdministracion("error", "La solicitud venció. Actualiza la página e inténtalo de nuevo.");
    }

    $accion = $_POST["accion"] ?? "";

    try {
        if ($accion === "crear") {
            $errores = validarUsuario($_POST);

            if ($errores) {
                volverAdministracion("error", implode(" ", $errores));
            }

            $nombre = trim($_POST["nombre"]);
            $correo = trim($_POST["correo"]);
            $passwordHash = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $rol = $_POST["rol"];
            $activo = (int) $_POST["activo"];

            $sql = "INSERT INTO usuarios (nombre, correo, password_hash, rol, activo) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $nombre, $correo, $passwordHash, $rol, $activo);
            $stmt->execute();

            volverAdministracion("exito", "El usuario fue creado correctamente.");
        }

        if ($accion === "actualizar") {
            $idUsuario = filter_input(INPUT_POST, "id_usuario", FILTER_VALIDATE_INT);
            $errores = validarUsuario($_POST, true);

            if (!$idUsuario) {
                $errores[] = "El usuario seleccionado no es válido.";
            }

            if ($errores) {
                volverAdministracion("error", implode(" ", $errores));
            }

            $nombre = trim($_POST["nombre"]);
            $correo = trim($_POST["correo"]);
            $rol = $_POST["rol"];
            $activo = (int) $_POST["activo"];
            $password = $_POST["password"] ?? "";

            // Impide que la cuenta actual pierda acceso durante la edición.
            if ($idUsuario === (int) $_SESSION["id_usuario"] && ($rol !== "Administrador" || $activo !== 1)) {
                volverAdministracion("error", "No puedes quitar tu propio acceso de administrador.");
            }

            if ($password !== "") {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nombre = ?, correo = ?, password_hash = ?, rol = ?, activo = ? WHERE id_usuario = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssii", $nombre, $correo, $passwordHash, $rol, $activo, $idUsuario);
            } else {
                $sql = "UPDATE usuarios SET nombre = ?, correo = ?, rol = ?, activo = ? WHERE id_usuario = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssii", $nombre, $correo, $rol, $activo, $idUsuario);
            }

            $stmt->execute();
            volverAdministracion("exito", "El usuario fue actualizado correctamente.");
        }

        if ($accion === "eliminar") {
            $idUsuario = filter_input(INPUT_POST, "id_usuario", FILTER_VALIDATE_INT);

            if (!$idUsuario) {
                volverAdministracion("error", "El usuario seleccionado no es válido.");
            }

            if ($idUsuario === (int) $_SESSION["id_usuario"]) {
                volverAdministracion("error", "No puedes eliminar la cuenta con la que iniciaste sesión.");
            }

            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                volverAdministracion("error", "El usuario ya no existe o no fue eliminado.");
            }

            volverAdministracion("exito", "El usuario fue eliminado correctamente.");
        }

        volverAdministracion("error", "La acción solicitada no es válida.");
    } catch (mysqli_sql_exception $error) {
        if ((int) $error->getCode() === 1062) {
            volverAdministracion("error", "El correo ya está registrado.");
        }

        volverAdministracion("error", "No fue posible completar la operación. Inténtalo de nuevo.");
    }
}

// Recupera el mensaje generado por la operación anterior.
$mensajeAdmin = $_SESSION["mensaje_admin"] ?? null;
unset($_SESSION["mensaje_admin"]);

// Consulta un registro cuando se pulsa Consultar o Editar.
$usuarioSeleccionado = null;
$modo = $_GET["modo"] ?? "";
$idSeleccionado = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($idSeleccionado && in_array($modo, ["ver", "editar"], true)) {
    $stmt = $conn->prepare("SELECT id_usuario, nombre, correo, rol, activo, fecha_creacion, ultimo_acceso FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $idSeleccionado);
    $stmt->execute();
    $usuarioSeleccionado = $stmt->get_result()->fetch_assoc();

    // Regresa al formulario de creación si el registro ya no existe.
    if (!$usuarioSeleccionado) {
        $modo = "";
    }
}

// Filtra la tabla por nombre o correo.
$buscar = trim($_GET["buscar"] ?? "");

if ($buscar !== "") {
    $termino = "%" . $buscar . "%";
    $stmt = $conn->prepare("SELECT id_usuario, nombre, correo, rol, activo, fecha_creacion, ultimo_acceso FROM usuarios WHERE nombre LIKE ? OR correo LIKE ? ORDER BY id_usuario DESC");
    $stmt->bind_param("ss", $termino, $termino);
    $stmt->execute();
    $usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $stmt = $conn->prepare("SELECT id_usuario, nombre, correo, rol, activo, fecha_creacion, ultimo_acceso FROM usuarios ORDER BY id_usuario DESC");
    $stmt->execute();
    $usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Calcula los datos de las tarjetas superiores.
$resumenUsuarios = ["total" => 0, "activos" => 0, "administradores" => 0];
$resultadoResumen = $conn->query(
    "SELECT COUNT(*) AS total, SUM(activo = 1) AS activos, SUM(rol = 'Administrador') AS administradores FROM usuarios"
);
$datosResumen = $resultadoResumen->fetch_assoc();
$resumenUsuarios = array_map("intval", $datosResumen);
