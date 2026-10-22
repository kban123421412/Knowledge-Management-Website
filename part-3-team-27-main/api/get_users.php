<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../dbConfig.php';
requireApiAuth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Method not allowed', 405);
}

$currentUserId = $_GET['currentUserId'] ?? null; //Need currentUser to get all but currentUser

if(!$currentUserId){
    jsonError('Missing currentUserId parameter', 400);
}

$query = "
    SELECT id, name, email, role
    FROM users
    WHERE id != ?
    ORDER BY name ASC
";

$statement = $conn->prepare($query);
$statement->bind_param("i", $currentUserId);
$statement->execute();

$result = $statement->get_result();

$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
 
echo json_encode(["success" => true, "users" => $users]);
?>