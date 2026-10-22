<?php
session_start();
require_once __DIR__ . '/../data/sampleData.php';

if (empty($_SESSION['loggedIn'])) {
    header('Location: /loginPage.php');
    exit();
}

if (!isset($_SESSION['task_overrides'])) {
    $_SESSION['task_overrides'] = ['created' => [], 'completed' => []];
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'complete':
        $taskId = (int)($_POST['task_id'] ?? 0);
        if ($taskId > 0) {
            $_SESSION['task_overrides']['completed'][] = $taskId;
        }
        header('Location: /employeeAnalytics.php');
        exit();

    case 'create':
        $role = $_SESSION['role'] ?? '';
        if ($role !== 'project_manager' && $role !== 'team_leader') {
            header('Location: /loginPage.php');
            exit();
        }

        $allTasks = $tasks;
        $existingCreated = $_SESSION['task_overrides']['created'];
        $maxId = 30;
        foreach ($existingCreated as $ct) {
            if ($ct['id'] > $maxId) $maxId = $ct['id'];
        }

        $newTask = [
            'id' => $maxId + 1,
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'assigned_to' => (int)($_POST['assigned_to'] ?? 0),
            'team_id' => (int)($_POST['team_id'] ?? 0),
            'priority' => $_POST['priority'] ?? 'medium',
            'difficulty' => $_POST['difficulty'] ?? 'medium',
            'status' => 'not_started',
            'created_at' => date('Y-m-d'),
            'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null,
            'completed_at' => null,
        ];

        if (!empty($newTask['title']) && $newTask['assigned_to'] > 0) {
            $_SESSION['task_overrides']['created'][] = $newTask;
            $_SESSION['flash_success'] = 'Task created successfully.';
        }

        header('Location: /managerAnalytics.php?tab=tasks');
        exit();

    default:
        header('Location: /loginPage.php');
        exit();
}
