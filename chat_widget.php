<!-- chat_widget.php - Floating Live Chat Widget & Smart Quick Reply Bot -->
<div id="purrfect-chat-root">
    <!-- 1. Floating Launcher Button (Fixed Bottom-Right) -->
    <button id="chat-launcher-btn" type="button" onclick="togglePurrfectChat()" title="ปรึกษาแอดมิน & แชทบอทแนะนำน้องแมว 🐾">
        <span class="launcher-icon">💬</span>
        <span class="launcher-cat-icon">🐾</span>
        <span class="launcher-status-dot"></span>
        <span id="chat-unread-badge" class="chat-unread-badge" style="display: none;">0</span>
    </button>

    <!-- 2. Chat Popup Window -->
    <div id="purrfect-chat-window" class="chat-window-hidden">
        <!-- Header -->
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="chat-avatar-wrapper">
                    <img src="assets/images/logo.png" alt="Purrfect Advisor" class="chat-header-avatar">
                    <span class="chat-online-badge"></span>
                </div>
                <div>
                    <div class="chat-header-title">Purrfect Live Chat 🐾</div>
                    <div class="chat-header-subtitle">
                        <span class="status-pulse-dot"></span> เจ้าหน้าที่ & AI แนะนำสายพันธุ์ (ออนไลน์)
                    </div>
                </div>
            </div>
            <div class="chat-header-actions">
                <button type="button" class="chat-btn-icon" onclick="togglePurrfectChat()" title="ย่อหน้าต่าง">✕</button>
            </div>
        </div>

        <!-- Current User Info Strip -->
        <div id="chat-user-strip" class="chat-user-strip">
            <span id="chat-user-label">กำลังโหลดข้อมูลผู้ใช้...</span>
        </div>

        <!-- Quick Reply Prompts Section -->
        <div class="chat-quick-section">
            <div class="chat-quick-title">⚡ คำถามด่วนยอดนิยม (คลิกเพื่อให้บอทแนะนำทันที):</div>
            <div class="chat-quick-pills">
                <button type="button" class="quick-pill" onclick="sendQuickReply(1)">
                    🐱 1. น้องแมวยอดนิยม เลี้ยงง่าย
                </button>
                <button type="button" class="quick-pill" onclick="sendQuickReply(2)">
                    🏢 2. เหมาะกับเลี้ยงในคอนโด
                </button>
                <button type="button" class="quick-pill" onclick="sendQuickReply(3)">
                    🤧 3. คนเป็นภูมิแพ้ ขนไม่ร่วง
                </button>
                <button type="button" class="quick-pill" onclick="sendQuickReply(4)">
                    👑 4. พันธุ์พรีเมียม ขนฟู ใบเพ็ด
                </button>
                <button type="button" class="quick-pill" onclick="sendQuickReply(5)">
                    🎁 5. ดีลส่วนลด & โปรโมชั่น
                </button>
                <button type="button" class="quick-pill" onclick="sendQuickReply(6)">
                    🩺 6. การรับประกันสุขภาพ 180 วัน
                </button>
            </div>
        </div>

        <!-- Chat Messages Body -->
        <div id="chat-messages-container" class="chat-messages-container">
            <div class="chat-loading-spinner" id="chat-loading">
                <span>🐾 กำลังโหลดการสนทนา...</span>
            </div>
        </div>

        <!-- Chat Input Bar -->
        <form id="chat-input-form" onsubmit="handleSendChatMessage(event)" class="chat-input-form">
            <input type="text" id="chat-input-text" placeholder="พิมพ์ข้อความคุยกับแอดมิน..." autocomplete="off" maxlength="500">
            <button type="submit" id="chat-send-btn" class="chat-send-btn" title="ส่งข้อความ">
                <span>➤</span>
            </button>
        </form>
    </div>
</div>

