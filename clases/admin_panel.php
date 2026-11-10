<?php
session_start();

// Si no está logueado o es un Cliente (idCargo == 2), lo expulsa
if (!isset($_SESSION['usuario_idCargo']) || $_SESSION['usuario_idCargo'] == 2) {
    header("Location: ../index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Twilight — Panel Interno</title>
    <link rel="stylesheet" href="../Css/index.css">
</head>
<body>

    <header class="navbar" style="background-color: #2b2d42; color: white;">
        <div class="logo">Twilight [Panel Interno]</div>
        <div class="nav-actions">
            <span style="margin-right: 15px;">Usuario: <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong></span>
            <a href="../clases/logout.php" class="btn-login" style="background-color: #e63946; color: white;">Cerrar Sesión</a>
        </div>
    </header>

    <main style="padding: 40px;">
        <h1>Gestión de Productos y Ventas</h1>
        <p>Vista exclusiva para Empleados y Administradores.</p>
    </main>

</body>
</html>