<?php
session_start();
require_once __DIR__ . '/../dbConfig.php';

header('Content-Type: application/json');

if (!isset($_SESSION['loggedIn'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorised']);
    exit;
}

$userId = $_SESSION['user_id'];

$result = mysqli_query($conn, "
    SELECT 
        c.id,
        CASE WHEN c.is_group = 1 THEN c.name ELSE u.name END AS contact_name,
        CASE WHEN c.is_group = 1 THEN SUBSTRING(c.name, 1, 1) ELSE SUBSTRING(u.name, 1, 1) END AS avatar_letter,
        c.is_group,
        cm.hidden_at,
        (SELECT COUNT(*) FROM messages m WHERE m.conversation_id = c.id AND m.deleted_at IS NULL) AS message_count
    FROM conversations c
    JOIN conversation_members cm ON c.id = cm.conversation_id AND cm.user_id = $userId
    LEFT JOIN conversation_members cm2 ON c.id = cm2.conversation_id AND cm2.user_id != $userId AND c.is_group = 0
    LEFT JOIN users u ON cm2.user_id = u.id
    WHERE cm.hidden_at IS NOT NULL
    ORDER BY cm.hidden_at DESC
");
$conversations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $conversations[] = $row;
}

echo json_encode(['success' => true, 'conversations' => $conversations]);