let _cachedTasks = [];
let _cachedTeams = [];
let _cachedUsers = [];
let _mgrCurrentPage = 1;
let _empCurrentPage = 1;

async function apiFetch(url) {
    const res = await fetch(url);
    const json = await res.json();
    if (!json.success) throw new Error(json.error || 'API error');
    return json.data;
}

async function apiPost(url, body) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.error || 'API error');
    return json.data;
}

async function apiPatch(url) {
    const res = await fetch(url, { method: 'PATCH' });
    const json = await res.json();
    if (!json.success) throw new Error(json.error || 'API error');
    return json.data;
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(str);
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return '\u2014';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function statusBadgeHtml(status, dueDate, completedAt) {
    const today = new Date().toISOString().split('T')[0];
    if (status === 'completed') {
        if (dueDate && completedAt > dueDate) return '<span class="badge-status badge-late">Completed Late</span>';
        return '<span class="badge-status badge-completed">Completed</span>';
    }
    if (status !== 'completed' && dueDate && dueDate < today) return '<span class="badge-status badge-overdue">Overdue</span>';
    if (status === 'in_progress') return '<span class="badge-status badge-in-progress">In Progress</span>';
    return '<span class="badge-status badge-not-started">Not Started</span>';
}

function priorityBadgeHtml(priority) {
    const cls = priority === 'low' ? 'badge-low' : priority === 'high' ? 'badge-high' : 'badge-medium';
    return `<span class="badge-priority ${cls}">${priority.charAt(0).toUpperCase() + priority.slice(1)}</span>`;
}

function difficultyBadgeHtml(difficulty) {
    const cls = difficulty === 'easy' ? 'badge-easy' : difficulty === 'hard' ? 'badge-hard' : 'badge-medium';
    return `<span class="badge-difficulty ${cls}">${difficulty.charAt(0).toUpperCase() + difficulty.slice(1)}</span>`;
}

function onTimeBadgeHtml(dueDate, completedAt) {
    if (!dueDate) return '<span class="badge-status badge-not-started">No Deadline</span>';
    if (completedAt <= dueDate) return '<span class="badge-status badge-on-time">On Time</span>';
    return '<span class="badge-status badge-late">Late</span>';
}

function sortableHeader(tableId, col, label, type) {
    return `<th class="sortable" onclick="sortTable('${tableId}',${col},'${type}')">${label} <span class="sort-icon">\u2195</span></th>`;
}

function renderStatCards(containerId, metrics) {
    const cards = [
        { icon: 'list-task', label: 'Tasks Assigned', value: metrics.total_assigned, color: 'blue' },
        { icon: 'check-circle', label: 'Completed', value: metrics.total_completed, color: 'green' },
        { icon: 'percent', label: 'Completion Rate', value: metrics.completion_rate + '%', color: 'amber' },
        { icon: 'exclamation-triangle', label: 'Overdue', value: metrics.overdue, color: metrics.overdue > 0 ? 'red' : 'green' },
        { icon: 'clock-history', label: 'Avg Completion', value: metrics.avg_completion_days + 'd', color: 'blue' },
        { icon: 'arrow-left-right', label: 'On Time', value: metrics.completed_on_time, color: 'green' },
    ];

    document.getElementById(containerId).innerHTML = cards.map(c => `
        <div class="stat-card">
            <div class="stat-icon ${c.color}"><i class="bi bi-${c.icon}"></i></div>
            <div class="stat-info">
                <div class="stat-label">${escapeHtml(c.label)}</div>
                <div class="stat-value">${escapeHtml(String(c.value))}</div>
            </div>
        </div>
    `).join('');
}

function renderPerformanceSummary(containerId, metrics) {
    document.getElementById(containerId).innerHTML = `
        <h3>Performance Summary</h3>
        <table class="analytics-table" id="perf-tbl">
            <tbody>
                <tr><td><strong>Average Completion Time</strong></td><td>${metrics.avg_completion_days} days</td></tr>
                <tr><td><strong>Completed On Time</strong></td><td>${metrics.completed_on_time} tasks</td></tr>
                <tr><td><strong>Completed Late</strong></td><td>${metrics.completed_late} tasks</td></tr>
                <tr><td><strong>In Progress</strong></td><td>${metrics.in_progress} tasks</td></tr>
                <tr><td><strong>Not Started</strong></td><td>${metrics.not_started} tasks</td></tr>
            </tbody>
        </table>
    `;
}

function renderTaskTable(containerId, data, showAction = false) {
    const today = new Date().toISOString().split('T')[0];
    const tasks = data.tasks || data;
    const total = data.total || tasks.length;
    const page = data.page || 1;
    const totalPages = data.total_pages || 1;
    const perPage = data.per_page || 25;

    let rows = tasks.map(t => {
        const isOverdue = t.status !== 'completed' && t.due_date && t.due_date < today;
        const statusForFilter = isOverdue ? 'overdue' : t.status;
        return `
        <tr class="task-row" data-status="${statusForFilter}" data-priority="${t.priority}">
            <td><strong>${escapeHtml(t.title)}</strong></td>
            <td>${priorityBadgeHtml(t.priority)}</td>
            <td>${difficultyBadgeHtml(t.difficulty)}</td>
            <td>${statusBadgeHtml(t.status, t.due_date, t.completed_at)}</td>
            <td>${formatDate(t.due_date)}</td>
            <td>${formatDate(t.completed_at)}</td>
            ${showAction ? `<td>${t.status !== 'completed'
                ? `<button class="btn-mark-complete" onclick="markComplete(${t.id})">Mark Complete</button>`
                : '<span style="color:#6b7280;font-size:0.82rem;">Done</span>'
            }</td>` : ''}
        </tr>`;
    }).join('');

    let paginationHtml = '';
    if (totalPages > 1) {
        paginationHtml = `<div class="pagination-row">
            <span class="pagination-info">Showing ${(page - 1) * perPage + 1}-${Math.min(page * perPage, total)} of ${total}</span>
            <div class="pagination-btns">
                <button class="pagination-btn" ${page <= 1 ? 'disabled' : ''} onclick="empGoToPage(${page - 1})">Prev</button>
                ${Array.from({length: totalPages}, (_, i) => i + 1).filter(p => p === 1 || p === totalPages || Math.abs(p - page) <= 1).map(p =>
                    `<button class="pagination-btn ${p === page ? 'active' : ''}" onclick="empGoToPage(${p})">${p}</button>`
                ).join('')}
                <button class="pagination-btn" ${page >= totalPages ? 'disabled' : ''} onclick="empGoToPage(${page + 1})">Next</button>
            </div>
        </div>`;
    }

    document.getElementById(containerId).innerHTML = `
        <div class="table-header-row">
            <h3>My Tasks</h3>
            <div class="filter-row">
                <input type="text" id="emp-filter-search" class="filter-search" placeholder="Search tasks..." oninput="filterEmployeeTasks()">
                <select id="emp-filter-status" class="filter-select" onchange="filterEmployeeTasks()">
                    <option value="all">All Statuses</option>
                    <option value="not_started">Not Started</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="overdue">Overdue</option>
                </select>
                <select id="emp-filter-priority" class="filter-select" onchange="filterEmployeeTasks()">
                    <option value="all">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
        </div>
        <table class="analytics-table" id="emp-task-tbl">
            <thead><tr>
                ${sortableHeader('emp-task-tbl', 0, 'Title', 'str')}
                ${sortableHeader('emp-task-tbl', 1, 'Priority', 'str')}
                ${sortableHeader('emp-task-tbl', 2, 'Difficulty', 'str')}
                ${sortableHeader('emp-task-tbl', 3, 'Status', 'str')}
                ${sortableHeader('emp-task-tbl', 4, 'Due Date', 'date')}
                ${sortableHeader('emp-task-tbl', 5, 'Completed', 'date')}
                ${showAction ? '<th>Action</th>' : ''}
            </tr></thead>
            <tbody>${rows}</tbody>
        </table>
        ${paginationHtml}
    `;
}

async function empGoToPage(page) {
    _empCurrentPage = page;
    await loadEmployeeTasks();
}

async function loadEmployeeTasks() {
    const search = document.getElementById('emp-filter-search')?.value || '';
    const status = document.getElementById('emp-filter-status')?.value || 'all';
    const priority = document.getElementById('emp-filter-priority')?.value || 'all';

    const params = new URLSearchParams({
        page: _empCurrentPage,
        per_page: 25,
        search, status, priority
    });

    const data = await apiFetch(`/api/tasks.php?${params}`);
    renderTaskTable('task-table', data, true);
}

function renderManagerTaskTable(containerId, data) {
    const today = new Date().toISOString().split('T')[0];
    const tasks = data.tasks || [];
    const total = data.total || 0;
    const page = data.page || 1;
    const totalPages = data.total_pages || 1;

    let rows = tasks.map(t => {
        const isOverdue = t.status !== 'completed' && t.due_date && t.due_date < today;
        const statusForFilter = isOverdue ? 'overdue' : t.status;
        let onTimeHtml = '';
        if (t.status === 'completed') {
            onTimeHtml = onTimeBadgeHtml(t.due_date, t.completed_at);
        }
        return `
            <tr class="task-row" data-status="${statusForFilter}" data-team="${t.team_id}" data-priority="${t.priority}" data-employee="${t.assigned_to}">
                <td><strong>${escapeHtml(t.title)}</strong></td>
                <td>${escapeHtml(t.assigned_to_name || '')}</td>
                <td>${escapeHtml(t.team_name || '')}</td>
                <td>${priorityBadgeHtml(t.priority)}</td>
                <td>${difficultyBadgeHtml(t.difficulty)}</td>
                <td>${statusBadgeHtml(t.status, t.due_date, t.completed_at)}</td>
                <td>${formatDate(t.due_date)}</td>
                <td>${onTimeHtml}</td>
            </tr>
        `;
    }).join('');

    let paginationHtml = '';
    if (totalPages > 1) {
        paginationHtml = `<div class="pagination-row">
            <span class="pagination-info">Showing ${(page - 1) * (data.per_page || 25) + 1}-${Math.min(page * (data.per_page || 25), total)} of ${total}</span>
            <div class="pagination-btns">
                <button class="pagination-btn" ${page <= 1 ? 'disabled' : ''} onclick="mgrGoToPage(${page - 1})">Prev</button>
                ${Array.from({length: totalPages}, (_, i) => i + 1).filter(p => p === 1 || p === totalPages || Math.abs(p - page) <= 1).map(p =>
                    `<button class="pagination-btn ${p === page ? 'active' : ''}" onclick="mgrGoToPage(${p})">${p}</button>`
                ).join('')}
                <button class="pagination-btn" ${page >= totalPages ? 'disabled' : ''} onclick="mgrGoToPage(${page + 1})">Next</button>
            </div>
        </div>`;
    }

    document.getElementById(containerId).innerHTML = `
        <table class="analytics-table" id="mgr-task-tbl">
            <thead><tr>
                ${sortableHeader('mgr-task-tbl', 0, 'Title', 'str')}
                ${sortableHeader('mgr-task-tbl', 1, 'Assigned To', 'str')}
                ${sortableHeader('mgr-task-tbl', 2, 'Team', 'str')}
                ${sortableHeader('mgr-task-tbl', 3, 'Priority', 'str')}
                ${sortableHeader('mgr-task-tbl', 4, 'Difficulty', 'str')}
                ${sortableHeader('mgr-task-tbl', 5, 'Status', 'str')}
                ${sortableHeader('mgr-task-tbl', 6, 'Due Date', 'date')}
                ${sortableHeader('mgr-task-tbl', 7, 'On Time', 'str')}
            </tr></thead>
            <tbody>${rows}</tbody>
        </table>
        ${paginationHtml}
    `;
}

async function mgrGoToPage(page) {
    _mgrCurrentPage = page;
    await loadManagerTasks();
}

async function loadManagerTasks() {
    const search = document.getElementById('filter-search')?.value || '';
    const status = document.getElementById('filter-status')?.value || 'all';
    const team = document.getElementById('filter-team')?.value || 'all';
    const priority = document.getElementById('filter-priority')?.value || 'all';
    const employee = document.getElementById('filter-employee')?.value || 'all';

    const params = new URLSearchParams({
        page: _mgrCurrentPage,
        per_page: 25,
        search, status, team, priority, employee
    });

    const data = await apiFetch(`/api/tasks.php?${params}`);
    renderManagerTaskTable('mgr-task-table-body', data);
}

function renderTeamSummaryTable(containerId, summary) {
    let rows = summary.map(ts => `
        <tr>
            <td><strong>${escapeHtml(ts.team.name)}</strong></td>
            <td>${ts.metrics.total_assigned}</td>
            <td>${ts.metrics.total_completed}</td>
            <td>${ts.metrics.completion_rate}%</td>
            <td>${ts.metrics.completed_on_time}</td>
            <td>${ts.metrics.completed_late}</td>
            <td>${ts.metrics.overdue}</td>
            <td>${ts.metrics.avg_completion_days}</td>
        </tr>
    `).join('');

    document.getElementById(containerId).innerHTML = `
        <h3>Team Summary</h3>
        <table class="analytics-table" id="team-sum-tbl">
            <thead><tr>
                ${sortableHeader('team-sum-tbl', 0, 'Team', 'str')}
                ${sortableHeader('team-sum-tbl', 1, 'Assigned', 'num')}
                ${sortableHeader('team-sum-tbl', 2, 'Completed', 'num')}
                ${sortableHeader('team-sum-tbl', 3, 'Rate', 'num')}
                ${sortableHeader('team-sum-tbl', 4, 'On Time', 'num')}
                ${sortableHeader('team-sum-tbl', 5, 'Late', 'num')}
                ${sortableHeader('team-sum-tbl', 6, 'Overdue', 'num')}
                ${sortableHeader('team-sum-tbl', 7, 'Avg Days', 'num')}
            </tr></thead>
            <tbody>${rows}</tbody>
        </table>
    `;
}

function renderMemberSummaryTable(containerId, summary) {
    let rows = summary.map(ms => `
        <tr data-team="${ms.user.team_id}">
            <td><strong><a href="#" class="member-link" onclick="event.preventDefault(); selectMemberFromTable('${escapeHtml(ms.user.name)}')">${escapeHtml(ms.user.name)}</a></strong></td>
            <td>${escapeHtml(ms.user.team_name || '')}</td>
            <td>${ms.metrics.total_assigned}</td>
            <td>${ms.metrics.total_completed}</td>
            <td>${ms.metrics.completion_rate}%</td>
            <td>${ms.metrics.completed_on_time}</td>
            <td>${ms.metrics.completed_late}</td>
            <td>${ms.metrics.overdue}</td>
        </tr>
    `).join('');

    document.getElementById(containerId).innerHTML = `
        <h3>Member Summary</h3>
        <table class="analytics-table" id="member-sum-tbl">
            <thead><tr>
                ${sortableHeader('member-sum-tbl', 0, 'Name', 'str')}
                ${sortableHeader('member-sum-tbl', 1, 'Team', 'str')}
                ${sortableHeader('member-sum-tbl', 2, 'Assigned', 'num')}
                ${sortableHeader('member-sum-tbl', 3, 'Completed', 'num')}
                ${sortableHeader('member-sum-tbl', 4, 'Rate', 'num')}
                ${sortableHeader('member-sum-tbl', 5, 'On Time', 'num')}
                ${sortableHeader('member-sum-tbl', 6, 'Late', 'num')}
                ${sortableHeader('member-sum-tbl', 7, 'Overdue', 'num')}
            </tr></thead>
            <tbody>${rows}</tbody>
        </table>
    `;
}

function populateSelectOptions(selectId, items, valueKey, labelFn) {
    const select = document.getElementById(selectId);
    if (!select) return;
    const existing = select.querySelector('option[value=""]');
    select.innerHTML = '';
    if (existing) select.appendChild(existing);
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item[valueKey];
        opt.textContent = labelFn(item);
        select.appendChild(opt);
    });
}

