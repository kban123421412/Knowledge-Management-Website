<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin() {
    if (empty($_SESSION['loggedIn'])) {
        header('Location: /loginPage.php');
        exit();
    }
}

function getSessionUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getSessionRole() {
    return $_SESSION['role'] ?? 'employee';
}

function getSessionTeamId() {
    return $_SESSION['team_id'] ?? null;
}

function getSessionName() {
    return $_SESSION['name'] ?? 'User';
}

function isManager() {
    $role = getSessionRole();
    return $role === 'project_manager' || $role === 'team_leader';
}
