<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Capturar los datos enviados desde el formulario
$nombre = $_POST['usuario'] ?? '';
$contraseña = $_POST['password'] ?? '';

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "vidrios");

// Verificar si hay error en la conexión
if ($conexion->connect_error) {
    die(json_encode(["status" => "error", "message" => "Error en la conexión a la base de datos"]));
}

// Preparar la consulta para buscar el usuario
$query = $conexion->prepare("SELECT * FROM usuarios WHERE nombre = ? AND contraseña = ?");
$query->bind_param("ss", $nombre, $contraseña);
$query->execute();
$resultado = $query->get_result();

// Verificar si existe un usuario que coincida
if ($resultado->num_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Inicio de sesión exitoso"]);
} else {
    echo json_encode(["status" => "error", "message" => "Usuario o contraseña incorrectos"]);
}

// Cerrar la conexión
$query->close();
$conexion->close();
?>
