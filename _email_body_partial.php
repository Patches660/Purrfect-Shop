<?php
$email_base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000');
?>
<!-- ================================================================== -->
<!-- CONTENT SALES LETTER & PRODUCT CATALOG (TO SEND TO NEW MEMBERS) -->
<!-- ================================================================== -->

<!-- Brand Hero Banner -->
<div class="email-brand-hero">
    <img src="<?php echo $email_base_url; ?>/assets/images/logo.png" alt="Cat Boutique Logo" class="email-brand-logo">
    <div class="email-member-tag">MEMBER ID: #<?php echo htmlspecialchars($member_id); ?></div>
    <h2 class="email-sales-h1">ยินดีต้อนรับสู่ครอบครัว Cat Boutique! 🎉</h2>
    <p class="email-sales-lead">
        เรียน คุณ <strong><?php echo htmlspecialchars($recipient_name); ?></strong><br>
        ขอบคุณที่ร่วมเป็นส่วนหนึ่งของครอบครัวคนรักแมว เพื่อเป็นการต้อนรับสมาชิกใหม่ เราขอมอบ <strong>แพ็กเกจของขวัญต้อนรับมูลค่ารวม 5,500 บาท</strong> ให้คุณเริ่มต้นรับเลี้ยงน้องแมวได้อย่างมีความสุขที่สุด
    </p>
</div>

<!-- Special Voucher Ticket Box -->
<div class="email-voucher-ticket">
    <span style="font-size: 0.8rem; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 1px;">
        ✨ โค้ดส่วนลดพิเศษสำหรับสมาชิกใหม่ ✨
    </span>
    <div class="email-code-big"><?php echo htmlspecialchars($voucher_code); ?></div>
    <div style="font-size: 0.95rem; font-weight: 800; color: #0F172A; margin-bottom: 0.3rem;">
        ลดทันที 15% จากค่าสินสอดน้องแมวทุกสายพันธุ์ในร้าน
    </div>
    <div style="font-size: 0.8rem; color: #10B981; font-weight: 700;">
        ⏰ โค้ดนี้มีอายุ 48 ชั่วโมงนับจากเวลาที่สมัครสมาชิก
    </div>
</div>

<!-- 3 Welcome Perks -->
<div class="email-perks-row">
    <div class="email-perk-box">
        <div class="email-perk-icon">🩺</div>
        <div class="email-perk-title">ฟรี! ตรวจสุขภาพ & ไวรัส</div>
        <div class="email-perk-desc">ตรวจแล็บ FIV/FeLV ลบ 100% ก่อนส่งมอบ (มูลค่า 2,500.-)</div>
    </div>
    <div class="email-perk-box">
        <div class="email-perk-icon">🚐</div>
        <div class="email-perk-title">ฟรี! รถส่งแอร์ปรับอุณหภูมิ</div>
        <div class="email-perk-desc">พี่เลี้ยงดูแลตลอดทาง ส่งฟรีถึงหน้าบ้านทุกจังหวัด (มูลค่า 1,500.-)</div>
    </div>
    <div class="email-perk-box">
        <div class="email-perk-icon">🛡️</div>
        <div class="email-perk-title">การันตีสุขภาพ 180 วัน</div>
        <div class="email-perk-desc">คุ้มครองโรคทางพันธุกรรม พร้อมสัตวแพทย์ดูแล 24 ชม.</div>
    </div>
</div>

<!-- Sales Pitch Story -->
<div style="margin: 2rem 0; padding: 1.2rem; background: #F8FAFC; border-radius: 12px; border-left: 4px solid #FF7556;">
    <h3 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin-bottom: 0.5rem;">
        💡 ทำไมสมาชิกใหม่ทุกคนถึงมั่นใจเมื่อรับเลี้ยงกับเรา?
    </h3>
    <p style="font-size: 0.88rem; color: #475569; line-height: 1.6; margin: 0;">
        น้องแมวทุกตัวที่ Cat Boutique ได้รับการเพาะพันธุ์ในระบบปิดมาตรฐานสากล WCF และ CFA เลี้ยงดูด้วยความรัก อาหารเกรดโฮลิสติก และได้รับการฝึกการใช้กระบะทรายตั้งแต่ 6 สัปดาห์แรก น้องทุกตัวจึงมีนิสัยอ่อนโยน เป็นมิตร และพร้อมเป็นสมาชิกใหม่ที่สร้างรอยยิ้มให้กับครอบครัวของคุณตั้งแต่วันแรกครับ
    </p>
</div>

<!-- Featured Cat Deals for New Members -->
<div class="email-products-heading">
    🐾 น้องแมวสายพันธุ์แนะนำ พร้อมย้ายบ้านทันที
</div>

