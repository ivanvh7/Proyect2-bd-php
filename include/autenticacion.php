<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'conexion.php';

function usuarioAutenticado() {
    return isset($_SESSION["user_id"]);
}

function esAdmin() {
    return isset($_SESSION["role"]) && $_SESSION["role"] === "admin";
}

function cerrarSesion() {
    session_destroy();
    header("Location: login.php");
    exit();
}

function autenticarUsuario($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT id, password, role FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["role"] = $role; // Guardar el rol en la sesión

            return ["success" => true, "role" => $role]; // Enviar el rol al frontend
        } else {
            return ["error" => "Contraseña incorrecta"];
        }
    } else {
        return ["error" => "Usuario no encontrado"];
    }
    $stmt->close();
}
?>