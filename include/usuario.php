<?php
require 'conexion.php';

class Usuario {
    public static function registrar($conn, $nombre, $email, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $email, $hashed_password);
        return $stmt->execute();
    }

    public static function obtenerUsuarioPorEmail($conn, $email) {
        $stmt = $conn->prepare("SELECT id, password FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