function populateFilterDropdown(selectId, items, valueKey, labelFn, allLabel) {
    const select = document.getElementById(selectId);
    if (!select) return;
    select.innerHTML = `<option value="all">${allLabel}</option>`;
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item[valueKey];
        opt.textContent = labelFn(item);
        select.appendChild(opt);
    });
}

async function changeTimeRange(range, btn) {
    btn.parentElement.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    try {
        const data = await apiFetch(`/api/analytics/weekly.php?range=${range}`);
        const chart = Chart.getChart('weeklyLine');
        if (chart) chart.destroy();
        createLineChart('weeklyLine', data.map(w => w.label), data.map(w => w.count));
    } catch (e) {
        console.error('Time range error:', e);
    }
}

async function changeMgrTimeRange(range, btn) {
    btn.parentElement.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    try {
        const data = await apiFetch(`/api/analytics/weekly.php?range=${range}`);
        const chart = Chart.getChart('mgrWeeklyLine');
        if (chart) chart.destroy();
        createLineChart('mgrWeeklyLine', data.map(w => w.label), data.map(w => w.count));
    } catch (e) {
        console.error('Time range error:', e);
    }
}

function selectMemberFromTable(memberName) {
    const empSelect = document.getElementById('comp-filter-employee');
    if (empSelect) {
        empSelect.value = memberName;
    }
    filterMemberTable();
    const detailCard = document.getElementById('member-detail-card');
    if (detailCard) detailCard.scrollIntoView({ behavior: 'smooth' });
}

