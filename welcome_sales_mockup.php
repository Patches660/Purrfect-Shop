<?php
require_once __DIR__ . '/data.php';

// Security check: Only Admin can access this page
if (!isAdmin()) {
    require_once __DIR__ . '/header.php';
    ?>
    <div style="max-width: 600px; margin: 4rem auto; text-align: center; background: var(--bg-card); padding: 3rem 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-md);">
        <div style="font-size: 4rem; margin-bottom: 1rem;">🔒</div>
        <h2 style="color: #EF4444; font-size: 1.5rem; margin-bottom: 0.8rem;">จำกัดสิทธิ์เฉพาะผู้ดูแลระบบ (Admin Only)</h2>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem; line-height: 1.6;">
            หน้า <strong>ส่งข้อมูล (Email & Content Dispatcher)</strong> นี้สงวนไว้เฉพาะสำหรับผู้ดูแลระบบ (Admin) เท่านั้น สมาชิกทั่วไปและผู้เข้าชมเว็บไซต์ไม่สามารถเข้าถึงได้<br>
            กรุณาเข้าสู่ระบบด้วยบัญชีผู้ดูแลระบบ เช่น <strong>admin</strong> (รหัสผ่าน <strong>admin123</strong>) หรือ <strong>Meow</strong>
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="login.php" class="btn btn-primary">เข้าสู่ระบบด้วยบัญชี Admin 🔑</a>
            <a href="index.php" class="btn btn-secondary">กลับหน้าหลัก 🏠</a>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/footer.php';
    exit;
}

$currUser = getCurrentUser();
$all_users = getUsers();
$all_outbox = getMailOutbox();
$smtp_config = getSmtpConfig();
$emailjs_config = getEmailJsConfig();
$active_driver = getActiveMailDriver();

$success_msg = "";
$error_msg = "";
$smtp_test_logs = [];
$emailjs_test_logs = [];

// -------------------------------------------------------------
// POST Handler 1: Save SMTP Settings
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_smtp_config') {
    $enabled = !empty($_POST['smtp_enabled']) ? true : false;
    $host = trim($_POST['smtp_host'] ?? 'smtp.gmail.com');
    $port = intval($_POST['smtp_port'] ?? 587);
    $encryption = trim($_POST['smtp_encryption'] ?? 'tls');
    $username = trim($_POST['smtp_username'] ?? '');
    $password = trim($_POST['smtp_password'] ?? '');
    $from_email = trim($_POST['smtp_from_email'] ?? $username);
    $from_name = trim($_POST['smtp_from_name'] ?? 'Purrfect Cattery & Boutique 🐾');

    $savedConfig = saveSmtpConfig([
        'enabled' => $enabled,
        'host' => $host,
        'port' => $port,
        'encryption' => $encryption,
        'username' => $username,
        'password' => $password,
        'from_email' => $from_email,
        'from_name' => $from_name,
        'last_status' => $enabled ? 'เปิดใช้งานส่งจริง (พร้อมส่งเข้าอีเมล)' : 'ปิดโหมดส่งจริง (ใช้งาน Outbox จำลอง)'
    ]);
    $smtp_config = $savedConfig;

    if ($enabled && (empty($username) || empty($password))) {
        $error_msg = "⚠️ บันทึกแล้ว แต่คุณเปิดใช้งานส่งจริงโดยยังไม่ได้กรอก Username หรือ App Password กรุณากรอกรหัสผ่านแอป 16 หลักเพื่อให้ระบบส่งเข้าอีเมลจริงได้";
    } else {
        $success_msg = "✓ บันทึกการตั้งค่า SMTP เรียบร้อยแล้ว! " . ($enabled ? "สถานะ: 🟢 พร้อมส่งเข้าอีเมลจริง (" . htmlspecialchars($host . ':' . $port) . ")" : "สถานะ: 🟡 ใช้งาน Outbox จำลองในระบบ");
    }
}

// -------------------------------------------------------------
// POST Handler 2: Test Live SMTP Email Delivery
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_smtp_email') {
    $test_email = trim($_POST['test_email'] ?? '');
    $test_name = trim($_POST['test_name'] ?? 'ผู้ทดสอบระบบ');

    if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "กรุณาระบุที่อยู่อีเมลสำหรับทดสอบให้ถูกต้อง (เช่น yourname@gmail.com)";
    } else {
        $cfg = getSmtpConfig();
        // Allow live overrides from test form if provided
        if (!empty($_POST['smtp_host'])) $cfg['host'] = trim($_POST['smtp_host']);
        if (!empty($_POST['smtp_port'])) $cfg['port'] = intval($_POST['smtp_port']);
        if (!empty($_POST['smtp_encryption'])) $cfg['encryption'] = trim($_POST['smtp_encryption']);
        if (!empty($_POST['smtp_username'])) $cfg['username'] = trim($_POST['smtp_username']);
        if (!empty($_POST['smtp_password'])) $cfg['password'] = trim($_POST['smtp_password']);
        if (!empty($_POST['smtp_from_email'])) $cfg['from_email'] = trim($_POST['smtp_from_email']);
        if (!empty($_POST['smtp_from_name'])) $cfg['from_name'] = trim($_POST['smtp_from_name']);

        if (empty($cfg['username']) || empty($cfg['password'])) {
            $error_msg = "❌ ไม่สามารถทดสอบส่งได้: ยังไม่ได้กรอก Username ผู้ส่ง หรือ Password (Google App Password 16 หลัก) ด้านบน กรุณากรอกข้อมูลให้ครบถ้วนก่อนกดทดสอบส่ง";
        } else {
            // Render email
            require_once __DIR__ . '/email_member_welcome.php';
            $rendered_email = renderMemberWelcomeEmail($test_name, $test_email, 'TEST-WELCOME15');

            $subject = "🧪 [ทดสอบการส่งจริง] ต้อนรับสู่ Purrfect Shop โดยระบบ Live SMTP Mailer";

            $res = SmtpMailer::send($test_email, $test_name, $subject, $rendered_email, $cfg);
            $smtp_test_logs = $res['logs'] ?? [];

            if ($res['success']) {
                $success_msg = "🎉 ส่งอีเมลทดสอบไปยัง {$test_email} สำเร็จจริง 100%! ตรวจสอบได้ที่กล่องจดหมาย Inbox หรือ Spam ของคุณทันที (" . htmlspecialchars($res['response'] ?? '250 OK') . ")";
                saveSmtpConfig([
                    'last_tested' => date('Y-m-d H:i:s'),
                    'last_status' => 'ส่งสำเร็จ: ' . ($res['response'] ?? '250 OK')
                ]);
                $smtp_config = getSmtpConfig();
            } else {
                $error_msg = "❌ การเชื่อมต่อ/ส่งเข้าอีเมลจริงล้มเหลว: " . $res['message'];
                saveSmtpConfig([
                    'last_tested' => date('Y-m-d H:i:s'),
                    'last_status' => 'ผิดพลาด: ' . $res['message']
                ]);
                $smtp_config = getSmtpConfig();
            }
        }
    }
}


// -------------------------------------------------------------
// POST Handler 2.1: Save EmailJS Settings (Dual Templates Supported)
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_emailjs_config') {
    $enabled = !empty($_POST['emailjs_enabled']);
    $service_id = trim($_POST['emailjs_service_id'] ?? '');
    $template_welcome = trim($_POST['emailjs_template_welcome'] ?? ($_POST['emailjs_template_id'] ?? 'template_xt7cq3g'));
    $template_subscribe = trim($_POST['emailjs_template_subscribe'] ?? 'template_y8wnrlt');
    $template_id = $template_welcome;
    $public_key = trim($_POST['emailjs_public_key'] ?? '');
    $private_key = trim($_POST['emailjs_private_key'] ?? '');
    $from_name = trim($_POST['emailjs_from_name'] ?? 'Purrfect Cattery & Boutique 🐾');
    $from_email = trim($_POST['emailjs_from_email'] ?? 'purrfect.cattery.shop@gmail.com');

    // Order Confirmation (EmailJS Account #2)
    $order_enabled = !empty($_POST['emailjs_order_enabled']);
    $order_service_id = trim($_POST['emailjs_order_service_id'] ?? '');
    $order_template_id = trim($_POST['emailjs_order_template_id'] ?? 'template_pt8dolh');
    $order_public_key = trim($_POST['emailjs_order_public_key'] ?? '');
    $order_from_name = trim($_POST['emailjs_order_from_name'] ?? 'Purrfect Shop — ยืนยันคำสั่งซื้อ 🐾');
    $order_from_email = trim($_POST['emailjs_order_from_email'] ?? '');

    $savedConfig = saveEmailJsConfig([
        'enabled' => $enabled,
        'service_id' => $service_id,
        'template_id' => $template_welcome,
        'template_welcome' => $template_welcome,
        'template_subscribe' => $template_subscribe,
        'public_key' => $public_key,
        'private_key' => $private_key,
        'from_name' => $from_name,
        'from_email' => $from_email,
        'order_enabled' => $order_enabled,
        'order_service_id' => $order_service_id,
        'order_template_id' => $order_template_id,
        'order_public_key' => $order_public_key,
        'order_from_name' => $order_from_name,
        'order_from_email' => $order_from_email,
        'last_status' => $enabled ? 'เปิดใช้งานส่งจริงผ่าน EmailJS REST API' : 'ปิดโหมดส่งจริง EmailJS'
    ]);
    $emailjs_config = $savedConfig;
    $active_driver = getActiveMailDriver();

    if ($enabled && (empty($service_id) || empty($template_welcome) || empty($public_key))) {
        $error_msg = "⚠️ บันทึกแล้ว แต่ยังกรอกข้อมูล EmailJS ไม่ครบถ้วน (ต้องการ Service ID, Template ID และ Public Key เพื่อให้ส่งออกจริงได้)";
    } else {
        $success_msg = "✓ บันทึกการตั้งค่า EmailJS เรียบร้อยแล้ว! (Welcome: {$template_welcome} | Subscribe: {$template_subscribe}) " . ($enabled ? "สถานะ: ⚡ พร้อมส่งจริงผ่าน EmailJS REST API" : "สถานะ: 🟡 ปิดโหมดส่งจริง EmailJS");
    }
}

