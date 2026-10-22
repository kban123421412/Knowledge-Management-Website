<?php 
require_once __DIR__ . '/../subsystems.php';
require_once __DIR__ . '/../dbConfig.php';


$currentUserId = $_SESSION['user_id'] ?? 4;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text Chat</title>

    <link rel="stylesheet" href="/topnavbar.css">
    <link rel="stylesheet" href="/subsystem/css/chat.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="chat-page-body">

    <div class="chat-layout">
        <?php include __DIR__ . '/textchatComponents/chatSidebar.php'; ?>

        <section class="chat-main-panel">
            <?php include __DIR__ . '/textchatComponents/chatWindow.php'; ?>
            <?php include __DIR__ . '/textchatComponents/chatInput.php'; ?>
        </section>
    </div>

    <?php include __DIR__ . '/textchatComponents/newChatModal.php'; ?>
    <?php include __DIR__ . '/textchatComponents/recentlyDeleted.php'; ?>

    <!-- Fix for analytics page. Removed duplicate bootstrap scripts from topnavbar, place in individual textchat and
        analytics files instead -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const CURRENT_USER_ID = <?php echo json_encode($currentUserId); ?>;
        let timeFormat = localStorage.getItem('timeFormat') || '24h';
        let chatDensity = localStorage.getItem('chatDensity') || 'comfortable';
        let RAW_DATA = null;
        let ALL_USERS = null;
        let allConversations = null;
        let activeId = null;

        init();

        async function init() {
            await fetchConversations(CURRENT_USER_ID);
            await fetchAllUsers(CURRENT_USER_ID)
            if (!RAW_DATA) return;

            allConversations = RAW_DATA.map(c => ({
            ...c,
            avatarColour: nameToColour(c.name || ''),
            messages: c.messages
                .map(m => ({ ...m, timestamp: new Date(m.timestamp) }))
                .sort((a, b) => a.timestamp - b.timestamp),
            })).sort((a, b) => b.messages.at(-1)?.timestamp - a.messages.at(-1)?.timestamp);

            activeId = allConversations[0]?.id ?? null;

            renderSidebar(allConversations);

            if (activeId) {
                const firstConv = allConversations.find(c => c.id === activeId);
                if (firstConv) renderMessages(firstConv);
            }

            let oldestMessageId = allConversations.find(c => c.id === activeId)?.messages[0]?.id || null;
        }

        async function fetchAllUsers(userid){
            try {
                const response = await fetch(`/api/get_users.php?currentUserId=${userid}`);
                const result = await response.json();

                if(result.success) {
                    ALL_USERS = result.users
                } else {
                    console.error('Failure getting users:', result.error);
                }
            } catch (error) {
                console.error('Error getting users for groupchats:', error);
            }
        }

        async function fetchConversations(userid){
            try {
                const response = await fetch(`/api/get_conversations.php?currentUserId=${userid}`);
                const result = await response.json();

                if(result.success) {
                    RAW_DATA = result.conversations;
                } else {
                    console.error('Failure getting users:', result.error);
                }
            } catch (error) {
                console.error('Error getting users for groupchats:', error);
            }
        }

        // Set the correct segment button as active on load
        document.querySelectorAll('#prefTimeFormat .mia-segment-btn').forEach(btn => {
            if (btn.dataset.value === timeFormat) btn.classList.add('active');
            else btn.classList.remove('active');
        });

        function applyDensity() {
            const area = document.querySelector('.chat-messages');
            area.classList.toggle('density-compact', chatDensity === 'compact');
        }

        applyDensity();

        function nameToColour(name) {
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
                hash = ((hash << 5) - hash) + name.charCodeAt(i);
                hash = hash & 0xffffff;
            }
            return '#' + Math.abs(hash).toString(16).padStart(6, '0').slice(0, 6);
        }

        // helpers 
        function formatTime(date) {
            if (timeFormat === '12h') {
                return date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });
            }
            return date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: false });
        }

        function formatDate(date) {
            return date.toLocaleDateString('en-GB', {
                weekday: 'long', day: '2-digit', month: '2-digit', year: '2-digit'
            });
        }

        function relativeLabel(date) {
            const today = new Date(); today.setHours(0, 0, 0, 0);
            const d = new Date(date); d.setHours(0, 0, 0, 0);
            const diff = Math.round((today - d) / 86400000);
            if (diff === 0) return formatTime(date);
            if (diff === 1) return 'Yesterday';
            if (diff < 7) return date.toLocaleDateString('en-GB', { weekday: 'long' });
            return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }

        function renderMessageContent(msg) {
            const text = msg.text;
            const urlRegex = /(https?:\/\/[^\s]+)/;
            const urlMatch = text.match(urlRegex);

            // if url, embed media
            if (urlMatch && urlMatch[0] === text.trim()) {
                const url = text.trim();
                const ext = url.split('.').pop().toLowerCase();

                if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                    return `<img src="${url}" style="max-width: 300px; border-radius: 8px; margin-top: 8px; cursor: pointer;" onclick="window.open('${url}', '_blank')">`;
                }
                if (['mp4', 'webm', 'mov'].includes(ext)) {
                    return `<video controls style="max-width: 300px; border-radius: 8px; margin-top: 8px;">
                      <source src="${url}">
                      Your browser doesn't support video.
                    </video>`;
                }
                if (['pdf'].includes(ext)) {
                    const filename = url.split('/').pop();
                    return `<a href="${url}" class="btn btn-sm btn-outline-primary" target="_blank">
                      <i class="bi bi-file-pdf"></i> ${filename}
                    </a>`;
                }
            }

            return `<p>${text}</p>`;
        }

        // render sidebar
        function renderSidebar(conversations) {
            const list = document.querySelector('.chat-list');
            list.innerHTML = '';

            //Small fix - conversations is no longer an array?
            if (!conversations) {
                list.innerHTML = '<p style="padding:16px;color:#6b7280;font-size:0.9rem;">No conversations found</p>';
                return;
            }

            for (const conversation of conversations) {
                const isActive = conversation.id === activeId;
                const isGroup = conversation.isGroup;
                const lastMsg = conversation.messages.at(-1);
                const lastMsgByMe = lastMsg?.direction === 'outgoing';

                const item = document.createElement('div');
                item.className = `chat-list-item${isActive ? ' active' : ''}`;
                item.dataset.conversationId = conversation.id;

                const isOnline = !conversation.isGroup && onlineUsers.has(conversation.contactId);
                item.innerHTML = `
            <div class="avatar-wrap">
                <div class="chat-avatar" style="background:${conversation.avatarColour}">${conversation.avatarLetter}</div>
                ${!conversation.isGroup ? `<span class="status-dot ${isOnline ? 'online' : ''}" data-user-id="${conversation.contactId}"></span>` : ''}
            </div>
            <div class="chat-list-text">
                <div class="chat-list-header">
                    <span class="chat-name">${conversation.name}</span>
                    <span class="chat-time">${lastMsg ? relativeLabel(lastMsg.timestamp) : ''}</span>
                </div>
                <p class="chat-preview">
                    ${isGroup ?
                        (lastMsg ? `${lastMsgByMe ? 'You' : lastMsg.senderName}: ${lastMsg.text}` : '<em>No messages.</em>')
                        : lastMsg ? (lastMsg.direction === 'outgoing' ? 'You: ' : '') + lastMsg.text : ''}
                </p>
            </div>`;

                item.addEventListener('click', () => selectConversation(conversation.id));
                list.appendChild(item);
            }
        }

        // render messages from database
        function renderMessages(conversation) {
            const headerEl = document.querySelector('.chat-window-user');
            const isOnline = !conversation.isGroup && onlineUsers.has(conversation.contactId);
            headerEl.innerHTML = conversation.name + (!conversation.isGroup ? ` <span style="font-size:0.75rem;color:${isOnline ? '#10b981' : '#9ca3af'};font-weight:600;">${isOnline ? 'Online' : 'Offline'}</span>` : '');

            const area = document.querySelector('.chat-messages');
            area.innerHTML = '';
            let lastDateStr = '';

            for (const msg of conversation.messages) {
                const dateStr = formatDate(msg.timestamp);
                const isOut = msg.direction === 'outgoing';

                if (dateStr !== lastDateStr) {
                    lastDateStr = dateStr;
                    const divider = document.createElement('div');
                    divider.className = 'chat-date-divider';
                    divider.textContent = dateStr;
                    area.appendChild(divider);
                }

                const row = document.createElement('div');
                row.className = `message-row ${isOut ? 'sent-row' : 'received-row'}`;

                if (msg.deleted) {
                    row.innerHTML = `
                <div class="message-bubble ${isOut ? 'sent' : 'received'} deleted-message">
                    <p><i class="bi bi-ban"></i> ${isOut ? 'You deleted this message' : 'This message was deleted'}</p>
                </div>`;
                } else if (!isOut) {
                    row.innerHTML = `
                <div class="message-avatar">${msg.senderName.charAt(0)}</div>
                <div class="message-bubble received">
                    ${renderMessageContent(msg)}
                    <span class="message-time">${formatTime(msg.timestamp)}</span>
                </div>`;
                } else {
                    row.innerHTML = `
                <div class="message-bubble sent">
                    ${renderMessageContent(msg)}
                    <span class="message-time">${formatTime(msg.timestamp)}</span>
                    <button class="msg-delete-btn" onclick="deleteMessage(${msg.id}, ${conversation.id})" title="Delete"><i class="bi bi-trash3"></i></button>
                </div>`;
                }

                area.appendChild(row);
            }

            area.scrollTop = area.scrollHeight;
            applyDensity();
        }

        // select conversation on the sidebar
        async function selectConversation(id) {
            activeId = id;
            const conversation = allConversations.find(c => c.id === id);

            // Reset pagination state for the new conversation
            oldestMessageId = conversation?.messages[0]?.id ?? null;

            renderSidebar(allConversations);
            if (conversation) renderMessages(conversation);
        }

        // search
        document.querySelector('.chat-sidebar-search input').addEventListener('input', e => {
            const q = e.target.value.toLowerCase();
            const filtered = allConversations.filter(c =>
                c.name.toLowerCase().includes(q) ||
                c.messages.some(m => m.text.toLowerCase().includes(q))
            );
            renderSidebar(filtered);
        });

        async function sendMessage(messageContent = null) {
            const input = document.querySelector('.chat-input-field');
            const text = messageContent || input.value.trim();
            if (!text || !activeId) return;

            const conversation = allConversations.find(c => c.id === activeId);
            if (!conversation) return;

            const formData = new FormData();
            formData.append('conversation_id', conversation.id);
            formData.append('content', text);
            formData.append('sender_id', CURRENT_USER_ID);

            try {
                const response = await fetch('/api/send_message.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    conversation.messages.push({
                        id: result.data.message_id,
                        direction: 'outgoing',
                        senderName: "You",
                        text,
                        timestamp: new Date(result.data.timestamp),
                    });
                    input.value = '';
                    renderSidebar(allConversations);
                    renderMessages(conversation);

                    if (typeof socket !== 'undefined' && socket.readyState === WebSocket.OPEN) {
                        socket.send(JSON.stringify({
                            type: 'message',
                            conversation_id: conversation.id,
                            message_id: result.data.message_id,
                            content: text,
                            created_at: result.data.timestamp,
                        }));
                    }
                } else {
                    console.error('Failed to send message:', result.error);
                    alert('Failed to send message. Please try again.');
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        }

        document.querySelector('.chat-send-btn').addEventListener('click', () => sendMessage());
        document.querySelector('.chat-input-field').addEventListener('keydown', e => {
            const enterSend = document.getElementById('prefEnterSend')?.checked ?? true;
            if (e.key === 'Enter' && !e.shiftKey && enterSend) {
                e.preventDefault();
                sendMessage();
            }
        });

        // upload handler
        document.getElementById('fileUploadBtn').addEventListener('click', () => {
            document.getElementById('fileInput').click();
        });

        const emojis = ['😀','😂','😍','🥰','😎','🤔','😅','😢','😡','🥳','👍','👎','❤️','🔥','🎉','✅','⭐','💯','🙏','👋','😊','🤣','😭','😱','🤝','💪','🙌','😏','🤷','👀','💀','🫡','😤','🥺','✨','💕','🎯','🚀','💡','⚡'];

        const emojiGrid = document.getElementById('emojiGrid');
        const emojiContainer = document.getElementById('emojiPickerContainer');
        const emojiBtn = document.getElementById('emojiBtn');

        emojiGrid.innerHTML = emojis.map(e => `<span class="emoji-item">${e}</span>`).join('');

        emojiBtn.addEventListener('click', () => {
            emojiContainer.style.display = emojiContainer.style.display === 'none' ? 'block' : 'none';
        });

        emojiGrid.addEventListener('click', (e) => {
            if (e.target.classList.contains('emoji-item')) {
                const input = document.querySelector('.chat-input-field');
                input.value += e.target.textContent;
                input.focus();
            }
        });

        document.addEventListener('click', (e) => {
            if (!emojiContainer.contains(e.target) && e.target !== emojiBtn && !emojiBtn.contains(e.target)) {
                emojiContainer.style.display = 'none';
            }
        });

        async function deleteMessage(messageId, conversationId) {
            if (!confirm('Delete this message?')) return;

            try {
                const formData = new FormData();
                formData.append('message_id', messageId);

                const response = await fetch('/api/delete_message.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    const conv = allConversations.find(c => c.id === conversationId);
                    if (conv) {
                        const msg = conv.messages.find(m => m.id === messageId);
                        if (msg) { msg.deleted = true; msg.text = ''; }
                        renderMessages(conv);
                        renderSidebar(allConversations);
                        if (document.getElementById('chatInfoPanel')?.classList.contains('open')) {
                            populateInfoPanel(conv);
                        }
                    }
                } else {
                    alert(result.error || 'Failed to delete message');
                }
            } catch (error) {
                alert('Error deleting message');
            }
        }

        let pendingFiles = [];

        function getFileIcon(name) {
            const ext = name.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return 'bi bi-file-image';
            if (['mp4', 'webm', 'mov'].includes(ext)) return 'bi bi-file-play';
            if (ext === 'pdf') return 'bi bi-file-pdf';
            return 'bi bi-file-earmark';
        }

        function renderAttachmentPreviews() {
            const preview = document.getElementById('attachmentPreview');
            if (pendingFiles.length === 0) {
                preview.style.display = 'none';
                return;
            }
            preview.style.display = 'block';
            preview.innerHTML = pendingFiles.map((f, i) => `
                <div class="attachment-preview-content">
                    <i class="${getFileIcon(f.name)}"></i>
                    <span>${f.name}</span>
                    <button class="attachment-remove" onclick="removeAttachment(${i})">&times;</button>
                </div>
            `).join('');
        }

        window.removeAttachment = function(index) {
            pendingFiles.splice(index, 1);
            renderAttachmentPreviews();
        };

        document.getElementById('fileInput').addEventListener('change', (e) => {
            for (const file of e.target.files) {
                pendingFiles.push(file);
            }
            renderAttachmentPreviews();
            document.getElementById('fileInput').value = '';
        });

        const originalSendMessage = sendMessage;
        sendMessage = async function(messageContent = null) {
            if (pendingFiles.length > 0 && !messageContent) {
                let uploadBtn = document.getElementById('fileUploadBtn');
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

                for (const file of pendingFiles) {
                    const uploadFormData = new FormData();
                    uploadFormData.append('file', file);

                    try {
                        const response = await fetch('/api/uploads.php', {
                            method: 'POST',
                            body: uploadFormData
                        });
                        const result = await response.json();

                        if (result.success) {
                            await originalSendMessage(location.origin + result.data.url);
                        } else {
                            alert('Upload failed: ' + result.error);
                        }
                    } catch (error) {
                        alert('Upload error for ' + file.name);
                    }
                }

                pendingFiles = [];
                renderAttachmentPreviews();
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="bi bi-paperclip"></i>';
                return;
            }
            await originalSendMessage(messageContent);
        };

        // info panel stuff
        const infoBtn = document.getElementById('chatInfoBtn');
        const infoPanel = document.getElementById('chatInfoPanel');
        const infoClose = document.getElementById('chatInfoClose');

        function populateInfoPanel(conversation) {
            document.getElementById('infoTabDetails').style.display = 'block';
            document.getElementById('infoTabMedia').style.display = 'none';
            document.querySelectorAll('.info-tab').forEach(t => t.classList.remove('active'));
            document.querySelector('.info-tab[data-tab="details"]').classList.add('active');

            // avatar
            const av = document.getElementById('infoAvatar');
            av.textContent = conversation.avatarLetter;
            av.className = 'info-avatar chat-avatar';
            av.style.background = conversation.avatarColour;
            // name
            document.getElementById('infoName').textContent = conversation.name;

            // earliest message date
            const earliest = conversation.messages.at(0)?.timestamp;
            document.getElementById('infoSince').textContent = earliest
                ? 'Chatting since ' + earliest.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' })
                : '';

            // stats
            const total = conversation.messages.length;
            const sent = conversation.messages.filter(m => m.direction === 'outgoing').length;
            const received = total - sent;

            document.getElementById('infoStats').innerHTML = `
        <div class="info-stat-row">
            <span class="info-stat-label">Total messages</span>
            <span class="info-stat-value">${total}</span>
        </div>
        <div class="info-stat-row">
            <span class="info-stat-label">Sent</span>
            <span class="info-stat-value">${sent}</span>
        </div>
        <div class="info-stat-row">
            <span class="info-stat-label">Received</span>
            <span class="info-stat-value">${received}</span>
        </div>`;

            // if gc show members
            if (conversation.isGroup) {
                const members = ALL_USERS.filter(u => conversation.members.map(x => x.id).includes(u.id));
                const wrap = document.createElement('div');
                wrap.className = 'info-members-wrap';
                wrap.innerHTML = `
            <div class="info-members-title">Members</div>
            ${members.map(m => `<div class="info-member-row">
                    <div class="chat-avatar" style="background:${nameToColour(m.name)}">${m.name.charAt(0)}</div>
                    <span>${m.name}</span>
                </div>`).join('')}
        `;
                document.getElementById('infoTabDetails').appendChild(wrap);

                //Leave group button for gcs - need to delete the button when info closed
                const leaveGroupButton = document.createElement('button');
                leaveGroupButton.className = "btn btn-outline-danger w-80 mt-3";
                leaveGroupButton.id = "leaveGroupBtn";
                leaveGroupButton.textContent = "Leave Group";
                document.getElementById('infoTabDetails').appendChild(leaveGroupButton);

                leaveGroupButton.addEventListener('click', () => leaveGroup(conversation.id));
            }

            // media tab
            function getMediaMessages(conversation) {
                const urlPattern = /https?:\/\/[^\s]+/gi;
                return conversation.messages.filter(m => urlPattern.test(m.text));
            }

            const mediaMessages = getMediaMessages(conversation);
            const mediaTab = document.getElementById('infoTabMedia');

            if (!mediaMessages.length) {
                mediaTab.innerHTML = `
            <div class="info-media-empty">
                <i class="bi bi-image" style="font-size:2rem;color:#d1d5db;"></i>
                <p>No media shared yet</p>
            </div>`;
            } else {
                const urlPattern = /https?:\/\/[^\s]+/gi;
                mediaTab.innerHTML = '<div class="info-media-grid">' + mediaMessages.map(m => {
                    const urls = m.text.match(urlPattern);
                    return urls.map(url => {
                        const ext = url.split('.').pop().toLowerCase().split('?')[0];
                        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                            return `<a href="${url}" target="_blank" class="info-media-thumb"><img src="${url}" alt=""></a>`;
                        } else if (['mp4', 'webm', 'mov'].includes(ext)) {
                            return `<a href="${url}" target="_blank" class="info-media-thumb info-media-video"><i class="bi bi-play-circle-fill"></i></a>`;
                        } else if (ext === 'pdf') {
                            const filename = url.split('/').pop();
                            return `<a href="${url}" target="_blank" class="info-media-file"><i class="bi bi-file-pdf"></i><span>${filename}</span></a>`;
                        }
                        return `<a href="${url}" target="_blank" class="info-media-file"><i class="bi bi-file-earmark"></i><span>${url.split('/').pop()}</span></a>`;
                    }).join('');
                }).join('') + '</div>';
            }
        }

        //Leave group functionality
        function leaveGroup(conversationId) {
            if (!confirm('Are you sure you want to leave this group?')) return;
            fetch('/api/leave_group.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ conversation_id: conversationId })
            })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        allConversations = allConversations.filter(c => c.id !== conversationId);
                        // If the active conversation was the one left, switch to another
                        renderSidebar(allConversations);

                        if (allConversations.length > 0) {
                            renderMessages(allConversations[0]);
                            //Run selectedSidebar for UI
                            selectConversation(allConversations[0]?.id);
                            //close info tab
                            closeInfoPanel();
                        } else {
                            //No other conversations to render.
                        }
                    }
                })
                .catch(error => {
                    console.error('Error leaving group:', error);
                    alert('An error occurred while leaving the group. Please try again.');
                });
        }

        function openInfoPanel(conversation) {
            populateInfoPanel(conversation);
            infoPanel.classList.add('open');
            infoBtn.classList.add('active');
        }

        infoBtn.addEventListener('click', () => {
            if (infoPanel.classList.contains('open')) {
                closeInfoPanel();
            } else {
                const conversation = allConversations.find(c => c.id === activeId);
                if (conversation) openInfoPanel(conversation);
            }
        });

        // update panel when switching conversations if open
        const _originalSelectConversation = selectConversation;
        selectConversation = function (id) {
            _originalSelectConversation(id);
            if (infoPanel.classList.contains('open')) {
                const conversation = allConversations.find(c => c.id === id);
                if (conversation) populateInfoPanel(conversation);
            }
        };

        //Close panel, also adds event listener for close button
        function closeInfoPanel() {
            infoPanel.classList.remove('open');
            infoBtn.classList.remove('active');

            const detailsTab = document.getElementById('infoTabDetails');

            const membersWrap = detailsTab.querySelector('.info-members-wrap');
            if (membersWrap) membersWrap.remove();
            const leaveBtn = detailsTab.querySelector('button');
            if (leaveBtn) leaveBtn.remove();
        }

        infoClose.addEventListener('click', closeInfoPanel);

        document.querySelectorAll('.info-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.info-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById('infoTabDetails').style.display = tab.dataset.tab === 'details' ? 'block' : 'none';
                document.getElementById('infoTabMedia').style.display = tab.dataset.tab === 'media' ? 'block' : 'none';
            });
        });

        renderSidebar(allConversations);
        if (activeId) renderMessages(allConversations.find(c => c.id === activeId));

        // delete chat and recently deleted
        const deleteChatBtn = document.getElementById('deleteChatBtn');
        const confirmDeleteChatBtn = document.getElementById('confirmDeleteChatBtn');
        const trashBtn = document.getElementById('openTrashBtn');
        const trashModalElement = document.getElementById('trashModal');
        const trashCloseBtn = document.getElementById('closeTrashBtn');
        const emptyTrashBtn = document.getElementById('emptyTrashBtn');
        const permanentDeleteBtn = document.getElementById('permanentDeleteBtn');
        const restoreBtn = document.getElementById('restoreBtn');

        const trashModalInstance = new bootstrap.Modal(trashModalElement);
        const deleteChatConfirmModal = new bootstrap.Modal(document.getElementById('deleteChatConfirmModal'));

        let selectedTrashIds = [];

        if (deleteChatBtn) {
            deleteChatBtn.addEventListener('click', () => {
                if (!activeId) return;
                deleteChatConfirmModal.show();
            });
        }

        // hide conversation (move to trash)
        if (confirmDeleteChatBtn) {
            confirmDeleteChatBtn.addEventListener('click', async () => {
                if (!activeId) return;

                try {
                    const res = await fetch('/api/hide_conversation.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ conversation_id: activeId })
                    });
                    const data = await res.json();

                    if (data.success) {
                        deleteChatConfirmModal.hide();
                        // Remove from sidebar
                        allConversations = allConversations.filter(c => c.id !== activeId);
                        activeId = allConversations[0]?.id ?? null;
                        renderSidebar(allConversations);
                        if (activeId) renderMessages(allConversations.find(c => c.id === activeId));
                        else {
                            document.querySelector('.chat-window-user').textContent = '';
                            document.querySelector('.chat-messages').innerHTML = '';
                        }
                    } else {
                        alert('Failed to delete chat: ' + data.error);
                    }
                } catch (e) {
                    alert('An error occurred.');
                }
            });
        }

        // load hidden conversations
        async function loadTrash() {
            const list = document.getElementById('trashList');
            list.innerHTML = '<p class="text-secondary text-center py-4" style="font-size:0.9rem;">Loading...</p>';
            selectedTrashIds = [];
            permanentDeleteBtn.style.display = 'none';

            try {
                const res = await fetch('/api/deleted_conversations.php');
                const data = await res.json();

                if (!data.success || !data.conversations.length) {
                    list.innerHTML = '<p class="text-secondary text-center py-4" style="font-size:0.9rem;">No deleted chats.</p>';
                    return;
                }

                list.innerHTML = data.conversations.map(c => `
            <div class="d-flex align-items-center gap-3 py-3 trash-border-b" data-id="${c.id}">
                <input type="checkbox" class="form-check-input mt-0 trash-check cursor-pointer shadow-none" 
                       style="width:1rem;height:1rem;" data-id="${c.id}">
                <div class="rounded-circle trash-avatar d-flex align-items-center justify-content-center fw-medium">
                    ${c.avatar_letter}
                </div>
                <div class="flex-grow-1">
                    <p class="fw-semibold text-dark mb-0">${c.contact_name}</p>
                    <p class="text-secondary mb-0" style="font-size:0.875rem;">${c.message_count} message${c.message_count != 1 ? 's' : ''}</p>
                </div>
                <span class="text-secondary" style="font-size:0.875rem;">${timeAgo(c.hidden_at)}</span>
            </div>
        `).join('');

                // checkbox listeners
                list.querySelectorAll('.trash-check').forEach(cb => {
                    cb.addEventListener('change', () => {
                        const id = parseInt(cb.dataset.id);
                        if (cb.checked) {
                            selectedTrashIds.push(id);
                        } else {
                            selectedTrashIds = selectedTrashIds.filter(i => i !== id);
                        }
                        const hasSelected = selectedTrashIds.length > 0;
                        permanentDeleteBtn.style.display = hasSelected ? 'block' : 'none';
                        restoreBtn.style.display = hasSelected ? 'block' : 'none';
                    });
                });

            } catch (e) {
                list.innerHTML = '<p class="text-danger text-center py-4">Failed to load.</p>';
            }
        }

        function timeAgo(timestamp) {
            const diff = Math.floor((Date.now() - new Date(timestamp)) / 1000);
            if (diff < 60) return 'Just now';
            if (diff < 3600) return Math.floor(diff / 60) + ' min ago';
            if (diff < 86400) return Math.floor(diff / 3600) + ' hour' + (Math.floor(diff / 3600) > 1 ? 's' : '') + ' ago';
            if (diff < 604800) return Math.floor(diff / 86400) + ' day' + (Math.floor(diff / 86400) > 1 ? 's' : '') + ' ago';
            return new Date(timestamp).toLocaleDateString('en-GB');
        }

        // permanently delete selected
        if (permanentDeleteBtn) {
            permanentDeleteBtn.addEventListener('click', async () => {
                if (!selectedTrashIds.length) return;

                for (const id of selectedTrashIds) {
                    await fetch('/api/permanent_delete_conversation.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ conversation_id: id })
                    });
                }
                loadTrash();
            });
        }

        if (restoreBtn) {
            restoreBtn.addEventListener('click', async () => {
                if (!selectedTrashIds.length) return;

                await Promise.all(selectedTrashIds.map(id =>
                    fetch('/api/restore_conversation.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ conversation_id: id })
                    })
                ));

                location.reload();
            });
        }

        // permanently delete all
        if (emptyTrashBtn) {
            emptyTrashBtn.addEventListener('click', async () => {
                const list = document.getElementById('trashList');
                const ids = [...list.querySelectorAll('.trash-check')].map(cb => parseInt(cb.dataset.id));
                if (!ids.length) return;

                for (const id of ids) {
                    await fetch('/api/permanent_delete_conversation.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ conversation_id: id })
                    });
                }
                loadTrash();
            });
        }

        if (trashBtn) trashBtn.addEventListener('click', () => {
            loadTrash();
            trashModalInstance.show();
        });

        if (trashCloseBtn) trashCloseBtn.addEventListener('click', () => trashModalInstance.hide());

        const onlineUsers = new Set();
        const socket = new WebSocket('ws://35.246.100.38:9000');

        socket.onopen = function (e) {
            console.log("WebSocket connection established");
            socket.send(JSON.stringify({
                type: 'login',
                user_id: CURRENT_USER_ID
            }));
        };

        socket.onmessage = function (event) {
            const data = JSON.parse(event.data);

            if (data.type === 'message') {
                const contact = allConversations.find(c => c.id == data.conversation_id);
                if (contact) {
                    contact.messages.push({
                        id: data.message_id,
                        direction: 'incoming',
                        senderName: data.sender_name || 'Someone',
                        text: data.content,
                        timestamp: new Date(data.created_at),
                    });
                    renderSidebar(allConversations);
                    if (contact.id === activeId) {
                        renderMessages(contact);
                    }
                }

                const indicator = document.getElementById('typing-indicator');
                if (indicator) indicator.style.display = 'none';

            } else if (data.type === 'typing') {
                const contact = allConversations.find(c => c.id == data.conversation_id);

                if (contact && contact.id === activeId) {
                    showTypingIndicator(contact ? contact.name : 'Someone');
                }

            } else if (data.type === 'presence') {
                if (data.status === 'online') {
                    onlineUsers.add(parseInt(data.user_id));
                } else {
                    onlineUsers.delete(parseInt(data.user_id));
                }
                document.querySelectorAll('.status-dot[data-user-id]').forEach(dot => {
                    const uid = parseInt(dot.dataset.userId);
                    dot.classList.toggle('online', onlineUsers.has(uid));
                });
                if (activeId) {
                    const conv = allConversations.find(c => c.id === activeId);
                    if (conv && !conv.isGroup) {
                        const headerEl = document.querySelector('.chat-window-user');
                        const isOn = onlineUsers.has(conv.contactId);
                        headerEl.innerHTML = conv.name + ` <span style="font-size:0.75rem;color:${isOn ? '#10b981' : '#9ca3af'};font-weight:600;">${isOn ? 'Online' : 'Offline'}</span>`;
                    }
                }
            }
        };

        socket.onerror = function (error) {
            console.error('WebSocket error:', error);
        };

        socket.onclose = function () {
            console.log('WebSocket connection closed');
        };

        //typing indicator for sending
        const chatInput = document.querySelector('.chat-input-field');

        chatInput.addEventListener('input', () => {
            if (typeof socket !== 'undefined' && socket.readyState === WebSocket.OPEN && activeId) {
                const contact = allConversations.find(c => c.id === activeId);
                if (contact) {
                    socket.send(JSON.stringify({
                        type: 'typing',
                        conversation_id: contact.id,
                        user_id: '<?php echo $_SESSION['name'] ?? "Someone"; ?>',
                    }));
                }
            }
        });

        //typing indicator for receiving
        let typingTimeout;

        function showTypingIndicator(name) {
            let indicator = document.querySelector('#typing-indicator');

            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'typing-indicator';
                indicator.className = 'message-row received-row';
                indicator.style.padding = '10px 0';

                const messagesArea = document.querySelector('.chat-messages');
                messagesArea.appendChild(indicator);
            }

            indicator.innerHTML = `
        <div class="message-avatar" style="visibility: hidden;"></div>
        <div class="message-bubble received" style="font-style: italic; color: #888;">
            <p>${name} is typing...</p>
        </div>
    `;
            indicator.style.display = 'block';

            const messagesArea = document.querySelector('.chat-messages');
            messagesArea.scrollTop = messagesArea.scrollHeight;

            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                indicator.style.display = 'none';
            }, 3000);
        }

        // sidebar collapse
        const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
        const chatSidebar = document.getElementById('chatSidebar');

        sidebarCollapseBtn.addEventListener('click', () => {
            chatSidebar.classList.toggle('collapsed');
        });

        // new chat modal 

        let ncAllUsers = [];
        let ncSelected = [];
        let ncFilter = 'all';
        let ncSearch = '';

        function ncLoadUsers() {
            ncAllUsers = ALL_USERS;
            ncRenderList();
        }

        function ncRenderList() {
            const list = document.getElementById('ncUserList');
            const roleMap = { project_manager: 'Manager', team_leader: 'Team Leader', employee: 'Employee' };

            let filtered = ncAllUsers.filter(u => {
                const matchSearch = u.name.toLowerCase().includes(ncSearch.toLowerCase());
                const matchFilter = ncFilter === 'all' || u.role === ncFilter;
                return matchSearch && matchFilter;
            });

            if (!filtered.length) {
                list.innerHTML = '<p style="color:#9ca3af;font-size:0.9rem;padding:16px 0;">No users found</p>';
                return;
            }

            // Group alphabetically
            const groups = {};
            filtered.forEach(u => {
                const letter = u.name[0].toUpperCase();
                if (!groups[letter]) groups[letter] = [];
                groups[letter].push(u);
            });

            list.innerHTML = Object.keys(groups).sort().map(letter => `
        <div class="nc-alpha-header">${letter}</div>
        ${groups[letter].map(u => {
                const isSelected = ncSelected.some(s => String(s.id) === String(u.id));
                const avatarColour = nameToColour(u.name);
                return `
            <div class="nc-user-row${isSelected ? ' selected' : ''}" data-id="${u.id}">
                <div class="chat-avatar" style="background:${avatarColour}; width:36px; height:36px; font-size:0.85rem;">
                    ${u.name[0].toUpperCase()}
                </div>
                <span class="nc-user-name">${u.name}</span>
                <span class="nc-user-role">${roleMap[u.role] ?? u.role}</span>
                <div class="nc-checkbox"></div>
            </div>`;
            }).join('')}
    `).join('');

            list.querySelectorAll('.nc-user-row').forEach(row => {
                row.addEventListener('click', () => ncToggleUser(parseInt(row.dataset.id)));
            });
        }

        function ncToggleUser(id) {
            const strId = String(id);
            const user = ncAllUsers.find(u => String(u.id) === strId);
            if (!user) return;

            const idx = ncSelected.findIndex(s => String(s.id) === strId);
            if (idx === -1) {
                ncSelected.push(user);
            } else {
                ncSelected.splice(idx, 1);
            }

            ncUpdateSelected();
            ncRenderList();
        }

        function ncUpdateSelected() {
            const wrap = document.getElementById('ncSelectedWrap');
            const chips = document.getElementById('ncSelectedChips');
            const groupWrap = document.getElementById('ncGroupNameWrap');
            const startBtn = document.getElementById('ncStartBtn');

            wrap.style.display = ncSelected.length ? 'block' : 'none';
            groupWrap.style.display = ncSelected.length >= 2 ? 'block' : 'none';

            const avatarColours = ncSelected.map(u => nameToColour(u.name));

            chips.innerHTML = ncSelected.map((u, i) => `
        <div class="nc-chip">
            <div class="chat-avatar" style="background:${avatarColours[i]}; width:24px; height:24px; font-size:0.7rem;">
                ${u.name[0].toUpperCase()}
            </div>
            ${u.name}
            <button onclick="ncToggleUser('${u.id}')">×</button>
        </div>
    `).join('');

            // update start button
            if (ncSelected.length === 0) {
                startBtn.disabled = true;
                startBtn.style.opacity = '0.5';
                startBtn.textContent = 'Start Chat';
            } else {
                startBtn.disabled = false;
                startBtn.style.opacity = '1';
                startBtn.textContent = ncSelected.length === 1 ? 'Start Chat' : 'Create Group';
            }
        }

        // search
        document.getElementById('newChatSearch').addEventListener('input', e => {
            ncSearch = e.target.value;
            ncRenderList();
        });

        // filters
        document.querySelectorAll('.nc-filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.nc-filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                ncFilter = btn.dataset.filter;
                ncRenderList();
            });
        });

        // load users when modal opens
        document.getElementById('modalNewChat').addEventListener('show.bs.modal', () => {
            ncSelected = [];
            ncSearch = '';
            ncFilter = 'all';
            document.getElementById('newChatSearch').value = '';
            document.getElementById('ncGroupName').value = '';
            document.querySelectorAll('.nc-filter-btn').forEach(b => b.classList.remove('active'));
            document.querySelector('.nc-filter-btn[data-filter="all"]').classList.add('active');
            ncUpdateSelected();
            ncLoadUsers();
        });

        // start chat
        document.getElementById('ncStartBtn').addEventListener('click', async () => {
            if (!ncSelected.length) return;

            const isGroup = ncSelected.length >= 2;
            const groupName = document.getElementById('ncGroupName').value.trim();

            if (isGroup && !groupName) {
                document.getElementById('ncGroupName').style.borderColor = '#ef4444';
                document.getElementById('ncGroupName').focus();
                return;
            }

            const payload = {
                members: ncSelected.map(u => u.id).join(','),
                made_by: <?php echo $currentUserId; ?>,
                groupchat_name: isGroup ? groupName : '',
            }

            fetch('/api/make_chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(payload)
            }).then(res => res.json())
                .then(result => {
                    if (result.success) {
                        // TODO: ideally we would just fetch the new conversation so no reload?
                        // maybe the api call returns the obj so we can chuck it in ALL_DATA with no worries
                        // then just renderSidebar or w/e
                        location.reload();
                    } else {
                        alert('Failed to create chat: ' + result.error);
                    }
                })
                .catch(error => {
                    console.error('Error creating chat:', error);
                    alert('An error occurred while creating the chat. Please try again.');
                });
        });

        document.querySelectorAll('#prefTimeFormat .mia-segment-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                timeFormat = btn.dataset.value;
                const conversation = allConversations.find(c => c.id === activeId);
                if (conversation) renderMessages(conversation);
                renderSidebar(allConversations);
            });
        });

        //Pagination for messages
        const messagesContainer = document.querySelector('.chat-messages');
        let isLoading = false;

        function prependMessages(messages) {
            const area = document.querySelector('.chat-messages');
            const prevScrollHeight = area.scrollHeight;
            const prevScrollTop = area.scrollTop;
            // Messages come in newest-first from the API, reverse so we process oldest-first
            const ordered = [...messages].reverse();
            // Remove the current top date divider — we'll re-evaluate it after prepending
            const existingTopDivider = area.querySelector('.chat-date-divider');
            const existingTopDate = existingTopDivider?.textContent ?? null;
            existingTopDivider?.remove();
            // Build fragment oldest-first, then prepend the whole thing
            const fragment = document.createDocumentFragment();
            let lastDateStr = null;
            for (const msg of ordered) {
                const dateStr = formatDate(new Date(msg.timestamp));
                if (dateStr !== lastDateStr) {
                    const divider = document.createElement('div');
                    divider.className = 'chat-date-divider';
                    divider.textContent = dateStr;
                    fragment.appendChild(divider);
                    lastDateStr = dateStr;
                }
                const row = document.createElement('div');
                row.className = `message-row ${msg.direction === 'outgoing' ? 'sent-row' : 'received-row'}`;
                if (msg.direction === 'incoming') {
                    const senderLetter = msg.senderName?.charAt(0)
                        ?? allConversations.find(c => c.id === activeId)?.avatarLetter
                        ?? '?';
                    row.innerHTML = `
                        <div class="message-avatar">${senderLetter}</div>
                        <div class="message-bubble received">
                            <p>${msg.text}</p>
                            <span class="message-time">${formatTime(new Date(msg.timestamp))}</span>
                        </div>`;
                } else {
                    row.innerHTML = `
                        <div class="message-bubble sent">
                            <p>${msg.text}</p>
                            <span class="message-time">${formatTime(new Date(msg.timestamp))}</span>
                        </div>`;
                }
                fragment.appendChild(row);
            }
            // Re-add the old top divider only if its date differs from the last batch date
            // (if same date, the batch's divider already covers it)
            if (existingTopDate && existingTopDate !== lastDateStr) {
                const divider = document.createElement('div');
                divider.className = 'chat-date-divider';
                divider.textContent = existingTopDate;
                fragment.appendChild(divider);
            }
            area.prepend(fragment);
            area.scrollTop = area.scrollHeight - prevScrollHeight + prevScrollTop;
        }

        messagesContainer.addEventListener('scroll', async () => {
            if (messagesContainer.scrollTop === 0 && !isLoading && oldestMessageId) {
                isLoading = true;
                const conversation = allConversations.find(c => c.id === activeId);

                if (!conversation){
                    isLoading = false;
                    return;
                }

                try {
                    const response = await fetch(`/api/messages.php?conversation_id=${conversation.id}&before=${oldestMessageId}`);
                    const result = await response.json();

                    if (result.success) {
                        const newMessages = result.messages.map(m => ({
                            id: m.id,
                            direction: m.sender_id == <?php echo $currentUserId; ?> ? 'outgoing' : 'incoming',
                            by: m.sender_id,
                            senderName: m.sender_name,
                            text: m.content,
                            timestamp: new Date(m.created_at),
                        }));

                        if (newMessages.length) {
                            oldestMessageId = newMessages[newMessages.length - 1].id;
                            prependMessages(newMessages);
                        } else {
                            oldestMessageId = null;
                        }
                    } else {
                        console.error('Failed to load messages:', result.error);
                    }
                } catch (error) {
                    console.error('Error loading messages:', error);
                } finally {
                    isLoading = false;
                }
            }
        });

    </script>
</body>

</html>
