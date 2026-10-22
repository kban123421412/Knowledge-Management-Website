<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../dbConfig.php';
requireApiAuth();

$members = $_POST['members'] ?? null;
$gcName = $_POST['groupchat_name'] ?? null;
$madeBy = $_POST['made_by'] ?? null;

if (!$members) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: members']);
    exit;
}

if (!$madeBy) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: made_by']);
    exit;
}
$members = explode(",", $members);

// add madeby
if (!in_array($madeBy, $members)) {
    $members[] = $madeBy;
}

$isGroup = count($members) > 2 ? true : false;


if ($isGroup && !$gcName) {
    http_response_code(400);
    echo json_encode(['error' => 'Group chats require a name']);
    exit;
}

if (!$isGroup && count($members) != 2) {
    http_response_code(400);
    echo json_encode(['error' => 'private chats require exactly 2 members']);
    exit;
}

if (!$isGroup && $gcName) {
    http_response_code(400);
    echo json_encode(['error' => 'private chats cannot have a group name']);
    exit;
}

if ($gcName) {
    // $content = mysqli_real_escape_string($conn, $content);
    $gcName = mysqli_real_escape_string($conn, $gcName);

    $query = "INSERT INTO conversations (name, is_group) 
          VALUES ('$gcName', 1)";

    if (mysqli_query($conn, $query)) {
        $gcId = mysqli_insert_id($conn);
        $timestamp = date('Y-m-d H:i:s');

        $query = "INSERT INTO conversation_members (conversation_id, user_id) VALUES ";
        $values = [];
        // parse members
        // echo "Members: " . $members . "\n";
        foreach ($members as $memberId) {
            $values[] = "($gcId, $memberId)";
        }
        $query .= implode(", ", $values);
        mysqli_query($conn, $query);


        http_response_code(200);
        echo json_encode(['success' => true, 'conversation_id' => $gcId, 'timestamp' => $timestamp]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save message']);
    }



} else {
    // just a private chat
    $query = "INSERT INTO conversations (is_group)
          VALUES (0)";
    if (mysqli_query($conn, $query)) {
        $convId = mysqli_insert_id($conn);
        $timestamp = date('Y-m-d H:i:s');
        $query = "INSERT INTO conversation_members (conversation_id, user_id) VALUES ($convId, $members[0]), ($convId, $members[1])";
        mysqli_query($conn, $query);
        http_response_code(200);
        echo json_encode(['success' => true, 'conversation_id' => $convId, 'timestamp' => $timestamp]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save message']);
    }

}
