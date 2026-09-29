<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/data.php';

// Get Cat ID from query
$cat_id = $_GET['id'] ?? 'cat_british';
if (!isset($cats[$cat_id])) {
    $cat_id = array_key_first($cats) ?? 'cat_british';
}
$current_cat = $cats[$cat_id];

// Load Reviews Data
$reviews_file = __DIR__ . '/data_reviews.json';
$all_reviews = file_exists($reviews_file) ? json_decode(file_get_contents($reviews_file), true) : [];

// Filter reviews for this specific cat breed
$breed_reviews = array_filter($all_reviews, function($r) use ($cat_id) {
    return ($r['breed_id'] ?? '') === $cat_id;
});
if (empty($breed_reviews)) {
    // If no specific reviews, show sample reviews
    $breed_reviews = array_slice($all_reviews, 0, 2);
}

// Microchip mapping for all 10 Breeds
$microchip_map = [
    'cat_british' => '900215001234567',
    'cat_persian' => '900215003344556',
    'cat_sphynx' => '900215009988776',
    'cat_siamese' => '900215004455667',
    'cat_mainecoon' => '900215008899112',
    'cat_ragdoll' => '900215007654321',
    'cat_bengal' => '900215006677889',
    'cat_scottish' => '900215007788990',
    'cat_munchkin' => '900215005566778',
    'cat_russian' => '900215009900112'
];
$current_chip = $microchip_map[$cat_id] ?? '900215001234567';

$page_title = htmlspecialchars($current_cat['name']) . ' - รายละเอียดน้องแมว | Purrfect Shop';
require_once __DIR__ . '/header.php';
?>

