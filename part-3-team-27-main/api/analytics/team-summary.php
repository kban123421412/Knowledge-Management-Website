<?php

require_once __DIR__ . '/../helpers.php';
requireApiAuth();

if (getRequestMethod() !== 'GET') {
    jsonError('Method not allowed', 405);
}

$role = getSessionRole();
$userId = getSessionUserId();
$teamId = getSessionTeamId();

if ($role !== 'project_manager' && $role !== 'team_leader') {
    jsonError('Access denied', 403);
}

$tasks = getFilteredTasks($userId, $role, $teamId);
$summary = getTeamSummary($tasks);

jsonResponse($summary);
