<?php
// Conexión a la base de datos (igual que en tu código original)
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

// Procesar formulario para actualizar butaca
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_butaca'])) {
    $id_boleto = $_POST['id_boleto'];
    $nueva_butaca = $_POST['nueva_butaca'];
    
    try {
        // Iniciar transacción
        $conn->beginTransaction();
        
        // 1. Verificar que el boleto existe y está activo
        $stmt = $conn->prepare("SELECT IdButaca FROM Boleto WHERE Id_Boleto = :id_boleto AND Activo = true");
        $stmt->bindParam(':id_boleto', $id_boleto);
        $stmt->execute();
        $butaca_actual = $stmt->fetchColumn();
        
        if ($butaca_actual) {
            // 2. Verificar que la nueva butaca está disponible
            $stmt = $conn->prepare("SELECT COUNT(*) FROM Boleto WHERE IdButaca = :nueva_butaca AND Activo = true");
            $stmt->bindParam(':nueva_butaca', $nueva_butaca);
            $stmt->execute();
            $butaca_ocupada = $stmt->fetchColumn();
            
            if ($butaca_ocupada == 0) {
                // 3. Actualizar el boleto con la nueva butaca
                $stmt = $conn->prepare("UPDATE Boleto SET IdButaca = :nueva_butaca WHERE Id_Boleto = :id_boleto");
                $stmt->bindParam(':nueva_butaca', $nueva_butaca);
                $stmt->bindParam(':id_boleto', $id_boleto);
                $stmt->execute();
                
                // 4. Obtener detalles de ambas butacas para mostrar
                $stmt = $conn->prepare("SELECT Id_Butaca, Fila, Columna FROM Butaca WHERE Id_Butaca IN (:butaca_actual, :nueva_butaca)");
                $stmt->bindParam(':butaca_actual', $butaca_actual);
                $stmt->bindParam(':nueva_butaca', $nueva_butaca);
                $stmt->execute();
                $detalles_butacas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $conn->commit();
                $mensaje = "Butaca actualizada correctamente";
            } else {
                $conn->rollBack();
                $mensaje = "La butaca seleccionada no está disponible";
            }
        } else {
            $conn->rollBack();
            $mensaje = "El boleto no existe o no está activo";
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
    <title>Actualizar Butaca</title>
    <style>
        /* Estilos similares a tu código original */
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
            color: #1976D2;
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
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            background-color: #1976D2;
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
            background-color: #1565C0;
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
        .butacas-info {
            margin-top: 15px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Actualizar Butaca de Boleto</h1>
        
        <form method="post">
            <div class="form-group">
                <label for="id_boleto">ID del Boleto:</label>
                <input type="number" id="id_boleto" name="id_boleto" required>
            </div>
            
            <div class="form-group">
                <label for="nueva_butaca">Nueva Butaca (ID):</label>
                <input type="number" id="nueva_butaca" name="nueva_butaca" required>
            </div>
            
            <button type="submit" name="actualizar_butaca">Actualizar Butaca</button>
        </form>
        
        <?php if (isset($mensaje)): ?>
            <div class="message <?php echo (strpos($mensaje, 'Error') === false) ? 'success' : 'error'; ?>">
                <p><?php echo $mensaje; ?></p>
                
                <?php if (isset($detalles_butacas) && !empty($detalles_butacas)): ?>
                    <div class="butacas-info">
                        <h3>Detalles del cambio:</h3>
                        <?php 
                        $butaca_actual = null;
                        $nueva_butaca = null;
                        
                        foreach ($detalles_butacas as $butaca) {
                            $id = $butaca['Id_Butaca'] ?? $butaca['id_butaca'] ?? $butaca['id'] ?? '';
                            $fila = $butaca['Fila'] ?? $butaca['fila'] ?? '';
                            $columna = $butaca['Columna'] ?? $butaca['columna'] ?? '';
                            
                            if ($id == $butaca_actual) {
                                $butaca_actual = "Butaca {$fila}{$columna} (ID: {$id})";
                            } else {
                                $nueva_butaca = "Butaca {$fila}{$columna} (ID: {$id})";
                            }
                        }
                        ?>
                        
                        <p><strong>Butaca anterior:</strong> <?= $butaca_actual ?? 'No disponible' ?></p>
                        <p><strong>Nueva butaca:</strong> <?= $nueva_butaca ?? 'No disponible' ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>