<?php
session_start();
require 'include/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    $role = (isset($_POST["is_admin"]) && $_POST["is_admin"] == "1") ? "admin" : "user";

    // Validar datos
    if (empty($nombre) || empty($email) || empty($password)) {
        echo json_encode(["error" => "Todos los campos son obligatorios"]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Correo inválido"]);
        exit();
    }

    // Verificar si el correo ya está registrado
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo json_encode(["error" => "El email ya está registrado"]);
        exit();
    }
    $stmt->close();

    // Encriptar la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $hashed_password, $role);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => "Usuario registrado correctamente", "redirect" => "index.php"]);
    } else {
        echo json_encode(["error" => "Error al registrar el usuario"]);
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="css/registro.css">
    <script src="js/registro.js" defer></script>
</head>
<body>
    <!-- Video de fondo -->
    <video autoplay muted loop class="video-background">
        <source src="assets/fondo_formulario.mp4" type="video/mp4">
        Tu navegador no soporta videos en HTML5.
    </video>

    <div class="register-wrapper">
        <div class="register-container">
            <h2><?= isset($_GET["admin"]) ? "Registrar Administrador" : "Registro" ?></h2>
            <form id="registerForm" class="register-form">
                <div class="input-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ingrese su nombre" required>
                </div>
                
                <div class="input-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
                </div>
                
                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                </div>
                
                <!-- Enviar el valor is_admin solo si estamos en modo admin -->
                <?php if (isset($_GET["admin"])): ?>
                    <input type="hidden" name="is_admin" value="1">
                <?php endif; ?>
                
                <button type="submit" class="register-btn">Registrarse</button>
            </form>
            
            <p id="registerMessage"></p>
        </div>
    </div>
</body>