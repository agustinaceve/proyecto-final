<?php
// Habilitar cabeceras para CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Datos de conexión a la base de datos
$host = "localhost"; // Servidor de base de datos
$dbname = "vidrios"; // Nombre de la base de datos
$username = "root"; // Usuario de XAMPP
$password = ""; // Contraseña (vacía en XAMPP por defecto)

// Crear conexión a la base de datos
$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Obtener acción de la solicitud
$action = $_GET['action'] ?? '';

// Acciones: listar, guardar, eliminar, buscar
switch ($action) {
    case 'list':
        // Obtener todos los clientes
        $result = $mysqli->query("SELECT * FROM clientes");
        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        echo json_encode($clientes);
        break;

    case 'save':
        // Guardar o actualizar cliente
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['correo'];
        $id = $_POST['id'] ?? null;

        if ($id) {
            // Actualizar cliente
            $stmt = $mysqli->prepare("UPDATE clientes SET nombre=?, apellido=?, direccion=?, telefono=?, correo=? WHERE id=?");
            $stmt->bind_param('sssssi', $nombre, $apellido, $direccion, $telefono, $correo, $id);
        } else {
            // Insertar nuevo cliente
            $stmt = $mysqli->prepare("INSERT INTO clientes (nombre, apellido, direccion, telefono, correo) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $nombre, $apellido, $direccion, $telefono, $correo);
        }

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Cliente guardado con éxito']);
        } else {
            echo json_encode(['error' => 'Error al guardar el cliente']);
        }
        break;

    case 'delete':
        // Eliminar cliente
        $id = $_GET['id'];
        if ($mysqli->query("DELETE FROM clientes WHERE id = $id")) {
            echo json_encode(['message' => 'Cliente eliminado con éxito']);
        } else {
            echo json_encode(['error' => 'Error al eliminar el cliente']);
        }
        break;

    case 'search':
        // Buscar clientes
        $query = $_GET['query'] ?? '';
        $result = $mysqli->query("SELECT * FROM clientes WHERE nombre LIKE '%$query%' OR apellido LIKE '%$query%'");
        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        echo json_encode($clientes);
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}

$mysqli->close();
?>
