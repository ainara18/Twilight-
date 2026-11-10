<?php
session_start();

if (!isset($_SESSION['idusuario']) || $_SESSION['idcargo'] != 1) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>
    <link rel="stylesheet" href="Css/Administrador.css">
</head>
<body>
    <div>
            <div class="nombre-usuario">
                <span>Bienvenido:</span>
                <strong><?= htmlspecialchars($_SESSION['nombre']); ?></strong>
            </div>
        </div>
        <nav>
            <h1>Panel de Administración</h1>
            <ul>
                <li><a href="../vistas/usuario.GUI.php" target="contenido">Administración de Usuarios</a></li>
                <li><a href="../vistas/cargo.GUI.php" target="contenido">Administración de Cargos</a></li>
                <li><a href="../vistas/twilight.GUI.php" target="contenido">Administración de Twilight</a></li>
               
                <li><a href="cerrar_sesion.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </aside>

    <iframe name="contenido" src="bienvenida_admin.php"></iframe>

        <main>
            <h1>Panel de Administración</h1>
            <p>Este es el panel de control para administradores.</p>
        </main>
    </div>
</body>
</html>

