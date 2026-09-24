<?php
require_once __DIR__ . '/data.php';

// Handle Add to Cart action
$added_message = "";
if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_cat') {
    $cat_id = $_POST['cat_id'] ?? '';
    if (addToCart($cat_id)) {
        $cat_name = $cats[$cat_id]['name'] ?? 'น้องแมว';
        $added_message = "🐾 เพิ่ม " . htmlspecialchars($cat_name) . " ลงในตะกร้าเรียบร้อยแล้ว!";
    }
}

// Check initial category from query string, default to 'popular'
$initial_cat = $_GET['cat'] ?? 'popular';
if (!isset($recommendation_categories[$initial_cat])) {
    $initial_cat = 'popular';
}

require_once __DIR__ . '/header.php';
?>

<!-- Added Alert Banner -->
<?php if (!empty($added_message)): ?>
    <div class="alert-box alert-success" style="margin-bottom: 2rem; justify-content: space-between; max-width: 1100px; margin-left: auto; margin-right: auto;">
        <span><?php echo $added_message; ?></span>
        <a href="cart.php" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.9rem; font-size: 0.8rem;">
            ดูตะกร้าสินค้า 🛒
        </a>
    </div>
<?php endif; ?>

<!-- Section Header -->
<div class="section-header">
    <span class="section-tag">🌟 PERSONALIZED RECOMMENDATIONS</span>
    <h1 class="section-title">แนะนำสายพันธุ์น้องแมวสำหรับคุณ</h1>
    <p class="section-subtitle">
        ค้นหาน้องแมวคู่หูที่ตอบโจทย์ชีวิตของคุณมากที่สุด คัดกรองตามแบบประเมิน 6 มิติ หรือเลือกชมตามหมวดหมู่ไลฟ์สไตล์ 🐾
    </p>
</div>

