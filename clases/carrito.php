<?php
session_start();
require_once 'conexion.php';

// Si no inició sesión, lo redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Eliminar un producto del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $indice = $_POST['indice'];
    if (isset($_SESSION['carrito'][$indice])) {
        unset($_SESSION['carrito'][$indice]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    }
    header("Location: carrito.php");
    exit();
}

// Vaciar carrito completo
if (isset($_GET['vaciar']) && $_GET['vaciar'] == '1') {
    $_SESSION['carrito'] = [];
    header("Location: carrito.php");
    exit();
}

// Obtener los detalles de los productos agregados
$productosEnCarrito = [];
$totalGeneral = 0;

if (!empty($_SESSION['carrito'])) {
    try {
        $conexionObj = new Conexion();
        $db = $conexionObj->getConexion();

        foreach ($_SESSION['carrito'] as $index => $id_prod) {
            $stmt = $db->prepare("SELECT * FROM producto WHERE id_prod = ? LIMIT 1");
            $stmt->execute([$id_prod]);
            $prod = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($prod) {
                $prod['indice_session'] = $index;
                $productosEnCarrito[] = $prod;
                $totalGeneral += $prod['precio'];
            }
        }
    } catch (PDOException $e) {
        $error_db = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito — Twilight</title>
    <!-- Salimos de clases/ para entrar a Css/ -->
    <link rel="stylesheet" href="../Css/index.css?v=5">
</head>
<body>
    <header>
        <nav class="navbar" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h1>Twilight Bijouterie</h1>
            <div class="menu-nav" style="display: flex; gap: 1.5rem; align-items: center;">
                <a href="../index.php" style="text-decoration: none; color: #2C2C2C; font-weight: 600;">← Seguir Comprando</a>
                <span>Hola, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></span>
                <a href="logout.php" style="text-decoration: none; color: #d32f2f; font-weight: bold; background: #ffdddd; padding: 0.5rem 1rem; border-radius: 6px;">Cerrar sesión</a>
            </div>
        </nav>
    </header>

    <main style="padding: 3rem 2rem; max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: 2rem; margin-bottom: 1.5rem; color: #1a1a1a;">Tu Carrito de Compras</h2>

        <?php if (!empty($productosEnCarrito)): ?>
            <div style="background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.06);">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid #eee; height: 40px;">
                            <th>Producto</th>
                            <th>Precio</th>
                            <th style="text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productosEnCarrito as $item): ?>
                            <tr style="border-bottom: 1px solid #eee; height: 70px;">
                                <td style="display: flex; align-items: center; gap: 1rem; padding: 0.5rem 0;">
                                    <!-- Salimos de clases/ para entrar a Imagenes/ -->
                                    <img src="../Imagenes/<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nom_prod']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['nom_prod']); ?></strong>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($item['precio'], 2); ?></td>
                                <td style="text-align: center;">
                                    <form method="POST" action="">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="indice" value="<?php echo $item['indice_session']; ?>">
                                        <button type="submit" style="background: #d32f2f; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer;">Quitar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #eee;">
                    <a href="carrito.php?vaciar=1" style="color: #666; text-decoration: none; font-size: 0.9rem;">Vaciar carrito</a>
                    <div style="text-align: right;">
                        <h3 style="font-size: 1.4rem; color: #1a1a1a;">Total: <span style="color: #B89628;">$<?php echo number_format($totalGeneral, 2); ?></span></h3>
                        <button onclick="alert('¡Gracias por tu compra!');" style="margin-top: 1rem; background: #B89628; color: #fff; border: none; padding: 0.75rem 1.5rem; font-size: 1rem; font-weight: bold; border-radius: 8px; cursor: pointer;">Finalizar Compra</button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="background: #fff; padding: 3rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.06);">
                <p style="color: #666; font-size: 1.1rem; margin-bottom: 1.5rem;">Tu carrito está vacío en este momento.</p>
                <a href="../index.php" style="background: #1a1a1a; color: #fff; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600;">Ver productos</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>