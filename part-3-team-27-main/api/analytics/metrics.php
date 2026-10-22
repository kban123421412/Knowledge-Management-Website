<?php

require_once __DIR__ . '/../helpers.php';
requireApiAuth();

if (getRequestMethod() !== 'GET') {
    jsonError('Method not allowed', 405);
}

$userId = getSessionUserId();
$role = getSessionRole();
$teamId = getSessionTeamId();

$metrics = computeMetricsSQL($userId, $role, $teamId);

jsonResponse($metrics);
