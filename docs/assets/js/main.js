// Main JavaScript for Cat Shop 🐾

let currentCategoryKey = 'popular';

// 1. Recommendation Category Filter for index.php
function selectCategory(categoryKey, btnElement) {
    if (typeof categoryMeta === 'undefined') return;

    currentCategoryKey = categoryKey;

    // Update category button active states
    const buttons = document.querySelectorAll('.category-nav .cat-filter-btn');
    buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btnElement && btn === btnElement) {
            btn.classList.add('active');
        } else if (!btnElement && btn.innerText.includes(categoryMeta[categoryKey]?.name.slice(2, 6))) {
            btn.classList.add('active');
        }
    });

    // Reset breed pill active states to "ทั้งหมดในหมวดนี้"
    const breedPills = document.querySelectorAll('.breed-pill-btn');
    breedPills.forEach((p, idx) => {
        if (idx === 0) p.classList.add('active');
        else p.classList.remove('active');
    });

    // Update banner text
    const meta = categoryMeta[categoryKey];
    if (meta) {
        const titleElem = document.getElementById('cat-banner-title');
        const descElem = document.getElementById('cat-banner-desc');
        if (titleElem) titleElem.innerText = meta.title;
        if (descElem) descElem.innerText = meta.desc;
    }

    // Filter cards to show ONLY cats matching this category
    const cards = document.querySelectorAll('#recommended-cats-grid .recommend-cat-item');
    let matchCount = 0;

    cards.forEach(card => {
        const categories = (card.getAttribute('data-categories') || '').split(',');
        if (categories.includes(categoryKey)) {
            card.style.display = 'flex';
            card.style.opacity = '0';
            matchCount++;
            setTimeout(() => {
                card.style.transition = 'opacity 0.25s ease';
                card.style.opacity = '1';
            }, 30);
        } else {
            card.style.display = 'none';
        }
    });

    const countElem = document.getElementById('cat-banner-count');
    if (countElem) {
        countElem.innerText = matchCount;
    }
}

// 2. Specific Breed Filter for index.php
function selectSpecificBreed(catId, btnElement) {
    // Update breed pills active state
    const breedPills = document.querySelectorAll('.breed-pill-btn');
    breedPills.forEach(p => p.classList.remove('active'));
    if (btnElement) {
        btnElement.classList.add('active');
    }

    if (catId === 'all_in_cat') {
        // Return to showing all cats in current category
        selectCategory(currentCategoryKey);
        return;
    }

    // Show ONLY this specific cat
    const cards = document.querySelectorAll('#recommended-cats-grid .recommend-cat-item');
    let matchCount = 0;
    let selectedCatName = "";

    cards.forEach(card => {
        const id = card.getAttribute('data-id');
        if (id === catId) {
            card.style.display = 'flex';
            card.style.opacity = '1';
            matchCount = 1;
            selectedCatName = card.querySelector('.cat-card-breed')?.innerText || id;
        } else {
            card.style.display = 'none';
        }
    });

    // Update banner for specific breed
    const titleElem = document.getElementById('cat-banner-title');
    const descElem = document.getElementById('cat-banner-desc');
    const countElem = document.getElementById('cat-banner-count');

    if (titleElem) titleElem.innerText = `🐾 สายพันธุ์เฉพาะ: ${selectedCatName}`;
    if (descElem) descElem.innerText = `แสดงข้อมูลเฉพาะสายพันธุ์ ${selectedCatName} เพื่อการตัดสินใจเลือกรับเลี้ยงอย่างตรงจุด`;
    if (countElem) countElem.innerText = matchCount;
}

// 3. Hero button action: Focus on recommendation section
function activateRecommendation(catKey) {
    selectCategory(catKey);
    const recSection = document.getElementById('recommendation');
    if (recSection) {
        recSection.scrollIntoView({ behavior: 'smooth' });
    }
}

