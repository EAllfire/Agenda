<?php
echo "<h2>🔍 PRUEBA DE CONEXIÓN A BASE DE DATOS REMOTA</h2>";
echo "<p><strong>Host:</strong> 107.180.11.215</p>";
echo "<p><strong>Usuario:</strong> eli</p>";
echo "<p><strong>Base de datos:</strong> agenda_hospital</p>";
echo "<hr>";

require_once 'includes/db.php';

if ($conn->connect_error) {
    echo "<div style='color: red; font-weight: bold;'>❌ ERROR DE CONEXIÓN:</div>";
    echo "<p style='color: red;'>" . $conn->connect_error . "</p>";
} else {
    echo "<div style='color: green; font-weight: bold;'>✅ CONEXIÓN EXITOSA</div>";
    echo "<p style='color: green;'>Conectado correctamente a la base de datos remota</p>";
    
    // Probar consulta básica
    echo "<h3>📋 Verificando tablas disponibles:</h3>";
    $result = $conn->query("SHOW TABLES");
    
    if ($result) {
        echo "<ul>";
        while ($row = $result->fetch_array()) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
        
        // Verificar las tablas portal específicamente
        echo "<h3>🎯 Verificando tablas portal:</h3>";
        $portal_tables = $conn->query("SHOW TABLES LIKE 'portal_%'");
        if ($portal_tables && $portal_tables->num_rows > 0) {
            echo "<div style='color: green;'>✅ Tablas portal encontradas:</div>";
            echo "<ul>";
            while ($row = $portal_tables->fetch_array()) {
                echo "<li style='color: green;'>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<div style='color: orange;'>⚠️ No se encontraron tablas con prefijo 'portal_'</div>";
        }
        
        // Verificar tablas agenda
        echo "<h3>🗃️ Verificando tablas agenda:</h3>";
        $agenda_tables = $conn->query("SHOW TABLES LIKE 'agenda_%'");
        if ($agenda_tables && $agenda_tables->num_rows > 0) {
            echo "<div style='color: blue;'>📋 Tablas agenda encontradas:</div>";
            echo "<ul>";
            while ($row = $agenda_tables->fetch_array()) {
                echo "<li style='color: blue;'>" . $row[0] . "</li>";
            }
            echo "</ul>";
        }
        
    } else {
        echo "<div style='color: red;'>❌ Error al consultar tablas: " . $conn->error . "</div>";
    }
}

$conn->close();
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
ul { background: #f5f5f5; padding: 15px; border-radius: 5px; }
li { margin: 5px 0; }
</style>