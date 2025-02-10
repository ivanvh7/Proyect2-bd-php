<?php
session_start();
require '../include/conexion.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Método no permitido."]);
    exit();
}

// Recoger los datos del formulario
$producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : null;
$nombre_usuario = isset($_POST['nombre_usuario']) ? trim($_POST['nombre_usuario']) : "Anónimo";
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : "";

// Validar que los campos no estén vacíos
if (empty($producto_id) || empty($comentario)) {
    echo json_encode(["success" => false, "error" => "El comentario y el producto son obligatorios."]);
    exit();
}

// Verificar conexión a la base de datos
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Error de conexión a la base de datos."]);
    exit();
}

// Insertar el comentario en la base de datos
$stmt = $conn->prepare("INSERT INTO comentarios (producto_id, usuario_id, comentario, fecha) VALUES (?, NULL, ?, NOW())");
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Error al preparar la consulta: " . $conn->error]);
    exit();
}

$stmt->bind_param("is", $producto_id, $comentario);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "nombre" => htmlspecialchars($nombre_usuario), "comentario" => htmlspecialchars($comentario)]);
} else {
    echo json_encode(["success" => false, "error" => "Error al ejecutar la consulta: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
