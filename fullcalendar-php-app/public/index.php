<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agenda FullCalendar</title>
  <!-- CSS locales -->
  <link href="assets/css/core.css" rel="stylesheet">
  <link href="assets/css/timegrid.css" rel="stylesheet">
  <link href="assets/css/resource-timegrid.css" rel="stylesheet">
  <!-- JS desde CDN (si falla, avísame y te los paso locales) -->
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.19/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.19/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/resource-timegrid@6.1.19/index.global.min.js"></script>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background: #f5f5f5;
      width: 100vw;
      overflow: hidden;
    }
    #main-container {
      display: flex;
      height: 100vh;
      width: 100vw;
      margin: 0;
      padding: 0;
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
    .fc-col-header-cell, .fc-resource { min-width: 180px !important; }
  </style>
</head>
<body>
  <div id="calendar"></div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'resourceTimeGridWeek', // vista con recursos
        locale: 'es',
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        resources: 'recursos_json.php',  // endpoint para recursos
        events: 'citas_json.php',        // endpoint para citas
        editable: true,
        selectable: true,
        height: '100vh',
        eventClick: function(info) {
          alert('Evento: ' + info.event.title);
        }
      });
      calendar.render();

      // Esperar a que el calendario esté renderizado antes de inicializar flatpickr
      setTimeout(function() {
        if (window.flatpickr && document.getElementById('mini-calendar-actual')) {
          flatpickr('#mini-calendar-actual', {
            locale: flatpickr.l10ns.es,
            inline: true,
            defaultDate: new Date(),
            showMonths: 1,
            onChange: function(selectedDates) {
              if (selectedDates && selectedDates[0]) {
                console.log('Mini calendario actual: fecha seleccionada', selectedDates[0]);
                calendar.changeView('resourceTimeGridDay');
                calendar.gotoDate(selectedDates[0]);
              } else {
                console.log('Mini calendario actual: sin fecha seleccionada');
              }
            }
          });
        }
        if (window.flatpickr && document.getElementById('mini-calendar-proximo')) {
          var today = new Date();
          var firstDayNext = new Date(today.getFullYear(), today.getMonth() + 1, 1);
          flatpickr('#mini-calendar-proximo', {
            locale: flatpickr.l10ns.es,
            inline: true,
            defaultDate: firstDayNext,
            showMonths: 1,
            onChange: function(selectedDates) {
              if (selectedDates && selectedDates[0]) {
                console.log('Mini calendario próximo: fecha seleccionada', selectedDates[0]);
                calendar.changeView('resourceTimeGridDay');
                calendar.gotoDate(selectedDates[0]);
              } else {
                console.log('Mini calendario próximo: sin fecha seleccionada');
              }
            }
          });
        }
      }, 300);
    });
  </script>
</body>
</html>