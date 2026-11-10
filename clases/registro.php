<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if (!empty($nombre) && !empty($email) && !empty($direccion) && !empty($clave)) {
        try {
            $conexionObj = new Conexion();
            $db = $conexionObj->getConexion();

            $stmtCheck = $db->prepare("SELECT id_usuario FROM usuario WHERE email = ? LIMIT 1");
            $stmtCheck->execute([$email]);

            if ($stmtCheck->rowCount() > 0) {
                echo "<script>alert('Ya existe una cuenta registrada con este correo electrónico.'); window.location.href='registro.html';</script>";
                exit();
            } else {
                $claveHash = password_hash($clave, PASSWORD_DEFAULT);

                $stmtInsert = $db->prepare("INSERT INTO usuario (nombre, email, direccion, clave, idCargo) VALUES (?, ?, ?, ?, 2)");
                
                if ($stmtInsert->execute([$nombre, $email, $direccion, $claveHash])) {
                    $_SESSION['usuario_id'] = $db->lastInsertId();
                    $_SESSION['usuario_nombre'] = $nombre;
                    $_SESSION['usuario_email'] = $email;
                    $_SESSION['usuario_idCargo'] = 2;

                    header("Location: ../index.php");
                    exit();
                } else {
                    echo "<script>alert('Hubo un error al registrar el usuario.'); window.location.href='registro.html';</script>";
                    exit();
                }
            }
        } catch (PDOException $e) {
            echo "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('Por favor completa todos los campos.'); window.location.href='registro.html';</script>";
        exit();
    }
}
?>