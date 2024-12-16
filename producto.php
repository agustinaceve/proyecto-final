<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vidrios";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

// Crear un producto
if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("INSERT INTO productoss (nombre, categoria, subcategoria, precio, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $data["nombre"], $data["categoria"], $data["subcategoria"], $data["precio"], $data["stock"]);
    $stmt->execute();
    echo json_encode(["message" => "Producto agregado correctamente"]);

// Obtener todos los productos
} elseif ($method === "GET") {
    $result = $conn->query("SELECT * FROM productoss");
    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
    echo json_encode($productos);

// Modificar un producto
} elseif ($method === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("UPDATE productoss SET nombre = ?, categoria = ?, subcategoria = ?, precio = ?, stock = ? WHERE id = ?");
    $stmt->bind_param("sssiii", $data["nombre"], $data["categoria"], $data["subcategoria"], $data["precio"], $data["stock"], $data["id"]);
    $stmt->execute();
    echo json_encode(["message" => "Producto modificado correctamente"]);

// Eliminar un producto
} elseif ($method === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("DELETE FROM productoss WHERE id = ?");
    $stmt->bind_param("i", $data["id"]);
    $stmt->execute();
    echo json_encode(["message" => "Producto eliminado correctamente"]);
}

$conn->close();
?>
