<?php
require_once('includes/db.php');
require_once('includes/GestorPagos.php');

header('Content-Type: application/json');

$cita_id = $_POST['cita_id'] ?? null;
$proveedor = $_POST['proveedor'] ?? 'simulador';
$metodo_pago = $_POST['metodo_pago'] ?? 'tarjeta';

try {
    if (!$cita_id) {
        throw new Exception('ID de cita requerido');
    }

    // Verificar que la cita existe y necesita pago
    $stmt = $conn->prepare("SELECT id, estado_pago FROM citas WHERE id = ?");
    $stmt->bind_param("i", $cita_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Cita no encontrada');
    }

    $cita = $result->fetch_assoc();
    
    if ($cita['estado_pago'] === 'completado') {
        throw new Exception('Esta cita ya ha sido pagada');
    }

    // Crear pago usando el gestor
    $gestorPagos = new GestorPagos($conn);
    $resultado = $gestorPagos->crearPago($cita_id, $proveedor, $metodo_pago);

    if ($resultado['success']) {
        echo json_encode($resultado);
    } else {
        throw new Exception($resultado['error'] ?? 'Error creando pago');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>