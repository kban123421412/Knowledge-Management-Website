const chartColors = {
    completed: '#10b981',
    inProgress: '#f59e0b',
    notStarted: '#6b7280',
    late: '#ef4444',
    onTime: '#10b981',
    line: '#374151',
    lineFill: 'rgba(55, 65, 81, 0.08)',
    gridLine: 'rgba(107, 114, 128, 0.1)',
    teamColors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
};

Chart.defaults.font.family = 'Arial, sans-serif';
Chart.defaults.color = '#6b7280';

function createDoughnutChart(canvasId, labels, data, colors) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyleWidth: 10,
                        font: { size: 12, weight: '600' }
                    }
                }
            }
        }
    });
}

function createBarChart(canvasId, labels, data, colors) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderRadius: 8,
                borderSkipped: false,
                maxBarThickness: 50,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { size: 12 } },
                    grid: { color: chartColors.gridLine }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 12, weight: '600' } }
                }
            }
        }
    });
}

function createLineChart(canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tasks Completed',
                data: data,
                borderColor: chartColors.line,
                backgroundColor: chartColors.lineFill,
                fill: true,
                tension: 0.35,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#fff',
                pointBorderColor: chartColors.line,
                pointBorderWidth: 2,
                borderWidth: 2.5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { size: 12 } },
                    grid: { color: chartColors.gridLine }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 12, weight: '600' } }
                }
            }
        }
    });
}

function createHorizontalBarChart(canvasId, labels, data, colors) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors || chartColors.teamColors.slice(0, data.length),
                borderRadius: 8,
                borderSkipped: false,
                maxBarThickness: 36,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { size: 12 } },
                    grid: { color: chartColors.gridLine }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 12, weight: '600' } }
                }
            }
        }
    });
}

function createStackedBarChart(canvasId, labels, datasets) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: { labels: labels, datasets: datasets },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 14, usePointStyle: true, font: { size: 12, weight: '600' } }
                }
            },
            scales: {
                y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1, font: { size: 12 } }, grid: { color: chartColors.gridLine } },
                x: { stacked: true, grid: { display: false }, ticks: { font: { size: 12, weight: '600' } } }
            }
        }
    });
}

function switchTab(tabName) {
    document.querySelectorAll('.analytics-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

    const tab = document.querySelector(`[data-tab="${tabName}"]`);
    const content = document.getElementById(`tab-${tabName}`);
    if (tab) tab.classList.add('active');
    if (content) content.classList.add('active');

    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);
}

function filterTasks() {
    const teamFilter = document.getElementById('filter-team')?.value || 'all';
    updateEmployeeDropdown(teamFilter);
    _mgrCurrentPage = 1;
    loadManagerTasks();
}

function updateEmployeeDropdown(teamFilter) {
    const empSelect = document.getElementById('filter-employee');
    if (!empSelect || typeof _cachedUsers === 'undefined') return;

    const currentVal = empSelect.value;
    empSelect.innerHTML = '<option value="all">All Employees</option>';

    const filtered = teamFilter === 'all'
        ? _cachedUsers
        : _cachedUsers.filter(u => String(u.team_id) === String(teamFilter));

    filtered.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id;
        opt.textContent = u.name;
        empSelect.appendChild(opt);
    });

    if (filtered.some(u => String(u.id) === currentVal)) {
        empSelect.value = currentVal;
    } else {
        empSelect.value = 'all';
    }
}

function filterEmployeeTasks() {
    _empCurrentPage = 1;
    loadEmployeeTasks();
}

function filterTeamTable() {
    const searchVal = (document.getElementById('team-perf-search')?.value || '').toLowerCase();

    document.querySelectorAll('#team-sum-tbl tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = (searchVal === '' || text.includes(searchVal)) ? '' : 'none';
    });
}

function filterMemberTable() {
    const teamFilter = document.getElementById('comp-filter-team')?.value || 'all';
    const empFilter = document.getElementById('comp-filter-employee')?.value || 'all';
    const searchVal = (document.getElementById('comp-member-search')?.value || '').toLowerCase();

    updateCompEmployeeDropdown(teamFilter);

    document.querySelectorAll('#member-sum-tbl tbody tr').forEach(row => {
        const team = row.dataset.team || '';
        const text = row.textContent.toLowerCase();
        const name = row.cells[0]?.textContent.trim() || '';

        const show =
            (teamFilter === 'all' || team === teamFilter) &&
            (empFilter === 'all' || name === empFilter) &&
            (searchVal === '' || text.includes(searchVal));

        row.style.display = show ? '' : 'none';
    });

    if (empFilter !== 'all') {
        showMemberDetail(empFilter);
    } else {
        const detailCard = document.getElementById('member-detail-card');
        if (detailCard) detailCard.style.display = 'none';
    }
}