<main class="page-container" style="padding: 2rem 1rem 5rem; max-width: 1200px; margin: 0 auto;">
    
    <!-- Breadcrumb Navigation -->
    <nav style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--text-muted); margin-bottom: 2rem; flex-wrap: wrap;">
        <a href="index.php" style="color: inherit; text-decoration: none;">🏠 หน้าแรก</a>
        <span>/</span>
        <a href="products.php" style="color: inherit; text-decoration: none;">🐱 น้องแมวทั้งหมด</a>
        <span>/</span>
        <span style="color: var(--primary-coral); font-weight: 700;" id="breadcrumbCatName"><?php echo htmlspecialchars($current_cat['name']); ?></span>
    </nav>

    <!-- Main Product Detail Card -->
    <div style="background: var(--bg-card); border-radius: 28px; border: 1px solid var(--border-color); box-shadow: 0 12px 40px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 3.5rem;">
        <div style="display: grid; grid-template-columns: 1fr 1.15fr; gap: 2.5rem; padding: 2.5rem;" class="cat-detail-grid">
            
            <!-- Left: Gallery & Badges -->
            <div>
                <div style="position: relative; border-radius: 22px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.08); margin-bottom: 1.2rem; background: #F8FAFC;">
                    <img id="detailCatImg" src="assets/images/<?php echo $current_cat['image']; ?>" alt="<?php echo htmlspecialchars($current_cat['name']); ?>" style="width: 100%; height: 420px; object-fit: cover; display: block; transition: transform 0.3s ease;">
                    <span style="position: absolute; top: 16px; left: 16px; background: rgba(16, 185, 129, 0.92); color: #fff; font-weight: 800; font-size: 0.82rem; padding: 6px 14px; border-radius: 999px; backdrop-filter: blur(4px); box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                        ✨ สุขภาพสมบูรณ์ พร้อมย้ายบ้าน
                    </span>
                    <span style="position: absolute; bottom: 16px; right: 16px; background: rgba(0,0,0,0.65); color: #fff; font-weight: 700; font-size: 0.8rem; padding: 4px 12px; border-radius: 999px; backdrop-filter: blur(4px);">
                        📷 ภาพถ่ายจริงจากฟาร์ม
                    </span>
                </div>

                <!-- Trust Badges Row -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div style="background: rgba(255, 107, 74, 0.08); border-radius: 14px; padding: 0.9rem; text-align: center; border: 1px solid rgba(255, 107, 74, 0.2);">
                        <span style="font-size: 1.4rem;">📜</span>
                        <strong style="display: block; font-size: 0.85rem; color: var(--text-main); margin-top: 2px;">ใบเพ็ดดีกรีแท้</strong>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">WCF / CFA สากล</span>
                    </div>
                    <div style="background: rgba(16, 185, 129, 0.08); border-radius: 14px; padding: 0.9rem; text-align: center; border: 1px solid rgba(16, 185, 129, 0.2);">
                        <span style="font-size: 1.4rem;">🛡️</span>
                        <strong style="display: block; font-size: 0.85rem; color: var(--text-main); margin-top: 2px;">การันตีสุขภาพ 180 วัน</strong>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">สัตวแพทย์ดูแลตลอด</span>
                    </div>
                </div>
            </div>

            <!-- Right: Details, Attributes & Action Buttons -->
            <div style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <!-- Breed & Title -->
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.6rem; flex-wrap: wrap;">
                        <span id="detailCatBreed" style="background: var(--primary-coral-soft, #FFF1EE); color: var(--primary-coral); font-weight: 800; font-size: 0.85rem; padding: 4px 12px; border-radius: 999px;">
                            <?php echo htmlspecialchars($current_cat['breed']); ?>
                        </span>
                        <span style="color: #F59E0B; font-weight: 800; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 4px;">
                            ⭐⭐⭐⭐⭐ 5.0 (รีวิวลูกค้า)
                        </span>
                    </div>

                    <h1 id="detailCatTitle" style="font-size: 2.2rem; font-weight: 900; color: var(--text-main); margin-bottom: 0.8rem; line-height: 1.25;">
                        <?php echo htmlspecialchars($current_cat['name']); ?>
                    </h1>

                    <p id="detailCatDesc" style="font-size: 1rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 1.5rem;">
                        <?php echo htmlspecialchars($current_cat['description']); ?>
                    </p>

                    <!-- Price Box -->
                    <div style="background: linear-gradient(135deg, rgba(255, 107, 74, 0.1) 0%, rgba(254, 243, 199, 0.4) 100%); border-radius: 18px; padding: 1.2rem 1.6rem; margin-bottom: 1.8rem; display: flex; align-items: center; justify-content: space-between; border: 1.5px solid rgba(255, 107, 74, 0.25); flex-wrap: wrap; gap: 10px;">
                        <div>
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 700; display: block;">ค่าสินสอด / รับเลี้ยงดู</span>
                            <span id="detailCatPrice" style="font-size: 2.2rem; font-weight: 900; color: var(--primary-coral); line-height: 1.1;">
                                <?php echo number_format($current_cat['price']); ?> ฿
                            </span>
                        </div>
                        <div style="text-align: right;">
                            <span style="background: #10B981; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 3px 10px; border-radius: 999px;">สมาชิกใหม่ลด 5%</span>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 4px;">ผ่อน 0% สูงสุด 10 เดือน</div>
                        </div>
                    </div>

                    <!-- Specifications Table Grid -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.9rem; margin-bottom: 2rem;">
                        <div style="background: var(--bg-page); border-radius: 12px; padding: 0.75rem 1rem; border: 1px solid var(--border-color);">
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">🎂 อายุของน้อง</span>
                            <strong id="detailCatAge" style="font-size: 0.95rem; color: var(--text-main);"><?php echo htmlspecialchars($current_cat['age']); ?></strong>
                        </div>
                        <div style="background: var(--bg-page); border-radius: 12px; padding: 0.75rem 1rem; border: 1px solid var(--border-color);">
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">⚥ เพศ</span>
                            <strong id="detailCatGender" style="font-size: 0.95rem; color: var(--text-main);"><?php echo htmlspecialchars($current_cat['gender']); ?></strong>
                        </div>
                        <div style="background: var(--bg-page); border-radius: 12px; padding: 0.75rem 1rem; border: 1px solid var(--border-color);">
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">🧶 ลักษณะขน</span>
                            <strong id="detailCatHair" style="font-size: 0.95rem; color: var(--text-main);"><?php echo htmlspecialchars($current_cat['hair_label']); ?></strong>
                        </div>
                        <div style="background: var(--bg-page); border-radius: 12px; padding: 0.75rem 1rem; border: 1px solid var(--border-color);">
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">💖 นิสัยเด่น</span>
                            <strong id="detailCatPersonality" style="font-size: 0.95rem; color: var(--text-main);"><?php echo htmlspecialchars($current_cat['personality']); ?></strong>
                        </div>
                        <div style="grid-column: span 2; background: var(--bg-page); border-radius: 12px; padding: 0.75rem 1rem; border: 1px solid var(--border-color);">
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">🩺 ประวัติวัคซีน & สุขภาพ</span>
                            <strong id="detailCatVaccine" style="font-size: 0.92rem; color: #059669;"><?php echo htmlspecialchars($current_cat['vaccine']); ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Call to Action Buttons -->
                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    <div class="detail-cta-row" style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 0.8rem;">
                        <!-- Add to Cart Form -->
                        <form method="POST" action="cart.php" id="add-to-cart-form" onsubmit="handleAddToCartDetail(event)" style="margin: 0;">
                            <input type="hidden" name="action" value="add_cat">
                            <input type="hidden" name="cat_id" id="detailFormCatId" value="<?php echo $cat_id; ?>">
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: 800; border-radius: 16px; box-shadow: 0 8px 20px rgba(255, 107, 74, 0.35); cursor: pointer;">
                                🛒 รับเลี้ยงน้องแมว 🐾
                            </button>
                        </form>

                        <!-- Booking Button -->
                        <a id="detailBookingLink" href="booking.php?breed=<?php echo urlencode($current_cat['name']); ?>" class="btn btn-secondary" style="padding: 1rem; font-size: 0.95rem; font-weight: 800; border-radius: 16px; text-align: center; text-decoration: none; justify-content: center;">
                            📅 จองดูตัว / Video Call
                        </a>
                    </div>

                    <!-- Digital Pedigree Link -->
                    <a id="detailPedigreeLink" href="pedigree.php?chip=<?php echo $current_chip; ?>" style="display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.88rem; color: var(--primary-coral); font-weight: 700; text-decoration: none; padding: 0.75rem; border-radius: 12px; background: rgba(255, 107, 74, 0.06); border: 1px dashed var(--primary-coral); text-align: center; line-height: 1.4;">
                        <span>🩺 ตรวจสอบใบเพ็ดดีกรี & สมุดวัคซีนของน้องตัวนี้ (Microchip) ➔</span>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- =========================================================
         SECTION: REVIEWS & STORIES FOR THIS CAT BREED (ย้ายรีวิวเข้ามาไว้ตรงนี้)
         ========================================================= -->
    <section style="margin-bottom: 4rem;">
        <div class="section-header" style="text-align: left; margin-bottom: 2rem;">
            <span class="section-tag">⭐ HAPPY STORIES & REVIEWS</span>
            <h2 class="section-title" id="reviewsSectionTitle">
                รีวิวความสุขจากครอบครัวที่รับเลี้ยง <?php echo htmlspecialchars($current_cat['breed']); ?> 🐾
            </h2>
            <p class="section-subtitle" style="margin-left: 0;">
                ภาพความประทับใจจริงและพัฒนาการของน้องแมวหลังย้ายเข้าบ้านใหม่อย่างอบอุ่น
            </p>
        </div>

        <div class="reviews-grid-container" id="breedReviewsContainer" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
            <?php foreach ($breed_reviews as $rev): ?>
                <div class="review-card" style="background: var(--bg-card); border-radius: 24px; padding: 1.5rem; border: 1px solid var(--border-color); box-shadow: 0 8px 24px rgba(0,0,0,0.04); display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <!-- Author & Rating Header -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #FF6B4A, #FFA07A); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.1rem;">
                                    <?php echo mb_substr($rev['author'] ?? 'ท', 0, 1, 'UTF-8'); ?>
                                </div>
                                <div>
                                    <strong style="display: block; font-size: 0.95rem; color: var(--text-main);"><?php echo htmlspecialchars($rev['author']); ?></strong>
                                    <span style="font-size: 0.78rem; color: var(--text-muted);">📍 <?php echo htmlspecialchars($rev['location']); ?> • <?php echo htmlspecialchars($rev['adoption_duration'] ?? 'รับเลี้ยงแล้ว'); ?></span>
                                </div>
                            </div>
                            <span style="background: #FEF3C7; color: #92400E; font-size: 0.8rem; font-weight: 800; padding: 3px 8px; border-radius: 999px;">
                                ⭐ <?php echo number_format($rev['rating'] ?? 5, 1); ?>
                            </span>
                        </div>

                        <!-- Review Image at Home -->
                        <?php if (!empty($rev['image_at_home'])): ?>
                            <div style="border-radius: 16px; overflow: hidden; margin-bottom: 1rem; max-height: 200px;">
                                <img src="<?php echo htmlspecialchars($rev['image_at_home']); ?>" alt="น้องแมวในบ้านใหม่" style="width: 100%; height: 200px; object-fit: cover;">
                            </div>
                        <?php endif; ?>

                        <!-- Comment Text -->
                        <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 1rem; font-style: italic;">
                            "<?php echo htmlspecialchars($rev['comment']); ?>"
                        </p>
                    </div>

                    <!-- Footer / Verified Badge -->
                    <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px dashed var(--border-color); padding-top: 0.8rem; font-size: 0.78rem; color: var(--text-muted);">
                        <span style="color: #10B981; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                            ✓ ผู้รับเลี้ยงจริง (Verified Adopter)
                        </span>
                        <span>📅 <?php echo htmlspecialchars($rev['date'] ?? '2026-09'); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Other Recommended Cats -->
    <section>
        <div class="section-header" style="text-align: left; margin-bottom: 2rem;">
            <span class="section-tag">🐾 MORE ADORABLE CATS</span>
            <h2 class="section-title">น้องแมวสายพันธุ์อื่นๆ ที่น่ารักไม่แพ้กัน</h2>
        </div>

        <div class="cats-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
            <?php 
            $count = 0;
            foreach ($cats as $k => $other_cat): 
                if ($k === $cat_id) continue;
                if ($count >= 4) break;
                $count++;
            ?>
                <a href="cat_detail.php?id=<?php echo $k; ?>" style="text-decoration: none; color: inherit;" class="cat-card">
                    <div class="cat-card-img-wrap">
                        <img src="assets/images/<?php echo $other_cat['image']; ?>" alt="<?php echo htmlspecialchars($other_cat['name']); ?>" class="cat-card-img">
                        <span class="cat-card-gender"><?php echo $other_cat['gender']; ?></span>
                    </div>
                    <div class="cat-card-body" style="padding: 1.2rem;">
                        <div class="cat-card-breed"><?php echo $other_cat['breed']; ?></div>
                        <h4 class="cat-card-name" style="font-size: 1.1rem; margin-bottom: 0.4rem;"><?php echo $other_cat['name']; ?></h4>
                        <div class="cat-card-footer" style="margin-top: 0.8rem;">
                            <span class="cat-price-val" style="font-size: 1.15rem; color: var(--primary-coral);"><?php echo number_format($other_cat['price']); ?> ฿</span>
                            <span style="font-size: 0.82rem; color: var(--primary-coral); font-weight: 800;">ดูรายละเอียด ➔</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

