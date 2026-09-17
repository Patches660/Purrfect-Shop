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