// -------------------------------------------------------------
// POST Handler 2.2: Test Live EmailJS Delivery (Supports Both Templates)
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_emailjs_email') {
    $test_email = trim($_POST['test_email'] ?? '');
    $test_name = trim($_POST['test_name'] ?? 'ผู้ทดสอบ EmailJS');
    // Ensure we check ui_template_choice first (native radio selection) then test_template_type
    $raw_choice = trim($_POST['ui_template_choice'] ?? ($_POST['test_template_type'] ?? 'welcome'));
    if ($raw_choice === 'order') {
        $test_template_type = 'order';
    } elseif ($raw_choice === 'subscribe') {
        $test_template_type = 'subscribe';
    } else {
        $test_template_type = 'welcome';
    }

    $cfg = getEmailJsConfig();
    if (!empty($_POST['emailjs_service_id'])) $cfg['service_id'] = trim($_POST['emailjs_service_id']);
    if (!empty($_POST['emailjs_template_welcome'])) $cfg['template_welcome'] = trim($_POST['emailjs_template_welcome']);
    if (!empty($_POST['emailjs_template_subscribe'])) $cfg['template_subscribe'] = trim($_POST['emailjs_template_subscribe']);
    if (!empty($_POST['emailjs_public_key'])) $cfg['public_key'] = trim($_POST['emailjs_public_key']);
    if (!empty($_POST['emailjs_private_key'])) $cfg['private_key'] = trim($_POST['emailjs_private_key']);
    if (!empty($_POST['emailjs_from_name'])) $cfg['from_name'] = trim($_POST['emailjs_from_name']);
    if (!empty($_POST['emailjs_from_email'])) $cfg['from_email'] = trim($_POST['emailjs_from_email']);

    // Select Active Template ID & Account credentials
    if ($test_template_type === 'subscribe') {
        $cfg['template_id'] = !empty($cfg['template_subscribe']) ? $cfg['template_subscribe'] : 'template_y8wnrlt';
    } elseif ($test_template_type === 'order') {
        if (!empty($cfg['order_service_id'])) $cfg['service_id'] = $cfg['order_service_id'];
        $cfg['template_id'] = !empty($cfg['order_template_id']) ? $cfg['order_template_id'] : 'template_pt8dolh';
        if (!empty($cfg['order_public_key'])) $cfg['public_key'] = $cfg['order_public_key'];
        if (!empty($cfg['order_from_name'])) $cfg['from_name'] = $cfg['order_from_name'];
        if (!empty($cfg['order_from_email'])) $cfg['from_email'] = $cfg['order_from_email'];
    } else {
        $cfg['template_id'] = !empty($cfg['template_welcome']) ? $cfg['template_welcome'] : ($cfg['template_id'] ?? 'template_xt7cq3g');
    }

    if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        if (strpos($test_email, '{{') !== false || strpos($test_email, 'to_email') !== false) {
            $error_msg = "⚠️ คุณกำลังกรอกตัวแปร '" . htmlspecialchars($test_email) . "' ลงในช่องหน้าเว็บ! ช่องนี้ต้องกรอกเป็น 'อีเมลจริง' (เช่น oavatan@gmail.com) ส่วนตัวแปร {{to_email}} ให้นำไปใส่ในช่อง 'To Email' บนหน้าเว็บ EmailJS Dashboard (emailjs.com) ครับ";
        } else {
            $error_msg = "กรุณาระบุที่อยู่อีเมลสำหรับทดสอบให้ถูกต้อง (เช่น oavatan@gmail.com)";
        }
    } elseif (empty($cfg['service_id']) || empty($cfg['template_id']) || empty($cfg['public_key'])) {
        $error_msg = "❌ ไม่สามารถทดสอบส่งได้: ยังไม่ได้กรอก Service ID, Template ID หรือ Public Key กรุณากรอกให้ครบถ้วนก่อนกดทดสอบ";
    } else {
        if ($test_template_type === 'subscribe') {
            require_once __DIR__ . '/email_newsletter_subscribe.php';
            $vCode = 'TEST-CATNEWS10';
            $rendered_email = renderNewsletterSubscribeEmail($test_name, $test_email, $vCode);
            $subject = "📬 [ทดสอบรับข่าวสาร] ขอบคุณที่ติดตาม Purrfect Shop 🐾 (โค้ด: {$vCode})";
            $tplLabel = "แบบที่ 2: รับข่าวสาร (" . htmlspecialchars($cfg['template_id']) . ")";
        } elseif ($test_template_type === 'order') {
            require_once __DIR__ . '/email_order_confirmation.php';
            $vCode = 'TEST-ORDER';
            $testOrderId = 'PFC-' . date('ymd') . '-TEST';
            $testItems = [
                ['id' => 'cat_british', 'name' => 'น้องสโนว์ (British Shorthair)', 'breed' => 'บริติช ช็อตแฮร์ (British Shorthair)', 'gender' => 'ผู้ (Male)', 'age' => '2.5 เดือน', 'qty' => 1, 'price' => 18000, 'image' => 'cat_british.jpg'],
                ['id' => 'cat_persian', 'name' => 'น้องปุยหิมะ (Persian Classic)', 'breed' => 'เปอร์เซีย (Persian)', 'gender' => 'เมีย (Female)', 'age' => '3 เดือน', 'qty' => 1, 'price' => 16500, 'image' => 'cat_persian.jpg'],
            ];
            $rendered_email = renderOrderConfirmationEmail($test_name, $test_email, $testOrderId, $testItems, 34500, 1725, 2294.25, 35069.25, '💳 ชำระผ่านบัตรเครดิต / เดบิต', date('Y-m-d', strtotime('+3 days')), '99/99 อาคารทดสอบ ถนนนวัตกรรม แขวงลาดยาว เขตจตุจักร กรุงเทพฯ 10900', 'ช่วงบ่าย (13:00 - 17:00 น.)');
            $subject = "✅ [ทดสอบยืนยันคำสั่งซื้อ #{$testOrderId}] ขอบคุณที่รับเลี้ยงน้องแมวกับ Purrfect Shop! 🐾";
            $tplLabel = "แบบที่ 3: ยืนยันคำสั่งซื้อ (" . htmlspecialchars($cfg['template_id']) . ")";
        } else {
            require_once __DIR__ . '/email_member_welcome.php';
            $vCode = 'TEST-EMAILJS15';
            $memberId = strtoupper(substr(md5($test_email), 0, 8));
            $rendered_email = renderMemberWelcomeEmail($test_name, $test_email, $vCode, $memberId);
            $subject = "🧪 [ทดสอบส่งจริงผ่าน EmailJS] ยินดีต้อนรับคุณ{$test_name} - Purrfect Shop 🐾";
            $tplLabel = "แบบที่ 1: ต้อนรับสมาชิกใหม่ (" . htmlspecialchars($cfg['template_id']) . ")";
        }

        $res = EmailJsMailer::send($test_email, $test_name, $subject, $rendered_email, $cfg, [
            'voucher_code' => $vCode,
            'member_id' => $memberId ?? '',
            'template_type' => $test_template_type
        ]);
        $emailjs_test_logs = $res['logs'] ?? [];

        if ($res['success']) {
            $success_msg = "🎉 ยิงส่งอีเมลทดสอบ {$tplLabel} ไปยัง {$test_email} ผ่าน EmailJS REST API สำเร็จ 100%! ตรวจสอบกล่องข้อความ Inbox หรือ Spam ของคุณได้ทันที (" . htmlspecialchars($res['response'] ?? '200 OK') . ")";
            saveEmailJsConfig([
                'last_tested' => date('Y-m-d H:i:s'),
                'last_status' => 'ส่งสำเร็จ (200 OK Accepted) [' . $test_template_type . ']'
            ]);
            $emailjs_config = getEmailJsConfig();
        } else {
            $error_msg = "❌ การส่งผ่าน EmailJS ล้มเหลว: " . $res['message'];
            saveEmailJsConfig([
                'last_tested' => date('Y-m-d H:i:s'),
                'last_status' => 'ล้มเหลว: ' . $res['message']
            ]);
            $emailjs_config = getEmailJsConfig();
        }

        // Also record in Outbox Log for full transparency
        $outbox = getMailOutbox();
        $logEntry = [
            'id' => 'mail_' . uniqid(),
            'to_email' => trim($test_email),
            'to_name' => trim($test_name),
            'sender_id' => $currUser['id'] ?? '',
            'sender_name' => $cfg['from_name'] ?? 'Purrfect Cattery & Boutique 🐾',
            'sender_email' => $cfg['from_email'] ?? 'purrfect.cattery.shop@gmail.com',
            'sender_avatar' => 'assets/images/logo.png',
            'subject' => trim($subject),
            'voucher_code' => trim($vCode),
            'preview' => mb_strimwidth(strip_tags($rendered_email), 0, 180, '...'),
            'status' => $res['success'] ? 'SENT_SUCCESS' : 'FAILED',
            'delivery_mode' => 'LIVE_EMAILJS',
            'delivery_status' => $res['success'] ? 'DELIVERED_VIA_EMAILJS' : 'EMAILJS_FAILED',
            'server_notice' => ($res['message'] ?? '') . " [Template: {$cfg['template_id']}]",
            'sent_at' => date('Y-m-d H:i:s'),
            'user_id' => ''
        ];
        array_unshift($outbox, $logEntry);
        file_put_contents(MAIL_OUTBOX_FILE, json_encode($outbox, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $all_outbox = $outbox;
    }
}

// -------------------------------------------------------------
// POST Handler 2.3: Retry Failed Order Confirmation Emails (Queue)
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST' && isset($_POST['action']) && $_POST['action'] === 'retry_order_emails') {
    $retryResult = retryPendingOrderEmails();
    if ($retryResult['success'] > 0) {
        $success_msg = "✅ Retry สำเร็จ! ส่งอีเมลยืนยันออเดอร์ได้ {$retryResult['success']} จาก {$retryResult['retried']} ฉบับ";
    } elseif ($retryResult['retried'] > 0) {
        $error_msg = "❌ Retry {$retryResult['retried']} ฉบับ แต่ยังส่งไม่สำเร็จ — กรุณาเปิด 'Allow non-browser access' ใน EmailJS account ที่ 2 ก่อน";
    } else {
        $success_msg = "ℹ️ ไม่มีอีเมล order ที่ค้างใน queue";
    }
}

// -------------------------------------------------------------
// POST Handler 3: Send Actual Email & Dispatch to Customer Inbox
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_real_email') {
    $send_mode = $_POST['send_mode'] ?? 'single'; // 'single', 'multiple', 'all'
    $voucher_code = trim($_POST['code'] ?? 'WELCOME15');
    $recipients_to_send = [];

    // Extract Selected Sender Information
    $sender_user_id = trim($_POST['sender_user_id'] ?? ($currUser['id'] ?? 'shop_default'));
    $sender_name = trim($_POST['sender_name'] ?? ($currUser['fullname'] ?? 'Purrfect Cattery & Boutique 🐾'));
    $sender_email = trim($_POST['sender_email'] ?? ($currUser['email'] ?? 'purrfect.cattery.shop@gmail.com'));
    $sender_avatar = trim($_POST['sender_avatar'] ?? ($currUser['avatar'] ?? 'assets/images/logo.png'));

    $sender_info = [
        'id' => $sender_user_id,
        'name' => $sender_name,
        'email' => $sender_email,
        'avatar' => $sender_avatar
    ];

    if ($send_mode === 'all') {
        foreach ($all_users as $u) {
            $recipients_to_send[] = [
                'id' => $u['id'],
                'name' => $u['fullname'],
                'email' => $u['email']
            ];
        }
    } elseif ($send_mode === 'multiple') {
        $selected_ids = $_POST['selected_users'] ?? [];
        if (is_array($selected_ids) && !empty($selected_ids)) {
            foreach ($all_users as $u) {
                if (in_array($u['id'], $selected_ids)) {
                    $recipients_to_send[] = [
                        'id' => $u['id'],
                        'name' => $u['fullname'],
                        'email' => $u['email']
                    ];
                }
            }
        }
    } else { // 'single'
        $single_user_id = trim($_POST['single_user_id'] ?? '');
        $recipient_name = trim($_POST['name'] ?? '');
        $recipient_email = trim($_POST['email'] ?? '');

        if (!empty($single_user_id)) {
            foreach ($all_users as $u) {
                if ($u['id'] === $single_user_id) {
                    $recipient_name = $u['fullname'];
                    $recipient_email = $u['email'];
                    break;
                }
            }
        }

        if (!empty($recipient_name) && !empty($recipient_email)) {
            $recipients_to_send[] = [
                'id' => $single_user_id,
                'name' => $recipient_name,
                'email' => $recipient_email
            ];
        }
    }

    if (empty($recipients_to_send)) {
        $error_msg = "กรุณาเลือกผู้รับ หรือกรอกชื่อและอีเมลอย่างน้อย 1 รายการ";
    } else {
        $sent_count = 0;
        $live_smtp_count = 0;
        $failed_count = 0;
        $sent_names = [];
        $last_err = '';

        require_once __DIR__ . '/email_member_welcome.php';

        foreach ($recipients_to_send as $rec) {
            $r_name = $rec['name'];
            $r_email = $rec['email'];
            $r_member_id = strtoupper(substr(md5($r_email), 0, 8));

            $rendered_email = renderMemberWelcomeEmail($r_name, $r_email, $voucher_code, $r_member_id, $sender_info);
            $subject = "🎁 [สิทธิพิเศษต้อนรับ] คุณ{$r_name} รับส่วนลด 15% (โค้ด: {$voucher_code}) + ชุดของขวัญทาสแมว Purrfect Shop";

            $res = sendActualEmail($r_email, $r_name, $subject, $rendered_email, $voucher_code, $rec['id'], $sender_info);
            $sent_count++;
            $sent_names[] = $r_name . " (" . $r_email . ")";

            if (!empty($res['smtp_result'])) {
                if ($res['smtp_result']['success']) {
                    $live_smtp_count++;
                } else {
                    $failed_count++;
                    $last_err = $res['smtp_result']['message'] ?? '';
                }
            }
        }

        $names_preview = implode(', ', array_slice($sent_names, 0, 3));
        if ($sent_count > 3) {
            $names_preview .= " และอีก " . ($sent_count - 3) . " ท่าน";
        }

        if ($active_driver === 'emailjs') {
            if ($live_smtp_count > 0 && $failed_count === 0) {
                $success_msg = "🎉 ดำเนินการส่งอีเมลจริงผ่าน EmailJS REST API สำเร็จ 100% ทั้งหมด {$sent_count} ท่าน! ({$names_preview}) อีเมลถูกส่งตรงเข้ากล่องข้อความผู้รับเรียบร้อยแล้ว";
            } elseif ($failed_count > 0) {
                $error_msg = "⚠️ พบข้อผิดพลาดในการส่งผ่าน EmailJS: {$last_err} (ระบบได้บันทึกเข้า Outbox จำลองและกล่องข้อความลูกค้าให้แล้ว)";
            } else {
                $success_msg = "✓ ดำเนินการส่งเรียบร้อยทั้งหมด {$sent_count} ท่าน ({$names_preview})";
            }
        } elseif (!empty($smtp_config['enabled'])) {
            if ($live_smtp_count > 0 && $failed_count === 0) {
                $success_msg = "🎉 ดำเนินการส่งอีเมลจริงผ่าน SMTP สำเร็จ 100% ทั้งหมด {$sent_count} ท่าน! ({$names_preview}) อีเมลถูกส่งตรงเข้ากล่องข้อความผู้รับเรียบร้อยแล้ว";
            } elseif ($failed_count > 0) {
                $error_msg = "⚠️ พบข้อผิดพลาดในการส่งผ่าน SMTP: {$last_err} (ระบบได้บันทึกเข้า Outbox จำลองและกล่องข้อความลูกค้าให้แล้ว)";
            } else {
                $success_msg = "✓ ดำเนินการส่งเรียบร้อยทั้งหมด {$sent_count} ท่าน ({$names_preview})";
            }
        } else {
            $success_msg = "✓ ดำเนินการบันทึกในโหมดจำลอง (Outbox Mode) สำเร็จ {$sent_count} ท่าน ({$names_preview}) บันทึกลงกล่องข้อความส่วนตัวและ Outbox แล้ว (เนื่องจากยังไม่ได้เปิดใช้งานส่งจริงภายนอก)";
        }
        
        // Refresh outbox
        $all_outbox = getMailOutbox();
    }
}

// Dynamic Parameters for Preview
$recipient_name = trim($_GET['name'] ?? ($recipient_name ?? ($currUser['fullname'] ?? 'คุณสุพรรษา วงศ์สวัสดิ์')));
$recipient_email = trim($_GET['email'] ?? ($recipient_email ?? ($currUser['email'] ?? 'customer.vip@gmail.com')));
$voucher_code = trim($_GET['code'] ?? ($voucher_code ?? 'WELCOME15'));
$member_id = strtoupper(substr(md5($recipient_email), 0, 8));
$current_view = $_GET['view'] ?? 'desktop'; // desktop, mobile, code, smtp, emailjs, outbox

// Active Preview Sender Info
$active_sender_id = trim($_GET['sender_id'] ?? ($currUser['id'] ?? 'shop_default'));
$active_sender_name = trim($_GET['sender_name'] ?? ($currUser['fullname'] ?? 'Purrfect Cattery & Boutique 🐾'));
$active_sender_email = trim($_GET['sender_email'] ?? ($currUser['email'] ?? 'purrfect.cattery.shop@gmail.com'));
$active_sender_avatar = trim($_GET['sender_avatar'] ?? ($currUser['avatar'] ?? 'assets/images/logo.png'));

$active_sender_info = [
    'id' => $active_sender_id,
    'name' => $active_sender_name,
    'email' => $active_sender_email,
    'avatar' => $active_sender_avatar
];

require_once __DIR__ . '/header.php';
?>

<style>
.mockup-wrapper {
    max-width: 1200px;
    margin: 0 auto 5rem auto;
    padding: 0 15px;
}

/* Tab Navigation Header */
.main-tabs-bar {
    display: flex;
    gap: 8px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    padding: 8px 12px;
    border-radius: var(--radius-lg);
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
}

.main-tab-item {
    padding: 0.6rem 1.2rem;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.main-tab-item:hover {
    color: var(--text-main);
    background: var(--bg-card-subtle);
}

.main-tab-item.active {
    background: var(--primary-coral);
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(255, 117, 86, 0.35);
}

.mockup-control-panel {
    background: var(--bg-card);
    border: 2px solid var(--primary-coral);
    border-radius: var(--radius-xl);
    padding: 1.8rem 2rem;
    margin-bottom: 2.5rem;
    box-shadow: 0 8px 30px rgba(255, 117, 86, 0.12);
}

.send-mode-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 12px;
    margin-bottom: 1.5rem;
}

.send-mode-card {
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 1rem 1.2rem;
    background: var(--bg-card);
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.send-mode-card:hover {
    border-color: var(--primary-coral);
    background: var(--bg-card-subtle);
}

.send-mode-card.selected {
    border-color: var(--primary-coral);
    background: var(--primary-coral-soft);
    box-shadow: 0 4px 12px rgba(255, 117, 86, 0.15);
}

.user-checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 10px;
    max-height: 280px;
    overflow-y: auto;
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    background: var(--bg-card);
    margin-top: 10px;
}

.user-check-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-card-subtle);
    cursor: pointer;
}

.smtp-card {
    background: var(--bg-card);
    border: 2px solid #10B981;
    border-radius: var(--radius-xl);
    padding: 2rem;
    box-shadow: 0 10px 35px rgba(16, 185, 129, 0.15);
    margin-bottom: 2rem;
}

.terminal-console {
    background: #0F172A;
    color: #38BDF8;
    border-radius: 12px;
    padding: 1.2rem;
    font-family: 'Consolas', 'Courier New', monospace;
    font-size: 0.85rem;
    max-height: 320px;
    overflow-y: auto;
    border: 1px solid #334155;
}