function autoSetTeamFromEmployee() {
    const empSelect = document.getElementById('assign-to-select');
    const teamSelect = document.getElementById('team-select');
    if (!empSelect || !teamSelect || typeof _cachedUsers === 'undefined') return;

    const empId = empSelect.value;
    if (!empId) return;

    const user = _cachedUsers.find(u => String(u.id) === empId);
    if (user && user.team_id) {
        teamSelect.value = String(user.team_id);
    }
}

function updateModalEmployees() {
    const teamSelect = document.getElementById('team-select');
    const empSelect = document.getElementById('assign-to-select');
    if (!teamSelect || !empSelect || typeof _cachedUsers === 'undefined') return;

    const selectedTeam = teamSelect.value;
    empSelect.innerHTML = '<option value="">Select employee...</option>';

    const filtered = (selectedTeam === '' || selectedTeam === 'all')
        ? _cachedUsers
        : _cachedUsers.filter(u => String(u.team_id) === String(selectedTeam));

    filtered.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id;
        opt.textContent = `${u.name} (${u.team_name})`;
        empSelect.appendChild(opt);
    });
}

async function markComplete(taskId) {
    try {
        await apiPatch(`/api/tasks.php?id=${taskId}`);
        loadEmployeeDashboard();
    } catch (e) {
        alert('Error: ' + e.message);
    }
}

