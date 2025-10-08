<?php
/**
 * Sistema de Pagos Modular para Hospital Angeles
 * Permite integrar múltiples proveedores de pago
 */

abstract class ProveedorPago {
    protected $configuracion;
    protected $conn;
    
    public function __construct($configuracion, $conn) {
        $this->configuracion = $configuracion;
        $this->conn = $conn;
    }
    
    // Métodos abstractos que cada proveedor debe implementar
    abstract public function crearPago($datos);
    abstract public function procesarWebhook($payload);
    abstract public function consultarEstado($referencia);
    abstract public function cancelarPago($referencia);
    
    // Método común para guardar pago en BD
    protected function guardarPago($cita_id, $monto, $metodo, $proveedor, $referencia = null, $estado = 'pendiente', $datos = null) {
        $stmt = $this->conn->prepare("
            INSERT INTO pagos (cita_id, monto, metodo_pago, proveedor_pago, referencia_externa, estado_pago, datos_transaccion) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $datos_json = $datos ? json_encode($datos) : null;
        $stmt->bind_param("idsssss", $cita_id, $monto, $metodo, $proveedor, $referencia, $estado, $datos_json);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    // Método común para actualizar estado de pago
    protected function actualizarEstadoPago($pago_id, $estado, $referencia = null, $datos = null) {
        $sql = "UPDATE pagos SET estado_pago = ?, fecha_actualizacion = NOW()";
        $params = [$estado];
        $types = "s";
        
        if ($referencia) {
            $sql .= ", referencia_externa = ?";
            $params[] = $referencia;
            $types .= "s";
        }
        
        if ($datos) {
            $sql .= ", datos_transaccion = ?";
            $params[] = json_encode($datos);
            $types .= "s";
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $pago_id;
        $types .= "i";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        return $stmt->execute();
    }
}

/**
 * Factory para crear proveedores de pago
 */
class PagoFactory {
    private static $conn;
    
    public static function setConnection($conn) {
        self::$conn = $conn;
    }
    
    public static function crear($proveedor) {
        // Obtener configuración del proveedor
        $stmt = self::$conn->prepare("SELECT configuracion FROM proveedores_pago WHERE nombre = ? AND habilitado = TRUE");
        $stmt->bind_param("s", $proveedor);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Proveedor de pago no disponible: " . $proveedor);
        }
        
        $row = $result->fetch_assoc();
        $configuracion = json_decode($row['configuracion'], true);
        
        // Crear instancia del proveedor específico
        switch ($proveedor) {
            case 'simulador':
                require_once 'pagos/SimuladorProvider.php';
                return new SimuladorProvider($configuracion, self::$conn);
                
            case 'stripe':
                require_once 'pagos/StripeProvider.php';
                return new StripeProvider($configuracion, self::$conn);
                
            case 'paypal':
                require_once 'pagos/PayPalProvider.php';
                return new PayPalProvider($configuracion, self::$conn);
                
            case 'mercadopago':
                require_once 'pagos/MercadoPagoProvider.php';
                return new MercadoPagoProvider($configuracion, self::$conn);
                
            case 'conekta':
                require_once 'pagos/ConektaProvider.php';
                return new ConektaProvider($configuracion, self::$conn);
                
            default:
                throw new Exception("Proveedor de pago no soportado: " . $proveedor);
        }
    }
}

/**
 * Gestor principal de pagos
 */
class GestorPagos {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
        PagoFactory::setConnection($conn);
    }
    
    public function obtenerProveedoresDisponibles() {
        $stmt = $this->conn->prepare("SELECT nombre FROM proveedores_pago WHERE habilitado = TRUE");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $proveedores = [];
        while ($row = $result->fetch_assoc()) {
            $proveedores[] = $row['nombre'];
        }
        
        return $proveedores;
    }
    
    public function calcularMontoCita($cita_id) {
        // Obtener información de la cita y servicio
        $stmt = $this->conn->prepare("
            SELECT c.tipo, s.precio, c.nota_paciente
            FROM agenda_citas c
            LEFT JOIN agenda_servicios s ON c.servicio_id = s.id
            WHERE c.id = ?
        ");
        $stmt->bind_param("i", $cita_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Cita no encontrada");
        }
        
        $cita = $result->fetch_assoc();
        
        // Si es un paquete, calcular precio especial
        if ($cita['tipo'] === 'paquete') {
            return $this->calcularPrecioPaquete($cita['nota_paciente']);
        }
        
        return floatval($cita['precio']) ?: 0.00;
    }
    
    private function calcularPrecioPaquete($nota_paciente) {
        // Extraer tipo de paquete de las notas
        if (strpos($nota_paciente, 'basico') !== false) {
            return 2500.00;
        } elseif (strpos($nota_paciente, 'completo') !== false) {
            return 7500.00;
        } elseif (strpos($nota_paciente, 'premium') !== false) {
            return 12500.00;
        }
        
        return 5000.00; // Precio por defecto
    }
    
    public function crearPago($cita_id, $proveedor, $metodo_pago = 'tarjeta') {
        try {
            $monto = $this->calcularMontoCita($cita_id);
            
            if ($monto <= 0) {
                throw new Exception("Monto inválido para la cita");
            }
            
            $proveedorPago = PagoFactory::crear($proveedor);
            
            $datos_pago = [
                'cita_id' => $cita_id,
                'monto' => $monto,
                'moneda' => 'MXN',
                'metodo' => $metodo_pago,
                'descripcion' => "Pago de cita médica #" . $cita_id
            ];
            
            return $proveedorPago->crearPago($datos_pago);
            
        } catch (Exception $e) {
            error_log("Error creando pago: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function procesarWebhook($proveedor, $payload) {
        try {
            $proveedorPago = PagoFactory::crear($proveedor);
            return $proveedorPago->procesarWebhook($payload);
        } catch (Exception $e) {
            error_log("Error procesando webhook: " . $e->getMessage());
            return false;
        }
    }
}
?>