<?php
require_once __DIR__ . '/data.php';

// Handle Add to Cart action
$added_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_cat') {
    $cat_id = $_POST['cat_id'] ?? '';
    if (addToCart($cat_id)) {
        $cat_name = $cats[$cat_id]['name'] ?? 'น้องแมว';
        $added_message = "🐾 เพิ่ม " . htmlspecialchars($cat_name) . " ลงในตะกร้าเรียบร้อยแล้ว!";
    }
}

// Initial filter from query string
$selected_category = $_GET['cat'] ?? 'all';
if (!isset($recommendation_categories[$selected_category])) {
    $selected_category = 'all';
}

require_once __DIR__ . '/header.php';
?>

<!-- Added Alert Banner -->
<?php if (!empty($added_message)): ?>
    <div class="alert-box alert-success" style="margin-bottom: 2rem; justify-content: space-between;">
        <span><?php echo $added_message; ?></span>
        <a href="cart.php" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.9rem; font-size: 0.8rem;">
            ดูตะกร้าสินค้า 🛒
        </a>
    </div>
<?php endif; ?>

<!-- Section Header -->
<div class="section-header">
    <span class="section-tag">🐱 PREMIUM CATTERY</span>
    <h1 class="section-title">ศูนย์รวมน้องแมวสายพันธุ์แท้ 100%</h1>
    <p class="section-subtitle">
        คัดสรรเฉพาะน้องแมวเกรดคุณภาพ เลี้ยงดูในระบบปิด ได้รับการฉีดวัคซีนและตรวจสุขภาพครบถ้วน พร้อมใบเพ็ดดีกรี
    </p>
</div>

<!-- Search & Filter Controls -->
<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm);">
    <!-- Category Tabs -->
    <div style="margin-bottom: 1.2rem;">
        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.6rem;">
            🏷️ เลือกหมวดหมู่แนะนำ:
        </label>
        <div class="category-nav" style="justify-content: flex-start; margin-bottom: 0;">
            <?php foreach ($recommendation_categories as $cat_key => $cat_meta): ?>
                <button type="button" 
                        class="cat-filter-btn <?php echo $selected_category === $cat_key ? 'active' : ''; ?>"
                        onclick="filterProductsPage('<?php echo $cat_key; ?>', this)">
                    <?php echo $cat_meta['name']; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Search & Sort Row -->
    <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between; padding-top: 1.2rem; border-top: 1px dashed var(--border-color);">
        <div style="flex: 1; min-width: 250px;">
            <input type="text" id="cat-search-input" class="form-control" 
                   placeholder="🔍 ค้นหาชื่อสายพันธุ์, ลักษณะ, หรือนิสัย เช่น บริติช, ขาสั้น, ขี้อ้อน..." 
                   oninput="applyProductFilters()">
        </div>
        <div style="display: flex; align-items: center; gap: 0.8rem;">
            <label for="cat-sort-select" style="font-size: 0.88rem; font-weight: 600; color: var(--text-secondary); white-space: nowrap;">
                เรียงตาม:
            </label>
            <select id="cat-sort-select" class="form-control" style="width: auto; padding: 0.6rem 1rem;" onchange="applyProductFilters()">
                <option value="default">สายพันธุ์แนะนำ</option>
                <option value="price_asc">ราคา: ต่ำไปสูง</option>
                <option value="price_desc">ราคา: สูงไปต่ำ</option>
                <option value="name_asc">ชื่อสายพันธุ์ (ก-ฮ / A-Z)</option>
            </select>
        </div>
    </div>
</div>

<!-- Mobile Filter Trigger Bar (Visible on Mobile & Tablet) -->
<div class="mobile-filter-bar">
    <button type="button" class="mobile-filter-trigger-btn" onclick="toggleProductFilterModal()" aria-label="เปิดตัวกรองสินค้าน้องแมว">
        <span style="display: inline-flex; align-items: center; gap: 8px;">
            <span>🏷️</span>
            <span style="font-weight: 800;">ตัวกรอง & ค้นหาสายพันธุ์</span>
        </span>
        <span class="mobile-filter-current-tag" id="mobile-filter-tag-label">
            <?php echo $recommendation_categories[$selected_category]['name'] ?? 'ทั้งหมด'; ?> ⚡
        </span>
    </button>
