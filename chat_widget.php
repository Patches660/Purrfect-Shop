<!-- chat_widget.php - Floating Live Chat Widget with Popup Quick Menu -->
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

        <!-- Chat Messages Body -->
        <div id="chat-messages-container" class="chat-messages-container">
            <div class="chat-loading-spinner" id="chat-loading">
                <span>🐾 กำลังโหลดการสนทนา...</span>
            </div>
        </div>

        <!-- Pop-up Quick Questions Menu Overlay (Toggled by "⚡ คำถาม" button) -->
        <div id="chat-quick-menu-popup" class="chat-quick-popup-hidden">
            <div class="chat-quick-popup-header">
                <span>⚡ เลือกคำถามด่วน (บอทแนะนำทันที)</span>
                <button type="button" class="quick-popup-close-btn" onclick="toggleQuickMenuPopup()">✕</button>
            </div>
            <div class="chat-quick-popup-grid">
                <button type="button" class="quick-popup-item" onclick="sendQuickReplyFromPopup(1)">
                    <span class="popup-item-icon">🐱</span>
                    <span class="popup-item-text">1. น้องแมวยอดนิยม เลี้ยงง่าย สำหรับมือใหม่</span>
                </button>
                <button type="button" class="quick-popup-item" onclick="sendQuickReplyFromPopup(2)">
                    <span class="popup-item-icon">🏢</span>
                    <span class="popup-item-text">2. เหมาะกับเลี้ยงในคอนโด / รักความสงบ</span>
                </button>
                <button type="button" class="quick-popup-item" onclick="sendQuickReplyFromPopup(3)">
                    <span class="popup-item-icon">🤧</span>
                    <span class="popup-item-text">3. คนเป็นภูมิแพ้ ขนไม่ร่วง / ผลัดขน 0%</span>
                </button>
                <button type="button" class="quick-popup-item" onclick="sendQuickReplyFromPopup(4)">
                    <span class="popup-item-icon">👑</span>
                    <span class="popup-item-text">4. สายพันธุ์พรีเมียม ขนฟู มีใบเพ็ดดีกรี</span>
                </button>
                <button type="button" class="quick-popup-item" onclick="sendQuickReplyFromPopup(5)">
                    <span class="popup-item-icon">🎁</span>
                    <span class="popup-item-text">5. โปรโมชั่น & ดีลส่วนลดสมาชิกใหม่</span>
                </button>
                <button type="button" class="quick-popup-item" onclick="sendQuickReplyFromPopup(6)">
                    <span class="popup-item-icon">🩺</span>
                    <span class="popup-item-text">6. การรับประกันสุขภาพ 180 วัน & ขนส่ง</span>
                </button>
            </div>
        </div>

        <!-- Chat Input Bar (With ⚡ คำถาม button beside input) -->
        <form id="chat-input-form" onsubmit="handleSendChatMessage(event)" class="chat-input-form">
            <button type="button" id="chat-quick-trigger-btn" class="chat-quick-trigger-btn" onclick="toggleQuickMenuPopup()" title="คลิกเพื่อเปิดคำถามด่วน 6 ข้อ">
                <span>⚡ คำถาม</span>
            </button>
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
    let quickPopupIsOpen = false;
    let lastMessageCount = 0;
    let pollInterval = null;
    let currentUserId = '';

    let isBotTyping = false;

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
            closeQuickMenuPopup();
        }
    };

    window.toggleQuickMenuPopup = function() {
        const popup = document.getElementById('chat-quick-menu-popup');
        quickPopupIsOpen = !quickPopupIsOpen;
        if (quickPopupIsOpen) {
            popup.classList.remove('chat-quick-popup-hidden');
            popup.classList.add('chat-quick-popup-visible');
        } else {
            popup.classList.remove('chat-quick-popup-visible');
            popup.classList.add('chat-quick-popup-hidden');
        }
    };

    window.closeQuickMenuPopup = function() {
        const popup = document.getElementById('chat-quick-menu-popup');
        quickPopupIsOpen = false;
        popup.classList.remove('chat-quick-popup-visible');
        popup.classList.add('chat-quick-popup-hidden');
    };

    window.sendQuickReplyFromPopup = function(option) {
        closeQuickMenuPopup();
        sendQuickReply(option);
    };

    window.loadChatMessages = function(forceScroll = false) {
        if (isBotTyping && !forceScroll) {
            return; // Don't interrupt typing animation during 3s delay
        }

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
                        strip.innerHTML = `👤 สนทนาในนามสมาชิก: <strong>${escapeHtml(u.name)}</strong> (ส่วนลด 5% 🐾)`;
                    } else {
                        strip.innerHTML = `👤 สนทนาในนาม: <strong>${escapeHtml(u.name)}</strong> • <a href="login.php" style="color:var(--primary-coral); text-decoration:underline;">เข้าสู่ระบบ</a> เพื่อสะสมแต้ม`;
                    }

                    if (!isBotTyping) {
                        renderMessages(conv.messages || [], forceScroll);
                    }
                }
            })
            .catch(err => console.log('Chat load error:', err));
    };

    function getCurrentTimeFormatted() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }

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

            let formattedTime = '';
            if (msg.timestamp) {
                const tParts = msg.timestamp.split(' ');
                if (tParts[1]) {
                    formattedTime = tParts[1].substring(0, 5);
                } else {
                    formattedTime = msg.timestamp;
                }
            }

            html += `
                <div class="chat-message-row ${senderClass}">
                    ${!isCustomer ? `<div class="chat-bubble-avatar"><img src="assets/images/logo.png" alt="Avatar"></div>` : ''}
                    <div class="chat-bubble-content">
                        <div class="chat-sender-header">
                            <span class="chat-sender-name">${escapeHtml(msg.sender_name || senderBadge)}</span>
                            <span class="chat-time">${formattedTime}</span>
                        </div>
                        <div class="chat-text">${escapeHtml(msg.text).replace(/\\n/g, '<br>')}</div>
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

    const quickPromptsMap = {
        1: '🐱 แนะนำน้องแมวยอดนิยม เลี้ยงง่าย สำหรับมือใหม่',
        2: '🏢 มีน้องแมวพันธุ์ไหนที่เหมาะกับเลี้ยงในคอนโด/ห้องพักบ้าง?',
        3: '🤧 เป็นภูมิแพ้ แนะนำน้องแมวขนไม่ร่วง/ผลัดขนน้อยหน่อยครับ',
        4: '👑 ขอดูแมวสายพันธุ์พรีเมียม ขนฟูหน้าหวาน มีใบเพ็ดดีกรี',
        5: '🎁 ตอนนี้มีโปรโมชั่นหรือดีลส่วนลดสมาชิกใหม่อะไรบ้าง?',
        6: '🩺 การรับประกันสุขภาพและบริการหลังการขายมีอะไรบ้าง?'
    };

    window.sendQuickReply = function(option) {
        const container = document.getElementById('chat-messages-container');
        const userText = quickPromptsMap[option] || 'คำถามด่วน';
        const currentTime = getCurrentTimeFormatted();

        // 1. Instantly display customer message in UI
        const customerRow = document.createElement('div');
        customerRow.className = 'chat-message-row msg-customer';
        customerRow.innerHTML = `
            <div class="chat-bubble-content">
                <div class="chat-sender-header">
                    <span class="chat-sender-name">คุณ</span>
                    <span class="chat-time">${currentTime}</span>
                </div>
                <div class="chat-text">${escapeHtml(userText)}</div>
            </div>
        `;
        container.appendChild(customerRow);

        // 2. Instantly display Typing Indicator
        const typingRow = document.createElement('div');
        typingRow.id = 'chat-typing-indicator';
        typingRow.className = 'chat-message-row msg-bot';
        typingRow.innerHTML = `
            <div class="chat-bubble-avatar"><img src="assets/images/logo.png" alt="Avatar"></div>
            <div class="chat-bubble-content">
                <div class="chat-sender-header">
                    <span class="chat-sender-name">🤖 ผู้ช่วย AI</span>
                    <span class="chat-time">${currentTime}</span>
                </div>
                <div class="chat-typing-bubble">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span style="font-size:0.75rem; color:#888; margin-left:6px; font-weight:600;">🐾 Purrfect Advisor กำลังตอบ... 💬</span>
                </div>
            </div>
        `;
        container.appendChild(typingRow);
        container.scrollTop = container.scrollHeight;

        isBotTyping = true;

        // 3. Send request to backend
        const formData = new FormData();
        formData.append('action', 'quick_reply');
        formData.append('option', option);

        fetch('api_chat.php', {
            method: 'POST',
            body: formData
        })
        .catch(err => console.log('Quick reply error:', err));

        // 4. Wait exactly 3 seconds (3000ms) before showing bot's response
        setTimeout(() => {
            isBotTyping = false;
            loadChatMessages(true);
        }, 3000);
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
