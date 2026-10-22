<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../dbConfig.php';
requireApiAuth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$messageId = $_POST['message_id'] ?? null;
$userId = $_SESSION['user_id'] ?? null;

if (!$messageId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing message_id']);
    exit;
}

$stmt = $conn->prepare("UPDATE messages SET deleted_at = NOW() WHERE id = ? AND sender_id = ?");
$stmt->bind_param("ii", $messageId, $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Message not found or not yours']);
}
