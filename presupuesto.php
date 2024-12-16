<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "vidrios");

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los productos desde la base de datos
$sql = "SELECT id, nombre, precio FROM productoss";
$result = $conn->query($sql);

// Verificar si hay productos
$productos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}

$conn->close();

// Retornar los productos en formato JSON
echo json_encode($productos);
?>