.terminal-line { margin-bottom: 4px; line-height: 1.4; }
.term-client { color: #FCD34D; }
.term-server { color: #34D399; }
.term-info   { color: #94A3B8; }
.term-error  { color: #F87171; font-weight: bold; }

.outbox-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.88rem;
    text-align: left;
}

.outbox-table th {
    background: var(--bg-card-subtle);
    padding: 10px 14px;
    font-weight: 800;
    color: var(--text-main);
    border-bottom: 2px solid var(--border-color);
}

.outbox-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
}
</style>

<div class="mockup-wrapper">

    <!-- Feedback Alerts -->
    <?php if (!empty($success_msg)): ?>
        <div class="alert-box alert-success" style="margin-bottom: 1.5rem; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <strong><?php echo htmlspecialchars($success_msg); ?></strong>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="profile.php?tab=inbox" class="btn btn-secondary btn-sm" style="background: #FFFFFF;">
                    📬 กล่องข้อความลูกค้า &rarr;
                </a>
                <a href="?view=outbox" class="btn btn-secondary btn-sm" style="background: #FFFFFF;">
                    📮 ดู Outbox (<?php echo count($all_outbox); ?>)
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="alert-box alert-danger" style="margin-bottom: 1.5rem;">
            ⚠️ <?php echo htmlspecialchars($error_msg); ?>
        </div>
    <?php endif; ?>

    <!-- Top Status Bar (แสดงสถานะของช่องทางส่งจริง: SMTP vs EmailJS vs Outbox) -->
    <?php 
    $active_driver = getActiveMailDriver();
    $bar_bg = ($active_driver === 'emailjs') ? 'rgba(59, 130, 246, 0.1)' : (($active_driver === 'smtp') ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)');
    $bar_border = ($active_driver === 'emailjs') ? '#3B82F6' : (($active_driver === 'smtp') ? '#10B981' : '#F59E0B');
    $bar_color = ($active_driver === 'emailjs') ? '#1E40AF' : (($active_driver === 'smtp') ? '#065F46' : '#92400E');
    $bar_icon = ($active_driver === 'emailjs') ? '⚡' : (($active_driver === 'smtp') ? '🟢' : '🟡');
    ?>
    <div style="margin-bottom: 1.2rem; padding: 0.9rem 1.4rem; border-radius: 14px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: <?php echo $bar_bg; ?>; border: 1.5px solid <?php echo $bar_border; ?>;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 2rem;"><?php echo $bar_icon; ?></span>
            <div>
                <div style="font-weight: 800; font-size: 1rem; color: <?php echo $bar_color; ?>;">
                    <?php 
                    if ($active_driver === 'emailjs') {
                        echo 'ระบบส่งเข้าอีเมลจริงผ่าน EmailJS พร้อมใช้งาน (Live EmailJS REST Active)';
                    } elseif ($active_driver === 'smtp') {
                        echo 'ระบบส่งเข้าอีเมลจริงผ่าน SMTP พร้อมใช้งาน (Live SMTP Active)';
                    } else {
                        echo 'ขณะนี้อยู่ในโหมดจำลอง (Local Outbox Mode - ยังไม่ได้เปิดใช้งานส่งจริง)';
                    }
                    ?>
                </div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 2px;">
                    <?php if ($active_driver === 'emailjs'): ?>
                        ช่องทาง: <strong>EmailJS REST API</strong> &bull; Service: <strong><?php echo htmlspecialchars($emailjs_config['service_id']); ?></strong> &bull; Template: <strong><?php echo htmlspecialchars($emailjs_config['template_id']); ?></strong>
                    <?php elseif ($active_driver === 'smtp'): ?>
                        ช่องทาง: <strong>SMTP Socket</strong> &bull; โฮสต์: <strong><?php echo htmlspecialchars($smtp_config['host'] . ':' . $smtp_config['port']); ?></strong> &bull; ผู้ส่ง: <strong><?php echo htmlspecialchars($smtp_config['from_email'] ?: $smtp_config['username']); ?></strong>
                    <?php else: ?>
                        คุณสามารถเลือกเปิดใช้การส่งจริงได้ 2 ช่องทาง: <strong>⚙️ Google SMTP</strong> (ฟรีตลอดชีพ 500 ฉบับ/วัน) หรือ <strong>⚡ EmailJS REST API</strong> (โควตาฟรี 200 ฉบับ/ด.)
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="?view=emailjs" class="btn btn-sm" style="background: <?php echo $current_view === 'emailjs' ? '#2563EB' : '#FFFFFF'; ?>; color: <?php echo $current_view === 'emailjs' ? '#FFFFFF' : '#1E40AF'; ?>; font-weight: 800; border: 1.5px solid #3B82F6; padding: 7px 14px; border-radius: 999px;">
                ⚡ ตั้งค่า EmailJS &rarr;
            </a>
            <a href="?view=smtp" class="btn btn-sm" style="background: <?php echo $current_view === 'smtp' ? '#059669' : '#FFFFFF'; ?>; color: <?php echo $current_view === 'smtp' ? '#FFFFFF' : '#065F46'; ?>; font-weight: 800; border: 1.5px solid #10B981; padding: 7px 14px; border-radius: 999px;">
                ⚙️ ตั้งค่า SMTP &rarr;
            </a>
        </div>
    </div>

    <!-- Main Tabs Navigation Bar -->
    <div class="main-tabs-bar">
        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
            <a href="?view=desktop&name=<?php echo urlencode($recipient_name); ?>&email=<?php echo urlencode($recipient_email); ?>&code=<?php echo urlencode($voucher_code); ?>" class="main-tab-item <?php echo $current_view === 'desktop' ? 'active' : ''; ?>">
                💻 หน้าจอส่ง & เดสก์ท็อป
            </a>
            <a href="?view=mobile&name=<?php echo urlencode($recipient_name); ?>&email=<?php echo urlencode($recipient_email); ?>&code=<?php echo urlencode($voucher_code); ?>" class="main-tab-item <?php echo $current_view === 'mobile' ? 'active' : ''; ?>">
                📱 พรีวิวมือถือ (Mobile)
            </a>
            <a href="?view=smtp" class="main-tab-item <?php echo $current_view === 'smtp' ? 'active' : ''; ?>" style="border: 1.5px solid <?php echo $current_view === 'smtp' ? 'transparent' : '#10B981'; ?>; color: <?php echo $current_view === 'smtp' ? '#FFFFFF' : '#047857'; ?>;">
                ⚙️ ส่งผ่าน SMTP
            </a>
            <a href="?view=emailjs" class="main-tab-item <?php echo $current_view === 'emailjs' ? 'active' : ''; ?>" style="border: 1.5px solid <?php echo $current_view === 'emailjs' ? 'transparent' : '#3B82F6'; ?>; color: <?php echo $current_view === 'emailjs' ? '#FFFFFF' : '#1D4ED8'; ?>;">
                ⚡ ส่งผ่าน EmailJS
            </a>
            <a href="?view=outbox" class="main-tab-item <?php echo $current_view === 'outbox' ? 'active' : ''; ?>">
                📮 ประวัติส่งออกจริง (Outbox Logs)
                <span style="background: <?php echo $current_view === 'outbox' ? '#FFFFFF' : '#10B981'; ?>; color: <?php echo $current_view === 'outbox' ? 'var(--primary-coral)' : '#FFFFFF'; ?>; font-size: 0.72rem; padding: 2px 7px; border-radius: 999px; margin-left: 4px;">
                    <?php echo count($all_outbox); ?>
                </span>
            </a>
            <a href="?view=code" class="main-tab-item <?php echo $current_view === 'code' ? 'active' : ''; ?>">
                📄 โค้ด HTML
            </a>
        </div>

        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <a href="admin.php" class="btn btn-primary btn-sm" style="font-size: 0.8rem; font-weight: 700; background: #1E293B; color: #FFFFFF; border-color: #334155;">
                🛠️ จัดการระบบ (Admin)
            </a>
            <a href="email_newsletter_subscribe.php" class="btn btn-secondary btn-sm" style="font-size: 0.8rem; background: #F0FDF4; border-color: #86EFAC; color: #166534; font-weight: 700;">
                📬 เทมเพลต Subscribe
            </a>
            <a href="email_member_welcome.php" class="btn btn-secondary btn-sm" style="font-size: 0.8rem; background: #FFF7ED; border-color: #FDBA74; color: #9A3412; font-weight: 700;">
                🐱 เทมเพลต สมัครสมาชิก
            </a>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- VIEW MODE: LIVE SMTP SETTINGS & TEST CENTER (แสดงอันดับ 1 ทันทีเมื่อคลิกแท็บนี้!) -->
    <!-- ================================================================== -->
    <?php if ($current_view === 'smtp'): ?>
        <div class="smtp-card" id="smtp-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px; border-bottom: 2px solid var(--border-color); padding-bottom: 1.2rem;">
                <div>
                    <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">
                        ⚙️ ตั้งค่าระบบส่งเข้าอีเมลจริง (Live SMTP Mailer Settings)
                    </h2>
                    <p style="font-size: 0.92rem; color: var(--text-muted); margin: 0;">
                        กรอกข้อมูลบัญชี Gmail หรือ Outlook ของคุณเพื่อให้อีเมลเด้งเข้ากล่องข้อความจริง 100%
                    </p>
                </div>
                <div>
                    <span style="font-size: 0.9rem; font-weight: 800; padding: 6px 16px; border-radius: 999px; background: <?php echo !empty($smtp_config['enabled']) ? '#DCFCE7' : '#FEF3C7'; ?>; color: <?php echo !empty($smtp_config['enabled']) ? '#166534' : '#92400E'; ?>; border: 1.5px solid <?php echo !empty($smtp_config['enabled']) ? '#86EFAC' : '#FDE68A'; ?>;">
                        <?php echo !empty($smtp_config['enabled']) ? '🟢 โหมดส่งอีเมลจริง เปิดใช้งานแล้ว' : '🟡 ปิดโหมดส่งจริง (ใช้งาน Outbox จำลอง)'; ?>
                    </span>
                </div>
            </div>

            <!-- Quick Presets -->
            <div style="background: var(--bg-card-subtle); padding: 1.2rem; border-radius: 12px; margin-bottom: 1.8rem; border: 1px solid var(--border-color);">
                <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-main); margin-bottom: 8px;">
                    ⚡ เลือกการตั้งค่าด่วน (1-Click Fill Preset):
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="applySmtpPreset('gmail')" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800;">
                        <span style="color: #EA4335;">🔴</span> Google Gmail (smtp.gmail.com:587)
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="applySmtpPreset('outlook')" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800;">
                        <span style="color: #0078D4;">🔵</span> Microsoft Outlook / Hotmail (smtp.office365.com:587)
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="applySmtpPreset('custom')" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800;">
                        <span style="color: #10B981;">🟢</span> Custom / เซิร์ฟเวอร์อื่น
                    </button>
                </div>
            </div>

            <!-- Gmail App Password Guide Notice -->
            <div style="background: #EFF6FF; border: 1.5px solid #BFDBFE; border-radius: 14px; padding: 1.4rem; margin-bottom: 2rem; color: #1E40AF;">
                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <span style="font-size: 2rem;">💡</span>
                    <div>
                        <strong style="font-size: 1.05rem; display: block; margin-bottom: 6px;">
                            สำคัญมาก: วิธีสร้าง "รหัสผ่านสำหรับแอป (Google App Password) 16 หลัก" เพื่อให้ส่งเข้า Gmail ได้จริง
                        </strong>
                        <div style="font-size: 0.9rem; line-height: 1.65;">
                            เนื่องจาก Google ไม่อนุญาตให้ใช้รหัสผ่านบัญชีทั่วไปเชื่อมต่อ SMTP ภายนอก คุณจำเป็นต้องใช้ <strong>App Password 16 ตัว</strong> โดยทำได้ง่ายๆ ดังนี้:
                            <ol style="margin: 8px 0 0 1.2rem; padding: 0;">
                                <li>เปิดเบราว์เซอร์ไปที่: <a href="https://myaccount.google.com/security" target="_blank" style="color: #2563EB; font-weight: 800; text-decoration: underline;">myaccount.google.com/security</a></li>
                                <li>เปิดใช้งาน <strong>"การยืนยันแบบ 2 ขั้นตอน" (2-Step Verification)</strong> ให้เรียบร้อย</li>
                                <li>ในช่องค้นหาด้านบนของหน้าความปลอดภัย ให้พิมพ์ค้นหาคำว่า <strong>"รหัสผ่านสำหรับแอป"</strong> (หรือคลิกเมนู App passwords)</li>
                                <li>ตั้งชื่อแอป เช่น <code>CatShop</code> แล้วกด <strong>"สร้าง" (Create)</strong></li>
                                <li>นำรหัสผ่าน 16 ตัวอักษร (เช่น <code>abcd efgh ijkl mnop</code>) มาวางลงในช่อง <strong>รหัสผ่าน / Google App Password</strong> ด้านล่างนี้ แล้วกดบันทึก!</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form: SMTP Configuration -->
            <form method="POST" action="welcome_sales_mockup.php?view=smtp" id="smtpSettingsForm">
                <input type="hidden" name="action" value="save_smtp_config">

                <!-- Toggle Live SMTP -->
                <div style="margin-bottom: 1.5rem; padding: 1.2rem; border-radius: 12px; background: rgba(16, 185, 129, 0.08); border: 2px solid #10B981;">
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="checkbox" name="smtp_enabled" id="smtp_enabled" value="1" <?php echo !empty($smtp_config['enabled']) ? 'checked' : ''; ?> style="width: 22px; height: 22px; accent-color: #10B981;">
                        <div>
                            <strong style="font-size: 1.05rem; color: #065F46;">
                                ☑️ เปิดใช้งานการส่งผ่าน SMTP เข้าอีเมลจริง (Enable Live Delivery)
                            </strong>
                            <div style="font-size: 0.85rem; color: #047857; margin-top: 2px;">
                                ติ๊กเครื่องหมายถูกนี้เพื่อให้ทุกการส่งอีเมลต้อนรับและโปรโมชันถูกยิงเข้ากล่องจดหมายจริงของผู้รับทันที
                            </div>
                        </div>
                    </label>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 1.5rem;">
                    <div>
                        <label for="smtp_host" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            🌐 SMTP Server Host:
                        </label>
                        <input type="text" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($smtp_config['host']); ?>" placeholder="smtp.gmail.com" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-weight: 600;">
                    </div>

                    <div>
                        <label for="smtp_port" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            🔌 SMTP Port:
                        </label>
                        <input type="number" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($smtp_config['port']); ?>" placeholder="587" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-weight: 600;">
                    </div>

                    <div>
                        <label for="smtp_encryption" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            🔒 รูปแบบการเข้ารหัส (Encryption):
                        </label>
                        <select id="smtp_encryption" name="smtp_encryption" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-weight: 600;">
                            <option value="tls" <?php echo ($smtp_config['encryption'] === 'tls') ? 'selected' : ''; ?>>STARTTLS (พอร์ต 587 - แนะนำสำหรับ Gmail)</option>
                            <option value="ssl" <?php echo ($smtp_config['encryption'] === 'ssl') ? 'selected' : ''; ?>>SSL / TLS Direct (พอร์ต 465)</option>
                            <option value="none" <?php echo ($smtp_config['encryption'] === 'none') ? 'selected' : ''; ?>>None (พอร์ต 25)</option>
                        </select>
                    </div>

                    <div>
                        <label for="smtp_username" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            👤 Username ผู้ส่ง (เช่น your.name@gmail.com): *
                        </label>
                        <input type="email" id="smtp_username" name="smtp_username" value="<?php echo htmlspecialchars($smtp_config['username']); ?>" placeholder="your.email@gmail.com" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-weight: 700;">
                    </div>

                    <div>
                        <label for="smtp_password" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            🔑 รหัสผ่าน / Google App Password (16 ตัวอักษร): *
                        </label>
                        <div style="position: relative;">
                            <input type="password" id="smtp_password" name="smtp_password" value="<?php echo htmlspecialchars($smtp_config['password']); ?>" placeholder="เช่น abcd efgh ijkl mnop" required style="width: 100%; padding: 10px 40px 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-family: monospace; font-weight: 700;">
                            <button type="button" onclick="togglePasswordVisibility()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.1rem;" title="แสดง/ซ่อนรหัสผ่าน">
                                👁️
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="smtp_from_email" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            📧 ที่อยู่อีเมลผู้ส่ง (From Email):
                        </label>
                        <input type="email" id="smtp_from_email" name="smtp_from_email" value="<?php echo htmlspecialchars($smtp_config['from_email'] ?: $smtp_config['username']); ?>" placeholder="เช่น your.email@gmail.com" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main);">
                    </div>

                    <div>
                        <label for="smtp_from_name" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            🏷️ ชื่อผู้ส่งที่แสดง (From Name):
                        </label>
                        <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?php echo htmlspecialchars($smtp_config['from_name']); ?>" placeholder="Purrfect Cattery & Boutique 🐾" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main);">
                    </div>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 2rem;">
                    <button type="submit" class="btn btn-primary" style="font-weight: 800; padding: 12px 28px; font-size: 1rem; box-shadow: var(--shadow-coral);">
                        💾 บันทึกการตั้งค่า SMTP และเปิดใช้งานส่งจริง
                    </button>
                </div>
            </form>

            <hr style="border: 0; border-top: 2px dashed var(--border-color); margin: 2rem 0;">

            <!-- Test Live Delivery Form -->
            <div style="background: var(--bg-card-subtle); border-radius: 16px; padding: 1.8rem; border: 2px dashed var(--primary-coral);">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                    <span style="font-size: 1.8rem;">🧪</span>
                    <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--text-main); margin: 0;">
                        ทดสอบส่งเข้าอีเมลจริงทันที (Live Mail Delivery Test)
                    </h3>
                </div>
                <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.2rem;">
                    กรอกที่อยู่อีเมลของคุณเองด้านล่าง แล้วกดปุ่ม ระบบจะส่งจดหมายต้อนรับสมาชิกพร้อมภาพน้องแมวและคูปองส่วนลด 15% ตรงเข้า Inbox ของคุณทันที
                </p>

                <form method="POST" action="welcome_sales_mockup.php?view=smtp" id="testEmailForm">
                    <input type="hidden" name="action" value="test_smtp_email">
                    <input type="hidden" name="smtp_host" id="t_host">
                    <input type="hidden" name="smtp_port" id="t_port">
                    <input type="hidden" name="smtp_encryption" id="t_enc">
                    <input type="hidden" name="smtp_username" id="t_user">
                    <input type="hidden" name="smtp_password" id="t_pass">
                    <input type="hidden" name="smtp_from_email" id="t_from_email">
                    <input type="hidden" name="smtp_from_name" id="t_from_name">

                    <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                        <div style="min-width: 240px;">
                            <label for="test_email" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 4px;">
                                📬 กรอกอีเมลจริงของคุณเพื่อรับจดหมายทดสอบ:
                            </label>
                            <input type="email" id="test_email" name="test_email" value="<?php echo htmlspecialchars($currUser['email'] ?? ''); ?>" required placeholder="yourname@gmail.com" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-weight: 700;">
                        </div>

                        <div style="min-width: 180px;">
                            <label for="test_name" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 4px;">
                                👤 ชื่อผู้รับ:
                            </label>
                            <input type="text" id="test_name" name="test_name" value="<?php echo htmlspecialchars($currUser['fullname'] ?? 'ผู้ทดสอบระบบ Cat Shop'); ?>" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main);">
                        </div>

                        <div>
                            <button type="submit" onclick="syncTestFormCredentials()" class="btn btn-primary" style="height: 44px; font-weight: 800; padding: 0 1.8rem; display: inline-flex; align-items: center; gap: 8px; box-shadow: var(--shadow-coral); font-size: 0.95rem;">
                                🚀 ยิงส่งเข้าอีเมลเดี๋ยวนี้!
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Terminal Diagnostic Logs Console -->
            <?php if (!empty($smtp_test_logs)): ?>
                <div style="margin-top: 2rem;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-main); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                        <span>💻</span> บันทึกการติดต่อสื่อสารกับ SMTP Server แบบ Real-time (Communication Transcript):
                    </div>
                    <div class="terminal-console">
                        <?php foreach ($smtp_test_logs as $lg): ?>
                            <div class="terminal-line">
                                <span style="color: #64748B;">[<?php echo htmlspecialchars($lg['time']); ?>]</span>
                                <?php if ($lg['direction'] === 'CLIENT'): ?>
                                    <span class="term-client">&gt; <?php echo htmlspecialchars($lg['message']); ?></span>
                                <?php elseif ($lg['direction'] === 'SERVER'): ?>
                                    <span class="term-server">&lt; <?php echo htmlspecialchars($lg['message']); ?></span>
                                <?php elseif ($lg['direction'] === 'ERROR'): ?>
                                    <span class="term-error">✖ <?php echo htmlspecialchars($lg['message']); ?></span>
                                <?php else: ?>
                                    <span class="term-info">&bull; <?php echo htmlspecialchars($lg['message']); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- ================================================================== -->
    <!-- VIEW MODE: EMAILJS REST API SETTINGS & TEST CENTER -->
    <!-- ================================================================== -->
    <?php if ($current_view === 'emailjs'): ?>
        <div class="smtp-card" id="emailjs-section" style="border-color: #3B82F6; box-shadow: 0 10px 35px rgba(59, 130, 246, 0.15);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px; border-bottom: 2px solid var(--border-color); padding-bottom: 1.2rem;">
                <div>
                    <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">
                        ⚡ ตั้งค่าส่งอีเมลผ่าน EmailJS (EmailJS REST API Settings)
                    </h2>
                    <p style="font-size: 0.92rem; color: var(--text-muted); margin: 0;">
                        ตัวเลือกการส่งผ่านระบบคลาวด์ EmailJS REST API v1.0 ไม่ต้องเปิดพอร์ต SMTP และใช้เทมเพลตจาก EmailJS Dashboard ได้
                    </p>
                </div>
                <div>
                    <span style="font-size: 0.9rem; font-weight: 800; padding: 6px 16px; border-radius: 999px; background: <?php echo !empty($emailjs_config['enabled']) ? '#DBEAFE' : '#FEF3C7'; ?>; color: <?php echo !empty($emailjs_config['enabled']) ? '#1E40AF' : '#92400E'; ?>; border: 1.5px solid <?php echo !empty($emailjs_config['enabled']) ? '#93C5FD' : '#FDE68A'; ?>;">
                        <?php echo !empty($emailjs_config['enabled']) ? '⚡ โหมดส่งผ่าน EmailJS: เปิดใช้งานแล้ว' : '🟡 ปิดโหมด EmailJS (ไม่เปิดใช้งาน)'; ?>
                    </span>
                </div>
            </div>

            <!-- EmailJS Setup Step-by-Step Guide -->
            <div style="background: #EFF6FF; border: 1.5px solid #BFDBFE; border-radius: 14px; padding: 1.4rem; margin-bottom: 2rem; color: #1E40AF;">
                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <span style="font-size: 2rem;">💡</span>
                    <div>
                        <strong style="font-size: 1.05rem; display: block; margin-bottom: 6px;">
                            วิธีสมัครและรับ Service ID / Template ID / Public Key จาก EmailJS (ทำฟรีใน 2 นาที):
                        </strong>
                        <div style="font-size: 0.9rem; line-height: 1.7;">
                            <ol style="margin: 8px 0 0 1.2rem; padding: 0;">
                                <li>ไปที่เว็บไซต์: <a href="https://www.emailjs.com" target="_blank" style="color: #2563EB; font-weight: 800; text-decoration: underline;">www.emailjs.com</a> แล้วกดสมัครบัญชีฟรี (ได้โควตาส่งฟรี 200 ฉบับ/เดือน)</li>
                                <li>ไปที่เมนู <strong>"Email Services"</strong> &rarr; กด <strong>"Add Service"</strong> &rarr; เลือก <strong>Gmail</strong> (หรือ Outlook) แล้วกดปุ่ม <strong>Connect Account</strong> เพื่อรับ <code>Service ID</code> (เช่น <code>service_catshop</code>)</li>
                                <li>ไปที่เมนู <strong>"Email Templates"</strong> &rarr; สร้างหรือตั้งค่าเทมเพลต 2 แบบตามที่ระบบต้องการ:
                                    <div style="margin: 8px 0; background: #FFFFFF; border: 1.5px solid #93C5FD; border-radius: 10px; padding: 12px 14px; box-shadow: 0 2px 6px rgba(59,130,246,0.08);">
                                        <div style="font-weight: 800; color: #1E40AF; font-size: 0.92rem; margin-bottom: 4px;">
                                            🐱 แบบที่ 1: ต้อนรับสมาชิกใหม่ (Member Welcome) &rarr; Template ID: <code>template_xt7cq3g</code>
                                        </div>
                                        <div style="font-size: 0.84rem; color: #334155; line-height: 1.6;">
                                            • <strong>To Email:</strong> ใส่ <code>{{to_email}}</code> &bull; <strong>Subject:</strong> ใส่ <code>{{subject}}</code><br>
                                            • <strong>Content:</strong> ใส่ <code>{{{message_html}}}</code> (วงเล็บปีกกา 3 ชั้น เพื่อแสดงบัตรสมาชิก VIP และภาพน้องแมว)
                                        </div>
                                    </div>
                                    <div style="margin: 8px 0; background: #FFFFFF; border: 1.5px solid #86EFAC; border-radius: 10px; padding: 12px 14px; box-shadow: 0 2px 6px rgba(16,185,129,0.08);">
                                        <div style="font-weight: 800; color: #065F46; font-size: 0.92rem; margin-bottom: 4px;">
                                            📬 แบบที่ 2: ยืนยันรับข่าวสาร (Newsletter Subscribe) &rarr; Template ID: <code>template_y8wnrlt</code>
                                        </div>
                                        <div style="font-size: 0.84rem; color: #334155; line-height: 1.6;">
                                            • <strong>To Email:</strong> ใส่ <code>{{to_email}}</code> &bull; <strong>Subject:</strong> ใส่ <code>{{subject}}</code><br>
                                            • <strong>Content:</strong> ใส่ <code>{{{message_html}}}</code> (วงเล็บปีกกา 3 ชั้น เพื่อแสดงไฮไลท์แมวและเกร็ดสุขภาพ)
                                        </div>
                                    </div>
                                    <div style="margin: 8px 0; background: #FFFFFF; border: 1.5px solid #FDBA74; border-radius: 10px; padding: 12px 14px; box-shadow: 0 2px 6px rgba(234,88,12,0.08);">
                                        <div style="font-weight: 800; color: #C2410C; font-size: 0.92rem; margin-bottom: 4px;">
                                            🛒 แบบที่ 3: ยืนยันคำสั่งซื้อ + รูปภาพสินค้า (Order Confirmation) &rarr; Template ID: <code>template_pt8dolh</code> (Account ที่ 2)
                                        </div>
                                        <div style="font-size: 0.84rem; color: #334155; line-height: 1.6;">
                                            • <strong>To Email:</strong> ใส่ <code>{{to_email}}</code> &bull; <strong>Subject:</strong> ใส่ <code>{{subject}}</code><br>
                                            • <strong>Content:</strong> ใส่ <code>{{{message_html}}}</code> (วงเล็บปีกกา 3 ชั้น เพื่อแสดงรายการสินค้าพร้อมรูปภาพ ตารางยอดชำระ และข้อมูลจัดส่ง)
                                        </div>
                                    </div>
                                </li>
                                <li style="margin: 6px 0; background: #FEF3C7; padding: 8px 12px; border-radius: 8px; border: 1.5px solid #FDE68A; color: #92400E; list-style-type: none;">
                                    ⚠️ <strong>จุดจำแนกสำคัญที่ห้ามสับสน:</strong><br>
                                    • <strong>ในเว็บ EmailJS (emailjs.com):</strong> ช่อง <code>To Email</code> ให้ใส่คำว่า <code>{{to_email}}</code><br>
                                    • <strong>ในหน้าเว็บนี้ (CatCyberShop):</strong> ช่องทดสอบด้านล่าง ให้กรอกเป็น <strong>อีเมลจริงของคุณ</strong> (เช่น <code>oavatan@gmail.com</code>) ห้ามพิมพ์คำว่า <code>{{to_email}}</code> ลงในช่องหน้าเว็บ!
                                </li>
                                <li>ไปที่เมนู <strong>"Account"</strong> (มุมซ้ายล่าง) &rarr; แท็บ <strong>"API Keys"</strong> &rarr; คัดลอก <strong>Public Key</strong></li>
                                <li><strong style="color: #DC2626;">📌 สำคัญสำหรับการส่งผ่าน PHP หลังบ้าน:</strong> ไปที่ <a href="https://dashboard.emailjs.com/admin/account/security" target="_blank" style="color: #2563EB; font-weight: 800; text-decoration: underline;">Account &rarr; Security</a> แล้วเปิดสวิตช์ <strong>"Allow EmailJS API for non-browser applications"</strong> (จำเป็นต้องเปิดเพื่อให้ PHP ส่งอัตโนมัติได้)</li>
                                <li>นำค่าทั้งหมดมากรอกลงในแบบฟอร์มด้านล่าง ติ๊กถูก แล้วกดบันทึก!</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form: EmailJS Configuration -->
            <form method="POST" action="welcome_sales_mockup.php?view=emailjs" id="emailjsSettingsForm">
                <input type="hidden" name="action" value="save_emailjs_config">

                <!-- Toggle EmailJS -->
                <div style="margin-bottom: 1.5rem; padding: 1.2rem; border-radius: 12px; background: rgba(59, 130, 246, 0.08); border: 2px solid #3B82F6;">
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="checkbox" name="emailjs_enabled" id="emailjs_enabled" value="1" <?php echo !empty($emailjs_config['enabled']) ? 'checked' : ''; ?> style="width: 22px; height: 22px; accent-color: #2563EB;">
                        <div>
                            <strong style="font-size: 1.05rem; color: #1E40AF;">
                                ⚡ เปิดใช้งานการส่งผ่าน EmailJS REST API (Enable EmailJS Delivery)
                            </strong>
                            <div style="font-size: 0.85rem; color: #1D4ED8; margin-top: 2px;">
                                เมื่อเปิดใช้งาน ตัวส่งอีเมลจะยิงผ่าน EmailJS ไปยังกล่องจดหมายจริงของลูกค้าโดยตรง
                            </div>
                        </div>
                    </label>
                </div>

                <input type="hidden" name="emailjs_template_id" id="emailjs_template_id" value="<?php echo htmlspecialchars($emailjs_config['template_welcome'] ?? 'template_xt7cq3g'); ?>">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 1.5rem;">
                    <div>
                        <label for="emailjs_service_id" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            🔌 EmailJS Service ID: *
                        </label>
                        <input type="text" id="emailjs_service_id" name="emailjs_service_id" value="<?php echo htmlspecialchars($emailjs_config['service_id']); ?>" placeholder="เช่น service_4aihlkj" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-weight: 700; font-family: monospace;">
                    </div>

                    <div>
                        <label for="emailjs_public_key" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            🔑 EmailJS Public Key: *
                        </label>
                        <input type="text" id="emailjs_public_key" name="emailjs_public_key" value="<?php echo htmlspecialchars($emailjs_config['public_key']); ?>" placeholder="เช่น pJxoR0jf2aj4qNih-" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-weight: 700; font-family: monospace;">
                    </div>

                    <div>
                        <label for="emailjs_template_welcome" style="font-weight: 800; font-size: 0.88rem; color: #1E40AF; display: block; margin-bottom: 6px;">
                            🐱 Template สมัครสมาชิกใหม่ (Member Welcome): *
                        </label>
                        <input type="text" id="emailjs_template_welcome" name="emailjs_template_welcome" value="<?php echo htmlspecialchars($emailjs_config['template_welcome'] ?? 'template_xt7cq3g'); ?>" placeholder="template_xt7cq3g" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 2px solid #3B82F6; background: #EFF6FF; color: #1E3A8A; font-weight: 800; font-family: monospace;">
                        <small style="color: #64748B; font-size: 0.75rem; display: block; margin-top: 4px;">ใช้ส่งต้อนรับเมื่อลูกค้าสมัครสมาชิกใหม่ + โค้ด WELCOME15</small>
                    </div>

                    <div>
                        <label for="emailjs_template_subscribe" style="font-weight: 800; font-size: 0.88rem; color: #065F46; display: block; margin-bottom: 6px;">
                            📬 Template รับข่าวสาร (Newsletter Subscribe): *
                        </label>
                        <input type="text" id="emailjs_template_subscribe" name="emailjs_template_subscribe" value="<?php echo htmlspecialchars($emailjs_config['template_subscribe'] ?? 'template_y8wnrlt'); ?>" placeholder="template_y8wnrlt" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 2px solid #10B981; background: #ECFDF5; color: #064E3B; font-weight: 800; font-family: monospace;">
                        <small style="color: #64748B; font-size: 0.75rem; display: block; margin-top: 4px;">ใช้ส่งยืนยันเมื่อลูกค้า Subscribe รับข่าวสาร + โค้ด CATNEWS10</small>
                    </div>

                    <div>
                        <label for="emailjs_from_name" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            🏷️ ชื่อผู้ส่งที่แสดง (From Name):
                        </label>
                        <input type="text" id="emailjs_from_name" name="emailjs_from_name" value="<?php echo htmlspecialchars($emailjs_config['from_name']); ?>" placeholder="Purrfect Cattery & Boutique 🐾" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main);">
                    </div>

                    <div>
                        <label for="emailjs_from_email" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            📧 อีเมลผู้ส่ง (From Email):
                        </label>
                        <input type="email" id="emailjs_from_email" name="emailjs_from_email" value="<?php echo htmlspecialchars($emailjs_config['from_email']); ?>" placeholder="purrfect.cattery.shop@gmail.com" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main);">
                    </div>

                    <div>
                        <label for="emailjs_private_key" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            🛡️ Private Key / Access Token (ไม่บังคับ):
                        </label>
                        <input type="password" id="emailjs_private_key" name="emailjs_private_key" value="<?php echo htmlspecialchars($emailjs_config['private_key'] ?? ''); ?>" placeholder="ใส่เฉพาะเมื่อเปิดโหมด Strict Authentication" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-family: monospace;">
                    </div>
                </div>

                <!-- Account 2: Order Confirmation Dedicated Settings -->
                <div style="background: #FFF7ED; border: 2px solid #FDBA74; border-radius: 14px; padding: 1.4rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                        <div style="font-weight: 800; font-size: 1rem; color: #C2410C; display: flex; align-items: center; gap: 8px;">
                            <span>🛒</span> EmailJS Account ที่ 2 (สำหรับส่งยืนยันคำสั่งซื้อ + รูปภาพสินค้า)
                        </div>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.88rem; color: #9A3412; font-weight: 700;">
                            <input type="checkbox" name="emailjs_order_enabled" id="emailjs_order_enabled" value="1" <?php echo !empty($emailjs_config['order_enabled']) ? 'checked' : ''; ?> style="width: 18px; height: 18px; accent-color: #EA580C;">
                            เปิดใช้งาน Account ที่ 2
                        </label>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
                        <div>
                            <label for="emailjs_order_service_id" style="font-weight: 700; font-size: 0.84rem; color: #9A3412; display: block; margin-bottom: 4px;">
                                🔌 Order Service ID:
                            </label>
                            <input type="text" id="emailjs_order_service_id" name="emailjs_order_service_id" value="<?php echo htmlspecialchars($emailjs_config['order_service_id'] ?? 'service_3n2rkol'); ?>" placeholder="service_3n2rkol" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid #FDBA74; background: #FFFFFF; font-weight: 700; font-family: monospace; color: #9A3412;">
                        </div>
                        <div>
                            <label for="emailjs_order_template_id" style="font-weight: 700; font-size: 0.84rem; color: #9A3412; display: block; margin-bottom: 4px;">
                                📑 Order Template ID:
                            </label>
                            <input type="text" id="emailjs_order_template_id" name="emailjs_order_template_id" value="<?php echo htmlspecialchars($emailjs_config['order_template_id'] ?? 'template_pt8dolh'); ?>" placeholder="template_pt8dolh" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid #FDBA74; background: #FFFFFF; font-weight: 700; font-family: monospace; color: #9A3412;">
                        </div>
                        <div>
                            <label for="emailjs_order_public_key" style="font-weight: 700; font-size: 0.84rem; color: #9A3412; display: block; margin-bottom: 4px;">
                                🔑 Order Public Key:
                            </label>
                            <input type="text" id="emailjs_order_public_key" name="emailjs_order_public_key" value="<?php echo htmlspecialchars($emailjs_config['order_public_key'] ?? 'W-8QyJeFcTQcTtoFA'); ?>" placeholder="W-8QyJeFcTQcTtoFA" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid #FDBA74; background: #FFFFFF; font-weight: 700; font-family: monospace; color: #9A3412;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 2rem;">
                    <button type="submit" class="btn btn-primary" style="background: #2563EB; border-color: #1D4ED8; font-weight: 800; padding: 12px 28px; font-size: 1rem; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35);">
                        💾 บันทึกการตั้งค่า EmailJS และเปิดใช้งานส่งจริง
                    </button>
                </div>
            </form>

            <?php
            // Show Retry Order Email Queue panel if there are pending/failed entries
            $queueFile = __DIR__ . '/data_email_queue.json';
            $pendingCount = 0;
            $failedCount = 0;
            if (file_exists($queueFile)) {
                $queueData = json_decode(file_get_contents($queueFile), true) ?: [];
                foreach ($queueData as $qe) {
                    if (($qe['status'] ?? '') === 'pending') $pendingCount++;
                    if (($qe['status'] ?? '') === 'failed' && ($qe['attempts'] ?? 0) < 5) $failedCount++;
                }
            }
            if ($pendingCount + $failedCount > 0):
            ?>
            <div style="background: #FFF7ED; border: 2px solid #F97316; border-radius: 14px; padding: 1.2rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                <div>
                    <div style="font-size: 1rem; font-weight: 800; color: #C2410C; margin-bottom: 4px;">
                        📬 อีเมลยืนยันคำสั่งซื้อที่ยังไม่ได้ส่ง
                    </div>
                    <div style="font-size: 0.88rem; color: #9A3412;">
                        <?php if ($pendingCount > 0): ?>⏳ รอส่ง: <strong><?= $pendingCount ?></strong> ฉบับ<?php endif; ?>
                        <?php if ($failedCount > 0): ?>&nbsp;❌ ส่งไม่สำเร็จ: <strong><?= $failedCount ?></strong> ฉบับ<?php endif; ?>
                        &nbsp;— กรุณาเปิด <strong>Allow non-browser access</strong> ใน EmailJS account ที่ 2 ก่อน แล้วกดลองส่งใหม่
                    </div>
                </div>
                <form method="POST" action="welcome_sales_mockup.php?view=emailjs" style="margin: 0;">
                    <input type="hidden" name="action" value="retry_order_emails">
                    <button type="submit" style="background: #EA580C; color: white; border: none; border-radius: 10px; padding: 10px 22px; font-size: 0.9rem; font-weight: 700; cursor: pointer; white-space: nowrap;">
                        🔄 ลองส่งใหม่ทั้งหมด
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <hr style="border: 0; border-top: 2px dashed var(--border-color); margin: 2rem 0;">

            <!-- Test Live Delivery Form via EmailJS -->
            <div style="background: var(--bg-card-subtle); border-radius: 16px; padding: 1.8rem; border: 2px dashed #3B82F6;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                    <span style="font-size: 1.8rem;">🧪</span>
                    <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--text-main); margin: 0;">
                        ทดสอบยิงส่งผ่าน EmailJS เข้าอีเมลจริงทันที (ส่งได้ทั้ง 2 รูปแบบ)
                    </h3>
                </div>
                <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.2rem;">
                    เลือกลักษณะจดหมายที่ต้องการทดสอบ แล้วกรอกอีเมลจริงของคุณเพื่อทดสอบยิงผ่าน REST API หรือผ่านเบราว์เซอร์
                </p>

                <form method="POST" action="welcome_sales_mockup.php?view=emailjs" id="testEmailJsForm">
                    <input type="hidden" name="action" value="test_emailjs_email">
                    <input type="hidden" name="test_template_type" id="test_template_type" value="welcome">
                    <input type="hidden" name="emailjs_service_id" id="t_ejs_service">
                    <input type="hidden" name="emailjs_template_id" id="t_ejs_template">
                    <input type="hidden" name="emailjs_template_welcome" id="t_ejs_template_welcome">
                    <input type="hidden" name="emailjs_template_subscribe" id="t_ejs_template_subscribe">
                    <input type="hidden" name="emailjs_public_key" id="t_ejs_public">
                    <input type="hidden" name="emailjs_private_key" id="t_ejs_private">
                    <input type="hidden" name="emailjs_from_name" id="t_ejs_name">
                    <input type="hidden" name="emailjs_from_email" id="t_ejs_email">

                    <!-- Template Selection Radio Cards -->
                    <div style="margin-bottom: 1.2rem;">
                        <label style="font-weight: 800; font-size: 0.92rem; color: var(--text-main); display: block; margin-bottom: 8px;">
                            📑 เลือกรูปแบบจดหมายที่ต้องการทดสอบยิงส่ง:
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 12px;">
                            <!-- Option 1: Welcome (Member Register) -->
                            <div id="tpl_card_welcome" onclick="selectTestTemplate('welcome')" style="display: flex; gap: 12px; padding: 14px; border-radius: 12px; border: 2.5px solid #2563EB; background: #EFF6FF; cursor: pointer; transition: all 0.2s;">
                                <input type="radio" name="ui_template_choice" id="choice_welcome" value="welcome" checked onchange="selectTestTemplate('welcome')" style="margin-top: 4px; accent-color: #2563EB; cursor: pointer;">
                                <label for="choice_welcome" style="cursor: pointer; margin: 0; flex: 1;">
                                    <strong style="color: #1E40AF; font-size: 0.95rem; display: block; margin-bottom: 2px;">
                                        🐱 แบบที่ 1: ต้อนรับสมาชิกใหม่
                                    </strong>
                                    <div style="font-size: 0.8rem; color: #2563EB; font-family: monospace; font-weight: 800; margin-bottom: 4px;">
                                        Template ID: <span id="lbl_tpl_welcome"><?php echo htmlspecialchars($emailjs_config['template_welcome'] ?? 'template_xt7cq3g'); ?></span>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #475569; line-height: 1.4;">
                                        สไตล์ Black & Gold VIP Club, บัตรสมาชิกดิจิทัล, โค้ด <strong>WELCOME15</strong>
                                    </div>
                                </label>
                            </div>

                            <!-- Option 2: Subscribe (Newsletter) -->
                            <div id="tpl_card_subscribe" onclick="selectTestTemplate('subscribe')" style="display: flex; gap: 12px; padding: 14px; border-radius: 12px; border: 2px solid var(--border-color); background: var(--bg-card); cursor: pointer; transition: all 0.2s;">
                                <input type="radio" name="ui_template_choice" id="choice_subscribe" value="subscribe" onchange="selectTestTemplate('subscribe')" style="margin-top: 4px; accent-color: #10B981; cursor: pointer;">
                                <label for="choice_subscribe" style="cursor: pointer; margin: 0; flex: 1;">
                                    <strong style="color: var(--text-main); font-size: 0.95rem; display: block; margin-bottom: 2px;">
                                        📬 แบบที่ 2: ยืนยันรับข่าวสาร
                                    </strong>
                                    <div style="font-size: 0.8rem; color: #059669; font-family: monospace; font-weight: 800; margin-bottom: 4px;">
                                        Template ID: <span id="lbl_tpl_subscribe"><?php echo htmlspecialchars($emailjs_config['template_subscribe'] ?? 'template_y8wnrlt'); ?></span>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #475569; line-height: 1.4;">
                                        สไตล์ Fresh Mint Gazette, ตั๋วคราฟท์รอยปรุ, โค้ด <strong>CATNEWS10</strong>
                                    </div>
                                </label>
                            </div>

                            <!-- Option 3: Order Confirmation (With Images) -->
                            <div id="tpl_card_order" onclick="selectTestTemplate('order')" style="display: flex; gap: 12px; padding: 14px; border-radius: 12px; border: 2px solid var(--border-color); background: var(--bg-card); cursor: pointer; transition: all 0.2s;">
                                <input type="radio" name="ui_template_choice" id="choice_order" value="order" onchange="selectTestTemplate('order')" style="margin-top: 4px; accent-color: #EA580C; cursor: pointer;">
                                <label for="choice_order" style="cursor: pointer; margin: 0; flex: 1;">
                                    <strong style="color: var(--text-main); font-size: 0.95rem; display: block; margin-bottom: 2px;">
                                        🛒 แบบที่ 3: ยืนยันคำสั่งซื้อ + รูปสินค้า
                                    </strong>
                                    <div style="font-size: 0.8rem; color: #EA580C; font-family: monospace; font-weight: 800; margin-bottom: 4px;">
                                        Template ID: <span id="lbl_tpl_order"><?php echo htmlspecialchars($emailjs_config['order_template_id'] ?? 'template_pt8dolh'); ?></span>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #475569; line-height: 1.4;">
                                        สไตล์ Warm Coral & Cream, <strong>แสดงรูปสินค้าน้องแมว</strong>, ยอดชำระ, ที่อยู่จัดส่ง
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                        <div style="min-width: 240px;">
                            <label for="test_ejs_target" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 4px;">
                                📬 กรอกอีเมลจริงของคุณเพื่อรับจดหมายทดสอบ:
                            </label>
                            <input type="email" id="test_ejs_target" name="test_email" value="<?php echo htmlspecialchars(!empty($_GET['target_email']) ? $_GET['target_email'] : 'oavatan@gmail.com'); ?>" required placeholder="oavatan@gmail.com" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-weight: 700;">
                        </div>

                        <div style="min-width: 180px;">
                            <label for="test_ejs_user" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); display: block; margin-bottom: 4px;">
                                👤 ชื่อผู้รับ:
                            </label>
                            <input type="text" id="test_ejs_user" name="test_name" value="<?php echo htmlspecialchars(!empty($_GET['target_name']) ? $_GET['target_name'] : 'คุณโอภาส'); ?>" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main);">
                        </div>

                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button type="button" onclick="sendViaBrowserEmailJS(this)" class="btn btn-primary" style="height: 44px; background: #2563EB; border-color: #1D4ED8; font-weight: 800; padding: 0 1.4rem; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35); font-size: 0.95rem;">
                                ⚡ ยิงส่งทันทีผ่าน Browser
                            </button>
                            <button type="submit" onclick="syncEmailJsTestForm()" class="btn btn-secondary" style="height: 44px; font-weight: 700; padding: 0 1.1rem; display: inline-flex; align-items: center; gap: 6px; font-size: 0.88rem;">
                                🖥️ ยิงผ่านเซิร์ฟเวอร์ PHP
                            </button>
                        </div>
                    </div>
                    <?php
                    require_once __DIR__ . '/email_member_welcome.php';
                    require_once __DIR__ . '/email_newsletter_subscribe.php';
                    require_once __DIR__ . '/email_order_confirmation.php';
                    $default_ejs_rendered_welcome = renderMemberWelcomeEmail('คุณโอภาส', 'oavatan@gmail.com', 'WELCOME15');
                    $default_ejs_rendered_subscribe = renderNewsletterSubscribeEmail('คุณโอภาส', 'oavatan@gmail.com', 'CATNEWS10');
                    $testItemsSample = [
                        ['id' => 'cat_british', 'name' => 'น้องสโนว์ (British Shorthair)', 'breed' => 'บริติช ช็อตแฮร์ (British Shorthair)', 'gender' => 'ผู้ (Male)', 'age' => '2.5 เดือน', 'qty' => 1, 'price' => 18000, 'image' => 'cat_british.jpg'],
                        ['id' => 'cat_persian', 'name' => 'น้องปุยหิมะ (Persian Classic)', 'breed' => 'เปอร์เซีย (Persian)', 'gender' => 'เมีย (Female)', 'age' => '3 เดือน', 'qty' => 1, 'price' => 16500, 'image' => 'cat_persian.jpg'],
                    ];
                    $default_ejs_rendered_order = renderOrderConfirmationEmail('คุณโอภาส', 'oavatan@gmail.com', 'PFC-' . date('ymd') . '-TEST', $testItemsSample, 34500, 1725, 2294.25, 35069.25, '💳 ชำระผ่านบัตรเครดิต / เดบิต', date('Y-m-d', strtotime('+3 days')), '99/99 อาคารทดสอบ ถนนนวัตกรรม แขวงลาดยาว เขตจตุจักร กรุงเทพฯ 10900', 'ช่วงบ่าย (13:00 - 17:00 น.)');
                    ?>
                    <textarea id="ejs_html_welcome" style="display: none;"><?php echo htmlspecialchars($default_ejs_rendered_welcome); ?></textarea>
                    <textarea id="ejs_html_subscribe" style="display: none;"><?php echo htmlspecialchars($default_ejs_rendered_subscribe); ?></textarea>
                    <textarea id="ejs_html_order" style="display: none;"><?php echo htmlspecialchars($default_ejs_rendered_order); ?></textarea>
                    <textarea id="ejs_full_html" style="display: none;"><?php echo htmlspecialchars($default_ejs_rendered_welcome); ?></textarea>
                </form>
            </div>

            <!-- EmailJS Diagnostic Logs Console -->
            <?php if (!empty($emailjs_test_logs)): ?>
                <div style="margin-top: 2rem;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-main); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                        <span>💻</span> บันทึกการส่งผ่าน EmailJS REST API แบบ Real-time (Communication Transcript):
                    </div>
                    <div class="terminal-console">
                        <?php foreach ($emailjs_test_logs as $lg): ?>
                            <div class="terminal-line">
                                <span style="color: #64748B;">[<?php echo htmlspecialchars($lg['time']); ?>]</span>
                                <?php if ($lg['direction'] === 'CLIENT'): ?>
                                    <span class="term-client">&gt; <?php echo htmlspecialchars($lg['message']); ?></span>
                                <?php elseif ($lg['direction'] === 'SERVER'): ?>
                                    <span class="term-server">&lt; <?php echo htmlspecialchars($lg['message']); ?></span>
                                <?php elseif ($lg['direction'] === 'ERROR'): ?>
                                    <span class="term-error">✖ <?php echo htmlspecialchars($lg['message']); ?></span>
                                <?php else: ?>
                                    <span class="term-info">&bull; <?php echo htmlspecialchars($lg['message']); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- ================================================================== -->
    <!-- VIEW MODE: OUTBOX MAIL LOGS -->
    <!-- ================================================================== -->
    <?php if ($current_view === 'outbox'): ?>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.8rem; box-shadow: var(--shadow-sm);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">
                        📮 บันทึกประวัติการส่งอีเมลจริง (Outbox Mail Logs)
                    </h3>
                    <p style="font-size: 0.88rem; color: var(--text-muted); margin: 0;">
                        รายการอีเมลและ Content ขายสินค้าที่ส่งออกจริงทาง Live SMTP หรือบันทึกเข้ากล่องจดหมายสมาชิก
                    </p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <a href="?view=smtp" class="btn btn-secondary btn-sm">
                        ⚙️ ตั้งค่า SMTP
                    </a>
                    <a href="?view=desktop" class="btn btn-primary btn-sm">
                        ✉️ ส่งอีเมลฉบับใหม่
                    </a>
                </div>
            </div>

            <?php if (empty($all_outbox)): ?>
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--text-muted);">
                    <span style="font-size: 3rem; display: block; margin-bottom: 0.5rem;">📭</span>
                    <p>ยังไม่มีประวัติการส่งอีเมลในระบบ</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="outbox-table">
                        <thead>
                            <tr>
                                <th>รหัสการส่ง / เวลา</th>
                                <th>ผู้ส่ง (From)</th>
                                <th>ผู้รับ (To)</th>
                                <th>หัวข้ออีเมล (Subject)</th>
                                <th>โค้ดส่วนลด</th>
                                <th>สถานะการส่งจริง</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_outbox as $item): ?>
                                <tr>
                                    <td>
                                        <strong style="color: var(--primary-coral); font-family: 'Outfit';">
                                            #<?php echo htmlspecialchars($item['id']); ?>
                                        </strong>
                                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                                            🕒 <?php echo htmlspecialchars($item['sent_at']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <img src="<?php echo htmlspecialchars($item['sender_avatar'] ?? 'assets/images/logo.png'); ?>" alt="" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1.5px solid #6366F1;">
                                            <div style="max-width: 140px;">
                                                <strong style="font-size: 0.84rem; color: var(--text-main); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?php echo htmlspecialchars($item['sender_name'] ?? 'Purrfect Shop'); ?>
                                                </strong>
                                                <div style="font-size: 0.72rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?php echo htmlspecialchars($item['sender_email'] ?? '-'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['to_name']); ?></strong>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                                            ✉️ <?php echo htmlspecialchars($item['to_email']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-main);">
                                            <?php echo htmlspecialchars($item['subject']); ?>
                                        </div>
                                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                                            <?php echo htmlspecialchars($item['preview'] ?? ''); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="background: #FEF3C7; color: #92400E; font-family: monospace; font-weight: 800; font-size: 0.85rem; padding: 3px 8px; border-radius: 6px; border: 1px solid #FCD34D;">
                                            <?php echo htmlspecialchars($item['voucher_code'] ?? '-'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $d_status = $item['delivery_status'] ?? ($item['php_mail_status'] ?? 'LOCAL_LOGGED'); 
                                        if ($d_status === 'DELIVERED_VIA_EMAILJS'):
                                        ?>
                                            <span style="background: #DBEAFE; color: #1E40AF; font-size: 0.78rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
                                                ⚡ ส่งถึงอีเมลจริงผ่าน EmailJS
                                            </span>
                                        <?php elseif ($d_status === 'DELIVERED_VIA_SMTP'):
                                        ?>
                                            <span style="background: #DCFCE7; color: #166534; font-size: 0.78rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
                                                🟢 ส่งถึงอีเมลจริงผ่าน SMTP
                                            </span>
                                        <?php elseif ($d_status === 'EMAILJS_FAILED'): ?>
                                            <span style="background: #FEE2E2; color: #991B1B; font-size: 0.78rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
                                                ❌ EmailJS ล้มเหลว
                                            </span>
                                        <?php elseif ($d_status === 'SMTP_FAILED'): ?>
                                            <span style="background: #FEE2E2; color: #991B1B; font-size: 0.78rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
                                                ❌ SMTP ล้มเหลว
                                            </span>
                                        <?php else: ?>
                                            <span style="background: #E0F2FE; color: #075985; font-size: 0.78rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
                                                🟡 บันทึกในระบบ (Outbox Mode)
                                            </span>
                                        <?php endif; ?>
                                        <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 3px; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($item['server_notice'] ?? $d_status); ?>">
                                            <?php echo htmlspecialchars($item['server_notice'] ?? $d_status); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <a href="?view=desktop&name=<?php echo urlencode($item['to_name']); ?>&email=<?php echo urlencode($item['to_email']); ?>&code=<?php echo urlencode($item['voucher_code'] ?? 'WELCOME15'); ?>" class="btn btn-secondary btn-sm" style="padding: 3px 8px; font-size: 0.75rem;">
                                                🔍 ดูพรีวิว
                                            </a>
                                            <a href="profile.php?tab=inbox" class="btn btn-secondary btn-sm" style="padding: 3px 8px; font-size: 0.75rem;">
                                                📬 กล่องข้อความ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- ================================================================== -->
    <!-- VIEW MODE: DESKTOP / MOBILE / CODE / DISPATCH FORM (เมื่อไม่ได้อยู่ในโหมด SMTP หรือ OUTBOX) -->
    <!-- ================================================================== -->
    <?php if ($current_view !== 'smtp' && $current_view !== 'emailjs' && $current_view !== 'outbox'): ?>
        <!-- Mockup Presentation Controller & Dispatcher -->
        <div class="mockup-control-panel">
            <div class="mockup-badge">
                <span>✉️</span>
                <span>ระบบส่งข้อมูล • LIVE EMAIL DISPATCHER</span>
                <span>🚀</span>
            </div>
            <h1 class="mockup-title">
                ส่งข้อมูล Content ขายสินค้า: เลือกว่าจะส่งให้ใครได้ตามต้องการ
            </h1>
            <p class="mockup-desc">
                สามารถเลือกผู้รับได้ทั้งแบบ <strong>รายบุคคล</strong>, <strong>เลือกหลายคนพร้อมกันผ่าน Checklist</strong> หรือ <strong>ยิงส่งให้สมาชิกทุกคนในระบบพร้อมกันทันที</strong>!
            </p>

            <!-- Dispatch Form -->
            <form method="POST" action="welcome_sales_mockup.php" id="mailSendForm">
                <input type="hidden" name="action" value="send_real_email">
                <input type="hidden" name="view" value="<?php echo htmlspecialchars($current_view); ?>">

                <!-- ========================================================== -->
                <!-- SENDER PROFILE PICKER (เลือกผู้ส่งจดหมาย พร้อมรูปโปรไฟล์) -->
                <!-- ========================================================== -->
                <div style="background: var(--bg-card); border: 2px solid #6366F1; border-radius: var(--radius-lg); padding: 1.4rem; margin-bottom: 1.6rem; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.08);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 1.35rem;">👤</span>
                            <strong style="font-size: 1.05rem; color: var(--text-main);">
                                1. เลือก "ผู้ส่ง" จดหมาย (Sender Profile):
                            </strong>
                        </div>
                        <span style="font-size: 0.8rem; color: #4338CA; background: #EEF2FF; padding: 4px 12px; border-radius: 999px; border: 1px solid #C7D2FE; font-weight: 700;">
                            ✨ เลือกผู้ใช้ที่สมัครในระบบเพื่อเป็นผู้จัดส่ง
                        </span>
                    </div>

                    <!-- Hidden sender fields submitted with form -->
                    <input type="hidden" name="sender_user_id" id="sender_user_id" value="<?php echo htmlspecialchars($active_sender_id); ?>">
                    <input type="hidden" name="sender_name" id="sender_name" value="<?php echo htmlspecialchars($active_sender_name); ?>">
                    <input type="hidden" name="sender_email" id="sender_email" value="<?php echo htmlspecialchars($active_sender_email); ?>">
                    <input type="hidden" name="sender_avatar" id="sender_avatar" value="<?php echo htmlspecialchars($active_sender_avatar); ?>">

                    <!-- Selected Sender Showcase Card -->
                    <div style="display: flex; align-items: center; gap: 14px; background: var(--bg-page); border: 1.5px solid #818CF8; padding: 12px 18px; border-radius: 14px; margin-bottom: 14px; flex-wrap: wrap;">
                        <div style="position: relative;">
                            <img id="sender_preview_avatar" src="<?php echo htmlspecialchars($active_sender_avatar); ?>" alt="Sender Avatar" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2.5px solid #6366F1; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.25);" onerror="this.src='assets/images/logo.png'">
                            <span id="sender_badge_role" style="position: absolute; bottom: -3px; right: -3px; background: #6366F1; color: #FFFFFF; font-size: 0.62rem; font-weight: 800; padding: 2px 6px; border-radius: 999px; border: 1.5px solid #FFFFFF;">
                                <?php echo (!empty($currUser['role']) && $currUser['role'] === 'admin') ? '👑 ADMIN' : '🐱 SENDER'; ?>
                            </span>
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <div style="font-weight: 800; font-size: 1.05rem; color: var(--text-main);" id="sender_preview_name">
                                <?php echo htmlspecialchars($active_sender_name); ?>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 2px;" id="sender_preview_email">
                                ✉️ <?php echo htmlspecialchars($active_sender_email); ?>
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 0.82rem; color: #047857; background: #DCFCE7; font-weight: 800; padding: 6px 14px; border-radius: 8px; border: 1px solid #86EFAC; display: inline-flex; align-items: center; gap: 4px;">
                                🟢 ผู้ส่งที่เลือกอยู่ในขณะนี้
                            </span>
                        </div>
                    </div>

                    <!-- Horizontal Scroll / Grid of Registered Users with Avatars -->
                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 8px;">
                        👇 คลิกเลือกเปลี่ยนผู้ส่งจากรายชื่อผู้ใช้ที่สมัครในระบบ (มีรูปโปรไฟล์):
                    </div>
                    <div style="display: flex; gap: 10px; overflow-x: auto; padding: 4px 2px 10px 2px;" class="sender-avatar-scroll">
                        <!-- Default Store Option -->
                        <div class="sender-card-item" onclick="selectSender('shop_default', 'Purrfect Cattery & Boutique 🐾', 'purrfect.cattery.shop@gmail.com', 'assets/images/logo.png', 'STORE', this)" style="cursor: pointer; padding: 8px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); background: var(--bg-card); display: flex; align-items: center; gap: 10px; min-width: 210px; transition: all 0.2s;">
                            <img src="assets/images/logo.png" alt="Shop" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--primary-coral);">
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); white-space: nowrap;">ร้านค้าส่วนกลาง 🐾</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted);">purrfect.shop@gmail.com</div>
                            </div>
                        </div>

                        <?php foreach ($all_users as $u): ?>
                            <?php 
                            $u_avatar = !empty($u['avatar']) ? $u['avatar'] : 'assets/images/logo.png'; 
                            $is_current = ($currUser && $currUser['id'] === $u['id']);
                            $u_role = (!empty($u['role']) && $u['role'] === 'admin') ? '👑 ADMIN' : '🐱 สมาชิก';
                            ?>
                            <div class="sender-card-item <?php echo $is_current ? 'selected' : ''; ?>" 
                                 onclick="selectSender('<?php echo htmlspecialchars($u['id']); ?>', '<?php echo htmlspecialchars(addslashes($u['fullname'])); ?>', '<?php echo htmlspecialchars(addslashes($u['email'])); ?>', '<?php echo htmlspecialchars(addslashes($u_avatar)); ?>', '<?php echo htmlspecialchars(addslashes($u_role)); ?>', this)" 
                                 style="cursor: pointer; padding: 8px 12px; border-radius: 10px; border: 1.5px solid <?php echo $is_current ? '#6366F1' : 'var(--border-color)'; ?>; background: <?php echo $is_current ? '#EEF2FF' : 'var(--bg-card)'; ?>; display: flex; align-items: center; gap: 10px; min-width: 220px; transition: all 0.2s;">
                                <img src="<?php echo htmlspecialchars($u_avatar); ?>" alt="" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1.5px solid #6366F1;">
                                <div style="overflow: hidden; flex: 1;">
                                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($u['fullname']); ?>
                                    </div>
                                    <div style="font-size: 0.72rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($u['email']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 2. Send Mode Selector Cards -->
                <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-main); margin-bottom: 8px;">
                    🎯 2. เลือกกลุ่มผู้รับอีเมล:
                </div>

                <div class="send-mode-selector">
                    <!-- Option 1: Single Recipient -->
                    <label class="send-mode-card selected" id="card_mode_single" onclick="setSendMode('single')">
                        <input type="radio" name="send_mode" value="single" checked onchange="setSendMode('single')">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">👤 ส่งให้รายบุคคล</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">เลือกสมาชิก 1 คน หรือพิมพ์ชื่อ-อีเมลเอง</div>
                        </div>
                    </label>

                    <!-- Option 2: Multiple Selection -->
                    <label class="send-mode-card" id="card_mode_multiple" onclick="setSendMode('multiple')">
                        <input type="radio" name="send_mode" value="multiple" onchange="setSendMode('multiple')">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">👥 เลือกหลายคนจากรายชื่อ</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">ติ๊กเลือกสมาชิกที่ต้องการส่งทีละหลายคน</div>
                        </div>
                    </label>

                    <!-- Option 3: All Members -->
                    <label class="send-mode-card" id="card_mode_all" onclick="setSendMode('all')">
                        <input type="radio" name="send_mode" value="all" onchange="setSendMode('all')">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">🌐 สมาชิกทุกคนในระบบ</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">ยิงส่งพร้อมกันทั้งหมด (<?php echo count($all_users); ?> ท่าน)</div>
                        </div>
                    </label>
                </div>

                <!-- Mode Container 1: Single User Selector -->
                <div id="section_single" style="background: var(--bg-page); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.2rem; margin-bottom: 1.2rem;">
                    <div style="margin-bottom: 12px;">
                        <label for="single_user_id" style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); display: block; margin-bottom: 4px;">
                            📌 เลือกจากสมาชิกในระบบ (อัปเดตชื่อและรูปโปรไฟล์ผู้รับอัตโนมัติ):
                        </label>
                        <select name="single_user_id" id="single_user_id" onchange="onSingleUserChange(this)" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-size: 0.9rem;">
                            <option value="">-- หรือเลือกจากรายชื่อสมาชิกในระบบ --</option>
                            <?php foreach ($all_users as $u): ?>
                                <?php $u_av = !empty($u['avatar']) ? $u['avatar'] : 'assets/images/logo.png'; ?>
                                <option value="<?php echo htmlspecialchars($u['id']); ?>" 
                                        data-name="<?php echo htmlspecialchars($u['fullname']); ?>" 
                                        data-email="<?php echo htmlspecialchars($u['email']); ?>"
                                        data-avatar="<?php echo htmlspecialchars($u_av); ?>">
                                    <?php echo htmlspecialchars($u['fullname']); ?> (@<?php echo htmlspecialchars($u['username']); ?> &bull; <?php echo htmlspecialchars($u['email']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Recipient Live Avatar Preview Card -->
                    <div id="recipient_preview_box" style="display: flex; align-items: center; gap: 12px; background: var(--bg-card); border: 1.5px dashed var(--primary-coral); padding: 10px 14px; border-radius: 10px; margin-bottom: 12px;">
                        <img id="recipient_preview_avatar" src="assets/images/logo.png" alt="Avatar" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-coral);">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-main);" id="recipient_preview_name">
                                <?php echo htmlspecialchars($recipient_name); ?>
                            </div>
                            <div style="font-size: 0.82rem; color: var(--text-muted);" id="recipient_preview_email">
                                ✉️ <?php echo htmlspecialchars($recipient_email); ?>
                            </div>
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--primary-coral); background: rgba(255, 117, 86, 0.1); padding: 4px 10px; border-radius: 999px;">
                            👤 ผู้รับที่เลือก
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label for="name" style="font-weight: 700; font-size: 0.82rem; color: var(--text-secondary); display: block; margin-bottom: 4px;">
                                ชื่อลูกค้าผู้รับ:
                            </label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($recipient_name); ?>" placeholder="ชื่อผู้รับ..." style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-main);">
                        </div>
                        <div>
                            <label for="email" style="font-weight: 700; font-size: 0.82rem; color: var(--text-secondary); display: block; margin-bottom: 4px;">
                                อีเมลผู้รับ:
                            </label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($recipient_email); ?>" placeholder="name@example.com" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-main);">
                        </div>
                    </div>
                </div>

                <!-- Mode Container 2: Multi-select Checklist -->
                <div id="section_multiple" style="display: none; background: var(--bg-page); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.2rem; margin-bottom: 1.2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                        <span style="font-weight: 700; font-size: 0.9rem; color: var(--text-main);">
                            👥 ติ๊กเลือกสมาชิกที่ต้องการส่งถึง (<span id="selected_count">0</span> คน):
                        </span>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="selectAllUsers(true)" style="padding: 3px 10px; font-size: 0.8rem;">
                                ✓ เลือกทุกคน
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="selectAllUsers(false)" style="padding: 3px 10px; font-size: 0.8rem;">
                                ✗ ล้างการเลือก
                            </button>
                        </div>
                    </div>

                    <div class="user-checkbox-grid">
                        <?php foreach ($all_users as $u): ?>
                            <label class="user-check-item">
                                <input type="checkbox" name="selected_users[]" value="<?php echo htmlspecialchars($u['id']); ?>" onchange="updateSelectedCount()">
                                <img src="<?php echo htmlspecialchars($u['avatar'] ?? 'assets/images/logo.png'); ?>" alt="" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid var(--primary-coral);">
                                <div style="overflow: hidden; flex: 1;">
                                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($u['fullname']); ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($u['email']); ?>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Mode Container 3: All Users Summary Banner -->
                <div id="section_all" style="display: none; background: #ECFDF5; border: 1.5px solid #A7F3D0; border-radius: var(--radius-md); padding: 1.2rem; margin-bottom: 1.2rem; color: #065F46;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 2rem;">🌐</span>
                        <div>
                            <strong style="font-size: 1.05rem; display: block;">พร้อมส่งอีเมลหาลูกค้าสมาชิกทุกคนในระบบ!</strong>
                            <span style="font-size: 0.88rem;">ระบบจะทำการส่งอีเมลต้อนรับและคูปองส่วนลดไปยังสมาชิกทั้งหมด <strong><?php echo count($all_users); ?> ท่าน</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Voucher Code & Dispatch Trigger Row -->
                <div style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; background: var(--bg-page); padding: 1.2rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <div style="flex: 1; min-width: 220px;">
                        <label for="code" style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); display: block; margin-bottom: 4px;">
                            🎟️ โค้ดส่วนลดที่แนบไป:
                        </label>
                        <input type="text" id="code" name="code" value="<?php echo htmlspecialchars($voucher_code); ?>" placeholder="WELCOME15" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-weight: 700;">
                    </div>

                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" class="btn btn-secondary" onclick="updatePreviewParams()" style="height: 42px; font-weight: 700; padding: 0 1.2rem;">
                            🔄 อัปเดตพรีวิว
                        </button>
                        <button type="submit" class="btn btn-primary" id="btn_submit_send" style="height: 42px; font-weight: 800; padding: 0 1.8rem; box-shadow: var(--shadow-coral); display: inline-flex; align-items: center; gap: 8px;">
                            🚀 ส่งอีเมลหาผู้รับที่เลือกทันที!
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Render Desktop Preview -->
        <?php if ($current_view === 'desktop'): ?>
            <div style="background: #FFFFFF; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); border: 1px solid #E2E8F0; overflow: hidden; margin-top: 1.5rem;">
                <div style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; padding: 0.8rem 1.2rem; display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #F59E0B; display: inline-block;"></span>
                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
                    <div style="flex: 1; text-align: center; font-size: 0.82rem; font-weight: 700; color: #64748B;">
                        Apple Mail / Gmail Client Preview &bull; Purrfect Shop VIP Welcome
                    </div>
                </div>
                <div>
                    <?php
                    require_once __DIR__ . '/email_member_welcome.php';
                    echo renderMemberWelcomeEmail($recipient_name, $recipient_email, $voucher_code, $member_id, $active_sender_info);
                    ?>
                </div>
            </div>
        <?php elseif ($current_view === 'mobile'): ?>
            <!-- Mobile Frame Simulation -->
            <div style="max-width: 420px; margin: 1.5rem auto 0 auto; border: 12px solid #1E293B; border-radius: 40px; box-shadow: 0 25px 60px rgba(0,0,0,0.25); background: #FFFFFF; overflow: hidden;">
                <div style="width: 140px; height: 20px; background: #1E293B; margin: 0 auto; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;"></div>
                <div style="max-height: 720px; overflow-y: auto;">
                    <?php
                    require_once __DIR__ . '/email_member_welcome.php';
                    echo renderMemberWelcomeEmail($recipient_name, $recipient_email, $voucher_code, $member_id, $active_sender_info);
                    ?>
                </div>
            </div>
        <?php elseif ($current_view === 'code'): ?>
            <!-- Raw HTML Box -->
            <div style="background: #0F172A; color: #E2E8F0; padding: 1.5rem; border-radius: 12px; font-family: Consolas, monospace; font-size: 0.85rem; max-height: 650px; overflow-y: auto; margin-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <strong style="color: #38BDF8;">📋 โค้ด HTML เต็มรูปแบบ (พร้อมใช้สำหรับ Email Template / MailChimp / Brevo / SMTP)</strong>
                    <button type="button" class="btn btn-primary btn-sm" onclick="copyCode()">คัดลอกโค้ด</button>
                </div>
                <pre id="raw-html-code" style="margin: 0; white-space: pre-wrap; word-break: break-all;"><?php
                    require_once __DIR__ . '/email_member_welcome.php';
                    echo htmlspecialchars(renderMemberWelcomeEmail($recipient_name, $recipient_email, $voucher_code, $member_id, $active_sender_info));
                ?></pre>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

