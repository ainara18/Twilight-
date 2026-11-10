<?php
session_start();
require_once 'clases/conexion.php';

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    if (!isset($_SESSION['usuario_id'])) {
        $alertaLogin = true;
    } else {
        $id_prod = $_POST['id_prod'];
        $_SESSION['carrito'][] = $id_prod;
        header("Location: index.php?agregado=1");
        exit();
    }
}

try {
    $conexionObj = new Conexion();
    $db = $conexionObj->getConexion();

    $stmt = $db->query("SELECT * FROM producto");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $productos = [];
}

$cantidadCarrito = count($_SESSION['carrito']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twilight — Bijouterie & Accesorios</title>
    <link rel="stylesheet" href="Css/index.css?v=5">
</head>
<body>
    <header>
        <nav class="navbar" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h1>Twilight Bijouterie</h1>
            <div class="menu-nav" style="display: flex; gap: 1.5rem; align-items: center;">
                <a href="index.php" style="text-decoration: none; color: #2C2C2C; font-weight: 600;">Inicio</a>
                
                <!-- Enlace actualizado hacia clases/carrito.php -->
                <a href="clases/carrito.php" style="text-decoration: none; font-weight: 600; color: #B89628; background: #FAF8F5; padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid #B89628;">
                    🛒 Carrito (<?php echo $cantidadCarrito; ?>)
                </a>

                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <span>Hola, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></span>
                    <a href="clases/logout.php" style="text-decoration: none; color: #d32f2f; font-weight: bold; background: #ffdddd; padding: 0.5rem 1rem; border-radius: 6px;">Cerrar sesión</a>
                <?php else: ?>
                    <a href="clases/login.html" style="text-decoration: none; color: #B89628; font-weight: 600;">Iniciar Sesión</a>
                    <a href="clases/registro.html" style="text-decoration: none; background: #1a1a1a; color: #fff; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600;">Registrarse</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main style="padding: 3rem 2rem; text-align: center;">
        <h2 style="font-size: 2rem; margin-bottom: 2rem; color: #1a1a1a;">Nuestros Productos</h2>

        <?php if (!empty($productos)): ?>
            <div class="contenedor-productos" style="display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap;">
                <?php foreach ($productos as $prod): ?>
                    <div class="card-producto" style="background: #fff; border-radius: 12px; padding: 1.5rem; width: 280px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); text-align: left; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="background: #f9f9f9; border-radius: 8px; overflow: hidden; margin-bottom: 1rem; height: 180px; display: flex; align-items: center; justify-content: center;">
                                <img src="Imagenes/<?php echo htmlspecialchars($prod['imagen']); ?>" alt="<?php echo htmlspecialchars($prod['nom_prod']); ?>" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                            </div>
                            <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem; color: #222;"><?php echo htmlspecialchars($prod['nom_prod']); ?></h3>
                            <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($prod['descripcion_prod']); ?></p>
                        </div>
                        <div>
                            <div style="font-size: 1.1rem; font-weight: bold; color: #B89628; margin-bottom: 1rem;">$<?php echo number_format($prod['precio'], 2); ?></div>
                            
                            <form method="POST" action="">
                                <input type="hidden" name="accion" value="agregar">
                                <input type="hidden" name="id_prod" value="<?php echo $prod['id_prod']; ?>">
                                <button type="submit" style="width: 100%; padding: 0.6rem; background: #1a1a1a; color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.3s;">Agregar al carrito</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: #666;">No hay productos disponibles en este momento.</p>
        <?php endif; ?>
    </main>

    <?php if (isset($alertaLogin) && $alertaLogin): ?>
        <script>
            alert('Debes iniciar sesión o registrarte para poder agregar productos al carrito.');
            window.location.href = 'clases/login.html';
        </script>
    <?php endif; ?>
</body>
</html>