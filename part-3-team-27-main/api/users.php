<?php

require_once __DIR__ . '/helpers.php';
requireApiAuth();

if (getRequestMethod() !== 'GET') {
    jsonError('Method not allowed', 405);
}

$role = getSessionRole();
$teamId = getSessionTeamId();

$employees = getVisibleEmployees($role, $teamId);

$result = array_map(function($u) {
    return [
        'id' => $u['id'],
        'name' => $u['name'],
        'email' => $u['email'],
        'role' => $u['role'],
        'team_id' => $u['team_id'],
        'team_name' => getTeamName($u['team_id']),
    ];
}, $employees);

jsonResponse(array_values($result));