<!-- EmailJS Browser SDK CDN (Load before scripts) -->
<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>

<script>
function setSendMode(mode) {
    document.querySelectorAll('.send-mode-card').forEach(c => c.classList.remove('selected'));
    const targetCard = document.getElementById('card_mode_' + mode);
    if (targetCard) targetCard.classList.add('selected');

    const radio = document.querySelector('input[name="send_mode"][value="' + mode + '"]');
    if (radio) radio.checked = true;

    document.getElementById('section_single').style.display = (mode === 'single' ? 'block' : 'none');
    document.getElementById('section_multiple').style.display = (mode === 'multiple' ? 'block' : 'none');
    document.getElementById('section_all').style.display = (mode === 'all' ? 'block' : 'none');

    const btnSubmit = document.getElementById('btn_submit_send');
    if (btnSubmit) {
        if (mode === 'all') {
            btnSubmit.innerHTML = '🚀 ยิงส่งให้สมาชิกทุกคน (<?php echo count($all_users); ?> ท่าน)!';
        } else if (mode === 'multiple') {
            updateSelectedCount();
        } else {
            btnSubmit.innerHTML = '🚀 ส่งอีเมลหาลูกค้ารายนี้ทันที!';
        }
    }
}

function selectSender(userId, name, email, avatar, role, el) {
    document.getElementById('sender_user_id').value = userId;
    document.getElementById('sender_name').value = name;
    document.getElementById('sender_email').value = email;
    document.getElementById('sender_avatar').value = avatar;

    var avEl = document.getElementById('sender_preview_avatar');
    if (avEl) avEl.src = avatar;
    var nameEl = document.getElementById('sender_preview_name');
    if (nameEl) nameEl.innerText = name;
    var emailEl = document.getElementById('sender_preview_email');
    if (emailEl) emailEl.innerHTML = '✉️ ' + email;
    var badgeEl = document.getElementById('sender_badge_role');
    if (badgeEl) badgeEl.innerText = role;

    document.querySelectorAll('.sender-card-item').forEach(function(item) {
        item.style.borderColor = 'var(--border-color)';
        item.style.background = 'var(--bg-card)';
    });
    if (el) {
        el.style.borderColor = '#6366F1';
        el.style.background = '#EEF2FF';
    }
}

