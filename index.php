<?php
require_once __DIR__ . '/includes/db.php';
if ($conn && !$conn->connect_error) {
  echo '<div style="background:#dff0d8;color:#3c763d;padding:10px;margin:10px 0;border-radius:5px;">Conexión exitosa a la base de datos.</div>';
} else {
  echo '<div style="background:#f2dede;color:#a94442;padding:10px;margin:10px 0;border-radius:5px;">Error de conexión: ' . htmlspecialchars($conn->connect_error) . '</div>';
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
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/index.global.min.css" rel="stylesheet">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
    #main-container {
      display: flex;
      max-width: 1400px;
      margin: 40px auto;
      align-items: flex-start;
    }
    #sidebar {
      width: 380px;
      min-width: 320px;
      margin-right: 48px;
      box-sizing: border-box;
    }
    #calendar {
      flex: 1;
      min-width: 0;
      margin-left: 0;
    }
    .context-menu {
      position: absolute;
      background: #fff;
      border: 1px solid #ccc;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      z-index: 9999;
      padding: 8px 0;
      border-radius: 6px;
      min-width: 120px;
      display: none;
    }
    .context-menu button {
      width: 100%;
      background: none;
      border: none;
      padding: 8px 16px;
      text-align: left;
      cursor: pointer;
      font-size: 15px;
    }
    .context-menu button:hover {
      background: #f0f0f0;
    }
  .filter-group { margin-bottom: 0px; padding-bottom: 0px; }
  .filter-group label { display: block; margin-bottom: 0px; font-weight: bold; }
  .filter-group label { display: block; margin-bottom: 0px !important; font-weight: bold; padding-bottom: 0px !important; }
    .filter-group label + div {
      margin-top: 0px !important;
      padding-top: 0px !important;
    }
    .filter-group {
      margin-bottom: 0px !important;
      padding-bottom: 0px !important;
    }
    #mini-calendar-actual, #mini-calendar-proximo {
      margin-top: 0px !important;
      margin-bottom: 0px !important;
      padding-top: 0px !important;
      padding-bottom: 0px !important;
    }
    .filter-group select, .filter-group .multiselect {
      width: 100%;
      padding: 6px;
      border-radius: 4px;
      border: 1px solid #ccc;
      font-size: 15px;
    }
    #mini-calendar-actual, #mini-calendar-proximo {
  vertical-align: top;
  .filter-group > * { margin-top: 0px !important; margin-bottom: 0px !important; padding-top: 0px !important; padding-bottom: 0px !important; }
  width: 100%;
  padding: 0 !important;
  border: none !important;
  background: none !important;
  margin-top: -24px !important;
  margin-bottom: 0px !important;
  position: relative;
    }
    /* Elimina el CSS vertical de flatpickr, ya no es necesario para dos calendarios separados */
    #mini-calendar-actual .flatpickr-calendar.inline,
    #mini-calendar-actual .flatpickr-calendar.inline,
    #mini-calendar-proximo .flatpickr-calendar.inline {
      width: 100% !important;
      min-width: 0 !important;
      margin-bottom: 0px !important;
      margin-top: 0px !important;
      box-shadow: 0 2px 8px #0001;
      background: #fff;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
    }
  </style>
