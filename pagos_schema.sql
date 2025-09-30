-- Script SQL para agregar funcionalidad de pagos
-- Ejecutar este script en la base de datos agenda_hospital

-- Tabla para almacenar información de pagos
CREATE TABLE IF NOT EXISTS pagos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cita_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    moneda VARCHAR(3) DEFAULT 'MXN',
    metodo_pago VARCHAR(50) NOT NULL,
    proveedor_pago VARCHAR(50) NOT NULL,
    referencia_externa VARCHAR(255),
    estado_pago ENUM('pendiente', 'procesando', 'completado', 'fallido', 'cancelado', 'reembolsado') DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    datos_transaccion JSON,
    notas TEXT,
    FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE CASCADE,
    INDEX idx_cita_id (cita_id),
    INDEX idx_estado_pago (estado_pago),
    INDEX idx_referencia_externa (referencia_externa)
);

-- Agregar campo de monto a la tabla servicios si no existe
ALTER TABLE servicios 
ADD COLUMN IF NOT EXISTS precio DECIMAL(8,2) DEFAULT 0.00 AFTER duracion_minutos;

-- Actualizar algunos precios de ejemplo (ajustar según tus precios reales)
UPDATE servicios SET precio = 1200.00 WHERE nombre LIKE '%Radiografía%';
UPDATE servicios SET precio = 3500.00 WHERE nombre LIKE '%Resonancia%';
UPDATE servicios SET precio = 2800.00 WHERE nombre LIKE '%Tomografía%';
UPDATE servicios SET precio = 1800.00 WHERE nombre LIKE '%Ultrasonido%' OR nombre LIKE '%Sonografía%';
UPDATE servicios SET precio = 1500.00 WHERE nombre LIKE '%Mastografía%';
UPDATE servicios SET precio = 800.00 WHERE nombre LIKE '%Laboratorio%';

-- Tabla para configuración de proveedores de pago
CREATE TABLE IF NOT EXISTS proveedores_pago (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    habilitado BOOLEAN DEFAULT TRUE,
    configuracion JSON,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar proveedores de pago por defecto (deshabilitados hasta configurar)
INSERT IGNORE INTO proveedores_pago (nombre, habilitado, configuracion) VALUES
('simulador', TRUE, '{"modo": "test", "auto_approve": true}'),
('stripe', FALSE, '{"public_key": "", "secret_key": "", "webhook_secret": ""}'),
('paypal', FALSE, '{"client_id": "", "client_secret": "", "mode": "sandbox"}'),
('mercadopago', FALSE, '{"public_key": "", "access_token": ""}'),
('conekta', FALSE, '{"public_key": "", "private_key": ""}'),
('openpay', FALSE, '{"merchant_id": "", "public_key": "", "private_key": ""}');

-- Agregar columna de estado de pago a citas
ALTER TABLE citas 
ADD COLUMN IF NOT EXISTS pago_requerido BOOLEAN DEFAULT TRUE AFTER tipo,
ADD COLUMN IF NOT EXISTS estado_pago ENUM('no_requerido', 'pendiente', 'completado') DEFAULT 'pendiente' AFTER pago_requerido;