function onSingleUserChange(selectEl) {
    if (!selectEl.value) return;
    const opt = selectEl.options[selectEl.selectedIndex];
    const name = opt.getAttribute('data-name');
    const email = opt.getAttribute('data-email');
    const avatar = opt.getAttribute('data-avatar') || 'assets/images/logo.png';

    document.getElementById('name').value = name;
    document.getElementById('email').value = email;

    var avEl = document.getElementById('recipient_preview_avatar');
    if (avEl) avEl.src = avatar;
    var nameEl = document.getElementById('recipient_preview_name');
    if (nameEl) nameEl.innerText = name;
    var emailEl = document.getElementById('recipient_preview_email');
    if (emailEl) emailEl.innerText = '✉️ ' + email;
}

function selectAllUsers(checkState) {
    const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
    checkboxes.forEach(cb => cb.checked = checkState);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('input[name="selected_users[]"]:checked');
    const countEl = document.getElementById('selected_count');
    if (countEl) countEl.innerText = checkboxes.length;

    const btnSubmit = document.getElementById('btn_submit_send');
    const currentMode = document.querySelector('input[name="send_mode"]:checked')?.value;
    if (btnSubmit && currentMode === 'multiple') {
        btnSubmit.innerHTML = '🚀 ส่งอีเมลหาผู้รับที่เลือก (' + checkboxes.length + ' ท่าน)!';
    }
}

