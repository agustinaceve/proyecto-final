<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vidrios";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $sql = "SELECT * FROM ventas";
        $result = $conn->query($sql);
        $ventas = [];
        while ($row = $result->fetch_assoc()) {
            $ventas[] = $row;
        }
        echo json_encode($ventas);
        break;

    case 'get':
        $id = $_GET['id'];
        $sql = "SELECT * FROM ventas WHERE id = $id";
        $result = $conn->query($sql);
        $venta = $result->fetch_assoc();
        echo json_encode($venta);
        break;

    case 'register':
        $cliente = $_POST['cliente'];
        $producto = $_POST['producto'];
        $cantidad = $_POST['cantidad'];
        $precio = $_POST['precio'];
        $fecha = $_POST['fecha'];

        $sql = "INSERT INTO ventas (cliente, producto, cantidad, precio_unitario, fecha) VALUES ('$cliente', '$producto', '$cantidad', '$precio', '$fecha')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
        }
        break;

    case 'update':
        $id = $_POST['id'];
        $cliente = $_POST['cliente'];
        $producto = $_POST['producto'];
        $cantidad = $_POST['cantidad'];
        $precio = $_POST['precio'];
        $fecha = $_POST['fecha'];

        $sql = "UPDATE ventas SET cliente='$cliente', producto='$producto', cantidad='$cantidad', precio_unitario='$precio', fecha='$fecha' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
        }
        break;

    case 'delete':
        $id = $_GET['id'];
        $sql = "DELETE FROM ventas WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Acción no válida."]);
        
}
// Acción para obtener el producto más vendido
if ($_GET['action'] == 'getMostSoldProduct') {
    $query = "
        SELECT producto, SUM(cantidad) AS total_vendido
        FROM detalle_ventas
        GROUP BY producto
        ORDER BY total_vendido DESC
        LIMIT 1
    ";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);  // Devolver el producto más vendido
    } else {
        echo json_encode(["message" => "No se encontraron ventas"]);
    }
}


$conn->close();
