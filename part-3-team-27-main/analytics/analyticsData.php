<?php

require_once __DIR__ . '/../dbConfig.php';

function loadTeams() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM teams ORDER BY id");
    $teams = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $teams[(int)$row['id']] = $row;
    }
    return $teams;
}

function loadUsers() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM users ORDER BY id");
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['id'] = (int)$row['id'];
        $row['team_id'] = $row['team_id'] !== null ? (int)$row['team_id'] : null;
        $users[$row['id']] = $row;
    }
    return $users;
}

function loadTasks() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM tasks ORDER BY created_at DESC");
    $tasks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['id'] = (int)$row['id'];
        $row['assigned_to'] = (int)$row['assigned_to'];
        $row['team_id'] = (int)$row['team_id'];
        $tasks[] = $row;
    }
    return $tasks;
}

function buildWhereClause($sessionUserId, $sessionRole, $sessionTeamId) {
    switch ($sessionRole) {
        case 'project_manager':
            return '';
        case 'team_leader':
            return "WHERE t.team_id = " . (int)$sessionTeamId;
        case 'employee':
            return "WHERE t.assigned_to = " . (int)$sessionUserId;
        default:
            return "WHERE 1=0";
    }
}

function getFilteredTasks($sessionUserId, $sessionRole, $sessionTeamId) {
    global $conn;
    $where = buildWhereClause($sessionUserId, $sessionRole, $sessionTeamId);
    $sql = "SELECT t.* FROM tasks t $where ORDER BY t.created_at DESC";
    $result = mysqli_query($conn, $sql);
    $tasks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['id'] = (int)$row['id'];
        $row['assigned_to'] = (int)$row['assigned_to'];
        $row['team_id'] = (int)$row['team_id'];
        $tasks[] = $row;
    }
    return $tasks;
}

function getFilteredTasksPaginated($sessionUserId, $sessionRole, $sessionTeamId, $page = 1, $perPage = 25, $search = '', $status = '', $teamId = '', $priority = '', $employeeId = '') {
    global $conn;
    $where = buildWhereClause($sessionUserId, $sessionRole, $sessionTeamId);
    $conditions = [];

    if ($search !== '') {
        $escaped = mysqli_real_escape_string($conn, $search);
        $conditions[] = "(t.title LIKE '%$escaped%' OR t.description LIKE '%$escaped%')";
    }
    if ($status !== '' && $status !== 'all') {
        if ($status === 'overdue') {
            $conditions[] = "t.status != 'completed' AND t.due_date IS NOT NULL AND t.due_date < CURDATE()";
        } else {
            $escaped = mysqli_real_escape_string($conn, $status);
            $conditions[] = "t.status = '$escaped'";
        }
    }
    if ($teamId !== '' && $teamId !== 'all') {
        $conditions[] = "t.team_id = " . (int)$teamId;
    }
    if ($priority !== '' && $priority !== 'all') {
        $escaped = mysqli_real_escape_string($conn, $priority);
        $conditions[] = "t.priority = '$escaped'";
    }
    if ($employeeId !== '' && $employeeId !== 'all') {
        $conditions[] = "t.assigned_to = " . (int)$employeeId;
    }

    $extraWhere = '';
    if (count($conditions) > 0) {
        $extraWhere = ($where === '' ? 'WHERE ' : ' AND ') . implode(' AND ', $conditions);
    }

    $countSql = "SELECT COUNT(*) as total FROM tasks t $where $extraWhere";
    $countResult = mysqli_query($conn, $countSql);
    $total = (int)mysqli_fetch_assoc($countResult)['total'];

    $offset = ($page - 1) * $perPage;
    $sql = "SELECT t.* FROM tasks t $where $extraWhere ORDER BY t.created_at DESC LIMIT $perPage OFFSET $offset";
    $result = mysqli_query($conn, $sql);
    $tasks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['id'] = (int)$row['id'];
        $row['assigned_to'] = (int)$row['assigned_to'];
        $row['team_id'] = (int)$row['team_id'];
        $tasks[] = $row;
    }

    return [
        'tasks' => $tasks,
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => (int)ceil($total / $perPage),
    ];
}

function computeMetrics($filteredTasks) {
    $today = date('Y-m-d');
    $totalAssigned = count($filteredTasks);
    $completed = array_filter($filteredTasks, fn($t) => $t['status'] === 'completed');
    $totalCompleted = count($completed);
    $completionRate = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100, 1) : 0;

    $onTime = 0;
    $late = 0;
    foreach ($completed as $t) {
        if ($t['due_date'] === null) continue;
        if ($t['completed_at'] <= $t['due_date']) {
            $onTime++;
        } else {
            $late++;
        }
    }

    $overdue = count(array_filter($filteredTasks, function($t) use ($today) {
        return $t['status'] !== 'completed'
            && $t['due_date'] !== null
            && $t['due_date'] < $today;
    }));

    $completionDays = [];
    foreach ($completed as $t) {
        if ($t['created_at'] && $t['completed_at']) {
            $days = (strtotime($t['completed_at']) - strtotime($t['created_at'])) / 86400;
            $completionDays[] = max(0, $days);
        }
    }
    $avgCompletionDays = count($completionDays) > 0 ? round(array_sum($completionDays) / count($completionDays), 1) : 0;

    $inProgress = count(array_filter($filteredTasks, fn($t) => $t['status'] === 'in_progress'));
    $notStarted = count(array_filter($filteredTasks, fn($t) => $t['status'] === 'not_started'));

    return [
        'total_assigned' => $totalAssigned,
        'total_completed' => $totalCompleted,
        'completion_rate' => $completionRate,
        'completed_on_time' => $onTime,
        'completed_late' => $late,
        'overdue' => $overdue,
        'avg_completion_days' => $avgCompletionDays,
        'in_progress' => $inProgress,
        'not_started' => $notStarted,
    ];
}

