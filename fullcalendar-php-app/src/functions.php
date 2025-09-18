<?php
function fetchEvents($pdo) {
    $stmt = $pdo->prepare("SELECT id, title, start, end FROM events");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchResources($pdo) {
    $stmt = $pdo->prepare("SELECT id, title FROM resources");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>