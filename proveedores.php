<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";  // Usuario por defecto en XAMPP
$password = "";      // Contraseña por defecto en XAMPP
$dbname = "vidrios"; // Nombre de la base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

// Configurar cabeceras para CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Obtener la acción solicitada
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $result = $conn->query("SELECT * FROM proveedores");
        if ($result) {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        } else {
            echo json_encode(["error" => "Error al obtener la lista de proveedores"]);
        }
        break;

    case 'save':
        $id = $_POST['id'] ?? null;
        $nombre = $_POST['nombre'] ?? '';
        $contacto = $_POST['contacto'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $productos = $_POST['productos'] ?? '';
        $cuit = $_POST['cuit'] ?? '';

        if ($nombre && $contacto && $telefono && $correo && $direccion && $productos && $cuit) {
            if ($id) {
                // Actualizar proveedor existente
                $stmt = $conn->prepare("UPDATE proveedores SET 
                    nombre=?, contacto=?, telefono=?, correo=?, direccion=?, productos=?, cuit=? 
                    WHERE id=?");
                $stmt->bind_param("sssssssi", $nombre, $contacto, $telefono, $correo, $direccion, $productos, $cuit, $id);
                $stmt->execute();
                echo json_encode(["success" => "Proveedor actualizado correctamente"]);
            } else {
                // Insertar nuevo proveedor
                $stmt = $conn->prepare("INSERT INTO proveedores (nombre, contacto, telefono, correo, direccion, productos, cuit) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $nombre, $contacto, $telefono, $correo, $direccion, $productos, $cuit);
                $stmt->execute();
                echo json_encode(["success" => "Proveedor agregado correctamente"]);
            }
        } else {
            echo json_encode(["error" => "Todos los campos son obligatorios"]);
        }
        break;

    case 'delete':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM proveedores WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            echo json_encode(["success" => "Proveedor eliminado correctamente"]);
        } else {
            echo json_encode(["error" => "ID de proveedor no proporcionado"]);
        }
        break;

    case 'get':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM proveedores WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode(["error" => "ID de proveedor no proporcionado"]);
        }
        break;

    case 'search':
        $query = $_GET['query'] ?? '';
        if ($query) {
            $stmt = $conn->prepare("SELECT * FROM proveedores WHERE 
                nombre LIKE ? OR productos LIKE ? OR direccion LIKE ?");
            $searchTerm = "%$query%";
            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        } else {
            echo json_encode(["error" => "Búsqueda no proporcionada"]);
        }
        break;

    case 'historial_compras':
        $cuit = $_GET['cuit'] ?? '';
        if ($cuit) {
            $stmt = $conn->prepare("SELECT * FROM compras WHERE cuit_proveedor=?");
            $stmt->bind_param("s", $cuit);
            $stmt->execute();
            $result = $stmt->get_result();
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        } else {
            echo json_encode(["error" => "CUIT no proporcionado"]);
        }
        break;

    case 'alertas':
        $result = $conn->query("SELECT * FROM compras WHERE fecha_entrega < CURDATE()");
        if ($result) {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        } else {
            echo json_encode(["error" => "Error al obtener alertas"]);
        }
        break;

    default:
        echo json_encode(["error" => "Acción no válida"]);
        break;
}

$conn->close();
?>
