<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vidrios";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Error al conectar con la base de datos."]));
}

// Si el formulario se envía por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar entrada
    $user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $pass = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($user) || empty($pass)) {
        echo json_encode(["success" => false, "message" => "El usuario y la contraseña son obligatorios."]);
        $conn->close();
        exit;
    }

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verificar la contraseña
        if ($stmt->num_rows > 0 && password_verify($pass, $hashed_password)) {
            // Inicio de sesión exitoso
            echo json_encode(["success" => true, "message" => "Inicio de sesión exitoso!"]);
        } else {
            // Usuario o contraseña incorrectos
            echo json_encode(["success" => false, "message" => "Usuario o contraseña incorrectos."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Error al preparar la consulta."]);
    }
}

$conn->close();
?>
