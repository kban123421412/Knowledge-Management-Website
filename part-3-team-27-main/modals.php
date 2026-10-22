<?php
// role display helper
$roleLabels = [
    'project_manager' => ['label' => 'Project Manager', 'class' => 'pm'],
    'team_leader'     => ['label' => 'Team Leader',     'class' => 'tl'],
    'employee'        => ['label' => 'Employee',         'class' => 'emp'],
];
$userRole    = $_SESSION['role']    ?? 'employee';
$userName    = $_SESSION['name']    ?? 'Unknown';
$userEmail   = $_SESSION['username'] ?? '';
$userInitial = strtoupper(substr($userName, 0, 1));
$roleMeta    = $roleLabels[$userRole] ?? $roleLabels['employee'];
?>

<link rel="stylesheet" href="/modal.css">

<!-- profile modal -->
<div class="modal fade mia-modal" id="modalProfile" tabindex="-1" aria-labelledby="modalProfileLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalProfileLabel">Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mia-profile-hero">
                    <div class="mia-profile-avatar-lg"><?php echo htmlspecialchars($userInitial); ?></div>
                    <div class="mia-profile-hero-name"><?php echo htmlspecialchars($userName); ?></div>
                    <div class="mia-profile-hero-email"><?php echo htmlspecialchars($userEmail); ?></div>
                    <span class="mia-role-badge <?php echo $roleMeta['class']; ?>">
                        <?php echo $roleMeta['label']; ?>
                    </span>
                </div>

                <p class="mia-modal-section-title">Account Details</p>
                <div class="mia-field-group">
                    <div class="mia-field-row">
                        <span class="mia-field-label">Full name</span>
                        <span class="mia-field-value"><?php echo htmlspecialchars($userName); ?></span>
                    </div>
                    <div class="mia-field-row">
                        <span class="mia-field-label">Email</span>
                        <span class="mia-field-value"><?php echo htmlspecialchars($userEmail); ?></span>
                    </div>
                    <div class="mia-field-row">
                        <span class="mia-field-label">Role</span>
                        <span class="mia-field-value"><?php echo $roleMeta['label']; ?></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="mia-cancel-btn" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<!-- settings modal -->
<div class="modal fade mia-modal" id="modalSettings" tabindex="-1" aria-labelledby="modalSettingsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalSettingsLabel">Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- <p class="mia-modal-section-title">Messaging</p>
                <div class="mia-field-group">
                    <div class="mia-toggle-row">
                        <div class="mia-toggle-row-text">
                            <span class="mia-toggle-label">Send on Enter</span>
                            <span class="mia-toggle-desc">Press Enter to send, Shift+Enter for new line</span>
                        </div>
                        <input class="form-check-input" type="checkbox" id="prefEnterSend" checked>
                    </div>
                    <div class="mia-toggle-row">
                        <div class="mia-toggle-row-text">
                            <span class="mia-toggle-label">Notification sounds</span>
                            <span class="mia-toggle-desc">Play a sound when a new message arrives</span>
                        </div>
                        <input class="form-check-input" type="checkbox" id="prefSounds" checked>
                    </div>
                </div> -->

                <p class="mia-modal-section-title">Display</p>
                <div class="mia-field-group">
                    <div class="mia-toggle-row">
                        <div class="mia-toggle-row-text">
                            <span class="mia-toggle-label">Time format</span>
                            <span class="mia-toggle-desc">Choose how timestamps are displayed</span>
                        </div>
                        <div class="mia-segment" id="prefTimeFormat">
                            <button class="mia-segment-btn active" data-value="24h">24h</button>
                            <button class="mia-segment-btn" data-value="12h">12h</button>
                        </div>
                    </div>
                    <div class="mia-toggle-row">
                        <div class="mia-toggle-row-text">
                            <span class="mia-toggle-label">Chat density</span>
                            <span class="mia-toggle-desc">Control spacing between messages</span>
                        </div>
                        <div class="mia-segment" id="prefDensity">
                            <button class="mia-segment-btn active" data-value="comfortable">Comfortable</button>
                            <button class="mia-segment-btn" data-value="compact">Compact</button>
                        </div>
                    </div>
                </div>

                <p class="mia-modal-section-title">Security</p>
                <div class="mia-field-group">
                    <div class="mia-field-row">
                        <span class="mia-field-label">Password</span>
                        <span class="mia-field-value" style="color:#6b7280;font-weight:400;">••••••••••</span>
                    </div>
                </div>

                <p class="mia-modal-section-title">About</p>
                <div class="mia-field-group">
                    <div class="mia-field-row">
                        <span class="mia-field-label">Application</span>
                        <span class="mia-field-value">Make-It-All</span>
                    </div>
                    <div class="mia-field-row">
                        <span class="mia-field-label">Version</span>
                        <span class="mia-field-value">1.0.0</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="mia-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                <button class="mia-save-btn" id="settingsSaveBtn">Save changes</button>
            </div>

        </div>
    </div>
</div>

<script>
// Segment controls
document.querySelectorAll('.mia-segment').forEach(seg => {
    seg.querySelectorAll('.mia-segment-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            seg.querySelectorAll('.mia-segment-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });
});

function flashSaved(btnId) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    const original = btn.textContent;
    btn.textContent = 'Saved ✓';
    btn.style.background = '#16a34a';
    setTimeout(() => {
        btn.textContent = original;
        btn.style.background = '';
    }, 1800);
}

document.getElementById('settingsSaveBtn')?.addEventListener('click', () => {
    const activeDensityBtn = document.querySelector('#prefDensity .mia-segment-btn.active');
    if (activeDensityBtn) {
        chatDensity = activeDensityBtn.dataset.value;
        localStorage.setItem('chatDensity', chatDensity);
        applyDensity();
    }

    const activeTimeBtn = document.querySelector('#prefTimeFormat .mia-segment-btn.active');
    if (activeTimeBtn) {
        timeFormat = activeTimeBtn.dataset.value;
        localStorage.setItem('timeFormat', timeFormat);
        const conversation = allConversations.find(c => c.id === activeId);
        if (conversation) renderMessages(conversation);
        renderSidebar(allConversations);
    }

    flashSaved('settingsSaveBtn');
});

document.getElementById('modalSettings')?.addEventListener('show.bs.modal', () => {
    const savedFormat = localStorage.getItem('timeFormat') || '24h';
    document.querySelectorAll('#prefTimeFormat .mia-segment-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.value === savedFormat);
    });

    const savedDensity = localStorage.getItem('chatDensity') || 'comfortable';
    document.querySelectorAll('#prefDensity .mia-segment-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.value === savedDensity);
    });

    const savedEnterSend = localStorage.getItem('enterSend');
    if (savedEnterSend !== null) {
        const checkbox = document.getElementById('prefEnterSend');
        if (checkbox) checkbox.checked = savedEnterSend === 'true';
    }
});
</script>