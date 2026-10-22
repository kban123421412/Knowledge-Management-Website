<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../dbConfig.php';
requireApiAuth();

header('Content-Type: application/json');

// should b POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
    exit;
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'No file provided'
    ]);
    exit;
}

$file = $_FILES['file'];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'mov', 'pdf'];
$maxSize = 50 * 1024 * 1024; // less than 50 mb

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExtensions)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Unsupported file type'
    ]);
    exit;
}

if ($file['size'] > $maxSize) {
    http_response_code(413);
    echo json_encode([
        'success' => false,
        'error' => 'File too large'
    ]);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/messages/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$filepath = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $filepath)) {
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'data' => [
            'url' => '/uploads/messages/' . $filename,
            'filename' => $filename
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to save file'
    ]);
}
?>
