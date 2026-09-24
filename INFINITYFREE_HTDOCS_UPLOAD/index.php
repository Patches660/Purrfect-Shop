<?php 
require_once __DIR__ . '/header.php'; 

// Handle Add to Cart from index
$added_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_cat') {
    $cat_id = $_POST['cat_id'] ?? '';
    if (addToCart($cat_id)) {
        $cat_name = $cats[$cat_id]['name'] ?? 'น้องแมว';
        $added_message = "🐾 เพิ่ม " . htmlspecialchars($cat_name) . " ลงในตะกร้าเรียบร้อยแล้ว!";
    }
}

// Check notification messages from query
$msg = $_GET['msg'] ?? '';
if ($msg === 'reg_success') {
    $added_message = "🎉 ยินดีต้อนรับสมาชิกใหม่! คุณได้รับสิทธิ์ส่วนลด 5% สำหรับทุกการจองน้องแมวแล้ว 🐾";
} elseif ($msg === 'login_success') {
    $added_message = "👋 ยินดีต้อนรับกลับมา! ระบบเปิดใช้งานส่วนลดสมาชิก 5% ให้คุณเรียบร้อยแล้ว";
} elseif ($msg === 'logged_out') {
    $added_message = "👋 ออกจากระบบเรียบร้อยแล้ว แล้วแวะมาหาน้องแมวใหม่อีกนะเหมียว!";
}
?>

<!-- Alert Notification -->
<?php if (!empty($added_message)): ?>
    <div class="alert-box alert-success" style="margin-bottom: 2rem; justify-content: space-between;">
        <span><?php echo $added_message; ?></span>
        <a href="cart.php" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.9rem; font-size: 0.8rem;">
            ไปที่ตะกร้า 🛒
        </a>
    </div>
<?php endif; ?>

<!-- Hero Section -->
<section class="hero-cat">
    <div>
        <div class="hero-badge-pill">
            <span>✨ อาณาจักรน้องแมวสายพันธุ์แท้ 100% เกรดพรีเมียม</span>
        </div>
        <h1 class="hero-title">
            ค้นหาเพื่อนรักสี่ขา <span>สุขภาพดี</span> อารมณ์ดี พร้อมย้ายบ้าน 🐾
        </h1>
        <p class="hero-desc">
            ยินดีต้อนรับสู่ <strong>Cat Shop</strong> ฟาร์มและศูนย์รวมน้องแมวสายพันธุ์แท้ เลี้ยงดูด้วยความรักในระบบปิดมาตรฐานสากล
            ตรวจสุขภาพและฉีดวัคซีนครบถ้วน พร้อมมอบความอบอุ่นให้กับบ้านใหม่ของคุณ
        </p>
        <div class="hero-actions">
            <a href="products.php" class="btn btn-primary btn-lg">
                ดูน้องแมวทั้งหมด 🐱
            </a>
            <a href="recommend.php" class="btn btn-secondary btn-lg">
                แนะนำสายพันธุ์ที่เหมาะกับคุณ 🌟
            </a>
        </div>
        <div class="hero-perks">
            <div class="hero-perk-item">
                <span>🩺 ตรวจสุขภาพ & วัคซีนครบ</span>
            </div>
            <div class="hero-perk-item">
                <span>📜 ใบเพ็ดดีกรีสายพันธุ์แท้</span>
            </div>
            <div class="hero-perk-item">
                <span>🚗 จัดส่งปลอดภัยติดแอร์</span>
            </div>
            <div class="hero-perk-item">
                <span>🛡️ รับประกันสุขภาพ 30 วัน</span>
            </div>
        </div>
    </div>

    <div class="hero-img-container">
        <img src="assets/images/cat_british.jpg" alt="British Shorthair Cat" class="hero-main-img">
        <div class="hero-floating-card">
            <span style="font-size: 1.8rem;">🐱</span>
            <div style="text-align: left;">
                <div style="font-size: 0.82rem; font-weight: 700; color: var(--primary-coral);">POPULAR BREED</div>
                <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">บริติช ช็อตแฮร์ หน้ากลม</div>
                <div style="font-size: 0.75rem; color: var(--text-muted);">วัคซีนครบ • พร้อมย้ายบ้าน</div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     RECOMMENDATION SYSTEM WITH CATEGORY SELECTION (ระบบแนะนำสินค้า)
     ========================================================= -->
