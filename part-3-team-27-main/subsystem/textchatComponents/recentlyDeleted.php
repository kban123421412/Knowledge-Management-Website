<!-- Confirmation Modal -->
<div class="modal fade mia-modal" id="deleteChatConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Chat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1.4rem 1.6rem;">
                <p style="color:#374151;margin:0;">Are you sure you want to delete this chat?</p>
            </div>
            <div class="modal-footer">
                <button class="mia-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                <button class="mia-save-btn" id="confirmDeleteChatBtn" style="background:#ef4444;">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Recently Deleted Modal -->
<div class="modal fade" id="trashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content trash-modal-content d-flex flex-column overflow-hidden">

            <div class="trash-modal-header px-4 py-3 d-flex justify-content-between align-items-center">
                <button class="btn btn-link text-danger text-decoration-none p-0 fw-medium" 
                        id="emptyTrashBtn" style="font-size: 0.875rem;">Empty Trash</button>
                <h2 class="fs-5 fw-semibold text-dark mb-0">Recently Deleted Chats</h2>
                <button type="button" id="closeTrashBtn" 
                        class="btn btn-link text-danger text-decoration-none p-0 fw-medium d-flex align-items-center gap-1" 
                        style="font-size: 0.875rem;">
                    close <span class="fs-4 lh-1">&times;</span>
                </button>
            </div>

            <div class="flex-grow-1 overflow-auto px-4 py-2" id="trashList">
                <p class="text-secondary text-center py-4" style="font-size:0.9rem;">Loading...</p>
            </div>

            <div class="px-4 py-3 d-flex justify-content-between align-items-center gap-3">
                <button class="btn btn-link text-danger text-decoration-none p-0 fw-semibold" 
                        id="permanentDeleteBtn" style="display:none;">
                    ❌ Delete permanently
                </button>
                <button class="btn btn-link text-dark text-decoration-none p-0 fw-semibold ms-auto" 
                        id="restoreBtn" style="display:none;">
                    ⟳ Restore
                </button>
            </div>
        </div>
    </div>
</div>