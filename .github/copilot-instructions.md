# Agenda Hospital - AI Coding Assistant Instructions

## Project Overview
This is a PHP-based hospital appointment scheduling system with FullCalendar integration for medical imaging services (Radiografía, Resonancia Magnética, Tomografía, etc.). The system manages appointments across different modalities/resources with time-based scheduling.

## Architecture & Key Components

### Database Structure (MySQL)
- **Core tables**: `pacientes`, `profesionales`, `servicios`, `modalidades`, `citas`, `estado_cita`
- **Key relationships**: Citas link patients to professionals and services with specific modalities
- **States**: `reservado`, `confirmado`, `asistió`, `no asistió`, `pendiente`, `en espera`
- **Connection**: Standard mysqli in `/includes/db.php` (MAMP defaults: root/root)

### File Organization Patterns
```
/citas/           # API endpoints for appointment operations
  citas_json.php       # Events data for FullCalendar
  recursos_json.php    # Resources/modalities for calendar
  guardar_cita.php     # Appointment creation with overlap validation
  guardar_paciente.php # Patient registration
  
/fullcalendar-php-app/public/  # Duplicate endpoints for FullCalendar
/includes/        # Shared database connection
index.php         # Main SPA with embedded calendar UI
```

### Frontend Architecture
- **Single-page application** in `index.php` (~700 lines) with embedded HTML, CSS, JS
- **FullCalendar v5** with resource scheduler for modality-based appointment display
- **Dual calendar views**: Main resourceTimeGrid + mini calendar navigation (Flatpickr)
- **Modal-based workflows** for appointment creation and patient registration
- **AJAX pattern**: Fetch API calls to `/citas/*.php` endpoints returning JSON

## Critical Development Patterns

### Appointment Overlap Prevention
```php
// Key pattern in guardar_cita.php - checks time conflicts within same modality
$sqlEmpalme = "SELECT COUNT(*) as total FROM citas WHERE fecha = ? AND modalidad_id = ? 
               AND ((hora_inicio < ? AND hora_fin > ?) OR ...)";
```

### JSON API Convention
- All endpoints return `{"success": boolean, "error": string}` format
- FullCalendar expects specific event format: `{id, title, start, end, resourceId, color}`
- Resources format: `{id, title}` from modalidades table

### State Management
- **Calendar state**: Managed by FullCalendar instance with dynamic resource/event filtering
- **UI state**: Modality selection drives both calendar resources AND service dropdown
- **Filter persistence**: Estado (appointment status) filter maintained across view changes

### CSS/Styling Approach
- **Bootstrap 4.6.2** for form components and layout
- **Embedded styles** in index.php for calendar customizations
- **Tooltip system**: Custom implementation with mouseover event handlers
- **Responsive**: Sidebar (320px fixed) + flexible calendar area

## Development Workflow

### Local Environment (MAMP)
```bash
# Database setup
mysql -u root -p < agenda_hospital.sql

# File structure expects MAMP htdocs structure
/Applications/MAMP/htdocs/agenda/
```

### Adding New Features
1. **Database changes**: Update `agenda_hospital.sql` with new tables/fields
2. **API endpoints**: Create new files in `/citas/` folder following JSON response pattern
3. **Frontend integration**: Modify the large JavaScript block in `index.php`
4. **Calendar integration**: Update FullCalendar configuration for new event types/resources

### Testing Patterns
- **Manual testing**: Use browser dev tools with calendar interactions
- **Database testing**: Check appointment overlap logic with edge cases
- **JSON validation**: Verify API responses match FullCalendar expected format

## Common Gotchas
- **Duplicate endpoints**: Both `/citas/` and `/fullcalendar-php-app/public/` serve similar functions
- **Time handling**: Mix of MySQL TIME format and JavaScript Date objects
- **Resource filtering**: Modality selection affects both calendar display AND form options
- **Modal state**: Multiple modals can conflict if not properly managed
- **Timezone**: No explicit timezone handling - assumes local time

## Integration Points
- **FullCalendar v5**: Core dependency for calendar functionality
- **Flatpickr**: Mini calendar navigation components
- **Bootstrap Timepicker**: For appointment time selection
- **MySQL**: Direct mysqli connections, no ORM

When modifying this system, maintain the established patterns of embedded JavaScript, JSON API responses, and the dual-calendar approach with resource-based filtering.