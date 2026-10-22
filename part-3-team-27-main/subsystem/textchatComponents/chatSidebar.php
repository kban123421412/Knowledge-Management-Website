<aside class="chat-sidebar" id="chatSidebar">
    <div class="chat-sidebar-top">
        <div class="chat-sidebar-toolbar">
            <button class="sidebar-icon-btn" id="newChatBtn" title="Create new chat or group"
                    data-bs-toggle="modal" data-bs-target="#modalNewChat">
                <i class="bi bi-person-plus"></i>
            </button>

            <div class="chat-sidebar-search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search chats">
            </div>

            <button class="sidebar-icon-btn" id="sidebarCollapseBtn" title="Collapse sidebar">
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>
        </div>
    </div>

    <div class="chat-list-wrapper">
        <div class="chat-list">
            <!-- Populated by JS from CSV -->
        </div>
    </div>

    <div class="chat-sidebar-bottom">
        <button class="sidebar-footer-btn" id="openTrashBtn" title="View recently deleted chats">
            <i class="bi bi-trash3"></i>
            <span>Recently Deleted</span>
        </button>
        <button class="sidebar-footer-btn delete-chat-btn" id="deleteChatBtn" title="Delete current chat">
            <i class="bi bi-trash"></i>
            <span>Delete Chat</span>
        </button>
    </div>
</aside>