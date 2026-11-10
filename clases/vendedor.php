<?php
session_start();

if (!isset($_SESSION['idusuario']) || $_SESSION['idcargo'] != 4) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Vendedor</title>
    <link rel="stylesheet" href="Css/cliente.css">
</head>
<body>
    <h1>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']); ?> (Vendedor)</h1>
    

    <ul>
        <li><a href="perfil.php">Ver perfil</a></li>
        <li><a href="cerrar_sesion.php">Cerrar sesión</a></li>
    </ul>
</body>
</html>
