<?php
session_start();
require_once 'includes/db.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$mensaje = '';

// Mensaje de logout exitoso
if (isset($_GET['logout'])) {
    $mensaje = 'Sesión cerrada exitosamente';
}

if ($_POST) {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($correo && $password) {
        $stmt = $conn->prepare("SELECT id, nombre, correo, password, tipo FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                // Login exitoso
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_correo'] = $user['correo'];
                $_SESSION['usuario_tipo'] = $user['tipo'];
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Contraseña incorrecta';
            }
        } else {
            $error = 'Usuario no encontrado';
        }
        $stmt->close();
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
    <title>Login - Agenda Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #333;
            margin-bottom: 5px;
        }
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
        }
        .btn-login:hover {
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
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            color: #667eea;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="images/logo.png" alt="Hospital Angeles" style="height: 60px; margin-bottom: 10px;">
                <h2 style="color: #1f2937; margin: 0;">HOSPITAL ÁNGELES</h2>
                <p style="color: #6c757d; margin: 5px 0 0 0;">IMAGENOLOGÍA - Sistema de Citas</p>
            </div>
            <p>Iniciar Sesión</p>
        </div>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" 
                       value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-login">
                Iniciar Sesión
            </button>
        </form>
        
        <div class="register-link">
            <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>