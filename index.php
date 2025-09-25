<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

// Verificar que el usuario esté logueado
verificarSesion();

// Obtener información del usuario actual
$usuario = obtenerUsuarioActual();

// Verificar permisos de visualización
if (!puedeRealizar('ver_citas')) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agenda Hospital</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Timepicker CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css" rel="stylesheet">
  <!-- FULLCALENDAR CSS LOCAL -->
  <link href="fullcalendar-php-app/assets/css/core.css" rel="stylesheet">
  <link href="fullcalendar-php-app/assets/css/timegrid.css" rel="stylesheet">
  <link href="fullcalendar-php-app/assets/css/resource-timegrid.css" rel="stylesheet">
  <!-- Flatpickr CSS -->
  <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
  <style>
  .fc-custom-tooltip {
    position: absolute;
    background: #fff;
    border: 1px solid #ccc;
    box-shadow: 0 2px 8px rgba(0,0,0,0.13);
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 15px;
    pointer-events: auto;
    z-index: 99999;
    max-width: 280px;
  }
  .estado-puntos {
    display: flex;
    gap: 8px;
    margin: 8px 0;
    align-items: center;
  }
  .estado-punto {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
  }
  .estado-punto:hover {
    transform: scale(1.3);
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
  }
  .estado-punto:active {
    transform: scale(1.1);
  }
  .estado-punto.clickeable {
    opacity: 0.6;
  }
  .estado-punto.clickeable:hover {
    opacity: 1;
  }
  .estado-punto.activo {
    opacity: 1 !important;
  }
  .estado-punto.sin-permisos {
    opacity: 0.3;
    cursor: not-allowed;
  }
  .estado-punto-tooltip {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    z-index: 100001;
  }
  .estado-punto:hover .estado-punto-tooltip {
    opacity: 1;
  }
    /* Cambia el color de fondo del día actual en FullCalendar */
    .fc-day-today, .fc-timegrid-col.fc-day-today {
      background: #fff !important;
      border-color: #fff !important;
    }
    html, body {
      height: 100%;
      margin: 0;
    width: fit-content;
  min-width: 320px;
      background: #f5f5f5;
      overflow: hidden;
    }
    #header-bar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: px;
      background: #fff;
      border-bottom: 1px solid #e0e0e0;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    #header-bar .logo {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    #header-bar .user-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    #header-bar .user-name {
      font-weight: 500;
      color: #333;
    }
    #header-bar .btn-logout {
      background: #dc3545;
      border: none;
      color: white;
      padding: 8px 16px;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
      transition: background 0.2s;
    }
    #header-bar .btn-logout:hover {
      background: #c82333;
    }
    #main-container {
      display: flex;
      height: calc(100vh - 60px);
      width: 100vw;
      margin: 0;
      padding: 0;
      align-items: stretch;
      margin-top: 80px;
    }
    #sidebar {
  width: 320px;
  min-width: 320px;
      height: 100vh;
      margin: 0;
      box-sizing: border-box;
      background: #fff;
      overflow-y: auto;
    }
    #calendar {
      flex: 1;
      min-width: 0;
      height: 100vh;
      margin: 0;
      max-width: 100vw;
      max-height: 100vh;
    }
    .context-menu { position: absolute; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 8px rgba(0,0,0,0.15); z-index: 9999; padding: 8px 0; border-radius: 6px; min-width: 120px; display: none; }
    .context-menu button { width: 100%; background: none; border: none; padding: 8px 16px; text-align: left; cursor: pointer; font-size: 15px; }
    .context-menu button:hover { background: #f0f0f0; }
    .filter-group { margin-bottom: 0px; padding-bottom: 0px; }
    .filter-group label { display: block; margin-bottom: 0px; font-weight: bold; }
    .filter-group label + div { margin-top: 0px !important; padding-top: 0px !important; }
    .tab-button { transition: all 0.3s ease; }
    .tab-button:hover { background: #f8f9fa !important; }
    .tab-button.active { color: #28a745; }
    #mini-calendar-actual, #mini-calendar-proximo {
      margin-top: 0px !important;
      margin-bottom: 0px !important;
      padding-top: 0px !important;
      padding-bottom: 0px !important;
      vertical-align: top;
      width: 100%;
  min-height: 120px;
  max-height: 150px;
  width: 95%;
      padding: 0 !important;
      border: none !important;
      background: none !important;
      margin-top: -12px !important;
      margin-bottom: 0px !important;
      position: relative;
    }
    #mini-calendar-actual .flatpickr-calendar.inline,
    #mini-calendar-actual .flatpickr-calendar.inline,
    #mini-calendar-proximo .flatpickr-calendar.inline {
      width: 95% !important;
      min-width: 120px !important;
      max-width: 180px !important;
      min-height: 100px !important;
      max-height: 140px !important;
      margin-bottom: 0px !important;
      margin-top: 0px !important;
      box-shadow: 0 2px 8px #0001;
      background: #fff;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
      overflow: hidden;
    }
  </style>
<body>
  <!-- Barra superior de usuario -->
  <div id="header-bar">
    <div class="logo">
      <img src="images/logo.png" alt="Hospital Angeles" style="height: 70px; width: auto; margin-bottom: 4px;">
      <div style="font-size: 16px; font-weight: 600; color: #333; line-height: 1;">Imagenología</div>
    </div>
    <div class="user-info">
      <span class="user-name">
        <?= htmlspecialchars($usuario['nombre']) ?>
      </span>
      <?= getBadgeTipoUsuario($usuario['tipo']) ?>
      <?php if (puedeRealizar('gestionar_usuarios')): ?>
        <a href="admin_usuarios.php" class="btn btn-sm btn-outline-secondary" title="Gestionar Usuarios">
          👥 Admin
        </a>
      <?php endif; ?>
      <button class="btn-logout" onclick="logout()">
        Cerrar Sesión
      </button>
    </div>
  </div>

  <div id="main-container">
    <div id="sidebar">
      <?php if (puedeRealizar('crear_citas')): ?>
      <?php endif; ?>
      <div class="filter-group">
        <label for="profesional-select">Modalidad</label>
        <select id="profesional-select" class="form-control">
          <!-- Las modalidades se agregan dinámicamente -->
        </select>
      </div>
      <div class="filter-group">
        <label for="estado-select">Estado de la reserva</label>
          <select id="estado-select" class="form-control">
            <option value="todos" selected>Todos</option>
            <option value="1">Reservado</option>
            <option value="2">Confirmado</option>
            <option value="3">Asistió</option>
            <option value="4">No asistió</option>
            <option value="5">Pendiente</option>
            <option value="6">En espera</option>
        </select>
      </div>
      <div class="filter-group" style="margin-top:32px;">
        <div id="mini-calendar-actual"></div>
      </div>
      <div class="filter-group" style="margin-top:16px;">
        <div id="mini-calendar-proximo"></div>
      </div>
    </div>
    <div id="calendar"></div>
  </div>
  <div id="contextMenu" class="context-menu">
    <?php if (puedeRealizar('crear_citas')): ?>
    <button id="bloquearBtn">Bloquear</button>
    <button id="agendarBtn">Agendar</button>
    <?php else: ?>
    <button disabled style="opacity:0.5;cursor:not-allowed;">Sin permisos para crear citas</button>
    <?php endif; ?>
  </div>
  <!-- Modal para agendar cita -->
  <div id="modalAgendar" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:10000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;padding:32px 40px;border-radius:12px;max-width:900px;width:100%;position:relative;max-height:90vh;overflow-y:auto;box-shadow:0 10px 30px rgba(0,0,0,0.2);">
      <button id="cerrarModalAgendar" style="position:absolute;top:12px;right:16px;font-size:24px;background:none;border:none;cursor:pointer;color:#666;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:50%;transition:background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='none'">&times;</button>
      
      <div style="margin-bottom:24px;">
        <h3 style="margin:0;font-size:24px;color:#333;font-weight:600;">Agendar Nueva Cita</h3>
        <p style="margin:8px 0 0 0;color:#666;font-size:14px;">Complete los datos para programar una nueva cita médica</p>
      </div>
      
      <form id="formAgendar">
        <!-- Sección: Fecha y Hora -->
        <div style="margin-bottom:24px;padding:20px;background:#f8f9fa;border-radius:8px;border-left:4px solid #333;">
          <h4 style="margin:0 0 16px 0;color:#333;font-size:16px;font-weight:600;">Fecha y Horario</h4>
          <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;">
            <div>
              <label for="agendarFecha" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Fecha:</label>
              <input type="text" id="agendarFecha" name="fecha" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;cursor:pointer;transition:border-color 0.2s;" autocomplete="off" />
            </div>
            <div>
              <label for="agendarHoraInicio" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Hora inicio:</label>
              <input type="text" id="agendarHoraInicio" name="hora_inicio" class="form-control timepicker" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;cursor:pointer;transition:border-color 0.2s;" />
            </div>
            <div>
              <label for="agendarHoraFin" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Hora fin:</label>
              <input type="text" id="agendarHoraFin" name="hora_fin" class="form-control timepicker" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;cursor:pointer;transition:border-color 0.2s;" />
            </div>
          </div>
        </div>

        <!-- Sección: Paciente -->
        <div style="margin-bottom:24px;padding:20px;background:#f8f9fa;border-radius:8px;border-left:4px solid #333;">
          <h4 style="margin:0 0 16px 0;color:#333;font-size:16px;font-weight:600;">Información del Paciente</h4>
          <div style="position:relative;">
            <label for="agendarPaciente" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Paciente:</label>
            <div style="display:flex;gap:8px;align-items:flex-start;flex-wrap:wrap;">
              <div style="flex:1;position:relative;min-width:300px;">
                <input type="text" id="agendarPaciente" name="paciente" placeholder="Buscar paciente existente..." autocomplete="off" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;transition:border-color 0.2s;" />
                <div id="pacientesDropdown" style="position:absolute;top:100%;left:0;width:100%;background:#fff;z-index:10001;border:2px solid #e1e5e9;border-top:none;border-radius:0 0 6px 6px;display:none;max-height:200px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,0.1);"></div>
              </div>
              <button type="button" id="btnMostrarRegistroPaciente" style="padding:12px 20px;background:#4caf50;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:14px;font-weight:500;white-space:nowrap;transition:background 0.2s;min-width:140px;" onmouseover="this.style.background='#45a049'" onmouseout="this.style.background='#4caf50'">+ Nuevo Paciente</button>
            </div>
            
            <!-- Formulario de registro de paciente -->
            <div id="registroPacienteBox" style="display:none;margin-top:16px;padding:24px;background:#fff;border-radius:8px;border:2px solid #e8f5e8;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                <h4 style="margin:0;font-size:18px;color:#333;font-weight:600;">Registrar Nuevo Paciente</h4>
                <button type="button" id="btnCancelarPaciente" style="background:#e0e0e0;color:#666;padding:8px 16px;border:none;border-radius:6px;cursor:pointer;font-size:13px;transition:background 0.2s;" onmouseover="this.style.background='#d5d5d5'" onmouseout="this.style.background='#e0e0e0'">Cancelar</button>
              </div>
              
              <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));gap:16px;margin-bottom:16px;">
                <div>
                  <label for="nuevoPacienteNombre" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Nombre:</label>
                  <input type="text" id="nuevoPacienteNombre" placeholder="Nombre del paciente" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;transition:border-color 0.2s;" value="">
                </div>
                <div>
                  <label for="nuevoPacienteApellido" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Apellido:</label>
                  <input type="text" id="nuevoPacienteApellido" placeholder="Apellido del paciente" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;transition:border-color 0.2s;" value="">
                </div>
              </div>
              
              <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));gap:16px;margin-bottom:16px;">
                <div>
                  <label for="nuevoPacienteTelefono" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Teléfono:</label>
                  <input type="text" id="nuevoPacienteTelefono" placeholder="Número de teléfono" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;transition:border-color 0.2s;" value="">
                </div>
                <div>
                  <label for="nuevoPacienteCorreo" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Correo electrónico:</label>
                  <input type="email" id="nuevoPacienteCorreo" placeholder="correo@ejemplo.com" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;transition:border-color 0.2s;" value="">
                </div>
              </div>
              
              <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));gap:16px;margin-bottom:16px;">
                <div>
                  <label for="nuevoPacienteTipo" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Tipo de paciente:</label>
                  <select id="nuevoPacienteTipo" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;transition:border-color 0.2s;">
                    <option value="">Seleccionar tipo...</option>
                    <option value="niño">Niño</option>
                    <option value="adulto" selected>Adulto</option>
                    <option value="IMSS">IMSS</option>
                    <option value="urgencias">Urgencias</option>
                    <option value="externo">Externo</option>
                    <option value="interno">Interno</option>
                  </select>
                </div>
                <div>
                  <label for="nuevoPacienteOrigen" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Origen:</label>
                  <select id="nuevoPacienteOrigen" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;transition:border-color 0.2s;">
                    <option value="">Seleccionar origen...</option>
                    <option value="urgencias">Urgencias</option>
                    <option value="externo" selected>Externo</option>
                    <option value="interno">Interno</option>
                  </select>
                </div>
              </div>
              
              <div style="margin-bottom:16px;">
                <label for="nuevoPacienteDiagnostico" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Diagnóstico o motivo:</label>
                <input type="text" id="nuevoPacienteDiagnostico" placeholder="Diagnóstico o motivo del estudio" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;transition:border-color 0.2s;" value="">
              </div>
              
              <div style="margin-bottom:20px;">
                <label for="nuevoPacienteComentarios" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Comentarios adicionales:</label>
                <textarea id="nuevoPacienteComentarios" placeholder="Información adicional sobre el paciente..." style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;resize:vertical;min-height:80px;transition:border-color 0.2s;"></textarea>
              </div>
              
              <div style="display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap;">
                <button type="button" id="btnGuardarPaciente" style="background:#4caf50;color:#fff;padding:12px 24px;border:none;border-radius:6px;cursor:pointer;font-size:14px;font-weight:500;transition:background 0.2s;" onmouseover="this.style.background='#45a049'" onmouseout="this.style.background='#4caf50'">💾 Guardar Paciente</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Sección: Modalidad y Servicio -->
        <div style="margin-bottom:24px;padding:20px;background:#f8f9fa;border-radius:8px;border-left:4px solid #333;">
          <h4 style="margin:0 0 16px 0;color:#333;font-size:16px;font-weight:600;">Modalidad y Servicio</h4>
          <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));gap:16px;">
            <div>
              <label style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Modalidad:</label>
              <input type="hidden" id="agendarProfesional" name="profesional" />
              <div style="padding:12px;background:#fff;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;min-height:20px;display:flex;align-items:center;">
                <span id="modalidadSeleccionadaLabel" style="color:#666;font-weight:500;">Radiología</span>
              </div>
            </div>
            <div>
              <label for="agendarServicio" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Servicio:</label>
              <select id="agendarServicio" name="servicio" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;transition:border-color 0.2s;">
                <option value="">Primero seleccione una modalidad</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Sección: Estado -->
        <div style="margin-bottom:24px;padding:20px;background:#f8f9fa;border-radius:8px;border-left:4px solid #333;">
          <h4 style="margin:0 0 16px 0;color:#333;font-size:16px;font-weight:600;">Estado de la Cita</h4>
          <div style="max-width:300px;">
            <label for="agendarEstado" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Estado inicial:</label>
            <select id="agendarEstado" name="estado_id" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;transition:border-color 0.2s;">
              <option value="1" selected>Reservado</option>
              <option value="2">Confirmado</option>
              <option value="3">Asistió</option>
              <option value="4">No asistió</option>
              <option value="5">Pendiente</option>
              <option value="6">En espera</option>
            </select>
          </div>
        </div>

        <!-- Sección: Información Adicional (Colapsible) -->
        <div style="margin-bottom:32px;padding:20px;background:#f8f9fa;border-radius:8px;border-left:4px solid #333;">
          <button type="button" id="btnToggleInfoAdicional" style="background:none;border:none;color:#333;padding:0;cursor:pointer;width:100%;text-align:left;font-weight:600;font-size:16px;display:flex;align-items:center;justify-content:between;margin-bottom:16px;">
            <span>Información Adicional</span>
            <span id="iconInfoAdicional" style="font-size:18px;transition:transform 0.3s;margin-left:auto;">▼</span>
          </button>
          <div id="infoAdicionalBox" style="display:none;">
            <div style="margin-bottom:16px;">
              <label for="notaPaciente" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Notas compartidas con el paciente:</label>
              <textarea id="notaPaciente" name="nota_paciente" rows="3" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;resize:vertical;transition:border-color 0.2s;" placeholder="Información que será visible para el paciente...">Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.</textarea>
            </div>
            <div>
              <label for="notaInterna" style="display:block;margin-bottom:6px;font-weight:500;color:#333;">Nota interna del personal:</label>
              <textarea id="notaInterna" name="nota_interna" rows="3" style="width:100%;padding:12px;border:2px solid #e1e5e9;border-radius:6px;font-size:14px;resize:vertical;transition:border-color 0.2s;" placeholder="Notas internas para uso del personal médico..."></textarea>
            </div>
          </div>
        </div>
        
        <!-- Botones de acción -->
        <div style="display:flex;justify-content:flex-end;gap:16px;padding-top:20px;border-top:2px solid #e1e5e9;flex-wrap:wrap;">
          <button type="button" onclick="document.getElementById('modalAgendar').style.display='none'" style="background:#f5f5f5;color:#666;padding:12px 24px;border:2px solid #e1e5e9;border-radius:6px;cursor:pointer;font-size:14px;font-weight:500;transition:all 0.2s;" onmouseover="this.style.background='#eeeeee'" onmouseout="this.style.background='#f5f5f5'">Cancelar</button>
          <button type="submit" style="background:#1976d2;color:#fff;padding:12px 32px;border:none;border-radius:6px;cursor:pointer;font-size:14px;font-weight:500;transition:background 0.2s;box-shadow:0 2px 4px rgba(25,118,210,0.3);" onmouseover="this.style.background='#1565c0'" onmouseout="this.style.background='#1976d2'">Guardar Cita</button>
        </div>
      </form>
      
      <script>
        var btnToggle = document.getElementById('btnToggleInfoAdicional');
        var iconToggle = document.getElementById('iconInfoAdicional');
        if (btnToggle && iconToggle) {
          btnToggle.onclick = function() {
            var box = document.getElementById('infoAdicionalBox');
            var isOpen = (box.style.display === 'block');
            box.style.display = isOpen ? 'none' : 'block';
            iconToggle.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            iconToggle.textContent = isOpen ? '▼' : '▲';
          };
        }
        
        // Mejorar la experiencia de hover en inputs - aplicar después de que el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
          const inputs = document.querySelectorAll('#modalAgendar input, #modalAgendar select, #modalAgendar textarea');
          inputs.forEach(input => {
            input.addEventListener('focus', function() {
              this.style.borderColor = '#1976d2';
              this.style.boxShadow = '0 0 0 3px rgba(25,118,210,0.1)';
            });
            input.addEventListener('blur', function() {
              this.style.borderColor = '#e1e5e9';
              this.style.boxShadow = 'none';
            });
          });
        });
      </script>
    </div>
  </div>
  
  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/index.global.min.js"></script>
  <script>
    // Paciente autocompletar y registro
    let pacientesList = [];
    let pacienteInput = document.getElementById('agendarPaciente');
    let pacientesDropdown = document.getElementById('pacientesDropdown');

    fetch('citas/pacientes_json.php')
      .then(r => r.json())
      .then(data => {
        pacientesList = data;
      });

    function renderPacientesDropdown(filtro) {
      pacientesDropdown.innerHTML = '';
      let filtroLower = filtro.toLowerCase();
      let filtrados = pacientesList.filter(p => p.nombre.toLowerCase().includes(filtroLower));
      if (filtrados.length === 0) {
        pacientesDropdown.style.display = 'none';
        return;
      }
      filtrados.forEach(p => {
        let item = document.createElement('div');
        item.textContent = p.nombre;
        item.style.padding = '6px 10px';
        item.style.cursor = 'pointer';
        item.onclick = function() {
          pacienteInput.value = p.nombre;
          pacienteInput.dataset.pacienteId = p.id;
          pacientesDropdown.style.display = 'none';
        };
        pacientesDropdown.appendChild(item);
      });
      pacientesDropdown.style.display = 'block';
    }

    pacienteInput.addEventListener('input', function() {
      let val = pacienteInput.value.trim();
      if (val.length > 0) {
        renderPacientesDropdown(val);
      } else {
        pacientesDropdown.style.display = 'none';
      }
    });
    pacienteInput.addEventListener('focus', function() {
      if (pacienteInput.value.trim().length > 0) {
        renderPacientesDropdown(pacienteInput.value.trim());
      }
    });
    pacienteInput.addEventListener('blur', function() {
      setTimeout(function() { pacientesDropdown.style.display = 'none'; }, 150);
    });

    document.getElementById('btnMostrarRegistroPaciente').onclick = function() {
      document.getElementById('registroPacienteBox').style.display = 'block';
      document.getElementById('nuevoPacienteNombre').focus();
    };

    document.getElementById('btnGuardarPaciente').onclick = function() {
      let nombre = document.getElementById('nuevoPacienteNombre').value.trim();
      let apellido = document.getElementById('nuevoPacienteApellido').value.trim();
      let telefono = document.getElementById('nuevoPacienteTelefono').value.trim();
      let correo = document.getElementById('nuevoPacienteCorreo').value.trim();
      let comentarios = document.getElementById('nuevoPacienteComentarios').value.trim();
      let diagnostico = document.getElementById('nuevoPacienteDiagnostico').value.trim();
      let tipo = document.getElementById('nuevoPacienteTipo').value;
      let origen = document.getElementById('nuevoPacienteOrigen').value;

      if (nombre && apellido) {
        let formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('apellido', apellido);
        formData.append('telefono', telefono);
        formData.append('correo', correo);
        formData.append('diagnostico', diagnostico);
        formData.append('tipo', tipo);
        formData.append('origen', origen);
        formData.append('comentarios', comentarios);

        fetch('citas/guardar_paciente.php', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(resp => {
          if (resp.success && resp.id) {
            pacientesList.push({id: resp.id, nombre: `${nombre} ${apellido}`});
            pacienteInput.value = `${nombre} ${apellido}`;
            pacienteInput.dataset.pacienteId = resp.id;
            document.getElementById('registroPacienteBox').style.display = 'none';
            alert('Paciente registrado correctamente.');
          } else {
            alert('Error al guardar paciente: ' + (resp.error || ''));
          }
        });
      } else {
        alert('Por favor ingresa nombre y apellido del paciente.');
      }
    };

    document.getElementById('btnCancelarPaciente').onclick = function() {
      document.getElementById('registroPacienteBox').style.display = 'none';
    };

    // -- Calendarios y demás lógica --
    function cargarProfesionales() {
      fetch('citas/recursos_json.php')
        .then(r => r.json())
        .then(data => {
          const select = document.getElementById('profesional-select');
          select.innerHTML = '';
          // Opción 'Todos'
          const optTodos = document.createElement('option');
          optTodos.value = 'todos';
          optTodos.textContent = 'Todos';
          select.appendChild(optTodos);
          // Modalidades
          data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.title;
            select.appendChild(opt);
          });
        });
    }
    cargarProfesionales();

    document.addEventListener('DOMContentLoaded', function() {
      function cargarServiciosPorModalidad(modalidadId) {
        var servicioSelect = document.getElementById('agendarServicio');
        servicioSelect.innerHTML = '';
        var defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = 'Seleccione un servicio';
        servicioSelect.appendChild(defaultOpt);
        if (!modalidadId || isNaN(modalidadId) || modalidadId <= 0) return;
        fetch('citas/servicios_por_modalidad.php?modalidad_id=' + modalidadId)
          .then(r => r.json())
          .then(data => {
            data.forEach(function(servicio) {
              var opt = document.createElement('option');
              opt.value = servicio.id;
              opt.textContent = servicio.nombre;
              servicioSelect.appendChild(opt);
            });
          })
          .catch(error => {
            console.error('Error cargando servicios:', error);
          });
      }

      var modalidadSelect = document.getElementById('profesional-select');
      modalidadSelect.addEventListener('change', function() {
        var modalidadId = modalidadSelect.value;
        
        // Solo cargar servicios si se selecciona una modalidad específica (no 'todos')
        if (modalidadId && modalidadId !== 'todos') {
          cargarServiciosPorModalidad(modalidadId);
        } else {
          // Si se selecciona 'todos', limpiar el select de servicios
          var servicioSelect = document.getElementById('agendarServicio');
          servicioSelect.innerHTML = '<option value="">Primero seleccione una modalidad</option>';
        }
        
        // Filtrar recursos en el calendario
        if (modalidadId === 'todos') {
          calendar.setOption('resources', 'fullcalendar-php-app/public/recursos_json.php');
        } else {
          fetch('fullcalendar-php-app/public/recursos_json.php')
            .then(r => r.json())
            .then(data => {
              const recurso = data.find(item => item.id == modalidadId);
              if (recurso) {
                calendar.setOption('resources', [recurso]);
              }
            });
        }
      });

      var today = new Date();
      var firstDayNext = new Date(today.getFullYear(), today.getMonth() + 1, 1);
      flatpickr('#mini-calendar-actual', {
        locale: flatpickr.l10ns.es,
        inline: true,
        defaultDate: today,
        showMonths: 1,
        onChange: function(selectedDates) {
          if (selectedDates && selectedDates[0]) {
            calendar.changeView('resourceTimeGridDay');
            calendar.gotoDate(selectedDates[0]);
          }
        }
      });
      flatpickr('#mini-calendar-proximo', {
        locale: flatpickr.l10ns.es,
        inline: true,
        defaultDate: firstDayNext,
        showMonths: 1,
        onChange: function(selectedDates) {
          if (selectedDates && selectedDates[0]) {
            calendar.changeView('resourceTimeGridDay');
            calendar.gotoDate(selectedDates[0]);
          }
        }
      });

      var calendarEl = document.getElementById('calendar');
      var contextMenu = document.getElementById('contextMenu');
      var bloquearBtn = document.getElementById('bloquearBtn');
      var agendarBtn = document.getElementById('agendarBtn');
      var lastDateClickInfo = null;
      var tooltipActivo = null; // Variable global para controlar tooltips

      // Event listener global para cerrar tooltip al hacer click fuera
      document.addEventListener('click', function(e) {
        if (tooltipActivo && !tooltipActivo.contains(e.target) && !e.target.closest('.fc-event')) {
          // Verificar si el click es en un punto de estado (no cerrar en ese caso)
          if (e.target.classList.contains('estado-punto') || e.target.closest('.estado-punto')) {
            return; // No cerrar tooltip si se hace click en un punto de estado
          }
          
          // Si el click no es en el tooltip ni en una cita, cerrar tooltip
          if (tooltipActivo.parentNode) {
            document.body.removeChild(tooltipActivo);
          }
          tooltipActivo = null;
          
          // Limpiar referencias en todos los elementos
          var eventos = document.querySelectorAll('.fc-event');
          eventos.forEach(function(evento) {
            if (evento._fcTooltip) {
              evento._fcTooltip = null;
            }
            if (evento._hideTimeout) {
              clearTimeout(evento._hideTimeout);
              evento._hideTimeout = null;
            }
          });
        }
        
        // También cerrar menú contextual si está abierto y el click no es en él
        var contextMenu = document.getElementById('contextMenu');
        if (contextMenu && contextMenu.style.display === 'block' && !contextMenu.contains(e.target)) {
          contextMenu.style.display = 'none';
        }
      });

      // Función para cambiar el estado de una cita
      function cambiarEstadoCita(citaId, nuevoEstado, evento, elementoCita) {
        // Mostrar indicador de carga
        var tooltip = elementoCita._fcTooltip;
        if (tooltip && tooltip.parentNode) {
          var loadingDiv = tooltip.querySelector('.estado-puntos');
          if (loadingDiv) {
            loadingDiv.innerHTML = '<span style="font-size:12px;">Actualizando estado...</span>';
          }
        }
        
        var formData = new FormData();
        formData.append('cita_id', citaId);
        formData.append('estado', nuevoEstado);
        
        fetch('citas/actualizar_estado.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Actualizar el evento en el calendario
            evento.setExtendedProp('estado', nuevoEstado);
            evento.setProp('backgroundColor', data.nuevo_color);
            evento.setProp('borderColor', data.nuevo_color);
            
            // Cerrar el tooltip actual
            if (tooltip && tooltip.parentNode) {
              document.body.removeChild(tooltip);
              elementoCita._fcTooltip = null;
              tooltipActivo = null;
            } else if (tooltip) {
              // Si el tooltip existe pero ya no tiene parent, solo limpiar las referencias
              elementoCita._fcTooltip = null;
              tooltipActivo = null;
            }
            
            // Mostrar mensaje de éxito
            var successMsg = document.createElement('div');
            successMsg.style.cssText = `
              position: fixed; top: 20px; right: 20px; z-index: 100000;
              background: #4CAF50; color: white; padding: 12px 20px;
              border-radius: 4px; font-family: Roboto, sans-serif;
              box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            `;
            successMsg.textContent = `Estado actualizado a: ${nuevoEstado}`;
            document.body.appendChild(successMsg);
            
            setTimeout(() => {
              if (successMsg.parentNode) {
                document.body.removeChild(successMsg);
              }
            }, 3000);
            
          } else {
            // Restablecer el tooltip si aún existe
            var tooltip = elementoCita._fcTooltip;
            if (tooltip && tooltip.parentNode) {
              var loadingDiv = tooltip.querySelector('.estado-puntos');
              if (loadingDiv) {
                loadingDiv.innerHTML = '<span style="font-size:12px; color:red;">Error al actualizar</span>';
              }
            }
            
            alert('Error al actualizar el estado: ' + (data.error || 'Error desconocido'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          
          // Restablecer el tooltip si aún existe
          var tooltip = elementoCita._fcTooltip;
          if (tooltip && tooltip.parentNode) {
            var loadingDiv = tooltip.querySelector('.estado-puntos');
            if (loadingDiv) {
              loadingDiv.innerHTML = '<span style="font-size:12px; color:red;">Error al actualizar</span>';
            }
          }
          
          alert('Error de conexión al actualizar el estado');
        });
      }

      var calendar = new FullCalendar.Calendar(calendarEl, {
        eventDidMount: function(info) {
          var event = info.event;
          var paciente = event.title.split(' (')[0];
          var servicio = event.title.split('(')[1]?.replace(')','') || '';
          var horaInicio = event.start ? event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
          var horaFin = event.end ? event.end.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
          var telefono = event.extendedProps.telefono || '';
          var diagnostico = event.extendedProps.diagnostico || '';
          var pago = event.extendedProps.pago || 'No pagado';
          var estadoActual = event.extendedProps.estado || '';
          
          // Definir todos los estados y sus colores
          var todosLosEstados = [
            {nombre: 'reservado', color: '#2196F3', label: 'Reservado'},
            {nombre: 'confirmado', color: '#FF9800', label: 'Confirmado'},
            {nombre: 'asistió', color: '#E91E63', label: 'Asistió'},
            {nombre: 'no asistió', color: '#FF7F50', label: 'No asistió'},
            {nombre: 'pendiente', color: '#F44336', label: 'Pendiente'},
            {nombre: 'en espera', color: '#4CAF50', label: 'En espera'}
          ];
          
          // Crear puntos de estados
          var estadoPuntos = todosLosEstados.map(estado => {
            var esActual = estadoActual.toLowerCase() === estado.nombre;
            var puedeClick = puedeCambiarEstados && !esActual;
            var claseEstado = esActual ? 'activo' : (puedeClick ? 'clickeable' : 'sin-permisos');
            var border = esActual ? '2px solid #000' : '1px solid #ccc';
            var cursor = puedeClick ? 'pointer' : 'not-allowed';
            var opacity = esActual ? '1' : (puedeClick ? '0.6' : '0.3');
            var tooltipText = esActual ? ' (Actual)' : 
                            (puedeClick ? ' - Click para cambiar' : ' - Sin permisos');
            
            return `
              <div class='estado-punto ${claseEstado}' 
                   data-estado='${estado.nombre}'
                   data-cita-id='${event.id}'
                   style='background-color:${estado.color}; border:${border}; opacity:${opacity}; cursor:${cursor};'>
                <div class='estado-punto-tooltip'>${estado.label}${tooltipText}</div>
              </div>
            `;
          }).join('');
          
          var tooltip = `
            <div style='font-family:Roboto,sans-serif;max-width:260px;'>
              <div style='font-weight:bold;font-size:16px;'>${paciente}</div>
              <div style='margin-bottom:4px;'>${servicio}</div>
              <div style='font-size:14px;'>${horaInicio} - ${horaFin}</div>
              <div style='font-size:14px;'>${pago}</div>
              <div class='estado-puntos'>
                <span style='font-size:12px; margin-right:8px;'>Estados:</span>
                ${estadoPuntos}
              </div>
              <hr style='margin:6px 0;'>
              <div style='font-size:14px;'><span style='margin-right:6px;'>📱</span>${telefono}</div>
              <div style='font-size:14px;'><span style='margin-right:6px;'>💬</span>${diagnostico}</div>
            </div>
          `;
          info.el.setAttribute('title', '');
          info.el.addEventListener('mouseenter', function(e) {
            // Si ya hay un tooltip activo, no crear otro
            if (tooltipActivo) {
              return;
            }
            
            // Limpiar cualquier timeout pendiente
            if (info.el._hideTimeout) {
              clearTimeout(info.el._hideTimeout);
              info.el._hideTimeout = null;
            }
            
            let tip = document.createElement('div');
            tip.className = 'fc-custom-tooltip';
            tip.innerHTML = tooltip;
            tip.style.position = 'absolute';
            tip.style.zIndex = 99999;
            tip.style.background = '#fff';
            tip.style.border = '1px solid #ccc';
            tip.style.boxShadow = '0 2px 8px rgba(0,0,0,0.13)';
            tip.style.padding = '12px 16px';
            tip.style.borderRadius = '8px';
            tip.style.fontSize = '15px';
            tip.style.pointerEvents = 'auto';
            tip.style.top = (e.clientY + 12) + 'px';
            tip.style.left = (e.clientX + 12) + 'px';
            tip.id = 'fc-tooltip-'+event.id;
            document.body.appendChild(tip);
            info.el._fcTooltip = tip;
            tooltipActivo = tip; // Marcar como tooltip activo
            
            // Prevenir que el tooltip desaparezca al hacer hover sobre él
            tip.addEventListener('mouseenter', function() {
              if (info.el._hideTimeout) {
                clearTimeout(info.el._hideTimeout);
                info.el._hideTimeout = null;
              }
            });
            
            tip.addEventListener('mouseleave', function() {
              info.el._hideTimeout = setTimeout(function() {
                if (info.el._fcTooltip && tooltipActivo === info.el._fcTooltip) {
                  document.body.removeChild(info.el._fcTooltip);
                  info.el._fcTooltip = null;
                  tooltipActivo = null;
                }
              }, 300);
            });
            
            // Agregar event listeners para los clicks en los puntos de estado
            tip.addEventListener('click', function(e) {
              if (e.target.classList.contains('estado-punto') && e.target.classList.contains('clickeable')) {
                if (!puedeCambiarEstados) {
                  alert('No tiene permisos para cambiar estados de citas.');
                  return;
                }
                
                var nuevoEstado = e.target.getAttribute('data-estado');
                var citaId = e.target.getAttribute('data-cita-id');
                
                if (nuevoEstado && citaId) {
                  cambiarEstadoCita(citaId, nuevoEstado, event, info.el);
                }
              }
            });
          });
          info.el.addEventListener('mousemove', function(e) {
            if (info.el._fcTooltip) {
              info.el._fcTooltip.style.top = (e.clientY + 12) + 'px';
              info.el._fcTooltip.style.left = (e.clientX + 12) + 'px';
            }
          });
          info.el.addEventListener('mouseleave', function() {
            info.el._hideTimeout = setTimeout(function() {
              if (info.el._fcTooltip && tooltipActivo === info.el._fcTooltip) {
                document.body.removeChild(info.el._fcTooltip);
                info.el._fcTooltip = null;
                tooltipActivo = null;
              }
            }, 300);
          });
        },
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        initialView: 'resourceTimeGridDay',
        locale: 'es',
        resources: 'fullcalendar-php-app/public/recursos_json.php',
        events: 'fullcalendar-php-app/public/citas_json.php',
        // ...sin eventDidMount personalizado...
    /* ...sin tooltip personalizado... */
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'resourceTimeGridDay,listWeek'
        },
        buttonText: {
          today: 'Hoy',
          month: 'Mes',
          week: 'Semana',
          day: 'Día',
          resourceTimeGridDay: 'Día',
          resourceTimeGridWeek: 'Semana'
        },
        slotMinTime: "07:00:00",
        slotMaxTime: "23:59:00",
        height: "100vh",
        selectable: true,
        select: function(info) {
          // Solo mostrar menú contextual si no hay tooltip activo
          if (!tooltipActivo) {
            lastDateClickInfo = info;
            contextMenu.style.display = 'block';
            contextMenu.style.left = info.jsEvent.pageX + 'px';
            contextMenu.style.top = info.jsEvent.pageY + 'px';
          }
        },
        dateClick: function(info) {
          // Solo mostrar menú contextual si no hay tooltip activo
          if (!tooltipActivo) {
            lastDateClickInfo = info;
            contextMenu.style.display = 'block';
            contextMenu.style.left = info.jsEvent.pageX + 'px';
            contextMenu.style.top = info.jsEvent.pageY + 'px';
          }
        },
      });
      calendar.render();

      document.getElementById('profesional-select').addEventListener('change', function() {
        calendar.refetchEvents();
      });
      document.getElementById('estado-select').addEventListener('change', function() {
        var estadoId = this.value;
        if (estadoId === 'todos') {
          calendar.setOption('events', 'fullcalendar-php-app/public/citas_json.php');
        } else {
          calendar.setOption('events', function(fetchInfo, successCallback, failureCallback) {
            fetch('fullcalendar-php-app/public/citas_json.php')
              .then(r => r.json())
              .then(data => {
                var filtrados = data.filter(ev => {
                  // Filtrar por estado_id si existe, si no por estado (nombre)
                  if (typeof ev.estado_id !== 'undefined') {
                    return String(ev.estado_id) === String(estadoId);
                  } else if (typeof ev.estado !== 'undefined') {
                    // El valor del select es el id, pero si el JSON tiene nombre, mapearlo
                    var nombres = {
                      '1': 'Reservado',
                      '2': 'Confirmado',
                      '3': 'Asistió',
                      '4': 'No asistió',
                      '5': 'Pendiente',
                      '6': 'En espera'
                    };
                    return ev.estado === nombres[estadoId];
                  }
                  return false;
                });
                successCallback(filtrados);
              })
              .catch(failureCallback);
          });
        }
      });

      document.getElementById('cerrarModalAgendar').onclick = function() {
        document.getElementById('modalAgendar').style.display = 'none';
      };

      // FECHA Y HORA DEFAULT SEGÚN CALENDARIO
      agendarBtn.onclick = function() {
        contextMenu.style.display = 'none';
        var fecha = '';
        var horaInicio = '';
        var horaFin = '';
        var now = new Date();

        if (lastDateClickInfo && lastDateClickInfo.start && lastDateClickInfo.end) {
          fecha = lastDateClickInfo.start.toISOString().split('T')[0];
          horaInicio = lastDateClickInfo.start.toTimeString().substring(0,5);
          horaFin = lastDateClickInfo.end.toTimeString().substring(0,5);
        } else if (lastDateClickInfo && lastDateClickInfo.date) {
          fecha = lastDateClickInfo.date.toISOString().split('T')[0];
          horaInicio = lastDateClickInfo.date.toTimeString().substring(0,5);
          var dateObj = new Date(lastDateClickInfo.date);
          dateObj.setMinutes(dateObj.getMinutes() + 30);
          horaFin = dateObj.toTimeString().substring(0,5);
        } else {
          fecha = now.toISOString().split('T')[0];
          horaInicio = now.toTimeString().substring(0,5);
          var dateObj = new Date(now);
          dateObj.setMinutes(dateObj.getMinutes() + 30);
          horaFin = dateObj.toTimeString().substring(0,5);
        }

        document.getElementById('agendarFecha').value = fecha;
        document.getElementById('agendarHoraInicio').value = horaInicio;
        document.getElementById('agendarHoraFin').value = horaFin;
        document.getElementById('agendarPaciente').value = '';
        document.getElementById('agendarServicio').value = '';
        var modalidadLabel = document.getElementById('modalidadSeleccionadaLabel');
        var modalidadNombre = '';
        var modalidadId = '';
        if (lastDateClickInfo && lastDateClickInfo.resource) {
          modalidadNombre = lastDateClickInfo.resource.title || '';
          modalidadId = lastDateClickInfo.resource.id || '';
        }
        modalidadLabel.textContent = modalidadNombre ? modalidadNombre : '(No seleccionado)';
        document.getElementById('agendarProfesional').value = modalidadId;
        cargarServiciosPorModalidad(modalidadId);
        document.getElementById('modalAgendar').style.display = 'flex';
        setTimeout(function() {
          var pacienteInput = document.getElementById('agendarPaciente');
          if (pacienteInput) pacienteInput.focus();
        }, 200);
      };

      document.getElementById('formAgendar').onsubmit = function(e) {
        e.preventDefault();
        var fecha = document.getElementById('agendarFecha').value;
        var horaInicio = document.getElementById('agendarHoraInicio').value;
        var horaFin = document.getElementById('agendarHoraFin').value;
        var pacienteId = pacienteInput.dataset.pacienteId || '';
        var profesionalId = document.getElementById('agendarProfesional').value;
        var servicioId = document.getElementById('agendarServicio').value;
        var modalidadId = profesionalId;
        var estadoId = document.getElementById('agendarEstado').value;
        var notaInterna = document.getElementById('notaInterna').value;
        var notaPaciente = document.getElementById('notaPaciente').value;

        if (!pacienteId) {
          alert('Selecciona o registra un paciente antes de agendar la cita.');
          return;
        }

        var formData = new FormData();
        formData.append('fecha', fecha);
        formData.append('hora_inicio', horaInicio);
        formData.append('hora_fin', horaFin);
        formData.append('paciente_id', pacienteId);
        formData.append('profesional_id', profesionalId);
        formData.append('servicio_id', servicioId);
        formData.append('modalidad_id', modalidadId);
        formData.append('estado_id', estadoId);
        formData.append('tipo', '');
        formData.append('nota_interna', notaInterna);
        formData.append('nota_paciente', notaPaciente);
        fetch('citas/guardar_cita.php', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(resp => {
          if (resp.success) {
            alert('Cita agendada correctamente.');
            document.getElementById('modalAgendar').style.display = 'none';
            calendar.refetchEvents();
          } else {
            alert('Error al guardar cita: ' + (resp.error || ''));
          }
        });
      };
    });

    // Variables de permisos del usuario actual
    var puedeEditarCitas = <?= puedeRealizar('editar_citas') ? 'true' : 'false' ?>;
    var puedeCambiarEstados = <?= puedeRealizar('cambiar_estados') ? 'true' : 'false' ?>;
    var tipoUsuario = '<?= $usuario['tipo'] ?>';

    // Función para logout
    function logout() {
      if (confirm('¿Está seguro que desea cerrar sesión?')) {
        window.location.href = 'logout.php';
      }
    }
  </script>
</body>
</html>