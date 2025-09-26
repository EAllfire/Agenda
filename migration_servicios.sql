-- Migration script to support multiple modalidades per service
-- and add descripcion and estado fields to servicios table

-- First, let's add the missing columns to servicios table
ALTER TABLE servicios ADD COLUMN descripcion TEXT AFTER nombre;
ALTER TABLE servicios ADD COLUMN estado ENUM('activo', 'inactivo') DEFAULT 'activo' AFTER descripcion;

-- Create the junction table for services and modalidades
CREATE TABLE servicios_modalidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT NOT NULL,
    modalidad_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_service_modalidad (servicio_id, modalidad_id),
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE,
    FOREIGN KEY (modalidad_id) REFERENCES modalidades(id) ON DELETE CASCADE
);

-- Migrate existing data from servicios to servicios_modalidades
INSERT INTO servicios_modalidades (servicio_id, modalidad_id)
SELECT id, modalidad_id FROM servicios WHERE modalidad_id IS NOT NULL;

-- Create a temporary table to consolidate services with the same name
CREATE TEMPORARY TABLE servicios_consolidados AS
SELECT 
    nombre,
    MIN(id) as keep_id,
    GROUP_CONCAT(id) as all_ids,
    GROUP_CONCAT(modalidad_id) as modalidad_ids
FROM servicios 
GROUP BY nombre;

-- Update servicios_modalidades to point to the consolidated service
UPDATE servicios_modalidades sm
JOIN servicios s ON sm.servicio_id = s.id
JOIN servicios_consolidados sc ON s.nombre = sc.nombre
SET sm.servicio_id = sc.keep_id
WHERE sm.servicio_id != sc.keep_id;

-- Delete duplicate services, keeping only the first one of each name
DELETE s FROM servicios s
JOIN servicios_consolidados sc ON s.nombre = sc.nombre
WHERE s.id != sc.keep_id;

-- Now we can safely drop the modalidad_id column from servicios
ALTER TABLE servicios DROP FOREIGN KEY servicios_ibfk_1;
ALTER TABLE servicios DROP COLUMN modalidad_id;

-- Update existing services to have active status and some descriptions
UPDATE servicios SET estado = 'activo', descripcion = CASE nombre
    WHEN 'Radiografía' THEN 'Estudio de imagen para diagnosticar fracturas y lesiones óseas'
    WHEN 'Resonancia Magnética' THEN 'Estudio avanzado de imagen con resonancia magnética'
    WHEN 'Tomografía' THEN 'Tomografía computarizada para diagnóstico detallado'
    WHEN 'Mastografía' THEN 'Estudio especializado para detección de cáncer de mama'
    WHEN 'Sonografía' THEN 'Ultrasonido diagnóstico no invasivo'
    WHEN 'Biometría Hemática' THEN 'Análisis completo de células sanguíneas'
    WHEN 'Química Sanguínea' THEN 'Análisis bioquímico de la sangre'
    WHEN 'Examen General de Orina' THEN 'Análisis completo de orina'
    WHEN 'Perfil Lipídico' THEN 'Análisis de colesterol y triglicéridos'
    WHEN 'Pruebas de Función Hepática' THEN 'Evaluación del funcionamiento del hígado'
    WHEN 'Pruebas de Función Renal' THEN 'Evaluación del funcionamiento de los riñones'
    WHEN 'Hormonal Tiroideo' THEN 'Análisis de hormonas tiroideas'
    WHEN 'Marcadores Tumorales' THEN 'Análisis para detección de marcadores de cáncer'
    ELSE 'Servicio médico especializado'
END;