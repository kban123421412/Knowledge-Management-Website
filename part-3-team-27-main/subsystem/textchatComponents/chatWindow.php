<div class="chat-window">
    <div class="chat-window-header">
        <div>
            <span class="chat-window-user"></span>
        </div>
        <button class="chat-info-btn" id="chatInfoBtn" title="Contact info">
            <i class="bi bi-info-circle"></i>
        </button>
    </div>

    <div class="chat-body-wrapper">
        <div class="chat-messages">
            <!-- Populated by JS from CSV -->
        </div>

        <div class="chat-info-panel" id="chatInfoPanel">
            <div class="info-panel-header">
                <span class="info-panel-title">Info</span>
                <button class="info-panel-close" id="chatInfoClose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="info-avatar-wrap">
                <div class="info-avatar" id="infoAvatar"></div>
                <div class="info-name" id="infoName"></div>
                <div class="info-since" id="infoSince"></div>
            </div>

            <div class="info-tabs">
                <button class="info-tab active" data-tab="details">Details</button>
                <button class="info-tab" data-tab="media">Media</button>
            </div>

            <div class="info-tab-content" id="infoTabDetails">
                <div class="info-stats" id="infoStats"></div>
            </div>

            <div class="info-tab-content" id="infoTabMedia" style="display:none;">
                <div class="info-media-empty">
                    <i class="bi bi-image" style="font-size:2rem;color:#d1d5db;"></i>
                    <p>No media shared yet</p>
                </div>
            </div>
        </div>
    </div>
</div>
