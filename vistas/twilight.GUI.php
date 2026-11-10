<?php
session_start();
require_once __DIR__ . '/../clases/conexion.php';

$conexionObj = new Conexion();
$db = $conexionObj->getConexion();

$error = "";
$exito = "";

// Variables para edición
$editando = false;
$productoEdit = [
    'id_prod' => '',
    'nom_prod' => '',
    'precio' => '',
    'stock_actual' => '',
    'descripcion_prod' => '',
    'id_cat' => '',
    'imagen' => ''
];

// Obtener categorías
try {
    $stmtCat = $db->query("SELECT * FROM categorias");
    $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categorias = [];
}

// 1. ELIMINAR / DAR DE BAJA PRODUCTO
if (isset($_GET['eliminar'])) {
    $idEliminar = (int)$_GET['eliminar'];
    try {
        // Opcional: obtener la imagen para borrarla del servidor
        $stmtImg = $db->prepare("SELECT imagen FROM producto WHERE id_prod = ?");
        $stmtImg->execute([$idEliminar]);
        $prodData = $stmtImg->fetch(PDO::FETCH_ASSOC);
        if ($prodData && !empty($prodData['imagen'])) {
            $rutaImg = __DIR__ . '/../Imagenes/' . $prodData['imagen'];
            if (file_exists($rutaImg)) {
                unlink($rutaImg);
            }
        }

        // Borrar registro de la base de datos
        $stmtDel = $db->prepare("DELETE FROM producto WHERE id_prod = ?");
        $stmtDel->execute([$idEliminar]);
        header("Location: twilight.GUI.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error al eliminar el producto: " . $e->getMessage();
    }
}

// 2. CARGAR DATOS PARA EDITAR
if (isset($_GET['editar'])) {
    $idEditar = (int)$_GET['editar'];
    try {
        $stmtEdit = $db->prepare("SELECT * FROM producto WHERE id_prod = ?");
        $stmtEdit->execute([$idEditar]);
        $resultadoEdit = $stmtEdit->fetch(PDO::FETCH_ASSOC);
        if ($resultadoEdit) {
            $editando = true;
            $productoEdit = $resultadoEdit;
        }
    } catch (PDOException $e) {
        $error = "Error al cargar el producto para editar.";
    }
}

// 3. PROCESAR FORMULARIO (ALTA O ACTUALIZACIÓN)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_prod = trim($_POST['id_prod'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $stock = trim($_POST['stock'] ?? '0');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $id_cat = trim($_POST['id_cat'] ?? '');
    $imagenActual = trim($_POST['imagen_actual'] ?? '');
    
    // Manejo de la subida de nueva imagen
    $nombreImagen = $imagenActual;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombreOriginal = $_FILES['imagen']['name'];
        $tmpName = $_FILES['imagen']['tmp_name'];
        
        $nombreImagen = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $nombreOriginal);
        $carpetaDestino = __DIR__ . '/../Imagenes/';
        
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }
        
        if (move_uploaded_file($tmpName, $carpetaDestino . $nombreImagen)) {
            // Borrar la imagen anterior si existía una nueva
            if (!empty($imagenActual) && file_exists($carpetaDestino . $imagenActual)) {
                unlink($carpetaDestino . $imagenActual);
            }
        }
    }

    if ($nombre && $precio !== '' && $id_cat) {
        try {
            if (!empty($id_prod)) {
                // ACTUALIZAR PRODUCTO EXISTENTE
                $sql = "UPDATE producto SET nom_prod = ?, precio = ?, stock_actual = ?, descripcion_prod = ?, id_cat = ?, imagen = ? WHERE id_prod = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$nombre, $precio, $stock, $descripcion, $id_cat, $nombreImagen, $id_prod]);
                $exito = "¡Producto actualizado correctamente!";
            } else {
                // INSERTAR NUEVO PRODUCTO
                $sql = "INSERT INTO producto (nom_prod, precio, stock_actual, descripcion_prod, id_cat, imagen) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$nombre, $precio, $stock, $descripcion, $id_cat, $nombreImagen]);
                $exito = "¡Producto agregado correctamente!";
            }
            
            // Limpiar variables de edición tras guardar
            $editando = false;
            $productoEdit = ['id_prod' => '', 'nom_prod' => '', 'precio' => '', 'stock_actual' => '', 'descripcion_prod' => '', 'id_cat' => '', 'imagen' => ''];
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        $error = "Por favor completa los campos obligatorios.";
    }
}

