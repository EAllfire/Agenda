# FullCalendar PHP App

## Overview
This project is a simple PHP application that integrates FullCalendar to display events fetched from a database. It includes endpoints for retrieving event data and additional resources, making it easy to manage and visualize calendar events.

## Project Structure
```
fullcalendar-php-app
├── public
│   ├── index.php
│   ├── citas_json.php
│   └── recursos_json.php
├── src
│   ├── db.php
│   └── functions.php
├── composer.json
└── README.md
```

## Requirements
- PHP 7.4 or higher
- Composer
- A web server (e.g., Apache, Nginx)
- A database (e.g., MySQL)

## Installation
1. Clone the repository:
   ```
   git clone <repository-url>
   cd fullcalendar-php-app
   ```

2. Install dependencies using Composer:
   ```
   composer install
   ```

3. Configure your database connection in `src/db.php`. Update the credentials to match your database setup.

4. Import the database schema (if provided) to create the necessary tables for events and resources.

5. Start your web server and navigate to `public/index.php` to view the application.

## Usage
- The main calendar interface is available at `public/index.php`.
- Events are fetched from `public/citas_json.php` and displayed on the calendar.
- Additional resources can be retrieved from `public/recursos_json.php`.

## Contributing
Feel free to submit issues or pull requests for improvements or bug fixes.

## License
This project is open-source and available under the MIT License.