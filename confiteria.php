<?php
// Conexión a la base de datos
include_once "../../CONNECTION/conexion.php";

// Verificar si la conexión se estableció correctamente (para diagnóstico)
if (!isset($conn)) {
    die("Error: No se pudo establecer la conexión a la base de datos");
}
// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    try {
        switch($_POST['accion']) {
            case 'agregar':
                $stmt = $conn->prepare("INSERT INTO confiteria (nombre, precio, categoria) VALUES (?, ?, ?)");
                $stmt->execute([$_POST['nombre'], $_POST['precio'], $_POST['categoria']]);
                $mensaje = "Producto agregado exitosamente!";
                break;
                
            case 'editar':
                $stmt = $conn->prepare("UPDATE confiteria SET nombre = ?, precio = ?, categoria = ?, disponible = ? WHERE id_producto = ?");
                $stmt->execute([
                    $_POST['nombre'],
                    $_POST['precio'],
                    $_POST['categoria'],
                    isset($_POST['disponible']) ? true : false,
                    $_POST['id_producto']
                ]);
                $mensaje = "Producto actualizado!";
                break;
                
            case 'eliminar':
                $stmt = $conn->prepare("DELETE FROM confiteria WHERE id_producto = ?");
                $stmt->execute([$_POST['id_producto']]);
                $mensaje = "Producto eliminado!";
                break;
        }
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
    }
}

// Obtener productos ordenados por categoría y nombre
$productos = $conn->query("SELECT * FROM confiteria ORDER BY categoria, nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú de Confitería</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <style>
        .menu-container {
            max-width: 800px;
            margin: 20px auto;
        }
        
        .categoria-group {
            margin-bottom: 30px;
            background: var(--card-bg);
            padding: 15px;
            border-radius: 8px;
        }
        
        .categoria-titulo {
            color: var(--accent-color);
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .producto-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #333;
        }
        
        .producto-nombre {
            font-weight: 500;
        }
        
        .producto-precio {
            color: var(--accent-color);
            font-weight: bold;
        }
        
        .form-container {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Menú de Confitería</div>
        <nav>
            <a href="../index.php">← Volver</a>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Productos de Confitería</h1>

        <?php if (isset($mensaje)): ?>
            <div class="mensaje <?= strpos($mensaje, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para agregar/editar -->
        <div class="form-container">
            <h2><?= isset($_GET['editar']) ? 'Editar' : 'Agregar' ?> Producto</h2>
            <form method="POST">
                <input type="hidden" name="accion" value="<?= isset($_GET['editar']) ? 'editar' : 'agregar' ?>">
                <?php if (isset($_GET['editar'])): ?>
                    <input type="hidden" name="id_producto" value="<?= $_GET['editar'] ?>">
                    <?php 
                    $producto = $conn->query("SELECT * FROM confiteria WHERE id_producto = " . $_GET['editar'])->fetch();
                    ?>
                <?php endif; ?>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" required 
                               value="<?= isset($producto) ? htmlspecialchars($producto['nombre']) : '' ?>">
                    </div>

                    <div class="form-group">
                        <label>Precio ($):</label>
                        <input type="number" step="0.01" name="precio" required 
                               value="<?= isset($producto) ? $producto['precio'] : '' ?>">
                    </div>

                    <div class="form-group">
                        <label>Categoría:</label>
                        <select name="categoria" required>
                            <option value="Bebidas" <?= isset($producto) && $producto['categoria'] == 'Bebidas' ? 'selected' : '' ?>>Bebidas</option>
                            <option value="Snacks" <?= isset($producto) && $producto['categoria'] == 'Snacks' ? 'selected' : '' ?>>Snacks</option>
                            <option value="Combos" <?= isset($producto) && $producto['categoria'] == 'Combos' ? 'selected' : '' ?>>Combos</option>
                            <option value="Dulces" <?= isset($producto) && $producto['categoria'] == 'Dulces' ? 'selected' : '' ?>>Dulces</option>
                        </select>
                    </div>

                    <?php if (isset($_GET['editar'])): ?>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="disponible" <?= $producto['disponible'] ? 'checked' : '' ?>>
                                Disponible
                            </label>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-submit">Guardar</button>
                <?php if (isset($_GET['editar'])): ?>
                    <a href="confiteria.php" class="btn-cancel">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Menú por categorías -->
        <div class="menu-container">
            <?php 
            // Agrupar por categoría
            $productosPorCategoria = [];
            foreach ($productos as $producto) {
                $productosPorCategoria[$producto['categoria']][] = $producto;
            }
            
            foreach ($productosPorCategoria as $categoria => $items): ?>
                <div class="categoria-group">
                    <h3 class="categoria-titulo"><?= htmlspecialchars($categoria) ?></h3>
                    
                    <?php foreach ($items as $producto): ?>
                        <div class="producto-item <?= !$producto['disponible'] ? 'no-disponible' : '' ?>">
                            <div class="producto-info">
                                <span class="producto-nombre"><?= htmlspecialchars($producto['nombre']) ?></span>
                                <?php if (!$producto['disponible']): ?>
                                    <span class="badge-no-disponible">No disponible</span>
                                <?php endif; ?>
                            </div>
                            <span class="producto-precio">$<?= number_format($producto['precio'], 2) ?></span>
                            <div class="producto-acciones">
                                <a href="confiteria.php?editar=<?= $producto['id_producto'] ?>" class="btn-edit">Editar</a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                    <button type="submit" class="btn-delete" onclick="return confirm('¿Eliminar este producto?')">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>