// Listar productos
try {
    $stmtProd = $db->query("SELECT p.*, c.nom_cat FROM producto p LEFT JOIN categorias c ON p.id_cat = c.id_cat ORDER BY p.id_prod DESC");
    $productosRegistrados = $stmtProd->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $productosRegistrados = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos — Twilight</title>
    <link rel="stylesheet" href="../Css/registro.css">
    <style>
        .contenedor-gestion { max-width: 850px; margin: 30px auto; padding: 25px; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #5a4a42; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        .tabla-admin { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .tabla-admin th, .tabla-admin td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 0.9rem; }
        .tabla-admin th { background-color: #faf5f2; color: #5a4a42; }
        .img-miniatura { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; }
        .nav-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .nav-header h1 { color: #5a4a42; margin: 0; font-size: 1.8rem; }
        .nav-header a { color: #8c7355; text-decoration: none; font-weight: 600; font-size: 0.95rem; }
        .btn-accion { padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; font-weight: 600; display: inline-block; margin-right: 4px; }
        .btn-editar { background-color: #4a5568; color: white; }
        .btn-editar:hover { background-color: #2d3748; }
        .btn-eliminar { background-color: #e53e3e; color: white; }
        .btn-eliminar:hover { background-color: #c53030; }
    </style>
</head>
<body>

    <div class="contenedor-gestion">
        <div class="nav-header">
            <h1>Twilight Bijouterie</h1>
            <a href="categorias.php">Ver Catálogo Público →</a>
        </div>
        
        <h3 style="color: #5a4a42; margin-bottom: 15px;">
            <?= $editando ? '✏️ Editar Producto (ID: ' . $productoEdit['id_prod'] . ')' : '1. Gestión y Carga de Productos' ?>
        </h3>

        <?php if (!empty($error)): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 6px; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($exito)): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 6px; margin-bottom: 15px;"><?= htmlspecialchars($exito) ?></div>
        <?php endif; ?>

        <form action="twilight.GUI.php" method="POST" enctype="multipart/form-data">
            <!-- Campo oculto para ID si estamos editando -->
            <input type="hidden" name="id_prod" value="<?= htmlspecialchars($productoEdit['id_prod']) ?>">
            <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($productoEdit['imagen']) ?>">

            <div class="form-group">
                <label for="nombre">Nombre Del Producto</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej. Collar Luna" value="<?= htmlspecialchars($productoEdit['nom_prod']) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="precio">Precio ($)</label>
                    <input type="number" step="0.01" id="precio" name="precio" placeholder="Ej. 2000" value="<?= htmlspecialchars($productoEdit['precio']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock Disponible</label>
                    <input type="number" id="stock" name="stock" placeholder="Ej. 10" min="0" value="<?= htmlspecialchars($productoEdit['stock_actual'] !== '' ? $productoEdit['stock_actual'] : '0') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3" placeholder="Escribe detalles del producto..."><?= htmlspecialchars($productoEdit['descripcion_prod']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="id_cat">Categoría</label>
                <select id="id_cat" name="id_cat" required>
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id_cat'] ?>" <?= ($productoEdit['id_cat'] == $cat['id_cat']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom_cat']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="imagen">Imagen del Producto <?= $editando ? '(Dejar en blanco para mantener la actual)' : '' ?></label>
                <input type="file" id="imagen" name="imagen" accept="image/*" <?= $editando ? '' : 'required' ?>>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; background: #222; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    <?= $editando ? 'Actualizar Producto' : 'Agregar Producto' ?>
                </button>
                <?php if ($editando): ?>
                    <a href="twilight.GUI.php" style="background: #ccc; color: #333; padding: 12px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; text-align: center;">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>

        <h3 style="color: #5a4a42; margin-top: 40px; border-bottom: 2px solid #faf5f2; padding-bottom: 8px;">2. Productos Registrados</h3>
        
        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($productosRegistrados)): ?>
                    <?php foreach ($productosRegistrados as $prod): ?>
                        <tr>
                            <td>
                                <?php if (!empty($prod['imagen'])): ?>
                                    <img src="../Imagenes/<?= htmlspecialchars($prod['imagen']) ?>" class="img-miniatura" alt="Foto">
                                <?php else: ?>
                                    <span>✨</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $prod['id_prod'] ?></td>
                            <td><?= htmlspecialchars($prod['nom_prod']) ?></td>
                            <td>$<?= number_format((float)$prod['precio'], 0, ',', '.') ?></td>
                            <td><?= (int)$prod['stock_actual'] ?> u.</td>
                            <td><?= htmlspecialchars($prod['nom_cat'] ?? 'General') ?></td>
                            <td>
                                <a href="twilight.GUI.php?editar=<?= $prod['id_prod'] ?>" class="btn-accion btn-editar">Editar</a>
                                <a href="twilight.GUI.php?eliminar=<?= $prod['id_prod'] ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este producto?');">Borrar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #8c827a; padding: 20px;">No hay productos cargados en el sistema.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>