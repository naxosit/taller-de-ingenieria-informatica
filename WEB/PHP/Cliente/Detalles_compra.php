<?php
session_start();
$compra = $_SESSION['compra'] ?? null;

if (!$compra) {
    header("Location: index.php");
    exit;
}

// Limpiar la sesión después de mostrar
unset($_SESSION['compra']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación - Cine Azul</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4F42B5;
            --success: #4CAF50;
        }
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px 20px;
            background-color: #f9f9f9;
        }
        .confirmation-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .confirmation-icon {
            font-size: 80px;
            color: var(--success);
            margin-bottom: 20px;
        }
        .movie-poster {
            width: 150px;
            height: 225px;
            margin: 0 auto 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        .movie-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .btn-home {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 12px 25px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 30px;
            font-weight: bold;
        }
        .details-list {
            text-align: left;
            max-width: 400px;
            margin: 20px auto;
        }
        .details-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1>¡Compra Exitosa!</h1>
        <p>Tu compra ha sido procesada correctamente</p>
        
        <?php if (!empty($compra['imagen'])): ?>
            <div class="movie-poster">
                <img src="<?= htmlspecialchars($compra['imagen']) ?>" alt="Póster de la película">
            </div>
        <?php endif; ?>
        
        <h2><?= htmlspecialchars($compra['pelicula']) ?></h2>
        
        <div class="details-list">
            <div class="details-item">
                <strong>Cine:</strong> <?= htmlspecialchars($compra['cine']) ?>
            </div>
            <div class="details-item">
                <strong>Sala:</strong> <?= htmlspecialchars($compra['sala']) ?>
            </div>
            <div class="details-item">
                <strong>Fecha y Hora:</strong> <?= $compra['fecha'] ?> a las <?= $compra['hora'] ?>
            </div>
            <div class="details-item">
                <strong>Asientos:</strong> <?= implode(', ', $compra['asientos']) ?>
            </div>
            <div class="details-item">
                <strong>Total:</strong> $<?= number_format($compra['total'], 0, ',', '.') ?>
            </div>
            <div class="details-item">
                <strong>Método de pago:</strong> 
                <?= htmlspecialchars(ucfirst($compra['tipoTarjeta'])) ?> 
                (<?= htmlspecialchars($compra['marcaTarjeta']) ?>)
            </div>
            <div class="details-item">
                <strong>Terminación:</strong> ****<?= htmlspecialchars($compra['ultimosDigitos']) ?>
            </div>
        </div>
        
        <a href="index.php" class="btn-home">
            <i class="fas fa-home"></i> Volver al Inicio
        </a>
    </div>
</body>
</html>