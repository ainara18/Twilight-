<?php
// Activar reporte de errores estricto para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Rutas corregidas desde la carpeta 'vistas/' hacia la raíz del proyecto
require_once '../clases/usuario.php';
require_once '../modelos/usuario.model.php';

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $clave = $_POST['clave'] ?? '';

    if ($nombre && $direccion && $email && $clave) {
        try {
            $usuarioModel = new UsuarioModel();
            $usuario = new Usuario();

            $usuario->setnombre($nombre);
            $usuario->setdireccion($direccion);
            $usuario->setemail($email);
            $usuario->setclave(password_hash($clave, PASSWORD_DEFAULT));
            
            // Asignamos el ID 2 por defecto (Cliente)
            if (method_exists($usuario, 'setidcargo')) {
                $usuario->setidcargo(2);
            } elseif (method_exists($usuario, 'setIdCargo')) {
                $usuario->setIdCargo(2);
            }

            $usuarioModel->Registrar($usuario);
            $exito = "Registro exitoso. Ahora puedes <a href='../clases/login.html'>iniciar sesión</a>.";
        } catch (Exception $e) {
            $error = "Error al registrar el usuario: " . $e->getMessage();
        }
    } else {
        $error = "Todos los campos son obligatorios y el correo electrónico debe ser válido.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — Twilight</title>
    <link rel="stylesheet" href="../Css/registro.css">
</head>
<body>

    <main class="contenedor-registro">
        <h1 class="brand-title">Twilight</h1>
        <h2 class="sub-title">Crear Cuenta</h2>
        <p class="subtitle-desc">Ingresá tus datos para registrarte</p>

        <?php if (!empty($error)): ?>
            <div class="msg-error" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 5px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($exito)): ?>
            <div class="msg-exito" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 5px;"><?= $exito ?></div>
        <?php else: ?>
            <form method="POST" action="registro.GUI.php">
                <div class="form-group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Tu nombre completo" required>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" placeholder="Calle, número, ciudad" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
                </div>

                <div class="form-group">
                    <label for="clave">Contraseña</label>
                    <input type="password" id="clave" name="clave" placeholder="********" required>
                </div>

                <button type="submit" class="btn-submit">Registrarse</button>
            </form>

            <p class="link-login">
                ¿Ya tienes cuenta? <a href="../clases/login.html">Inicia sesión aquí</a>
            </p>
        <?php endif; ?>
    </main>

</body>
</html>