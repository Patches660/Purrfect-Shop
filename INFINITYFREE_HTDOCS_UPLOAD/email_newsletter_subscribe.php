<?php
/**
 * Purrfect Cat Cyber Shop
 * Template 1: Content รูปภาพสำหรับส่ง Email หลังจากลูกค้า Subscribe เพื่อรับข่าวสาร
 * (Theme: Fresh Mint & Cream • Purrfect Weekly Cat Gazette / Magazine Digest)
 * EmailJS Template ID: template_y8wnrlt
 */

require_once __DIR__ . '/data.php';

/**
 * ฟังก์ชันสร้างเนื้อหา HTML Email สำหรับส่งหลังลูกค้า Subscribe รับข่าวสาร
 * สไตล์: Weekly Cat Digest Magazine (โทนเขียวมิ้นต์-ครีม อบอุ่น สดใส เป็นมิตร)
 */
function renderNewsletterSubscribeEmail($recipient_name = 'คนรักน้องแมว', $recipient_email = 'subscriber@example.com', $voucher_code = 'CATNEWS10') {
    $email_base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000');
    $recipient_name = htmlspecialchars($recipient_name);
    $recipient_email = htmlspecialchars($recipient_email);
    $voucher_code = htmlspecialchars($voucher_code);

    // Smart CDN Image Fallback: When running on localhost, use HTTPS CDN for images so Gmail can display them
    $is_local = (strpos($email_base_url, 'localhost') !== false || strpos($email_base_url, '127.0.0.1') !== false);
    $logo_img_url = $is_local ? 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200&auto=format&fit=crop&q=80' : "{$email_base_url}/assets/images/logo.png";
    $cat_scottish_url = $is_local ? 'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=600&auto=format&fit=crop&q=80' : "{$email_base_url}/assets/images/cat_scottish.jpg";
    $cat_british_url = $is_local ? 'https://images.unsplash.com/photo-1592194996308-7b43878e84a6?w=600&auto=format&fit=crop&q=80' : "{$email_base_url}/assets/images/cat_british.jpg";
    $cat_ragdoll_url = $is_local ? 'https://images.unsplash.com/photo-1543852786-1cf6624b9987?w=600&auto=format&fit=crop&q=80' : "{$email_base_url}/assets/images/cat_ragdoll.jpg";

    $issue_date = date('d F Y');

    return <<<HTML
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📬 Purrfect Cat Gazette - ยืนยันการรับข่าวสาร</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F0FDF4; font-family: 'Sukhumvit Set', 'Prompt', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #1E293B;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #F0FDF4; padding: 25px 0 45px 0;">
        <tr>
            <td align="center">
                <!-- Main Email Card: Fresh Mint & Nature Magazine Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 620px; background-color: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 30px rgba(16, 185, 129, 0.12); border: 2px solid #BBF7D0;">
                    
                    <!-- Top Editorial Masthead / Header Bar -->
                    <tr>
                        <td style="background: #064E3B; padding: 12px 25px; border-bottom: 2px solid #10B981;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="left">
                                        <span style="font-size: 11px; font-weight: 800; color: #A7F3D0; letter-spacing: 2px; text-transform: uppercase;">
                                            📰 PURRFECT CAT GAZETTE &bull; WEEKLY DIGEST
                                        </span>
                                    </td>
                                    <td align="right">
                                        <span style="font-size: 11px; font-weight: 700; color: #6EE7B7; letter-spacing: 0.5px;">
                                            ฉบับประจำสัปดาห์ • {$issue_date}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Magazine Cover Hero Header -->
                    <tr>
                        <td align="center" style="background: linear-gradient(180deg, #064E3B 0%, #047857 60%, #059669 100%); padding: 35px 25px 30px 25px; text-align: center; position: relative;">
                            
                            <!-- Stamp Badge -->
                            <div style="display: inline-block; background: rgba(255, 255, 255, 0.15); border: 1.5px dashed #A7F3D0; padding: 5px 16px; border-radius: 999px; margin-bottom: 16px;">
                                <span style="font-size: 11px; font-weight: 800; color: #ECFDF5; letter-spacing: 1.5px; text-transform: uppercase;">
                                    📬 SUBSCRIBER CONFIRMED &bull; ฉบับต้อนรับเพื่อนใหม่
                                </span>
                            </div>

                            <img src="{$logo_img_url}" alt="Purrfect Shop Logo" width="80" height="80" style="display: block; margin: 0 auto 15px auto; border-radius: 50%; border: 3px solid #6EE7B7; box-shadow: 0 6px 20px rgba(0,0,0,0.25); object-fit: cover;">
                            
                            <h1 style="color: #FFFFFF; font-size: 25px; font-weight: 800; margin: 0 0 8px 0; line-height: 1.35; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                ขอบคุณที่ติดตามข่าวสาร Purrfect Shop! 🐾
                            </h1>
                            <p style="color: #D1FAE5; font-size: 14.5px; margin: 0 auto; max-width: 480px; line-height: 1.6;">
                                สวัสดีคุณ <strong>{$recipient_name}</strong> ยินดีต้อนรับสู่คอมมูนิตี้คนรักแมว เราจะคอยส่งไฮไลท์น้องแมวเข้าใหม่และเกร็ดสุขภาพส่งตรงถึงคุณเป็นประจำ
                            </p>
                        </td>
                    </tr>

                    <!-- Distinct Voucher: Craft Paper Style Ticket with Scissors Cut Line -->
                    <tr>
                        <td style="padding: 24px 24px 10px 24px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background: #FFFBEB; border: 2.5px dashed #10B981; border-radius: 16px; text-align: center; padding: 22px 20px; position: relative;">
                                <tr>
                                    <td>
                                        <div style="display: inline-block; background: #ECFDF5; color: #047857; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 999px; border: 1px solid #A7F3D0; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                                            ✂️ GIFT VOUCHER &bull; สิทธิพิเศษสำหรับผู้รับข่าวสาร
                                        </div>
                                        <div style="font-family: 'Consolas', 'Courier New', monospace; font-size: 30px; font-weight: 900; color: #059669; letter-spacing: 4px; margin: 6px 0;">
                                            {$voucher_code}
                                        </div>
                                        <div style="font-size: 15px; font-weight: 800; color: #064E3B; margin-bottom: 4px;">
                                            ลดทันที 10% สำหรับสั่งซื้ออาหาร ขนมนำเข้า และของเล่นน้องแมว
                                        </div>
                                        <div style="font-size: 12px; color: #65A30D; font-weight: 700;">
                                            ✓ ใช้ได้ทั้งบนเว็บไซต์และหน้าร้าน Cat Boutique (ไม่มีขั้นต่ำในการสั่งซื้อ)
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- What Subscribers Get: 3 Editorial Badges -->
                    <tr>
                        <td style="padding: 14px 24px 18px 24px;">
                            <div style="font-size: 15px; font-weight: 800; color: #064E3B; margin-bottom: 12px; text-align: center;">
                                🌿 สิ่งที่คุณจะได้รับจากจดหมายข่าวของเรา:
                            </div>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td width="33.33%" valign="top" style="padding: 4px;">
                                        <div style="background: #F0FDF4; border: 1.5px solid #DCFCE7; border-radius: 12px; padding: 12px 10px; text-align: center;">
                                            <div style="font-size: 24px; margin-bottom: 4px;">🌟</div>
                                            <div style="font-weight: 800; font-size: 12px; color: #065F46; margin-bottom: 2px;">แมวเข้าใหม่ก่อนใคร</div>
                                            <div style="font-size: 11px; color: #475569; line-height: 1.35;">จองน้องแมวรุ่นใหม่ล่วงหน้า 24 ชม.</div>
                                        </div>
                                    </td>
                                    <td width="33.33%" valign="top" style="padding: 4px;">
                                        <div style="background: #F0FDF4; border: 1.5px solid #DCFCE7; border-radius: 12px; padding: 12px 10px; text-align: center;">
                                            <div style="font-size: 24px; margin-bottom: 4px;">🩺</div>
                                            <div style="font-weight: 800; font-size: 12px; color: #065F46; margin-bottom: 2px;">เกร็ดหมอแมว</div>
                                            <div style="font-size: 11px; color: #475569; line-height: 1.35;">สาระสุขภาพแมวจากสัตวแพทย์ทุกสัปดาห์</div>
                                        </div>
                                    </td>
                                    <td width="33.33%" valign="top" style="padding: 4px;">
                                        <div style="background: #F0FDF4; border: 1.5px solid #DCFCE7; border-radius: 12px; padding: 12px 10px; text-align: center;">
                                            <div style="font-size: 24px; margin-bottom: 4px;">⚡</div>
                                            <div style="font-weight: 800; font-size: 12px; color: #065F46; margin-bottom: 2px;">ดีลลับเฉพาะทาสแมว</div>
                                            <div style="font-size: 11px; color: #475569; line-height: 1.35;">โค้ด Flash Sale ลับพิเศษประจำฉบับ</div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Section: New Arrival Kittens Showcase (Polaroid Frame Style) -->
                    <tr>
                        <td style="padding: 16px 24px 20px 24px; border-top: 1px dashed #D1FAE5;">
                            <div style="font-size: 16px; font-weight: 800; color: #064E3B; margin-bottom: 4px; text-align: center;">
                                🐾 ไฮไลท์น้องแมวประจำสัปดาห์ (New Arrivals)
                            </div>
                            <div style="font-size: 12.5px; color: #64748B; text-align: center; margin-bottom: 16px;">
                                น้องๆ ทุกตัวตรวจแล็บสุขภาพเรียบร้อย พร้อมย้ายบ้านหาคุณพ่อคุณแม่ 🏡
                            </div>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <!-- Cat 1: Scottish -->
                                    <td width="32%" valign="top" style="padding: 4px;">
                                        <div style="border: 1.5px solid #E2E8F0; border-radius: 14px; overflow: hidden; background: #FFFFFF; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.04);">
                                            <div style="position: relative;">
                                                <img src="{$cat_scottish_url}" alt="Scottish Fold" width="100%" style="height: 135px; object-fit: cover; display: block;">
                                            </div>
                                            <div style="padding: 10px 8px;">
                                                <span style="display: inline-block; background: #FEF3C7; color: #B45309; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-bottom: 4px;">หูพับเกรดพรีเมียม</span>
                                                <div style="font-weight: 800; font-size: 13px; color: #0F172A;">น้องโมจิ 🍡</div>
                                                <div style="font-size: 11px; color: #64748B; margin-bottom: 6px;">สก็อตติช โฟลด์ หางแน่น</div>
                                                <div style="font-size: 14px; font-weight: 900; color: #059669;">18,700 ฿</div>
                                                <a href="{$email_base_url}/products.php" style="display: inline-block; background: #ECFDF5; color: #047857; text-decoration: none; font-size: 11px; font-weight: 800; padding: 5px 12px; border-radius: 999px; margin-top: 6px; border: 1px solid #A7F3D0;">
                                                    ดูตัวจริง &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Cat 2: British -->
                                    <td width="32%" valign="top" style="padding: 4px;">
                                        <div style="border: 1.5px solid #E2E8F0; border-radius: 14px; overflow: hidden; background: #FFFFFF; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.04);">
                                            <div style="position: relative;">
                                                <img src="{$cat_british_url}" alt="British Shorthair" width="100%" style="height: 135px; object-fit: cover; display: block;">
                                            </div>
                                            <div style="padding: 10px 8px;">
                                                <span style="display: inline-block; background: #EFF6FF; color: #1E40AF; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-bottom: 4px;">แก้มกลมบึ้ก</span>
                                                <div style="font-weight: 800; font-size: 13px; color: #0F172A;">น้องบราวนี่ 🧸</div>
                                                <div style="font-size: 11px; color: #64748B; margin-bottom: 6px;">บริติช ช็อตแฮร์ แท้</div>
                                                <div style="font-size: 14px; font-weight: 900; color: #059669;">21,250 ฿</div>
                                                <a href="{$email_base_url}/products.php" style="display: inline-block; background: #ECFDF5; color: #047857; text-decoration: none; font-size: 11px; font-weight: 800; padding: 5px 12px; border-radius: 999px; margin-top: 6px; border: 1px solid #A7F3D0;">
                                                    ดูตัวจริง &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Cat 3: Ragdoll -->
                                    <td width="32%" valign="top" style="padding: 4px;">
                                        <div style="border: 1.5px solid #E2E8F0; border-radius: 14px; overflow: hidden; background: #FFFFFF; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.04);">
                                            <div style="position: relative;">
                                                <img src="{$cat_ragdoll_url}" alt="Ragdoll" width="100%" style="height: 135px; object-fit: cover; display: block;">
                                            </div>
                                            <div style="padding: 10px 8px;">
                                                <span style="display: inline-block; background: #FDF2F8; color: #9D174D; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-bottom: 4px;">ตาสีฟ้าขนฟู</span>
                                                <div style="font-weight: 800; font-size: 13px; color: #0F172A;">น้องสโนว์ ❄️</div>
                                                <div style="font-size: 11px; color: #64748B; margin-bottom: 6px;">แร็กดอลล์ บลูไบคัลเลอร์</div>
                                                <div style="font-size: 14px; font-weight: 900; color: #059669;">27,200 ฿</div>
                                                <a href="{$email_base_url}/products.php" style="display: inline-block; background: #ECFDF5; color: #047857; text-decoration: none; font-size: 11px; font-weight: 800; padding: 5px 12px; border-radius: 999px; margin-top: 6px; border: 1px solid #A7F3D0;">
                                                    ดูตัวจริง &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Veterinary Column Card -->
                    <tr>
                        <td style="padding: 10px 24px 20px 24px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background: #F0FDF4; border: 1.5px solid #86EFAC; border-radius: 14px; padding: 18px;">
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                                            <span style="font-size: 18px;">🩺</span>
                                            <span style="font-size: 13px; font-weight: 800; color: #065F46; text-transform: uppercase;">
                                                คอลัมน์สุขภาพจากคุณหมอ &bull; Dr. Purrfect Advice
                                            </span>
                                        </div>
                                        <div style="font-size: 15px; font-weight: 800; color: #0F172A; margin-bottom: 8px;">
                                            "5 สัญญาณลับที่บอกว่าน้องแมวกำลังมีความสุขและไว้ใจคุณสุดๆ"
                                        </div>
                                        <div style="font-size: 12.5px; color: #334155; line-height: 1.65;">
                                            🐾 <strong>หางตั้งตรงปลายงอนิดๆ:</strong> กำลังอารมณ์ดีและตื่นเต้นที่เจอคุณ<br>
                                            🐾 <strong>เสียงเพอร์ (Purring) สั่นในลำคอ:</strong> รู้สึกปลอดภัยและอบอุ่นใจมาก<br>
                                            🐾 <strong>นอนหงายโชว์พุงกลม:</strong> แสดงถึงความไว้วางใจขั้นสูงสุด<br>
                                            🐾 <strong>เอาหัวหรือแก้มมาถูไถ (Bunting):</strong> ปล่อยฟีโรโมนตีตราว่าคุณคือครอบครัว<br>
                                            🐾 <strong>กะพริบตาให้ช้าๆ (Slow Blink):</strong> สัญญาณบอกรักในภาษาน้องแมว
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Fun Cat Trivia Box -->
                    <tr>
                        <td style="padding: 0 24px 22px 24px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background: #FEF3C7; border: 1.5px solid #FDE68A; border-radius: 12px; padding: 14px 18px;">
                                <tr>
                                    <td>
                                        <div style="font-size: 12px; font-weight: 800; color: #92400E; margin-bottom: 3px;">
                                            💡 รู้หรือไม่? (Cat Fun Fact of the Week)
                                        </div>
                                        <div style="font-size: 12px; color: #78350F; line-height: 1.5;">
                                            น้องแมวใช้เวลาทำความสะอาดร่างกาย (Grooming) มากถึง <strong>30-50%</strong> ของเวลาที่ตื่นในแต่ละวัน เพื่อช่วยระบายความร้อนและสร้างความผ่อนคลาย!
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Call To Action Button -->
                    <tr>
                        <td align="center" style="padding: 5px 24px 28px 24px; text-align: center;">
                            <a href="{$email_base_url}/products.php" style="display: inline-block; background: #059669; color: #FFFFFF; font-size: 15px; font-weight: 800; text-decoration: none; padding: 13px 34px; border-radius: 999px; box-shadow: 0 4px 15px rgba(5, 150, 105, 0.35);">
                                🐾 เลือกชมน้องแมวและช้อปปิ้งของใช้
                            </a>
                            <div style="font-size: 11.5px; color: #64748B; margin-top: 10px;">
                                จัดส่งรถตู้แอร์ปรับอุณหภูมิฟรีทั่วประเทศ &bull; ปรึกษาสัตวแพทย์ฟรีตลอดชีพ
                            </div>
                        </td>
                    </tr>

                    <!-- Editorial Footer -->
                    <tr>
                        <td style="background: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 22px 24px; text-align: center;">
                            <div style="font-size: 12px; font-weight: 800; color: #064E3B; margin-bottom: 4px;">
                                Purrfect Cattery & Boutique 🐾 &bull; Cat Gazette Editorial Team
                            </div>
                            <div style="font-size: 11px; color: #64748B; line-height: 1.6; margin-bottom: 10px;">
                                คุณได้รับอีเมลฉบับนี้เนื่องจากได้กด Subscribe รับข่าวสารทางอีเมล: <strong>{$recipient_email}</strong><br>
                                หากไม่ต้องการรับจดหมายข่าว สามารถกดปรับแต่งความถี่หรือ <a href="{$email_base_url}/subscribe.php" style="color: #059669; text-decoration: underline;">ยกเลิกรับข่าวสาร (Unsubscribe)</a> ได้ตลอดเวลา
                            </div>
                            <div style="font-size: 10.5px; color: #94A3B8;">
                                &copy; 2026 Purrfect Shop. พัฒนาโดย Purrfect Cattery Team 🐾
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
HTML;
}

