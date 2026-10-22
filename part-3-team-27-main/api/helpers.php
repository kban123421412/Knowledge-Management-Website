<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../dbConfig.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../analytics/analyticsData.php';

header('Content-Type: application/json');

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode(['success' => true, 'data' => $data]);
    exit();
}

function jsonError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit();
}

function requireApiAuth() {
    if (empty($_SESSION['loggedIn'])) {
        jsonError('Not authenticated', 401);
    }
}

function getRequestMethod() {
    return $_SERVER['REQUEST_METHOD'];
}

function getJsonBody() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}
