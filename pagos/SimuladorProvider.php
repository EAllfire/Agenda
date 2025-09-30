<?php
/**
 * Proveedor de prueba para simular pagos
 * Útil para desarrollo y testing antes de implementar proveedores reales
 */

require_once '../includes/GestorPagos.php';

class SimuladorProvider extends ProveedorPago {
    
    public function crearPago($datos) {
        try {
            // Simular creación de pago
            $referencia = 'SIM_' . uniqid() . '_' . time();
            
            // Guardar pago en BD
            $pago_id = $this->guardarPago(
                $datos['cita_id'],
                $datos['monto'],
                $datos['metodo'],
                'simulador',
                $referencia,
                'pendiente',
                [
                    'descripcion' => $datos['descripcion'],
                    'moneda' => $datos['moneda']
                ]
            );
            
            if (!$pago_id) {
                throw new Exception("Error al guardar pago en base de datos");
            }
            
            // Simular respuesta del proveedor
            return [
                'success' => true,
                'pago_id' => $pago_id,
                'referencia' => $referencia,
                'url_pago' => "pago_simulado.php?ref=" . $referencia,
                'estado' => 'pendiente',
                'monto' => $datos['monto'],
                'moneda' => $datos['moneda']
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function procesarWebhook($payload) {
        // Simular procesamiento de webhook
        $data = json_decode($payload, true);
        
        if (isset($data['referencia']) && isset($data['estado'])) {
            // Buscar pago por referencia
            $stmt = $this->conn->prepare("SELECT id, cita_id FROM pagos WHERE referencia_externa = ?");
            $stmt->bind_param("s", $data['referencia']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $pago = $result->fetch_assoc();
                
                // Actualizar estado del pago
                $this->actualizarEstadoPago($pago['id'], $data['estado'], null, $data);
                
                // Si el pago está completado, actualizar la cita
                if ($data['estado'] === 'completado') {
                    $this->actualizarEstadoCita($pago['cita_id'], 'completado');
                }
                
                return true;
            }
        }
        
        return false;
    }
    
    public function consultarEstado($referencia) {
        $stmt = $this->conn->prepare("SELECT estado_pago FROM pagos WHERE referencia_externa = ?");
        $stmt->bind_param("s", $referencia);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $pago = $result->fetch_assoc();
            return $pago['estado_pago'];
        }
        
        return null;
    }
    
    public function cancelarPago($referencia) {
        $stmt = $this->conn->prepare("SELECT id FROM pagos WHERE referencia_externa = ?");
        $stmt->bind_param("s", $referencia);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $pago = $result->fetch_assoc();
            return $this->actualizarEstadoPago($pago['id'], 'cancelado');
        }
        
        return false;
    }
    
    private function actualizarEstadoCita($cita_id, $estado_pago) {
        $stmt = $this->conn->prepare("UPDATE citas SET estado_pago = ? WHERE id = ?");
        $stmt->bind_param("si", $estado_pago, $cita_id);
        return $stmt->execute();
    }
}
?>