// Standalone Web Preview Router
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $currUser = getCurrentUser();
    $recipient_name = trim($_GET['name'] ?? ($currUser['fullname'] ?? 'คุณโอภาส (Subscriber)'));
    $recipient_email = trim($_GET['email'] ?? ($currUser['email'] ?? 'oavatan@gmail.com'));
    $voucher_code = trim($_GET['code'] ?? 'CATNEWS10');
    $current_view = $_GET['view'] ?? 'desktop';

    $success_msg = "";
    $error_msg = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_test_subscriber_email') {
        $target_email = trim($_POST['target_email'] ?? $recipient_email);
        $target_name = trim($_POST['target_name'] ?? $recipient_name);

        if (empty($target_email) || !filter_var($target_email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "กรุณากรอกอีเมลที่ถูกต้องสำหรับทดสอบ";
        } else {
            $html_content = renderNewsletterSubscribeEmail($target_name, $target_email, $voucher_code);
            $subject = "📬 [ยืนยันการรับข่าวสาร] Purrfect Cat Gazette + โค้ดส่วนลด 10% ({$voucher_code})";

            $res = sendActualEmail($target_email, $target_name, $subject, $html_content, $voucher_code, '', [], 'subscribe');
            if (!empty($res['delivery_status']) && $res['delivery_status'] === 'DELIVERED_VIA_EMAILJS') {
                $success_msg = "🎉 ยิงส่งอีเมลข่าวสารไปยัง {$target_email} ผ่าน EmailJS (Template: template_y8wnrlt) สำเร็จจริง 100%! ตรวจสอบได้ที่ Inbox หรือ Spam ของคุณทันที";
            } elseif (!empty($res['smtp_result']) && $res['smtp_result']['success']) {
                $success_msg = "🎉 ส่งอีเมลข่าวสารไปยัง {$target_email} ผ่าน Live SMTP สำเร็จจริง 100%! ตรวจสอบได้ที่ Inbox ของคุณทันที";
            } elseif (!empty($res['smtp_result']) && !$res['smtp_result']['success']) {
                $error_msg = "❌ ส่งผ่าน SMTP ล้มเหลว: " . $res['smtp_result']['message'] . " (แต่ได้บันทึกลง Outbox จำลองแล้ว)";
            } elseif (!empty($res['delivery_status']) && $res['delivery_status'] === 'EMAILJS_FAILED') {
                $error_msg = "❌ ส่งผ่าน EmailJS ล้มเหลว: " . ($res['server_notice'] ?? '') . " (บันทึกลง Outbox จำลองแล้ว)";
            } else {
                $success_msg = "✓ บันทึกการส่งในโหมด Outbox จำลองสำเร็จ! (เปิดใช้งาน EmailJS หรือ SMTP เพื่อส่งเข้ากล่องจดหมายจริง)";
            }
        }
    }

    $rendered_email_html = renderNewsletterSubscribeEmail($recipient_name, $recipient_email, $voucher_code);
    require_once __DIR__ . '/header.php';
