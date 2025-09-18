<?php
// filepath: /fullcalendar-php-app/fullcalendar-php-app/public/recursos_json.php

require_once '../includes/db.php';

// Define getDatabaseConnection if not already defined
if (!function_exists('getDatabaseConnection')) {
    function getDatabaseConnection() {
        $host = 'localhost';
        $db   = 'your_database_name';
        $user = 'your_db_user';
        $pass = 'your_db_password';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            return new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}

header('Content-Type: application/json');

try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT id, name FROM resources");
    $stmt->execute();
    
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($resources);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>