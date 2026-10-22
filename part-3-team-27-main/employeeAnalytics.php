<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$name = getSessionName();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Analytics - Make-It-All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/topnavbar.css">
    <link rel="stylesheet" href="/css/analytics.css">
</head>

<body>

    <?php include __DIR__ . '/topnavbar.php'; ?>

    <div class="analytics-container">
        <h1 class="page-title">My Analytics</h1>
        <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($name); ?>. Here's your productivity overview.</p>

        <div id="stat-cards" class="stat-cards"></div>

        <div class="chart-row">
            <div class="chart-card">
                <h3>Task Status Breakdown</h3>
                <div class="chart-wrapper doughnut"><canvas id="statusDoughnut"></canvas></div>
            </div>
            <div class="chart-card">
                <h3>On Time vs Late</h3>
                <div class="chart-wrapper"><canvas id="onTimeBar"></canvas></div>
            </div>
        </div>

        <div class="chart-row full">
            <div class="chart-card">
                <div class="chart-header-row">
                    <h3>Completion Trend</h3>
                    <div class="time-range-btns">
                        <button class="time-btn active" onclick="changeTimeRange('last8weeks', this)">8 Weeks</button>
                        <button class="time-btn" onclick="changeTimeRange('thisweek', this)">This Week</button>
                        <button class="time-btn" onclick="changeTimeRange('thismonth', this)">This Month</button>
                        <button class="time-btn" onclick="changeTimeRange('last3months', this)">3 Months</button>
                        <button class="time-btn" onclick="changeTimeRange('last6months', this)">6 Months</button>
                        <button class="time-btn" onclick="changeTimeRange('thisyear', this)">This Year</button>
                    </div>
                </div>
                <div class="chart-wrapper"><canvas id="weeklyLine"></canvas></div>
            </div>
        </div>

        <div class="chart-row">
            <div class="chart-card">
                <h3>Priority Distribution</h3>
                <div class="chart-wrapper doughnut"><canvas id="priorityDoughnut"></canvas></div>
            </div>
            <div class="chart-card">
                <h3>Difficulty Distribution</h3>
                <div class="chart-wrapper doughnut"><canvas id="difficultyDoughnut"></canvas></div>
            </div>
        </div>

        <div class="chart-row full">
            <div id="performance-summary" class="chart-card"></div>
        </div>

        <div id="teamAggregateCard" class="chart-card" style="margin-top:1.2rem;"></div>

        <div id="task-table" class="table-card"></div>
    </div>

    <?php include __DIR__ . '/modals.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/js/analyticsCharts.js"></script>
    <script src="/js/analyticsApp.js"></script>
    <script>loadEmployeeDashboard();</script>

</body>

</html>
