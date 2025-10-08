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
    // Consulta simple para obtener servicios con duración
    $sql = "SELECT 
                s.id,
                s.nombre,
                COALESCE(s.descripcion, '') as descripcion,
                COALESCE(s.precio, 0) as precio,
                COALESCE(s.modalidad_id, 0) as modalidad_id,
                COALESCE(s.duracion_minutos, 30) as duracion_minutos,
                COALESCE(m.nombre, 'Sin modalidad') as modalidad_nombre
            FROM agenda_servicios s
            LEFT JOIN agenda_modalidades m ON s.modalidad_id = m.id
            ORDER BY s.nombre ASC, s.id ASC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    
    $servicios = [];
    while ($row = $result->fetch_assoc()) {
        $servicios[] = [
            'id' => (int)$row['id'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'precio' => (float)$row['precio'],
            'modalidad_id' => (int)$row['modalidad_id'],
            'modalidad_nombre' => $row['modalidad_nombre'],
            'duracion_minutos' => (int)$row['duracion_minutos']
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($servicios);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>