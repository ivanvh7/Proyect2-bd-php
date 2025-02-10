<?php
session_start();
require 'include/conexion.php';
require 'include/producto.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$producto_id = $_GET['id'];
$producto = Producto::obtenerProductoPorId($conn, $producto_id);

if (!$producto) {
    echo "<h2>Producto no encontrado</h2>";
    exit();
}

// Obtener comentarios del producto
$query = $conn->prepare("SELECT comentarios.comentario, comentarios.fecha, usuarios.nombre 
                         FROM comentarios 
                         LEFT JOIN usuarios ON comentarios.usuario_id = usuarios.id 
                         WHERE comentarios.producto_id = ? 
                         ORDER BY comentarios.fecha DESC");
$query->bind_param("i", $producto_id);
$query->execute();
$result = $query->get_result();
$comentarios_existentes = $result->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($producto['nombre']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/detalle.css">
</head>
<body>
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-6 col-lg-5 text-center">
            <div class="imagen-container">
                <?php if (!empty($producto['imagen'])): ?>
                    <img src="<?= htmlspecialchars($producto['imagen']) ?>" class="img-fluid rounded shadow detalle-imagen" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                <?php else: ?>
                    <img src="images/no-image.png" class="img-fluid rounded shadow detalle-imagen" alt="Imagen no disponible">
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6 col-lg-7">
    <h2><?= htmlspecialchars($producto['nombre']) ?></h2>
    <h4 class="text-success">Precio: <?= number_format($producto['precio'], 2) ?> €</h4>
    <button class="btn btn-primary mt-2" onclick="añadirAlCarrito(<?= $producto_id ?>)">Añadir al carrito</button>
</div>

    </div>
    <h4>Comentarios y Valoraciones</h4>
    <div id="comentarios">
        <?php if ($comentarios_existentes):
            while ($comentario = $result->fetch_assoc()): ?>
                <div class="border p-3 mb-2">
                    <strong><?= htmlspecialchars($comentario['nombre'] ?? 'Anónimo') ?></strong> 
                    <span class="text-muted small"> - <?= date("d/m/Y H:i", strtotime($comentario['fecha'])) ?></span>
                    <p><?= htmlspecialchars($comentario['comentario']) ?></p>
                </div>
            <?php endwhile;
        else: ?>
            <p id="sinComentarios">Aún no hay comentarios. ¡Sé el primero en comentar!</p>
        <?php endif; ?>
    </div>

    <form id="formComentario" class="mt-3">
        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" placeholder="Tu nombre" required>
        <textarea class="form-control mt-2" id="comentario" name="comentario" placeholder="Escribe tu comentario aquí..." required></textarea>
        <input type="hidden" name="producto_id" value="<?= $producto_id ?>">
        <button type="submit" class="btn btn-primary mt-2">Enviar</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#formComentario').submit(function(e) {
            e.preventDefault();
            var nombreUsuario = $('#nombre_usuario').val().trim();
            var comentario = $('#comentario').val().trim();
            if (nombreUsuario === '' || comentario === '') {
                alert('Por favor, completa todos los campos.');
                return;
            }
            $.post('include/guardar_comentario.php', $(this).serialize(), function(response) {
                if (response.success) {
                    $('#sinComentarios').remove(); // Eliminar mensaje si hay comentarios
                    $('#comentarios').prepend('<div class="border p-3 mb-2"><strong>' + nombreUsuario + '</strong> <span class="text-muted small"> - Ahora</span><p>' + comentario + '</p></div>');
                    $('#nombre_usuario').val('');
                    $('#comentario').val('');
                } else {
                    alert(response.error);
                }
            }, 'json');
        });
    });
</script>
</body>
</html>