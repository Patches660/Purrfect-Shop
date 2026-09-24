<!-- chat_widget.php - Floating Live Chat Widget with Popup Quick Menu -->
<div id="purrfect-chat-root">
    <!-- 1. Floating Launcher Button (Fixed Bottom-Right) -->
    <button id="chat-launcher-btn" type="button" onclick="togglePurrfectChat()" title="ปรึกษาแอดมิน & แชทบอทแนะนำน้องแมว 🐾">
        <span class="launcher-icon">💬</span>
        <span class="launcher-cat-icon">🐾</span>
        <span class="launcher-close-icon">✕</span>
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
    let pollInterval = null;
    let isBotTyping = false;
    let lastRenderedSig = '';

    const quickPromptsMap = {
        1: '🐱 แนะนำน้องแมวยอดนิยม เลี้ยงง่าย สำหรับมือใหม่',
        2: '🏢 มีน้องแมวพันธุ์ไหนที่เหมาะกับเลี้ยงในคอนโด/ห้องพักบ้าง?',
        3: '🤧 เป็นภูมิแพ้ แนะนำน้องแมวขนไม่ร่วง/ผลัดขนน้อยหน่อยครับ',
        4: '👑 ขอดูแมวสายพันธุ์พรีเมียม ขนฟูหน้าหวาน มีใบเพ็ดดีกรี',
        5: '🎁 ตอนนี้มีโปรโมชั่นหรือดีลส่วนลดสมาชิกใหม่อะไรบ้าง?',
        6: '🩺 การรับประกันสุขภาพและบริการหลังการขายมีอะไรบ้าง?'
    };

    // Client-Side Fallback Knowledge Base & Quick Replies for Static GitHub Pages
    const BOT_RESPONSES = {
        1: {
            text: "นี่คือน้องแมวยอดนิยมอันดับ 1 เลี้ยงง่าย เป็นมิตร สุขภาพแข็งแรง เหมาะสำหรับมือใหม่ที่เพิ่งเริ่มเลี้ยงที่สุดครับ 🐾",
            cards: [
                { id: 'cat_british', name: 'น้องบริติช บลู', breed: 'British Shorthair', price: '฿35,000', image: 'assets/images/cat_british.jpg', highlight: 'นิสัยสุขุม น่ารัก ขี้อ้อน', link: 'products.html' },
                { id: 'cat_americanshorthair', name: 'น้องอเมริกัน ซิลเวอร์', breed: 'American Shorthair', price: '฿28,000', image: 'assets/images/cat_americanshorthair.jpg', highlight: 'ลายคมชัด ขี้เล่น เลี้ยงง่าย', link: 'products.html' },
                { id: 'cat_scottish', name: 'น้องสกอตติช โฟลด์', breed: 'Scottish Fold', price: '฿32,000', image: 'assets/images/cat_scottish.jpg', highlight: 'หูพับหน้ากลม น่ารักติดคน', link: 'products.html' }
            ]
        },
        2: {
            text: "สำหรับเพื่อนๆ ที่พักอาศัยในคอนโดหรือห้องพัก แนะนำสายพันธุ์ที่รักความสงบ ไม่ส่งเสียงดังรบกวนข้างห้อง และปรับตัวเก่งครับ 🏢✨",
            cards: [
                { id: 'cat_persian', name: 'น้องเปอร์เซีย ขาวสำลี', breed: 'Persian', price: '฿29,000', image: 'assets/images/cat_persian.jpg', highlight: 'เงียบสงบ สุภาพ เรียบร้อย', link: 'recommend.html' },
                { id: 'cat_russian', name: 'น้องรัสเซียน บลู', breed: 'Russian Blue', price: '฿38,000', image: 'assets/images/cat_russian.jpg', highlight: 'ฉลาด รักความเงียบสงบ', link: 'recommend.html' },
                { id: 'cat_ragdoll', name: 'น้องแร็กดอลล์ ตาฟ้า', breed: 'Ragdoll', price: '฿45,000', image: 'assets/images/cat_ragdoll.jpg', highlight: 'ตัวใหญ่นุ่มนิ่ม อ่อนโยน', link: 'recommend.html' }
            ]
        },
        3: {
            text: "ผู้ที่เป็นโรคภูมิแพ้ขนสัตว์ หรือไม่ชอบกวาดขนแมว แนะนำกลุ่มสายพันธุ์ Hypoallergenic / ขนสั้นพิเศษ หรือไร้ขน 100% เหล่านี้ครับ 🤧🌿",
            cards: [
                { id: 'cat_sphynx', name: 'น้องสฟิงซ์ ชมพู', breed: 'Sphynx', price: '฿42,000', image: 'assets/images/cat_sphynx.jpg', highlight: 'ขนร่วง 0% ไร้ภูมิแพ้ อบอุ่น', link: 'recommend.html' },
                { id: 'cat_devon_rex', name: 'น้องเดวอน เร็กซ์', breed: 'Devon Rex', price: '฿39,000', image: 'assets/images/cat_devon_rex.jpg', highlight: 'ขนหยักนุ่ม ผลัดขนน้อยมาก', link: 'recommend.html' },
                { id: 'cat_cornish_rex', name: 'น้องคอร์นิช เร็กซ์', breed: 'Cornish Rex', price: '฿36,000', image: 'assets/images/cat_cornish_rex.jpg', highlight: 'ขนสั้นลอนสวย ไฮโปอัลเลอร์เจนิก', link: 'recommend.html' }
            ]
        },
        4: {
            text: "ขอนำเสนอน้องแมวเกรดพรีเมียม ขนฟูอลังการ หน้าหวาน และมีใบเพ็ดดีกรี CFA/WCF สายเลือดแชมป์สากล 100% ครับ 👑💎",
            cards: [
                { id: 'cat_mainecoon', name: 'น้องเมนคูน ไจแอนท์', breed: 'Maine Coon', price: '฿55,000', image: 'assets/images/cat_mainecoon.jpg', highlight: 'ยักษ์ใหญ่ใจดี มีใบเพ็ด CFA', link: 'products.html' },
                { id: 'cat_ragdoll', name: 'น้องแร็กดอลล์ บลูพอยท์', breed: 'Ragdoll', price: '฿45,000', image: 'assets/images/cat_ragdoll.jpg', highlight: 'ขนฟูสวย หรูหราระดับท็อป', link: 'products.html' },
                { id: 'cat_siberian', name: 'น้องไซบีเรียน ฟอเรสต์', breed: 'Siberian', price: '฿48,000', image: 'assets/images/cat_siberian.jpg', highlight: 'ขนหนานุ่ม 3 ชั้น สายพันธุ์แท้', link: 'products.html' }
            ]
        },
        5: {
            text: "🎉 สิทธิพิเศษและโค้ดส่วนลดสุดฮอตประจำเดือนนี้:\n\n" +
                  "• 🏷️ โค้ด CAT10OFF: รับส่วนลด 10% เมื่อช้อปขั้นต่ำ 300.- (ใช้ได้ 1 ครั้ง/1 เดือน)\n" +
                  "• 🏷️ โค้ด NEWMEMBER5: รับส่วนลด 5% ทุกรายการสำหรับสมาชิกใหม่\n" +
                  "• 🏷️ โค้ด CATNEWS10: ส่วนลด 10% เมื่อสมัครรับจดหมายข่าว\n" +
                  "• 🎁 ฟรี! Starter Kit เซ็ตของขวัญต้อนรับน้องแมว 11 รายการ มูลค่า 4,500 บาท ฟรีทุกออเดอร์!",
            cards: [
                { id: 'cat_british', name: 'น้องบริติช บลู', breed: 'British Shorthair', price: '฿35,000', image: 'assets/images/cat_british.jpg', highlight: 'ใช้โค้ด CAT10OFF ลด 10%', link: 'welcome_deal.html' },
                { id: 'cat_munchkin', name: 'น้องมันช์กิ้น ขาสั้น', breed: 'Munchkin', price: '฿38,000', image: 'assets/images/cat_munchkin.jpg', highlight: 'แถมฟรี Starter Kit 11 ชิ้น', link: 'welcome_deal.html' }
            ]
        },
        6: {
            text: "🩺 การันตีมาตรฐานสุขภาพระดับสากลจาก Purrfect Cattery:\n\n" +
                  "1. 💉 วัคซีนครบ 2 เข็ม + ถ่ายพยาธิ + หยดยาป้องกันเห็บหมัด\n" +
                  "2. 🔬 ตรวจผลแล็บ ปลอดโรคลิวคีเมีย (FeLV) และเอดส์แมว (FIV) 100%\n" +
                  "3. 🛡️ การันตีคุ้มครองสุขภาพโรคร้ายแรงนานถึง 180 วันเต็ม\n" +
                  "4. 🚗 บริการจัดส่งด้วยรถ Pet Taxi ควบคุมอุณหภูมิถึงหน้าบ้านทั่วประเทศ\n" +
                  "5. 👨‍⚕️ ปรึกษาสัตวแพทย์ประจำฟาร์มฟรีตลอดอายุขัยของน้องแมวครับ!",
            cards: []
        }
    };

    function getLocalChatMessages() {
        try {
            const raw = localStorage.getItem('purrfect_chat_history');
            if (raw) return JSON.parse(raw);
        } catch(e) {}
        return [
            {
                id: 'msg_init_static',
                sender: 'bot',
                sender_name: 'Purrfect Bot 🐾',
                text: 'สวัสดีครับยินดีต้อนรับสู่ Purrfect Shop! 🐾 มีอะไรให้บอทหรือแอดมินดูแล สามารถเลือกคำถามด่วนด้านล่าง หรือพิมพ์ข้อความพูดคุยได้เลยครับ',
                cards: [],
                timestamp: getCurrentTimeFormatted()
            }
        ];
    }

    function saveLocalChatMessages(msgs) {
        try {
            localStorage.setItem('purrfect_chat_history', JSON.stringify(msgs));
        } catch(e) {}
    }

    window.togglePurrfectChat = function() {
        const win = document.getElementById('purrfect-chat-window');
        const launcher = document.getElementById('chat-launcher-btn');
        if (!win || !launcher) return;

        launcher.classList.remove('anim-pulse');
        void launcher.offsetWidth;
        launcher.classList.add('anim-pulse');

        chatIsOpen = !chatIsOpen;

        if (chatIsOpen) {
            win.classList.remove('chat-window-hidden', 'chat-window-closing');
            win.classList.add('chat-window-visible');
            launcher.classList.add('launcher-active');
            const unread = document.getElementById('chat-unread-badge');
            if (unread) {
                unread.style.display = 'none';
                unread.innerText = '0';
            }
            loadChatMessages(true);
            setTimeout(() => {
                const input = document.getElementById('chat-input-text');
                if (input) input.focus();
            }, 300);
        } else {
            launcher.classList.remove('launcher-active');
            closeQuickMenuPopup();
            win.classList.remove('chat-window-visible');
            win.classList.add('chat-window-closing');
            setTimeout(() => {
                if (!chatIsOpen) {
                    win.classList.remove('chat-window-closing');
                    win.classList.add('chat-window-hidden');
                }
            }, 260);
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
        if (isBotTyping && !forceScroll) return;

        const isPhpServer = window.location.protocol.startsWith('http') && !window.location.hostname.includes('github.io');

        if (isPhpServer) {
            fetch('api_chat.php?action=get_messages')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success' && data.conversation) {
                        const conv = data.conversation;
                        const u = data.current_user;
                        updateUserStrip(u);
                        if (!isBotTyping) {
                            renderMessages(conv.messages || [], forceScroll);
                        }
                    }
                })
                .catch(() => fallbackClientLoad(forceScroll));
        } else {
            fallbackClientLoad(forceScroll);
        }
    };

    function fallbackClientLoad(forceScroll) {
        const user = typeof getClientLoggedUser === 'function' ? getClientLoggedUser() : null;
        updateUserStrip(user ? { name: user.username, is_member: true } : { name: 'ผู้เยี่ยมชม', is_member: false });
        const msgs = getLocalChatMessages();
        renderMessages(msgs, forceScroll);
    }

    function updateUserStrip(u) {
        const strip = document.getElementById('chat-user-label');
        if (!strip) return;
        if (u && u.is_member) {
            strip.innerHTML = `👤 สนทนาในนามสมาชิก: <strong>${escapeHtml(u.name)}</strong> (ส่วนลด 5% 🐾)`;
        } else {
            strip.innerHTML = `👤 สนทนาในนาม: <strong>${escapeHtml(u ? u.name : 'ผู้เยี่ยมชม')}</strong> • <a href="login.html" style="color:var(--primary-coral); text-decoration:underline;">เข้าสู่ระบบ</a> เพื่อสะสมแต้ม`;
        }
    }

    function getCurrentTimeFormatted() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    function renderMessages(messages, forceScroll = false) {
        const container = document.getElementById('chat-messages-container');
        if (!container) return;

        const currentSig = messages.length + '_' + (messages.length > 0 ? (messages[messages.length - 1].id || messages[messages.length - 1].text) : '');
        if (currentSig === lastRenderedSig && !forceScroll) {
            return;
        }
        lastRenderedSig = currentSig;

        let html = '';
        messages.forEach(msg => {
            const isCustomer = msg.sender === 'customer';
            const senderClass = isCustomer ? 'msg-customer' : (msg.sender === 'admin' ? 'msg-admin' : 'msg-bot');
            const senderBadge = isCustomer ? 'คุณ' : (msg.sender === 'admin' ? '👨‍💼 แอดมิน' : '🤖 ผู้ช่วย AI');

            let cardHtml = '';
            if (msg.cards && msg.cards.length > 0) {
                cardHtml += `
                    <div class="chat-carousel-wrapper">
                        <button type="button" class="carousel-nav-btn nav-prev" onclick="scrollChatCarousel(this, -180)" title="เลื่อนดูน้องแมวก่อนหน้า">◀</button>
                        <div class="chat-cards-carousel">
                `;
                msg.cards.forEach(card => {
                    cardHtml += `
                        <div class="chat-cat-card">
                            <img src="${card.image}" alt="${escapeHtml(card.name)}" class="chat-cat-img" onerror="this.src='assets/images/logo.png'">
                            <div class="chat-cat-info">
                                <div class="chat-cat-name">${escapeHtml(card.name)}</div>
                                <div class="chat-cat-breed">${escapeHtml(card.breed)}</div>
                                <div class="chat-cat-highlight">${escapeHtml(card.highlight || '')}</div>
                                <div class="chat-cat-price">${escapeHtml(card.price_fmt || card.price)}</div>
                                <a href="${card.link || 'products.html'}" class="btn btn-primary btn-sm chat-card-action">
                                    ดูน้องตัวนี้ 🐾
                                </a>
                            </div>
                        </div>
                    `;
                });
                cardHtml += `
                        </div>
                        <button type="button" class="carousel-nav-btn nav-next" onclick="scrollChatCarousel(this, 180)" title="เลื่อนดูน้องแมวถัดไป">▶</button>
                    </div>
                `;
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
        container.scrollTop = container.scrollHeight;
    }

    window.scrollChatCarousel = function(btn, delta) {
        if (!btn) return;
        const wrapper = btn.closest('.chat-carousel-wrapper');
        if (wrapper) {
            const carousel = wrapper.querySelector('.chat-cards-carousel');
            if (carousel) {
                carousel.scrollBy({ left: delta, behavior: 'smooth' });
            }
        }
    };

    function showTypingIndicator(userText) {
        const container = document.getElementById('chat-messages-container');
        if (!container) return;

        const currentTime = getCurrentTimeFormatted();

        if (userText) {
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
        }

        const oldTyping = document.getElementById('chat-typing-indicator');
        if (oldTyping) oldTyping.remove();

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
                    <span style="font-size:0.75rem; color:#888; margin-left:6px; font-weight:600;">กำลังพิมพ์ข้อความ... 💬</span>
                </div>
            </div>
        `;
        container.appendChild(typingRow);
        container.scrollTop = container.scrollHeight;
        isBotTyping = true;
    }

    window.sendQuickReply = function(option) {
        closeQuickMenuPopup();
        const userText = quickPromptsMap[option] || 'คำถามด่วน';
        showTypingIndicator(userText);

        const isPhpServer = window.location.protocol.startsWith('http') && !window.location.hostname.includes('github.io');

        if (isPhpServer) {
            const formData = new FormData();
            formData.append('action', 'quick_reply');
            formData.append('option', option);

            fetch('api_chat.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(() => {
                    setTimeout(() => {
                        isBotTyping = false;
                        const typing = document.getElementById('chat-typing-indicator');
                        if (typing) typing.remove();
                        loadChatMessages(true);
                    }, 1200);
                })
                .catch(() => triggerClientQuickReply(option, userText));
        } else {
            triggerClientQuickReply(option, userText);
        }
    };

    function triggerClientQuickReply(option, userText) {
        setTimeout(() => {
            isBotTyping = false;
            const typing = document.getElementById('chat-typing-indicator');
            if (typing) typing.remove();

            const botData = BOT_RESPONSES[option] || {
                text: "สวัสดีครับ! สนใจสอบถามข้อมูลน้องแมวตัวไหนเป็นพิเศษ ทักแชทบอกแอดมินได้เลยครับ 🐾",
                cards: []
            };

            const msgs = getLocalChatMessages();
            msgs.push({
                id: 'msg_c_' + Date.now(),
                sender: 'customer',
                sender_name: 'คุณ',
                text: userText,
                cards: [],
                timestamp: getCurrentTimeFormatted()
            });
            msgs.push({
                id: 'msg_b_' + Date.now(),
                sender: 'bot',
                sender_name: 'Purrfect Advisor 🐾',
                text: botData.text,
                cards: botData.cards || [],
                timestamp: getCurrentTimeFormatted()
            });

            saveLocalChatMessages(msgs);
            renderMessages(msgs, true);
        }, 1200);
    }

    window.handleSendChatMessage = function(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input-text');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        showTypingIndicator(text);

        const isPhpServer = window.location.protocol.startsWith('http') && !window.location.hostname.includes('github.io');

        if (isPhpServer) {
            const formData = new FormData();
            formData.append('action', 'send_message');
            formData.append('message', text);

            fetch('api_chat.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(() => {
                    setTimeout(() => {
                        isBotTyping = false;
                        const typing = document.getElementById('chat-typing-indicator');
                        if (typing) typing.remove();
                        loadChatMessages(true);
                    }, 1200);
                })
                .catch(() => triggerClientChatMessage(text));
        } else {
            triggerClientChatMessage(text);
        }
    };

    function triggerClientChatMessage(text) {
        setTimeout(() => {
            isBotTyping = false;
            const typing = document.getElementById('chat-typing-indicator');
            if (typing) typing.remove();

            let replyText = "ขอบคุณสำหรับข้อความครับ! 🐾 เจ้าหน้าที่หรือผู้ช่วย AI กำลังตรวจสอบข้อมูลน้องแมวและจะแนะนำให้คุณลูกค้าทันทีครับ หากต้องการดูคำถามด่วนสามารถกดปุ่ม ⚡ คำถาม ได้เลยครับ";
            let cards = [];

            const lower = text.toLowerCase();
            if (lower.includes('ราคา') || lower.includes('เท่าไหร่') || lower.includes('โปรโมชั่น') || lower.includes('ส่วนลด') || lower.includes('คูปอง') || lower.includes('cat10off')) {
                replyText = "🎉 ตอนนี้มีโปรโมชั่นพิเศษ:\n• โค้ด CAT10OFF ลดทันที 10% (ขั้นต่ำ 300.-)\n• โค้ด NEWMEMBER5 ลด 5% สำหรับสมาชิกใหม่\n• แถมฟรี Starter Kit 11 ชิ้นมูลค่า 4,500.- ทุกออเดอร์ครับ!";
                cards = BOT_RESPONSES[5].cards;
            } else if (lower.includes('คอนโด') || lower.includes('ห้องพัก')) {
                replyText = BOT_RESPONSES[2].text;
                cards = BOT_RESPONSES[2].cards;
            } else if (lower.includes('แพ้') || lower.includes('ภูมิแพ้') || lower.includes('ขนร่วง')) {
                replyText = BOT_RESPONSES[3].text;
                cards = BOT_RESPONSES[3].cards;
            } else if (lower.includes('ส่ง') || lower.includes('ประกัน') || lower.includes('วัคซีน')) {
                replyText = BOT_RESPONSES[6].text;
            } else if (lower.includes('สวัสดี') || lower.includes('หวัดดี') || lower.includes('hello') || lower.includes('hi')) {
                replyText = "สวัสดีครับยินดีต้อนรับสู่ Purrfect Shop! 🐾 สนใจน้องแมวพันธุ์ไหนเป็นพิเศษ หรือสอบถามการดูแล สามารถเลือกคำถามด่วนด้านล่างหรือพิมพ์คุยได้เลยครับ";
                cards = BOT_RESPONSES[1].cards;
            }

            const msgs = getLocalChatMessages();
            msgs.push({
                id: 'msg_c_' + Date.now(),
                sender: 'customer',
                sender_name: 'คุณ',
                text: text,
                cards: [],
                timestamp: getCurrentTimeFormatted()
            });
            msgs.push({
                id: 'msg_b_' + Date.now(),
                sender: 'bot',
                sender_name: 'Purrfect Advisor 🐾',
                text: replyText,
                cards: cards,
                timestamp: getCurrentTimeFormatted()
            });

            saveLocalChatMessages(msgs);
            renderMessages(msgs, true);
        }, 1200);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadChatMessages(false);
        const isPhpServer = window.location.protocol.startsWith('http') && !window.location.hostname.includes('github.io');
        if (isPhpServer) {
            pollInterval = setInterval(() => {
                if (chatIsOpen && !isBotTyping) {
                    loadChatMessages(false);
                }
            }, 3500);
        }
    });
})();
</script>
