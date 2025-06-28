<?php
// Conectar a la base de datos
include_once("../../CONNECTION/conexion.php");
session_start();

// Recuperar datos del formulario
$idFuncion = $_POST['idFuncion'];
$asientos = $_POST['asientos'];
$tipoTarjeta = $_POST['card-type'];
$marcaTarjeta = $_POST['card-brand'];
$ultimosDigitos = $_POST['last-digits'];
$fechaTransaccion = date('Y-m-d H:i:s');

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener información de la función
    $stmt = $pdo->prepare("
        SELECT 
            F.*, 
            P.nombre AS nombre_pelicula,
            P.imagen,
            C.nombre_cine,
            S.nombre AS nombre_sala
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

    // Insertar boletos y pagos
foreach ($asientos as $idButaca) {
    // 1. Insertar boleto
    $stmtBoleto = $pdo->prepare("
        INSERT INTO Boleto (RUT, IdFuncion, IdButaca, Estado_Butaca, Fecha_inicio_boleto, Fecha_fin_boleto, Activo)
        VALUES (:rut, :idFuncion, :idButaca, 'ocupada', :fechaInicio, :fechaFin, true)
    ");
    
    // Calcular fecha de fin (función + duración de la película)
    $fechaInicio = $funcion['fechahora'];
    $duracion = $funcion['duracion']; // Asegúrate de incluir 'duracion' en tu SELECT inicial
    $fechaFin = date('Y-m-d H:i:s', strtotime("+$duracion minutes", strtotime($fechaInicio)));
    
    $rutUsuario = $_SESSION['rut']; // Obtener RUT de la sesión
    
    $stmtBoleto->bindParam(':rut', $rutUsuario);
    $stmtBoleto->bindParam(':idFuncion', $idFuncion);
    $stmtBoleto->bindParam(':idButaca', $idButaca);
    $stmtBoleto->bindParam(':fechaInicio', $fechaInicio);
    $stmtBoleto->bindParam(':fechaFin', $fechaFin);
    $stmtBoleto->execute();
    
    // 2. Obtener ID del boleto insertado
    $idBoleto = $pdo->lastInsertId();
    
    // 3. Insertar pago asociado
    $stmtPago = $pdo->prepare("
        INSERT INTO Pago (IdBoleto, Tipo, Marca, CuatroDig, Fecha_Transf)
        VALUES (:idBoleto, :tipo, :marca, :digitos, :fecha)
    ");
    
    $stmtPago->bindParam(':idBoleto', $idBoleto);
    $stmtPago->bindParam(':tipo', $tipoTarjeta);
    $stmtPago->bindParam(':marca', $marcaTarjeta);
    $stmtPago->bindParam(':digitos', $ultimosDigitos);
    $stmtPago->bindParam(':fecha', $fechaTransaccion);
    $stmtPago->execute();
}
    
    // Obtener nombres de los asientos
    $asientosNombres = [];
    foreach ($asientos as $idButaca) {
        $stmt = $pdo->prepare("SELECT fila, columna FROM butaca WHERE id_butaca = :id");
        $stmt->bindParam(':id', $idButaca);
        $stmt->execute();
        $butaca = $stmt->fetch();
        $asientosNombres[] = $butaca['fila'] . $butaca['columna'];
    }
    
    // Calcular total
    $total = count($asientos) * 2500;
    
    // Guardar datos en sesión para mostrar en confirmación
    $_SESSION['compra'] = [
        'pelicula' => $funcion['nombre_pelicula'],
        'imagen' => $funcion['imagen'],
        'cine' => $funcion['nombre_cine'],
        'sala' => $funcion['nombre_sala'],
        'fecha' => date('d M Y', strtotime($funcion['fechahora'])),
        'hora' => date('H:i', strtotime($funcion['fechahora'])),
        'asientos' => $asientosNombres,
        'total' => $total,
        'tipoTarjeta' => $tipoTarjeta,
        'marcaTarjeta' => $marcaTarjeta,
        'ultimosDigitos' => $ultimosDigitos
    ];
    
    // Insertar boletos y pagos
    foreach ($asientos as $idButaca) {
        // ... (código existente para insertar boletos y pagos) ...
    }
    
    // Redirigir a página de confirmación
    header("Location: Detalles_compra.php");
    exit;
    
} catch (PDOException $e) {
    die("Error en el proceso de pago: " . $e->getMessage());
}