<section id="recommendation" class="recommendation-wrapper">
    <div class="section-header">
        <span class="section-tag">✨ PERSONALIZED RECOMMENDATIONS</span>
        <h2 class="section-title">แนะนำสายพันธุ์น้องแมวที่เหมาะกับคุณ</h2>
        <p class="section-subtitle">
            คลิกเลือกหมวดหมู่ที่สนใจ หรือเจาะจงเฉพาะสายพันธุ์ ระบบจะคัดกรองแสดงเฉพาะน้องแมวที่เกี่ยวข้องให้ทันที 🐾
        </p>
    </div>

    <!-- 1. Category Selector Buttons -->
    <div style="margin-bottom: 1.5rem;">
        <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.6rem; text-align: center;">
            🎯 เลือกตามหมวดหมู่ไลฟ์สไตล์:
        </div>
        <div class="category-nav">
            <?php 
            $is_first = true;
            foreach ($recommendation_categories as $key => $cat_meta): 
            ?>
                <button type="button" 
                        class="cat-filter-btn <?php echo $is_first ? 'active' : ''; ?>" 
                        onclick="selectCategory('<?php echo $key; ?>', this)">
                    <?php echo $cat_meta['name']; ?>
                </button>
            <?php 
                $is_first = false;
            endforeach; 
            ?>
        </div>
    </div>

    <!-- 2. Specific Breed Selector Pills -->
    <div class="breed-selector-row">
        <span class="breed-selector-label">🐾 หรือเลือกเจาะจงเฉพาะสายพันธุ์:</span>
        <div class="breed-pills-wrap">
            <button type="button" class="breed-pill-btn active" onclick="selectSpecificBreed('all_in_cat', this)">
                ทั้งหมดในหมวดนี้
            </button>
            <?php foreach ($cats as $b_key => $b_cat): ?>
                <button type="button" class="breed-pill-btn" onclick="selectSpecificBreed('<?php echo $b_cat['id']; ?>', this)">
                    <?php echo $b_cat['breed']; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 3. Active Category Status Banner (No Bounce Links!) -->
    <div class="category-banner" id="category-banner">
        <div>
            <div class="category-banner-title" id="cat-banner-title">
                🔥 สายพันธุ์ยอดนิยมอันดับ 1
            </div>
            <div class="category-banner-desc" id="cat-banner-desc">
                น้องแมวสายพันธุ์ท็อปฮิตที่ได้รับความนิยมสูงสุด นิสัยน่ารัก เข้ากับทุกคนง่าย
            </div>
        </div>
        <div class="cat-filter-count-badge">
            แสดงเฉพาะหมวดนี้: <strong id="cat-banner-count">4</strong> ตัว
        </div>
    </div>

    <!-- 4. Dynamic Filtered Cats Grid (Shows ONLY relevant cats) -->
    <div class="cats-grid" id="recommended-cats-grid">
        <?php foreach ($cats as $key => $cat): ?>
            <div class="cat-card recommend-cat-item" 
                 data-id="<?php echo $cat['id']; ?>"
                 data-categories="<?php echo implode(',', $cat['categories']); ?>" 
                 data-hair="<?php echo $cat['hair_type']; ?>">
                <div class="cat-card-img-wrap">
                    <img src="assets/images/<?php echo $cat['image']; ?>" alt="<?php echo $cat['name']; ?>" class="cat-card-img">
                    <span class="cat-card-badge">✨ แนะนำพิเศษ</span>
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

                    <div class="cat-card-footer">
                        <div class="cat-price-box">
                            <span class="cat-price-label">ค่าสินสอด / รับเลี้ยง</span>
                            <span class="cat-price-val"><?php echo number_format($cat['price']); ?> ฿</span>
                        </div>
                        <form method="POST" action="index.php#recommendation">
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

    <!-- 5. Smart Cat Matcher Quiz Widget -->
    <div class="matcher-widget">
        <div class="matcher-header">
            <h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">
                🎯 Smart Cat Matcher: ค้นหาน้องแมวที่ตรงใจคุณ
            </h3>
            <p style="font-size: 0.92rem; color: var(--text-secondary);">
                ตอบคำถามสั้นๆ 3 ข้อ เพื่อให้ระบบช่วยประเมินสายพันธุ์น้องแมวที่เหมาะกับคุณที่สุด
            </p>
        </div>

        <div class="matcher-grid">
            <div class="matcher-group">
                <label for="q_home">1. สถานที่อยู่อาศัยของคุณ 🏡</label>
                <select id="q_home" class="matcher-select">
                    <option value="condo">คอนโดมิเนียม / หอพัก (พื้นที่จำกัด ต้องการความสงบ)</option>
                    <option value="house">บ้านเดี่ยว / ทาวน์โฮม (มีพื้นที่ วิ่งเล่นได้)</option>
                </select>
            </div>

            <div class="matcher-group">
                <label for="q_personality">2. นิสัยน้องแมวที่คุณชื่นชอบ 💖</label>
                <select id="q_personality" class="matcher-select">
                    <option value="calm">ขี้อ้อน เรียบร้อย รักความสงบ</option>
                    <option value="playful">ร่าเริง ซุกซน พลังงานสูง ชอบเล่น</option>
                    <option value="beginner">เลี้ยงง่าย สุขภาพแข็งแรง เหมาะกับมือใหม่</option>
                </select>
            </div>

            <div class="matcher-group">
                <label for="q_hair">3. การดูแลเรื่องขน 🧶</label>
                <select id="q_hair" class="matcher-select">
                    <option value="short">ขนสั้น ดูแลง่าย ขนไม่พันกัน</option>
                    <option value="long">ขนยาว ฟูนุ่ม เหมือนตุ๊กตา</option>
                    <option value="hairless">ไร้ขน / ขนร่วงน้อย (มีภูมิแพ้)</option>
                </select>
            </div>
        </div>

        <div class="matcher-btn-wrap">
            <button type="button" class="btn btn-primary" onclick="runCatMatcher()">
                ค้นหาน้องแมวที่แนะนำสำหรับฉัน 🔍🐾
            </button>
        </div>

        <!-- Match Result Display -->
        <div id="matcher-results" class="matcher-result-box">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <span class="section-tag">🎉 ผลการจับคู่ของคุณ</span>
                <h4 style="font-size: 1.3rem; font-weight: 700; color: var(--primary-coral);" id="match-title">
                    สายพันธุ์ที่ตรงใจคุณมากที่สุด
                </h4>
                <p style="font-size: 0.9rem; color: var(--text-muted);" id="match-score">ความเหมาะสม 98% สำหรับไลฟ์สไตล์ของคุณ</p>
            </div>
            <div class="cats-grid" id="matched-cats-container">
                <!-- Injected by JavaScript -->
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section style="margin-bottom: 4rem;">
    <div class="section-header">
        <span class="section-tag">🌟 OUR STANDARDS</span>
        <h2 class="section-title">ทำไมคนรักแมวจึงไว้วางใจ Cat Shop</h2>
        <p class="section-subtitle">มาตรฐานการเพาะพันธุ์และการดูแลน้องแมวที่ดีที่สุด เพื่อความสุขของทุกครอบครัว</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow-sm); text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🩺</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.6rem;">ตรวจสุขภาพและฉีดวัคซีน</h3>
            <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6;">
                น้องแมวทุกตัวผ่านการตรวจสุขภาพอย่างละเอียดจากสัตวแพทย์ชั้นนำ ฉีดวัคซีนรวม ถ่ายพยาธิ และมีสมุดประจำตัวพร้อมย้ายบ้าน
            </p>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow-sm); text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📜</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.6rem;">สายพันธุ์แท้ มีใบเพ็ดดีกรี</h3>
            <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6;">
                การันตีสายพันธุ์แท้ 100% จากสมาคมระดับสากล (WCF / CFA / TICA) พ่อแม่พันธุ์นำเข้าสายเลือดแชมป์ ปลอดภัยจากโรคทางพันธุกรรม
            </p>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow-sm); text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">💖</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.6rem;">เลี้ยงดูด้วยความรักในระบบปิด</h3>
            <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6;">
                เติบโตในห้องปรับอากาศปลอดเชื้อ ได้รับโภชนาการเกรดพรีเมียม เข้าสังคมง่าย อารมณ์ดี และผ่านการฝึกใช้กระบะทราย 100%
            </p>
        </div>
    </div>
