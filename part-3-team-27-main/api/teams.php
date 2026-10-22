<?php

require_once __DIR__ . '/helpers.php';
requireApiAuth();

if (getRequestMethod() !== 'GET') {
    jsonError('Method not allowed', 405);
}

$role = getSessionRole();
$teamId = getSessionTeamId();

$teams = getVisibleTeams($role, $teamId);

jsonResponse(array_values($teams));
