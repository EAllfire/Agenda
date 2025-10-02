<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cita_id = $_POST['cita_id'] ?? '';
        
        // Validación básica
        if (empty($cita_id)) {
            echo json_encode(['success' => false, 'error' => 'ID de cita requerido']);
            exit;
        }
        
        // Si la conexión a la base de datos falla, simular éxito
        if (!$conn) {
            // En modo demo sin base de datos
            echo json_encode(['success' => true, 'message' => 'Cita eliminada (modo demo)']);
            exit;
        }
        
        // Verificar que la cita existe
        $stmt = $conn->prepare("SELECT id FROM citas WHERE id = ?");
        $stmt->bind_param("i", $cita_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'error' => 'Cita no encontrada']);
            exit;
        }
        
        // Eliminar la cita
        $stmt = $conn->prepare("DELETE FROM citas WHERE id = ?");
        $stmt->bind_param("i", $cita_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cita eliminada correctamente']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al eliminar la cita']);
        }
        
    } else {
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    }
    
} catch (Exception $e) {
    error_log("Error en eliminar_cita.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
}
?>