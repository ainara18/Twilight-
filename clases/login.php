<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if (!empty($email) && !empty($clave)) {
        try {
            $conexionObj = new Conexion();
            $db = $conexionObj->getConexion();

            $stmt = $db->prepare("SELECT * FROM usuario WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                $hashPass = $usuario['clave'] ?? '';

                if (password_verify($clave, $hashPass) || $clave === $hashPass) {
                    $_SESSION['usuario_id'] = $usuario['id_usuario'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    $_SESSION['usuario_idCargo'] = $usuario['idCargo'] ?? 2;

                    header("Location: ../index.php");
                    exit();
                } else {
                    echo "<script>alert('Contraseña incorrecta.'); window.location.href='login.html';</script>";
                    exit();
                }
            } else {
                echo "<script>alert('No existe una cuenta registrada con ese correo.'); window.location.href='login.html';</script>";
                exit();
            }
        } catch (PDOException $e) {
            echo "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('Por favor completa todos los campos.'); window.location.href='login.html';</script>";
        exit();
    }
}