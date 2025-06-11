<?php
include_once("../../CONNECTION/conexion.php");

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

        $stmt = $conn->prepare("SELECT IdButaca FROM Boleto WHERE Id_Boleto = :id_boleto AND Activo = true");
        $stmt->bindParam(':id_boleto', $id_boleto);
        $stmt->execute();
        $butaca_actual = $stmt->fetchColumn();

        if ($butaca_actual) {
            $stmt = $conn->prepare("SELECT Id_Sala FROM Butaca WHERE Id_Butaca = :id_butaca");
            $stmt->bindParam(':id_butaca', $butaca_actual);
            $stmt->execute();
            $id_sala = $stmt->fetchColumn();

            if ($id_sala) {
                $stmt = $conn->prepare("
                    SELECT b.Id_Butaca, b.Fila, b.Columna
                    FROM Butaca b
                    WHERE b.Id_Sala = :id_sala
                    AND b.Id_Butaca NOT IN (
                        SELECT IdButaca FROM Boleto WHERE Activo = true
                    )
                    ORDER BY b.Fila, b.Columna
                ");
                $stmt->bindParam(':id_sala', $id_sala);
                $stmt->execute();
                $butacas_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $mensaje = "No se pudo obtener la sala de la butaca actual.";
            }
        } else {
            $mensaje = "El boleto no existe o no está activo.";
        }
    }

    if (isset($_POST['actualizar_butaca'])) {
        $id_boleto = $_POST['id_boleto'];
        $nueva_butaca = $_POST['nueva_butaca'];

        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("SELECT IdButaca, IdPelicula FROM Boleto WHERE Id_Boleto = :id_boleto AND Activo = true");
            $stmt->bindParam(':id_boleto', $id_boleto);
            $stmt->execute();
            $boleto_info = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($boleto_info) {
                $butaca_actual = $boleto_info['idbutaca'];

                $stmt = $conn->prepare("SELECT Id_Sala FROM Butaca WHERE Id_Butaca = :butaca_actual");
                $stmt->bindParam(':butaca_actual', $butaca_actual);
                $stmt->execute();
                $sala_actual = $stmt->fetchColumn();

                if ($sala_actual) {
                    $stmt = $conn->prepare("SELECT Id_Sala FROM Butaca WHERE Id_Butaca = :nueva_butaca");
                    $stmt->bindParam(':nueva_butaca', $nueva_butaca);
                    $stmt->execute();
                    $sala_nueva = $stmt->fetchColumn();

                    if ($sala_nueva && $sala_nueva == $sala_actual) {
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM Boleto WHERE IdButaca = :nueva_butaca AND Activo = true");
                        $stmt->bindParam(':nueva_butaca', $nueva_butaca);
                        $stmt->execute();
                        $butaca_ocupada = $stmt->fetchColumn();

                        if ($butaca_ocupada == 0) {
                            $stmt = $conn->prepare("UPDATE Boleto SET IdButaca = :nueva_butaca WHERE Id_Boleto = :id_boleto");
                            $stmt->bindParam(':nueva_butaca', $nueva_butaca);
                            $stmt->bindParam(':id_boleto', $id_boleto);
                            $stmt->execute();

                            $stmt = $conn->prepare("SELECT Id_Butaca, Fila, Columna FROM Butaca WHERE Id_Butaca = :butaca_actual OR Id_Butaca = :nueva_butaca");
                            $stmt->bindParam(':butaca_actual', $butaca_actual);
                            $stmt->bindParam(':nueva_butaca', $nueva_butaca);
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
                    $mensaje = "Error al obtener la sala de la butaca actual.";
                }
            } else {
                $conn->rollBack();
                $mensaje = "El boleto no existe o no está activo.";
            }
        } catch(PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $mensaje = "Error al procesar la solicitud: " . $e->getMessage();
        }
    }
}
?>
