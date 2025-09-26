-- Script para agregar campos de rango de horas a la tabla servicios
ALTER TABLE servicios ADD COLUMN hora_inicio TIME DEFAULT NULL AFTER precio;
ALTER TABLE servicios ADD COLUMN hora_fin TIME DEFAULT NULL AFTER hora_inicio;

-- Actualizar servicios existentes con horarios predeterminados
UPDATE servicios SET 
    hora_inicio = '08:00:00',
    hora_fin = '18:00:00'
WHERE hora_inicio IS NULL OR hora_fin IS NULL;