async function handleCreateTask(form) {
    const formData = new FormData(form);
    const body = {};
    formData.forEach((v, k) => body[k] = v);
    body.assigned_to = parseInt(body.assigned_to);
    body.team_id = parseInt(body.team_id) || 0;

    if (body.team_id <= 0 && body.assigned_to > 0) {
        const user = _cachedUsers.find(u => u.id === body.assigned_to || String(u.id) === String(body.assigned_to));
        if (user) body.team_id = parseInt(user.team_id);
    }

    try {
        await apiPost('/api/tasks.php', body);
        const modal = bootstrap.Modal.getInstance(document.getElementById('createTaskModal'));
        if (modal) modal.hide();
        form.reset();
        loadManagerDashboard();
    } catch (e) {
        alert('Error: ' + e.message);
    }
}

async function loadEmployeeDashboard() {
    try {
        const [metrics, weekly, allTasks, taskPage] = await Promise.all([
            apiFetch('/api/analytics/metrics.php'),
            apiFetch('/api/analytics/weekly.php'),
            apiFetch('/api/tasks.php?all=1'),
            apiFetch('/api/tasks.php?page=1&per_page=25'),
        ]);

        const tasks = allTasks;

        renderStatCards('stat-cards', metrics);
        renderPerformanceSummary('performance-summary', metrics);
        renderTaskTable('task-table', taskPage, true);

        document.querySelectorAll('canvas').forEach(c => {
            const chart = Chart.getChart(c);
            if (chart) chart.destroy();
        });

        createDoughnutChart('statusDoughnut',
            ['Completed', 'In Progress', 'Not Started'],
            [metrics.total_completed, metrics.in_progress, metrics.not_started],
            [chartColors.completed, chartColors.inProgress, chartColors.notStarted]
        );

        createBarChart('onTimeBar',
            ['On Time', 'Late'],
            [metrics.completed_on_time, metrics.completed_late],
            [chartColors.onTime, chartColors.late]
        );

        createLineChart('weeklyLine',
            weekly.map(w => w.label),
            weekly.map(w => w.count)
        );

        const priorityCounts = { low: 0, medium: 0, high: 0 };
        tasks.forEach(t => { if (priorityCounts[t.priority] !== undefined) priorityCounts[t.priority]++; });
        createDoughnutChart('priorityDoughnut',
            ['Low', 'Medium', 'High'],
            [priorityCounts.low, priorityCounts.medium, priorityCounts.high],
            ['#3b82f6', '#f59e0b', '#ef4444']
        );

        const difficultyCounts = { easy: 0, medium: 0, hard: 0 };
        tasks.forEach(t => { if (difficultyCounts[t.difficulty] !== undefined) difficultyCounts[t.difficulty]++; });
        createDoughnutChart('difficultyDoughnut',
            ['Easy', 'Medium', 'Hard'],
            [difficultyCounts.easy, difficultyCounts.medium, difficultyCounts.hard],
            ['#10b981', '#f59e0b', '#8b5cf6']
        );

        if (document.getElementById('teamAggregateCard')) {
            try {
                const teamMetrics = await apiFetch('/api/analytics/team-aggregate.php');
                document.getElementById('teamAggregateCard').innerHTML = `
                    <h3>My Team Overview</h3>
                    <table class="analytics-table">
                        <tbody>
                            <tr><td><strong>Team Tasks</strong></td><td>${teamMetrics.total_assigned}</td></tr>
                            <tr><td><strong>Team Completion Rate</strong></td><td>${teamMetrics.completion_rate}%</td></tr>
                            <tr><td><strong>Team Overdue</strong></td><td>${teamMetrics.overdue}</td></tr>
                            <tr><td><strong>Team Avg Completion</strong></td><td>${teamMetrics.avg_completion_days} days</td></tr>
                        </tbody>
                    </table>
                `;
            } catch (e) {}
        }
    } catch (e) {
        console.error('Dashboard load error:', e);
    }
}

