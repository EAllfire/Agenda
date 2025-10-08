#!/bin/bash

# Script para actualizar todos los nombres de tablas en el código PHP
# De: usuarios, pacientes, etc. 
# A: agenda_usuarios, agenda_pacientes, etc.

echo "🔄 Iniciando actualización masiva de nombres de tablas..."

# Función para hacer reemplazos en archivos PHP
update_table_names() {
    echo "📝 Actualizando: $1"
    
    # Reemplazar nombres de tablas en consultas SQL
    sed -i '' 's/FROM usuarios/FROM agenda_usuarios/g' "$1"
    sed -i '' 's/INTO usuarios/INTO agenda_usuarios/g' "$1"
    sed -i '' 's/UPDATE usuarios/UPDATE agenda_usuarios/g' "$1"
    sed -i '' 's/DELETE FROM usuarios/DELETE FROM agenda_usuarios/g' "$1"
    
    sed -i '' 's/FROM pacientes/FROM agenda_pacientes/g' "$1"
    sed -i '' 's/INTO pacientes/INTO agenda_pacientes/g' "$1"
    sed -i '' 's/UPDATE pacientes/UPDATE agenda_pacientes/g' "$1"
    sed -i '' 's/DELETE FROM pacientes/DELETE FROM agenda_pacientes/g' "$1"
    
    sed -i '' 's/FROM profesionales/FROM agenda_profesionales/g' "$1"
    sed -i '' 's/INTO profesionales/INTO agenda_profesionales/g' "$1"
    sed -i '' 's/UPDATE profesionales/UPDATE agenda_profesionales/g' "$1"
    
    sed -i '' 's/FROM servicios/FROM agenda_servicios/g' "$1"
    sed -i '' 's/INTO servicios/INTO agenda_servicios/g' "$1"
    sed -i '' 's/UPDATE servicios/UPDATE agenda_servicios/g' "$1"
    sed -i '' 's/DELETE FROM servicios/DELETE FROM agenda_servicios/g' "$1"
    
    sed -i '' 's/FROM modalidades/FROM agenda_modalidades/g' "$1"
    sed -i '' 's/INTO modalidades/INTO agenda_modalidades/g' "$1"
    sed -i '' 's/UPDATE modalidades/UPDATE agenda_modalidades/g' "$1"
    
    sed -i '' 's/FROM citas/FROM agenda_citas/g' "$1"
    sed -i '' 's/INTO citas/INTO agenda_citas/g' "$1"
    sed -i '' 's/UPDATE citas/UPDATE agenda_citas/g' "$1"
    sed -i '' 's/DELETE FROM citas/DELETE FROM agenda_citas/g' "$1"
    
    sed -i '' 's/FROM estado_cita/FROM agenda_estado_cita/g' "$1"
    sed -i '' 's/INTO estado_cita/INTO agenda_estado_cita/g' "$1"
    sed -i '' 's/UPDATE estado_cita/UPDATE agenda_estado_cita/g' "$1"
    
    sed -i '' 's/FROM paquetes/FROM agenda_paquetes/g' "$1"
    sed -i '' 's/INTO paquetes/INTO agenda_paquetes/g' "$1"
    sed -i '' 's/UPDATE paquetes/UPDATE agenda_paquetes/g' "$1"
    
    sed -i '' 's/FROM mensajes/FROM agenda_mensajes/g' "$1"
    sed -i '' 's/INTO mensajes/INTO agenda_mensajes/g' "$1"
    sed -i '' 's/UPDATE mensajes/UPDATE agenda_mensajes/g' "$1"
    
    sed -i '' 's/FROM ventas_servicios/FROM agenda_ventas_servicios/g' "$1"
    sed -i '' 's/INTO ventas_servicios/INTO agenda_ventas_servicios/g' "$1"
    sed -i '' 's/UPDATE ventas_servicios/UPDATE agenda_ventas_servicios/g' "$1"
}

# Actualizar archivos principales
echo "🎯 Actualizando archivos principales..."
update_table_names "index.php"
update_table_names "login.php"
update_table_names "registro.php"
update_table_names "admin_usuarios.php"
update_table_names "catalogo_servicios.php"
update_table_names "actualizar_cita.php"
update_table_names "eliminar_cita.php"
update_table_names "guardar_cita.php"
update_table_names "guardar_paciente.php"
update_table_names "actualizar_estado.php"
update_table_names "servicios_por_modalidad.php"
update_table_names "servicios_con_duracion.php"
update_table_names "citas_json.php"
update_table_names "recursos_json.php"
update_table_names "estados_json.php"
update_table_names "pacientes_json.php"
update_table_names "guardar_reserva_cliente.php"

# Actualizar archivos en carpeta /citas/
echo "📁 Actualizando archivos en /citas/..."
for file in citas/*.php; do
    if [[ -f "$file" ]]; then
        update_table_names "$file"
    fi
done

# Actualizar archivos en carpeta /includes/
echo "📁 Actualizando archivos en /includes/..."
for file in includes/*.php; do
    if [[ -f "$file" ]]; then
        update_table_names "$file"
    fi
done

# Actualizar archivos en carpeta /pagos/
echo "📁 Actualizando archivos en /pagos/..."
for file in pagos/*.php; do
    if [[ -f "$file" ]]; then
        update_table_names "$file"
    fi
done

# Actualizar otros archivos PHP importantes
echo "📁 Actualizando otros archivos..."
update_table_names "test_db.php"
update_table_names "test_duration_system.php"
update_table_names "iniciar_pago.php"
update_table_names "actualizar_usuarios.php"
update_table_names "verificar_usuarios.php"
update_table_names "crear_usuario_test.php"

echo "✅ ¡Actualización masiva completada!"
echo "🔍 Ahora todas las consultas SQL usan los nuevos nombres de tablas con prefijo 'agenda_'"