</div>

<!-- All Cats Grid -->
<div class="cats-grid" id="products-catalog-grid">
    <?php foreach ($cats as $key => $cat): ?>
        <div class="cat-card product-item-card" 
             data-id="<?php echo $cat['id']; ?>"
             data-name="<?php echo htmlspecialchars($cat['name']); ?>"
             data-breed="<?php echo htmlspecialchars($cat['breed']); ?>"
             data-desc="<?php echo htmlspecialchars($cat['description']); ?>"
             data-price="<?php echo $cat['price']; ?>"
             data-categories="<?php echo implode(',', $cat['categories']); ?>"
             data-hair="<?php echo $cat['hair_type']; ?>"
             data-age="<?php echo htmlspecialchars($cat['age']); ?>">
            <div class="cat-card-img-wrap">
                <img src="assets/images/<?php echo $cat['image']; ?>" alt="<?php echo $cat['name']; ?>" class="cat-card-img">
                <span class="cat-card-badge">✨ พร้อมย้ายบ้าน</span>
                <span class="cat-card-gender"><?php echo $cat['gender']; ?></span>
            </div>
            
            <div class="cat-card-body">
                <div class="cat-card-breed"><?php echo $cat['breed']; ?></div>
                <h3 class="cat-card-name"><?php echo $cat['name']; ?></h3>
                <p class="cat-card-desc"><?php echo $cat['description']; ?></p>
                
                <div class="cat-tags-row">
                    <span class="cat-pill-tag highlight">🩺 <?php echo $cat['age']; ?></span>
                    <span class="cat-pill-tag">🧶 <?php echo $cat['hair_label']; ?></span>
                    <span class="cat-pill-tag">📜 ใบเพ็ดดีกรี</span>
                </div>

                <div style="background: var(--bg-page); border-radius: var(--radius-sm); padding: 0.6rem 0.8rem; margin-bottom: 1.2rem; font-size: 0.78rem; color: var(--text-secondary);">
                    <div style="font-weight: 600; color: var(--primary-coral); margin-bottom: 0.2rem;">✨ ข้อมูลสุขภาพ:</div>
                    <div><?php echo $cat['vaccine']; ?></div>
                </div>

                <div class="cat-card-footer">
                    <div class="cat-price-box">
                        <span class="cat-price-label">ค่าสินสอด / รับเลี้ยง</span>
                        <span class="cat-price-val"><?php echo number_format($cat['price']); ?> ฿</span>
                    </div>
                    <form method="POST" action="products.php">
                        <input type="hidden" name="action" value="add_cat">
                        <input type="hidden" name="cat_id" value="<?php echo $cat['id']; ?>">
                        <button type="submit" class="btn btn-primary btn-sm">
                            รับเลี้ยงน้อง 🐾
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Floating Blue Product Filter Action Button (Mobile & Tablet - Positioned Above Chat Icon) -->
<button type="button" id="mobile-floating-filter-btn" onclick="toggleProductFilterModal()" title="เปิด/ปิดตัวกรองน้องแมว (ราคา, อายุ, ขน, สายพันธุ์)" aria-label="ตัวกรองสินค้า">
    <span class="floating-filter-icon">🧺</span>
    <span class="floating-filter-badge" id="floating-filter-badge-val">กรอง</span>
</button>

<!-- =========================================================
     MOBILE PRODUCT FILTER DRAWER / BOTTOM SHEET MODAL
     ========================================================= -->
<div id="product-filter-backdrop" class="mobile-drawer-backdrop" onclick="closeProductFilterModal()"></div>