</head>
<body>
  <div id="main-container">
    <div id="sidebar">
      <div class="filter-group">
        <label for="profesional-select">Modalidad</label>
        <select id="profesional-select" class="form-control">
          <!-- Las modalidades se agregan dinámicamente -->
        </select>
      </div>
      <div class="filter-group">
        <label for="estado-select">Estado de la reserva</label>
        <select id="estado-select" class="form-control">
          <option value="activa" selected>Activa</option>
          <option value="cancelada">Cancelada</option>
        </select>
      </div>
      <div class="filter-group" style="margin-top:32px;">
  <div id="mini-calendar-actual" style="margin-top:-12px !important;padding-top:0px !important;display:block;"></div>
      </div>
      <div class="filter-group" style="margin-top:16px;">
  <div id="mini-calendar-proximo" style="margin-top:-12px !important;padding-top:0px !important;display:block;"></div>
      </div>
    </div>
    <div id="calendar"></div>
  </div>
  <div id="contextMenu" class="context-menu">
    <button id="bloquearBtn">Bloquear</button>
    <button id="agendarBtn">Agendar</button>
  </div>
  <!-- Modal para agendar cita -->
  <div id="modalAgendar" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:10000;align-items:center;justify-content:center;">
    <div style="background:#fff;padding:24px 32px;border-radius:10px;max-width:400px;width:90%;position:relative;">
      <button id="cerrarModalAgendar" style="position:absolute;top:8px;right:12px;font-size:20px;background:none;border:none;cursor:pointer;">&times;</button>
      <h3>Agendar cita</h3>
      <form id="formAgendar">
        <div style="margin-bottom:12px;display:flex;gap:8px;">
          <div style="flex:1;">
            <label for="agendarFecha">Fecha:</label>
            <input type="text" id="agendarFecha" name="fecha" style="width:100%;padding:6px;cursor:pointer;background:#f9f9f9;" autocomplete="off" />
          </div>
          <div style="flex:1;">
            <label for="agendarHoraInicio">Hora inicio:</label>
            <input type="text" id="agendarHoraInicio" name="hora_inicio" class="form-control timepicker" style="width:100%;padding:6px;cursor:pointer;background:#f9f9f9;" />
            </div>
            <div style="flex:1;">
              <label for="agendarHoraFin">Hora fin:</label>
              <input type="text" id="agendarHoraFin" name="hora_fin" class="form-control timepicker" style="width:100%;padding:6px;cursor:pointer;background:#f9f9f9;" />
          </div>
        </div>
        <div style="margin-bottom:12px;position:relative;">
          <label for="agendarPaciente">Paciente:</label>
          <input type="text" id="agendarPaciente" name="paciente" placeholder="Buscar o registrar paciente" autocomplete="off" style="width:100%;padding:6px;" />
          <button type="button" id="btnMostrarRegistroPaciente" style="position:absolute;right:0;top:22px;padding:4px 10px;background:#1976d2;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:13px;">Agregar</button>
          <div id="registroPacienteBox" style="display:none;margin-top:10px;padding:12px;background:#f7f7f7;border-radius:6px;box-shadow:0 2px 8px #0001;">
            <h4 style="margin:0 0 8px 0;font-size:16px;">Registrar paciente</h4>
            <input type="text" id="nuevoPacienteNombre" placeholder="Nombre" style="width:100%;margin-bottom:6px;padding:6px;" />
            <input type="text" id="nuevoPacienteApellido" placeholder="Apellido" style="width:100%;margin-bottom:6px;padding:6px;" />
            <input type="text" id="nuevoPacienteTelefono" placeholder="Teléfono" style="width:100%;margin-bottom:6px;padding:6px;" />
            <input type="text" id="nuevoPacienteDiagnostico" placeholder="Diagnóstico o motivo del estudio" style="width:100%;margin-bottom:6px;padding:6px;" />
            <select id="nuevoPacienteTipo" style="width:100%;margin-bottom:6px;padding:6px;"></select>
            <div id="nuevoTipoPacienteBox" style="display:none; margin-bottom:6px;">
              <input type="text" id="nuevoTipoPaciente" placeholder="Escribe el nuevo tipo de paciente..." style="width:75%;padding:6px;display:inline-block;vertical-align:middle;" />
              <button type="button" id="btnAgregarTipoPaciente" style="background:#1976d2;color:#fff;padding:6px 12px;border:none;border-radius:4px;cursor:pointer;font-size:13px;display:inline-block;vertical-align:middle;">Agregar</button>
              <button type="button" id="btnBorrarTiposPaciente" style="background:#d32f2f;color:#fff;padding:6px 12px;border:none;border-radius:4px;cursor:pointer;font-size:13px;display:inline-block;vertical-align:middle;margin-left:8px;">Borrar todos</button>
            </div>
            <select id="nuevoPacienteOrigen" style="width:100%;margin-bottom:10px;padding:6px;">
              <option value="">Origen</option>
              <option value="urgencias">Urgencias</option>
              <option value="externo">Externo</option>
              <option value="interno">Interno</option>
            </select>
              <label for="nuevoPacienteCorreo">Correo:</label>
              <input type="email" id="nuevoPacienteCorreo" placeholder="Correo electrónico" style="width:100%;margin-bottom:6px;padding:6px;" />
              <label for="nuevoPacienteComentarios">Comentarios adicionales:</label>
              <textarea id="nuevoPacienteComentarios" placeholder="Comentarios adicionales" style="width:100%;margin-bottom:6px;padding:6px;"></textarea>
            <button type="button" id="btnGuardarPaciente" style="background:#388e3c;color:#fff;padding:6px 16px;border:none;border-radius:4px;cursor:pointer;">Guardar</button>
            <button type="button" id="btnCancelarPaciente" style="background:#e0e0e0;color:#333;padding:6px 12px;border:none;border-radius:4px;cursor:pointer;margin-left:8px;">Volver</button>
          </div>
        </div>
        <div style="margin-bottom:12px;">
          <label for="modalidadSeleccionadaLabel">Modalidad:</label>
           <!-- Modalidad solo se muestra como texto, no editable -->
           <input type="hidden" id="agendarProfesional" name="profesional" />
           <span id="modalidadSeleccionadaLabel" style="display:inline-block;padding:6px 12px;background:#f9f9f9;border-radius:4px;border:1px solid #ccc;min-width:120px;"></span>
        </div>
        <div style="margin-bottom:12px;">
          <label for="agendarServicio">Servicio:</label>
          <select id="agendarServicio" name="servicio" style="width:100%;padding:6px;"></select>
        </div>
        <!-- Campo de modalidad eliminado -->
        <div style="margin-bottom:12px;">
          <label for="agendarEstado">Estado:</label>
          <div style="display:flex;align-items:center;gap:10px;">
            <span style="display:inline-block;width:18px;height:18px;border-radius:50%;background:#1976d2;border:1px solid #ccc;"></span>
            <span style="font-size:15px;color:#1976d2;">Reservado</span>
          </div>
        </div>
        <div style="margin-bottom:12px;">
          <button type="button" id="btnToggleInfoAdicional" style="background:#f9f9f9;color:#222;padding:6px 16px;border:1px solid #ccc;border-radius:4px;cursor:pointer;width:100%;text-align:left;font-weight:bold;display:flex;align-items:center;justify-content:space-between;">
            <span>Información adicional</span>
            <span id="iconInfoAdicional" style="font-size:18px;transition:transform 0.2s;">&#9660;</span>
          </button>
          <div id="infoAdicionalBox" style="display:none;margin-top:10px;">
            <label for="notaPaciente">Notas compartidas con el paciente:</label>
            <textarea id="notaPaciente" name="nota_paciente" rows="3" style="width:100%;padding:6px;resize:vertical;" placeholder="Escribe aquí las notas que serán visibles para el paciente...">Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.</textarea>
            <label for="notaInterna" style="margin-top:10px;display:block;">Nota interna:</label>
            <textarea id="notaInterna" name="nota_interna" rows="3" style="width:100%;padding:6px;resize:vertical;" placeholder="Escribe aquí la nota interna para uso del personal..."></textarea>
          </div>
        </div>
        <button type="submit" style="background:#1976d2;color:#fff;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;">Guardar cita</button>
      </form>
    <script>
      var btnToggle = document.getElementById('btnToggleInfoAdicional');
      var iconToggle = document.getElementById('iconInfoAdicional');
      btnToggle.onclick = function() {
        var box = document.getElementById('infoAdicionalBox');
        var isOpen = (box.style.display === 'block');
        box.style.display = isOpen ? 'none' : 'block';
        iconToggle.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
      };
    </script>
    </div>
  </div>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Timepicker CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/index.global.min.css" rel="stylesheet">
  <!-- Flatpickr CSS debe ir después de Bootstrap para que no se sobrescriba -->
  <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
  <!-- Flatpickr JS y localización español (debe ir antes de cualquier script que lo use) -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/index.global.min.js"></script>
  <script>
    // Cargar modalidades/profesionales dinámicamente
    function cargarProfesionales() {
      fetch('citas/recursos_json.php')
        .then(r => r.json())
        .then(data => {
          const select = document.getElementById('profesional-select');
          select.innerHTML = '';
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
      // Cargar servicios dinámicamente según modalidad seleccionada
      function cargarServiciosPorModalidad(modalidadId) {
        console.log('Ejecutando cargarServiciosPorModalidad con modalidadId:', modalidadId);
        var servicioSelect = document.getElementById('agendarServicio');
        // Limpiar el select completamente
        servicioSelect.innerHTML = '';
        var defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = 'Seleccione un servicio';
        servicioSelect.appendChild(defaultOpt);
        // Solo cargar si modalidadId es un número válido mayor a 0
        if (!modalidadId || isNaN(modalidadId) || modalidadId <= 0) return;
        fetch('citas/servicios_por_modalidad.php?modalidad_id=' + modalidadId)
          .then(r => r.json())
          .then(data => {
            const idsAgregados = new Set();
            data.forEach(function(servicio) {
              if (!idsAgregados.has(servicio.id)) {
                var opt = document.createElement('option');
                opt.value = servicio.id;
                opt.textContent = servicio.nombre;
                servicioSelect.appendChild(opt);
                idsAgregados.add(servicio.id);
                console.log('Agregando servicio:', servicio.id, servicio.nombre);
              } else {
                console.log('Servicio duplicado ignorado:', servicio.id, servicio.nombre);
              }
            });
          });
      }

      // Detectar cambio de modalidad y cargar servicios
      var modalidadSelect = document.getElementById('profesional-select');
      modalidadSelect.addEventListener('change', function() {
        // Eliminar el listener de cambio de modalidad fuera del modal
        // Solo se cargan servicios al abrir el modal
      });
      // Cargar servicios al abrir el modal si ya hay modalidad seleccionada
      document.getElementById('agendarBtn').addEventListener('click', function() {
  document.getElementById('modalAgendar').style.display = 'flex';
});
      // Verificación de scripts y divs
      if (typeof flatpickr === 'undefined') {
        alert('Error: flatpickr no está cargado. Verifica el script en el head.');
      }
      if (typeof FullCalendar === 'undefined') {
        alert('Error: FullCalendar no está cargado. Verifica el script en el head.');
      }
      if (!document.getElementById('mini-calendar-actual')) {
        alert('Error: Falta el div mini-calendar-actual en el HTML.');
      }
      if (!document.getElementById('mini-calendar-proximo')) {
        alert('Error: Falta el div mini-calendar-proximo en el HTML.');
      }
      if (!document.getElementById('calendar')) {
        alert('Error: Falta el div calendar en el HTML.');
      }

        // Inicializar flatpickr para el campo de fecha del modal de agendar cita
        var agendarFecha = document.getElementById('agendarFecha');
        if (agendarFecha) {
          flatpickr(agendarFecha, {
            dateFormat: 'Y-m-d',
            allowInput: true,
            clickOpens: true,
            locale: 'es',
            disableMobile: true
          });
        }

      var calendarEl = document.getElementById('calendar');
      var contextMenu = document.getElementById('contextMenu');
      var bloquearBtn = document.getElementById('bloquearBtn');
      var agendarBtn = document.getElementById('agendarBtn');
      var lastDateClickInfo = null;
      // Autocompletar pacientes en el campo de paciente
      var pacienteInput = document.getElementById('agendarPaciente');
      var pacientesList = [];
      var pacientesDropdown = document.createElement('div');
      pacientesDropdown.style.position = 'absolute';
      pacientesDropdown.style.background = '#fff';
      pacientesDropdown.style.border = '1px solid #ccc';
      pacientesDropdown.style.zIndex = '10001';
      pacientesDropdown.style.width = '100%';
      pacientesDropdown.style.maxHeight = '180px';
      pacientesDropdown.style.overflowY = 'auto';
      pacientesDropdown.style.display = 'none';
      pacienteInput.parentNode.appendChild(pacientesDropdown);

      function renderPacientesDropdown(filtro) {
        pacientesDropdown.innerHTML = '';
        var filtroLower = filtro.toLowerCase();
        var filtrados = pacientesList.filter(p => p.nombre.toLowerCase().includes(filtroLower));
        if (filtrados.length === 0) {
          pacientesDropdown.style.display = 'none';
          return;
        }
        filtrados.forEach(p => {
          var item = document.createElement('div');
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
        var val = pacienteInput.value.trim();
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

      fetch('citas/pacientes_json.php')
        .then(r => r.json())
        .then(data => {
          pacientesList = data;
        });

      function hideContextMenu() {
        contextMenu.style.display = 'none';
      }

      document.addEventListener('click', function(e) {
        if (!contextMenu.contains(e.target)) {
          hideContextMenu();
        }
      });

      // Mini calendario: mes actual
      var today = new Date();
      var selectedDate = today;
      var isSyncing = false;
      var calendarActual = flatpickr('#mini-calendar-actual', {
        locale: flatpickr.l10ns.es,
        inline: true,
        defaultDate: today,
        showMonths: 1,
        prevArrow: '<svg width="18" height="18" viewBox="0 0 18 18" style="vertical-align:middle"><polyline points="12 4 6 9 12 14" fill="none" stroke="#1976d2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        nextArrow: '',
        onChange: function(selectedDates, dateStr) {
          if (isSyncing) return;
          if (selectedDates.length) {
            isSyncing = true;
            selectedDate = selectedDates[0];
            calendar.gotoDate(selectedDate);
            calendarProximo.clear();
            calendarActual.setDate(selectedDate, true);
            isSyncing = false;
          }
        },
        onMonthChange: function(selectedDates, dateStr, instance) {
          var nextMonth = new Date(instance.currentYear, instance.currentMonth + 1, 1);
          isSyncing = true;
          calendarProximo.setDate(nextMonth, false);
          calendarProximo.jumpToDate(nextMonth);
          isSyncing = false;
        },
        onYearChange: function(selectedDates, dateStr, instance) {
          var nextMonth = new Date(instance.currentYear, instance.currentMonth + 1, 1);
          isSyncing = true;
          calendarProximo.setDate(nextMonth, false);
          calendarProximo.jumpToDate(nextMonth);
          isSyncing = false;
        }
      });
      // Mini calendario: mes próximo
      var firstDayNext = new Date(today.getFullYear(), today.getMonth() + 1, 1);
      var calendarProximo = flatpickr('#mini-calendar-proximo', {
        locale: flatpickr.l10ns.es,
        inline: true,
        defaultDate: firstDayNext,
        showMonths: 1,
        prevArrow: '',
        nextArrow: '<svg width="18" height="18" viewBox="0 0 18 18" style="vertical-align:middle"><polyline points="6 4 12 9 6 14" fill="none" stroke="#1976d2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        onChange: function(selectedDates, dateStr) {
          if (isSyncing) return;
          if (selectedDates.length) {
            isSyncing = true;
            selectedDate = selectedDates[0];
            calendar.gotoDate(selectedDate);
            calendarActual.clear();
            calendarProximo.setDate(selectedDate, true);
            isSyncing = false;
          }
        },
        onMonthChange: function(selectedDates, dateStr, instance) {
          var prevMonth = new Date(instance.currentYear, instance.currentMonth - 1, 1);
          isSyncing = true;
          calendarActual.setDate(prevMonth, false);
          calendarActual.jumpToDate(prevMonth);
          isSyncing = false;
        },
        onYearChange: function(selectedDates, dateStr, instance) {
          var prevMonth = new Date(instance.currentYear, instance.currentMonth - 1, 1);
          isSyncing = true;
          calendarActual.setDate(prevMonth, false);
          calendarActual.jumpToDate(prevMonth);
          isSyncing = false;
        }
      });
      // Ajuste visual para separar los calendarios
      document.getElementById('mini-calendar-proximo').style.marginTop = '12px';

      // Inicializar calendario principal
      var calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        initialView: 'resourceTimeGridDay',
        locale: 'es',
        resources: 'citas/recursos_json.php',
        events: function(fetchInfo, successCallback, failureCallback) {
          // Filtros
          const profesionales = Array.from(document.getElementById('profesional-select').selectedOptions).map(o => o.value);
          const estados = Array.from(document.getElementById('estado-select').selectedOptions).map(o => o.value);
          let url = 'citas/citas_json.php';
          let params = [];
          if (profesionales.length) params.push('profesionales=' + encodeURIComponent(profesionales.join(',')));
          if (estados.length) params.push('estados=' + encodeURIComponent(estados.join(',')));
          if (params.length) url += '?' + params.join('&');
          fetch(url)
            .then(r => r.json())
            .then(successCallback)
            .catch(failureCallback);
        },
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'resourceTimeGridDay,resourceTimeGridWeek'
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
        height: "auto",
        selectable: true,
        select: function(info) {
          lastDateClickInfo = info;
          contextMenu.style.display = 'block';
          contextMenu.style.left = info.jsEvent.pageX + 'px';
          contextMenu.style.top = info.jsEvent.pageY + 'px';
        },
        dateClick: function(info) {
          lastDateClickInfo = info;
          contextMenu.style.display = 'block';
          contextMenu.style.left = info.jsEvent.pageX + 'px';
          contextMenu.style.top = info.jsEvent.pageY + 'px';
        },
      });
      calendar.render();

      // Recargar eventos al cambiar filtros
      document.getElementById('profesional-select').addEventListener('change', function() {
    // Solo recargar eventos del calendario, no cargar servicios en el modal
    // No llamar cargarServiciosPorModalidad aquí
      });
      document.getElementById('estado-select').addEventListener('change', function() {
        calendar.refetchEvents();
      });

      // Modal agendar cita
      document.getElementById('cerrarModalAgendar').onclick = function() {
        document.getElementById('modalAgendar').style.display = 'none';
      };
      document.getElementById('formAgendar').onsubmit = function(e) {
        e.preventDefault();
        // Recopilar datos del formulario
        var fecha = document.getElementById('agendarFecha').value;
        
        var horaInicio = document.getElementById('agendarHoraInicio').value;
        var horaFin = document.getElementById('agendarHoraFin').value;
        var pacienteNombre = document.getElementById('agendarPaciente').value.trim();
        var profesionalId = document.getElementById('agendarProfesional').value;
        var servicioId = document.getElementById('agendarServicio').value;
        var servicioNombre = '';
        var servicioSelect = document.getElementById('agendarServicio');
        if (servicioSelect.selectedIndex > 0) {
          servicioNombre = servicioSelect.options[servicioSelect.selectedIndex].text;
        }
  var modalidad = document.getElementById('modalidadSeleccionadaLabel').textContent;
  var estado = 'reservado'; // Estado fijo, azul por default
        // Primero, buscar o registrar paciente
        function guardarCitaConPaciente(pacienteId) {
          // Usar el id del servicio seleccionado directamente
          if (!servicioId) {
            alert('Servicio no válido.');
            return;
          }
          // Guardar cita
          var formData = new FormData();
          formData.append('fecha', fecha);
          formData.append('hora_inicio', horaInicio);
          formData.append('hora_fin', horaFin);
          formData.append('paciente_id', pacienteId);
          formData.append('profesional_id', profesionalId);
          formData.append('servicio_id', servicioId);
          formData.append('modalidad_id', profesionalId); // Enviar modalidad_id correctamente
          formData.append('estado', 'reservado'); // Valor permitido por el ENUM
          formData.append('tipo', '');
          var notaInterna = document.getElementById('notaInterna').value;
          var notaPaciente = document.getElementById('notaPaciente').value;
          console.log('Enviando nota interna:', notaInterna);
          console.log('Enviando nota paciente:', notaPaciente);
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
        }
        // Buscar paciente por nombre (simple, puedes mejorar con autocomplete y backend)
  fetch('citas/citas_json.php')
          .then(r => r.json())
          .then(citas => {
            // Aquí podrías buscar pacientes existentes, pero para demo, siempre registrar nuevo
            // Registrar paciente
            var nombre = pacienteNombre.split(' ')[0] || '';
            var apellido = pacienteNombre.split(' ').slice(1).join(' ') || '';
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('apellido', apellido);
            formData.append('telefono', '');
            formData.append('correo', '');
            formData.append('diagnostico', '');
            formData.append('tipo', '');
            formData.append('origen', '');
            fetch('citas/guardar_paciente.php', {
              method: 'POST',
              body: formData
            })
            .then(r => r.json())
            .then(resp => {
              if (resp.success && resp.id) {
                guardarCitaConPaciente(resp.id);
              } else {
                alert('Error al guardar paciente: ' + (resp.error || ''));
              }
            });
          });
      };

      // Modal agendar cita: fecha y hora desplegables
      var pacienteInput = document.getElementById('agendarPaciente');
      var pacientesList = [];
      var pacientesDropdown = document.createElement('div');
      pacientesDropdown.style.position = 'absolute';
      pacientesDropdown.style.background = '#fff';
      pacientesDropdown.style.border = '1px solid #ccc';
      pacientesDropdown.style.zIndex = '10001';
      pacientesDropdown.style.width = '100%';
      pacientesDropdown.style.maxHeight = '180px';
      pacientesDropdown.style.overflowY = 'auto';
      pacientesDropdown.style.display = 'none';
      pacienteInput.parentNode.appendChild(pacientesDropdown);

      // Renderiza la lista de pacientes filtrados y permite seleccionarlos
      function renderPacientesDropdown(filtro) {
        pacientesDropdown.innerHTML = '';
        var filtroLower = filtro.toLowerCase();
        var filtrados = pacientesList.filter(p =>
          (p.nombre + ' ' + p.apellido).toLowerCase().includes(filtroLower)
        );
        if (filtrados.length === 0) {
          pacientesDropdown.style.display = 'none';
          return;
        }
        filtrados.forEach(p => {
          var item = document.createElement('div');
          item.textContent = p.nombre + ' ' + p.apellido + ' (' + p.tipo + ')';
          item.style.padding = '6px 10px';
          item.style.cursor = 'pointer';
          item.onclick = function() {
            pacienteInput.value = p.nombre + ' ' + p.apellido;
            pacienteInput.dataset.pacienteId = p.id;
            pacientesDropdown.style.display = 'none';
          };
          pacientesDropdown.appendChild(item);
        });
        pacientesDropdown.style.display = 'block';
      }

      pacienteInput.addEventListener('input', function() {
        var val = pacienteInput.value.trim();
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

      // Cargar pacientes desde el backend
      fetch('citas/pacientes_json.php')
        .then(r => r.json())
        .then(data => {
          pacientesList = data.sort((a, b) => {
            var nombreA = (a.nombre + ' ' + a.apellido).toLowerCase();
            var nombreB = (b.nombre + ' ' + b.apellido).toLowerCase();
            return nombreA.localeCompare(nombreB);
          });
        });
      agendarBtn.onclick = function() {
        hideContextMenu();
        // Siempre mostrar el modal de agendar cita
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
          // Calcular horaFin sumando 30 minutos a horaInicio
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
        // Mostrar modalidad seleccionada en el label y guardar modalidad_id
        var modalidadLabel = document.getElementById('modalidadSeleccionadaLabel');
        var modalidadNombre = '';
        var modalidadId = '';
        if (lastDateClickInfo && lastDateClickInfo.resource) {
          modalidadNombre = lastDateClickInfo.resource.title || '';
          modalidadId = lastDateClickInfo.resource.id || '';
        }
        modalidadLabel.textContent = modalidadNombre ? modalidadNombre : '(No seleccionado)';
        document.getElementById('agendarProfesional').value = modalidadId;
        // Cargar servicios de la modalidad seleccionada
        cargarServiciosPorModalidad(modalidadId);
        document.getElementById('modalAgendar').style.display = 'flex';
        setTimeout(function() {
          var pacienteInput = document.getElementById('agendarPaciente');
          if (pacienteInput) pacienteInput.focus();
        }, 200);
      };

      // Lógica para mostrar/ocultar y guardar paciente
      var btnMostrarRegistroPaciente = document.getElementById('btnMostrarRegistroPaciente');
      var registroPacienteBox = document.getElementById('registroPacienteBox');
      var btnGuardarPaciente = document.getElementById('btnGuardarPaciente');
      var btnCancelarPaciente = document.getElementById('btnCancelarPaciente');
      // Tipos de paciente iniciales
  var tiposPaciente = [];
      var selectTipoPaciente = document.getElementById('nuevoPacienteTipo');
      function renderTiposPaciente() {
        selectTipoPaciente.innerHTML = '<option value="">Tipo de paciente</option>';
        tiposPaciente.forEach(function(tipo) {
          var opt = document.createElement('option');
          opt.value = tipo;
          opt.textContent = tipo.charAt(0).toUpperCase() + tipo.slice(1);
          selectTipoPaciente.appendChild(opt);
        });
        var optNuevo = document.createElement('option');
        optNuevo.value = '__nuevo__';
        optNuevo.textContent = 'Agregar nuevo tipo...';
        selectTipoPaciente.appendChild(optNuevo);
      }
      renderTiposPaciente();
      var inputNuevoTipo = document.getElementById('nuevoTipoPaciente');
      var btnAgregarTipo = document.getElementById('btnAgregarTipoPaciente');
      var boxNuevoTipo = document.getElementById('nuevoTipoPacienteBox');
      boxNuevoTipo.style.display = 'none';
      selectTipoPaciente.addEventListener('change', function() {
        if (selectTipoPaciente.value === '__nuevo__') {
          boxNuevoTipo.style.display = 'block';
          inputNuevoTipo.focus();
        } else {
          boxNuevoTipo.style.display = 'none';
        }
      });
      btnAgregarTipo.onclick = function() {
        var nuevoTipo = inputNuevoTipo.value.trim();
        if (nuevoTipo && !tiposPaciente.includes(nuevoTipo)) {
          tiposPaciente.push(nuevoTipo);
          renderTiposPaciente();
          selectTipoPaciente.value = nuevoTipo;
          inputNuevoTipo.value = '';
          boxNuevoTipo.style.display = 'none';
        }
      };
      document.getElementById('btnBorrarTiposPaciente').onclick = function() {
        tiposPaciente = [];
        renderTiposPaciente();
        selectTipoPaciente.value = '';
      };
      btnMostrarRegistroPaciente.onclick = function() {
        registroPacienteBox.style.display = 'block';
        document.getElementById('nuevoPacienteNombre').focus();
      };
      btnCancelarPaciente.onclick = function() {
        registroPacienteBox.style.display = 'none';
        document.getElementById('nuevoPacienteNombre').value = '';
        document.getElementById('nuevoPacienteApellido').value = '';
        document.getElementById('nuevoPacienteTelefono').value = '';
        document.getElementById('nuevoPacienteDiagnostico').value = '';
        document.getElementById('nuevoPacienteTipo').selectedIndex = 0;
        document.getElementById('nuevoPacienteOrigen').selectedIndex = 0;
      };
      btnGuardarPaciente.onclick = function() {
        var nombre = document.getElementById('nuevoPacienteNombre').value.trim();
        var apellido = document.getElementById('nuevoPacienteApellido').value.trim();
  var telefono = document.getElementById('nuevoPacienteTelefono').value.trim();
  var correo = document.getElementById('nuevoPacienteCorreo').value.trim();
  var comentarios = document.getElementById('nuevoPacienteComentarios').value.trim();
  var diagnostico = document.getElementById('nuevoPacienteDiagnostico').value.trim();
  var tipo = document.getElementById('nuevoPacienteTipo').value;
  var origen = document.getElementById('nuevoPacienteOrigen').value;
        if (nombre && apellido) {
          // Guardar paciente vía AJAX
          var formData = new FormData();
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
              document.getElementById('agendarPaciente').value = nombre + ' ' + apellido;
              registroPacienteBox.style.display = 'none';
              document.getElementById('nuevoPacienteNombre').value = '';
              document.getElementById('nuevoPacienteApellido').value = '';
              document.getElementById('nuevoPacienteTelefono').value = '';
              document.getElementById('nuevoPacienteDiagnostico').value = '';
              document.getElementById('nuevoPacienteTipo').selectedIndex = 0;
              document.getElementById('nuevoPacienteOrigen').selectedIndex = 0;
              alert('Paciente registrado correctamente.');
            } else {
              alert('Error al guardar paciente: ' + (resp.error || 'Error desconocido'));
            }
          })
          .catch(err => {
            alert('Error de red al guardar paciente: ' + err);
          });
        } else {
          alert('Por favor ingresa nombre y apellido del paciente.');
        }
      };
    });
  </script>
  <script>
  var modalidadSelect = document.getElementById('profesional-select');
  var servicioSelect = document.getElementById('servicio-select');

  // 👉 Cargar servicios al cambiar la modalidad
  modalidadSelect.addEventListener('change', function() {
    var modalidadId = modalidadSelect.value;
    console.log("Cambio de modalidad, cargando servicios de ID:", modalidadId);
    cargarServiciosPorModalidad(modalidadId);
  });

  // 👉 Función que consulta los servicios de la modalidad
  function cargarServiciosPorModalidad(modalidadId) {
    if (!modalidadId) {
      servicioSelect.innerHTML = '<option value="">Seleccione un servicio</option>';
      return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "servicios_por_modalidad.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
      if (xhr.status === 200) {
        servicioSelect.innerHTML = xhr.responseText;
      } else {
        servicioSelect.innerHTML = '<option value="">Error al cargar servicios</option>';
      }
    };

    xhr.send("modalidad_id=" + modalidadId);
  }
</script>

</body>
</html>
