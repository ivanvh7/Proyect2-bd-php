<?php
session_start();
require 'include/conexion.php';
require 'include/autenticacion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $resultado = autenticarUsuario($conn, $email, $password);

    echo json_encode($resultado); // Devuelve JSON solo para JavaScript
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
     <!-- Video de fondo -->
     <video autoplay muted loop class="video-background">
        <source src="assets/fondo_formulario.mp4" type="video/mp4">
        Tu navegador no soporta videos en HTML5.
    </video>

    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form id="loginForm" method="POST">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>

        <p id="loginMessage"></p>

        <!-- Enlace para registrarse -->
        <p>¿No tienes cuenta? <a href="crear.php">Regístrate aquí</a></p>

        <!-- Mostrar opción de registrar un admin si no hay admins -->
        <?php
        $resultado = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE role = 'admin'");
        $row = $resultado->fetch_assoc();
        if ($row['total'] == 0) {
            echo '<p style="margin-top: 10px;"><a href="crear.php?admin=1">Registrar como Administrador</a></p>';
        }
        ?>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita recargar la página

    let formData = new FormData(this);

    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Convertir la respuesta en JSON
    .then(data => {
        if (data.success) {
            // Redirigir al usuario según su rol
            window.location.href = data.role === 'admin' ? 'listado_admin.php' : 'listado.php';
        } else {
            // Mostrar el mensaje de error en la interfaz
            document.getElementById('loginMessage').innerText = data.error;
        }
    })
    .catch(error => console.error('Error:', error));
});

    </script>
</body>
</html>
