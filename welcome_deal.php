<?php
require_once __DIR__ . '/data.php';

$currUser = getCurrentUser();
$is_new_register = isset($_GET['registered']) && $_GET['registered'] == '1';

// Handle Add Item to Cart (both cats and starter bundles)
$added_message = "";
if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_item') {
    $item_id = $_POST['item_id'] ?? '';
    if (addToCart($item_id)) {
        $item_name = $cats[$item_id]['name'] ?? ($starter_bundles[$item_id]['name'] ?? 'สินค้า');
        $added_message = "🎉 เพิ่ม <strong>" . htmlspecialchars($item_name) . "</strong> ลงในตะกร้าเรียบร้อยแล้ว!";
    }
}

// Curated 6 popular cats for new members
$curated_cat_ids = ['cat_british', 'cat_scottish', 'cat_ragdoll', 'cat_khao_manee', 'cat_americanshorthair', 'cat_japanese_bobtail'];
$featured_cats = [];
foreach ($curated_cat_ids as $cid) {
    if (isset($cats[$cid])) {
        $featured_cats[$cid] = $cats[$cid];
    }
}

require_once __DIR__ . '/header.php';
?>

<style>
/* Scoped Styling for Welcome Deal Sales Page */
.welcome-sales-page {
    max-width: 1200px;
    margin: 0 auto 4rem auto;
}

/* Hero Section */
.welcome-hero {
    background: linear-gradient(135deg, rgba(255, 117, 86, 0.12) 0%, rgba(255, 154, 139, 0.08) 50%, rgba(255, 238, 232, 0.15) 100%);
    border: 2px solid rgba(255, 117, 86, 0.25);
    border-radius: var(--radius-xl);
    padding: 3rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 2.5rem;
    box-shadow: var(--shadow-md);
}

[data-theme="dark"] .welcome-hero {
    background: linear-gradient(135deg, rgba(255, 117, 86, 0.15) 0%, rgba(23, 32, 48, 0.8) 100%);
    border-color: rgba(255, 117, 86, 0.35);
}

.welcome-hero::before {
    content: "🐱";
    position: absolute;
    top: -20px;
    right: -20px;
    font-size: 8rem;
    opacity: 0.08;
    pointer-events: none;
}

.vip-pill-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #FF7556, #FF9A8B);
    color: #FFFFFF;
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    padding: 0.4rem 1.2rem;
    border-radius: var(--radius-full);
    margin-bottom: 1.2rem;
    box-shadow: 0 4px 12px rgba(255, 117, 86, 0.3);
}

.welcome-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.8rem;
    line-height: 1.25;
}

.welcome-subtitle {
    font-size: 1.15rem;
    color: var(--text-secondary);
    max-width: 780px;
    margin: 0 auto 1.8rem auto;
    line-height: 1.6;
}

/* Countdown Bar */
.deal-timer-banner {
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
    background: var(--bg-card);
    border: 1px dashed var(--primary-coral);
    padding: 0.75rem 1.8rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
}

.timer-box {
    display: flex;
    gap: 0.4rem;
    font-weight: 800;
    color: #FF5A5F;
    font-size: 1.2rem;
    font-variant-numeric: tabular-nums;
}

/* Vouchers Grid */
.vouchers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.voucher-card {
    background: var(--bg-card);
    border: 2px dashed rgba(255, 117, 86, 0.4);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    box-shadow: var(--shadow-sm);
    transition: transform 0.3s ease, border-color 0.3s ease;
}

.voucher-card:hover {
    transform: translateY(-4px);
    border-color: var(--primary-coral);
    box-shadow: var(--shadow-md);
}

.voucher-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: #FFF1EE;
    color: var(--primary-coral);
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.65rem;
    border-radius: var(--radius-full);
}

[data-theme="dark"] .voucher-badge {
    background: rgba(255, 117, 86, 0.2);
    color: #FFA48E;
}

.voucher-code-box {
    background: var(--bg-page);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 0.6rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 1rem;
}

.voucher-code-text {
    font-family: monospace;
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--primary-coral);
    letter-spacing: 1px;
}

/* Storytelling 4 Pillars */
.story-pillars {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3.5rem;
}

.pillar-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.8rem 1.4rem;
    text-align: center;
    box-shadow: var(--shadow-sm);
    transition: transform 0.3s ease;
}

.pillar-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-coral);
}

.pillar-icon {
    font-size: 2.5rem;
    margin-bottom: 0.8rem;
}

.pillar-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.pillar-desc {
    font-size: 0.88rem;
    color: var(--text-secondary);
    line-height: 1.5;
}

/* Bundle Cards */
.bundles-section {
    margin-bottom: 4rem;
}

.bundles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.8rem;
}

.bundle-card {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: 2rem 1.6rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    box-shadow: var(--shadow-sm);
    transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
}

.bundle-card.popular-bundle {
    border-color: var(--primary-coral);
    box-shadow: 0 8px 24px rgba(255, 117, 86, 0.18);
    transform: scale(1.02);
}

.bundle-card:hover {
    transform: translateY(-6px) scale(1.02);
    border-color: var(--primary-coral);
}