<script>
(function() {
    let chatIsOpen = false;
    let lastMessageCount = 0;
    let pollInterval = null;
    let currentUserId = '';

    window.togglePurrfectChat = function() {
        const win = document.getElementById('purrfect-chat-window');
        const launcher = document.getElementById('chat-launcher-btn');
        chatIsOpen = !chatIsOpen;

        if (chatIsOpen) {
            win.classList.remove('chat-window-hidden');
            win.classList.add('chat-window-visible');
            launcher.classList.add('launcher-active');
            document.getElementById('chat-unread-badge').style.display = 'none';
            document.getElementById('chat-unread-badge').innerText = '0';
            loadChatMessages(true);
            setTimeout(() => {
                document.getElementById('chat-input-text').focus();
            }, 300);
        } else {
            win.classList.remove('chat-window-visible');
            win.classList.add('chat-window-hidden');
            launcher.classList.remove('launcher-active');
        }
    };

    window.loadChatMessages = function(forceScroll = false) {
        fetch('api_chat.php?action=get_messages')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.conversation) {
                    const conv = data.conversation;
                    const u = data.current_user;
                    currentUserId = u.id;

                    // Update user strip
                    const strip = document.getElementById('chat-user-label');
                    if (u.is_member) {
                        strip.innerHTML = `👤 สนทนาในนามสมาชิก: <strong>${escapeHtml(u.name)}</strong> (สิทธิ์ส่วนลด 5% 🐾)`;
                    } else {
                        strip.innerHTML = `👤 สนทนาในนาม: <strong>${escapeHtml(u.name)}</strong> • <a href="login.php" style="color:var(--primary-coral); text-decoration:underline;">เข้าสู่ระบบ</a> เพื่อสะสมแต้ม`;
                    }

                    renderMessages(conv.messages || [], forceScroll);
                }
            })
            .catch(err => console.log('Chat load error:', err));
    };

    function renderMessages(messages, forceScroll = false) {
        const container = document.getElementById('chat-messages-container');
        if (!container) return;

        const isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 60;
        
        let html = '';
        messages.forEach(msg => {
            const isCustomer = msg.sender === 'customer';
            const senderClass = isCustomer ? 'msg-customer' : (msg.sender === 'admin' ? 'msg-admin' : 'msg-bot');
            const senderBadge = isCustomer ? 'คุณ' : (msg.sender === 'admin' ? '👨‍💼 แอดมิน' : '🤖 ผู้ช่วย AI');

            let cardHtml = '';
            if (msg.cards && msg.cards.length > 0) {
                cardHtml += '<div class="chat-cards-carousel">';
                msg.cards.forEach(card => {
                    cardHtml += `
                        <div class="chat-cat-card">
                            <img src="${card.image}" alt="${escapeHtml(card.name)}" class="chat-cat-img" onerror="this.src='assets/images/logo.png'">
                            <div class="chat-cat-info">
                                <div class="chat-cat-name">${escapeHtml(card.name)}</div>
                                <div class="chat-cat-breed">${escapeHtml(card.breed)}</div>
                                <div class="chat-cat-highlight">${escapeHtml(card.highlight || '')}</div>
                                <div class="chat-cat-price">${escapeHtml(card.price_fmt || card.price)}</div>
                                <a href="${card.link || 'products.php'}" class="btn btn-primary btn-sm chat-card-action">
                                    ดูน้องตัวนี้ 🐾
                                </a>
                            </div>
                        </div>
                    `;
                });
                cardHtml += '</div>';
            }

            const formattedTime = msg.timestamp ? msg.timestamp.split(' ')[1] || msg.timestamp : '';

            html += `
                <div class="chat-message-row ${senderClass}">
                    ${!isCustomer ? `<div class="chat-bubble-avatar"><img src="assets/images/logo.png" alt="Avatar"></div>` : ''}
                    <div class="chat-bubble-content">
                        <div class="chat-sender-header">
                            <span class="chat-sender-name">${escapeHtml(msg.sender_name || senderBadge)}</span>
                            <span class="chat-time">${formattedTime}</span>
                        </div>
                        <div class="chat-text">${escapeHtml(msg.text).replace(/\n/g, '<br>')}</div>
                        ${cardHtml}
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        if (forceScroll || isAtBottom || messages.length > lastMessageCount) {
            container.scrollTop = container.scrollHeight;
        }
        lastMessageCount = messages.length;
    }

    window.sendQuickReply = function(option) {
        const btn = event.currentTarget;
        btn.classList.add('pill-clicked');
        setTimeout(() => btn.classList.remove('pill-clicked'), 600);

        const formData = new FormData();
        formData.append('action', 'quick_reply');
        formData.append('option', option);

        fetch('api_chat.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                loadChatMessages(true);
            }
        })
        .catch(err => console.log('Quick reply error:', err));
    };

    window.handleSendChatMessage = function(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input-text');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        const formData = new FormData();
        formData.append('action', 'send_message');
        formData.append('message', text);

        fetch('api_chat.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                loadChatMessages(true);
            }
        })
        .catch(err => console.log('Send msg error:', err));
    };

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Auto poll when page loaded
    document.addEventListener('DOMContentLoaded', () => {
        loadChatMessages(false);
        pollInterval = setInterval(() => {
            if (chatIsOpen) {
                loadChatMessages(false);
            }
        }, 3500);
    });
})();
</script>