<div id="product-filter-modal" class="filter-bottom-sheet" aria-label="แผงตัวกรองสินค้าน้องแมว">
    <div class="filter-sheet-header">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 1.4rem;">🧺</span>
            <div>
                <strong style="font-size: 1.05rem; color: var(--text-main); display: block;">ตัวกรองและค้นหาน้องแมว 🐾</strong>
                <span style="font-size: 0.76rem; color: var(--text-muted);">เลือกตามราคา, อายุ, ประเภทขน และหมวดหมู่</span>
            </div>
        </div>
        <button type="button" class="drawer-close-btn" onclick="closeProductFilterModal()" aria-label="ปิดตัวกรอง">✕</button>
    </div>
    
    <div class="filter-sheet-body">
        <!-- 1. Search Box -->
        <div class="form-group" style="margin-bottom: 1.1rem;">
            <label class="filter-dimension-title">
                🔍 ค้นหาคำสำคัญ:
            </label>
            <input type="text" id="cat-search-input-modal" class="form-control" 
                   placeholder="พิมพ์ชื่อสายพันธุ์, นิสัย เช่น บริติช, ขาสั้น, ขี้อ้อน..." 
                   oninput="syncSearchInputs(this.value); applyProductFilters();">
        </div>

        <!-- 2. Sort Select -->
        <div class="form-group" style="margin-bottom: 1.1rem;">
            <label class="filter-dimension-title">
                📊 การจัดเรียง:
            </label>
            <select id="cat-sort-select-modal" class="form-control" onchange="syncSortSelects(this.value); applyProductFilters();">
                <option value="default">⭐ สายพันธุ์แนะนำ</option>
                <option value="price_asc">💵 ราคา: ต่ำไปสูง</option>
                <option value="price_desc">💎 ราคา: สูงไปต่ำ</option>
                <option value="name_asc">🔤 ชื่อสายพันธุ์ (ก-ฮ / A-Z)</option>
            </select>
        </div>

        <!-- 3. Price Range Filter -->
        <div class="form-group" style="margin-bottom: 1.1rem;">
            <label class="filter-dimension-title">
                💰 ช่วงราคา (Price Range):
            </label>
            <div class="filter-modal-pills-grid">
                <button type="button" class="cat-filter-btn cat-modal-pill price-pill active" data-price-range="all" onclick="selectPriceRange('all', this)">ทุกระดับราคา</button>
                <button type="button" class="cat-filter-btn cat-modal-pill price-pill" data-price-range="under_18k" onclick="selectPriceRange('under_18k', this)">ต่ำกว่า 18,000 ฿</button>
                <button type="button" class="cat-filter-btn cat-modal-pill price-pill" data-price-range="18k_28k" onclick="selectPriceRange('18k_28k', this)">18,000 - 28,000 ฿</button>
                <button type="button" class="cat-filter-btn cat-modal-pill price-pill" data-price-range="above_28k" onclick="selectPriceRange('above_28k', this)">มากกว่า 28,000 ฿</button>
            </div>
        </div>

        <!-- 4. Age Filter -->
        <div class="form-group" style="margin-bottom: 1.1rem;">
            <label class="filter-dimension-title">
                🎂 อายุของน้องแมว (Age):
            </label>
            <div class="filter-modal-pills-grid">
                <button type="button" class="cat-filter-btn cat-modal-pill age-pill active" data-age="all" onclick="selectAgeFilter('all', this)">ทุกช่วงอายุ</button>
                <button type="button" class="cat-filter-btn cat-modal-pill age-pill" data-age="baby" onclick="selectAgeFilter('baby', this)">ลูกแมว (2 - 3 เดือน)</button>
                <button type="button" class="cat-filter-btn cat-modal-pill age-pill" data-age="junior" onclick="selectAgeFilter('junior', this)">วัยกำลังซน (3.5 - 5 เดือน)</button>
                <button type="button" class="cat-filter-btn cat-modal-pill age-pill" data-age="adult" onclick="selectAgeFilter('adult', this)">วัยโต (6+ เดือน)</button>
            </div>
        </div>

        <!-- 5. Hair Type Filter -->
        <div class="form-group" style="margin-bottom: 1.1rem;">
            <label class="filter-dimension-title">
                🧶 ประเภทขน (Hair Type):
            </label>
            <div class="filter-modal-pills-grid">
                <button type="button" class="cat-filter-btn cat-modal-pill hair-pill active" data-hair="all" onclick="selectHairFilter('all', this)">ทุกประเภทขน</button>
                <button type="button" class="cat-filter-btn cat-modal-pill hair-pill" data-hair="short" onclick="selectHairFilter('short', this)">ขนสั้น (Short)</button>
                <button type="button" class="cat-filter-btn cat-modal-pill hair-pill" data-hair="long" onclick="selectHairFilter('long', this)">ขนยาวฟู (Long)</button>
                <button type="button" class="cat-filter-btn cat-modal-pill hair-pill" data-hair="hairless" onclick="selectHairFilter('hairless', this)">ไร้ขน (Sphynx)</button>
                <button type="button" class="cat-filter-btn cat-modal-pill hair-pill" data-hair="curly" onclick="selectHairFilter('curly', this)">ขนหยิก (Rex)</button>
            </div>
        </div>

        <!-- 6. Category Filter Pills -->
        <div class="form-group" style="margin-bottom: 0.5rem;">
            <label class="filter-dimension-title">
                🎯 หมวดหมู่ไลฟ์สไตล์ (Categories):
            </label>
            <div class="filter-modal-pills-grid">
                <?php foreach ($recommendation_categories as $cat_key => $cat_meta): ?>
                    <button type="button" 
                            class="cat-filter-btn cat-modal-pill category-pill <?php echo $selected_category === $cat_key ? 'active' : ''; ?>"
                            data-cat="<?php echo $cat_key; ?>"
                            onclick="selectCategoryInModal('<?php echo $cat_key; ?>', this)">
                        <?php echo $cat_meta['name']; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="filter-sheet-footer">
        <button type="button" class="btn btn-secondary" onclick="resetAllProductFilters()" style="flex: 1; justify-content: center; font-size: 0.88rem;">
            ล้างตัวกรอง 🔄
        </button>
        <button type="button" class="btn btn-primary" onclick="closeProductFilterModal()" style="flex: 2; justify-content: center; font-weight: 800; font-size: 0.92rem; background: #0284C7; border-color: #0284C7;">
            ดูผลลัพธ์ (<span id="modal-result-count"><?php echo count($cats); ?></span> ตัว) ✓
        </button>
    </div>
