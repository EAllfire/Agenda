<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("includes/db.php");
header('Content-Type: application/json');

// Obtener datos del POST
$nombre_completo = $_POST['nombre'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$email = $_POST['email'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$fecha_cita = $_POST['fecha_cita'] ?? '';
$hora_seleccionada = $_POST['hora_seleccionada'] ?? '';
$modalidad_id = $_POST['modalidad_id'] ?? null;
$servicio_id = $_POST['servicio_id'] ?? null;
$tipo_reserva = $_POST['tipo_reserva'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';

// Para paquetes
$paquete_tipo = $_POST['paquete_tipo'] ?? '';
$paquete_servicios = $_POST['paquete_servicios'] ?? '';

error_log('guardar_reserva_cliente.php datos recibidos: ' . json_encode($_POST));

$response = [];

try {
    // Validar datos requeridos
    if (empty($nombre_completo) || empty($telefono) || empty($email) || empty($fecha_cita) || empty($hora_seleccionada)) {
        throw new Exception("Faltan datos requeridos");
    }

    // Separar nombre y apellido
    $nombre_partes = explode(' ', trim($nombre_completo), 2);
    $nombre = $nombre_partes[0];
    $apellido = isset($nombre_partes[1]) ? $nombre_partes[1] : '';

    // Iniciar transacción
    $conn->begin_transaction();

    // 1. Verificar si el paciente ya existe por email
    $stmt_check = $conn->prepare("SELECT id FROM pacientes WHERE correo = ? LIMIT 1");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if ($result->num_rows > 0) {
        // Paciente existe, obtener ID
        $paciente = $result->fetch_assoc();
        $paciente_id = $paciente['id'];
        
        // Actualizar datos del paciente
        $stmt_update = $conn->prepare("UPDATE pacientes SET nombre = ?, apellido = ?, telefono = ? WHERE id = ?");
        $stmt_update->bind_param("sssi", $nombre, $apellido, $telefono, $paciente_id);
        $stmt_update->execute();
        
    } else {
        // 2. Crear nuevo paciente
        $stmt_paciente = $conn->prepare("INSERT INTO pacientes (nombre, apellido, telefono, correo, comentarios, tipo, origen) VALUES (?, ?, ?, ?, ?, 'cliente', 'web')");
        $comentarios_paciente = "Fecha nacimiento: " . $fecha_nacimiento . ($observaciones ? " | " . $observaciones : "");
        $stmt_paciente->bind_param("sssss", $nombre, $apellido, $telefono, $email, $comentarios_paciente);
        
        if (!$stmt_paciente->execute()) {
            throw new Exception("Error al crear paciente: " . $stmt_paciente->error);
        }
        
        $paciente_id = $conn->insert_id;
    }

    // 3. Calcular hora de fin (duración por defecto 60 minutos)
    $hora_inicio = $hora_seleccionada . ":00";
    $hora_fin_timestamp = strtotime($hora_inicio) + (60 * 60); // 60 minutos
    $hora_fin = date("H:i:s", $hora_fin_timestamp);

    // 4. Obtener ID del estado "reservado" (o crear uno por defecto)
    $stmt_estado = $conn->prepare("SELECT id FROM estado_cita WHERE nombre = 'reservado' LIMIT 1");
    $stmt_estado->execute();
    $result_estado = $stmt_estado->get_result();
    
    if ($result_estado->num_rows > 0) {
        $estado = $result_estado->fetch_assoc();
        $estado_id = $estado['id'];
    } else {
        // Si no existe, usar ID 1 por defecto o crear
        $estado_id = 1;
    }

    // 5. Obtener profesional por defecto (primer profesional activo)
    $stmt_prof = $conn->prepare("SELECT id FROM profesionales ORDER BY id LIMIT 1");
    $stmt_prof->execute();
    $result_prof = $stmt_prof->get_result();
    
    if ($result_prof->num_rows > 0) {
        $profesional = $result_prof->fetch_assoc();
        $profesional_id = $profesional['id'];
    } else {
        throw new Exception("No hay profesionales disponibles");
    }

    // 6. Preparar datos según tipo de reserva
    if ($tipo_reserva === 'paquete') {
        // Para paquetes, usar servicio genérico o crear uno
        $stmt_servicio_paq = $conn->prepare("SELECT id FROM servicios WHERE nombre LIKE '%paquete%' OR nombre LIKE '%integral%' LIMIT 1");
        $stmt_servicio_paq->execute();
        $result_serv = $stmt_servicio_paq->get_result();
        
        if ($result_serv->num_rows > 0) {
            $servicio = $result_serv->fetch_assoc();
            $servicio_id = $servicio['id'];
        } else {
            // Usar primer servicio disponible
            $stmt_first_serv = $conn->prepare("SELECT id, modalidad_id FROM servicios ORDER BY id LIMIT 1");
            $stmt_first_serv->execute();
            $result_first = $stmt_first_serv->get_result();
            if ($result_first->num_rows > 0) {
                $servicio_data = $result_first->fetch_assoc();
                $servicio_id = $servicio_data['id'];
                $modalidad_id = $servicio_data['modalidad_id'];
            } else {
                throw new Exception("No hay servicios disponibles");
            }
        }
        
        $nota_paciente = "Paquete: " . $paquete_tipo . " | Servicios: " . $paquete_servicios;
    } else {
        // Para servicios individuales, validar que existan
        if (!$servicio_id || !$modalidad_id) {
            throw new Exception("Servicio o modalidad no especificados");
        }
        
        $nota_paciente = $observaciones;
    }

    // 7. Verificar empalme de citas
    $sqlEmpalme = "SELECT COUNT(*) as total FROM citas 
                   WHERE fecha = ? AND modalidad_id = ? 
                   AND ((hora_inicio < ? AND hora_fin > ?) 
                   OR (hora_inicio < ? AND hora_fin > ?) 
                   OR (hora_inicio >= ? AND hora_inicio < ?))";
    
    $stmtEmpalme = $conn->prepare($sqlEmpalme);
    $stmtEmpalme->bind_param("sissssss", 
        $fecha_cita, $modalidad_id, 
        $hora_fin, $hora_inicio,
        $hora_inicio, $hora_fin,
        $hora_inicio, $hora_fin
    );
    $stmtEmpalme->execute();
    $resultEmpalme = $stmtEmpalme->get_result();
    $empalme = $resultEmpalme->fetch_assoc();

    if ($empalme['total'] > 0) {
        throw new Exception("Ya existe una cita en ese horario para la modalidad seleccionada");
    }

    // 8. Crear la cita
    $stmt_cita = $conn->prepare("INSERT INTO citas (fecha, hora_inicio, hora_fin, paciente_id, profesional_id, servicio_id, modalidad_id, estado_id, nota_paciente, nota_interna, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $nota_interna = "Reserva web - Cliente: " . $nombre_completo . " | Email: " . $email;
    $tipo_cita = ($tipo_reserva === 'paquete') ? 'paquete' : 'individual';
    
    $stmt_cita->bind_param("sssiiiissss", 
        $fecha_cita, 
        $hora_inicio, 
        $hora_fin, 
        $paciente_id, 
        $profesional_id, 
        $servicio_id, 
        $modalidad_id, 
        $estado_id, 
        $nota_paciente, 
        $nota_interna,
        $tipo_cita
    );

    if (!$stmt_cita->execute()) {
        throw new Exception("Error al crear cita: " . $stmt_cita->error);
    }

    $cita_id = $conn->insert_id;

    // Confirmar transacción
    $conn->commit();
    
    // Si llegamos aquí, la reserva se creó exitosamente
    // Ahora crear el pago
    try {
        require_once('includes/GestorPagos.php');
        $gestorPagos = new GestorPagos($conn);
        
        $resultado_pago = $gestorPagos->crearPago($cita_id, 'simulador', 'tarjeta');
        
        if ($resultado_pago['success']) {
            // Respuesta exitosa con información de pago
            $response = [
                "success" => true,
                "message" => "Reserva creada exitosamente",
                "requiere_pago" => true,
                "data" => [
                    "paciente_id" => $paciente_id,
                    "cita_id" => $cita_id,
                    "fecha" => $fecha_cita,
                    "hora" => $hora_inicio,
                    "tipo" => $tipo_reserva,
                    "pago" => $resultado_pago
                ]
            ];
        } else {
            // Si falla el pago, mantener la cita pero marcar pago como pendiente
            $response = [
                "success" => true,
                "message" => "Reserva creada. Hubo un problema inicializando el pago.",
                "requiere_pago" => true,
                "pago_error" => $resultado_pago['error'] ?? 'Error desconocido',
                "data" => [
                    "paciente_id" => $paciente_id,
                    "cita_id" => $cita_id,
                    "fecha" => $fecha_cita,
                    "hora" => $hora_inicio,
                    "tipo" => $tipo_reserva
                ]
            ];
        }
    } catch (Exception $e) {
        // Si hay error con el sistema de pagos, mantener la cita
        error_log('Error inicializando pago: ' . $e->getMessage());
        
        $response = [
            "success" => true,
            "message" => "Reserva creada. El sistema de pagos no está disponible temporalmente.",
            "requiere_pago" => false,
            "data" => [
                "paciente_id" => $paciente_id,
                "cita_id" => $cita_id,
                "fecha" => $fecha_cita,
                "hora" => $hora_inicio,
                "tipo" => $tipo_reserva
            ]
        ];
    }

    // Log para debug
    error_log('Reserva creada exitosamente - Paciente ID: ' . $paciente_id . ', Cita ID: ' . $cita_id);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    
    $response = [
        "success" => false,
        "error" => $e->getMessage()
    ];
    
    error_log('Error en guardar_reserva_cliente.php: ' . $e->getMessage());
}

// Cerrar conexión
$conn->close();

// Enviar respuesta
echo json_encode($response);
?>