<?php
session_start();
include_once("../.././../CONNECTION/conexion.php"); // 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $id_promocion = isset($_POST['idpromocion']) ? intval($_POST['idpromocion']) : 0;
    
    try {
        // --- Acción para GUARDAR (Crear o Actualizar) ---
        if ($action == 'save') {
            $nombre = trim($_POST['nombrepromocion']);
            $descripcion = trim($_POST['descripcionpromocion']);
            $dia = trim($_POST['diapromocion']);

            if (empty($nombre) || empty($descripcion) || empty($dia) || ctype_space($descripcion)) {
                $_SESSION['status_message'] = "Error: Todos los campos son obligatorios y no pueden contener solo espacios.";
            } else {
                if ($id_promocion > 0) {
                    // --- Actualizar una promoción existente ---
                    $sql = "UPDATE promocionesdiarias SET nombrepromocion = :nombre, descripcionpromocion = :descripcion, diapromocion = :dia WHERE idpromocion = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $id_promocion, PDO::PARAM_INT);
                } else {
                    // --- Crear una nueva promoción ---
                    $sql = "INSERT INTO promocionesdiarias (nombrepromocion, descripcionpromocion, diapromocion) VALUES (:nombre, :descripcion, :dia)";
                    $stmt = $conn->prepare($sql);
                }

                $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
                $stmt->bindParam(':dia', $dia, PDO::PARAM_STR);
                $stmt->execute();
                
                $_SESSION['status_message'] = "Promoción guardada correctamente.";
            }
        }
        // --- Acción para ELIMINAR ---
        elseif ($action == 'delete' && $id_promocion > 0) {
            $sql = "DELETE FROM promocionesdiarias WHERE idpromocion = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id_promocion, PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['status_message'] = "Promoción eliminada correctamente.";
        }
    } catch (PDOException $e) {
        $_SESSION['status_message'] = "Error en la base de datos: " . $e->getMessage();
    }

    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

try {
    $stmt = $conn->query("SELECT * FROM promocionesdiarias ORDER BY idpromocion ASC");
    $promociones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al consultar las promociones: " . $e->getMessage());
}

$status_message = '';
if (isset($_SESSION['status_message'])) {
    $status_message = $_SESSION['status_message'];
    unset($_SESSION['status_message']);
}


$dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Promociones</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; text-align: center; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .form-container, .promo-item { background: #fdfdfd; padding: 20px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #ddd; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type="text"], textarea, select { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 80px; }
        .btn { padding: 10px 15px; border: none; border-radius: 5px; color: #fff; font-weight: bold; cursor: pointer; display: inline-block; text-align: center;}
        .btn-save { background-color: #28a745; }
        .btn-update { background-color: #007bff; }
        .btn-delete { background-color: #dc3545; width: 100%; box-sizing: border-box; }
        .status-message { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb;}
    </style>
</head>
<body>



<div class="container">
    <h1>Gestionar Promociones</h1>

    <?php if ($status_message): ?>
        <div class="status-message"><?php echo htmlspecialchars($status_message); ?></div>
    <?php endif; ?>

    <div class="form-container">
        <h2>Crear Nueva Promoción</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <input type="hidden" name="action" value="save">
            <div class="form-group">
                <label for="nombre">Nombre de la Promoción</label>
                <input type="text" id="nombre" name="nombrepromocion" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcionpromocion" required></textarea>
            </div>
            <div class="form-group">
                <label for="dia">Día de la Promoción</label>
                <select id="dia" name="diapromocion" required>
                    <option value="">Seleccione un día...</option>
                    <?php foreach ($dias_semana as $dia): ?>
                        <option value="<?php echo $dia; ?>"><?php echo $dia; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-save">Crear Promoción</button>
        </form>
    </div>

    <hr>

    <h2>Promociones Actuales</h2>
    <?php if (empty($promociones)): ?>
        <p>No hay promociones registradas.</p>
    <?php else: ?>
        <?php foreach ($promociones as $promo): ?>
            <div class="promo-item">
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="idpromocion" value="<?php echo $promo['idpromocion']; ?>">
                    
                    <div class="form-group">
                        <label>Nombre de la Promoción</label>
                        <input type="text" name="nombrepromocion" value="<?php echo htmlspecialchars($promo['nombrepromocion']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcionpromocion" required><?php echo htmlspecialchars($promo['descripcionpromocion']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Día de la Promoción</label>
                        <select name="diapromocion" required>
                            <?php foreach ($dias_semana as $dia): ?>
                                <option value="<?php echo $dia; ?>" <?php if ($promo['diapromocion'] == $dia) echo 'selected'; ?>>
                                    <?php echo $dia; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-update">Actualizar</button>
                </form>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="idpromocion" value="<?php echo $promo['idpromocion']; ?>">
                    <button type="submit" class="btn btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar esta promoción?');">Eliminar</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>