</div>

<script>
let currentProductCategory = '<?php echo $selected_category; ?>';
let currentPriceRange = 'all';
let currentAgeFilter = 'all';
let currentHairFilter = 'all';

function filterProductsPage(categoryKey, btnElement) {
    currentProductCategory = categoryKey;
    
    // Update desktop button states
    const buttons = document.querySelectorAll('.category-nav .cat-filter-btn');
    buttons.forEach(b => b.classList.remove('active'));
    if (btnElement) {
        btnElement.classList.add('active');
    }

    // Update modal button states
    const modalButtons = document.querySelectorAll('.category-pill');
    modalButtons.forEach(b => {
        if (b.getAttribute('data-cat') === categoryKey) {
            b.classList.add('active');
            const tagLabel = document.getElementById('mobile-filter-tag-label');
            if (tagLabel) {
                tagLabel.textContent = b.textContent.trim() + ' ⚡';
            }
        } else {
            b.classList.remove('active');
        }
    });

    window.history.pushState(null, '', 'products.php?cat=' + categoryKey);
    applyProductFilters();
}

function selectCategoryInModal(categoryKey, btnElement) {
    const desktopButtons = document.querySelectorAll('.category-nav .cat-filter-btn');
    let matchedDesktopBtn = null;
    desktopButtons.forEach(b => {
        if (b.textContent.trim() === btnElement.textContent.trim()) {
            matchedDesktopBtn = b;
        }
    });
    filterProductsPage(categoryKey, matchedDesktopBtn);
}

