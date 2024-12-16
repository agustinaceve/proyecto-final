<?php
// Configuración de cabeceras
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

// Datos de conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vidrios";

// Crear la conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Conexión fallida: " . $conn->connect_error]));
}

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

// Verificar el método de la solicitud y realizar acciones
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action'])) {
    // Mostrar usuarios
    if ($_GET['action'] == 'read') {
        $sql = "SELECT id, nombre, contraseña FROM usuarios";
        $result = $conn->query($sql);

        $usuarios = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
        }
        echo json_encode($usuarios);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action'])) {
    // Crear usuario
    if ($_GET['action'] == 'create') {
        if (isset($data['nombre']) && isset($data['contraseña'])) {
            $nombre = $conn->real_escape_string($data['nombre']);
            $contraseña = $conn->real_escape_string($data['contraseña']);
            $sql = "INSERT INTO usuarios (nombre, contraseña) VALUES ('$nombre', '$contraseña')";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(["success" => true, "message" => "Usuario creado exitosamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al crear el usuario: " . $conn->error]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Datos incompletos."]);
        }
    }
    // Modificar usuario
    elseif ($_GET['action'] == 'update') {
        if (isset($data['id']) && isset($data['nombre']) && isset($data['contraseña'])) {
            $id = $conn->real_escape_string($data['id']);
            $nombre = $conn->real_escape_string($data['nombre']);
            $contraseña = $conn->real_escape_string($data['contraseña']);
            $sql = "UPDATE usuarios SET nombre = '$nombre', contraseña = '$contraseña' WHERE id = $id";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(["success" => true, "message" => "Usuario actualizado exitosamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar el usuario: " . $conn->error]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Datos incompletos."]);
        }
    }
    // Eliminar usuario
   // Eliminar usuario
elseif ($_GET['action'] == 'delete') {
    if (isset($_GET['id'])) { // Cambia $data['id'] por $_GET['id']
        $id = $conn->real_escape_string($_GET['id']);
        $sql = "DELETE FROM usuarios WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Usuario eliminado exitosamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar el usuario: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID no proporcionado."]);
    }
}

}

// Cerrar la conexión
$conn->close();
?>
