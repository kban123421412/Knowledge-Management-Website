<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../dbConfig.php';
requireApiAuth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$body = json_decode(file_get_contents('php://input'), true);
$conversationId = $body['conversation_id'] ?? null;
if (!$conversationId) {
    jsonError('Missing conversation_id', 400);
}

$userId = $_SESSION['user_id'] ?? null; // however you resolve the authed user
$statement = $conn->prepare("
    DELETE FROM conversation_members
    WHERE conversation_id = ? AND user_id = ?
");
$statement->bind_param("ii", $conversationId, $userId);
$statement->execute();
echo json_encode(["success" => true]);
?>