<?php
include_once("../../../CONNECTION/conexion.php");

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$butacas_disponibles = [];
$mensaje = null;
$detalles_butacas = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cargar_butacas'])) {
        $id_boleto = $_POST['id_boleto'];

        // Obtener información completa del boleto
        $stmt = $conn->prepare("
            SELECT 
                b.IdButaca, 
                f.IdFuncion,
                f.Id_Sala,
                p.nombre AS nombre_pelicula,
                s.nombre AS nombre_sala,
                c.nombre_cine,
                f.fechahora
            FROM Boleto b
            JOIN Funcion f ON b.IdFuncion = f.IdFuncion
            JOIN Pelicula p ON f.id_pelicula = p.idpelicula
            JOIN Sala s ON f.id_sala = s.idsala
            JOIN Cine c ON s.cine_idcine = c.idcine
            WHERE b.Id_Boleto = :id_boleto AND b.Activo = true
        ");
        $stmt->bindParam(':id_boleto', $id_boleto);
        $stmt->execute();
        $boleto_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($boleto_info) {
            $id_sala = $boleto_info['id_sala'];
            $id_funcion = $boleto_info['idfuncion'];
            $butaca_actual = $boleto_info['idbutaca'];

            // Obtener todas las butacas de la sala con su estado
            $stmt = $conn->prepare("
                SELECT 
                    b.Id_Butaca, 
                    b.Fila, 
                    b.Columna,
                    CASE 
                        WHEN b.Id_Butaca = :butaca_actual THEN 'actual'
                        WHEN EXISTS (
                            SELECT 1 
                            FROM Boleto 
                            WHERE IdButaca = b.Id_Butaca 
                                AND Activo = true
                                AND IdFuncion = :id_funcion
                        ) THEN 'ocupada'
                        ELSE 'disponible'
                    END AS estado
                FROM Butaca b
                WHERE b.Id_Sala = :id_sala
                ORDER BY b.Fila, b.Columna
            ");
            $stmt->bindParam(':id_sala', $id_sala);
            $stmt->bindParam(':id_funcion', $id_funcion);
            $stmt->bindParam(':butaca_actual', $butaca_actual);
            $stmt->execute();
            $butacas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Guardar datos necesarios en sesión
            $_SESSION['butacas_data'] = [
                'butacas' => $butacas,
                'id_boleto' => $id_boleto,
                'funcion_info' => [
                    'nombre_pelicula' => $boleto_info['nombre_pelicula'],
                    'nombre_sala' => $boleto_info['nombre_sala'],
                    'nombre_cine' => $boleto_info['nombre_cine'],
                    'fechahora' => $boleto_info['fechahora']
                ],
                'butaca_actual' => $butaca_actual
            ];
        } else {
            $mensaje = "El boleto no existe o no está activo.";
        }
    }

    if (isset($_POST['actualizar_butaca'])) {
        $id_boleto = $_POST['id_boleto'];
        $nueva_butaca = $_POST['nueva_butaca'];

        try {
            $conn->beginTransaction();

            // Obtener información del boleto
            $stmt = $conn->prepare("
                SELECT 
                    b.IdButaca, 
                    f.Id_Sala
                FROM Boleto b
                JOIN Funcion f ON b.IdFuncion = f.IdFuncion
                WHERE b.Id_Boleto = :id_boleto AND b.Activo = true
            ");
            $stmt->bindParam(':id_boleto', $id_boleto);
            $stmt->execute();
            $boleto_info = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($boleto_info) {
                $butaca_actual = $boleto_info['idbutaca'];
                $id_sala_actual = $boleto_info['id_sala'];

                // Verificar que la nueva butaca pertenece a la misma sala
                $stmt = $conn->prepare("SELECT Id_Sala FROM Butaca WHERE Id_Butaca = :nueva_butaca");
                $stmt->bindParam(':nueva_butaca', $nueva_butaca);
                $stmt->execute();
                $id_sala_nueva = $stmt->fetchColumn();

                if ($id_sala_nueva == $id_sala_actual) {
                    // Verificar que la nueva butaca no esté ocupada
                    $stmt = $conn->prepare("
                        SELECT COUNT(*) 
                        FROM Boleto 
                        WHERE IdButaca = :nueva_butaca 
                        AND Activo = true
                        AND IdFuncion = (SELECT IdFuncion FROM Boleto WHERE Id_Boleto = :id_boleto)
                    ");
                    $stmt->bindParam(':nueva_butaca', $nueva_butaca);
                    $stmt->bindParam(':id_boleto', $id_boleto);
                    $stmt->execute();
                    $ocupada = $stmt->fetchColumn();

                    if ($ocupada == 0) {
                        // Actualizar la butaca del boleto
                        $stmt = $conn->prepare("
                            UPDATE Boleto 
                            SET IdButaca = :nueva_butaca 
                            WHERE Id_Boleto = :id_boleto
                        ");
                        $stmt->bindParam(':nueva_butaca', $nueva_butaca);
                        $stmt->bindParam(':id_boleto', $id_boleto);
                        $stmt->execute();

                        // Obtener detalles de las butacas
                        $stmt = $conn->prepare("
                            SELECT Id_Butaca, Fila, Columna 
                            FROM Butaca 
                            WHERE Id_Butaca IN (:butaca_actual, :nueva_butaca)
                        ");
                        $stmt->bindValue(':butaca_actual', $butaca_actual);
                        $stmt->bindValue(':nueva_butaca', $nueva_butaca);
                        $stmt->execute();
                        $detalles_butacas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        $conn->commit();
                        $mensaje = "Butaca actualizada correctamente.";
                    } else {
                        $conn->rollBack();
                        $mensaje = "La nueva butaca ya está ocupada.";
                    }
                } else {
                    $conn->rollBack();
                    $mensaje = "La nueva butaca no pertenece a la misma sala.";
                }
            } else {
                $conn->rollBack();
                $mensaje = "El boleto no existe o no está activo.";
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            $mensaje = "Error al procesar la solicitud: " . $e->getMessage();
        }
    }
}
?>