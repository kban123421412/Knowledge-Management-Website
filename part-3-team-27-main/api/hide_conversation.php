<?php
session_start();
require_once __DIR__ . '/../dbConfig.php';

header('Content-Type: application/json');

if (!isset($_SESSION['loggedIn'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorised']);
    exit;
}

$userId         = $_SESSION['user_id'];
$conversationId = $_POST['conversation_id'] ?? null;

if (!$conversationId) {
    echo json_encode(['success' => false, 'error' => 'Missing conversation_id']);
    exit;
}

$stmt = mysqli_prepare($conn, "
    UPDATE conversation_members 
    SET hidden_at = NOW() 
    WHERE conversation_id = ? AND user_id = ?
");
mysqli_stmt_bind_param($stmt, 'ii', $conversationId, $userId);
mysqli_stmt_execute($stmt);

echo json_encode(['success' => true]);