<div class="recommendation-wrapper" style="max-width: 1150px; margin: 0 auto; padding: 0 15px;">

    <!-- =========================================================
         1. SMART CAT MATCHER 6-QUESTION QUIZ WIDGET
         ========================================================= -->
    <div class="matcher-widget" style="background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-card-subtle) 100%); border: 2px solid var(--primary-coral); border-radius: var(--radius-lg); padding: 2rem 2.2rem; box-shadow: var(--shadow-md); margin-bottom: 3.5rem;">
        <div class="matcher-header" style="text-align: center; margin-bottom: 2rem;">
            <span style="background: var(--primary-coral-soft); color: var(--primary-coral); font-size: 0.82rem; font-weight: 800; padding: 0.3rem 0.9rem; border-radius: 9999px; display: inline-block; margin-bottom: 0.6rem; border: 1px solid rgba(255, 117, 86, 0.3);">
                🧬 6-DIMENSION SMART CAT MATCHER
            </span>
            <h2 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.4rem;">
                🎯 แบบประเมินค้นหาน้องแมวที่ตรงใจ (6 คำถาม)
            </h2>
            <p style="font-size: 0.95rem; color: var(--text-secondary); max-width: 680px; margin: 0 auto;">
                ตอบคำถาม 6 ข้อเกี่ยวกับที่อยู่อาศัย เวลาว่าง ประสบการณ์ การดูแลขน นิสัย และงบประมาณ ระบบจะประมวลผลจับคู่กับน้องแมวทั้ง 32 สายพันธุ์แบบเรียลไทม์!
            </p>
        </div>

        <form id="catMatcherForm" onsubmit="runComprehensiveCatMatcher(event)">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.4rem; margin-bottom: 2rem;">
                
                <!-- Q1: ที่อยู่อาศัย -->
                <div class="matcher-group" style="background: var(--bg-card); padding: 1.2rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <label for="q_home" style="display: block; font-weight: 700; font-size: 0.95rem; color: var(--text-main); margin-bottom: 0.6rem;">
                        🏡 1. สถานที่อยู่อาศัยของคุณเป็นแบบไหน?
                    </label>
                    <select id="q_home" class="matcher-select" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card-subtle); color: var(--text-main); font-size: 0.9rem;">
                        <option value="condo">🏢 คอนโด / อพาร์ตเมนต์ (พื้นที่จำกัด ต้องการแมวเงียบสงบ)</option>
                        <option value="townhouse">🏘️ ทาวน์โฮม / บ้านแฝด (มีพื้นที่ปานกลาง แมวเล่นสนุกได้)</option>
                        <option value="house">🏡 บ้านเดี่ยวมีบริเวณ / มีสวน (พื้นที่กว้างขวาง สายพันธุ์แอคทีฟ)</option>
                    </select>
                </div>

                <!-- Q2: เวลาว่างต่อวัน -->
                <div class="matcher-group" style="background: var(--bg-card); padding: 1.2rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <label for="q_time" style="display: block; font-weight: 700; font-size: 0.95rem; color: var(--text-main); margin-bottom: 0.6rem;">
                        ⏰ 2. เวลาว่างในการอยู่กับน้องแมวต่อวัน?
                    </label>
                    <select id="q_time" class="matcher-select" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card-subtle); color: var(--text-main); font-size: 0.9rem;">
                        <option value="limited">💼 มีเวลาจำกัด (1-3 ชม./วัน ทำงานนอกบ้าน แมวดูแลตัวเองได้)</option>
                        <option value="medium" selected>⏳ มีเวลาปานกลาง (3-6 ชม./วัน ว่างช่วงเช้า-เย็นและวันหยุด)</option>
                        <option value="high">🛋️ มีเวลาเยอะมาก / Work From Home (อยู่บ้านทั้งวัน ต้องการแมวขี้อ้อน)</option>
                    </select>
                </div>

                <!-- Q3: ประสบการณ์การเลี้ยง -->
                <div class="matcher-group" style="background: var(--bg-card); padding: 1.2rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <label for="q_exp" style="display: block; font-weight: 700; font-size: 0.95rem; color: var(--text-main); margin-bottom: 0.6rem;">
                        🔰 3. ประสบการณ์ในการเลี้ยงน้องแมว?
                    </label>
                    <select id="q_exp" class="matcher-select" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card-subtle); color: var(--text-main); font-size: 0.9rem;">
                        <option value="beginner">🌱 มือใหม่ป้ายแดง เพิ่งเริ่มเลี้ยง (ต้องการแมวเลี้ยงง่าย ปรับตัวเก่ง)</option>
                        <option value="intermediate">🐾 พอมีประสบการณ์ (เคยเลี้ยงแมวหรือสัตว์เลี้ยงมาก่อน)</option>
                        <option value="expert">👑 ทาสแมวระดับโปร (เชี่ยวชาญ รับมือสายพันธุ์พิเศษได้)</option>
                    </select>
                </div>

                <!-- Q4: การดูแลเส้นขน & ภูมิแพ้ -->
                <div class="matcher-group" style="background: var(--bg-card); padding: 1.2rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <label for="q_hair" style="display: block; font-weight: 700; font-size: 0.95rem; color: var(--text-main); margin-bottom: 0.6rem;">
                        🧶 4. ความพร้อมเรื่องการดูแลขน & อาการภูมิแพ้?
                    </label>
                    <select id="q_hair" class="matcher-select" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card-subtle); color: var(--text-main); font-size: 0.9rem;">
                        <option value="short">✨ ขนสั้น ดูแลง่าย ขนร่วงน้อย แปรงขนอาทิตย์ละครั้ง</option>
                        <option value="long">☁️ ชอบแมวขนยาว ฟูนุ่ม เหมือนตุ๊กตา (พร้อมแปรงขนทุกวัน)</option>
                        <option value="medium">🦁 ขนปานกลาง / กึ่งยาว นุ่มแน่น กรูมมิ่งสบายๆ</option>
                        <option value="hypoallergenic">🌿 มีคนเป็นภูมิแพ้ / กังวลเรื่องขนร่วง (ไร้ขน หรือพันธุ์ขนร่วงน้อย)</option>
                    </select>
                </div>

                <!-- Q5: บุคลิกนิสัยที่ต้องการ -->
                <div class="matcher-group" style="background: var(--bg-card); padding: 1.2rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <label for="q_personality" style="display: block; font-weight: 700; font-size: 0.95rem; color: var(--text-main); margin-bottom: 0.6rem;">
                        💖 5. ลักษณะนิสัยของน้องแมวที่คุณใฝ่ฝัน?
                    </label>
                    <select id="q_personality" class="matcher-select" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card-subtle); color: var(--text-main); font-size: 0.9rem;">
                        <option value="cuddly">🥰 ขี้อ้อนสุดใจ ติดเจ้าของ เดินตามเหมือนเงา ชอบให้อุ้ม</option>
                        <option value="calm">🍵 สุภาพ เรียบร้อย สงบนิ่ง เป็นตัวของตัวเอง ไม่ส่งเสียงดัง</option>
                        <option value="playful">🎾 ร่าเริง ซุกซน พลังงานสูง ชวนเล่นเกม คาบของเล่น</option>
                        <option value="smart">🧠 ฉลาด ช่างสังเกต ช่างพูด ช่างคุย สื่อสารเก่ง</option>
                    </select>
                </div>

                <!-- Q6: งบประมาณ -->
                <div class="matcher-group" style="background: var(--bg-card); padding: 1.2rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <label for="q_budget" style="display: block; font-weight: 700; font-size: 0.95rem; color: var(--text-main); margin-bottom: 0.6rem;">
                        💰 6. งบประมาณที่ตั้งไว้สำหรับการรับเลี้ยง?
                    </label>
                    <select id="q_budget" class="matcher-select" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card-subtle); color: var(--text-main); font-size: 0.9rem;">
                        <option value="any">💎 ไม่จำกัดงบประมาณ (เน้นสายพันธุ์ที่เหมาะสมที่สุด)</option>
                        <option value="eco">🪙 สบายกระเป๋า (ไม่เกิน 20,000 บาท)</option>
                        <option value="mid">💳 มาตรฐานกำลังดี (20,000 - 30,000 บาท)</option>
                        <option value="prem">👑 พรีเมียม / ประกวด (มากกว่า 30,000 บาท ขึ้นไป)</option>
                    </select>
                </div>

            </div>

            <div style="text-align: center;">
                <button type="submit" class="btn btn-primary matcher-submit-btn">
                    <span class="btn-text-desktop">🎯 ประมวลผลค้นหาน้องแมวที่ใช่สำหรับฉัน (6 มิติ) 🐾</span>
                    <span class="btn-text-mobile">🎯 ค้นหาน้องแมวที่ใช่สำหรับคุณ 🐾</span>
                </button>
            </div>
        </form>

        <!-- Match Results Section (Hidden until submitted) -->
        <div id="matcher-results" style="display: none; margin-top: 2.5rem; padding-top: 2rem; border-top: 2px dashed var(--primary-coral);">
            <div style="text-align: center; margin-bottom: 2rem;">
                <span class="section-tag" style="background: #10B981; color: #FFFFFF;">🎉 ผลการประมวลผลจับคู่ 6 มิติ</span>
                <h3 style="font-size: 1.6rem; font-weight: 800; color: var(--primary-coral); margin: 0.4rem 0;" id="match-title">
                    สายพันธุ์ที่ตรงใจคุณมากที่สุด
                </h3>
                <p style="font-size: 0.95rem; color: var(--text-secondary);" id="match-score">
                    ระบบได้วิเคราะห์สายพันธุ์ทั้งหมดเทียบกับไลฟ์สไตล์ของคุณเรียบร้อยแล้ว
                </p>
            </div>

            <div class="cats-grid" id="matched-cats-container">
                <!-- Dynamically populated by JS -->
            </div>
        </div>
    </div>


    <!-- =========================================================
         2. MANUAL CATEGORY TAB SELECTOR & BROWSER
         ========================================================= -->
    <div style="border-top: 2px solid var(--border-color); padding-top: 2.5rem; margin-bottom: 2rem;">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <span class="section-tag">🐾 BROWSE BY CATEGORY</span>
            <h2 style="font-size: 1.45rem; font-weight: 800; color: var(--text-main);">
                หรือเลือกชมน้องแมวตามหมวดหมู่ไลฟ์สไตล์
            </h2>
        </div>

        <!-- Category Nav -->
        <div class="category-nav" style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; margin-bottom: 1.5rem;">
            <?php foreach ($recommendation_categories as $key => $cat_meta): ?>
                <button type="button" 
                        class="cat-filter-btn <?php echo $key === $initial_cat ? 'active' : ''; ?>" 
                        onclick="selectCategory('<?php echo $key; ?>', this)">
                    <?php echo $cat_meta['name']; ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Specific Breed Selector Pills -->
        <div class="breed-selector-row" style="margin-bottom: 1.5rem;">
            <span class="breed-selector-label" style="font-weight: 700; font-size: 0.9rem;">🐾 เจาะจงเฉพาะสายพันธุ์:</span>
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

        <!-- Category Banner -->
        <div class="category-banner" id="category-banner" style="margin-bottom: 2rem;">
            <div>
                <div class="category-banner-title" id="cat-banner-title">
                    <?php echo $recommendation_categories[$initial_cat]['title']; ?>
                </div>
                <div class="category-banner-desc" id="cat-banner-desc">
                    <?php echo $recommendation_categories[$initial_cat]['desc']; ?>
                </div>
            </div>
            <div class="cat-filter-count-badge">
                แสดงเฉพาะหมวดนี้: <strong id="cat-banner-count">4</strong> ตัว
            </div>
        </div>

        <!-- Recommended Cats Grid -->
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

                        <div style="background: var(--bg-page); border-radius: var(--radius-sm); padding: 0.6rem 0.8rem; margin-bottom: 1.2rem; font-size: 0.78rem; color: var(--text-secondary);">
                            <div style="font-weight: 600; color: var(--primary-coral); margin-bottom: 0.2rem;">✨ นิสัย & ความโดดเด่น:</div>
                            <div><?php echo $cat['personality']; ?></div>
                        </div>

                        <div class="cat-card-footer">
                            <div class="cat-price-box">
                                <span class="cat-price-label">ค่าสินสอด / รับเลี้ยง</span>
                                <span class="cat-price-val"><?php echo number_format($cat['price']); ?> ฿</span>
                            </div>
                            <form method="POST" action="recommend.php?cat=<?php echo urlencode($initial_cat); ?>">
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
    </div>
