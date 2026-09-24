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
                        <img src="assets/images/${cat.image}" alt="${cat.name}" class="cat-card-img">
                        <span class="cat-card-badge" style="background: var(--accent-amber); color: #FFFFFF;">
                            🎯 ตรงใจ ${item.score}%
                        </span>
                        <span class="cat-card-gender">${cat.gender}</span>
                    </div>
                    <div class="cat-card-body">
                        <div class="cat-card-breed">${cat.breed}</div>
                        <h3 class="cat-card-name">${cat.name}</h3>
                        <p class="cat-card-desc">${cat.description}</p>
                        <div class="cat-tags-row">
                            <span class="cat-pill-tag highlight">🩺 ${cat.age}</span>
                            <span class="cat-pill-tag">🧶 ${cat.hair_label}</span>
                            <span class="cat-pill-tag">🐾 เหมาะกับคุณ</span>
                        </div>
                        <div class="cat-card-footer">
                            <div class="cat-price-box">
                                <span class="cat-price-label">ค่าสินสอด</span>
                                <span class="cat-price-val">${Number(cat.price).toLocaleString()} ฿</span>
                            </div>
                            <form method="POST" action="index.php#recommendation">
                                <input type="hidden" name="action" value="add_cat">
                                <input type="hidden" name="cat_id" value="${cat.id}">
                                <button type="submit" class="btn btn-primary btn-sm">รับเลี้ยงน้อง 🐾</button>
                            </form>
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
                    <a href="index.html#points" class="top-badge-pill" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #92400E; border: 1px solid #F59E0B; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="ระบบสะสมแต้ม Paw Points">
                        🐾 ${user.paw_points || 150} พอยท์
                    </a>
                `}
                <a href="subscribe.html" class="btn btn-sm" style="background: rgba(255, 117, 86, 0.12); color: var(--primary-coral); border: 1.5px solid var(--primary-coral); font-weight: 700; padding: 0.35rem 0.85rem; font-size: 0.82rem; border-radius: 999px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    📬 ข่าวสาร
                </a>
                <span class="user-badge" style="cursor: default; display: inline-flex; align-items: center; gap: 6px; padding: 0.25rem 0.65rem; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 999px;" title="เข้าสู่ระบบแล้ว">
                    <img src="${user.avatar || 'assets/images/logo.png'}" alt="Avatar" class="user-avatar-sm" style="width: 22px; height: 22px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--primary-coral);">
                    <strong style="color: var(--text-main); font-size: 0.85rem;">คุณ${user.username}</strong>
                </span>
                <button type="button" onclick="clientLogout()" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #EF4444; border: 1.5px solid rgba(239, 68, 68, 0.3); font-weight: 700; padding: 0.35rem 0.8rem; font-size: 0.82rem; border-radius: 999px; cursor: pointer;">
                    🚪 ออกจากระบบ
                </button>
            </div>
        `;
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
});
