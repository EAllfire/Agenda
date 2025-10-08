-- Migración: Renombrar agenda_servicios → portal_servicios y agenda_pacientes → portal_pacientes
-- Fecha: 7 de octubre de 2025
-- Descripción: Cambio de nombres específicos para servicios y pacientes al portal

-- Desactivar verificación de claves foráneas para permitir el renombrado
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Renombrar agenda_servicios a portal_servicios
RENAME TABLE agenda_servicios TO portal_servicios;

-- 2. Renombrar agenda_pacientes a portal_pacientes  
RENAME TABLE agenda_pacientes TO portal_pacientes;

-- Reactivar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- Verificar que las tablas se renombraron correctamente
SHOW TABLES LIKE 'portal_%';