<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../dbConfig.php';
requireApiAuth();

header('Content-Type: application/json');

$conversationId = $_POST['conversation_id'] ?? null;
$content = $_POST['content'] ?? null;
$senderId = $_SESSION['user_id'] ?? null;

if (!$conversationId || !$content || !$senderId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$messageType = 'text';
if (filter_var($content, FILTER_VALIDATE_URL)) {
    $path = parse_url($content, PHP_URL_PATH);
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $messageType = 'image';
    } elseif (in_array($ext, ['mp4', 'webm', 'mov'])) {
        $messageType = 'video';
    } else {
        $messageType = 'file';
    }
}

$stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, content, message_type, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("iiss", $conversationId, $senderId, $content, $messageType);

if ($stmt->execute()) {
    $messageId = $conn->insert_id;
    $timestamp = date('Y-m-d H:i:s');
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'data' => [
            'message_id' => $messageId,
            'timestamp' => $timestamp,
            'type' => $messageType
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save message']);
}
