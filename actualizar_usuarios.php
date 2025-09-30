<?php
require_once('includes/db.php');

echo "<h2>Actualizando tabla usuarios para login con nombre de usuario...</h2>";

try {
    // 1. Verificar si la columna ya existe
    $result = $conn->query("DESCRIBE usuarios");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    echo "<h3>Columnas actuales en tabla usuarios:</h3>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>$column</li>";
    }
    echo "</ul>";
    
    if (!in_array('nombre_usuario', $columns)) {
        echo "<h3>🔧 Agregando columna nombre_usuario...</h3>";
        
        // 2. Agregar la columna nombre_usuario
        $sql = "ALTER TABLE usuarios ADD COLUMN nombre_usuario VARCHAR(50) UNIQUE AFTER nombre";
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>✓ Columna nombre_usuario agregada exitosamente</p>";
        } else {
            throw new Exception("Error agregando columna: " . $conn->error);
        }
        
        // 3. Actualizar usuarios existentes con nombres de usuario basados en su correo
        echo "<h3>📝 Actualizando usuarios existentes...</h3>";
        
        $usuarios = $conn->query("SELECT id, correo FROM usuarios");
        while ($usuario = $usuarios->fetch_assoc()) {
            // Crear nombre de usuario basado en el correo (parte antes del @)
            $nombre_usuario = explode('@', $usuario['correo'])[0];
            
            // Limpiar caracteres especiales
            $nombre_usuario = preg_replace('/[^a-zA-Z0-9_.-]/', '', $nombre_usuario);
            
            // Asegurar que sea único
            $contador = 1;
            $nombre_original = $nombre_usuario;
            
            do {
                $check = $conn->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ?");
                $check->bind_param("s", $nombre_usuario);
                $check->execute();
                $existe = $check->get_result()->num_rows > 0;
                
                if ($existe) {
                    $nombre_usuario = $nombre_original . $contador;
                    $contador++;
                }
            } while ($existe);
            
            // Actualizar el usuario
            $update = $conn->prepare("UPDATE usuarios SET nombre_usuario = ? WHERE id = ?");
            $update->bind_param("si", $nombre_usuario, $usuario['id']);
            
            if ($update->execute()) {
                echo "<p style='color: blue;'>✓ Usuario ID {$usuario['id']}: {$usuario['correo']} → $nombre_usuario</p>";
            } else {
                echo "<p style='color: red;'>✗ Error actualizando usuario ID {$usuario['id']}: " . $conn->error . "</p>";
            }
        }
        
        // 4. Hacer la columna NOT NULL después de llenarla
        echo "<h3>🔒 Configurando columna como requerida...</h3>";
        $sql = "ALTER TABLE usuarios MODIFY nombre_usuario VARCHAR(50) NOT NULL UNIQUE";
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>✓ Columna configurada como NOT NULL UNIQUE</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Advertencia configurando NOT NULL: " . $conn->error . "</p>";
        }
        
    } else {
        echo "<p style='color: green;'>✓ La columna nombre_usuario ya existe</p>";
    }
    
    // 5. Verificar el campo password
    echo "<h3>🔐 Verificando campo de contraseña...</h3>";
    if (in_array('password_hash', $columns)) {
        echo "<p style='color: green;'>✓ Campo password_hash existe</p>";
    } else if (in_array('password', $columns)) {
        echo "<p style='color: blue;'>ℹ️ Usando campo 'password' existente</p>";
        echo "<p style='color: orange;'>⚠️ Recomendación: Renombrar 'password' a 'password_hash' para mayor claridad</p>";
    } else {
        echo "<p style='color: red;'>✗ No se encontró campo de contraseña</p>";
    }
    
    // 6. Verificar campo activo
    echo "<h3>👤 Verificando campo activo...</h3>";
    if (!in_array('activo', $columns)) {
        echo "<p style='color: blue;'>➕ Agregando campo 'activo'...</p>";
        $sql = "ALTER TABLE usuarios ADD COLUMN activo BOOLEAN DEFAULT TRUE";
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>✓ Campo 'activo' agregado</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Advertencia agregando campo activo: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: green;'>✓ Campo 'activo' existe</p>";
    }
    
    // 7. Mostrar usuarios finales
    echo "<h3>👥 Usuarios configurados:</h3>";
    $usuarios = $conn->query("SELECT id, nombre, nombre_usuario, correo, tipo FROM usuarios");
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Correo</th><th>Tipo</th></tr>";
    while ($usuario = $usuarios->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$usuario['id']}</td>";
        echo "<td>{$usuario['nombre']}</td>";
        echo "<td><strong>{$usuario['nombre_usuario']}</strong></td>";
        echo "<td>{$usuario['correo']}</td>";
        echo "<td>{$usuario['tipo']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h3 style='color: green;'>🎉 ¡Actualización completada!</h3>";
    echo "<p><strong>Ahora puedes usar el login con nombres de usuario:</strong></p>";
    echo "<ul>";
    echo "<li><a href='login.php' target='_blank'>Ir al Login</a></li>";
    echo "<li><a href='admin_usuarios.php' target='_blank'>Administrar Usuarios</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Error: " . $e->getMessage() . "</p>";
}

$conn->close();
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>