function updatePreviewParams() {
    const name = encodeURIComponent(document.getElementById('name').value);
    const email = encodeURIComponent(document.getElementById('email').value);
    const code = encodeURIComponent(document.getElementById('code').value);
    const senderId = encodeURIComponent(document.getElementById('sender_user_id')?.value || '');
    const senderName = encodeURIComponent(document.getElementById('sender_name')?.value || '');
    const senderEmail = encodeURIComponent(document.getElementById('sender_email')?.value || '');
    const senderAvatar = encodeURIComponent(document.getElementById('sender_avatar')?.value || '');
    const view = '<?php echo htmlspecialchars($current_view === "outbox" || $current_view === "smtp" ? "desktop" : $current_view); ?>';

    window.location.href = 'welcome_sales_mockup.php?view=' + view + '&name=' + name + '&email=' + email + '&code=' + code + '&sender_id=' + senderId + '&sender_name=' + senderName + '&sender_email=' + senderEmail + '&sender_avatar=' + senderAvatar;
}

function copyCode() {
    const codeEl = document.getElementById('raw-html-code');
    if (codeEl) {
        navigator.clipboard.writeText(codeEl.innerText).then(() => {
            alert("✓ คัดลอกโค้ด HTML สำเร็จแล้ว!");
        });
    }
}