<div class="email-cat-cards">
    <!-- Cat 1 -->
    <div class="email-cat-item">
        <img src="<?php echo $email_base_url; ?>/assets/images/cat_scottish.jpg" alt="Scottish Fold" class="email-cat-thumb">
        <div class="email-cat-info">
            <div class="email-cat-title">น้องโมจิ (Scottish Fold)</div>
            <div class="email-cat-sub">สก็อตติช โฟลด์ หูพับกลม แก้มยุ้ย ขี้อ้อน</div>
            <div>
                <span class="email-cat-old">22,000 ฿</span>
                <span class="email-cat-price">18,700 ฿</span>
            </div>
            <div style="margin-top: 0.8rem;">
                <a href="<?php echo $email_base_url; ?>/products.php" class="btn btn-primary btn-sm" style="width: 100%; display: block; text-decoration: none; font-size: 0.8rem;">
                    รับเลี้ยงน้องตัวนี้ 🐾
                </a>
            </div>
        </div>
    </div>

    <!-- Cat 2 -->
    <div class="email-cat-item">
        <img src="<?php echo $email_base_url; ?>/assets/images/cat_british.jpg" alt="British Shorthair" class="email-cat-thumb">
        <div class="email-cat-info">
            <div class="email-cat-title">น้องบราวนี่ (British Shorthair)</div>
            <div class="email-cat-sub">บริติช ช็อตแฮร์ โครงสร้างล่ำ ขนแน่นดั่งกำมะหยี่</div>
            <div>
                <span class="email-cat-old">25,000 ฿</span>
                <span class="email-cat-price">21,250 ฿</span>
            </div>
            <div style="margin-top: 0.8rem;">
                <a href="<?php echo $email_base_url; ?>/products.php" class="btn btn-primary btn-sm" style="width: 100%; display: block; text-decoration: none; font-size: 0.8rem;">
                    รับเลี้ยงน้องตัวนี้ 🐾
                </a>
            </div>
        </div>
    </div>

    <!-- Cat 3 -->
    <div class="email-cat-item">
        <img src="<?php echo $email_base_url; ?>/assets/images/cat_ragdoll.jpg" alt="Ragdoll" class="email-cat-thumb">
        <div class="email-cat-info">
            <div class="email-cat-title">น้องสโนว์ (Ragdoll)</div>
            <div class="email-cat-sub">แร็กดอลล์ ตาสีฟ้า ขนนุ่มฟู อุ้มแล้วตัวนิ่ม</div>
            <div>
                <span class="email-cat-old">32,000 ฿</span>
                <span class="email-cat-price">27,200 ฿</span>
            </div>
            <div style="margin-top: 0.8rem;">
                <a href="<?php echo $email_base_url; ?>/products.php" class="btn btn-primary btn-sm" style="width: 100%; display: block; text-decoration: none; font-size: 0.8rem;">
                    รับเลี้ยงน้องตัวนี้ 🐾
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Starter Kit Add-on Banner -->
<div class="email-bundle-banner">
    <div style="font-size: 3rem;">📦</div>
    <div style="flex: 1;">
        <div style="font-weight: 800; font-size: 1.05rem; color: #0F172A;">
            สิทธิ์แลกซื้อพิเศษ: ชุดทาสแมวมือใหม่ (Starter Welcome Pack)
        </div>
        <div style="font-size: 0.85rem; color: #64748B; margin-top: 0.2rem;">
            คอนโดแมว 3 ชั้น + กระบะทรายอัตโนมัติ + ชามน้ำพุกรองไอออน + อาหารโฮลิสติก 5kg + ขนมแมวเลีย 1 โหล
        </div>
        <div style="margin-top: 0.4rem;">
            <span style="text-decoration: line-through; color: #94A3B8; font-size: 0.85rem;">4,990 ฿</span>
            <span style="font-size: 1.25rem; font-weight: 900; color: #10B981; margin-left: 0.4rem;">เพียง 2,490 ฿</span>
            <span style="background: #D1FAE5; color: #065F46; font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 999px; margin-left: 0.4rem;">ลด 50%</span>
        </div>
    </div>
    <div>
        <a href="<?php echo $email_base_url; ?>/welcome_deal.php#bundles-anchor" class="btn btn-secondary btn-sm" style="white-space: nowrap; text-decoration: none;">
            ดูแพ็กเกจนี้ 🛒
        </a>
    </div>
</div>

<!-- Main Call-To-Action Button -->
<div class="email-cta-container">
    <a href="<?php echo $email_base_url; ?>/products.php" class="email-big-btn">
        🛍️ กดรับสิทธิ์ส่วนลด 15% & เลือกน้องแมวทันที 🐾
    </a>
    <div style="font-size: 0.8rem; color: #94A3B8; margin-top: 0.8rem;">
        หรือกดแชทคุยกับสัตวแพทย์และพี่เลี้ยงผ่าน LINE OA: @catboutique (ตลอด 24 ชม.)
    </div>
</div>

<!-- Email Footer -->
<div class="email-footer-box">
    <p style="margin-bottom: 0.4rem;">
        <strong>Cat Boutique Cattery Thailand</strong> • พัฒนาและดูแลโดย Pattanun Kamhongsa (รหัสนักศึกษา 66040233148)<br>
        คณะเทคโนโลยีสารสนเทศ (IT) • ฟาร์มแมวมาตรฐานสากลระบบปิด
    </p>
    <p style="color: #CBD5E1; font-size: 0.72rem; margin-top: 0.6rem;">
        อีเมลฉบับนี้ส่งถึง <?php echo htmlspecialchars($recipient_email); ?> เนื่องจากคุณได้ทำการสมัครสมาชิกบน Cat Boutique<br>
        หากต้องการจัดการการรับข่าวสาร สามารถ <a href="<?php echo $email_base_url; ?>/profile.php" style="color: #94A3B8; text-decoration: underline;">ตั้งค่าโปรไฟล์</a> ได้ตลอดเวลา
    </p>
</div>
