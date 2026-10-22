<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../dbConfig.php';
requireApiAuth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Method not allowed', 405);
}

$conversationId = $_GET['conversation_id'] ?? null;
if (!$conversationId) {
    jsonError('Missing conversation_id parameter', 400);
}

$before = $_GET['before'] ?? null; // For pagination, get messages before this ID

//Query to get last 10 messages for the conversation - most recent first.
$query = "
    SELECT m.id, m.conversation_id, m.sender_id, m.content, m.message_type, m.created_at, u.name as sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.conversation_id = ?
    AND m.id < ?
    AND m.deleted_at IS NULL
    ORDER BY m.created_at DESC
    LIMIT 10
";

$statement = $conn->prepare($query);
$statement->bind_param("ii", $conversationId, $before);
$statement->execute();

$result = $statement->get_result();

$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
 
echo json_encode(["success" => true, "messages" => $messages]);
?>
