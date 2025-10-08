-- Script de migración para agregar prefijo 'agenda_' a todas las tablas
-- Ejecutar después de hacer backup de la base de datos

USE agenda_hospital;

-- Deshabilitar verificación de foreign keys temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Renombrar todas las tablas agregando el prefijo 'agenda_'
RENAME TABLE usuarios TO agenda_usuarios;
RENAME TABLE pacientes TO agenda_pacientes;
RENAME TABLE profesionales TO agenda_profesionales;
RENAME TABLE modalidades TO agenda_modalidades;
RENAME TABLE servicios TO agenda_servicios;
RENAME TABLE estado_cita TO agenda_estado_cita;
RENAME TABLE citas TO agenda_citas;
RENAME TABLE paquetes TO agenda_paquetes;
RENAME TABLE mensajes TO agenda_mensajes;
RENAME TABLE ventas_servicios TO agenda_ventas_servicios;

-- Si existen tablas de pagos, también renombrarlas
-- RENAME TABLE pagos TO agenda_pagos;
-- RENAME TABLE proveedores_pago TO agenda_proveedores_pago;

-- Reactivar verificación de foreign keys
SET FOREIGN_KEY_CHECKS = 1;

-- Verificar que las tablas fueron renombradas correctamente
SHOW TABLES;

-- Verificar que las foreign keys siguen funcionando
DESCRIBE agenda_citas;
DESCRIBE agenda_ventas_servicios;
DESCRIBE agenda_ventas_paquetes;

SELECT 'Migración completada exitosamente. Todas las tablas ahora tienen el prefijo agenda_' as STATUS;