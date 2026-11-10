<?php
require_once '../clases/cargo.php';
require_once '../modelos/cargo.model.php';

$cargoModel = new CargoModel();
$cargo = new Cargo(); // Objeto para el formulario

// Manejo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['operacion'])) {
    $operacion = $_POST['operacion'];
    $idCargo = filter_input(INPUT_POST, 'idCargo', FILTER_VALIDATE_INT);
    $nombreCargo = trim($_POST['Cargo'] ?? '');

    switch ($operacion) {
        case 'actualizar':
            if ($idCargo && $nombreCargo !== '') {
                $cargo->setidCargo($idCargo);
                $cargo->setcargo($nombreCargo);
                $cargoModel->Actualizar($cargo);
                header('Location: cargo.GUI.php');
                exit();
            }
            break;

        case 'registrar':
            if ($nombreCargo !== '') {
                $cargo->setcargo($nombreCargo);
                $cargoModel->Registrar($cargo);
                header('Location: cargo.GUI.php');
                exit();
            }
            break;

        case 'eliminar':
            $idEliminar = filter_input(INPUT_POST, 'idcargo', FILTER_VALIDATE_INT);
            if ($idEliminar) {
                $cargoModel->Eliminar($idEliminar);
                header('Location: cargo.GUI.php');
                exit();
            }
            break;

        case 'editar':
            $idEditar = filter_input(INPUT_POST, 'idcargo', FILTER_VALIDATE_INT);
            if ($idEditar) {
                $cargo = $cargoModel->Obtener($idEditar);
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Cargos</title>
    <link rel="stylesheet" href="../Css/Cargo.GUI.css">
</head>
<body>

    <div class="container">
        <h1>Administración de Cargos</h1>
        <div class="form-container">
    
            <!-- FORMULARIO ADMINISTRACION CARGOS -->
            <form action="cargo.GUI.php" method="post">
                <input type="hidden" name="operacion" value="<?= ($cargo->getidcargo() > 0) ? 'actualizar' : 'registrar'; ?>">
                <input type="hidden" name="idCargo" value="<?= htmlspecialchars($cargo->getidcargo()); ?>">

                <label for="name">Cargo:</label>
                <input type="text" name="Cargo" required value="<?= htmlspecialchars($cargo->getCargo() ?? ''); ?>"><br>

                <button type="submit">
                    <?= ($cargo->getidcargo() > 0) ? 'Actualizar' : 'Registrar'; ?>
                </button>
            </form>
        </div>

        <div class="list-container">
            <!-- Tabla de Cargos -->
            <h2>Listado de Cargos</h2>
            <table border="1">
                <tr>
                    <th>Cargo</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($cargoModel->ListarTodos() as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r->getcargo()); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="operacion" value="editar">
                                <input type="hidden" name="idcargo" value="<?= $r->getidCargo(); ?>">
                                <button type="submit">Editar</button>
                            </form>
                            <form method="post" style="display:inline;" onsubmit="return confirm('¿Desea eliminar este cargo?');">
                                <input type="hidden" name="operacion" value="eliminar">
                                <input type="hidden" name="idcargo" value="<?= $r->getidCargo(); ?>">
                                <button type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
