<?php
/**
 * AJAX Endpoint: Get Destinations by Category
 * URL: /public/ajax-destinations.php?category_id=X
 */

require_once '../bootstrap.php';

header('Content-Type: application/json');

$category_id = $_GET['category_id'] ?? null;

if (!$category_id) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, name 
        FROM destinations
        WHERE category_id = :category_id 
          AND status = 'active'
        ORDER BY name ASC
    ");
    $stmt->execute(['category_id' => $category_id]);
    $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($destinations);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
