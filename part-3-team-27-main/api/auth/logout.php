<?php

require_once __DIR__ . '/../helpers.php';

if (getRequestMethod() !== 'POST') {
    jsonError('Method not allowed', 405);
}

session_destroy();
jsonResponse(['message' => 'Logged out']);