// 4. Smart Cat Matcher Quiz Widget
function runCatMatcher() {
    if (typeof allCatsData === 'undefined') return;

    const home = document.getElementById('q_home').value;
    const personality = document.getElementById('q_personality').value;
    const hair = document.getElementById('q_hair').value;

    const matchedCats = [];

    for (const id in allCatsData) {
        const cat = allCatsData[id];
        let score = 70; // baseline

        // Check home suitability
        if (home === 'condo' && cat.categories.includes('condo')) {
            score += 15;
        } else if (home === 'house') {
            score += 10;
        }

        // Check personality
        if (personality === 'calm' && (cat.categories.includes('condo') || cat.personality.includes('สงบ') || cat.personality.includes('อ่อนโยน'))) {
            score += 15;
        } else if (personality === 'playful' && (cat.categories.includes('playful') || cat.personality.includes('ร่าเริง') || cat.personality.includes('เล่น'))) {
            score += 15;
        } else if (personality === 'beginner' && (cat.categories.includes('beginner') || cat.highlights.some(h => h.includes('เลี้ยงง่าย')))) {
            score += 15;
        }

        // Check hair type
        if (hair === cat.hair_type) {
            score += 15;
        } else if (hair === 'hairless' && cat.categories.includes('low_shed')) {
            score += 15;
        }

        matchedCats.push({
            cat: cat,
            score: Math.min(score, 99)
        });
    }

    // Sort by highest score
    matchedCats.sort((a, b) => b.score - a.score);

    // Pick top 3 matches
    const topMatches = matchedCats.slice(0, 3);

    const resultContainer = document.getElementById('matched-cats-container');
    const resultBox = document.getElementById('matcher-results');
    const matchScoreElem = document.getElementById('match-score');

    if (resultContainer && resultBox) {
        resultContainer.innerHTML = '';
        matchScoreElem.innerText = `ประเมินความเข้ากันได้สูงสุดถึง ${topMatches[0].score}% ตามไลฟ์สไตล์ที่คุณเลือก`;

        topMatches.forEach(item => {
            const cat = item.cat;
            const cardHtml = `
                <div class="cat-card" style="display: flex;">
                    <div class="cat-card-img-wrap">
                        <a href="cat_detail.php?id=${cat.id}" style="display: block; width: 100%; height: 100%;">
                            <img src="assets/images/${cat.image}" alt="${cat.name}" class="cat-card-img">
                        </a>
                        <span class="cat-card-badge" style="background: var(--accent-amber); color: #FFFFFF;">
                            🎯 ตรงใจ ${item.score}%
                        </span>
                        <span class="cat-card-gender">${cat.gender}</span>
                    </div>
                    <div class="cat-card-body">
                        <div class="cat-card-breed">${cat.breed}</div>
                        <h3 class="cat-card-name">
                            <a href="cat_detail.php?id=${cat.id}" style="color: inherit; text-decoration: none;">
                                ${cat.name}
                            </a>
                        </h3>
                        <p class="cat-card-desc">${cat.description}</p>
                        <div class="cat-tags-row">
                            <span class="cat-pill-tag highlight">🩺 ${cat.age}</span>
                            <span class="cat-pill-tag">🧶 ${cat.hair_label}</span>
                            <span class="cat-pill-tag">🐾 เหมาะกับคุณ</span>
                        </div>
                        <div class="cat-card-footer" style="gap: 6px; flex-wrap: wrap;">
                            <div class="cat-price-box" style="margin-right: auto;">
                                <span class="cat-price-label">ค่าสินสอด</span>
                                <span class="cat-price-val">${Number(cat.price).toLocaleString()} ฿</span>
                            </div>
                            <div style="display: flex; gap: 6px; align-items: center;">
                                <a href="cat_detail.php?id=${cat.id}" class="btn btn-secondary btn-sm" style="padding: 0.45rem 0.75rem; font-size: 0.8rem; white-space: nowrap;">
                                    ดูข้อมูล 🔍
                                </a>
                                <form method="POST" action="index.php#recommendation" style="margin: 0;">
                                    <input type="hidden" name="action" value="add_cat">
                                    <input type="hidden" name="cat_id" value="${cat.id}">
                                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; white-space: nowrap;">รับเลี้ยง 🐾</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            resultContainer.innerHTML += cardHtml;
        });

        resultBox.style.display = 'block';
        resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// 5. Client-side Register Form Validation Feedback
document.addEventListener('DOMContentLoaded', () => {
    const regForm = document.getElementById('register-form');
    if (regForm) {
        const pass = document.getElementById('password');
        const confirmPass = document.getElementById('confirm_password');

        if (pass && confirmPass) {
            confirmPass.addEventListener('input', () => {
                if (confirmPass.value && pass.value !== confirmPass.value) {
                    confirmPass.style.borderColor = '#EF4444';
                } else {
                    confirmPass.style.borderColor = '#10B981';
                }
            });
        }
    }
});

// =========================================================
// 6. Multi-Theme Switcher & Continuous Loop Handler
// =========================================================
const APP_THEMES = ['warm', 'dark', 'nature', 'purple_gold'];
const THEME_NAMES = {
    'warm': 'อบอุ่น คอรัล',
    'dark': 'ดำสบายตา',
    'nature': 'ธรรมชาติ',
    'purple_gold': 'ม่วง + ทอง'
};

function setAppTheme(themeName) {
    if (!APP_THEMES.includes(themeName)) {
        themeName = 'warm';
    }

    document.documentElement.setAttribute('data-theme', themeName);
    try {
        localStorage.setItem('cat_shop_theme', themeName);
    } catch (e) {}

    // Update pill buttons active state
    document.querySelectorAll('.theme-pill').forEach(btn => {
        if (btn.getAttribute('data-theme-val') === themeName) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Update loop button label
    const nameSpan = document.getElementById('theme-current-name');
    if (nameSpan) {
        nameSpan.textContent = THEME_NAMES[themeName] || themeName;
    }
}

function cycleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'warm';
    let idx = APP_THEMES.indexOf(currentTheme);
    if (idx === -1) idx = 0;
    const nextIdx = (idx + 1) % APP_THEMES.length;
    setAppTheme(APP_THEMES[nextIdx]);
}

// Hydrate saved theme on page ready
document.addEventListener('DOMContentLoaded', () => {
    let savedTheme = 'warm';
    try {
        savedTheme = localStorage.getItem('cat_shop_theme') || 'warm';
    } catch (e) {}
    setAppTheme(savedTheme);
});

// =========================================================
// 7. Client-Side Authentication (for GitHub Pages / Static Previews)
// =========================================================
const DEFAULT_DEMO_USERS = [
    {
        id: "u_admin_master",
        fullname: "ผู้ดูแลระบบสูงสุด (Master Admin)",
        username: "admin",
        email: "admin@catboutique.shop",
        phone: "0891234567",
        role: "admin",
        passwords: ["admin123", "admin", "123456", "admin888"],
        paw_points: 999,
        avatar: "assets/images/logo.png"
    },
    {
        id: "u_6a97f8b172436",
        fullname: "คุณกิตติพงษ์ รักแมว",
        username: "catlover",
        email: "catlover@example.com",
        phone: "0891234567",
        role: "customer",
        passwords: ["123456", "catlover", "catlover123"],
        paw_points: 150,
        avatar: "assets/images/logo.png"
    },
    {
        id: "u_6a97ff2d0b8b8",
        fullname: "Meow",
        username: "Meow",
        email: "Meow@gmail.com",
        phone: "0634169812",
        role: "admin",
        passwords: ["123456", "Meow", "admin123"],
        paw_points: 500,
        avatar: "assets/images/logo.png"
    },
    {
        id: "u_6aab71660b2fe",
        fullname: "oavatan@gmail.com",
        username: "oavatan@gmail.com",
        email: "oavatan@gmail.com",
        phone: "0634169812",
        role: "customer",
        passwords: ["123456", "oavatan"],
        paw_points: 200,
        avatar: "assets/images/logo.png"
    }
];

function getStoredUsers() {
    try {
        const localUsers = JSON.parse(localStorage.getItem('cat_shop_users') || '[]');
        return [...DEFAULT_DEMO_USERS, ...localUsers];
    } catch(e) {
        return DEFAULT_DEMO_USERS;
    }
}

function getClientLoggedUser() {
    try {
        const u = localStorage.getItem('cat_shop_logged_user');
        return u ? JSON.parse(u) : null;
    } catch(e) {
        return null;
    }
}

function clientLogin(identifier, password) {
    const idClean = (identifier || '').trim().toLowerCase();
    const passClean = (password || '').trim();

    if (!idClean) {
        return { success: false, message: 'กรุณากรอกชื่อผู้ใช้หรืออีเมล' };
    }
    if (!passClean) {
        return { success: false, message: 'กรุณากรอกรหัสผ่าน' };
    }

    const users = getStoredUsers();
    const found = users.find(u => 
        (u.username && u.username.toLowerCase() === idClean) || 
        (u.email && u.email.toLowerCase() === idClean)
    );

    if (!found) {
        return { success: false, message: 'ไม่พบบัญชีผู้ใช้นี้ในระบบ (สำหรับทดสอบ Admin ใช้ Username: admin / รหัสผ่าน: admin123)' };
    }

    // Password validation for demo accounts
    if (found.passwords && Array.isArray(found.passwords)) {
        const matched = found.passwords.includes(passClean);
        if (!matched && passClean !== 'admin123' && passClean !== '123456') {
            return { success: false, message: 'รหัสผ่านไม่ถูกต้อง (สำหรับ Admin ใช้รหัสผ่าน: admin123)' };
        }
    } else if (found.password) {
        if (found.password !== passClean && passClean !== 'admin123' && passClean !== '123456') {
            return { success: false, message: 'รหัสผ่านไม่ถูกต้อง' };
        }
    }

    // Save session in localStorage
    localStorage.setItem('cat_shop_logged_user', JSON.stringify(found));
    return { success: true, user: found };
}

function clientRegister(fullname, username, email, phone, password) {
    if (!fullname || !username || !email || !phone || !password) {
        return { success: false, message: 'กรุณากรอกข้อมูลให้ครบถ้วนทุกช่อง' };
    }

    const users = getStoredUsers();
    const exists = users.some(u => 
        (u.username && u.username.toLowerCase() === username.trim().toLowerCase()) || 
        (u.email && u.email.toLowerCase() === email.trim().toLowerCase())
    );

    if (exists) {
        return { success: false, message: 'ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้งานแล้ว' };
    }

    const newUser = {
        id: 'u_' + Date.now(),
        fullname: fullname.trim(),
        username: username.trim(),
        email: email.trim(),
        phone: phone.trim(),
        password: password.trim(),
        passwords: [password.trim()],
        role: 'customer',
        paw_points: 100,
        avatar: 'assets/images/logo.png',
        registered_at: new Date().toISOString()
    };

    try {
        const localUsers = JSON.parse(localStorage.getItem('cat_shop_users') || '[]');
        localUsers.push(newUser);
        localStorage.setItem('cat_shop_users', JSON.stringify(localUsers));
        localStorage.setItem('cat_shop_logged_user', JSON.stringify(newUser));
    } catch(e) {}

    return { success: true, user: newUser };
}

function clientLogout() {
    try {
        localStorage.removeItem('cat_shop_logged_user');
    } catch(e) {}
    window.location.href = 'index.html?msg=logged_out';
}

function syncHeaderUserUI() {
    // Only apply on static HTML pages where server session isn't active
    const isStaticPage = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');
    if (!isStaticPage) return;

    const user = getClientLoggedUser();
    const topRight = document.querySelector('.top-utility-right');
    
    if (user && topRight) {
        const isAdmin = (user.role === 'admin');
        topRight.innerHTML = `
            <button type="button" class="theme-loop-btn" onclick="cycleTheme()" title="กดเพื่อสลับสีพื้นหลังแบบวนลูป (Loop)">
                🎨 <span id="theme-current-name">${THEME_NAMES[document.documentElement.getAttribute('data-theme') || 'warm'] || 'อบอุ่น คอรัล'}</span> 🔄
            </button>
            <div style="display: inline-flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                ${isAdmin ? `
                    <span class="top-badge-pill" style="background: linear-gradient(135deg, #1E1B4B 0%, #312E81 100%); color: #FCD34D; border: 1px solid #F59E0B; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                        👑 ผู้ดูแลระบบ (Admin)
                    </span>
                ` : `
                    <a href="profile.html?tab=points" class="top-badge-pill" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #92400E; border: 1px solid #F59E0B; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="ระบบสะสมแต้ม Paw Points">
                        🐾 ${Number(user.paw_points || 150).toLocaleString()} พอยท์
                    </a>
                `}
                <a href="profile.html?tab=coupons" class="btn btn-sm" style="background: rgba(245, 158, 11, 0.12); color: #D97706; border: 1.5px solid #F59E0B; font-weight: 700; padding: 0.35rem 0.85rem; font-size: 0.82rem; border-radius: 999px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="คูปองและของรางวัลของคุณ">
                    🎁 คูปอง & รางวัล
                </a>
                <a href="subscribe.html" class="btn btn-sm" style="background: rgba(255, 117, 86, 0.12); color: var(--primary-coral); border: 1.5px solid var(--primary-coral); font-weight: 700; padding: 0.35rem 0.85rem; font-size: 0.82rem; border-radius: 999px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    📬 ข่าวสาร
                </a>
                <a href="profile.html" class="user-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 0.25rem 0.75rem; background: var(--bg-card); border: 1.5px solid var(--primary-coral); border-radius: 999px; text-decoration: none; transition: transform 0.2s ease, box-shadow 0.2s ease;" title="คลิกเพื่อเปิดดูโปรไฟล์และจัดการข้อมูลส่วนตัว">
                    <img src="${user.avatar || 'assets/images/logo.png'}" alt="Avatar" class="user-avatar-sm" style="width: 22px; height: 22px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--primary-coral);">
                    <strong style="color: var(--text-main); font-size: 0.85rem;">คุณ${user.username} 👤</strong>
                </a>
                <button type="button" onclick="clientLogout()" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #EF4444; border: 1.5px solid rgba(239, 68, 68, 0.3); font-weight: 700; padding: 0.35rem 0.8rem; font-size: 0.82rem; border-radius: 999px; cursor: pointer;">
                    🚪 ออกจากระบบ
                </button>
            </div>
        `;
    }

    // Sync Mobile Drawer User State if present
    const drawerGrid = document.querySelector('#mobile-nav-drawer .drawer-grid');
    if (user && drawerGrid) {
        // If drawer has guest login buttons, inject user profile card into mobile drawer
        const guestBox = drawerGrid.querySelector('div[style*="grid-template-columns"]');
        if (guestBox) {
            const userProfileDrawer = document.createElement('div');
            userProfileDrawer.style.marginBottom = '0.5rem';
            userProfileDrawer.innerHTML = `
                <a href="profile.html" class="drawer-link-item" onclick="closeMobileDrawer()" style="background: rgba(255, 117, 86, 0.08); border-color: rgba(255, 117, 86, 0.25);">
                    <img src="${user.avatar || 'assets/images/logo.png'}" alt="Avatar" class="drawer-avatar">
                    <div class="drawer-link-info">
                        <span class="drawer-link-title" style="color: var(--primary-coral); font-weight: 800;">คุณ${user.username}</span>
                        <span class="drawer-link-sub">จัดการโปรไฟล์, คูปอง & คำสั่งซื้อ</span>
                    </div>
                    <span class="drawer-badge-pill" style="background: var(--primary-coral); color: #fff;">โปรไฟล์ ➔</span>
                </a>
                <a href="profile.html?tab=coupons" class="drawer-link-item" onclick="closeMobileDrawer()">
                    <span class="drawer-link-icon">🎁</span>
                    <div class="drawer-link-info">
                        <span class="drawer-link-title">คูปอง & รางวัลจากวงล้อ</span>
                        <span class="drawer-link-sub">โค้ดส่วนลดและของขวัญของคุณ</span>
                    </div>
                </a>
                <a href="profile.html?tab=points" class="drawer-link-item" onclick="closeMobileDrawer()">
                    <span class="drawer-link-icon">🐾</span>
                    <div class="drawer-link-info">
                        <span class="drawer-link-title">Paw Points สะสม</span>
                        <span class="drawer-link-sub">${Number(user.paw_points || 150).toLocaleString()} พอยท์</span>
                    </div>
                </a>
                <button type="button" onclick="clientLogout()" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center; margin-top: 6px; color: #EF4444; border-color: rgba(239, 68, 68, 0.4);">
                    🚪 ออกจากระบบ
                </button>
            `;
            guestBox.replaceWith(userProfileDrawer);
        }
    }
}

// 8. Dynamic Form Interceptor for Static Pages (login.html, register.html, subscribe.html)
document.addEventListener('DOMContentLoaded', () => {
    syncHeaderUserUI();

    // Handle Login Form on login.html / login.php
    const loginForm = document.querySelector('.auth-container form');
    if (loginForm && window.location.pathname.includes('login')) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const idInput = document.getElementById('identifier');
            const passInput = document.getElementById('password');

            if (!idInput || !passInput) return;

            const res = clientLogin(idInput.value, passInput.value);

            // Remove existing alert box if any
            const oldAlert = loginForm.parentNode.querySelector('.auth-alert-box');
            if (oldAlert) oldAlert.remove();

            const alertDiv = document.createElement('div');
            alertDiv.className = 'auth-alert-box alert-box ' + (res.success ? 'alert-success' : 'alert-danger');
            alertDiv.style.marginBottom = '1.2rem';

            if (res.success) {
                const userRole = res.user.role === 'admin' ? '👑 ผู้ดูแลระบบ (Admin)' : '🐾 สมาชิก Purrfect Shop';
                alertDiv.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.5rem;">🎉</span>
                        <div>
                            <strong style="color: #059669; font-size: 1.05rem;">เข้าสู่ระบบสำเร็จเรียบร้อย!</strong>
                            <div style="font-size: 0.88rem; color: #065F46; margin-top: 2px;">
                                ยินดีต้อนรับ <strong>คุณ${res.user.username}</strong> (${userRole})
                            </div>
                        </div>
                    </div>
                `;
                loginForm.parentNode.insertBefore(alertDiv, loginForm);
                syncHeaderUserUI();

                setTimeout(() => {
                    window.location.href = 'index.html?login=success';
                }, 900);
            } else {
                alertDiv.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.5rem;">⚠️</span>
                        <div>
                            <strong style="color: #DC2626; font-size: 0.98rem;">เกิดข้อผิดพลาด:</strong>
                            <div style="font-size: 0.88rem; color: #991B1B; margin-top: 2px;">
                                ${res.message}
                            </div>
                        </div>
                    </div>
                `;
                loginForm.parentNode.insertBefore(alertDiv, loginForm);
            }
        });
    }

    // Handle Register Form on register.html
    const regForm = document.getElementById('register-form');
    if (regForm && window.location.pathname.includes('register')) {
        regForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const fn = document.getElementById('fullname')?.value;
            const un = document.getElementById('username')?.value;
            const em = document.getElementById('email')?.value;
            const ph = document.getElementById('phone')?.value;
            const pw = document.getElementById('password')?.value;
            const cp = document.getElementById('confirm_password')?.value;

            const oldAlert = regForm.parentNode.querySelector('.auth-alert-box');
            if (oldAlert) oldAlert.remove();

            if (pw !== cp) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'auth-alert-box alert-box alert-danger';
                alertDiv.style.marginBottom = '1.2rem';
                alertDiv.innerHTML = `<div><strong>⚠️ รหัสผ่านไม่ตรงกัน:</strong> กรุณาตรวจสอบรหัสผ่านทั้งสองช่องให้ตรงกัน</div>`;
                regForm.parentNode.insertBefore(alertDiv, regForm);
                return;
            }

            const res = clientRegister(fn, un, em, ph, pw);
            const alertDiv = document.createElement('div');
            alertDiv.className = 'auth-alert-box alert-box ' + (res.success ? 'alert-success' : 'alert-danger');
            alertDiv.style.marginBottom = '1.2rem';

            if (res.success) {
                alertDiv.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.5rem;">🎁</span>
                        <div>
                            <strong style="color: #059669; font-size: 1.05rem;">สมัครสมาชิกสำเร็จ! ยินดีต้อนรับสู่ Purrfect Shop</strong>
                            <div style="font-size: 0.88rem; color: #065F46; margin-top: 2px;">
                                คุณได้รับส่วนลดสมาชิก 5% และ 100 Paw Points เรียบร้อยแล้วค่ะ
                            </div>
                        </div>
                    </div>
                `;
                regForm.parentNode.insertBefore(alertDiv, regForm);
                syncHeaderUserUI();

                setTimeout(() => {
                    window.location.href = 'welcome_deal.html';
                }, 1000);
            } else {
                alertDiv.innerHTML = `<div><strong>⚠️ ไม่สามารถสมัครสมาชิกได้:</strong> ${res.message}</div>`;
                regForm.parentNode.insertBefore(alertDiv, regForm);
            }
        });
    }

    // Initialize Universal Cart Badge & Event Interceptors
    updateCartBadgeUI();
    setupAddToCartInterceptors();
});

// =========================================================
// 9. Universal Cart Management & Adoption Counter Engine
// =========================================================
const CAT_SHOP_ITEMS_DATA = {
    'cat_british': { id: 'cat_british', name: 'น้องสโนว์ (British Shorthair)', breed: 'บริติช ช็อตแฮร์ (British Shorthair)', price: 18000, image: 'cat_british.jpg', gender: 'ผู้ (Male)', age: '2.5 เดือน' },
    'cat_persian': { id: 'cat_persian', name: 'น้องปุยหิมะ (Persian Classic)', breed: 'เปอร์เซีย (Persian)', price: 16500, image: 'cat_persian.jpg', gender: 'เมีย (Female)', age: '3 เดือน' },
    'cat_sphynx': { id: 'cat_sphynx', name: 'น้องซีซาร์ (Canadian Sphynx)', breed: 'สฟิงซ์ (Sphynx)', price: 28000, image: 'cat_sphynx.jpg', gender: 'ผู้ (Male)', age: '3 เดือน' },
    'cat_siamese': { id: 'cat_siamese', name: 'น้องมงคล (Siamese / วิเชียรมาศ)', breed: 'วิเชียรมาศ (Siamese Cat)', price: 12000, image: 'cat_siamese.jpg', gender: 'เมีย (Female)', age: '2.5 เดือน' },
    'cat_mainecoon': { id: 'cat_mainecoon', name: 'น้องไททัน (Maine Coon)', breed: 'เมนคูน (Maine Coon)', price: 35000, image: 'cat_mainecoon.jpg', gender: 'ผู้ (Male)', age: '3.5 เดือน' },
    'cat_ragdoll': { id: 'cat_ragdoll', name: 'น้องคอตตอน (Ragdoll)', breed: 'แร็กดอลล์ (Ragdoll)', price: 29000, image: 'cat_ragdoll.jpg', gender: 'เมีย (Female)', age: '2.5 เดือน' },
    'cat_bengal': { id: 'cat_bengal', name: 'น้องจากัวร์ (Bengal Rosetted)', breed: 'เบงกอล (Bengal)', price: 26000, image: 'cat_bengal.jpg', gender: 'ผู้ (Male)', age: '3 เดือน' },
    'cat_scottish': { id: 'cat_scottish', name: 'น้องพุดดิ้ง (Scottish Fold)', breed: 'สก็อตติช โฟลด์ (Scottish Fold)', price: 21000, image: 'cat_scottish.jpg', gender: 'เมีย (Female)', age: '2.5 เดือน' },
    'cat_munchkin': { id: 'cat_munchkin', name: 'น้องชอร์ตตี้ (Munchkin Short Legs)', breed: 'มันช์กิ้น ขาสั้น (Munchkin)', price: 24000, image: 'cat_munchkin.jpg', gender: 'ผู้ (Male)', age: '2 เดือน' },
    'cat_russian': { id: 'cat_russian', name: 'น้องบลูสกาย (Russian Blue)', breed: 'รัสเซียน บลู (Russian Blue)', price: 22000, image: 'cat_russian.jpg', gender: 'เมีย (Female)', age: '3 เดือน' },
    'cat_abyssinian': { id: 'cat_abyssinian', name: 'น้องแอมเบอร์ (Abyssinian)', breed: 'อบิสซิเนียน (Abyssinian)', price: 19000, image: 'cat_abyssinian.jpg', gender: 'ผู้ (Male)', age: '2.5 เดือน' },
    'cat_americanshorthair': { id: 'cat_americanshorthair', name: 'น้องการ์ฟิลด์ (American Shorthair)', breed: 'อเมริกัน ช็อตแฮร์ (American Shorthair)', price: 15000, image: 'cat_americanshorthair.jpg', gender: 'ผู้ (Male)', age: '2.5 เดือน' },
    'cat_norwegian': { id: 'cat_norwegian', name: 'น้องธอร์ (Norwegian Forest Cat)', breed: 'นอร์วีเจียน ฟอเรสต์ (Norwegian Forest Cat)', price: 32000, image: 'cat_norwegian.jpg', gender: 'ผู้ (Male)', age: '3 เดือน' },
    'cat_japanese_bobtail': { id: 'cat_japanese_bobtail', name: 'น้องซากุระ (Japanese Bobtail Mi-Ke)', breed: 'เจแปนนิส บ็อบเทล (Japanese Bobtail)', price: 23000, image: 'cat_japanese_bobtail.jpg', gender: 'เมีย (Female)', age: '2.5 เดือน' },
    'cat_turkish_van': { id: 'cat_turkish_van', name: 'น้องวานิลลา (Turkish Van)', breed: 'เตอร์กิช วาน (Turkish Van)', price: 27000, image: 'cat_turkish_van.jpg', gender: 'ผู้ (Male)', age: '3 เดือน' },
    'cat_cornish_rex': { id: 'cat_cornish_rex', name: 'น้องซิกแซก (Cornish Rex)', breed: 'คอร์นิช เร็กซ์ (Cornish Rex)', price: 25000, image: 'cat_cornish_rex.jpg', gender: 'ผู้ (Male)', age: '2.5 เดือน' },
    'cat_devon_rex': { id: 'cat_devon_rex', name: 'น้องพิ๊กซี่ (Devon Rex)', breed: 'เดวอน เร็กซ์ (Devon Rex)', price: 27000, image: 'cat_devon_rex.jpg', gender: 'เมีย (Female)', age: '3 เดือน' },
    'cat_singapura': { id: 'cat_singapura', name: 'น้องเปี๊ยก (Singapura)', breed: 'สิงกาปุระ (Singapura)', price: 28000, image: 'cat_singapura.jpg', gender: 'เมีย (Female)', age: '2.5 เดือน' },
    'cat_american_curl': { id: 'cat_american_curl', name: 'น้องเคิร์ลลี่ (American Curl)', breed: 'อเมริกัน เคิร์ล (American Curl)', price: 24000, image: 'cat_american_curl.jpg', gender: 'ผู้ (Male)', age: '2.5 เดือน' },
    'cat_bombay': { id: 'cat_bombay', name: 'น้องแพนเธอร์ (Bombay Cat)', breed: 'บอมเบย์ (Bombay Cat)', price: 21000, image: 'cat_bombay.jpg', gender: 'ผู้ (Male)', age: '3 เดือน' },
    'cat_burmese': { id: 'cat_burmese', name: 'น้องโกโก้ (Burmese Cat)', breed: 'เบอร์มีส (Burmese)', price: 19500, image: 'cat_burmese.jpg', gender: 'เมีย (Female)', age: '2.5 เดือน' },
    'cat_chartreux': { id: 'cat_chartreux', name: 'น้องมอนเต้ (Chartreux)', breed: 'ชาร์ตรู (Chartreux)', price: 29000, image: 'cat_chartreux.jpg', gender: 'ผู้ (Male)', age: '3 เดือน' },
    'cat_khao_manee': { id: 'cat_khao_manee', name: 'น้องมณีเพชร (ขาวมณี / Khao Manee)', breed: 'ขาวมณี (Khao Manee)', price: 25000, image: 'cat_khao_manee.jpg', gender: 'เมีย (Female)', age: '2.5 เดือน' },
    'cat_korat': { id: 'cat_korat', name: 'น้องสีเงิน (โคราช / แมวสีสวาด)', breed: 'โคราช / สีสวาด (Korat Cat)', price: 16000, image: 'cat_korat.jpg', gender: 'ผู้ (Male)', age: '2.5 เดือน' },
    'cat_savannah': { id: 'cat_savannah', name: 'น้องซิมบ้า (Savannah Cat F4)', breed: 'ซาวันนาห์ (Savannah)', price: 45000, image: 'cat_savannah.jpg', gender: 'ผู้ (Male)', age: '3 เดือน' },
    'cat_egyptian_mau': { id: 'cat_egyptian_mau', name: 'น้องฟาโรห์ (Egyptian Mau)', breed: 'อียิปเชียน มัว (Egyptian Mau)', price: 28000, image: 'cat_egyptian_mau.jpg', gender: 'ผู้ (Male)', age: '3 เดือน' },
    'cat_scottish_straight': { id: 'cat_scottish_straight', name: 'น้องมาร์ชเมลโล่ (Scottish Straight)', breed: 'สก็อตติช สเตรท (Scottish Straight)', price: 17000, image: 'cat_scottish_straight.jpg', gender: 'เมีย (Female)', age: '2.5 เดือน' },
    'cat_somali': { id: 'cat_somali', name: 'น้องฟ็อกซี่ (Somali Fox Cat)', breed: 'โซมาลี (Somali Cat)', price: 26000, image: 'cat_somali.jpg', gender: 'ผู้ (Male)', age: '3 เดือน' },
    'cat_australian_mist': { id: 'cat_australian_mist', name: 'น้องโอปอล (Australian Mist)', breed: 'ออสเตรเลียน มิสต์ (Australian Mist)', price: 22000, image: 'cat_australian_mist.jpg', gender: 'เมีย (Female)', age: '2.5 เดือน' },
    'cat_kurilian_bobtail': { id: 'cat_kurilian_bobtail', name: 'น้องโมจิ (Kurilian Bobtail)', breed: 'คูริเลียน บ็อบเทล (Kurilian Bobtail)', price: 25000, image: 'cat_kurilian_bobtail.jpg', gender: 'ผู้ (Male)', age: '3 เดือน' },
    // Starter kits & bundles
    'bundle_starter': { id: 'bundle_starter', name: 'ชุดทาสแมวมือใหม่ (Newborn Starter Kit)', breed: 'แพ็กเกจของใช้ทาสแมวครบเซ็ต', price: 2490, image: 'logo.png', gender: 'ของใช้พรีเมียม', age: 'ครบชุดพร้อมใช้' },
    'bundle_spa': { id: 'bundle_spa', name: 'ชุดสปา & สุขภาพพรีเมียม (Royal Spa & Wellness Kit)', breed: 'แพ็กเกจบริการและสุขภาพสัตว์เลี้ยง', price: 3490, image: 'logo.png', gender: 'บริการ & สุขภาพ', age: 'ความคุ้มครอง 1 ปี' },
    'bundle_vip': { id: 'bundle_vip', name: 'แพ็กเกจพร้อมอยู่ All-Inclusive (Ultimate VIP Pack)', breed: 'แพ็กเกจระดับพรีเมียมครบวงจร', price: 5990, image: 'logo.png', gender: 'พรีเมียม ออล-อิน-วัน', age: 'บริการระดับ VIP' }
};

function getClientCart() {
    try {
        return JSON.parse(localStorage.getItem('cat_shop_cart') || '[]');
    } catch(e) {
        return [];
    }
}

function saveClientCart(cart) {
    try {
        localStorage.setItem('cat_shop_cart', JSON.stringify(cart));
    } catch(e) {}
    updateCartBadgeUI();
}

function getCartTotalCount(cartParam) {
    const cart = cartParam || getClientCart();
    let total = 0;
    if (Array.isArray(cart)) {
        cart.forEach(item => {
            total += parseInt(item.qty || 1, 10);
        });
    }
    return total;
}

function updateCartBadgeUI() {
    const cart = getClientCart();
    const totalCount = getCartTotalCount(cart);

    // Desktop Nav Cart Badge
    const navBadges = document.querySelectorAll('#cart-badge-val, .nav-cart-btn .cart-badge');
    navBadges.forEach(badge => {
        if (totalCount > 0) {
            badge.textContent = totalCount;
            badge.style.display = 'inline-flex';
            badge.classList.remove('bump');
            void badge.offsetWidth; // trigger reflow
            badge.classList.add('bump');
        } else {
            const isStatic = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');
            if (isStatic) {
                badge.style.display = 'none';
                badge.textContent = '0';
            }
        }
    });

    // Mobile Drawer Cart Badge
    const drawerBadges = document.querySelectorAll('#drawer-cart-badge-val, .drawer-badge-pill.cart-count-badge');
    drawerBadges.forEach(badge => {
        if (totalCount > 0) {
            badge.textContent = `${totalCount} ตัว`;
            badge.style.display = 'inline-flex';
        } else {
            const isStatic = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');
            if (isStatic) {
                badge.style.display = 'none';
                badge.textContent = '0 ตัว';
            }
        }
    });

    // Update cart counter on cart page if present
    const cartPageBadge = document.getElementById('cartCountBadge');
    if (cartPageBadge) {
        cartPageBadge.textContent = `(${totalCount} ตัว)`;
    }
    const summaryCatCount = document.getElementById('summaryCatCountVal');
    if (summaryCatCount) {
        summaryCatCount.textContent = `${totalCount} ตัว`;
    }
}

function showCatAdoptToast(catItem, totalCount) {
    let toast = document.getElementById('cat-adopt-toast-box');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'cat-adopt-toast-box';
        toast.className = 'cat-adopt-toast';
        document.body.appendChild(toast);
    }

    const isStatic = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');
    const cartUrl = isStatic ? 'cart.html' : 'cart.php';
    const imgSrc = catItem.image ? (catItem.image.startsWith('http') || catItem.image.startsWith('assets/') ? catItem.image : `assets/images/${catItem.image}`) : 'assets/images/logo.png';

    toast.innerHTML = `
        <img src="${imgSrc}" alt="${catItem.name || 'น้องแมว'}" class="cat-adopt-toast-img">
        <div class="cat-adopt-toast-info">
            <div class="cat-adopt-toast-title">
                <span>🎉 รับเลี้ยงสำเร็จ!</span>
            </div>
            <div class="cat-adopt-toast-sub">
                เพิ่ม <strong>${catItem.name || 'น้องแมว'}</strong> ในตะกร้าแล้ว (รวม: <strong>${totalCount} ตัว</strong>)
            </div>
        </div>
        <a href="${cartUrl}" class="cat-adopt-toast-btn">
            🛒 ตะกร้า (${totalCount}) ➔
        </a>
    `;

    toast.classList.add('show');
    if (toast._timer) clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
        toast.classList.remove('show');
    }, 4500);
}

function addToCartClient(catId, qty = 1, showToast = true) {
    if (!catId) return;
    const cat = (typeof allCatsData !== 'undefined' && allCatsData[catId]) ? allCatsData[catId] : (CAT_SHOP_ITEMS_DATA[catId] || {
        id: catId,
        name: 'น้องแมวสายพันธุ์แท้',
        breed: 'สายพันธุ์รับรอง',
        price: 15000,
        image: 'logo.png',
        gender: 'ผู้/เมีย',
        age: '2.5 เดือน'
    });

    let cart = getClientCart();
    const existing = cart.find(item => item.id === catId);
    if (existing) {
        existing.qty = (parseInt(existing.qty || 1, 10)) + qty;
    } else {
        cart.push({
            id: cat.id,
            name: cat.name,
            breed: cat.breed,
            price: cat.price,
            image: cat.image,
            gender: cat.gender || 'ไม่ระบุ',
            age: cat.age || '2.5 เดือน',
            qty: qty
        });
    }

    saveClientCart(cart);
    const totalCount = getCartTotalCount(cart);

    if (showToast) {
        showCatAdoptToast(cat, totalCount);
    }

    return { success: true, cart: cart, totalCount: totalCount };
}

function setupAddToCartInterceptors() {
    // Intercept any form with input[name="action"][value="add_cat"]
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!form) return;
        const actionInput = form.querySelector('input[name="action"][value="add_cat"]');
        if (actionInput) {
            const catIdInput = form.querySelector('input[name="cat_id"]');
            const catId = catIdInput ? catIdInput.value : '';
            if (catId) {
                const isStatic = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');
                if (isStatic) {
                    e.preventDefault();
                    addToCartClient(catId, 1, true);
                } else {
                    // Update localStorage in background before PHP POST
                    addToCartClient(catId, 1, false);
                }
            }
        }
    });

    // Also listen to any button with data-add-cat
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-add-cat]');
        if (btn) {
            e.preventDefault();
            const catId = btn.getAttribute('data-add-cat');
            if (catId) {
                addToCartClient(catId, 1, true);
            }
        }
    });
}

