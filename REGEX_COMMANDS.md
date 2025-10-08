# 📋 COMANDOS REGEX PARA VS CODE SEARCH & REPLACE

## FASE 1: Mapeos en consultas SQL (ejecutar primero)

### 1. SELECT con diagnostico
**Buscar:** `p\.diagnostico`
**Reemplazar por:** `p.alergias AS diagnostico`
**Archivos:** `**/*.php`
**Comentario:** Mapea la columna diagnostico local a alergias remoto con alias

### 2. INSERT con diagnostico
**Buscar:** `INSERT INTO portal_pacientes \([^)]*\)diagnostico([^)]*\)`
**Reemplazar por:** `INSERT INTO portal_pacientes ($1)alergias$2`
**Archivos:** `**/*.php`
**Comentario:** Cambia la columna en INSERT statements

### 3. SELECT con modalidad_id en portal_servicios
**Buscar:** `s\.modalidad_id`
**Reemplazar por:** `s.modalidad AS modalidad_id`
**Archivos:** `**/*.php`
**Comentario:** Mapea modalidad_id local a modalidad remoto con alias

### 4. WHERE con modalidad_id en portal_servicios
**Buscar:** `WHERE modalidad_id =`
**Reemplazar por:** `WHERE modalidad =`
**Archivos:** `**/*.php`
**Comentario:** Actualiza condiciones WHERE

### 5. SELECT con duracion_minutos
**Buscar:** `s\.duracion_minutos`
**Reemplazar por:** `s.duracion AS duracion_minutos`
**Archivos:** `**/*.php`
**Comentario:** Mapea duracion_minutos local a duracion remoto con alias

### 6. INSERT/UPDATE con modalidad_id en portal_servicios
**Buscar:** `modalidad_id = \?`
**Reemplazar por:** `modalidad = ?`
**Archivos:** `**/*.php`
**Comentario:** Actualiza parámetros en operaciones de escritura

### 7. INSERT/UPDATE con duracion_minutos
**Buscar:** `duracion_minutos = \?`
**Reemplazar por:** `duracion = ?`
**Archivos:** `**/*.php`
**Comentario:** Actualiza parámetros de duración

## FASE 2: Columnas en queries específicas

### 8. JOIN con modalidad_id
**Buscar:** `ON s\.modalidad_id = m\.id`
**Reemplazar por:** `ON s.modalidad = m.id`
**Archivos:** `**/*.php`
**Comentario:** Actualiza JOINs entre servicios y modalidades

### 9. INSERT columns list - modalidad_id
**Buscar:** `\(([^)]*,\s*)modalidad_id(\s*,[^)]*)\)`
**Reemplazar por:** `($1modalidad$2)`
**Archivos:** `**/*.php`
**Comentario:** Actualiza lista de columnas en INSERT

### 10. INSERT columns list - duracion_minutos
**Buscar:** `\(([^)]*,\s*)duracion_minutos(\s*,[^)]*)\)`
**Reemplazar por:** `($1duracion$2)`
**Archivos:** `**/*.php`
**Comentario:** Actualiza lista de columnas en INSERT

## FASE 3: Referencias 'price' a 'precio' (si existen)

### 11. Columna price en portal_servicios
**Buscar:** `portal_servicios\.price`
**Reemplazar por:** `portal_servicios.precio`
**Archivos:** `**/*.php`
**Comentario:** Actualiza referencias de precio

### 12. SELECT price
**Buscar:** `SELECT ([^,]*,\s*)price(\s*,[^,]*)`
**Reemplazar por:** `SELECT $1precio$2`
**Archivos:** `**/*.php`
**Comentario:** Actualiza SELECT con precio

## VERIFICACIÓN MANUAL REQUERIDA

### Variables JavaScript/PHP que mantengan compatibilidad:
- Variables `duracion_minutos` en JavaScript pueden mantenerse para compatibilidad
- Variables `modalidad_id` en formularios HTML pueden mantenerse
- Solo cambiar las consultas SQL directas

### Archivos que requieren revisión manual:
- `index.php` (líneas 1716, 1723, 2515) - Variables JavaScript
- `catalogo_servicios.php` - Formularios HTML
- Cualquier archivo con lógica de negocio compleja

## ORDEN DE EJECUCIÓN RECOMENDADO:
1. Ejecutar FASE 1 (consultas SQL)
2. Ejecutar FASE 2 (estructura de queries)
3. Revisar manualmente archivos con variables JavaScript
4. Ejecutar tests de validación
5. Verificar funcionamiento completo