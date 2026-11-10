<?php
// Activar reporte de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../clases/usuario.php';
require_once __DIR__ . '/../modelos/usuario.model.php';

$usuarioModel = new UsuarioModel();

// Si se recibe una petición para eliminar por la URL
if (isset($_GET['eliminar'])) {
    $idEliminar = (int)$_GET['eliminar'];
    $usuarioModel->Eliminar($idEliminar);
    header("Location: usuario.GUI.php");
    exit();
}

$usuarios = $usuarioModel->Listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios Registrados — Twilight</title>
    <link rel="stylesheet" href="../Css/registro.css">
    <style>
        .tabla-usuarios { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top: 20px; }
        .tabla-usuarios th, .tabla-usuarios td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        .tabla-usuarios th { background-color: #faf5f2; color: #5a4a42; }
        .btn-eliminar { background-color: #e63946; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; transition: background 0.2s; display: inline-block; }
        .btn-eliminar:hover { background-color: #d62828; }
        .contenedor-tabla { max-width: 900px; margin: 40px auto; padding: 20px; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="contenedor-tabla">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h1 style="color: #5a4a42; margin: 0;">Twilight</h1>
            <a href="registro.GUI.php" class="btn-submit" style="padding: 8px 15px; text-decoration: none; border-radius: 4px; background: #8c7355; color: white; width: auto;">+ Nuevo Usuario</a>
        </div>

        <h2 style="color: #5a4a42; font-weight: 500;">Usuarios Registrados</h2>

        <table class="tabla-usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u->getidusuario()) ?></td>
                            <td><?= htmlspecialchars($u->getnombre()) ?></td>
                            <td><?= htmlspecialchars($u->getdireccion()) ?></td>
                            <td><?= htmlspecialchars($u->getemail()) ?></td>
                            <td>
                                <a href="usuario.GUI.php?eliminar=<?= $u->getidusuario() ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de que deseas dar de baja a este usuario?');">Dar de baja</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #8c827a; padding: 20px;">No hay usuarios registrados en el sistema.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>