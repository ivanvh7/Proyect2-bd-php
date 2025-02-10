<?php
session_start();
require 'include/conexion.php';
require 'include/autenticacion.php';
require 'include/producto.php';

// Verificar si es administrador
if (!usuarioAutenticado() || !esAdmin()) {
    header("Location: index.php");
    exit();
}

// Manejar eliminación de productos, usuarios y comentarios en el mismo archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['producto_id'])) {
        $producto_id = intval($_POST['producto_id']);
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $producto_id);
        echo json_encode(["success" => $stmt->execute()]);
        exit();
    }
    if (isset($_POST['usuario_id'])) {
        $usuario_id = intval($_POST['usuario_id']);
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        echo json_encode(["success" => $stmt->execute()]);
        exit();
    }
    if (isset($_POST['comentario_id'])) {
        $comentario_id = intval($_POST['comentario_id']);
        $stmt = $conn->prepare("DELETE FROM comentarios WHERE id = ?");
        $stmt->bind_param("i", $comentario_id);
        echo json_encode(["success" => $stmt->execute()]);
        exit();
    }

    // Manejar creación de un nuevo producto
    if (isset($_POST['nombre'], $_POST['descripcion'], $_POST['precio']) && isset($_FILES['imagen'])) {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $precio = floatval($_POST['precio']);

        if (empty($nombre) || empty($descripcion) || $precio <= 0) {
            echo json_encode(["success" => false, "error" => "Todos los campos son obligatorios y el precio debe ser mayor a 0."]);
            exit();
        }

        // Validar imagen
        $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['imagen']['type'], $permitidos)) {
            echo json_encode(["success" => false, "error" => "Formato de imagen no válido. Solo JPG, PNG o GIF."]);
            exit();
        }

        // Guardar la imagen
        $imagen_nombre = time() . '_' . basename($_FILES['imagen']['name']);
        $imagen_ruta = 'images/' . $imagen_nombre;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_ruta)) {
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $imagen_ruta);
            
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Producto agregado correctamente."]);
            } else {
                echo json_encode(["success" => false, "error" => "Error al agregar el producto: " . $stmt->error]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Error al subir la imagen."]);
        }
        exit();
    }
} 

// Obtener todos los productos
$productos = Producto::obtenerProductos($conn);

// Obtener todos los usuarios
$usuarios = $conn->query("SELECT id, nombre, email, role FROM usuarios");

// Obtener todos los comentarios
$comentarios = $conn->query("
    SELECT comentarios.id, comentarios.comentario, comentarios.fecha, 
           COALESCE(usuarios.nombre, 'Anónimo') AS usuario, 
           productos.nombre AS producto 
    FROM comentarios 
    LEFT JOIN usuarios ON comentarios.usuario_id = usuarios.id 
    JOIN productos ON comentarios.producto_id = productos.id 
    ORDER BY comentarios.fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/admin.js" defer></script>
</head>
<body>
    <div class="admin-container">
        <h2>Panel de Administración</h2>
        <a href="logout.php" class="logout-btn">Cerrar Sesión</a>

        <h3>Añadir Nuevo Producto</h3>
<div class="form-container">
    <form id="formProducto" method="POST" enctype="multipart/form-data">
        <label>Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>
        <label>Descripción:</label>
        <textarea name="descripcion" id="descripcion" required></textarea>
        <label>Precio (€):</label>
        <input type="number" name="precio" id="precio" step="0.01" required>
        <label>Imagen:</label>
        <input type="file" name="imagen" id="imagen" accept="image/*" required>
        <button type="submit">Añadir Producto</button>
    </form>
    <p id="mensaje" style="color: green; display: none;"></p>
</div>

<h3>Gestión de Productos</h3>
<table id="productosTable">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Precio</th>
        <th>Acción</th>
    </tr>
    <?php while ($row = $productos->fetch_assoc()): ?>
    <tr id="producto_<?= $row['id'] ?>">
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['nombre']) ?></td>
        <td><?= htmlspecialchars($row['descripcion']) ?></td>
        <td><?= number_format($row['precio'], 2) ?> €</td>
        <td><button class="delete-btn" onclick="eliminarElemento('producto_id', <?= $row['id'] ?>)">Eliminar</button></td>
    </tr>
    <?php endwhile; ?>
</table>


        <h3>Gestión de Usuarios</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acción</th>
            </tr>
            <?php while ($user = $usuarios->fetch_assoc()): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['nombre']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['role'] ?></td>
                <td><button class="delete-btn" onclick="eliminarElemento('usuario_id', <?= $user['id'] ?>)">Eliminar</button></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <h3>Gestión de Comentarios</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Producto</th>
                <th>Comentario</th>
                <th>Fecha</th>
                <th>Acción</th>
            </tr>
            <?php while ($comentario = $comentarios->fetch_assoc()): ?>
            <tr>
                <td><?= $comentario['id'] ?></td>
                <td><?= htmlspecialchars($comentario['usuario']) ?></td>
                <td><?= htmlspecialchars($comentario['producto']) ?></td>
                <td><?= htmlspecialchars($comentario['comentario']) ?></td>
                <td><?= date("d/m/Y H:i", strtotime($comentario['fecha'])) ?></td>
                <td><button class="delete-btn" onclick="eliminarElemento('comentario_id', <?= $comentario['id'] ?>)">Eliminar</button></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
