#!/bin/bash

# Script para actualizar TODOS los nombres de tablas incluyendo JOINs
echo "🔄 Iniciando corrección completa de nombres de tablas..."

# Función mejorada para hacer reemplazos completos
update_all_table_references() {
    echo "📝 Actualizando: $1"
    
    # Reemplazar en cláusulas FROM, INTO, UPDATE, DELETE, JOIN
    sed -i '' 's/\bpacientes\b/agenda_pacientes/g' "$1"
    sed -i '' 's/\busuarios\b/agenda_usuarios/g' "$1"
    sed -i '' 's/\bprofesionales\b/agenda_profesionales/g' "$1"
    sed -i '' 's/\bservicios\b/agenda_servicios/g' "$1"
    sed -i '' 's/\bmodalidades\b/agenda_modalidades/g' "$1"
    sed -i '' 's/\bcitas\b/agenda_citas/g' "$1"
    sed -i '' 's/\bestado_cita\b/agenda_estado_cita/g' "$1"
    sed -i '' 's/\bpaquetes\b/agenda_paquetes/g' "$1"
    sed -i '' 's/\bmensajes\b/agenda_mensajes/g' "$1"
    sed -i '' 's/\bventas_servicios\b/agenda_ventas_servicios/g' "$1"
}

# Actualizar archivos principales
echo "🎯 Actualizando archivos principales..."
update_all_table_references "index.php"
update_all_table_references "login.php"
update_all_table_references "registro.php"
update_all_table_references "admin_usuarios.php"
update_all_table_references "catalogo_servicios.php"
update_all_table_references "actualizar_cita.php"
update_all_table_references "eliminar_cita.php"
update_all_table_references "guardar_cita.php"
update_all_table_references "guardar_paciente.php"
update_all_table_references "actualizar_estado.php"
update_all_table_references "servicios_por_modalidad.php"
update_all_table_references "servicios_con_duracion.php"
update_all_table_references "citas_json.php"
update_all_table_references "recursos_json.php"
update_all_table_references "estados_json.php"
update_all_table_references "pacientes_json.php"
update_all_table_references "guardar_reserva_cliente.php"

# Actualizar archivos en carpeta /citas/
echo "📁 Actualizando archivos en /citas/..."
for file in citas/*.php; do
    if [[ -f "$file" ]]; then
        update_all_table_references "$file"
    fi
done

# Actualizar archivos en carpeta /includes/
echo "📁 Actualizando archivos en /includes/..."
for file in includes/*.php; do
    if [[ -f "$file" ]]; then
        update_all_table_references "$file"
    fi
done

# Actualizar archivos en fullcalendar-php-app/public/
echo "📁 Actualizando archivos en /fullcalendar-php-app/public/..."
for file in fullcalendar-php-app/public/*.php; do
    if [[ -f "$file" ]]; then
        update_all_table_references "$file"
    fi
done

echo "✅ ¡Corrección completa terminada!"
echo "🔍 Ahora TODAS las referencias a tablas usan el prefijo 'agenda_'"