function togglePasswordVisibility() {
    const passInput = document.getElementById('smtp_password');
    if (passInput) {
        passInput.type = passInput.type === 'password' ? 'text' : 'password';
    }
}

function applySmtpPreset(provider) {
    const hostEl = document.getElementById('smtp_host');
    const portEl = document.getElementById('smtp_port');
    const encEl = document.getElementById('smtp_encryption');

    if (provider === 'gmail') {
        if (hostEl) hostEl.value = 'smtp.gmail.com';
        if (portEl) portEl.value = '587';
        if (encEl) encEl.value = 'tls';
        alert("✓ กำหนดค่า Gmail สำเร็จ (Host: smtp.gmail.com, Port: 587, STARTTLS)\nกรุณากรอก Username เป็นที่อยู่ Gmail ของคุณ และใช้ Google App Password 16 หลักในช่องรหัสผ่าน");
    } else if (provider === 'outlook') {
        if (hostEl) hostEl.value = 'smtp.office365.com';
        if (portEl) portEl.value = '587';
        if (encEl) encEl.value = 'tls';
        alert("✓ กำหนดค่า Microsoft Outlook/Hotmail สำเร็จ (Host: smtp.office365.com, Port: 587, STARTTLS)");
    } else if (provider === 'custom') {
        if (hostEl) hostEl.value = '';
        if (portEl) portEl.value = '587';
        if (encEl) encEl.value = 'tls';
        if (hostEl) hostEl.focus();
    }
}


