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

<!-- Product Count Bar -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
    <div>
        แสดงผลน้องแมว: <strong id="product-count" style="color: var(--primary-coral);"><?php echo count($cats); ?></strong> ตัว
    </div>
    <div style="font-size: 0.82rem;">
        🩺 ตรวจสุขภาพแล้วทุกตัว • 🚗 รับประกันการจัดส่งปลอดภัย
    </div>
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
             data-hair="<?php echo $cat['hair_type']; ?>">
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

<script>
let currentProductCategory = '<?php echo $selected_category; ?>';

function filterProductsPage(categoryKey, btnElement) {
    currentProductCategory = categoryKey;
    
    // Update button states
    const buttons = document.querySelectorAll('.category-nav .cat-filter-btn');
    buttons.forEach(b => b.classList.remove('active'));
    if (btnElement) {
        btnElement.classList.add('active');
    }

    // Update URL without reload
    window.history.pushState(null, '', 'products.php?cat=' + categoryKey);

    applyProductFilters();
}

function applyProductFilters() {
    const searchVal = (document.getElementById('cat-search-input').value || '').trim().toLowerCase();
    const sortVal = document.getElementById('cat-sort-select').value;
    const cards = Array.from(document.querySelectorAll('.product-item-card'));
    
    let visibleCount = 0;

    cards.forEach(card => {
        const categories = card.getAttribute('data-categories').split(',');
        const name = (card.getAttribute('data-name') || '').toLowerCase();
        const breed = (card.getAttribute('data-breed') || '').toLowerCase();
        const desc = (card.getAttribute('data-desc') || '').toLowerCase();
        
        // Category check
        const matchCategory = (currentProductCategory === 'all') || categories.includes(currentProductCategory);
        
        // Search check
        const matchSearch = !searchVal || name.includes(searchVal) || breed.includes(searchVal) || desc.includes(searchVal);

        if (matchCategory && matchSearch) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    document.getElementById('product-count').innerText = visibleCount;

    // Sorting
    const grid = document.getElementById('products-catalog-grid');
    const visibleCards = cards.filter(c => c.style.display !== 'none');

    visibleCards.sort((a, b) => {
        const priceA = parseFloat(a.getAttribute('data-price')) || 0;
        const priceB = parseFloat(b.getAttribute('data-price')) || 0;
        const nameA = a.getAttribute('data-name');
        const nameB = b.getAttribute('data-name');

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
