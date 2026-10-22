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
-- groups
SELECT 
    c.id as conversation_id,
    c.name,
    c.is_group,
    NULL as contact_id,
    CAST(c.name AS CHAR CHARACTER SET utf8mb4) as contact_name,
    CAST(NULL AS CHAR CHARACTER SET utf8mb4) as email,
    CAST(SUBSTRING(c.name, 1, 1) AS CHAR CHARACTER SET utf8mb4) as avatar_letter
FROM conversations c
JOIN conversation_members cm ON c.id = cm.conversation_id
WHERE cm.user_id = ? AND c.is_group = 1

UNION ALL

SELECT
    c.id as conversation_id,
    CAST(c.name AS CHAR CHARACTER SET utf8mb4),
    c.is_group,
    u.id as contact_id,
    CAST(u.name AS CHAR CHARACTER SET utf8mb4) as contact_name,
    CAST(u.email AS CHAR CHARACTER SET utf8mb4),
    CAST(SUBSTRING(u.name, 1, 1) AS CHAR CHARACTER SET utf8mb4) as avatar_letter
FROM conversations c
JOIN conversation_members cm ON c.id = cm.conversation_id
JOIN conversation_members cm2 ON c.id = cm2.conversation_id AND cm2.user_id != ?
JOIN users u ON cm2.user_id = u.id
WHERE cm.user_id = ? AND c.is_group = 0
";

$statement = $conn->prepare($query);
$statement->bind_param("iii", $currentUserId, $currentUserId, $currentUserId);
$statement->execute();
$result = $statement->get_result();

$conversations = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $conversationId = $row['conversation_id'];
        $isGroup = $row['is_group'];
        
        if (!isset($conversations[$conversationId])) {
            $idForHash = $isGroup ? $conversationId : $row['contact_id'];
            
            $conversations[$conversationId] = [
                'id' => $conversationId,
                'name' => $row['contact_name'],
                'email' => $row['email'],
                'contactId' => $row['contact_id'] ? (int)$row['contact_id'] : null,
                'isGroup' => (bool)$isGroup,
                'avatarLetter' => $row['avatar_letter'],
                'avatarClass' => '',
                'messages' => [],
                'members' => [],
            ];

                // Fetch members for this conversation
            $membersQuery = "
                SELECT u.id, u.name, u.email
                FROM users u
                JOIN conversation_members cm ON u.id = cm.user_id
                WHERE cm.conversation_id = ?
                ORDER BY u.name ASC
            ";
            $stmtMember = $conn->prepare($membersQuery);
            $stmtMember->bind_param("i", $conversationId);
            $stmtMember->execute();
            $membersResult = $stmtMember->get_result();
            
            while ($member = $membersResult->fetch_assoc()) {
                $conversations[$conversationId]['members'][] = [
                    'id' => $member['id'],
                    'name' => $member['name'],
                    'email' => $member['email'],
                ];
            }
            
            //Change to messages.php
            // Fetch all messages for this conversation
            $msgQuery = "
                SELECT m.id, m.sender_id, m.content, m.created_at, m.deleted_at, u.name AS sender_name
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                WHERE m.conversation_id = ?
                ORDER BY m.created_at DESC
                LIMIT 10
            ";
            $stmtMsg = $conn->prepare($msgQuery);
            $stmtMsg->bind_param("i", $conversationId);
            $stmtMsg->execute();
            $msgResult = $stmtMsg->get_result();
            
            while ($msg = $msgResult->fetch_assoc()) {
                $conversations[$conversationId]['messages'][] = [
                    'id' => $msg['id'],
                    'direction' => $msg['sender_id'] == $currentUserId ? 'outgoing' : 'incoming',
                    'by' => $msg['sender_id'],
                    'senderName' => $msg['sender_name'],
                    'text' => $msg['deleted_at'] ? '' : $msg['content'],
                    'timestamp' => $msg['created_at'],
                    'deleted' => $msg['deleted_at'] !== null,
                ];
            }
        }
    }
}

echo json_encode(["success" => true, "conversations" => array_values($conversations) ?? []]);
?>
