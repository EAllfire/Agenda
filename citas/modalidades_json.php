<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Verificar que el usuario sea admin
if (!puedeRealizar('gestionar_usuarios')) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

try {
    $sql = "SELECT id, nombre FROM agenda_modalidades ORDER BY nombre ASC";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    
    $modalidades = [];
    while ($row = $result->fetch_assoc()) {
        $modalidades[] = [
            'id' => (int)$row['id'],
            'nombre' => $row['nombre']
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($modalidades);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>