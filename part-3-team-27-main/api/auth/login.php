<?php

require_once __DIR__ . '/../helpers.php';

if (getRequestMethod() !== 'POST') {
    jsonError('Method not allowed', 405);
}

$body = getJsonBody();
$email = trim($body['email'] ?? '');
$password = trim($body['password'] ?? '');

if (empty($email) || empty($password)) {
    jsonError('Email and password are required');
}

global $conn;
$stmt = mysqli_prepare($conn, "SELECT id, email, password, name, role, team_id FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user || $user['password'] !== $password) {
    jsonError('Invalid email or password', 401);
}

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['username'] = $user['email'];
$_SESSION['name'] = $user['name'];
$_SESSION['role'] = $user['role'];
$_SESSION['team_id'] = $user['team_id'] !== null ? (int)$user['team_id'] : null;
$_SESSION['loggedIn'] = true;

jsonResponse([
    'id' => (int)$user['id'],
    'email' => $user['email'],
    'name' => $user['name'],
    'role' => $user['role'],
    'team_id' => $user['team_id'] !== null ? (int)$user['team_id'] : null,
]);
