<?php

require_once __DIR__ . '/helpers.php';
requireApiAuth();

$method = getRequestMethod();
$userId = getSessionUserId();
$role = getSessionRole();
$teamId = getSessionTeamId();

switch ($method) {
    case 'GET':
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int)($_GET['per_page'] ?? 25)));
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $filterTeam = $_GET['team'] ?? '';
        $priority = $_GET['priority'] ?? '';
        $employee = $_GET['employee'] ?? '';
        $all = $_GET['all'] ?? '';

        if ($all === '1') {
            $tasks = getFilteredTasks($userId, $role, $teamId);
            $enriched = array_map(function($t) {
                $t['assigned_to_name'] = getUserName($t['assigned_to']);
                $t['team_name'] = getTeamName($t['team_id']);
                return $t;
            }, $tasks);
            jsonResponse(array_values($enriched));
        }

        $result = getFilteredTasksPaginated($userId, $role, $teamId, $page, $perPage, $search, $status, $filterTeam, $priority, $employee);

        $enriched = array_map(function($t) {
            $t['assigned_to_name'] = getUserName($t['assigned_to']);
            $t['team_name'] = getTeamName($t['team_id']);
            return $t;
        }, $result['tasks']);

        jsonResponse([
            'tasks' => array_values($enriched),
            'total' => $result['total'],
            'page' => $result['page'],
            'per_page' => $result['per_page'],
            'total_pages' => $result['total_pages'],
        ]);
        break;

    case 'POST':
        if ($role !== 'project_manager' && $role !== 'team_leader') {
            jsonError('Only managers and team leaders can create tasks', 403);
        }

        $body = getJsonBody();
        $title = trim($body['title'] ?? '');
        $description = trim($body['description'] ?? '');
        $assignedTo = (int)($body['assigned_to'] ?? 0);
        $taskTeamId = (int)($body['team_id'] ?? 0);
        $priority = $body['priority'] ?? 'medium';
        $difficulty = $body['difficulty'] ?? 'medium';
        $dueDate = !empty($body['due_date']) ? $body['due_date'] : null;

        if (empty($title) || $assignedTo <= 0 || $taskTeamId <= 0) {
            jsonError('Title, assigned_to, and team_id are required');
        }

        global $conn;
        $stmt = mysqli_prepare($conn, "INSERT INTO tasks (title, description, assigned_to, team_id, priority, difficulty, status, created_at, due_date) VALUES (?, ?, ?, ?, ?, ?, 'not_started', CURDATE(), ?)");
        mysqli_stmt_bind_param($stmt, "ssiisss", $title, $description, $assignedTo, $taskTeamId, $priority, $difficulty, $dueDate);
        $success = mysqli_stmt_execute($stmt);
        $newId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        if (!$success) {
            jsonError('Failed to create task', 500);
        }

        jsonResponse([
            'id' => $newId,
            'title' => $title,
            'description' => $description,
            'assigned_to' => $assignedTo,
            'team_id' => $taskTeamId,
            'priority' => $priority,
            'difficulty' => $difficulty,
            'status' => 'not_started',
            'created_at' => date('Y-m-d'),
            'due_date' => $dueDate,
            'completed_at' => null,
        ], 201);
        break;

    case 'PATCH':
        $taskId = (int)($_GET['id'] ?? 0);
        if ($taskId <= 0) {
            jsonError('Task ID is required');
        }

        global $conn;
        $stmt = mysqli_prepare($conn, "UPDATE tasks SET status = 'completed', completed_at = CURDATE() WHERE id = ? AND assigned_to = ?");
        mysqli_stmt_bind_param($stmt, "ii", $taskId, $userId);
        $success = mysqli_stmt_execute($stmt);
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($affected === 0) {
            jsonError('Task not found or not assigned to you', 404);
        }

        jsonResponse(['message' => 'Task marked as completed', 'task_id' => $taskId]);
        break;

    default:
        jsonError('Method not allowed', 405);
}
