-- Script para cambiar de rango de horas a duración en minutos
-- Eliminar la columna hora_fin y cambiar hora_inicio a duracion_minutos

-- Paso 1: Eliminar la columna hora_fin
ALTER TABLE servicios DROP COLUMN hora_fin;

-- Paso 2: Cambiar hora_inicio a duracion_minutos (en minutos)
ALTER TABLE servicios CHANGE COLUMN hora_inicio duracion_minutos INT DEFAULT 30 COMMENT 'Duración del servicio en minutos';

-- Paso 3: Asignar duraciones aleatorias y descripciones realistas para cada servicio
UPDATE servicios SET 
    duracion_minutos = CASE 
        WHEN nombre = 'Radiografía' THEN 15
        WHEN nombre = 'Resonancia Magnética' THEN 45
        WHEN nombre = 'Tomografía' THEN 30
        WHEN nombre = 'Rayos X' THEN 10
        WHEN nombre = 'Mamografía' THEN 20
        WHEN nombre = 'Hemograma' THEN 5
        WHEN nombre = 'Glucosa' THEN 5
        WHEN nombre = 'Ultrasonido abdominal' THEN 25
        WHEN nombre = 'Ultrasonido pélvico' THEN 25
        WHEN nombre = 'TAC de tórax' THEN 30
        WHEN nombre = 'TAC de abdomen' THEN 35
        WHEN nombre = 'Resonancia cerebral' THEN 50
        WHEN nombre = 'Resonancia columna' THEN 45
        WHEN nombre = 'Perfil Lipídico' THEN 5
        WHEN nombre = 'Química Sanguínea' THEN 8
        ELSE 20 -- Duración por defecto
    END,
    descripcion = CASE 
        WHEN nombre = 'Radiografía' THEN 'Estudio radiográfico para evaluación de estructuras óseas y tejidos blandos'
        WHEN nombre = 'Resonancia Magnética' THEN 'Estudio avanzado de resonancia magnética nuclear con imágenes de alta resolución'
        WHEN nombre = 'Tomografía' THEN 'Tomografía computarizada con contraste para diagnóstico detallado'
        WHEN nombre = 'Rayos X' THEN 'Radiografía simple para evaluación inicial de lesiones óseas'
        WHEN nombre = 'Mamografía' THEN 'Mastografía digital para detección temprana de cáncer de mama'
        WHEN nombre = 'Hemograma' THEN 'Biometría hemática completa con conteo diferencial de células'
        WHEN nombre = 'Glucosa' THEN 'Determinación de glucosa sérica en ayunas'
        WHEN nombre = 'Ultrasonido abdominal' THEN 'Ultrasonografía abdominal completa para evaluación de órganos internos'
        WHEN nombre = 'Ultrasonido pélvico' THEN 'Ultrasonografía pélvica transvaginal y transabdominal'
        WHEN nombre = 'TAC de tórax' THEN 'Tomografía computarizada de tórax con y sin contraste'
        WHEN nombre = 'TAC de abdomen' THEN 'Tomografía computarizada abdominal con contraste oral e intravenoso'
        WHEN nombre = 'Resonancia cerebral' THEN 'Resonancia magnética nuclear cerebral con secuencias especializadas'
        WHEN nombre = 'Resonancia columna' THEN 'Resonancia magnética de columna vertebral completa'
        WHEN nombre = 'Perfil Lipídico' THEN 'Análisis completo de lípidos: colesterol total, HDL, LDL y triglicéridos'
        WHEN nombre = 'Química Sanguínea' THEN 'Panel metabólico básico: glucosa, urea, creatinina, electrolitos'
        ELSE CONCAT('Estudio especializado: ', nombre)
    END
WHERE duracion_minutos IS NULL OR descripcion IS NULL OR descripcion = '';