</main>

<style>
@media (max-width: 900px) {
    .cat-detail-grid {
        grid-template-columns: 1fr !important;
        gap: 1.8rem !important;
        padding: 1.5rem 1.2rem !important;
    }
}
@media (max-width: 600px) {
    #detailCatImg {
        height: 280px !important;
    }
    #detailCatTitle {
        font-size: 1.6rem !important;
    }
    .detail-cta-row {
        grid-template-columns: 1fr !important;
    }
}
</style>

<!-- Client-side JS to handle URL parameters dynamically on static exports -->
<script>
const allCats = <?php echo json_encode($cats, JSON_UNESCAPED_UNICODE); ?>;
const allReviewsData = <?php echo json_encode($all_reviews, JSON_UNESCAPED_UNICODE); ?>;
const microchipMap = <?php echo json_encode($microchip_map, JSON_UNESCAPED_UNICODE); ?>;

(function initStaticCatDetail() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const targetId = urlParams.get('id');
        if (targetId && allCats[targetId]) {
            renderCatDetailsClient(targetId);
        }
    } catch(e) {}
})();

function renderCatDetailsClient(id) {
    const cat = allCats[id];
    if (!cat) return;

    document.title = `${cat.name} - รายละเอียดน้องแมว | Purrfect Shop`;
    const bc = document.getElementById('breadcrumbCatName');
    if (bc) bc.textContent = cat.name;

    const img = document.getElementById('detailCatImg');
    if (img) {
        img.src = `assets/images/${cat.image}`;
        img.alt = cat.name;
    }

    const breed = document.getElementById('detailCatBreed');
    if (breed) breed.textContent = cat.breed;

    const title = document.getElementById('detailCatTitle');
    if (title) title.textContent = cat.name;

    const desc = document.getElementById('detailCatDesc');
    if (desc) desc.textContent = cat.description;

    const price = document.getElementById('detailCatPrice');
    if (price) price.textContent = new Intl.NumberFormat().format(cat.price) + " ฿";

    const age = document.getElementById('detailCatAge');
    if (age) age.textContent = cat.age;

    const gender = document.getElementById('detailCatGender');
    if (gender) gender.textContent = cat.gender;

    const hair = document.getElementById('detailCatHair');
    if (hair) hair.textContent = cat.hair_label;

    const pers = document.getElementById('detailCatPersonality');
    if (pers) pers.textContent = cat.personality;

    const vac = document.getElementById('detailCatVaccine');
    if (vac) vac.textContent = cat.vaccine;

    const formCatId = document.getElementById('detailFormCatId');
    if (formCatId) formCatId.value = cat.id;

    const isStatic = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');

    const bookLink = document.getElementById('detailBookingLink');
    if (bookLink) {
        bookLink.href = isStatic 
            ? `booking.html?breed=${encodeURIComponent(cat.name)}`
            : `booking.php?breed=${encodeURIComponent(cat.name)}`;
    }

    const chip = microchipMap[cat.id] || '900215001234567';
    const pedLink = document.getElementById('detailPedigreeLink');
    if (pedLink) {
        pedLink.href = isStatic
            ? `pedigree.html?chip=${chip}`
            : `pedigree.php?chip=${chip}`;
    }

    const revTitle = document.getElementById('reviewsSectionTitle');
    if (revTitle) revTitle.textContent = `รีวิวความสุขจากครอบครัวที่รับเลี้ยง ${cat.breed} 🐾`;

    // Render Filtered Reviews
    const matchedRev = allReviewsData.filter(r => r.breed_id === cat.id);
    const revToRender = matchedRev.length > 0 ? matchedRev : allReviewsData.slice(0, 2);
    const container = document.getElementById('breedReviewsContainer');
    if (container && revToRender.length > 0) {
        container.innerHTML = revToRender.map(r => `
            <div class="review-card" style="background: var(--bg-card); border-radius: 24px; padding: 1.5rem; border: 1px solid var(--border-color); box-shadow: 0 8px 24px rgba(0,0,0,0.04); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #FF6B4A, #FFA07A); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.1rem;">
                                ${(r.author || 'ท').substring(0, 1)}
                            </div>
                            <div>
                                <strong style="display: block; font-size: 0.95rem; color: var(--text-main);">${r.author}</strong>
                                <span style="font-size: 0.78rem; color: var(--text-muted);">📍 ${r.location} • ${r.adoption_duration || 'รับเลี้ยงแล้ว'}</span>
                            </div>
                        </div>
                        <span style="background: #FEF3C7; color: #92400E; font-size: 0.8rem; font-weight: 800; padding: 3px 8px; border-radius: 999px;">
                            ⭐ ${(r.rating || 5).toFixed(1)}
                        </span>
                    </div>
                    ${r.image_at_home ? `
                        <div style="border-radius: 16px; overflow: hidden; margin-bottom: 1rem; max-height: 200px;">
                            <img src="${r.image_at_home}" alt="น้องแมวในบ้านใหม่" style="width: 100%; height: 200px; object-fit: cover;">
                        </div>
                    ` : ''}
                    <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 1rem; font-style: italic;">
                        "${r.comment}"
                    </p>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px dashed var(--border-color); padding-top: 0.8rem; font-size: 0.78rem; color: var(--text-muted);">
                    <span style="color: #10B981; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                        ✓ ผู้รับเลี้ยงจริง (Verified Adopter)
                    </span>
                    <span>📅 ${r.date || '2026-09'}</span>
                </div>
            </div>
        `).join('');
    }
}

// Client-side Add to Cart handler
function handleAddToCartDetail(e) {
    const catId = document.getElementById('detailFormCatId')?.value || 'cat_british';
    const cat = allCats[catId];
    
    // 1. Always update localStorage cart
    if (cat) {
        let cart = [];
        try {
            cart = JSON.parse(localStorage.getItem('cat_shop_cart') || '[]');
        } catch(err) { cart = []; }

        const existing = cart.find(item => item.id === catId);
        if (existing) {
            existing.qty = (existing.qty || 1) + 1;
        } else {
            cart.push({
                id: cat.id,
                name: cat.name,
                breed: cat.breed,
                price: cat.price,
                image: cat.image,
                gender: cat.gender,
                age: cat.age,
                qty: 1
            });
        }

        try {
            localStorage.setItem('cat_shop_cart', JSON.stringify(cart));
        } catch(err) {}
    }

    // 2. If static HTML environment, navigate to cart.html
    const isStatic = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');
    if (isStatic) {
        if (e) e.preventDefault();
        window.location.href = `cart.html?add=${encodeURIComponent(catId)}`;
    }
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