function updateCompEmployeeDropdown(teamFilter) {
    const empSelect = document.getElementById('comp-filter-employee');
    if (!empSelect || typeof _cachedUsers === 'undefined') return;

    const currentVal = empSelect.value;
    empSelect.innerHTML = '<option value="all">All Employees</option>';

    const filtered = teamFilter === 'all'
        ? _cachedUsers
        : _cachedUsers.filter(u => String(u.team_id) === String(teamFilter));

    filtered.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.name;
        opt.textContent = u.name;
        empSelect.appendChild(opt);
    });

    if (filtered.some(u => u.name === currentVal)) {
        empSelect.value = currentVal;
    } else {
        empSelect.value = 'all';
    }
}

function showMemberDetail(memberName) {
    const detailCard = document.getElementById('member-detail-card');
    if (!detailCard || typeof _cachedTasks === 'undefined') return;

    const memberTasks = _cachedTasks.filter(t => t.assigned_to_name === memberName);
    if (memberTasks.length === 0) {
        detailCard.style.display = 'none';
        return;
    }

    const today = new Date().toISOString().split('T')[0];
    const total = memberTasks.length;
    const completed = memberTasks.filter(t => t.status === 'completed');
    const inProgress = memberTasks.filter(t => t.status === 'in_progress').length;
    const notStarted = memberTasks.filter(t => t.status === 'not_started').length;
    const overdue = memberTasks.filter(t => t.status !== 'completed' && t.due_date && t.due_date < today).length;
    const onTime = completed.filter(t => t.due_date && t.completed_at <= t.due_date).length;
    const late = completed.filter(t => t.due_date && t.completed_at > t.due_date).length;
    const rate = total > 0 ? Math.round((completed.length / total) * 100) : 0;

    const taskRows = memberTasks.map(t => {
        const isOverdue = t.status !== 'completed' && t.due_date && t.due_date < today;
        return `<tr>
            <td><strong>${t.title}</strong></td>
            <td>${priorityBadgeHtml(t.priority)}</td>
            <td>${difficultyBadgeHtml(t.difficulty)}</td>
            <td>${statusBadgeHtml(t.status, t.due_date, t.completed_at)}</td>
            <td>${formatDate(t.due_date)}</td>
        </tr>`;
    }).join('');

    detailCard.style.display = 'block';
    detailCard.innerHTML = `
        <h3>${memberName}'s Performance</h3>
        <div class="stat-cards" style="margin-bottom:1rem;">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-list-task"></i></div>
                <div class="stat-info"><div class="stat-label">Assigned</div><div class="stat-value">${total}</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
                <div class="stat-info"><div class="stat-label">Completed</div><div class="stat-value">${completed.length}</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="bi bi-percent"></i></div>
                <div class="stat-info"><div class="stat-label">Rate</div><div class="stat-value">${rate}%</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon ${overdue > 0 ? 'red' : 'green'}"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="stat-info"><div class="stat-label">Overdue</div><div class="stat-value">${overdue}</div></div>
            </div>
        </div>
        <table class="analytics-table">
            <thead><tr><th>Title</th><th>Priority</th><th>Difficulty</th><th>Status</th><th>Due Date</th></tr></thead>
            <tbody>${taskRows}</tbody>
        </table>
    `;
}

let sortState = {};

function sortTable(tableId, colIndex, type) {
    const key = tableId + '-' + colIndex;
    sortState[key] = sortState[key] === 'asc' ? 'desc' : 'asc';
    const dir = sortState[key];

    const tbody = document.querySelector(`#${tableId} tbody`);
    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('tr'));
    rows.sort((a, b) => {
        let aVal = a.cells[colIndex]?.textContent.trim() || '';
        let bVal = b.cells[colIndex]?.textContent.trim() || '';

        if (type === 'num') {
            aVal = parseFloat(aVal.replace('%', '')) || 0;
            bVal = parseFloat(bVal.replace('%', '')) || 0;
        } else if (type === 'date') {
            aVal = aVal === '\u2014' ? '' : new Date(aVal).getTime() || 0;
            bVal = bVal === '\u2014' ? '' : new Date(bVal).getTime() || 0;
        } else {
            aVal = aVal.toLowerCase();
            bVal = bVal.toLowerCase();
        }

        if (aVal < bVal) return dir === 'asc' ? -1 : 1;
        if (aVal > bVal) return dir === 'asc' ? 1 : -1;
        return 0;
    });

    rows.forEach(r => tbody.appendChild(r));

    document.querySelectorAll(`#${tableId} th .sort-icon`).forEach(el => el.textContent = '\u2195');
    const th = document.querySelectorAll(`#${tableId} th`)[colIndex];
    const icon = th?.querySelector('.sort-icon');
    if (icon) icon.textContent = dir === 'asc' ? '\u2191' : '\u2193';
}
