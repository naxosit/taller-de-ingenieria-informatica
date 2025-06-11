<?php
include_once("../../CONNECTION/conexion.php");

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rut = $_POST['rut'];

    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Perfil WHERE RUT = :rut");
        $stmt->bindParam(':rut', $rut);
        $stmt->execute();
        $existe = $stmt->fetchColumn();

        if ($existe > 0) {
            $conn->beginTransaction();

            $stmt = $conn->prepare("SELECT IdButaca FROM Boleto WHERE RUT = :rut AND Activo = true");
            $stmt->bindParam(':rut', $rut);
            $stmt->execute();
            $butacas_a_liberar = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $stmt = $conn->prepare("UPDATE Boleto SET Activo = false WHERE RUT = :rut AND Activo = true");
            $stmt->bindParam(':rut', $rut);
            $stmt->execute();
            $filas_afectadas = $stmt->rowCount();

            if ($filas_afectadas > 0 && !empty($butacas_a_liberar)) {
                $placeholders = implode(',', array_fill(0, count($butacas_a_liberar), '?'));
                $stmt = $conn->prepare("DELETE FROM Boleto WHERE IdButaca IN ($placeholders)");
                $stmt->execute($butacas_a_liberar);

                $stmt = $conn->prepare("SELECT Id_Butaca, Fila, Columna FROM Butaca WHERE Id_Butaca IN ($placeholders)");
                $stmt->execute($butacas_a_liberar);
                $detalles_butacas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $conn->commit();
                $mensaje = "Se anularon $filas_afectadas compras asociadas al RUT $rut";
            } else {
                $conn->rollBack();
                $mensaje = "No se encontraron compras activas para el RUT $rut";
            }
        } else {
            $mensaje = "El RUT ingresado no existe en el sistema";
        }
    } catch(PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $mensaje = "Error al procesar la solicitud: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anular Compras</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <link rel="stylesheet" href="../../CSS/botones.css">
    <style>
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
        .mensaje-exito, .mensaje-error {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            font-weight: bold;
        }
        .mensaje-exito {
            background-color: #e8f5e9;
            color: #1b5e20;
            border-left: 5px solid #2e7d32;
        }
        .mensaje-error {
            background-color: #ffebee;
            color: #b71c1c;
            border-left: 5px solid #c62828;
        }
        .butacas-list {
            margin-top: 15px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        .butacas-list ul {
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Web Cine - Gestión de Compras</div>
        <nav>
            <a href="Cartelera.php">Cartelera</a>
            <a href="vista_encargado.php">Películas</a>
            <a href="vista_salas.php">Salas</a>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Anular Compras por RUT</h1>

        <form method="post">
            <div class="form-group">
                <label for="rut">Ingrese el RUT del cliente:</label>
                <input type="text" id="rut" name="rut" class="form-input" placeholder="Ej: 12345678-9" required>
            </div>
            <button type="submit" class="boton-agregar">Anular Compras</button>
        </form>

        <?php if (isset($mensaje)): ?>
            <div class="<?php echo (strpos($mensaje, 'Error') === false && strpos($mensaje, 'no') === false) ? 'mensaje-exito' : 'mensaje-error'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>

            <?php if (isset($detalles_butacas) && !empty($detalles_butacas)): ?>
                <div class="butacas-list">
                    <h3>Butacas liberadas:</h3>
                    <ul>
                            <?php foreach ($detalles_butacas as $butaca): 
                                $fila = $butaca['Fila'] ?? $butaca['fila'] ?? '';
                                $columna = $butaca['Columna'] ?? $butaca['columna'] ?? '';
                                $id = $butaca['Id_Butaca'] ?? $butaca['id_butaca'] ?? $butaca['id'] ?? '';
                                
                                if (!empty($fila) && !empty($columna) && !empty($id)): ?>
                                <li>
                                    Butaca <?= htmlspecialchars($fila) . htmlspecialchars($columna) ?>
                                </li>
                            <?php endif; endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
