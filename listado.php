<?php
session_start();
require 'include/conexion.php';
require 'include/autenticacion.php';
require 'include/producto.php';

if (!usuarioAutenticado()) {
    header("Location: index.php");
    exit();
}

$productos = Producto::obtenerProductos($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos</title>
    <link rel="stylesheet" href="css/listado.css">
    <script src="js/votar.js" defer></script>
    <!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

</head>
<body>
    <h2>Listado de Productos</h2>
    <a href="logout.php">Cerrar Sesión</a>
    <table>
    <tr>
        <th>Producto</th>
        <th>Descripción</th>
        <th>Precio</th>
        <th>Valoración</th>
    </tr>
    <?php while ($row = $productos->fetch_assoc()): ?>
    <tr class="clickable-row" data-href="detalle.php?id=<?= $row['id'] ?>" data-average="<?= round($row['media'], 1) ?>">
        <td><?= htmlspecialchars($row['nombre']) ?></td>
        <td><?= htmlspecialchars($row['descripcion']) ?></td>
        <td><?= number_format($row['precio'], 2) ?>€</td>
        <td>
            <div class="rating" data-product-id="<?= $row['id'] ?>">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="bi bi-star-fill star" data-value="<?= $i ?>"></i>
                <?php endfor; ?>
            </div>
            <p class="rating-info">
                Valoración: <span class="rating-average"><?= round($row['media'], 1) ?></span> ★ 
                (<span class="rating-count"><?= $row['total_votos'] ?></span> valoraciones)
            </p>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>