<?php
session_start();
require 'include/conexion.php';
require 'include/autenticacion.php';
require 'include/producto.php';

if (!usuarioAutenticado()) {
    echo json_encode(["error" => "No autorizado"]);
    exit();
}

$usuario_id = $_SESSION["user_id"];
$producto_id = isset($_POST["producto_id"]) ? (int) $_POST["producto_id"] : 0;
$valor = isset($_POST["valor"]) ? (int) $_POST["valor"] : 0;

if ($valor < 1 || $valor > 5) {
    echo json_encode(["error" => "Valoración inválida"]);
    exit();
}

$resultado = Producto::registrarVoto($conn, $usuario_id, $producto_id, $valor);

if (isset($resultado["success"])) {
    echo json_encode(["success" => true, "media" => $resultado["media"], "total" => $resultado["total"]]);
} else {
    echo json_encode(["error" => $resultado["error"]]);
}
?>

