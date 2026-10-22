<?php

require_once __DIR__ . '/../helpers.php';
requireApiAuth();

if (getRequestMethod() !== 'GET') {
    jsonError('Method not allowed', 405);
}

$userId = getSessionUserId();
$teamId = getSessionTeamId();

if ($teamId === null) {
    jsonError('No team assigned', 400);
}

$allTasks = loadTasks();
$teamTasks = array_values(array_filter($allTasks, fn($t) => $t['team_id'] === $teamId));
$metrics = computeMetrics($teamTasks);

jsonResponse($metrics);
