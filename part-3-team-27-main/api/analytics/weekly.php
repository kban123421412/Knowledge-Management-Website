<?php

require_once __DIR__ . '/../helpers.php';
requireApiAuth();

if (getRequestMethod() !== 'GET') {
    jsonError('Method not allowed', 405);
}

$userId = getSessionUserId();
$role = getSessionRole();
$teamId = getSessionTeamId();

$range = $_GET['range'] ?? 'last8weeks';
$validRanges = ['thisweek', 'thismonth', 'last3months', 'last6months', 'thisyear', 'last8weeks', 'custom'];
if (!in_array($range, $validRanges)) $range = 'last8weeks';
$from = preg_replace('/[^0-9\-]/', '', $_GET['from'] ?? '');
$to = preg_replace('/[^0-9\-]/', '', $_GET['to'] ?? '');

$where = buildWhereClause($userId, $role, $teamId);
global $conn;

$result = [];

if ($range === 'thisweek') {
    $monday = date('Y-m-d', strtotime('monday this week'));
    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    for ($i = 0; $i < 7; $i++) {
        $day = date('Y-m-d', strtotime("+{$i} days", strtotime($monday)));
        $extraWhere = ($where === '' ? 'WHERE' : "$where AND");
        $sql = "SELECT COUNT(*) as cnt FROM tasks t $extraWhere t.status = 'completed' AND t.completed_at = '$day'";
        $res = mysqli_query($conn, $sql);
        $count = (int)mysqli_fetch_assoc($res)['cnt'];
        $result[] = ['label' => $days[$i], 'count' => $count];
    }
} elseif ($range === 'thismonth') {
    $firstDay = date('Y-m-01');
    $lastDay = date('Y-m-t');
    $weekNum = 1;
    $current = strtotime($firstDay);
    while ($current <= strtotime($lastDay)) {
        $weekStart = date('Y-m-d', $current);
        $weekEnd = date('Y-m-d', min(strtotime('+6 days', $current), strtotime($lastDay)));
        $extraWhere = ($where === '' ? 'WHERE' : "$where AND");
        $sql = "SELECT COUNT(*) as cnt FROM tasks t $extraWhere t.status = 'completed' AND t.completed_at >= '$weekStart' AND t.completed_at <= '$weekEnd'";
        $res = mysqli_query($conn, $sql);
        $count = (int)mysqli_fetch_assoc($res)['cnt'];
        $result[] = ['label' => 'Week ' . $weekNum, 'count' => $count];
        $current = strtotime('+7 days', $current);
        $weekNum++;
    }
} elseif ($range === 'last3months' || $range === 'last6months') {
    $months = $range === 'last3months' ? 3 : 6;
    $weeks = $months * 4;
    $thisMonday = strtotime('monday this week');
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
} elseif ($range === 'thisyear') {
    $year = date('Y');
    for ($m = 1; $m <= 12; $m++) {
        $monthStart = "$year-" . str_pad($m, 2, '0', STR_PAD_LEFT) . "-01";
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        $label = date('M', strtotime($monthStart));
        $extraWhere = ($where === '' ? 'WHERE' : "$where AND");
        $sql = "SELECT COUNT(*) as cnt FROM tasks t $extraWhere t.status = 'completed' AND t.completed_at >= '$monthStart' AND t.completed_at <= '$monthEnd'";
        $res = mysqli_query($conn, $sql);
        $count = (int)mysqli_fetch_assoc($res)['cnt'];
        $result[] = ['label' => $label, 'count' => $count];
    }
} elseif ($range === 'custom' && $from !== '' && $to !== '') {
    $fromDate = date('Y-m-d', strtotime($from));
    $toDate = date('Y-m-d', strtotime($to));
    $daysDiff = (strtotime($toDate) - strtotime($fromDate)) / 86400;

    if ($daysDiff <= 14) {
        $current = strtotime($fromDate);
        while ($current <= strtotime($toDate)) {
            $day = date('Y-m-d', $current);
            $label = date('M j', $current);
            $extraWhere = ($where === '' ? 'WHERE' : "$where AND");
            $sql = "SELECT COUNT(*) as cnt FROM tasks t $extraWhere t.status = 'completed' AND t.completed_at = '$day'";
            $res = mysqli_query($conn, $sql);
            $count = (int)mysqli_fetch_assoc($res)['cnt'];
            $result[] = ['label' => $label, 'count' => $count];
            $current = strtotime('+1 day', $current);
        }
    } elseif ($daysDiff <= 90) {
        $current = strtotime($fromDate);
        while ($current <= strtotime($toDate)) {
            $weekStart = date('Y-m-d', $current);
            $weekEnd = date('Y-m-d', min(strtotime('+6 days', $current), strtotime($toDate)));
            $label = date('M j', $current);
            $extraWhere = ($where === '' ? 'WHERE' : "$where AND");
            $sql = "SELECT COUNT(*) as cnt FROM tasks t $extraWhere t.status = 'completed' AND t.completed_at >= '$weekStart' AND t.completed_at <= '$weekEnd'";
            $res = mysqli_query($conn, $sql);
            $count = (int)mysqli_fetch_assoc($res)['cnt'];
            $result[] = ['label' => $label, 'count' => $count];
            $current = strtotime('+7 days', $current);
        }
    } else {
        $current = strtotime(date('Y-m-01', strtotime($fromDate)));
        while ($current <= strtotime($toDate)) {
            $monthStart = date('Y-m-01', $current);
            $monthEnd = date('Y-m-t', $current);
            $label = date('M Y', $current);
            $extraWhere = ($where === '' ? 'WHERE' : "$where AND");
            $sql = "SELECT COUNT(*) as cnt FROM tasks t $extraWhere t.status = 'completed' AND t.completed_at >= '$monthStart' AND t.completed_at <= '$monthEnd'";
            $res = mysqli_query($conn, $sql);
            $count = (int)mysqli_fetch_assoc($res)['cnt'];
            $result[] = ['label' => $label, 'count' => $count];
            $current = strtotime('+1 month', $current);
        }
    }
} else {
    $result = getWeeklyTrendsSQL($userId, $role, $teamId);
}

jsonResponse($result);
