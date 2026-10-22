<?php

function renderStatCard($icon, $label, $value, $colorClass = 'blue') {
    echo '<div class="stat-card">';
    echo '  <div class="stat-icon ' . htmlspecialchars($colorClass) . '">';
    echo '    <i class="bi bi-' . htmlspecialchars($icon) . '"></i>';
    echo '  </div>';
    echo '  <div class="stat-info">';
    echo '    <div class="stat-label">' . htmlspecialchars($label) . '</div>';
    echo '    <div class="stat-value">' . htmlspecialchars($value) . '</div>';
    echo '  </div>';
    echo '</div>';
}

function renderStatusBadge($status, $dueDate = null, $completedAt = null) {
    $today = date('Y-m-d');

    if ($status === 'completed') {
        if ($dueDate !== null && $completedAt > $dueDate) {
            echo '<span class="badge-status badge-late">Completed Late</span>';
        } else {
            echo '<span class="badge-status badge-completed">Completed</span>';
        }
    } elseif ($status !== 'completed' && $dueDate !== null && $dueDate < $today) {
        echo '<span class="badge-status badge-overdue">Overdue</span>';
    } elseif ($status === 'in_progress') {
        echo '<span class="badge-status badge-in-progress">In Progress</span>';
    } else {
        echo '<span class="badge-status badge-not-started">Not Started</span>';
    }
}

function renderPriorityBadge($priority) {
    $class = 'badge-medium';
    if ($priority === 'low') $class = 'badge-low';
    if ($priority === 'high') $class = 'badge-high';
    echo '<span class="badge-priority ' . $class . '">' . ucfirst(htmlspecialchars($priority)) . '</span>';
}

function renderDifficultyBadge($difficulty) {
    $class = 'badge-medium';
    if ($difficulty === 'easy') $class = 'badge-easy';
    if ($difficulty === 'hard') $class = 'badge-hard';
    echo '<span class="badge-difficulty ' . $class . '">' . ucfirst(htmlspecialchars($difficulty)) . '</span>';
}

function renderOnTimeBadge($dueDate, $completedAt) {
    if ($dueDate === null) {
        echo '<span class="badge-status badge-not-started">No Deadline</span>';
    } elseif ($completedAt <= $dueDate) {
        echo '<span class="badge-status badge-on-time">On Time</span>';
    } else {
        echo '<span class="badge-status badge-late">Late</span>';
    }
}
