<?php
session_start(); //Need to fix our file structure
require_once "pageConfig.php";

$requestedPage = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestedPage);
$path = substr($parsedUrl['path'], strlen('/subsystems.php/'));
$pageKey = explode('/', $path)[0] ?? 'textchat';
$currentPage = Pages::tryFrom($pageKey) ?? Pages::TEXTCHAT;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="subsystem.css">
    <link rel="stylesheet" href="topnavbar.css">
    <title>MakeItAll Subsystems</title>
</head>

<body>

<?php 
include "topnavbar.php";

switch ($currentPage) {
    case Pages::TEXTCHAT:
        include "subsystem/textchat.php";
        break;

    case Pages::ANALYTICS:
        include "managerAnalytics.php";
        break;
    
}
?>



</body>

<?php include __DIR__ . '/modals.php'; ?>

</html>