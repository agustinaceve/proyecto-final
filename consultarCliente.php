<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
// Configurar la conexión a la base de datos
$host = "localhost"; // Cambia esto si tu base de datos está en otro servidor
$dbname = "vidrios"; // Nombre de la base de datos
$username = "root"; // Usuario de la base de datos
$password = ""; // Contraseña de la base de datos

// Crear la conexión
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

// Comprobar si se ha recibido el código del cliente
if (isset($_GET['codigoCliente'])) {
    $codigoCliente = $_GET['codigoCliente'];

    // Buscar el cliente en la base de datos
    $sql = "SELECT * FROM clientes WHERE codigo_cliente = :codigoCliente";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['codigoCliente' => $codigoCliente]);

    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        // Devolver los datos del cliente como un JSON
        echo json_encode([
            'codigoCliente' => $cliente['codigo_cliente'],
            'nombre' => $cliente['nombre'],
            'direccion' => $cliente['direccion'],
            'telefono' => $cliente['telefono'],
            'correo' => $cliente['correo']
        ]);
    } else {
        // Si no se encuentra al cliente
        echo json_encode(['error' => 'Cliente no encontrado']);
    }
} else {
    echo json_encode(['error' => 'No se recibió código de cliente']);
}
?>
