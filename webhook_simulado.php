<?php
require_once('includes/db.php');
require_once('includes/GestorPagos.php');

header('Content-Type: application/json');

// Obtener payload del webhook
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

error_log('Webhook simulado recibido: ' . $payload);

try {
    if (!$data || !isset($data['referencia']) || !isset($data['estado'])) {
        throw new Exception('Datos de webhook inválidos');
    }

    // Procesar webhook usando el gestor de pagos
    $gestorPagos = new GestorPagos($conn);
    $resultado = $gestorPagos->procesarWebhook('simulador', $payload);

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Webhook procesado correctamente'
        ]);
    } else {
        throw new Exception('Error procesando webhook');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>