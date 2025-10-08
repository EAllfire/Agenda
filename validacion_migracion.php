<?php
echo "<h2>🔍 VALIDACIÓN DE MIGRACIÓN LOCAL → REMOTO</h2>";
echo "<p><strong>Validando adaptación de campos locales a estructura remota</strong></p>";
echo "<hr>";

require_once 'includes/db.php';

// Test 1: Verificar que portal_pacientes tiene columna 'alergias'
echo "<h3>1. ✅ Verificando estructura portal_pacientes:</h3>";
$result = $conn->query("DESCRIBE portal_pacientes");
$columns_pacientes = [];
while ($row = $result->fetch_assoc()) {
    $columns_pacientes[] = $row['Field'];
    echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
}
echo "<div style='color: " . (in_array('alergias', $columns_pacientes) ? 'green' : 'red') . ";'>";
echo in_array('alergias', $columns_pacientes) ? "✅ Columna 'alergias' encontrada" : "❌ Columna 'alergias' NO encontrada";
echo "</div><br>";

// Test 2: Verificar que portal_servicios tiene columnas correctas
echo "<h3>2. ✅ Verificando estructura portal_servicios:</h3>";
$result = $conn->query("DESCRIBE portal_servicios");
$columns_servicios = [];
while ($row = $result->fetch_assoc()) {
    $columns_servicios[] = $row['Field'];
    echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
}
echo "<div style='color: " . (in_array('modalidad', $columns_servicios) ? 'green' : 'red') . ";'>";
echo in_array('modalidad', $columns_servicios) ? "✅ Columna 'modalidad' encontrada" : "❌ Columna 'modalidad' NO encontrada";
echo "</div>";
echo "<div style='color: " . (in_array('duracion', $columns_servicios) ? 'green' : 'red') . ";'>";
echo in_array('duracion', $columns_servicios) ? "✅ Columna 'duracion' encontrada" : "❌ Columna 'duracion' NO encontrada";
echo "</div>";
echo "<div style='color: " . (in_array('precio', $columns_servicios) ? 'green' : 'red') . ";'>";
echo in_array('precio', $columns_servicios) ? "✅ Columna 'precio' encontrada" : "❌ Columna 'precio' NO encontrada";
echo "</div><br>";

// Test 3: Probar INSERT en portal_pacientes con mapeo diagnostico → alergias
echo "<h3>3. 🧪 Test INSERT portal_pacientes (diagnostico → alergias):</h3>";
try {
    $test_diagnostico = "Prueba migración - " . date('Y-m-d H:i:s');
    $nombre = "Test";
    $apellido = "Migracion";
    $telefono = "555-TEST";
    $correo = "test@migracion.com";
    $tipo = "interno";
    $origen = "test";
    $comentarios = "Test de migración";
    
    $stmt = $conn->prepare("INSERT INTO portal_pacientes (nombre, apellido, telefono, correo, alergias, tipo, origen, comentarios) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $nombre, $apellido, $telefono, $correo, $test_diagnostico, $tipo, $origen, $comentarios);
    
    if ($stmt->execute()) {
        $test_id = $conn->insert_id;
        echo "<div style='color: green;'>✅ INSERT exitoso con ID: $test_id</div>";
        
        // Verificar que se guardó en alergias
        $check = $conn->query("SELECT alergias FROM portal_pacientes WHERE id = $test_id");
        $row = $check->fetch_assoc();
        echo "<div style='color: green;'>✅ Dato guardado en 'alergias': " . $row['alergias'] . "</div>";
        
        // Limpiar test
        $conn->query("DELETE FROM portal_pacientes WHERE id = $test_id");
        echo "<div style='color: blue;'>🧹 Registro de prueba eliminado</div>";
    } else {
        echo "<div style='color: red;'>❌ Error en INSERT: " . $conn->error . "</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Excepción en INSERT: " . $e->getMessage() . "</div>";
}
echo "<br>";

// Test 4: Probar SELECT con mapeo alergias AS diagnostico
echo "<h3>4. 🧪 Test SELECT con mapeo (alergias AS diagnostico):</h3>";
try {
    // Buscar algunos pacientes reales
    $result = $conn->query("SELECT nombre, apellido, alergias AS diagnostico FROM portal_pacientes LIMIT 3");
    if ($result && $result->num_rows > 0) {
        echo "<div style='color: green;'>✅ SELECT con alias exitoso:</div>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['nombre']} {$row['apellido']} - Diagnóstico: " . ($row['diagnostico'] ?: 'Sin diagnóstico') . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<div style='color: orange;'>⚠️ No hay pacientes en la tabla o error en consulta</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Error en SELECT: " . $e->getMessage() . "</div>";
}
echo "<br>";

// Test 5: Probar SELECT servicios con mapeos modalidad y duracion
echo "<h3>5. 🧪 Test SELECT servicios con mapeos:</h3>";
try {
    $result = $conn->query("SELECT nombre, modalidad AS modalidad_id, duracion AS duracion_minutos, precio FROM portal_servicios LIMIT 3");
    if ($result && $result->num_rows > 0) {
        echo "<div style='color: green;'>✅ SELECT servicios con alias exitoso:</div>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['nombre']} - Modalidad: {$row['modalidad_id']}, Duración: {$row['duracion_minutos']} min, Precio: \${$row['precio']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<div style='color: orange;'>⚠️ No hay servicios en la tabla o error en consulta</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Error en SELECT servicios: " . $e->getMessage() . "</div>";
}
echo "<br>";

// Test 6: Verificar endpoints JSON principales
echo "<h3>6. 🌐 Test endpoints JSON:</h3>";
$endpoints = [
    'citas/citas_json.php' => 'Citas con pacientes y servicios',
    'citas/servicios_json.php' => 'Lista de servicios',
    'citas/recursos_json.php' => 'Recursos/modalidades'
];

foreach ($endpoints as $endpoint => $description) {
    echo "<div>";
    echo "<strong>$description:</strong> ";
    $url = "http://localhost:8888/agenda/$endpoint";
    
    // Verificar que el archivo existe
    if (file_exists($endpoint)) {
        echo "<span style='color: green;'>✅ Archivo existe</span>";
        // Podrías añadir una verificación HTTP aquí si quieres
    } else {
        echo "<span style='color: red;'>❌ Archivo no encontrado</span>";
    }
    echo "</div>";
}

echo "<br><hr>";
echo "<h3>📋 RESUMEN DE VALIDACIÓN:</h3>";
echo "<p><strong>Puntos críticos a verificar manualmente:</strong></p>";
echo "<ul>";
echo "<li>✅ Probar creación de cita desde el formulario principal</li>";
echo "<li>✅ Verificar que el calendario muestra datos correctamente</li>";
echo "<li>✅ Probar formulario de nuevo paciente</li>";
echo "<li>✅ Verificar filtros por modalidad en servicios</li>";
echo "<li>✅ Comprobar que no hay errores 500 en endpoints JSON</li>";
echo "</ul>";

$conn->close();
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
ul { background: #f5f5f5; padding: 15px; border-radius: 5px; }
li { margin: 5px 0; }
h3 { color: #333; border-bottom: 2px solid #ddd; padding-bottom: 5px; }
</style>