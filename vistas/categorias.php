<?php
session_start();
require_once __DIR__ . '/../clases/conexion.php';

$conexionObj = new Conexion();
$db = $conexionObj->getConexion();

$productos = [];
$categoriasList = [];
$categoriaSeleccionada = $_GET['cat'] ?? null;

try {
    $stmtCat = $db->query("SELECT * FROM categorias");
    $categoriasList = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

    if ($categoriaSeleccionada) {
        $sql = "SELECT p.*, c.nom_cat 
                FROM producto p 
                LEFT JOIN categorias c ON p.id_cat = c.id_cat 
                WHERE p.id_cat = ? 
                ORDER BY p.id_prod DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$categoriaSeleccionada]);
    } else {
        $sql = "SELECT p.*, c.nom_cat 
                FROM producto p 
                LEFT JOIN categorias c ON p.id_cat = c.id_cat 
                ORDER BY p.id_prod DESC";
        $stmt = $db->query($sql);
    }
    
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $productos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías — Twilight Bijouterie</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&family=Playfair+Display:ital,wght@0,500;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/index.css">
    <style>
        .productos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .product-card { background: white; border-radius: 12px; padding: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; flex-direction: column; align-items: center; }
        .product-img-container { width: 100%; height: 220px; background: #fdfbf7; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 15px; position: relative; }
        .product-img-container img { width: 100%; height: 100%; object-fit: cover; }
        .placeholder-text { color: #8c827a; font-size: 0.9rem; font-weight: 500; text-align: center; padding: 10px; }
        .categoria-tag { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #8c7355; margin-bottom: 5px; align-self: flex-start; }
        .titulo-producto { font-size: 1.1rem; color: #3a2e2b; margin: 5px 0; align-self: flex-start; font-family: 'Playfair Display', serif; }
        .precio-producto { font-size: 1rem; font-weight: 600; color: #5a4a42; margin-bottom: 15px; align-self: flex-start; }
        .btn-add-cart { width: 100%; background: #222; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: 500; transition: background 0.2s; }
        .btn-add-cart:hover { background: #444; }
        .filtros-container { display: flex; justify-content: center; gap: 10px; margin: 30px 0; flex-wrap: wrap; }
        .filtro-btn { padding: 8px 18px; border-radius: 20px; border: 1px solid #ddd; background: white; cursor: pointer; color: #5a4a42; font-size: 0.9rem; text-decoration: none; transition: all 0.2s; }
        .filtro-btn.active, .filtro-btn:hover { background: #222; color: white; border-color: #222; }
    </style>
</head>
<body>

    <!-- Navegación -->
    <header class="navbar" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 40px; background: white; border-bottom: 1px solid #eee;">
        <div class="logo" style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700;">Twilight</div>
        <nav>
            <ul class="nav-links" style="display: flex; gap: 20px; list-style: none; margin: 0; padding: 0;">
                <li><a href="../clases/index.php" style="text-decoration: none; color: #5a4a42;">Inicio</a></li>
                <li><a href="../clases/categorias.php" style="text-decoration: none; color: #222; font-weight: 600;">Productos</a></li>
                <li><a href="../clases/index.php#nosotros" style="text-decoration: none; color: #5a4a42;">Nosotros</a></li>
                <li><a href="twilight.GUI.php" style="text-decoration: none; color: #8c7355; font-weight: 600;">Gestión Panel</a></li>
            </ul>
        </nav>
        <div class="nav-actions">
            <?php if (isset($_SESSION['usuario_nombre']) || isset($_SESSION['nombre'])): ?>
                <span style="margin-right: 15px; font-weight: 600; color: #5a4a42;">Hola, <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? $_SESSION['nombre']) ?></span>
                <a href="../clases/logout.php" style="background: #e63946; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 0.9rem;">Cerrar Sesión</a>
            <?php else: ?>
                <a href="../clases/login.php" style="background: #222; color: white; padding: 8px 18px; border-radius: 6px; text-decoration: none; font-size: 0.9rem;">Ingresar</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Filtros -->
    <div class="filtros-container">
        <a href="categorias.php" class="filtro-btn <?= !$categoriaSeleccionada ? 'active' : '' ?>">Todos</a>
        <?php foreach ($categoriasList as $cat): ?>
            <a href="categorias.php?cat=<?= $cat['id_cat'] ?>" class="filtro-btn <?= ($categoriaSeleccionada == $cat['id_cat']) ? 'active' : '' ?>"><?= htmlspecialchars($cat['nom_cat']) ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Catálogo -->
    <div class="productos-grid">
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $p): ?>
                <?php 
                    $nombreProd = $p['nom_prod'] ?? 'Producto';
                    $precioProd = $p['precio'] ?? 0;
                    $catNombre  = $p['nom_cat'] ?? '';
                    $imgFile    = $p['imagen'] ?? null;
                ?>
                <div class="product-card">
                    <div class="product-img-container">
                        <?php if (!empty($imgFile)): ?>
                            <img src="../Imagenes/<?= htmlspecialchars($imgFile) ?>" alt="<?= htmlspecialchars($nombreProd) ?>">
                        <?php else: ?>
                            <span class="placeholder-text">✨ <?= htmlspecialchars($nombreProd) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($catNombre)): ?>
                        <span class="categoria-tag"><?= htmlspecialchars($catNombre) ?></span>
                    <?php endif; ?>
                    <h3 class="titulo-producto"><?= htmlspecialchars($nombreProd) ?></h3>
                    <p class="precio-producto">$<?= number_format((float)$precioProd, 0, ',', '.') ?></p>
                    <button class="btn-add-cart">Añadir al Carrito</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="grid-column: 1/-1; text-align: center; color: #8c827a; padding: 40px;">No hay productos disponibles en esta categoría.</p>
        <?php endif; ?>
    </div>

</body>
</html>