.bundle-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #FF7556, #FF9A8B);
    color: #FFFFFF;
    font-size: 0.75rem;
    font-weight: 800;
    padding: 0.3rem 1.2rem;
    border-radius: var(--radius-full);
    white-space: nowrap;
    box-shadow: 0 4px 10px rgba(255, 117, 86, 0.3);
}

.bundle-price-wrap {
    margin: 1.2rem 0;
    padding: 1rem;
    background: var(--bg-page);
    border-radius: var(--radius-md);
    text-align: center;
}

.bundle-old-price {
    text-decoration: line-through;
    color: var(--text-muted);
    font-size: 0.95rem;
    margin-right: 0.5rem;
}

.bundle-new-price {
    font-size: 2rem;
    font-weight: 800;
    color: var(--primary-coral);
}

.bundle-features {
    list-style: none;
    padding: 0;
    margin: 1.2rem 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.bundle-features li {
    padding: 0.45rem 0;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    border-bottom: 1px dashed var(--border-color);
}

.bundle-features li:last-child {
    border-bottom: none;
}

/* FAQ Accordion */
.faq-section {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: 2.5rem 2rem;
    margin-bottom: 3.5rem;
    box-shadow: var(--shadow-sm);
}

.faq-item {
    border-bottom: 1px solid var(--border-color);
    padding: 1.2rem 0;
}

.faq-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.faq-question {
    font-weight: 700;
    font-size: 1.05rem;
    color: var(--text-primary);
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
}

.faq-answer {
    margin-top: 0.8rem;
    font-size: 0.92rem;
    color: var(--text-secondary);
    line-height: 1.6;
    display: none;
}

.faq-item.active .faq-answer {
    display: block;
}

.faq-icon {
    transition: transform 0.3s ease;
}

.faq-item.active .faq-icon {
    transform: rotate(180deg);
}

/* Reviews Grid */
.reviews-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3.5rem;
}

.review-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
}

.review-stars {
    color: #FFB800;
    font-size: 1.1rem;
    margin-bottom: 0.6rem;
}

.review-text {
    font-size: 0.9rem;
    color: var(--text-secondary);
    line-height: 1.5;
    margin-bottom: 1rem;
}

.reviewer-meta {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.reviewer-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--primary-coral);
}

/* Toast */
#toast-notification {
    position: fixed;
    bottom: 80px;
    right: 25px;
    background: #10B981;
    color: #FFFFFF;
    padding: 0.75rem 1.4rem;
    border-radius: var(--radius-full);
    font-weight: 700;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    display: none;
    align-items: center;
    gap: 0.5rem;
    z-index: 9999;
    animation: fadeInToast 0.3s ease;
}

@keyframes fadeInToast {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Sticky Bottom Action Bar */
.sticky-sales-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    border-top: 1px solid rgba(255, 117, 86, 0.3);
    padding: 0.8rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
    z-index: 990;
}

[data-theme="dark"] .sticky-sales-bar {
    background: rgba(23, 32, 48, 0.95);
    border-top-color: rgba(255, 117, 86, 0.3);
}