?>
<div style="max-width: 1080px; margin: 2rem auto; padding: 0 1rem;">
    <div style="background: var(--bg-card); border-radius: 16px; border: 1.5px solid var(--border-color); padding: 1.5rem; margin-bottom: 2rem; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <span style="display: inline-block; background: #ECFDF5; color: #047857; font-size: 0.82rem; font-weight: 800; padding: 4px 12px; border-radius: 999px; border: 1px solid #A7F3D0; margin-bottom: 4px;">
                    📬 Template 1: Newsletter Subscribe &bull; EmailJS: template_y8wnrlt
                </span>
                <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                    พรีวิวแม่แบบอีเมล: สำหรับส่งหลังลูกค้า Subscribe รับข่าวสาร
                </h2>
                <p style="color: var(--text-muted); font-size: 0.88rem; margin: 4px 0 0 0;">
                    ธีม <strong>Fresh Mint & Gazette Digest</strong> ออกแบบเฉพาะสำหรับข่าวสารและเกร็ดสุขภาพแมว
                </p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="?view=desktop" class="btn btn-sm <?php echo $current_view === 'desktop' ? 'btn-primary' : 'btn-secondary'; ?>">🖥️ Desktop</a>
                <a href="?view=mobile" class="btn btn-sm <?php echo $current_view === 'mobile' ? 'btn-primary' : 'btn-secondary'; ?>">📱 Mobile</a>
                <a href="?view=raw" class="btn btn-sm <?php echo $current_view === 'raw' ? 'btn-primary' : 'btn-secondary'; ?>">📄 Raw HTML</a>
                <a href="email_member_welcome.php" class="btn btn-sm" style="background: #1E293B; color: #FCD34D; font-weight: 800; border: 1px solid #F59E0B;">
                    👑 สลับไปดูแม่แบบสมาชิก VIP &rarr;
                </a>
            </div>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div style="background: #ECFDF5; border: 1.5px solid #10B981; border-radius: 10px; padding: 0.8rem 1.2rem; color: #065F46; font-weight: 700; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($success_msg); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div style="background: #FEF2F2; border: 1.5px solid #EF4444; border-radius: 10px; padding: 0.8rem 1.2rem; color: #991B1B; font-weight: 700; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <!-- Quick Test Send Form -->
        <form method="POST" action="email_newsletter_subscribe.php?view=<?php echo htmlspecialchars($current_view); ?>" style="background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px; padding: 1rem; display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
            <input type="hidden" name="action" value="send_test_subscriber_email">
            <div style="flex: 1; min-width: 220px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">📬 อีเมลสำหรับทดสอบส่งจริง:</label>
                <input type="email" name="target_email" value="<?php echo htmlspecialchars($recipient_email); ?>" required style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-weight: 700;">
            </div>
            <div style="min-width: 160px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">👤 ชื่อผู้รับ:</label>
                <input type="text" name="target_name" value="<?php echo htmlspecialchars($recipient_name); ?>" required style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main);">
            </div>
            <button type="submit" class="btn btn-primary" style="background: #059669; border-color: #047857; font-weight: 800; padding: 8px 18px;">
                ⚡ ยิงส่งทดสอบเข้าอีเมลจริง
            </button>
        </form>
    </div>

    <!-- Preview Container -->
    <div style="background: #E2E8F0; padding: 2rem 1rem; border-radius: 16px; text-align: center;">
        <?php if ($current_view === 'mobile'): ?>
            <div style="width: 375px; margin: 0 auto; background: #FFFFFF; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); overflow: hidden; border: 8px solid #1E293B;">
                <iframe srcdoc="<?php echo htmlspecialchars($rendered_email_html); ?>" style="width: 100%; height: 680px; border: none;"></iframe>
            </div>
        <?php elseif ($current_view === 'raw'): ?>
            <pre style="text-align: left; background: #0F172A; color: #F8FAFC; padding: 1.5rem; border-radius: 12px; overflow-x: auto; font-size: 0.82rem; font-family: monospace; max-height: 600px;"><?php echo htmlspecialchars($rendered_email_html); ?></pre>
        <?php else: ?>
            <div style="max-width: 680px; margin: 0 auto; background: #FFFFFF; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden;">
                <iframe srcdoc="<?php echo htmlspecialchars($rendered_email_html); ?>" style="width: 100%; height: 800px; border: none;"></iframe>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
    require_once __DIR__ . '/footer.php';
}
