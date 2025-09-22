<?php
require_once __DIR__ . '/includes/db.php';
// Mensaje de conexión eliminado
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
    /* Cambia el color de fondo del día actual en FullCalendar */
    .fc-day-today, .fc-timegrid-col.fc-day-today {
      background: #fff !important;
      border-color: #fff !important;
    }
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      width: 100vw;
      background: #f5f5f5;
      overflow: hidden;
    }
    #main-container {
      display: flex;
      height: 100vh;
      width: 100vw;
      margin: 0;
      padding: 0;
      align-items: stretch;
    }
    #sidebar {
      width: 420px;
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
    #mini-calendar-actual, #mini-calendar-proximo { margin-top: 0px !important; margin-bottom: 0px !important; padding-top: 0px !important; padding-bottom: 0px !important; vertical-align: top; width: 100%; padding: 0 !important; border: none !important; background: none !important; margin-top: -24px !important; margin-bottom: 0px !important; position: relative;}
    #mini-calendar-actual .flatpickr-calendar.inline,
    #mini-calendar-proximo .flatpickr-calendar.inline { width: 100% !important; min-width: 0 !important; margin-bottom: 0px !important; margin-top: 0px !important; box-shadow: 0 2px 8px #0001; background: #fff; border-radius: 8px; border: 1px solid #e0e0e0; }
  </style>
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
        <!-- Paciente autocompletar y botón registrar -->
        <div style="margin-bottom:12px;position:relative;">
          <label for="agendarPaciente">Paciente:</label>
          <input type="text" id="agendarPaciente" name="paciente" placeholder="Buscar o registrar paciente" autocomplete="off" style="width:100%;padding:6px;" />
          <div id="pacientesDropdown" style="position:absolute;top:38px;left:0;width:100%;background:#fff;z-index:10001;border:1px solid #ccc;display:none;max-height:180px;overflow-y:auto;"></div>
          <button type="button" id="btnMostrarRegistroPaciente" style="position:absolute;right:0;top:22px;padding:4px 10px;background:#1976d2;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:13px;">Agregar</button>
          <div id="registroPacienteBox" style="display:none;margin-top:10px;padding:12px;background:#f7f7f7;border-radius:6px;box-shadow:0 2px 8px #0001;">
            <h4 style="margin:0 0 8px 0;font-size:16px;">Registrar paciente</h4>
            <input type="text" id="nuevoPacienteNombre" placeholder="Nombre" style="width:100%;margin-bottom:6px;padding:6px;" value="Juan">
            <input type="text" id="nuevoPacienteApellido" placeholder="Apellido" style="width:100%;margin-bottom:6px;padding:6px;" value="Pérez">
            <input type="text" id="nuevoPacienteTelefono" placeholder="Teléfono" style="width:100%;margin-bottom:6px;padding:6px;" value="625118881">
            <input type="text" id="nuevoPacienteDiagnostico" placeholder="Diagnóstico o motivo del estudio" style="width:100%;margin-bottom:6px;padding:6px;" value="Fractura de tobillo">
            <select id="nuevoPacienteTipo" style="width:100%;margin-bottom:6px;padding:6px;">
              <option value="niño" selected>Niño</option>
              <option value="adulto">Adulto</option>
              <option value="IMSS">IMSS</option>
              <option value="urgencias">Urgencias</option>
              <option value="externo">Externo</option>
              <option value="interno">Interno</option>
            </select>
            <select id="nuevoPacienteOrigen" style="width:100%;margin-bottom:10px;padding:6px;">
              <option value="">Origen</option>
              <option value="urgencias" selected>Urgencias</option>
              <option value="externo">Externo</option>
              <option value="interno">Interno</option>
            </select>
            <label for="nuevoPacienteCorreo">Correo:</label>
            <input type="email" id="nuevoPacienteCorreo" placeholder="Correo electrónico" style="width:100%;margin-bottom:6px;padding:6px;" value="juanperez@gmail.com">
            <label for="nuevoPacienteComentarios">Comentarios adicionales:</label>
            <textarea id="nuevoPacienteComentarios" placeholder="Comentarios adicionales" style="width:100%;margin-bottom:6px;padding:6px;">Paciente con antecedentes de fractura previa.</textarea>
            <button type="button" id="btnGuardarPaciente" style="background:#388e3c;color:#fff;padding:6px 16px;border:none;border-radius:4px;cursor:pointer;">Guardar</button>
            <button type="button" id="btnCancelarPaciente" style="background:#e0e0e0;color:#333;padding:6px 12px;border:none;border-radius:4px;cursor:pointer;margin-left:8px;">Volver</button>
          </div>
        </div>
        <div style="margin-bottom:12px;">
          <label for="modalidadSeleccionadaLabel">Modalidad:</label>
          <input type="hidden" id="agendarProfesional" name="profesional" />
          <span id="modalidadSeleccionadaLabel" style="display:inline-block;padding:6px 12px;background:#f9f9f9;border-radius:4px;border:1px solid #ccc;min-width:120px;">Radiología</span>
        </div>
        <div style="margin-bottom:12px;">
          <label for="agendarServicio">Servicio:</label>
          <select id="agendarServicio" name="servicio" style="width:100%;padding:6px;">
            <option value="1" selected>Radiografía</option>
            <option value="2">Resonancia Magnética</option>
          </select>
        </div>
        <div style="margin-bottom:12px;">
          <label for="agendarEstado">Estado:</label>
          <select id="agendarEstado" name="estado_id" style="width:100%;padding:6px;">
            <option value="1" selected>Reservado</option>
            <option value="2">Confirmado</option>
            <option value="3">Asistió</option>
            <option value="4">No asistió</option>
            <option value="5">Pendiente</option>
            <option value="6">En espera</option>
          </select>
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
            <textarea id="notaInterna" name="nota_interna" rows="3" style="width:100%;padding:6px;resize:vertical;" placeholder="Escribe aquí la nota interna para uso del personal...">Información interna adicional</textarea>
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
          });
      }

      var modalidadSelect = document.getElementById('profesional-select');
      modalidadSelect.addEventListener('change', function() {
        var modalidadId = modalidadSelect.value;
        cargarServiciosPorModalidad(modalidadId);
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

      var calendar = new FullCalendar.Calendar(calendarEl, {
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
        height: "100vh",
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
  </script>
</body>
</html>