function selectPriceRange(rangeKey, btnElement) {
    currentPriceRange = rangeKey;
    document.querySelectorAll('.price-pill').forEach(b => b.classList.remove('active'));
    if (btnElement) btnElement.classList.add('active');
    applyProductFilters();
}

function selectAgeFilter(ageKey, btnElement) {
    currentAgeFilter = ageKey;
    document.querySelectorAll('.age-pill').forEach(b => b.classList.remove('active'));
    if (btnElement) btnElement.classList.add('active');
    applyProductFilters();
}

function selectHairFilter(hairKey, btnElement) {
    currentHairFilter = hairKey;
    document.querySelectorAll('.hair-pill').forEach(b => b.classList.remove('active'));
    if (btnElement) btnElement.classList.add('active');
    applyProductFilters();
}

function toggleProductFilterModal() {
    const modal = document.getElementById('product-filter-modal');
    if (modal && modal.classList.contains('open')) {
        closeProductFilterModal();
    } else {
        openProductFilterModal();
    }
}

function openProductFilterModal() {
    const modal = document.getElementById('product-filter-modal');
    const backdrop = document.getElementById('product-filter-backdrop');
    const btn = document.getElementById('mobile-floating-filter-btn');
    if (modal && backdrop) {
        modal.classList.add('open');
        backdrop.classList.add('open');
        document.body.style.overflow = 'hidden';
        if (btn) {
            btn.classList.add('active');
            const icon = btn.querySelector('.floating-filter-icon');
            if (icon) icon.textContent = '✕';
            const badge = btn.querySelector('.floating-filter-badge');
            if (badge) badge.textContent = 'ปิด';
        }
    }
}

function closeProductFilterModal() {
    const modal = document.getElementById('product-filter-modal');
    const backdrop = document.getElementById('product-filter-backdrop');
    const btn = document.getElementById('mobile-floating-filter-btn');
    if (modal && backdrop) {
        modal.classList.remove('open');
        backdrop.classList.remove('open');
        document.body.style.overflow = '';
        if (btn) {
            btn.classList.remove('active');
            const icon = btn.querySelector('.floating-filter-icon');
            if (icon) icon.textContent = '🧺';
            const badge = btn.querySelector('.floating-filter-badge');
            if (badge) badge.textContent = 'กรอง';
        }
    }
}

function syncSearchInputs(val) {
    const mainInput = document.getElementById('cat-search-input');
    const modalInput = document.getElementById('cat-search-input-modal');
    if (mainInput && mainInput.value !== val) mainInput.value = val;
    if (modalInput && modalInput.value !== val) modalInput.value = val;
}

function syncSortSelects(val) {
    const mainSelect = document.getElementById('cat-sort-select');
    const modalSelect = document.getElementById('cat-sort-select-modal');
    if (mainSelect && mainSelect.value !== val) mainSelect.value = val;
    if (modalSelect && modalSelect.value !== val) modalSelect.value = val;
}

function resetAllProductFilters() {
    syncSearchInputs('');
    syncSortSelects('default');
    
    currentPriceRange = 'all';
    document.querySelectorAll('.price-pill').forEach(b => b.classList.toggle('active', b.getAttribute('data-price-range') === 'all'));

    currentAgeFilter = 'all';
    document.querySelectorAll('.age-pill').forEach(b => b.classList.toggle('active', b.getAttribute('data-age') === 'all'));

    currentHairFilter = 'all';
    document.querySelectorAll('.hair-pill').forEach(b => b.classList.toggle('active', b.getAttribute('data-hair') === 'all'));

    const allBtn = document.querySelector('.category-nav .cat-filter-btn');
    filterProductsPage('all', allBtn);
    applyProductFilters();
}