async function loadManagerDashboard() {
    const pageRole = document.body.dataset.role;
    const isProjectManager = pageRole === 'project_manager';

    try {
        const [metrics, weekly, allTasks, taskPage, teamSummary, memberSummary, users, teams] = await Promise.all([
            apiFetch('/api/analytics/metrics.php'),
            apiFetch('/api/analytics/weekly.php'),
            apiFetch('/api/tasks.php?all=1'),
            apiFetch('/api/tasks.php?page=1&per_page=25'),
            apiFetch('/api/analytics/team-summary.php').catch(() => []),
            apiFetch('/api/analytics/member-summary.php').catch(() => []),
            apiFetch('/api/users.php'),
            apiFetch('/api/teams.php'),
        ]);

        const tasks = allTasks;
        _cachedTasks = allTasks;
        _cachedTeams = teams;
        _cachedUsers = users;

        renderStatCards('mgr-stat-cards', metrics);
        renderPerformanceSummary('mgr-performance-summary', metrics);
        renderManagerTaskTable('mgr-task-table-body', taskPage);

        populateSelectOptions('assign-to-select', users, 'id', u => `${u.name} (${u.team_name})`);
        const teamSelect = document.getElementById('team-select');
        if (teamSelect) {
            const allOpt = teamSelect.querySelector('option[value=""]');
            teamSelect.innerHTML = '';
            if (allOpt) teamSelect.appendChild(allOpt);
            teams.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.name;
                teamSelect.appendChild(opt);
            });
        }

        populateFilterDropdown('filter-team', teams, 'id', t => t.name, 'All Teams');
        populateFilterDropdown('filter-employee', users, 'id', u => u.name, 'All Employees');

        if (document.getElementById('overview-filter-team')) {
            populateFilterDropdown('overview-filter-team', teams, 'id', t => t.name, 'All Teams');
        }

        if (document.getElementById('comparison-filter-team')) {
            populateFilterDropdown('comparison-filter-team', teams, 'id', t => t.name, 'All Teams');
        }

        document.querySelectorAll('canvas').forEach(c => {
            const chart = Chart.getChart(c);
            if (chart) chart.destroy();
        });

        createDoughnutChart('mgrStatusDoughnut',
            ['Completed', 'In Progress', 'Not Started'],
            [metrics.total_completed, metrics.in_progress, metrics.not_started],
            [chartColors.completed, chartColors.inProgress, chartColors.notStarted]
        );

        createBarChart('mgrOnTimeBar',
            ['On Time', 'Late'],
            [metrics.completed_on_time, metrics.completed_late],
            [chartColors.onTime, chartColors.late]
        );

        createLineChart('mgrWeeklyLine',
            weekly.map(w => w.label),
            weekly.map(w => w.count)
        );

        const mgrPriorityCounts = { low: 0, medium: 0, high: 0 };
        tasks.forEach(t => { if (mgrPriorityCounts[t.priority] !== undefined) mgrPriorityCounts[t.priority]++; });
        createDoughnutChart('mgrPriorityDoughnut',
            ['Low', 'Medium', 'High'],
            [mgrPriorityCounts.low, mgrPriorityCounts.medium, mgrPriorityCounts.high],
            ['#3b82f6', '#f59e0b', '#ef4444']
        );

        const mgrDifficultyCounts = { easy: 0, medium: 0, hard: 0 };
        tasks.forEach(t => { if (mgrDifficultyCounts[t.difficulty] !== undefined) mgrDifficultyCounts[t.difficulty]++; });
        createDoughnutChart('mgrDifficultyDoughnut',
            ['Easy', 'Medium', 'Hard'],
            [mgrDifficultyCounts.easy, mgrDifficultyCounts.medium, mgrDifficultyCounts.hard],
            ['#10b981', '#f59e0b', '#8b5cf6']
        );

        if (isProjectManager && teamSummary.length > 0) {
            renderTeamSummaryTable('team-summary-table', teamSummary);
        }

        if (memberSummary.length > 0) {
            renderMemberSummaryTable('member-summary-table', memberSummary);
        }

        if (document.getElementById('comp-filter-team')) {
            populateFilterDropdown('comp-filter-team', teams, 'id', t => t.name, 'All Teams');
        }
        if (document.getElementById('comp-filter-employee')) {
            populateFilterDropdown('comp-filter-employee', users, 'name', u => u.name, 'All Employees');
        }
    } catch (e) {
        console.error('Manager dashboard load error:', e);
    }
}
