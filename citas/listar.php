<?php include("../includes/db.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agenda Hospital Ángeles</title>

  <!-- FullCalendar con timeline -->
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #f8f9fa;
    }
    #calendar {
      max-width: 95%;
      margin: 20px auto;
    }
  </style>
</head>
<body>
  <h2 style="text-align:center; margin:20px;">Agenda Imagenología</h2>
  <div id="calendar"></div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source', // licencia open source
      initialView: 'resourceTimeGridDay', // vista con recursos (servicios) en columnas
      locale: 'es',
      slotMinTime: "07:00:00", // inicio del día
      slotMaxTime: "23:00:00", // fin del día
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,resourceTimeGridDay,resourceTimeGridWeek'
      },
      resources: 'recursos_json.php',  // servicios/modalidades
      events: 'citas_json.php'         // citas por día/servicio
    });
    calendar.render();
  });
  </script>
</body>
</html>