var currentSelectedTemplate = 'welcome';

function selectTestTemplate(type) {
    if (type !== 'subscribe') type = 'welcome';
    currentSelectedTemplate = type;
    
    var inp = document.getElementById('test_template_type');
    if (inp) inp.value = type;

    var radWel = document.getElementById('choice_welcome');
    var radSub = document.getElementById('choice_subscribe');
    if (radWel) radWel.checked = (type === 'welcome');
    if (radSub) radSub.checked = (type === 'subscribe');

    var cardWel = document.getElementById('tpl_card_welcome');
    var cardSub = document.getElementById('tpl_card_subscribe');
    var fullHtml = document.getElementById('ejs_full_html');
    var htmlWel = document.getElementById('ejs_html_welcome');
    var htmlSub = document.getElementById('ejs_html_subscribe');

    if (type === 'subscribe') {
        if (cardSub) {
            cardSub.style.borderColor = '#10B981';
            cardSub.style.backgroundColor = '#ECFDF5';
            var sTitle = cardSub.querySelector('strong');
            if (sTitle) sTitle.style.color = '#065F46';
        }
        if (cardWel) {
            cardWel.style.borderColor = 'var(--border-color)';
            cardWel.style.backgroundColor = 'var(--bg-card)';
            var wTitle = cardWel.querySelector('strong');
            if (wTitle) wTitle.style.color = 'var(--text-main)';
        }
        if (fullHtml && htmlSub) fullHtml.value = htmlSub.value;
    } else {
        if (cardWel) {
            cardWel.style.borderColor = '#2563EB';
            cardWel.style.backgroundColor = '#EFF6FF';
            var wTitle = cardWel.querySelector('strong');
            if (wTitle) wTitle.style.color = '#1E40AF';
        }
        if (cardSub) {
            cardSub.style.borderColor = 'var(--border-color)';
            cardSub.style.backgroundColor = 'var(--bg-card)';
            var sTitle = cardSub.querySelector('strong');
            if (sTitle) sTitle.style.color = 'var(--text-main)';
        }
        if (fullHtml && htmlWel) fullHtml.value = htmlWel.value;
    }
}

function sendViaBrowserEmailJS(btn) {
    if (!btn) btn = (typeof event !== 'undefined' && event && event.target) ? event.target : document.activeElement;
    
    // Always detect selected radio directly
    var checkedRadio = document.querySelector('input[name="ui_template_choice"]:checked');
    var activeType = checkedRadio ? checkedRadio.value : currentSelectedTemplate;
    if (activeType !== 'subscribe') activeType = 'welcome';
    currentSelectedTemplate = activeType;

    var pkey = document.getElementById('emailjs_public_key')?.value || '';
    var sid = document.getElementById('emailjs_service_id')?.value || '';
    var tidWel = document.getElementById('emailjs_template_welcome')?.value || 'template_xt7cq3g';
    var tidSub = document.getElementById('emailjs_template_subscribe')?.value || 'template_y8wnrlt';
    var tid = (activeType === 'subscribe') ? tidSub : tidWel;
    var targetEmail = document.getElementById('test_ejs_target')?.value || '';
    var targetName = document.getElementById('test_ejs_user')?.value || 'ผู้ทดสอบระบบ';

    if (!targetEmail) {
        alert('กรุณากรอกอีเมลผู้รับเพื่อทดสอบ (เช่น oavatan@gmail.com)');
        return;
    }
    if (targetEmail.indexOf('{{') !== -1 || targetEmail.indexOf('}}') !== -1 || targetEmail.indexOf('to_email') !== -1) {
        alert('⚠️ คุณกำลังกรอกตัวแปร {{to_email}} ลงในช่องบนหน้าเว็บ!\n\n💡 ช่องนี้ต้องกรอกเป็น "อีเมลจริง" (เช่น oavatan@gmail.com)\nส่วนตัวแปร {{to_email}} ให้นำไปใส่ในช่อง "To Email" บนหน้าเว็บ EmailJS Dashboard (emailjs.com) ครับ');
        return;
    }
    if (!sid || !tid || !pkey) {
        alert('กรุณากรอก Service ID, Template ID และ Public Key ในแบบฟอร์มด้านบน');
        return;
    }

    var origText = btn ? btn.innerHTML : '⚡ ยิงส่งทันทีผ่าน Browser';
    if (btn) {
        btn.innerHTML = '⏳ กำลังยิงส่งผ่าน EmailJS (' + tid + ')...';
        btn.disabled = true;
    }

    if (typeof emailjs === 'undefined') {
        if (btn) { btn.innerHTML = origText; btn.disabled = false; }
        alert('⚠️ ระบบยังไม่โหลดไลบรารี EmailJS จาก CDN\nกรุณากดปุ่ม "🖥️ ยิงผ่านเซิร์ฟเวอร์ PHP" ด้านข้างแทนได้ทันทีครับ');
        return;
    }

    // Determine correct HTML and Subject based on activeType
    var htmlWel = document.getElementById('ejs_html_welcome')?.value || '';
    var htmlSub = document.getElementById('ejs_html_subscribe')?.value || '';
    var activeHtml = (activeType === 'subscribe') ? htmlSub : htmlWel;

    var subjectStr = (activeType === 'subscribe') 
        ? '📬 [ทดสอบรับข่าวสาร] ขอบคุณที่ติดตาม Purrfect Shop 🐾 (โค้ด: CATNEWS10)' 
        : '🧪 [ทดสอบส่งจริงผ่าน EmailJS] ยินดีต้อนรับคุณ' + targetName + ' - Purrfect Shop 🐾 (โค้ด: WELCOME15)';
    var vCodeStr = (activeType === 'subscribe') ? 'CATNEWS10' : 'WELCOME15';

    try {
        emailjs.init({ publicKey: pkey });
        emailjs.send(sid, tid, {
            to_email: targetEmail,
            email: targetEmail,
            user_email: targetEmail,
            recipient_email: targetEmail,
            to: targetEmail,
            target_email: targetEmail,
            recipient: targetEmail,
            ' to_email ': targetEmail,
            'to_email ': targetEmail,
            ' to_email': targetEmail,
            ' email ': targetEmail,
            ' to ': targetEmail,
            to_name: targetName,
            name: targetName,
            user_name: targetName,
            recipient_name: targetName,
            ' to_name ': targetName,
            from_name: document.getElementById('emailjs_from_name')?.value || 'Purrfect Cattery & Boutique 🐾',
            from_email: document.getElementById('emailjs_from_email')?.value || 'purrfect.cattery.shop@gmail.com',
            subject: subjectStr,
            message_html: activeHtml,
            message: (activeType === 'subscribe') 
                ? 'ขอบคุณที่ติดตามข่าวสาร Purrfect Shop โค้ดส่วนลด 10% คือ CATNEWS10' 
                : 'ขอต้อนรับคุณ ' + targetName + ' สู่ Purrfect Shop โค้ดส่วนลด VIP 15% คือ WELCOME15',
            voucher_code: vCodeStr,
            template_type: activeType
        }).then(function(response) {
            if (btn) { btn.innerHTML = origText; btn.disabled = false; }
            var typeTitle = (activeType === 'subscribe') ? 'แบบที่ 2 (รับข่าวสาร - ' + tid + ')' : 'แบบที่ 1 (ต้อนรับสมาชิก - ' + tid + ')';
            alert('🎉 ยอดเยี่ยมมาก! ยิงส่ง ' + typeTitle + ' สำเร็จ 100% (HTTP ' + response.status + ' OK)\nกรุณาตรวจสอบกล่องข้อความ Inbox หรือโฟลเดอร์สแปมของ ' + targetEmail);
            location.reload();
        }, function(error) {
            if (btn) { btn.innerHTML = origText; btn.disabled = false; }
            var errStr = (error && (error.text || error.message)) ? (error.text || error.message) : JSON.stringify(error);
            var tip = '';
            if (errStr.indexOf('corrupted') !== -1) {
                tip = '\n\n💡 สาเหตุ: รูปแบบอีเมลไม่ถูกต้อง ตรวจสอบว่าช่องหน้าเว็บกรอกอีเมลจริง (เช่น oavatan@gmail.com) และใน EmailJS Dashboard ช่อง To Email ใส่ {{to_email}} พอดี';
            } else if (errStr.indexOf('empty') !== -1) {
                tip = '\n\n💡 สาเหตุ: EmailJS ไม่พบอีเมลผู้รับ ตรวจสอบว่าใน EmailJS Dashboard เมนู Templates ช่อง "To Email" ได้ใส่ {{to_email}} และกดปุ่ม Save แล้วหรือไม่';
            }
            alert('⚠️ EmailJS แจ้งเตือน: ' + errStr + tip + '\n\n💡 หากเกิดปัญหา สามารถกดปุ่ม "🖥️ ยิงผ่านเซิร์ฟเวอร์ PHP" ด้านข้างแทนได้ทันทีครับ');
        });
    } catch (e) {
        if (btn) { btn.innerHTML = origText; btn.disabled = false; }
        alert('เกิดข้อผิดพลาด: ' + e.message + '\n\n💡 กรุณากดปุ่ม "🖥️ ยิงผ่านเซิร์ฟเวอร์ PHP" ด้านข้างแทนได้ทันทีครับ');
    }
}

function syncEmailJsTestForm() {
    // Always detect selected radio directly
    var checkedRadio = document.querySelector('input[name="ui_template_choice"]:checked');
    var activeType = checkedRadio ? checkedRadio.value : currentSelectedTemplate;
    if (activeType !== 'subscribe') activeType = 'welcome';
    currentSelectedTemplate = activeType;

    var sid = document.getElementById('emailjs_service_id');
    var tidWel = document.getElementById('emailjs_template_welcome');
    var tidSub = document.getElementById('emailjs_template_subscribe');
    var pkey = document.getElementById('emailjs_public_key');
    var priv = document.getElementById('emailjs_private_key');
    var fname = document.getElementById('emailjs_from_name');
    var femail = document.getElementById('emailjs_from_email');

    var activeTid = (activeType === 'subscribe') ? (tidSub?.value || 'template_y8wnrlt') : (tidWel?.value || 'template_xt7cq3g');

    if (sid) document.getElementById('t_ejs_service').value = sid.value;
    document.getElementById('t_ejs_template').value = activeTid;
    if (document.getElementById('test_template_type')) document.getElementById('test_template_type').value = activeType;
    if (document.getElementById('t_ejs_template_welcome')) document.getElementById('t_ejs_template_welcome').value = tidWel?.value || '';
    if (document.getElementById('t_ejs_template_subscribe')) document.getElementById('t_ejs_template_subscribe').value = tidSub?.value || '';
    if (pkey) document.getElementById('t_ejs_public').value = pkey.value;
    if (priv) document.getElementById('t_ejs_private').value = priv.value;
    if (fname) document.getElementById('t_ejs_name').value = fname.value;
    if (femail) document.getElementById('t_ejs_email').value = femail.value;
}

function syncTestFormCredentials() {
    document.getElementById('t_host').value = document.getElementById('smtp_host')?.value || '';
    document.getElementById('t_port').value = document.getElementById('smtp_port')?.value || '';
    document.getElementById('t_enc').value = document.getElementById('smtp_encryption')?.value || '';
    document.getElementById('t_user').value = document.getElementById('smtp_username')?.value || '';
    document.getElementById('t_pass').value = document.getElementById('smtp_password')?.value || '';
    document.getElementById('t_from_email').value = document.getElementById('smtp_from_email')?.value || '';
    document.getElementById('t_from_name').value = document.getElementById('smtp_from_name')?.value || '';
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