function parseCatAgeMonths(ageStr) {
    if (!ageStr) return 3;
    const match = ageStr.match(/([0-9.]+)/);
    return match ? parseFloat(match[1]) : 3;
}

function applyProductFilters() {
    const mainInput = document.getElementById('cat-search-input');
    const searchVal = (mainInput ? mainInput.value : '').trim().toLowerCase();
    
    const mainSelect = document.getElementById('cat-sort-select');
    const sortVal = mainSelect ? mainSelect.value : 'default';
    
    const cards = Array.from(document.querySelectorAll('.product-item-card'));
    
    let visibleCount = 0;

    cards.forEach(card => {
        const categories = (card.getAttribute('data-categories') || '').split(',');
        const name = (card.getAttribute('data-name') || '').toLowerCase();
        const breed = (card.getAttribute('data-breed') || '').toLowerCase();
        const desc = (card.getAttribute('data-desc') || '').toLowerCase();
        const price = parseFloat(card.getAttribute('data-price')) || 0;
        const hair = (card.getAttribute('data-hair') || '').toLowerCase();
        const ageStr = (card.getAttribute('data-age') || card.querySelector('.cat-pill-tag') ? card.querySelector('.cat-pill-tag').textContent : '');
        const ageMonths = parseCatAgeMonths(ageStr);

        // 1. Category check
        const matchCategory = (currentProductCategory === 'all') || categories.includes(currentProductCategory);
        
        // 2. Search check
        const matchSearch = !searchVal || name.includes(searchVal) || breed.includes(searchVal) || desc.includes(searchVal);

        // 3. Price Range check
        let matchPrice = true;
        if (currentPriceRange === 'under_18k') {
            matchPrice = price < 18000;
        } else if (currentPriceRange === '18k_28k') {
            matchPrice = price >= 18000 && price <= 28000;
        } else if (currentPriceRange === 'above_28k') {
            matchPrice = price > 28000;
        }

        // 4. Age check
        let matchAge = true;
        if (currentAgeFilter === 'baby') {
            matchAge = ageMonths <= 3.2;
        } else if (currentAgeFilter === 'junior') {
            matchAge = ageMonths > 3.2 && ageMonths <= 5.5;
        } else if (currentAgeFilter === 'adult') {
            matchAge = ageMonths > 5.5;
        }

        // 5. Hair Type check
        let matchHair = true;
        if (currentHairFilter !== 'all') {
            matchHair = hair === currentHairFilter || (currentHairFilter === 'curly' && (hair === 'curly' || hair === 'rex'));
        }

        if (matchCategory && matchSearch && matchPrice && matchAge && matchHair) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const countEl = document.getElementById('product-count');
    if (countEl) countEl.innerText = visibleCount;

    const modalCountEl = document.getElementById('modal-result-count');
    if (modalCountEl) modalCountEl.innerText = visibleCount;

    // Sorting
    const grid = document.getElementById('products-catalog-grid');
    const visibleCards = cards.filter(c => c.style.display !== 'none');

    visibleCards.sort((a, b) => {
        const priceA = parseFloat(a.getAttribute('data-price')) || 0;
        const priceB = parseFloat(b.getAttribute('data-price')) || 0;
        const nameA = a.getAttribute('data-name') || '';
        const nameB = b.getAttribute('data-name') || '';

        if (sortVal === 'price_asc') {
            return priceA - priceB;
        } else if (sortVal === 'price_desc') {
            return priceB - priceA;
        } else if (sortVal === 'name_asc') {
            return nameA.localeCompare(nameB, 'th');
        }
        return 0; // default order
    });

    visibleCards.forEach(c => grid.appendChild(c));
}

// Initial apply
window.addEventListener('DOMContentLoaded', () => {
    if (currentProductCategory !== 'all') {
        applyProductFilters();
    }
});
</script>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
