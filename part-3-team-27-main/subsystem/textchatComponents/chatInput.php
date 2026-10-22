<div class="chat-input-bar">
    <div class="attachment-preview" id="attachmentPreview" style="display:none;">
        <div class="attachment-preview-content">
            <i class="bi bi-file-earmark" id="attachmentIcon"></i>
            <span id="attachmentName"></span>
            <button class="attachment-remove" id="attachmentRemove">&times;</button>
        </div>
    </div>
    <div class="emoji-picker-container" id="emojiPickerContainer" style="display:none;">
        <div class="emoji-picker-grid" id="emojiGrid"></div>
    </div>
    <div class="chat-input-row">
        <button class="chat-icon-btn" id="fileUploadBtn">
            <i class="bi bi-paperclip"></i>
        </button>

        <input type="file" id="fileInput" accept="image/*,video/*,.pdf" style="display:none;" multiple>

        <input type="text" class="chat-input-field" placeholder="Write a message...">

        <button class="chat-icon-btn" id="emojiBtn">
            <i class="bi bi-emoji-smile"></i>
        </button>

        <button class="chat-send-btn">
            <i class="bi bi-send"></i>
        </button>
    </div>
</div>