/* Celebration Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.65);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    padding: 1.5rem;
}

.modal-content-box {
    background: var(--bg-card);
    border: 2px solid var(--primary-coral);
    border-radius: var(--radius-xl);
    max-width: 520px;
    width: 100%;
    padding: 2.5rem 2rem;
    text-align: center;
    box-shadow: var(--shadow-xl);
    position: relative;
    animation: zoomInModal 0.3s ease;
}

@keyframes zoomInModal {
    from { transform: scale(0.85); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>

<div class="welcome-sales-page">

    <!-- New Member Registration VIP Email Dispatch Banner -->
    <?php if ($is_new_register): ?>
        <div style="margin-bottom: 2rem; padding: 1.25rem 1.75rem; border-radius: var(--radius-lg); background: linear-gradient(135deg, #0B0F19 0%, #1E293B 100%); color: #F8FAFC; border: 2px solid #F59E0B; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.2rem; position: relative; overflow: hidden;">
            <div style="display: flex; align-items: center; gap: 1.2rem;">
                <div style="width: 52px; height: 52px; background: linear-gradient(135deg, #F59E0B, #D97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4); flex-shrink: 0;">
                    👑
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.15rem; color: #FBBF24; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                        <span>สมัครสมาชิกสำเร็จ! ยินดีต้อนรับคุณ <?php echo htmlspecialchars($currUser['fullname'] ?? 'สมาชิก VIP'); ?></span>
                        <span style="font-size: 0.75rem; background: #D97706; color: #FFFFFF; padding: 0.2rem 0.6rem; border-radius: 9999px; font-weight: 700;">VIP MEMBER</span>
                    </div>
                    <div style="font-size: 0.95rem; color: #E2E8F0; line-height: 1.6;">
                        ✉️ ระบบได้จัดส่งอีเมลต้อนรับ <strong>VIP Member Card</strong> และโค้ดลด 15% (<code style="background: rgba(245, 158, 11, 0.25); color: #FCD34D; padding: 0.15rem 0.45rem; border-radius: 4px; font-weight: 700;">WELCOME15</code>) ไปยังอีเมล <strong><?php echo htmlspecialchars($currUser['email'] ?? ''); ?></strong> เรียบร้อยแล้ว! (ตรวจเช็คได้ในกล่องจดหมายของคุณ)
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 0.6rem; align-items: center;">
                <button type="button" class="btn btn-primary btn-sm" onclick="copyVoucher('WELCOME15', this)" style="background: linear-gradient(135deg, #F59E0B, #D97706); border: none; font-weight: 700; padding: 0.6rem 1.1rem;">
                    คัดลอกโค้ด WELCOME15 📋
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Added To Cart Alert -->
    <?php if (!empty($added_message)): ?>
        <div class="alert-box alert-success" style="margin-bottom: 2rem; justify-content: space-between;">
            <span><?php echo $added_message; ?></span>
            <a href="cart.php" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.9rem; font-size: 0.85rem;">
                ดูตะกร้าสินค้า 🛒
            </a>
        </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <div class="welcome-hero">
        <div class="vip-pill-badge">
            <span>✨</span>
            <span>EXCLUSIVE VIP WELCOME OFFER</span>
            <span>🐾</span>
        </div>
        <h1 class="welcome-title">
            ยินดีต้อนรับสู่ครอบครัวทาสแมว Cat Boutique! 🎉
        </h1>
        <p class="welcome-subtitle">
            <?php if ($currUser): ?>
                สวัสดีคุณ <strong><?php echo htmlspecialchars($currUser['fullname']); ?></strong> ขอต้อนรับสู่คอมมูนิตี้คนรักแมวสายพันธุ์แท้ เราได้ปลดล็อกสิทธิพิเศษ มูลค่ารวมกว่า <strong>5,500 บาท</strong> ให้คุณเริ่มต้นรับเลี้ยงน้องแมวได้อย่างอุ่นใจที่สุด
            <?php else: ?>
                ขอต้อนรับสมาชิกใหม่ทุกท่าน สิทธิพิเศษต้อนรับมูลค่ากว่า <strong>5,500 บาท</strong> ถูกเปิดใช้งานแล้วสำหรับคุณ รับเลี้ยงน้องแมวสายพันธุ์แท้พร้อมชุดของแถมและประกันสุขภาพฟรี!
            <?php endif; ?>
        </p>

        <!-- Limited Time Timer -->
        <div class="deal-timer-banner">
            <span style="font-size: 1.3rem;">⏳</span>
            <span style="font-weight: 600; color: var(--text-primary);">สิทธิพิเศษสมาชิกใหม่สิ้นสุดใน:</span>
            <div class="timer-box" id="welcome-countdown">
                <span id="hours">47</span>:<span id="minutes">59</span>:<span id="seconds">30</span>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="#vouchers-anchor" class="btn btn-primary btn-lg" style="box-shadow: 0 4px 15px rgba(255,117,86,0.35);">
                🎁 กดรับโค้ดส่วนลด 15%
            </a>
            <a href="#bundles-anchor" class="btn btn-secondary btn-lg">
                📦 ดูชุด Starter Pack มือใหม่
            </a>
            <?php if (isAdmin()): ?>
            <a href="welcome_sales_mockup.php" class="btn btn-secondary btn-lg" style="border: 2px dashed var(--primary-coral); background: var(--bg-card);">
                ✉️ ส่งข้อมูล (Admin)
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 1. Vouchers Section -->
    <div id="vouchers-anchor" style="scroll-margin-top: 90px; margin-bottom: 3.5rem;">
        <div class="section-header" style="margin-bottom: 1.8rem;">
            <span class="section-tag">🎟️ WELCOME VOUCHERS</span>
            <h2 class="section-title">คูปองของขวัญต้อนรับสมาชิกใหม่</h2>
            <p class="section-subtitle">กดคัดลอกโค้ดเพื่อนำไปกรอกหรือใช้เป็นส่วนลดในการสั่งจองน้องแมวและสินค้าในระบบ</p>
        </div>

        <div class="vouchers-grid">
            <!-- Voucher 1 -->
            <div class="voucher-card">
                <span class="voucher-badge">ลดสูงสุด 15%</span>
                <div>
                    <div style="font-size: 2rem; margin-bottom: 0.4rem;">🐱</div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.3rem;">
                        ส่วนลดรับเลี้ยงน้องแมวตัวแรก
                    </h3>
                    <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">
                        ลดทันที 15% จากค่าสินสอดน้องแมวทุกสายพันธุ์ในร้าน สำหรับสมาชิกที่รับเลี้ยงครั้งแรก
                    </p>
                </div>
                <div class="voucher-code-box">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">โค้ดส่วนลดของคุณ:</span>
                        <span class="voucher-code-text">WELCOME15</span>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="copyVoucher('WELCOME15', this)">
                        คัดลอก 📋
                    </button>
                </div>
            </div>

            <!-- Voucher 2 -->
            <div class="voucher-card">
                <span class="voucher-badge">ฟรี 2,500.-</span>
                <div>
                    <div style="font-size: 2rem; margin-bottom: 0.4rem;">🩺</div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.3rem;">
                        ฟรี! ตรวจสุขภาพ & วัคซีนครบ
                    </h3>
                    <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">
                        รับสิทธิ์ตรวจสุขภาพเบื้องต้น ตรวจแล็บไวรัส FIV/FeLV ฟรี + ฝังไมโครชิปสากลก่อนส่งมอบ
                    </p>
                </div>
                <div class="voucher-code-box">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">โค้ดสิทธิ์ตรวจสุขภาพ:</span>
                        <span class="voucher-code-text">HEALTHKITVIP</span>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="copyVoucher('HEALTHKITVIP', this)">
                        คัดลอก 📋
                    </button>
                </div>
            </div>

            <!-- Voucher 3 -->
            <div class="voucher-card">
                <span class="voucher-badge">ฟรี 1,500.-</span>
                <div>
                    <div style="font-size: 2rem; margin-bottom: 0.4rem;">🚐</div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.3rem;">
                        ฟรี! ส่งด่วนแอร์ปรับอุณหภูมิ
                    </h3>
                    <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">
                        บริการส่งมอบน้องแมวด้วยรถยนต์ปรับอุณหภูมิพิเศษ พร้อมพี่เลี้ยงดูแลตลอดเส้นทางถึงหน้าบ้าน
                    </p>
                </div>
                <div class="voucher-code-box">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">โค้ดจัดส่งฟรี:</span>
                        <span class="voucher-code-text">FREESHIPVIP</span>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="copyVoucher('FREESHIPVIP', this)">
                        คัดลอก 📋
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Storytelling / Sales Pitch -->
    <div style="margin-bottom: 4rem;">
        <div class="section-header" style="margin-bottom: 2rem;">
            <span class="section-tag">🌟 WHY CHOOSE US</span>
            <h2 class="section-title">ทำไมทาสแมวกว่า 5,200+ คน จึงเลือกรับเลี้ยงกับเรา?</h2>
            <p class="section-subtitle">
                เราเข้าใจว่าการรับสมาชิกใหม่เข้าบ้านคือการตัดสินใจครั้งสำคัญ Cat Boutique จึงคัดสรรสิ่งที่ดีที่สุดเพื่อความสุขระยะยาวของคุณและน้องแมว
            </p>
        </div>

        <div class="story-pillars">
            <div class="pillar-card">
                <div class="pillar-icon">🏆</div>
                <h3 class="pillar-title">สายพันธุ์แท้ 100% มีใบเพ็ดดีกรี</h3>
                <p class="pillar-desc">
                    พัฒนาสายพันธุ์ตามมาตรฐานสมาคมแมวสากล WCF / CFA / TICA ลำดับเครือญาติชัดเจน ปราศจากปัญหาเลือดชิด
                </p>
            </div>

            <div class="pillar-card">
                <div class="pillar-icon">🩺</div>
                <h3 class="pillar-title">ตรวจสุขภาพ & ไวรัส FeLV/FIV ลบ</h3>
                <p class="pillar-desc">
                    น้องแมวได้รับการตรวจสุขภาพจากสัตวแพทย์ทุกสัปดาห์ วัคซีนรวมครบ ถ่ายพยาธิ และมีสมุดประจำตัวเล่มจริง
                </p>
            </div>

            <div class="pillar-card">
                <div class="pillar-icon">🛡️</div>
                <h3 class="pillar-title">รับประกันสุขภาพยาวนาน 180 วัน</h3>
                <p class="pillar-desc">
                    การันตีคุ้มครองโรคทางพันธุกรรม (FIP, PKD, HCM) ยาวนานถึง 180 วัน เปลี่ยนตัวใหม่หรือยินดีคืนเงินเต็มจำนวน
                </p>
            </div>

            <div class="pillar-card">
                <div class="pillar-icon">💬</div>
                <h3 class="pillar-title">ที่ปรึกษาการเลี้ยงดูฟรีตลอดชีพ</h3>
                <p class="pillar-desc">
                    ไม่ว่าจะมือใหม่หรือมีคำถาม ทีมงานผู้เชี่ยวชาญและสัตวแพทย์พร้อมให้คำแนะนำและช่วยเหลือตลอด 24 ชั่วโมง
                </p>
            </div>
        </div>
    </div>

    <!-- 3. Member Starter Bundles -->
    <div id="bundles-anchor" class="bundles-section" style="scroll-margin-top: 90px;">
        <div class="section-header" style="margin-bottom: 2.2rem;">
            <span class="section-tag">📦 STARTER CARE PACKAGES</span>
            <h2 class="section-title">3 ชุดของใช้และดูแลสุขภาพสุดคุ้มสำหรับสมาชิกใหม่</h2>
            <p class="section-subtitle">จัดเซ็ตครบ จบในที่เดียว ไม่ต้องเสียเวลาเลือกซื้อทีละชิ้น พร้อมส่วนลดพิเศษเฉพาะสมาชิก</p>
        </div>

        <div class="bundles-grid">
            <?php foreach ($starter_bundles as $b_id => $bundle): ?>
                <?php $is_pop = ($b_id === 'bundle_starter'); ?>
                <div class="bundle-card <?php echo $is_pop ? 'popular-bundle' : ''; ?>">
                    <?php if (!empty($bundle['badge'])): ?>
                        <div class="bundle-badge"><?php echo $bundle['badge']; ?></div>
                    <?php endif; ?>

                    <div>
                        <div style="font-size: 2.2rem; margin-bottom: 0.5rem; text-align: center;">
                            <?php 
                            if ($b_id === 'bundle_starter') echo '🏡';
                            elseif ($b_id === 'bundle_spa') echo '🛁';
                            else echo '👑';
                            ?>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--text-primary); text-align: center; margin-bottom: 0.3rem;">
                            <?php echo htmlspecialchars($bundle['name']); ?>
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); text-align: center; margin-bottom: 0.5rem;">
                            <?php echo htmlspecialchars($bundle['description']); ?>
                        </p>

                        <div class="bundle-price-wrap">
                            <div>
                                <span class="bundle-old-price"><?php echo number_format($bundle['original_price']); ?> ฿</span>
                                <span style="color: #10B981; font-weight: 700; font-size: 0.85rem;">ประหยัด <?php echo number_format($bundle['original_price'] - $bundle['price']); ?> ฿</span>
                            </div>
                            <div class="bundle-new-price"><?php echo number_format($bundle['price']); ?> ฿</div>
                            <span style="font-size: 0.78rem; color: var(--text-muted);">ราคาพิเศษเฉพาะสมาชิกใหม่</span>
                        </div>

                        <ul class="bundle-features">
                            <?php if ($b_id === 'bundle_starter'): ?>
                                <li><span>✅</span> คอนโดแมว 3 ชั้น นุ่มสบาย รับน้ำหนักได้ดี</li>
                                <li><span>✅</span> กระบะทรายขนาดใหญ่ + ที่ตักทรายแถมฟรี</li>
                                <li><span>✅</span> ชามน้ำพุไอออน กรองน้ำเงียบกริบ 2.5L</li>
                                <li><span>✅</span> อาหารเม็ดเกรด Holistic Grain-Free 5kg</li>
                                <li><span>✅</span> ขนมแมวเลียพรีเมียม 1 โหล (12 ซอง)</li>
                                <li><span>✅</span> ไม้ล่อแมวขนนก + ลูกบอลกระดิ่ง</li>
                            <?php elseif ($b_id === 'bundle_spa'): ?>
                                <li><span>✅</span> บัตรกรูมมิ่ง อาบน้ำตัดแต่งขนพรีเมียม 3 ครั้ง</li>
                                <li><span>✅</span> ประกันสุขภาพและอุบัติเหตุสัตว์เลี้ยง 1 ปี</li>
                                <li><span>✅</span> เจลวิตามินบำรุงขนและลดก้อนขนนำเข้า</li>
                                <li><span>✅</span> แปรงสแตนเลสนวดผิวหนังป้องกันขนพันกัน</li>
                                <li><span>✅</span> น้ำยาเช็ดหูและชุดตัดเล็บแมวนิรภัย</li>
                                <li><span>✅</span> ปรึกษาสัตวแพทย์ทางโทรศัพท์ฟรี 1 ปี</li>
                            <?php else: ?>
                                <li><span>✅</span> รวมของใช้และบริการทั้งหมดในเซ็ต 1 + เซ็ต 2</li>
                                <li><span>✅</span> กรงเดินทางขึ้นเครื่องบินมาตรฐาน IATA</li>
                                <li><span>✅</span> ปลอกคอ GPS อัจฉริยะ ติดตามตำแหน่งผ่านมือถือ</li>
                                <li><span>✅</span> เครื่องให้อาหารอัตโนมัติตั้งเวลาผ่าน Wi-Fi</li>
                                <li><span>✅</span> กล้องวงจรปิด Pet Cam ดูน้องแมวผ่านแอป 24 ชม.</li>
                                <li><span>✅</span> รับสิทธิ์บริการพี่เลี้ยงและฝากเลี้ยงฟรี 5 วัน</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <form method="POST" action="welcome_deal.php" style="margin-top: 1.5rem;">
                        <input type="hidden" name="action" value="add_item">
                        <input type="hidden" name="item_id" value="<?php echo $b_id; ?>">
                        <button type="submit" class="btn <?php echo $is_pop ? 'btn-primary' : 'btn-secondary'; ?>" style="width: 100%; padding: 0.75rem;">
                            สั่งซื้อแพ็กเกจนี้ 🛒
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 4. Curated Hot Cats for New Adopters -->
    <div style="margin-bottom: 4rem;">
        <div class="section-header" style="margin-bottom: 2rem;">
            <span class="section-tag">🐱 BEST FOR BEGINNERS</span>
            <h2 class="section-title">น้องแมวยอดฮิตที่เหมาะที่สุดสำหรับมือใหม่</h2>
            <p class="section-subtitle">นิสัยอ่อนโยน เลี้ยงง่าย ปรับตัวเข้ากับครอบครัวได้อย่างรวดเร็ว พร้อมย้ายบ้านทันที</p>
        </div>

        <div class="products-grid">
            <?php foreach ($featured_cats as $c_id => $cat): ?>
                <?php 
                $original_price = $cat['price'];
                $member_price = round($original_price * 0.95); // 5% default member discount
                ?>
                <div class="cat-card">
                    <div class="cat-card-img-wrap">
                        <img src="assets/images/<?php echo $cat['image']; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="cat-card-img" loading="lazy">
                        <span class="cat-card-badge">✨ พร้อมย้ายบ้าน</span>
                        <span class="cat-card-gender"><?php echo htmlspecialchars($cat['gender']); ?></span>
                    </div>

                    <div class="cat-card-body">
                        <div class="cat-card-breed"><?php echo htmlspecialchars($cat['breed']); ?></div>
                        <h3 class="cat-card-name"><?php echo htmlspecialchars($cat['name']); ?></h3>
                        <p class="cat-card-desc"><?php echo htmlspecialchars($cat['description']); ?></p>

                        <div class="cat-tags-row">
                            <span class="cat-pill-tag highlight">🩺 <?php echo htmlspecialchars($cat['age']); ?></span>
                            <span class="cat-pill-tag">🧶 <?php echo htmlspecialchars($cat['hair_label']); ?></span>
                            <span class="cat-pill-tag">📜 ใบเพ็ดดีกรีแท้</span>
                        </div>

                        <div style="background: var(--bg-page); border-radius: var(--radius-sm); padding: 0.5rem 0.8rem; margin-bottom: 1rem; font-size: 0.78rem; color: #10B981; font-weight: 600;">
                            🎁 สิทธิ์สมาชิก: ส่งฟรีแอร์ + ประกัน 180 วัน
                        </div>

                        <div class="cat-card-footer">
                            <div class="cat-price-box">
                                <span class="cat-price-label" style="text-decoration: line-through; color: var(--text-muted); font-size: 0.75rem;">
                                    ปกติ <?php echo number_format($original_price); ?> ฿
                                </span>
                                <span class="cat-price-val" style="color: var(--primary-coral); font-size: 1.25rem;">
                                    <?php echo number_format($member_price); ?> ฿
                                </span>
                            </div>

                            <form method="POST" action="welcome_deal.php">
                                <input type="hidden" name="action" value="add_item">
                                <input type="hidden" name="item_id" value="<?php echo $c_id; ?>">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    รับเลี้ยงน้อง 🐾
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="products.php" class="btn btn-secondary btn-lg">
                ดูน้องแมวสายพันธุ์อื่นทั้งหมดในร้าน (32 ตัว) 🐾
            </a>
        </div>
    </div>

    <!-- 5. Real Adopter Testimonials -->
    <div style="margin-bottom: 4rem;">
        <div class="section-header" style="margin-bottom: 2rem;">
            <span class="section-tag">💬 REAL REVIEWS</span>
            <h2 class="section-title">เสียงตอบรับจากครอบครัวทาสแมวตัวจริง</h2>
            <p class="section-subtitle">ความสุขและความมั่นใจของลูกค้าที่รับเลี้ยงน้องแมวไปดูแลจริง</p>
        </div>

        <div class="reviews-grid">
            <div class="review-card">
                <div class="review-stars">⭐⭐⭐⭐⭐</div>
                <p class="review-text">
                    "ประทับใจตั้งแต่สมัครสมาชิกเลยค่ะ ได้โค้ดส่วนลด 15% คุ้มมาก น้องสก็อตติชสุขภาพแข็งแรง ร่าเริงตั้งแต่วันแรก ทีมงานให้คำปรึกษาดีมากเรื่องเตรียมอาหารและกระบะทราย ประทับใจมากค่ะ!"
                </p>
                <div class="reviewer-meta">
                    <img src="assets/images/cat_scottish.jpg" alt="Reviewer" class="reviewer-avatar">
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary);">คุณแพรวา ม.</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">รับเลี้ยงน้องโมจิ (Scottish Fold) • กรุงเทพฯ</div>
                    </div>
                </div>
            </div>

            <div class="review-card">
                <div class="review-stars">⭐⭐⭐⭐⭐</div>
                <p class="review-text">
                    "อยู่เชียงใหม่ ตอนแรกกังวลเรื่องการจัดส่ง แต่ทางร้านส่งด้วยรถตู้ปรับอุณหภูมิพิเศษ น้องบริติชมาถึงอย่างปลอดภัย ไม่ตื่นกลัวเลย มีใบเพ็ดดีกรีและสมุดวัคซีนส่งมาพร้อม แนะนำเลยครับ"
                </p>
                <div class="reviewer-meta">
                    <img src="assets/images/cat_british.jpg" alt="Reviewer" class="reviewer-avatar">
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary);">คุณกิตติศักดิ์ ภ.</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">รับเลี้ยงน้องบราวนี่ (British Shorthair) • เชียงใหม่</div>
                    </div>
                </div>
            </div>

            <div class="review-card">
                <div class="review-stars">⭐⭐⭐⭐⭐</div>
                <p class="review-text">
                    "สั่งซื้อชุด Starter Pack ไปด้วย คุ้มมาก ไม่ต้องไปวิ่งหาซื้อของเอง ขาดตกอะไรทางร้านจัดมาให้ครบ น้องแร็กดอลล์ขนนุ่มฟูเหมือนก้อนเมฆ ขี้อ้อนสุดๆ แฟนชอบมาก ขอบคุณทีมงาน Cat Boutique ครับ"
                </p>
                <div class="reviewer-meta">
                    <img src="assets/images/cat_ragdoll.jpg" alt="Reviewer" class="reviewer-avatar">
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary);">คุณณภัทร ว.</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">รับเลี้ยงน้องสโนว์ (Ragdoll) • นนทบุรี</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. FAQ Section -->
    <div class="faq-section">
        <div class="section-header" style="margin-bottom: 1.5rem; text-align: left;">
            <span class="section-tag">❓ FAQ FOR NEW ADOPTERS</span>
            <h2 class="section-title">คำถามที่พบบ่อยสำหรับทาสแมวมือใหม่</h2>
        </div>

        <div class="faq-item active">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>1. มือใหม่ไม่เคยเลี้ยงแมวมาก่อน จะเริ่มต้นอย่างไรดี?</span>
                <span class="faq-icon">▼</span>
            </div>
            <div class="faq-answer">
                ไม่ต้องกังวลเลยครับ! ทางร้านมีทีมงานสัตวแพทย์และผู้เชี่ยวชาญช่วยแนะนำสายพันธุ์ที่เข้ากับไลฟ์สไตล์ของคุณ พร้อมคู่มือการเตรียมบ้าน และแนะนำให้เลือกซื้อชุด Starter Pack ที่มีอุปกรณ์จำเป็นครบถ้วนตั้งแต่วันแรกที่น้องย้ายเข้าบ้านครับ
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>2. การจัดส่งน้องแมวปลอดภัยหรือไม่ และมีบริการส่งถึงที่บ้านไหม?</span>
                <span class="faq-icon">▼</span>
            </div>
            <div class="faq-answer">
                เราจัดส่งด้วยรถยนต์เฉพาะทางที่มีระบบควบคุมอุณหภูมิและความชื้น (Climate-Controlled) พร้อมพี่เลี้ยงดูแลน้ำ อาหาร และความสะอาดตลอดทาง ส่งตรงถึงหน้าบ้านทุกจังหวัดในประเทศไทย ปลอดภัย 100% ครับ
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>3. ใบเพ็ดดีกรีและเอกสารตรวจสุขภาพจะได้รับเมื่อไหร่?</span>
                <span class="faq-icon">▼</span>
            </div>
            <div class="faq-answer">
                สมุดวัคซีน ผลตรวจแล็บ FIV/FeLV และใบรับประกันสุขภาพจะส่งมอบพร้อมตัวน้องแมวในวันรับมอบ ส่วนใบเพ็ดดีกรีอย่างเป็นทางการจากสมาคม WCF หรือ CFA จะส่งตามไปให้ภายใน 30-45 วันหลังจากขึ้นทะเบียนเรียบร้อยครับ
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>4. โค้ดส่วนลดสมาชิกใหม่สามารถใช้ร่วมกับอะไรได้บ้าง?</span>
                <span class="faq-icon">▼</span>
            </div>
            <div class="faq-answer">
                โค้ด <code>WELCOME15</code> สามารถใช้ลดค่าสินสอดน้องแมวได้ทันที 15% และสมาชิกทุกคนจะได้รับสิทธิ์ส่งฟรีแอร์และบริการตรวจสุขภาพฟรีโดยอัตโนมัติครับ!
            </div>
        </div>
    </div>

</div>

<!-- Sticky Bottom CTA Bar -->
<div class="sticky-sales-bar">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="font-size: 1.5rem; display: none; @media(min-width: 600px){display:inline;}">🎁</span>
        <div>
            <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-primary);">
                โค้ดสมาชิกใหม่: <span style="color: var(--primary-coral); font-family: monospace;">WELCOME15</span> (ลด 15%)
            </div>
            <div style="font-size: 0.78rem; color: var(--text-muted);">
                หมดเขตในอีก <span id="bar-timer">47:59:30</span>
            </div>
        </div>
    </div>
    <div style="display: flex; gap: 0.8rem; align-items: center;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="copyVoucher('WELCOME15', this)">
            คัดลอกโค้ด 📋
        </button>
        <a href="cart.php" class="btn btn-primary btn-sm" style="box-shadow: 0 4px 12px rgba(255,117,86,0.3);">
            ไปที่ตะกร้า 🛒
        </a>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast-notification">
    <span>✅</span>
    <span id="toast-msg">คัดลอกโค้ดสำเร็จแล้ว!</span>
</div>

<!-- Confetti Celebration Modal for New Registrants -->
<?php if ($is_new_register): ?>
<div class="modal-overlay" id="welcome-modal">
    <div class="modal-content-box" style="border: 2px solid #F59E0B; box-shadow: 0 20px 50px rgba(0,0,0,0.35);">
        <div style="font-size: 3.5rem; margin-bottom: 0.5rem; animation: bounce 1s infinite alternate;">👑</div>
        <h2 style="font-size: 1.7rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.4rem;">
            สมัครสมาชิก VIP สำเร็จแล้ว!
        </h2>
        <p style="font-size: 0.98rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 1.2rem;">
            ยินดีต้อนรับคุณ <strong><?php echo htmlspecialchars($currUser['fullname'] ?? 'สมาชิกใหม่'); ?></strong> สู่ Purrfect VIP Club!<br>
            เราได้เตรียมโค้ดส่วนลด 15% และชุดของขวัญต้อนรับไว้ให้คุณเรียบร้อยแล้ว
        </p>

        <!-- Sent Email Notification Box -->
        <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.35); border-radius: var(--radius-md); padding: 0.85rem 1rem; margin-bottom: 1.2rem; text-align: left; display: flex; align-items: center; gap: 0.75rem;">
            <div style="font-size: 1.6rem;">✉️</div>
            <div style="font-size: 0.86rem; color: var(--text-primary); line-height: 1.45;">
                <strong style="color: #059669;">ส่งอีเมลต้อนรับ VIP เข้ากล่องจดหมายแล้ว!</strong><br>
                ส่งตรงไปยัง: <span style="font-weight: 700; color: var(--primary-coral);"><?php echo htmlspecialchars($currUser['email'] ?? ''); ?></span><br>
                <span style="font-size: 0.78rem; color: var(--text-muted);">(มีบัตร VIP Member Card + โค้ดลด 15% + สิทธิ์บริการพิเศษ)</span>
            </div>
        </div>

        <div style="background: var(--bg-page); border: 2px dashed #F59E0B; border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.5rem;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.2rem;">โค้ดส่วนลดต้อนรับสมาชิกของคุณ</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #D97706; letter-spacing: 1px; font-family: monospace;">WELCOME15</div>
            <div style="font-size: 0.78rem; color: #10B981; font-weight: 600; margin-top: 0.2rem;">ลดทันที 15% + จัดส่งรถตู้แอร์ฟรี + ตรวจแล็บฟรีถึงบ้าน</div>
        </div>

        <div style="display: flex; gap: 0.8rem;">
            <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="copyVoucher('WELCOME15', this)">
                คัดลอกโค้ด 📋
            </button>
            <button type="button" class="btn btn-primary" style="flex: 1.2;" onclick="closeWelcomeModal()">
                เริ่มช้อปปิ้งเลย 🐾
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Clipboard Copy with Toast
function copyVoucher(code, btn) {
    navigator.clipboard.writeText(code).then(() => {
        const originalText = btn.innerHTML;
        btn.innerHTML = "คัดลอกแล้ว! ✅";
        btn.classList.add('btn-success');
        
        showToast("คัดลอกโค้ด " + code + " เรียบร้อยแล้ว! นำไปใช้ในตะกร้าได้เลย");

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
        }, 2500);
    }).catch(err => {
        alert("โค้ดของคุณคือ: " + code);
    });
}

function showToast(msg) {
    const toast = document.getElementById('toast-notification');
    const toastMsg = document.getElementById('toast-msg');
    if (toast && toastMsg) {
        toastMsg.textContent = msg;
        toast.style.display = 'flex';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }
}

// Countdown Timer with localStorage persistence
(function initTimer() {
    let endTime = localStorage.getItem('cat_welcome_endtime');
    if (!endTime) {
        // 48 hours from now
        endTime = Date.now() + (48 * 60 * 60 * 1000);
        localStorage.setItem('cat_welcome_endtime', endTime);
    } else {
        endTime = parseInt(endTime);
    }

    function updateTimer() {
        const now = Date.now();
        let remaining = Math.max(0, Math.floor((endTime - now) / 1000));

        if (remaining <= 0) {
            // Reset for another loop
            endTime = Date.now() + (48 * 60 * 60 * 1000);
            localStorage.setItem('cat_welcome_endtime', endTime);
            remaining = 48 * 3600;
        }

        const hrs = String(Math.floor(remaining / 3600)).padStart(2, '0');
        const mins = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
        const secs = String(remaining % 60).padStart(2, '0');

        const hoursEl = document.getElementById('hours');
        const minutesEl = document.getElementById('minutes');
        const secondsEl = document.getElementById('seconds');
        const barTimerEl = document.getElementById('bar-timer');

        if (hoursEl) hoursEl.textContent = hrs;
        if (minutesEl) minutesEl.textContent = mins;
        if (secondsEl) secondsEl.textContent = secs;
        if (barTimerEl) barTimerEl.textContent = hrs + ':' + mins + ':' + secs;
    }

    updateTimer();
    setInterval(updateTimer, 1000);
})();

// Toggle FAQ
function toggleFaq(el) {
    const item = el.parentElement;
    item.classList.toggle('active');
}

// Close Welcome Modal
function closeWelcomeModal() {
    const modal = document.getElementById('welcome-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
