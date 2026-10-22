<div class="modal fade mia-modal" id="modalNewChat" tabindex="-1" aria-labelledby="modalNewChatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalNewChatLabel">
                    <i class="bi bi-people me-2"></i>New Chat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="padding: 0;">

                <!-- search and filter -->
                <div style="padding: 16px 16px 0;">
                    <div class="mia-field-row" style="padding: 0; background: transparent;">
                        <div class="chat-sidebar-search" style="flex: 1;">
                            <i class="bi bi-search"></i>
                            <input type="text" id="newChatSearch" placeholder="Search name...">
                        </div>
                        <div style="display: flex; gap: 6px; margin-left: 10px;">
                            <button class="nc-filter-btn active" data-filter="all">All</button>
                            <button class="nc-filter-btn" data-filter="project_manager">Manager</button>
                            <button class="nc-filter-btn" data-filter="team_leader">Team Leader</button>
                            <button class="nc-filter-btn" data-filter="employee">Employee</button>
                        </div>
                    </div>
                </div>

                <!-- selected users -->
                <div style="padding: 12px 16px 0;">
                    <div id="ncSelectedWrap" style="display:none; margin-bottom: 8px;">
                        <p class="mia-modal-section-title" style="margin-bottom: 6px;">Selected</p>
                        <div id="ncSelectedChips" style="display: flex; flex-wrap: wrap; gap: 6px;"></div>
                    </div>

                    <!-- group name when 2+ selected -->
                    <div id="ncGroupNameWrap" style="display:none; margin-bottom: 8px;">
                        <p class="mia-modal-section-title" style="margin-bottom: 6px;">Group Name</p>
                        <input type="text" id="ncGroupName" class="chat-input-field" 
                               placeholder="Enter group name..." style="width:100%; border-radius: 12px;">
                    </div>
                </div>

                <!-- user list -->
                <div id="ncUserList" style="max-height: 340px; overflow-y: auto; padding: 8px 16px 16px;">
                    <p style="color: #9ca3af; font-size: 0.9rem; padding: 16px 0;">Loading users...</p>
                </div>

            </div>

            <div class="modal-footer">
                <button class="mia-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                <button class="mia-save-btn" id="ncStartBtn" disabled style="opacity: 0.5;">
                    Start Chat
                </button>
            </div>

        </div>
    </div>
</div>