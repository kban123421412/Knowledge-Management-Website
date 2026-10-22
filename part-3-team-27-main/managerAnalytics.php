<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if (!isManager()) {
    header('Location: /employeeAnalytics.php');
    exit();
}

$role = getSessionRole();
$teamId = getSessionTeamId();
$name = getSessionName();
$isProjectManager = $role === 'project_manager';
$activeTab = $_GET['tab'] ?? 'overview';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Analytics - Make-It-All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/topnavbar.css">
    <link rel="stylesheet" href="/css/analytics.css">
</head>

<body data-role="<?php echo htmlspecialchars($role); ?>">

    <?php include __DIR__ . '/topnavbar.php'; ?>

    <div class="analytics-container">
        <h1 class="page-title"><?php echo $isProjectManager ? 'Project Analytics' : 'Team Analytics'; ?></h1>
        <p class="page-subtitle">
            Welcome back, <?php echo htmlspecialchars($name); ?>.
            <?php if (!$isProjectManager): ?>
                Viewing analytics for your team.
            <?php else: ?>
                Viewing analytics across all teams.
            <?php endif; ?>
        </p>

        <div class="analytics-tabs">
            <button class="analytics-tab <?php echo $activeTab === 'overview' ? 'active' : ''; ?>" data-tab="overview" onclick="switchTab('overview')">Overview</button>
            <button class="analytics-tab <?php echo $activeTab === 'comparison' ? 'active' : ''; ?>" data-tab="comparison" onclick="switchTab('comparison')">
                <?php echo $isProjectManager ? 'Team Comparison' : 'Member Comparison'; ?>
            </button>
            <button class="analytics-tab <?php echo $activeTab === 'tasks' ? 'active' : ''; ?>" data-tab="tasks" onclick="switchTab('tasks')">Task Management</button>
        </div>

        <div id="tab-overview" class="tab-content <?php echo $activeTab === 'overview' ? 'active' : ''; ?>">
            <div id="mgr-stat-cards" class="stat-cards"></div>

            <div class="chart-row">
                <div class="chart-card">
                    <h3>Task Status Breakdown</h3>
                    <div class="chart-wrapper doughnut"><canvas id="mgrStatusDoughnut"></canvas></div>
                </div>
                <div class="chart-card">
                    <h3>On Time vs Late</h3>
                    <div class="chart-wrapper"><canvas id="mgrOnTimeBar"></canvas></div>
                </div>
            </div>

            <div class="chart-row full">
                <div class="chart-card">
                    <div class="chart-header-row">
                        <h3>Completion Trend</h3>
                        <div class="time-range-btns">
                            <button class="time-btn active" onclick="changeMgrTimeRange('last8weeks', this)">8 Weeks</button>
                            <button class="time-btn" onclick="changeMgrTimeRange('thisweek', this)">This Week</button>
                            <button class="time-btn" onclick="changeMgrTimeRange('thismonth', this)">This Month</button>
                            <button class="time-btn" onclick="changeMgrTimeRange('last3months', this)">3 Months</button>
                            <button class="time-btn" onclick="changeMgrTimeRange('last6months', this)">6 Months</button>
                            <button class="time-btn" onclick="changeMgrTimeRange('thisyear', this)">This Year</button>
                        </div>
                    </div>
                    <div class="chart-wrapper"><canvas id="mgrWeeklyLine"></canvas></div>
                </div>
            </div>

            <div class="chart-row">
                <div class="chart-card">
                    <h3>Priority Distribution</h3>
                    <div class="chart-wrapper doughnut"><canvas id="mgrPriorityDoughnut"></canvas></div>
                </div>
                <div class="chart-card">
                    <h3>Difficulty Distribution</h3>
                    <div class="chart-wrapper doughnut"><canvas id="mgrDifficultyDoughnut"></canvas></div>
                </div>
            </div>

            <div class="chart-row full">
                <div id="mgr-performance-summary" class="chart-card"></div>
            </div>
        </div>

        <div id="tab-comparison" class="tab-content <?php echo $activeTab === 'comparison' ? 'active' : ''; ?>">
            <?php if ($isProjectManager): ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                    <h2 style="font-size:1.15rem; font-weight:700; color:#111827; margin:0;">Team Performance</h2>
                </div>
                <div class="filter-row">
                    <input type="text" id="team-perf-search" class="filter-search" placeholder="Search teams..." oninput="filterTeamTable()">
                </div>
                <div id="team-summary-table" class="table-card" style="margin-top:0;"></div>
            <?php endif; ?>

            <div style="margin-top:1.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                    <h2 style="font-size:1.15rem; font-weight:700; color:#111827; margin:0;">Member Performance</h2>
                </div>
                <div class="filter-row">
                    <input type="text" id="comp-member-search" class="filter-search" placeholder="Search members..." oninput="filterMemberTable()">
                    <?php if ($isProjectManager): ?>
                        <select id="comp-filter-team" class="filter-select" onchange="filterMemberTable()"></select>
                    <?php endif; ?>
                    <select id="comp-filter-employee" class="filter-select" onchange="filterMemberTable()"></select>
                </div>
            </div>
            <div id="member-summary-table" class="table-card" style="margin-top:0;"></div>
            <div id="member-detail-card" class="chart-card" style="margin-top:1.2rem; display:none;"></div>
        </div>

        <div id="tab-tasks" class="tab-content <?php echo $activeTab === 'tasks' ? 'active' : ''; ?>">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.2rem;">
                <h2 style="font-size:1.15rem; font-weight:700; color:#111827; margin:0;">All Tasks</h2>
                <button class="btn-create-task" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="bi bi-plus-lg"></i> Create Task
                </button>
            </div>

            <div class="filter-row">
                <input type="text" id="filter-search" class="filter-search" placeholder="Search tasks..." oninput="filterTasks()">
                <select id="filter-status" class="filter-select" onchange="filterTasks()">
                    <option value="all">All Statuses</option>
                    <option value="not_started">Not Started</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="overdue">Overdue</option>
                </select>
                <select id="filter-team" class="filter-select" onchange="filterTasks()"></select>
                <select id="filter-priority" class="filter-select" onchange="filterTasks()">
                    <option value="all">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
                <select id="filter-employee" class="filter-select" onchange="filterTasks()"></select>
            </div>

            <div id="mgr-task-table-body" class="table-card" style="margin-top:0;"></div>
        </div>

    </div>

    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createTaskForm" onsubmit="event.preventDefault(); handleCreateTask(this);">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Assign To</label>
                                <select class="form-select" name="assigned_to" id="assign-to-select" required onchange="autoSetTeamFromEmployee()">
                                    <option value="">Select employee...</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Team</label>
                                <select class="form-select" name="team_id" id="team-select" onchange="updateModalEmployees()">
                                    <option value="">All Teams</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label">Difficulty</label>
                                <select class="form-select" name="difficulty">
                                    <option value="easy">Easy</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" name="due_date">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px;">Cancel</button>
                        <button type="submit" class="btn-create-task">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/modals.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/js/analyticsCharts.js"></script>
    <script src="/js/analyticsApp.js"></script>
    <script>
        loadManagerDashboard();
        const urlTab = new URLSearchParams(window.location.search).get('tab');
        if (urlTab) switchTab(urlTab);
    </script>

</body>

</html>