</section>

<!-- Member Privilege Callout -->
<section style="background: var(--bg-card-subtle); border: 2px solid var(--border-hover); border-radius: var(--radius-lg); padding: 3rem 2rem; text-align: center; margin-bottom: 2rem;">
    <span class="section-tag" style="background: var(--bg-card);">🎁 สิทธิพิเศษสำหรับสมาชิกใหม่</span>
    <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.8rem; margin-top: 0.5rem;">
        สมัครสมาชิกวันนี้ รับส่วนลดทันที 5% ทุกตัว!
    </h2>
    <p style="font-size: 1.05rem; color: var(--text-secondary); max-width: 640px; margin: 0 auto 1.8rem auto;">
        พร้อมรับชุดของขวัญต้อนรับน้องแมว (Welcome Kitten Starter Kit) มูลค่า 2,500 บาท ฟรี! อาหารเกรดพรีเมียม ทรายแมว ของเล่น และคำปรึกษาจากสัตวแพทย์ตลอดชีพ
    </p>
    <?php if (!isUserLoggedIn()): ?>
        <a href="register.php" class="btn btn-primary btn-lg">
            สมัครสมาชิกเพื่อรับสิทธิพิเศษ 🐾
        </a>
    <?php else: ?>
        <span class="btn btn-secondary btn-lg" style="pointer-events: none; background: var(--bg-card); color: var(--accent-mint); font-weight: 700;">
            ✓ คุณเป็นสมาชิก Cat Shop แล้ว (รับส่วนลด 5% ในตะกร้า)
        </span>
    <?php endif; ?>
</section>

<!-- Pass PHP category metadata to JS -->
<script>
const categoryMeta = <?php echo json_encode($recommendation_categories, JSON_UNESCAPED_UNICODE); ?>;
const allCatsData = <?php echo json_encode($cats, JSON_UNESCAPED_UNICODE); ?>;

// Auto-run initial recommendation filter on page load (defaults to popular)
window.addEventListener('DOMContentLoaded', () => {
    selectCategory('popular');
});
</script>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
