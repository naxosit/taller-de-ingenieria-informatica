<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'BD_CINE';
$username = 'postgres';
$password = '123456';

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rut = $_POST['rut'];
    
    try {
        // Verificar si el RUT existe en la tabla Perfil
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Perfil WHERE RUT = :rut");
        $stmt->bindParam(':rut', $rut);
        $stmt->execute();
        $existe = $stmt->fetchColumn();
        
        if ($existe > 0) {
            // Iniciar transacción para asegurar la integridad de los datos
            $conn->beginTransaction();
            
            // 1. Obtener los IDs de butacas antes de anular los boletos
            $stmt = $conn->prepare("SELECT IdButaca FROM Boleto WHERE RUT = :rut AND Activo = true");
            $stmt->bindParam(':rut', $rut);
            $stmt->execute();
            $butacas_a_liberar = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // 2. Anular los boletos (marcar como inactivos)
            $stmt = $conn->prepare("UPDATE Boleto SET Activo = false WHERE RUT = :rut AND Activo = true");
            $stmt->bindParam(':rut', $rut);
            $stmt->execute();
            $filas_afectadas = $stmt->rowCount();
            
            if ($filas_afectadas > 0 && !empty($butacas_a_liberar)) {
                // 3. Actualizar estado de butacas a "disponible"
                $placeholders = implode(',', array_fill(0, count($butacas_a_liberar), '?'));
                $stmt = $conn->prepare("DELETE FROM Boleto WHERE IdButaca IN ($placeholders)");
                $stmt->execute($butacas_a_liberar);
                
                // 4. Obtener detalles de butacas liberadas para mostrar
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
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d32f2f;
            text-align: center;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            background-color: #d32f2f;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #b71c1c;
        }
        .message {
            margin-top: 25px;
            padding: 15px;
            border-radius: 4px;
            border-left: 5px solid;
        }
        .success {
            background-color: #e8f5e9;
            border-color: #2e7d32;
            color: #1b5e20;
        }
        .error {
            background-color: #ffebee;
            border-color: #c62828;
            color: #b71c1c;
        }
        .butacas-list {
            margin-top: 15px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        .butacas-list h3 {
            margin-top: 0;
            color: #333;
        }
        .butacas-list ul {
            padding-left: 20px;
        }
        .butacas-list li {
            margin-bottom: 5px;
            padding: 5px;
            background-color: white;
            border-radius: 3px;
        }
    </style>
</head> 
<body>
    <div class="container">
        <h1>Anular Compras por RUT</h1>
        
        <form method="post">
            <div class="form-group">
                <label for="rut">Ingrese el RUT del cliente:</label>
                <input type="text" id="rut" name="rut" placeholder="Ej: 12345678-9" required>
            </div>
            
            <button type="submit">Anular Compras</button>
        </form>
        
        <?php if (isset($mensaje)): ?>
            <div class="message <?php echo (strpos($mensaje, 'Error') === false) ? 'success' : 'error'; ?>">
                <p><?php echo $mensaje; ?></p>
                
                <?php if (isset($detalles_butacas) && !empty($detalles_butacas)): ?>
                    <div class="butacas-list">
                        <h3>Butacas liberadas:</h3>
                        <ul>
                            <?php foreach ($detalles_butacas as $butaca): 
                                if (isset($butaca['Fila'], $butaca['Columna'], $butaca['Id_Butaca'])): ?>
                                <li>
                                    Butaca <?= htmlspecialchars($butaca['Fila']) . htmlspecialchars($butaca['Columna']) ?> 
                                    (ID: <?= htmlspecialchars($butaca['Id_Butaca']) ?>)
                                </li>
                            <?php endif; endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>