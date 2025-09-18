<?php
require_once '../src/db.php';

header('Content-Type: application/json');

try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT id, nombre AS title FROM modalidades");
    $stmt->execute();
    $modalidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($modalidades);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>