</div>

<!-- Metadata and Comprehensive Matcher Script -->
<script>
const categoryMeta = <?php echo json_encode($recommendation_categories, JSON_UNESCAPED_UNICODE); ?>;
const allCatsData = <?php echo json_encode($cats, JSON_UNESCAPED_UNICODE); ?>;

function runComprehensiveCatMatcher(e) {
    if (e) e.preventDefault();
    if (typeof allCatsData === 'undefined') return;

    const home = document.getElementById('q_home').value;
    const time = document.getElementById('q_time').value;
    const exp = document.getElementById('q_exp').value;
    const hair = document.getElementById('q_hair').value;
    const personality = document.getElementById('q_personality').value;
    const budget = document.getElementById('q_budget').value;

    const matchedList = [];

    for (const id in allCatsData) {
        const cat = allCatsData[id];
        let score = 55; // baseline score
        let matchReasons = [];

        // 1. Home Suitability
        if (home === 'condo') {
            if (cat.categories.includes('condo')) {
                score += 8;
                matchReasons.push('เหมาะกับคอนโด ไม่ส่งเสียงดัง');
            } else if (cat.personality.includes('สงบ')) {
                score += 5;
            }
        } else if (home === 'house') {
            if (cat.categories.includes('playful') || cat.breed.includes('Maine Coon') || cat.breed.includes('Bengal')) {
                score += 8;
                matchReasons.push('มีพื้นที่กว้างขวางวิ่งเล่นสนุก');
            } else {
                score += 6;
            }
        } else { // townhouse
            score += 7;
        }

        // 2. Time Available
        if (time === 'limited') {
            if (cat.categories.includes('condo') || cat.personality.includes('รักสงบ') || cat.personality.includes('นิ่ง')) {
                score += 8;
                matchReasons.push('ดูแลตัวเองได้ดีเวลาอยู่ตัวเดียว');
            }
        } else if (time === 'high') {
            if (cat.categories.includes('popular') || cat.personality.includes('อ้อน') || cat.personality.includes('ติดคน')) {
                score += 8;
                matchReasons.push('ชอบคลอเคลีย เข้ากับคน WFH');
            }
        } else {
            score += 7;
        }

        // 3. Experience Level
        if (exp === 'beginner') {
            if (cat.categories.includes('beginner') || cat.highlights.some(h => h.includes('เลี้ยงง่าย'))) {
                score += 8;
                matchReasons.push('เลี้ยงง่าย สุขภาพแข็งแรง');
            }
        } else if (exp === 'expert') {
            if (cat.hair_type === 'hairless' || cat.hair_type === 'long' || cat.breed.includes('Bengal')) {
                score += 8;
                matchReasons.push('สายพันธุ์พิเศษตอบโจทย์มือโปร');
            }
        } else {
            score += 6;
        }

        // 4. Hair Type & Allergy
        if (hair === 'short' && cat.hair_type === 'short') {
            score += 9;
            matchReasons.push('ขนสั้น ดูแลง่าย ไม่พันกัน');
        } else if (hair === 'long' && cat.hair_type === 'long') {
            score += 9;
            matchReasons.push('ขนยาวนุ่มฟู สวยงามดุจตุ๊กตา');
        } else if (hair === 'medium' && (cat.hair_type === 'medium' || cat.hair_type === 'semi-long')) {
            score += 9;
            matchReasons.push('ขนปานกลางนุ่มแน่น');
        } else if (hair === 'hypoallergenic') {
            if (cat.hair_type === 'hairless' || cat.categories.includes('low_shed') || cat.breed.includes('Sphynx') || cat.breed.includes('Devon')) {
                score += 12;
                matchReasons.push('ขนร่วงน้อย เหมาะกับผู้กังวลภูมิแพ้');
            }
        }

        // 5. Personality
        if (personality === 'cuddly') {
            if (cat.personality.includes('อ้อน') || cat.personality.includes('คลอเคลีย') || cat.categories.includes('popular')) {
                score += 9;
                matchReasons.push('นิสัยขี้อ้อน ติดเจ้าของมาก');
            }
        } else if (personality === 'calm') {
            if (cat.personality.includes('สงบ') || cat.personality.includes('สุภาพ') || cat.categories.includes('condo')) {
                score += 9;
                matchReasons.push('นิสัยเรียบร้อย สุภาพ ไม่ทำลายของ');
            }
        } else if (personality === 'playful') {
            if (cat.personality.includes('ร่าเริง') || cat.personality.includes('ซน') || cat.categories.includes('playful')) {
                score += 9;
                matchReasons.push('ร่าเริง พลังงานสูง ชวนเล่นสนุก');
            }
        } else if (personality === 'smart') {
            if (cat.personality.includes('ฉลาด') || cat.personality.includes('พูด') || cat.personality.includes('สื่อสาร')) {
                score += 9;
                matchReasons.push('ฉลาด ช่างคุย โต้ตอบเก่ง');
            }
        }

        // 6. Budget Check
        const price = Number(cat.price) || 0;
        if (budget === 'eco') {
            if (price <= 20000) {
                score += 8;
                matchReasons.push('ราคาอยู่ในงบประมาณสบายกระเป๋า');
            }
        } else if (budget === 'mid') {
            if (price >= 18000 && price <= 30000) {
                score += 8;
                matchReasons.push('ราคาคุ้มค่ามาตรฐาน');
            }
        } else if (budget === 'prem') {
            if (price >= 28000) {
                score += 8;
                matchReasons.push('เกรดพรีเมียมคัดพิเศษ');
            }
        } else {
            score += 6;
        }

        // Cap score at 99%
        const finalScore = Math.min(score, 99);
        matchedList.push({
            cat: cat,
            score: finalScore,
            reasons: matchReasons.slice(0, 2)
        });
    }

    // Sort by highest score descending
    matchedList.sort((a, b) => b.score - a.score);

    // Take top 4 best matches
    const top4 = matchedList.slice(0, 4);

    const resultBox = document.getElementById('matcher-results');
    const resultContainer = document.getElementById('matched-cats-container');
    const scoreText = document.getElementById('match-score');

    if (resultContainer && resultBox) {
        resultContainer.innerHTML = '';
        scoreText.innerText = `ความเข้ากันได้สูงสุดถึง ${top4[0].score}% จากการวิเคราะห์ 6 มิติ (ที่อยู่อาศัย, เวลา, ประสบการณ์, ขน, นิสัย, งบประมาณ)`;

        top4.forEach(item => {
            const cat = item.cat;
            const reasonsHtml = item.reasons.map(r => `<span style="font-size: 0.72rem; background: var(--bg-card-subtle); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3); padding: 2px 8px; border-radius: 9999px; font-weight: 600;">✓ ${r}</span>`).join(' ');

            const card = document.createElement('div');
            card.className = 'cat-card';
            card.style.display = 'flex';
            card.innerHTML = `
                <div class="cat-card-img-wrap">
                    <img src="assets/images/${cat.image}" alt="${cat.name}" class="cat-card-img">
                    <span class="cat-card-badge" style="background: linear-gradient(135deg, #10B981, #059669); color: #FFFFFF; font-weight: 800;">
                        💖 แมตช์ตรงใจ ${item.score}%
                    </span>
                    <span class="cat-card-gender">${cat.gender}</span>
                </div>
                <div class="cat-card-body">
                    <div class="cat-card-breed">${cat.breed}</div>
                    <h3 class="cat-card-name">${cat.name}</h3>
                    <p class="cat-card-desc">${cat.description}</p>
                    
                    <div style="display: flex; gap: 4px; flex-wrap: wrap; margin-bottom: 0.8rem;">
                        ${reasonsHtml}
                    </div>

                    <div class="cat-tags-row">
                        <span class="cat-pill-tag highlight">🩺 ${cat.age}</span>
                        <span class="cat-pill-tag">🧶 ${cat.hair_label}</span>
                        <span class="cat-pill-tag">📜 ใบเพ็ดดีกรี</span>
                    </div>

                    <div class="cat-card-footer">
                        <div class="cat-price-box">
                            <span class="cat-price-label">ค่าสินสอด</span>
                            <span class="cat-price-val">${Number(cat.price).toLocaleString()} ฿</span>
                        </div>
                        <form method="POST" action="recommend.php">
                            <input type="hidden" name="action" value="add_cat">
                            <input type="hidden" name="cat_id" value="${cat.id}">
                            <button type="submit" class="btn btn-primary btn-sm">รับเลี้ยงน้อง 🐾</button>
                        </form>
                    </div>
                </div>
            `;
            resultContainer.appendChild(card);
        });

        resultBox.style.display = 'block';
        resultBox.scrollIntoView({ behavior: 'smooth' });
    }
}

window.addEventListener('DOMContentLoaded', () => {
    selectCategory('<?php echo $initial_cat; ?>');
});
</script>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
