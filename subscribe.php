<?php
require_once __DIR__ . '/data.php';

$success = false;
$error = '';
$subscriber_email = '';
$subscriber_name = '';

$currUser = getCurrentUser();
if ($currUser) {
    $subscriber_name = $currUser['fullname'] ?? $currUser['username'] ?? '';
    $subscriber_email = $currUser['email'] ?? '';
}

if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST') {
    $subscriber_email = trim($_POST['subscriber_email'] ?? '');
    $subscriber_name = trim($_POST['subscriber_name'] ?? '');

    if (empty($subscriber_name)) {
        $parts = explode('@', $subscriber_email);
        $subscriber_name = !empty($parts[0]) ? 'คุณ' . $parts[0] : 'คนรักน้องแมว';
    }

    if (empty($subscriber_email) || !filter_var($subscriber_email, FILTER_VALIDATE_EMAIL)) {
        $error = "กรุณากรอกที่อยู่อีเมลให้ถูกต้อง (เช่น oavatan@gmail.com)";
    } else {
        // Send actual email via EmailJS (template_y8wnrlt) or active driver
        $res = sendNewsletterSubscribeEmail($subscriber_email, $subscriber_name);
        
        // Log to newsletters file
        $newsletters = getNewsletters();
        $subEntry = [
            'id' => 'sub_' . uniqid(),
            'type' => 'subscriber_direct',
            'target_email' => $subscriber_email,
            'target_name' => $subscriber_name,
            'title' => '📬 ยืนยันการรับข่าวสาร (โดยไม่ต้องสมัครสมาชิก)',
            'message' => 'ผู้ใช้ได้ลงทะเบียนรับจดหมายข่าวและคูปองส่วนลด 10% (CATNEWS10)',
            'voucher_code' => 'CATNEWS10',
            'sent_at' => date('Y-m-d H:i:s')
        ];
        array_unshift($newsletters, $subEntry);
        file_put_contents(NEWSLETTERS_FILE, json_encode($newsletters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $success = true;
    }
}

$page_title = "รับข้อมูลข่าวสาร & ดีลพิเศษน้องแมว 🐾 - Purrfect Shop";
require_once __DIR__ . '/header.php';
?>

<div style="max-width: 860px; margin: 3rem auto; padding: 0 1rem;">
    <?php if ($success): ?>
        <!-- Success State Card -->
        <div style="background: var(--bg-card); border-radius: 20px; border: 2px solid #10B981; padding: 3rem 2rem; text-align: center; box-shadow: var(--shadow-lg);">
            <div style="font-size: 4.5rem; margin-bottom: 1rem; animation: bounce 1s infinite alternate;">🎉</div>
            <span style="background: #ECFDF5; color: #065F46; font-size: 0.88rem; font-weight: 800; padding: 6px 18px; border-radius: 999px; border: 1px solid #A7F3D0; display: inline-block; margin-bottom: 1rem;">
                ✓ ลงทะเบียนรับข่าวสารสำเร็จเรียบร้อย
            </span>
            <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.8rem;">
                ขอบคุณที่ติดตามข่าวสาร Purrfect Shop! 🐾
            </h1>
            <p style="font-size: 1.05rem; color: var(--text-secondary); max-width: 580px; margin: 0 auto 2rem auto; line-height: 1.6;">
                เราได้จัดส่งจดหมายต้อนรับพร้อมโค้ดส่วนลด 10% ไปยังอีเมล <strong><?php echo htmlspecialchars($subscriber_email); ?></strong> เรียบร้อยแล้ว (ตรวจสอบได้ที่กล่องข้อความ Inbox หรือ Spam ของคุณ)
            </p>

            <!-- Voucher Ticket Card -->
            <div style="max-width: 440px; margin: 0 auto 2.5rem auto; background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%); border: 2px dashed #F97316; border-radius: 16px; padding: 1.6rem; text-align: center; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.15);">
                <div style="font-size: 0.82rem; font-weight: 800; color: #C2410C; letter-spacing: 1px; text-transform: uppercase;">
                    🎁 โค้ดส่วนลดพิเศษสำหรับ Subscriber
                </div>
                <div style="font-family: 'Consolas', monospace; font-size: 2.2rem; font-weight: 900; color: #EA580C; letter-spacing: 4px; margin: 10px 0;">
                    CATNEWS10
                </div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0F172A; margin-bottom: 6px;">
                    ลดทันที 10% สำหรับอาหาร ของเล่น และอุปกรณ์น้องแมว
                </div>
                <div style="font-size: 0.8rem; color: #16A34A; font-weight: 700;">
                    ✓ ไม่มีขั้นต่ำ • ใช้สั่งซื้อได้ทันทีบนเว็บไซต์
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="products.php" class="btn btn-primary" style="padding: 12px 28px; font-weight: 800; font-size: 1rem;">
                    🐱 เลือกชมน้องแมวและสินค้า &rarr;
                </a>
                <a href="index.php" class="btn btn-secondary" style="padding: 12px 24px; font-weight: 700;">
                    🏠 กลับหน้าหลัก
                </a>
                <a href="register.php" class="btn" style="background: rgba(255, 117, 86, 0.15); color: var(--primary-coral); border: 1.5px solid var(--primary-coral); padding: 12px 24px; font-weight: 800; border-radius: 999px;">
                    ✨ อัปเกรดเป็นสมาชิก VIP (รับลด 15%)
                </a>
            </div>
        </div>

    <?php else: ?>

        <!-- Main Subscribe Card -->
        <div style="background: var(--bg-card); border-radius: 24px; border: 1.5px solid var(--border-color); overflow: hidden; box-shadow: var(--shadow-lg);">
            <!-- Card Header Banner -->
            <div style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); padding: 3rem 2rem 2.5rem 2rem; text-align: center; position: relative; border-bottom: 4px solid #FF7556;">
                <div style="display: inline-block; background: rgba(255, 117, 86, 0.2); color: #FFA48E; font-size: 0.85rem; font-weight: 800; letter-spacing: 1px; padding: 6px 18px; border-radius: 999px; border: 1px solid rgba(255, 117, 86, 0.4); margin-bottom: 1rem;">
                    📬 รับข่าวสารฟรี • ยังไม่ต้องสมัครสมาชิก (No Registration Required)
                </div>
                <h1 style="color: #FFFFFF; font-size: 2.2rem; font-weight: 800; margin: 0 0 0.8rem 0; line-height: 1.3;">
                    รับข้อมูลข่าวสาร & ดีลลับน้องแมว 🐾
                </h1>
                <p style="color: #94A3B8; font-size: 1.05rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                    ติดตามความเคลื่อนไหวน้องแมวเข้าใหม่ เกร็ดความรู้จากสัตวแพทย์ และดีล Flash Sale สุดคุ้ม โดยยังไม่ต้องสมัครสมาชิกเต็มรูปแบบ
                </p>
            </div>

            <!-- Card Body Content -->
            <div style="padding: 2.5rem 2rem;">
                <?php if (!empty($error)): ?>
                    <div style="background: #FEF2F2; border: 1.5px solid #EF4444; border-radius: 12px; padding: 1rem 1.2rem; margin-bottom: 1.5rem; color: #991B1B; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                        <span>⚠️</span> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- 3 Highlights / Pillars -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 2.2rem;">
                    <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: 14px; padding: 1.2rem 1rem; text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 6px;">🌟</div>
                        <strong style="display: block; font-size: 0.95rem; color: var(--text-main); margin-bottom: 4px;">
                            แมวเข้าใหม่ก่อนใคร 24 ชม.
                        </strong>
                        <div style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.4;">
                            แจ้งเตือนน้องแมวสายพันธุ์แท้รุ่นใหม่ ให้คุณได้เลือกชมก่อนใคร
                        </div>
                    </div>

                    <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: 14px; padding: 1.2rem 1rem; text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 6px;">🩺</div>
                        <strong style="display: block; font-size: 0.95rem; color: var(--text-main); margin-bottom: 4px;">
                            เกร็ดสุขภาพจากสัตวแพทย์
                        </strong>
                        <div style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.4;">
                            วิธีดูแลโภชนาการ การป้องกันโรค และพฤติกรรมเจ้าเหมียวส่งตรงทุกสัปดาห์
                        </div>
                    </div>

                    <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: 14px; padding: 1.2rem 1rem; text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 6px;">🎁</div>
                        <strong style="display: block; font-size: 0.95rem; color: var(--text-main); margin-bottom: 4px;">
                            รับโค้ดลดทันที 10%
                        </strong>
                        <div style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.4;">
                            รับโค้ด <strong>CATNEWS10</strong> นำไปใช้ซื้อสินค้าและอุปกรณ์ได้ทันที
                        </div>
                    </div>
                </div>

                <!-- Simple Subscription Form -->
                <div style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.04) 0%, rgba(255, 117, 86, 0.06) 100%); border: 2px dashed var(--primary-coral); border-radius: 18px; padding: 2rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin: 0 0 1rem 0; text-align: center;">
                        กรอกอีเมลเพื่อเริ่มรับข่าวสาร & โค้ดส่วนลด 🐾
                    </h3>

                    <form method="POST" action="subscribe.php">
                        <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 14px; margin-bottom: 1.2rem;">
                            <div>
                                <label for="subscriber_name" style="display: block; font-weight: 700; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
                                    👤 ชื่อของคุณ (ไม่บังคับ):
                                </label>
                                <input type="text" id="subscriber_name" name="subscriber_name" value="<?php echo htmlspecialchars($subscriber_name); ?>" placeholder="เช่น คุณโอภาส หรือ ทาสแมว" style="width: 100%; padding: 12px 16px; border-radius: 10px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-size: 0.95rem;">
                            </div>

                            <div>
                                <label for="subscriber_email" style="display: block; font-weight: 700; font-size: 0.88rem; color: var(--text-main); margin-bottom: 6px;">
                                    📬 อีเมลที่จะรับข่าวสาร: <span style="color: #EF4444;">*</span>
                                </label>
                                <input type="email" id="subscriber_email" name="subscriber_email" value="<?php echo htmlspecialchars($subscriber_email); ?>" required placeholder="เช่น oavatan@gmail.com" style="width: 100%; padding: 12px 16px; border-radius: 10px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-size: 0.95rem; font-weight: 700;">
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">
                                <input type="checkbox" name="consent" checked required style="width: 18px; height: 18px; accent-color: var(--primary-coral); margin-top: 2px;">
                                <span>ยินยอมรับข่าวสาร โปรโมชัน และบทความสาระความรู้เกี่ยวกับน้องแมวจาก Purrfect Shop (สามารถยกเลิกได้ตลอดเวลาโดยไม่มีเงื่อนไข)</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px 20px; font-size: 1.1rem; font-weight: 800; border-radius: 12px; box-shadow: 0 4px 18px rgba(255, 117, 86, 0.4); display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <span>📬 ยืนยันรับข้อมูลข่าวสาร & รับโค้ดส่วนลด 10% ✨</span>
                        </button>
                    </form>
                </div>

                <!-- Footer Callout: Member Registration Alternative -->
                <div style="margin-top: 2rem; padding: 1.2rem; background: var(--bg-card-subtle); border-radius: 14px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                    <div>
                        <strong style="color: var(--text-main); font-size: 0.95rem; display: block; margin-bottom: 2px;">
                            💡 ต้องการสิทธิพิเศษที่เหนือกว่าใช่ไหม?
                        </strong>
                        <div style="font-size: 0.84rem; color: var(--text-muted);">
                            สมัครสมาชิกเต็มรูปแบบเพื่อรับส่วนลด 15% (WELCOME15), บัตรสมาชิก VIP ดิจิทัล และสะสมแต้มทุกการสั่งซื้อ
                        </div>
                    </div>
                    <a href="register.php" class="btn btn-secondary btn-sm" style="padding: 8px 18px; font-weight: 800; font-size: 0.85rem; white-space: nowrap;">
                        ✨ สมัครสมาชิกเต็มรูปแบบ
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
