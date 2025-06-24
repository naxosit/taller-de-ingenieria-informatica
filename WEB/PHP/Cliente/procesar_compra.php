<?php
// Conectar a la base de datos
include_once("../../CONNECTION/conexion.php");

session_start();

// Recuperar datos de la compra
$idFuncion = $_POST['idFuncion'];
$asientos = $_POST['asientos'];
$total = count($asientos) * 2500; // Precio por asiento


try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener información de la función
    $stmt = $pdo->prepare("
        SELECT 
            F.*, 
            P.nombre AS nombre_pelicula,
            P.duracion,
            P.imagen,
            S.nombre AS nombre_sala,
            C.nombre_cine
        FROM funcion F
        JOIN pelicula P ON F.id_pelicula = P.idpelicula
        JOIN sala S ON F.id_sala = S.idsala
        JOIN cine C ON S.cine_idcine = C.idcine
        WHERE F.idfuncion = :idFuncion
    ");
    $stmt->bindParam(':idFuncion', $idFuncion);
    $stmt->execute();
    $funcion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$funcion) {
        throw new Exception("No se encontró la función seleccionada");
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago - Cine Azul</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../CSS/Client/Procesar.css">
</head>
<body>
    <div class="container">
        <!-- Resumen de Compra -->
        <div class="summary-card">
            <h2 class="section-title">Resumen de Compra</h2>
            
            <div class="movie-info">
                <div class="poster">
                    <?php if (!empty($funcion['imagen'])): ?>
                        <img src="<?php echo htmlspecialchars($funcion['imagen']); ?>">
                    <?php else: ?>
                        <i class="fas fa-film"></i>
                    <?php endif; ?>
                </div>
                <div class="details">
                    <h3 class="movie-title"><?php echo htmlspecialchars($funcion['nombre_pelicula']); ?></h3>
                    <p class="format"><?php echo "2D, CONV, SUBTITULAD"; ?></p>
                    
                    <div class="info-item">
                        <span class="info-label">Cine:</span>
                        <span><?php echo htmlspecialchars($funcion['nombre_cine']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha:</span>
                        <span><?php echo date('d M Y', strtotime($funcion['fechahora'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Hora:</span>
                        <span><?php echo date('H:i', strtotime($funcion['fechahora'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Sala:</span>
                        <span><?php echo htmlspecialchars($funcion['nombre_sala']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="ticket-summary">
                <div class="summary-row">
                    <span>Butacas:</span>
                    <span><?php echo count($asientos); ?></span>
                </div>
                <div class="summary-row">
                    <span>Entradas:</span>
                    <span><?php echo count($asientos); ?></span>
                </div>
                <div class="summary-row total-row">
                    <span>Total:</span>
                    <span>$<?php echo number_format($total, 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Datos de Pago -->
        <div class="payment-card">
            <h2 class="section-title">Datos de Pago</h2>
            
            <form method="POST" action="confirmar_pago.php">
                <!-- Datos de la transacción -->
                <input type="hidden" name="idFuncion" value="<?php echo $idFuncion; ?>">
                <?php foreach ($asientos as $asiento): ?>
                    <input type="hidden" name="asientos[]" value="<?php echo $asiento; ?>">
                <?php endforeach; ?>
                
                <!-- Información de tarjeta -->
                <div class="card-group">
                    <div class="form-group">
                        <label for="card-type">Tipo de tarjeta</label>
                        <select id="card-type" name="card-type" required>
                            <option value="">Seleccionar</option>
                            <option value="debito">Débito</option>
                            <option value="credit">Crédito</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="card-brand">Marca</label>
                        <select id="card-brand" name="card-brand" required>
                            <option value="">Seleccionar</option>
                            <option value="visa">Visa</option>
                            <option value="mastercard">Mastercard</option>
                            <option value="amex">American Express</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="last-digits">Últimos 4 dígitos de la tarjeta</label>
                    <input type="text" id="last-digits" name="last-digits" 
                           pattern="\d{4}" maxlength="4" placeholder="1234" required>
                </div>
                
                <div class="disclaimer">
                    <div class="disclaimer-item">
                        <i class="fas fa-info-circle"></i>
                        <span>No se hacen cambios ni devoluciones</span>
                    </div>
                    <div class="disclaimer-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Toda la información de pago es segura</span>
                    </div>
                </div>
                
                <button type="submit" class="btn-pay">
                    <i class="fas fa-lock"></i> Confirmar Pago
                </button>
            </form>
        </div>
    </div>
</body>
</html>