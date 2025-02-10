<?php
require 'conexion.php';

class Producto {
    public static function obtenerProductos($conn) {
        $query = "SELECT p.*, 
                         COALESCE((SELECT AVG(valor) FROM votos WHERE producto_id = p.id), 0) AS media,
                         COALESCE((SELECT COUNT(*) FROM votos WHERE producto_id = p.id), 0) AS total_votos
                  FROM productos p";
        return $conn->query($query);
    }

    public static function obtenerProductoPorId($conn, $id) {
        $stmt = $conn->prepare("SELECT p.*, 
                                        COALESCE((SELECT AVG(valor) FROM votos WHERE producto_id = p.id), 0) AS media,
                                        COALESCE((SELECT COUNT(*) FROM votos WHERE producto_id = p.id), 0) AS total_votos
                                 FROM productos p WHERE p.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function crearProducto($conn, $nombre, $descripcion, $precio, $imagen) {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $imagen);
        return $stmt->execute();
    }

    public static function registrarVoto($conn, $usuario_id, $producto_id, $valor) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM votos WHERE usuario_id = ? AND producto_id = ?");
        $stmt->bind_param("ii", $usuario_id, $producto_id);
        $stmt->execute();
        $stmt->bind_result($existe);
        $stmt->fetch();
        $stmt->close();

        if ($existe > 0) {
            return ["error" => "Ya has votado este producto"];
        }

        // Insertar el voto con validación de error
        $stmt = $conn->prepare("INSERT INTO votos (usuario_id, producto_id, valor) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $usuario_id, $producto_id, $valor);
        if (!$stmt->execute()) {
            return ["error" => "Error al registrar el voto"];
        }
        $stmt->close();

        // Obtener la media actualizada y total de votos
        $stmt_avg = $conn->prepare("SELECT AVG(valor), COUNT(*) FROM votos WHERE producto_id = ?");
        $stmt_avg->bind_param("i", $producto_id);
        $stmt_avg->execute();
        $stmt_avg->bind_result($media, $total);
        $stmt_avg->fetch();
        $stmt_avg->close();

        return ["success" => true, "media" => round($media, 1), "total" => $total];
    }

    public static function eliminarProducto($conn, $producto_id) {
        // Primero, eliminar los votos asociados al producto
        $stmt_votos = $conn->prepare("DELETE FROM votos WHERE producto_id = ?");
        $stmt_votos->bind_param("i", $producto_id);
        $stmt_votos->execute();
        $stmt_votos->close();

        // Luego, eliminar el producto
        $stmt_producto = $conn->prepare("DELETE FROM productos WHERE id = ?");
        $stmt_producto->bind_param("i", $producto_id);
        if ($stmt_producto->execute()) {
            return ["success" => "Producto eliminado correctamente"];
        } else {
            return ["error" => "Error al eliminar el producto"];
        }
    }
}
?>