function computeMetricsSQL($sessionUserId, $sessionRole, $sessionTeamId) {
    global $conn;
    $where = buildWhereClause($sessionUserId, $sessionRole, $sessionTeamId);

    $sql = "SELECT
        COUNT(*) as total_assigned,
        SUM(t.status = 'completed') as total_completed,
        SUM(t.status = 'in_progress') as in_progress,
        SUM(t.status = 'not_started') as not_started,
        SUM(t.status = 'completed' AND t.due_date IS NOT NULL AND t.completed_at <= t.due_date) as completed_on_time,
        SUM(t.status = 'completed' AND t.due_date IS NOT NULL AND t.completed_at > t.due_date) as completed_late,
        SUM(t.status != 'completed' AND t.due_date IS NOT NULL AND t.due_date < CURDATE()) as overdue,
        AVG(CASE WHEN t.status = 'completed' AND t.created_at IS NOT NULL AND t.completed_at IS NOT NULL
            THEN DATEDIFF(t.completed_at, t.created_at) END) as avg_completion_days
        FROM tasks t $where";

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    $totalAssigned = (int)$row['total_assigned'];
    $totalCompleted = (int)$row['total_completed'];

    return [
        'total_assigned' => $totalAssigned,
        'total_completed' => $totalCompleted,
        'completion_rate' => $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100, 1) : 0,
        'completed_on_time' => (int)$row['completed_on_time'],
        'completed_late' => (int)$row['completed_late'],
        'overdue' => (int)$row['overdue'],
        'avg_completion_days' => round((float)($row['avg_completion_days'] ?? 0), 1),
        'in_progress' => (int)$row['in_progress'],
        'not_started' => (int)$row['not_started'],
    ];
}

function getWeeklyTrends($filteredTasks, $weeks = 8) {
    $result = [];
    $thisMonday = strtotime('monday this week');

    for ($i = $weeks - 1; $i >= 0; $i--) {
        $weekStart = date('Y-m-d', strtotime("-{$i} weeks", $thisMonday));
        $weekEnd = date('Y-m-d', strtotime('+6 days', strtotime($weekStart)));
        $label = date('M j', strtotime($weekStart));

        $count = count(array_filter($filteredTasks, function($t) use ($weekStart, $weekEnd) {
            return $t['status'] === 'completed'
                && $t['completed_at'] !== null
                && $t['completed_at'] >= $weekStart
                && $t['completed_at'] <= $weekEnd;
        }));

        $result[] = ['label' => $label, 'count' => $count];
    }
    return $result;
}

function getWeeklyTrendsSQL($sessionUserId, $sessionRole, $sessionTeamId, $weeks = 8) {
    global $conn;
    $where = buildWhereClause($sessionUserId, $sessionRole, $sessionTeamId);
    $thisMonday = strtotime('monday this week');
    $result = [];

    for ($i = $weeks - 1; $i >= 0; $i--) {
        $weekStart = date('Y-m-d', strtotime("-{$i} weeks", $thisMonday));
        $weekEnd = date('Y-m-d', strtotime('+6 days', strtotime($weekStart)));
        $label = date('M j', strtotime($weekStart));

        $extraWhere = ($where === '' ? 'WHERE' : "$where AND");
        $sql = "SELECT COUNT(*) as cnt FROM tasks t $extraWhere t.status = 'completed' AND t.completed_at >= '$weekStart' AND t.completed_at <= '$weekEnd'";
        $res = mysqli_query($conn, $sql);
        $count = (int)mysqli_fetch_assoc($res)['cnt'];

        $result[] = ['label' => $label, 'count' => $count];
    }
    return $result;
}

function getTeamSummary($filteredTasks) {
    $teams = loadTeams();
    $summary = [];

    foreach ($teams as $team) {
        $teamTasks = array_filter($filteredTasks, fn($t) => $t['team_id'] === (int)$team['id']);
        $teamMetrics = computeMetrics($teamTasks);
        $summary[] = [
            'team' => $team,
            'metrics' => $teamMetrics,
        ];
    }

    return $summary;
}

function getMemberSummary($filteredTasks, $teamId = null) {
    $users = loadUsers();
    $summary = [];

    foreach ($users as $user) {
        if ($user['role'] === 'project_manager') continue;
        if ($teamId !== null && $user['team_id'] !== $teamId) continue;

        $userTasks = array_filter($filteredTasks, fn($t) => $t['assigned_to'] === $user['id']);
        if (count($userTasks) === 0) continue;

        $userMetrics = computeMetrics($userTasks);
        $userData = $user;
        $userData['team_name'] = getTeamName($user['team_id']);
        $summary[] = [
            'user' => $userData,
            'metrics' => $userMetrics,
        ];
    }

    return $summary;
}

function getUserName($userId) {
    $users = loadUsers();
    return $users[(int)$userId]['name'] ?? 'Unknown';
}

function getTeamName($teamId) {
    $teams = loadTeams();
    return $teams[(int)$teamId]['name'] ?? 'Unknown';
}

function getVisibleEmployees($sessionRole, $sessionTeamId) {
    $users = loadUsers();
    $employees = [];
    foreach ($users as $user) {
        if ($user['role'] === 'project_manager') continue;
        if ($sessionRole === 'team_leader' && $user['team_id'] !== $sessionTeamId) continue;
        $employees[] = $user;
    }
    return $employees;
}

function getVisibleTeams($sessionRole, $sessionTeamId) {
    $teams = loadTeams();
    if ($sessionRole === 'project_manager') return $teams;
    if ($sessionRole === 'team_leader') {
        return array_filter($teams, fn($t) => (int)$t['id'] === $sessionTeamId);
    }
    return [];
}
