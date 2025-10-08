<?php
session_start();
require_once 'includes/db.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_POST) {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    
    if ($nombre && $correo && $password && $confirm_password && $tipo) {
        if ($password !== $confirm_password) {
            $error = 'Las contraseñas no coinciden';
        } elseif (strlen($password) < 6) {
            $error = 'La contraseña debe tener al menos 6 caracteres';
        } elseif (!in_array($tipo, ['caja', 'lectura'])) {
            $error = 'Tipo de usuario no válido. Solo se permiten usuarios de Caja y Lectura.';
        } else {
            // Verificar si el correo ya existe
            $stmt = $conn->prepare("SELECT id FROM agenda_usuarios WHERE correo = ?");
            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'El correo electrónico ya está registrado';
            } else {
                // Crear usuario
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO agenda_usuarios (nombre, correo, password, tipo) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $nombre, $correo, $password_hash, $tipo);
                
                if ($stmt->execute()) {
                    $success = 'Usuario registrado exitosamente. Ya puedes iniciar sesión.';
                } else {
                    $error = 'Error al crear el usuario';
                }
            }
            $stmt->close();
        }
    } else {
        $error = 'Por favor complete todos los campos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Agenda Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
            padding: 20px 0;
        }
        .register-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header h2 {
            color: #333;
            margin-bottom: 5px;
        }
        .register-header p {
            color: #666;
            font-size: 14px;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
        }
        .btn-register:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .alert {
            border-radius: 8px;
            font-size: 14px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .tipo-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 8px;
        }
        .badge-admin { background: #dc3545; color: white; }
        .badge-caja { background: #fd7e14; color: white; }
        .badge-lectura { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="images/logo.png" alt="Hospital Angeles" style="height: 60px; margin-bottom: 10px;">
                <h2 style="color: #1f2937; margin: 0;">HOSPITAL ÁNGELES</h2>
                <p style="color: #6c757d; margin: 5px 0 0 0;">IMAGENOLOGÍA - Sistema de Citas</p>
            </div>
            <p>Crear Nueva Cuenta</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" 
                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" 
                       value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="tipo">Tipo de Usuario</label>
                <select class="form-control" id="tipo" name="tipo" required>
                    <option value="">Seleccionar tipo de usuario</option>
                    <option value="caja" <?= ($_POST['tipo'] ?? '') === 'caja' ? 'selected' : '' ?>>
                        Caja <span class="tipo-badge badge-caja">Gestión de Citas</span>
                    </option>
                    <option value="lectura" <?= ($_POST['tipo'] ?? '') === 'lectura' ? 'selected' : '' ?>>
                        Lectura <span class="tipo-badge badge-lectura">Solo Ver</span>
                    </option>
                </select>
                <small class="form-text text-muted">
                    <strong>Caja:</strong> Crear/editar citas | 
                    <strong>Lectura:</strong> Solo visualizar<br>
                    <em>Nota: Solo un administrador puede crear otros administradores</em>
                </small>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <small class="form-text text-muted">Mínimo 6 caracteres</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-register">
                Crear Cuenta
            </button>
        </form>
        
        <div class="login-link">
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>