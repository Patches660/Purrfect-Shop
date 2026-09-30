<?php
require_once __DIR__ . '/data.php';

// Check if user is logged in (with fallback for CLI / static HTML export)
$currUser = getCurrentUser();
if (!$currUser) {
    if (php_sapi_name() === 'cli' || !isset($_SERVER['HTTP_HOST'])) {
        $currUser = [
            'id' => 'u_6a97f8b172436',
            'fullname' => 'คุณรักแมว เหมียวเหมียว',
            'username' => 'catlover',
            'email' => 'member@purrfectshop.com',
            'phone' => '081-234-5678',
            'role' => 'customer',
            'paw_points' => 350,
            'avatar' => 'assets/images/logo.png',
            'bank_name' => 'กสิกรไทย (KBANK)',
            'bank_account' => '123-4-56789-0',
            'bank_account_name' => 'คุณรักแมว เหมียวเหมียว',
            'delivery_address' => '99/9 หมู่บ้านแมวน่ารัก ซอย 5 แขวงบางแคเหนือ เขตบางแค กรุงเทพมหานคร 10160'
        ];
    } else {
        header("Location: login.php?redirect=profile.php");
        exit;
    }
}

$success_msg = "";
$error_msg = "";
$active_tab = $_GET['tab'] ?? 'profile';

// Handle Profile Updates (POST)
if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_profile') {
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $new_password = $_POST['new_password'] ?? '';
        $selected_avatar = $_POST['selected_avatar'] ?? ($currUser['avatar'] ?? 'assets/images/logo.png');

        // Handle Avatar File Upload if provided
        if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES['avatar_file']['tmp_name'];
            $fileName = $_FILES['avatar_file']['name'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($fileExt, $allowedExts)) {
                $newFileName = 'avatar_' . $currUser['id'] . '_' . time() . '.' . $fileExt;
                $destPath = __DIR__ . '/assets/images/avatars/' . $newFileName;
                if (move_uploaded_file($fileTmp, $destPath)) {
                    $selected_avatar = 'assets/images/avatars/' . $newFileName;
                }
            } else {
                $error_msg = "รองรับเฉพาะไฟล์รูปภาพ .jpg, .png, .webp, .gif เท่านั้น";
            }
        }

        if (empty($fullname) || empty($email) || empty($phone)) {
            $error_msg = "กรุณากรอกชื่อ-นามสกุล, อีเมล และเบอร์โทรศัพท์ให้ครบถ้วน";
        } else {
            $updateData = [
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $phone,
                'avatar' => $selected_avatar,
                'new_password' => $new_password
            ];

            $res = updateUserProfile($currUser['id'], $updateData);
            if ($res['success']) {
                $success_msg = "✓ อัปเดตข้อมูลส่วนตัวและรูปโปรไฟล์เรียบร้อยแล้ว!";
                $currUser = getCurrentUser(); // refresh
                $active_tab = 'profile';
            } else {
                $error_msg = $res['message'];
            }
        }
    }

    if ($action === 'update_payment') {
        $bank_name = trim($_POST['bank_name'] ?? '');
        $bank_account = trim($_POST['bank_account'] ?? '');
        $bank_account_name = trim($_POST['bank_account_name'] ?? '');
        $delivery_address = trim($_POST['delivery_address'] ?? '');

        $updateData = [
            'bank_name' => $bank_name,
            'bank_account' => $bank_account,
            'bank_account_name' => $bank_account_name,
            'delivery_address' => $delivery_address
        ];

        $res = updateUserProfile($currUser['id'], $updateData);
        if ($res['success']) {
            $success_msg = "✓ บันทึกข้อมูลบัญชีธนาคารและที่อยู่จัดส่งเรียบร้อยแล้ว! (ข้อมูลจะถูกนำไปกรอกในหน้าตะกร้าให้อัตโนมัติ)";
            $currUser = getCurrentUser(); // refresh
            $active_tab = 'payment';
        } else {
            $error_msg = $res['message'];
        }
    }
}

// Fetch user's orders
$user_orders = getUserOrders($currUser['email']);
if (empty($user_orders)) {
    $user_orders = getUserOrders($currUser['username']);
}
if (empty($user_orders)) {
    $user_orders = getUserOrders($currUser['id']);
}

// Compute metrics
$total_cats_adopted = 0;
// Fetch user's messages and newsletters
$user_messages = getCustomerMessages($currUser['email'], $currUser['id']);

$total_spent = 0;
$adoptedCats = [];
foreach ($user_orders as $ord) {
    $total_cats_adopted += $ord['cat_count'] ?? 0;
    $total_spent += $ord['total'] ?? 0;
    foreach ($ord['items'] ?? [] as $it) {
        $adoptedCats[] = [
            'id' => $it['id'] ?? '',
            'name' => $it['name'] ?? 'น้องแมว',
            'breed' => $it['breed'] ?? 'สายพันธุ์แท้',
            'image' => $it['image'] ?? 'cat_british.jpg',
            'age' => $it['age'] ?? '2.5 เดือน',
            'gender' => $it['gender'] ?? 'ไม่ระบุ',
            'order_id' => $ord['order_id'] ?? '',
            'created_at' => $ord['created_at'] ?? ''
        ];
    }
}

require_once __DIR__ . '/header.php';
?>

<!-- Section Header -->
<div class="section-header" style="margin-bottom: 2rem;">
    <span class="section-tag">⚙️ MEMBER DASHBOARD</span>
    <h1 class="section-title">จัดการโปรไฟล์ & ประวัติการรับเลี้ยง</h1>
    <p class="section-subtitle">ตั้งค่าข้อมูลส่วนตัว รูปโปรไฟล์ ข้อมูลการชำระเงิน และตรวจสอบรายการรับเลี้ยงน้องแมวของคุณ</p>
</div>

<?php if (!empty($success_msg)): ?>
    <div class="alert-box alert-success" style="max-width: 1000px; margin: 0 auto 1.5rem auto;">
        <?php echo htmlspecialchars($success_msg); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div class="alert-box alert-danger" style="max-width: 1000px; margin: 0 auto 1.5rem auto;">
        ⚠️ <?php echo htmlspecialchars($error_msg); ?>
    </div>
<?php endif; ?>

<div style="max-width: 1050px; margin: 0 auto 4rem auto;">
    <!-- Profile Overview Card -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-md); margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="position: relative;">
                <img src="<?php echo htmlspecialchars($currUser['avatar'] ?? 'assets/images/logo.png'); ?>" 
                     alt="Profile Avatar" 
                     id="header-avatar-preview"
                     style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-coral); box-shadow: var(--shadow-coral);">
                <span style="position: absolute; bottom: 2px; right: 2px; background: var(--accent-mint); width: 18px; height: 18px; border-radius: 50%; border: 3px solid #FFFFFF;" title="ออนไลน์"></span>
            </div>
            <div>
                <h2 id="profile-fullname-text" style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.2rem;">
                    <?php echo htmlspecialchars($currUser['fullname']); ?>
                </h2>
                <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                    @<span id="profile-username-text"><?php echo htmlspecialchars($currUser['username']); ?></span> &bull; <span id="profile-email-text"><?php echo htmlspecialchars($currUser['email']); ?></span>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <span style="background: var(--primary-coral-soft); color: var(--primary-coral); font-size: 0.78rem; font-weight: 700; padding: 0.25rem 0.75rem; border-radius: 9999px; border: 1px solid rgba(255, 117, 86, 0.3);">
                        🌟 สมาชิกทางการ (ส่วนลดพิเศษ 5%)
                    </span>
                    <span style="background: var(--accent-amber-soft); color: #B45309; font-size: 0.78rem; font-weight: 600; padding: 0.25rem 0.75rem; border-radius: 9999px;">
                        📞 <span id="profile-phone-text"><?php echo htmlspecialchars($currUser['phone']); ?></span>
                    </span>
                    <span style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #92400E; border: 1px solid #F59E0B; font-size: 0.78rem; font-weight: 700; padding: 0.25rem 0.75rem; border-radius: 9999px;">
                        🐾 <span id="profile-points-text"><?php echo number_format($currUser['paw_points'] ?? 150); ?></span> Paw Points
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Summary Counters -->
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.8rem 1.2rem; text-align: center; min-width: 110px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">รับเลี้ยงน้องแมว</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary-coral); font-family: 'Outfit';" id="profile-cats-count">
                    <?php echo $total_cats_adopted; ?> ตัว
                </div>
            </div>
            <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.8rem 1.2rem; text-align: center; min-width: 120px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">ยอดที่ชำระแล้ว</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--accent-mint); font-family: 'Outfit';" id="profile-spent-total">
                    <?php echo number_format($total_spent); ?> ฿
                </div>
            </div>
            <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.8rem 1.2rem; text-align: center; min-width: 100px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">คำสั่งจอง</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); font-family: 'Outfit';" id="profile-orders-count">
                    <?php echo count($user_orders); ?> ครั้ง
                </div>
            </div>

            <!-- Logout Button -->
            <a href="logout.php" 
               class="btn btn-secondary" 
               onclick="if(typeof clientLogout === 'function' && (window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php'))){ event.preventDefault(); clientLogout(); } else { return confirm('คุณต้องการออกจากระบบ Cat Shop ใช่หรือไม่?'); }" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.3rem; border-color: rgba(239, 68, 68, 0.4); color: #EF4444; font-weight: 700; background: rgba(239, 68, 68, 0.06); border-radius: var(--radius-md); text-decoration: none;" 
               title="กดเพื่อออกจากระบบ">
                ออกจากระบบ 🚪
            </a>
        </div>
    </div>

    <!-- Navigation Tabs for Profile (Modern Card Blocks Grid) -->
    <div class="profile-tabs-nav">
        <button type="button" 
                class="profile-nav-btn <?php echo $active_tab === 'profile' ? 'active' : ''; ?>" 
                id="btn-tab-profile"
                onclick="switchProfileTab('profile', this)">
            <div class="nav-btn-icon-wrap" style="background: rgba(255, 107, 74, 0.12); color: #FF533D;">
                👤
            </div>
            <div class="nav-btn-content">
                <div class="nav-btn-top">
                    <span class="nav-label">ข้อมูลส่วนตัว & รูปโปรไฟล์</span>
                </div>
                <span class="nav-desc">จัดการข้อมูลผู้ใช้ อีเมล และรูปโปรไฟล์</span>
            </div>
        </button>

        <button type="button" 
                class="profile-nav-btn <?php echo $active_tab === 'vaccine' ? 'active' : ''; ?>" 
                id="btn-tab-vaccine"
                onclick="switchProfileTab('vaccine', this)">
            <div class="nav-btn-icon-wrap" style="background: #ECFDF5; color: #059669;">
                💉
            </div>
            <div class="nav-btn-content">
                <div class="nav-btn-top">
                    <span class="nav-label">สมุดวัคซีน & สุขภาพ</span>
                    <span class="nav-badge nav-badge-mint" id="vaccine-counter-badge"><?php echo count($adoptedCats); ?></span>
                </div>
                <span class="nav-desc">ประวัติการฉีดวัคซีน & ตารางนัดหมาย</span>
            </div>
        </button>

        <button type="button" 
                class="profile-nav-btn <?php echo $active_tab === 'coupons' ? 'active' : ''; ?>" 
                id="btn-tab-coupons"
                onclick="switchProfileTab('coupons', this)">
            <div class="nav-btn-icon-wrap" style="background: #FDF2F8; color: #DB2777;">
                🎁
            </div>
            <div class="nav-btn-content">
                <div class="nav-btn-top">
                    <span class="nav-label">คูปอง & รางวัลจากวงล้อ</span>
                    <span class="nav-badge nav-badge-coral" id="rewards-counter-badge">0</span>
                </div>
                <span class="nav-desc">โค้ดส่วนลด & ของรางวัลที่ได้รับ</span>
            </div>
        </button>

        <button type="button" 
                class="profile-nav-btn <?php echo $active_tab === 'payment' ? 'active' : ''; ?>" 
                id="btn-tab-payment"
                onclick="switchProfileTab('payment', this)">
            <div class="nav-btn-icon-wrap" style="background: #EFF6FF; color: #2563EB;">
                💳
            </div>
            <div class="nav-btn-content">
                <div class="nav-btn-top">
                    <span class="nav-label">ข้อมูลการชำระเงิน & ที่อยู่</span>
                </div>
                <span class="nav-desc">บัญชีธนาคาร ที่อยู่ และใบกำกับภาษี</span>
            </div>
        </button>

        <button type="button" 
                class="profile-nav-btn <?php echo $active_tab === 'orders' ? 'active' : ''; ?>" 
                id="btn-tab-orders"
                onclick="switchProfileTab('orders', this)">
            <div class="nav-btn-icon-wrap" style="background: #FFFBEB; color: #D97706;">
                📦
            </div>
            <div class="nav-btn-content">
                <div class="nav-btn-top">
                    <span class="nav-label">ประวัติการสั่งซื้อ & น้องแมว</span>
                    <span class="nav-badge nav-badge-amber" id="orders-tab-count"><?php echo count($user_orders); ?></span>
                </div>
                <span class="nav-desc">คำสั่งจอง ใบเสร็จ และน้องแมวที่รับเลี้ยง</span>
            </div>
        </button>

        <button type="button" 
                class="profile-nav-btn <?php echo $active_tab === 'points' ? 'active' : ''; ?>" 
                id="btn-tab-points"
                onclick="switchProfileTab('points', this)">
            <div class="nav-btn-icon-wrap" style="background: #F5F3FF; color: #7C3AED;">
                🐾
            </div>
            <div class="nav-btn-content">
                <div class="nav-btn-top">
                    <span class="nav-label">สะสมแต้ม Paw Points & Tiers</span>
                </div>
                <span class="nav-desc">คะแนนสะสม & สิทธิพิเศษระดับ VIP</span>
            </div>
        </button>

        <button type="button" 
                class="profile-nav-btn <?php echo $active_tab === 'inbox' ? 'active' : ''; ?>" 
                id="btn-tab-inbox"
                onclick="switchProfileTab('inbox', this)">
            <div class="nav-btn-icon-wrap" style="background: #F0F9FF; color: #0284C7;">
                📬
            </div>
            <div class="nav-btn-content">
                <div class="nav-btn-top">
                    <span class="nav-label">กล่องจดหมาย & ข่าวสารร้าน</span>
                    <span class="nav-badge nav-badge-blue" id="inbox-tab-count"><?php echo count($user_messages); ?></span>
                </div>
                <span class="nav-desc">ข้อความแจ้งเตือน & ประกาศจากร้าน</span>
            </div>
        </button>

        <a href="tracking.php" class="profile-nav-btn profile-nav-link-track" title="เปิดหน้าติดตามสถานะการจัดส่งแบบสด">
            <div class="nav-btn-icon-wrap" style="background: #FFF7ED; color: #EA580C;">
                📍
            </div>
            <div class="nav-btn-content">
                <div class="nav-btn-top">
                    <span class="nav-label">ติดตามการจัดส่งสด</span>
                    <span class="nav-pulse-dot" title="Live GPS"></span>
                </div>
                <span class="nav-desc">ตรวจสถานะ GPS พิกัดน้องแมวสดๆ</span>
            </div>
        </a>

        <a href="admin.php" class="profile-nav-btn profile-nav-link-admin" title="เปิดหน้าจัดการระบบหลังบ้าน (Admin Control Panel)">
            <div class="nav-btn-icon-wrap" style="background: rgba(245, 158, 11, 0.2); color: #FCD34D;">
                👑
            </div>
            <div class="nav-btn-content">
                <div class="nav-btn-top">
                    <span class="nav-label">ผู้ดูแลระบบ (Admin) &rarr;</span>
                    <span class="nav-badge" style="background: #F59E0B; color: #1E1B4B; font-weight: 900; font-size: 0.68rem;">MASTER</span>
                </div>
                <span class="nav-desc">เข้าสู่แดชบอร์ดจัดการระบบหลังบ้าน</span>
            </div>
        </a>
    </div>

    <!-- =========================================================
         TAB 1: ข้อมูลส่วนตัว & รูปโปรไฟล์
         ========================================================= -->
    <div id="tab-profile" class="profile-tab-panel" style="<?php echo $active_tab === 'profile' ? 'display: block;' : 'display: none;'; ?>">
        <div class="checkout-block">
            <h3 class="checkout-block-title" style="margin-bottom: 1.4rem;">
                👤 ตั้งค่าข้อมูลส่วนตัว & รูปโปรไฟล์
            </h3>

            <form method="POST" action="profile.php?tab=profile" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_profile">

                <!-- Avatar Selection & Upload Section -->
                <div style="background: var(--bg-card-subtle); border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 1.4rem; margin-bottom: 1.8rem;">
                    <label style="display: block; font-weight: 700; color: var(--text-main); margin-bottom: 0.6rem;">
                        🖼️ เลือกรูปโปรไฟล์ของคุณ:
                    </label>

                    <!-- Avatar Presets -->
                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.2rem;">
                        <?php 
                        $presets = [
                            ['src' => 'assets/images/logo.png', 'label' => 'โลโก้ร้าน'],
                            ['src' => 'assets/images/cat_british.jpg', 'label' => 'บริติช'],
                            ['src' => 'assets/images/cat_ragdoll.jpg', 'label' => 'แร็กดอลล์'],
                            ['src' => 'assets/images/cat_persian.jpg', 'label' => 'เปอร์เซีย'],
                            ['src' => 'assets/images/cat_khao_manee.jpg', 'label' => 'ขาวมณี'],
                            ['src' => 'assets/images/cat_mainecoon.jpg', 'label' => 'เมนคูน']
                        ];
                        $currAvatar = $currUser['avatar'] ?? 'assets/images/logo.png';
                        ?>
                        <?php foreach ($presets as $p): ?>
                            <label style="cursor: pointer; text-align: center;">
                                <input type="radio" name="selected_avatar" value="<?php echo $p['src']; ?>" 
                                       <?php echo $currAvatar === $p['src'] ? 'checked' : ''; ?>
                                       onchange="document.getElementById('header-avatar-preview').src = this.value;"
                                       style="display: none;">
                                <img src="<?php echo $p['src']; ?>" alt="<?php echo $p['label']; ?>" 
                                     style="width: 55px; height: 55px; border-radius: 50%; object-fit: cover; border: 3px solid <?php echo $currAvatar === $p['src'] ? 'var(--primary-coral)' : 'var(--border-color)'; ?>; transition: var(--transition); padding: 2px;"
                                     class="preset-avatar-img">
                                <span style="display: block; font-size: 0.72rem; color: var(--text-muted); margin-top: 0.2rem;"><?php echo $p['label']; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Custom Image Upload -->
                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                        <span style="font-size: 0.85rem; color: var(--text-muted);">หรืออัปโหลดรูปของคุณเอง:</span>
                        <input type="file" name="avatar_file" id="avatar_file" accept="image/*" class="form-control" style="width: auto; padding: 0.4rem 0.8rem; font-size: 0.82rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; margin-bottom: 1.2rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="fullname">ชื่อ - นามสกุล *</label>
                        <input type="text" name="fullname" id="fullname" class="form-control" 
                               value="<?php echo htmlspecialchars($currUser['fullname']); ?>" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="username">ชื่อผู้ใช้งาน (Username)</label>
                        <input type="text" id="username" class="form-control" 
                               value="<?php echo htmlspecialchars($currUser['username']); ?>" readonly 
                               style="background: var(--bg-page); cursor: not-allowed;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; margin-bottom: 1.2rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="email">อีเมลสำหรับติดต่อ & รับใบเสร็จ *</label>
                        <input type="email" name="email" id="email" class="form-control" 
                               value="<?php echo htmlspecialchars($currUser['email']); ?>" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="phone">เบอร์โทรศัพท์สำหรับจัดส่งน้องแมว *</label>
                        <input type="tel" name="phone" id="phone" class="form-control" 
                               value="<?php echo htmlspecialchars($currUser['phone']); ?>" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.8rem;">
                    <label for="new_password">เปลี่ยนรหัสผ่านใหม่ (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" 
                           placeholder="ระบุรหัสผ่านใหม่ 6 ตัวอักษรขึ้นไป (ถ้าต้องการเปลี่ยน)">
                </div>

                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                        💾 บันทึกการเปลี่ยนแปลงข้อมูลโปรไฟล์
                    </button>
                    <a href="logout.php" 
                       class="btn btn-secondary" 
                       onclick="return confirm('คุณต้องการออกจากระบบ Cat Shop ใช่หรือไม่?');" 
                       style="color: #EF4444; border-color: rgba(239, 68, 68, 0.4); padding: 0.75rem 1.4rem;">
                        ออกจากระบบ 🚪
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================================
         TAB 2: ข้อมูลการชำระเงิน & ที่อยู่จัดส่ง
         ========================================================= -->
    <div id="tab-payment" class="profile-tab-panel" style="<?php echo $active_tab === 'payment' ? 'display: block;' : 'display: none;'; ?>">
        <div class="checkout-block">
            <h3 class="checkout-block-title" style="margin-bottom: 0.4rem;">
                💳 ข้อมูลที่ใช้ในการชำระเงิน & ที่อยู่จัดส่งประจำ
            </h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 1.6rem;">
                บันทึกบัญชีธนาคารและที่อยู่ประจำของคุณ เพื่อให้นำไปกรอกในหน้าชำระเงินตะกร้าสินค้าอัตโนมัติ ไม่ต้องกรอกซ้ำทุกครั้ง 🐾
            </p>

            <form method="POST" action="profile.php?tab=payment">
                <input type="hidden" name="action" value="update_payment">

                <!-- Bank Details -->
                <div style="background: var(--bg-card-subtle); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.4rem; margin-bottom: 1.5rem;">
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--primary-coral); margin-bottom: 1rem;">
                        🏦 ข้อมูลบัญชีธนาคารของคุณสำหรับโอนเงิน
                    </h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; margin-bottom: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="bank_name">ธนาคารที่ใช้ประจำ</label>
                            <?php $userBank = $currUser['bank_name'] ?? 'ธนาคารกสิกรไทย (KBANK)'; ?>
                            <select name="bank_name" id="bank_name" class="form-control">
                                <option value="ธนาคารกสิกรไทย (KBANK)" <?php echo $userBank === 'ธนาคารกสิกรไทย (KBANK)' ? 'selected' : ''; ?>>ธนาคารกสิกรไทย (KBANK)</option>
                                <option value="ธนาคารไทยพาณิชย์ (SCB)" <?php echo $userBank === 'ธนาคารไทยพาณิชย์ (SCB)' ? 'selected' : ''; ?>>ธนาคารไทยพาณิชย์ (SCB)</option>
                                <option value="ธนาคารกรุงเทพ (BBL)" <?php echo $userBank === 'ธนาคารกรุงเทพ (BBL)' ? 'selected' : ''; ?>>ธนาคารกรุงเทพ (BBL)</option>
                                <option value="ธนาคารกรุงไทย (KTB)" <?php echo $userBank === 'ธนาคารกรุงไทย (KTB)' ? 'selected' : ''; ?>>ธนาคารกรุงไทย (KTB)</option>
                                <option value="ธนาคารทหารไทยธนชาต (TTB)" <?php echo $userBank === 'ธนาคารทหารไทยธนชาต (TTB)' ? 'selected' : ''; ?>>ธนาคารทหารไทยธนชาต (TTB)</option>
                                <option value="พร้อมเพย์ / PromptPay" <?php echo $userBank === 'พร้อมเพย์ / PromptPay' ? 'selected' : ''; ?>>พร้อมเพย์ / PromptPay</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="bank_account">เลขที่บัญชีของคุณ</label>
                            <input type="text" name="bank_account" id="bank_account" class="form-control" 
                                   placeholder="เช่น 123-4-56789-0 หรือ 0812345678" 
                                   value="<?php echo htmlspecialchars($currUser['bank_account'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="bank_account_name">ชื่อบัญชี (Account Name)</label>
                        <input type="text" name="bank_account_name" id="bank_account_name" class="form-control" 
                               placeholder="เช่น นายกิตติพงษ์ รักแมว" 
                               value="<?php echo htmlspecialchars($currUser['bank_account_name'] ?? $currUser['fullname']); ?>">
                    </div>
                </div>

                <!-- Default Delivery Address -->
                <div class="form-group" style="margin-bottom: 1.8rem;">
                    <label for="delivery_address">🏠 ที่อยู่สำหรับจัดส่งน้องแมวประจำ</label>
                    <textarea name="delivery_address" id="delivery_address" class="form-control" rows="3" 
                              placeholder="เช่น 123/45 หมู่บ้านสุขใจ ซอยอารีย์ ถนนพหลโยธิน แขวงสามเสนใน เขตพญาไท กรุงเทพฯ 10400"><?php echo htmlspecialchars($currUser['delivery_address'] ?? ''); ?></textarea>
                    <small style="color: var(--text-muted); display: block; margin-top: 0.3rem;">
                        💡 ที่อยู่นี้จะถูกกรอกลงในหน้าตะกร้าสินค้าอัตโนมัติ สามารถเปลี่ยนแปลงในแต่ละออเดอร์ได้
                    </small>
                </div>

                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                    💾 บันทึกข้อมูลการชำระเงิน & ที่อยู่จัดส่ง
                </button>
            </form>
        </div>
    </div>

    <!-- =========================================================
         TAB 3: รายการที่ทำการสั่งซื้อ & แมวที่ชำระแล้ว
         ========================================================= -->
    <div id="tab-orders" class="profile-tab-panel" style="<?php echo $active_tab === 'orders' ? 'display: block;' : 'display: none;'; ?>">
        <div class="checkout-block">
            <div class="checkout-block-header">
                <h3 class="checkout-block-title">
                    <span>📦 รายการคำสั่งซื้อ & น้องแมวที่ชำระเงินแล้ว</span>
                    <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">(พบ <?php echo count($user_orders); ?> รายการ)</span>
                </h3>
                <a href="cart.php" class="btn btn-secondary btn-sm">
                    ดูตะกร้าปัจจุบัน 🛒
                </a>
            </div>

            <?php if (empty($user_orders)): ?>
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--text-muted);">
                    <span style="font-size: 3.5rem; display: block; margin-bottom: 0.5rem;">🐾</span>
                    <h4 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">ยังไม่มีรายการสั่งซื้อ</h4>
                    <p style="font-size: 0.9rem; margin-bottom: 1.5rem;">คุณยังไม่เคยทำรายการรับเลี้ยงน้องแมวกับเรา</p>
                    <a href="products.php" class="btn btn-primary">เลือกชมน้องแมวสายพันธุ์แท้ 🐱</a>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php foreach ($user_orders as $order): ?>
                        <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm); transition: var(--transition);">
                            <!-- Order Header -->
                            <div style="background: var(--bg-card-subtle); padding: 1rem 1.4rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.8rem;">
                                <div>
                                    <span style="font-size: 0.78rem; color: var(--text-muted); display: block;">รหัสคำสั่งจอง:</span>
                                    <strong style="font-family: 'Outfit'; font-size: 1.1rem; color: var(--primary-coral);">
                                        <?php echo htmlspecialchars($order['order_id']); ?>
                                    </strong>
                                    <div style="margin-top: 4px;">
                                        <span style="font-size: 0.78rem; font-weight: 800; background: #EFF6FF; color: #1D4ED8; padding: 2px 8px; border-radius: 6px; border: 1px solid #BFDBFE; display: inline-flex; align-items: center; gap: 4px; font-family: 'Outfit', sans-serif;">
                                            🚚 รหัส Tracking: <?php echo htmlspecialchars($order['tracking_id'] ?? ('TRK-' . strtoupper(substr(md5($order['order_id']), 0, 8)))); ?>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span style="font-size: 0.78rem; color: var(--text-muted); display: block;">วันที่ทำรายการ:</span>
                                    <span style="font-size: 0.88rem; font-weight: 600; color: var(--text-main);">
                                        <?php echo htmlspecialchars($order['created_at']); ?> น.
                                    </span>
                                </div>
                                <div>
                                    <span style="font-size: 0.78rem; color: var(--text-muted); display: block;">สถานะ:</span>
                                    <span style="background: var(--accent-mint-soft); color: #065F46; padding: 0.2rem 0.7rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.3);">
                                        ✓ <?php echo htmlspecialchars($order['status'] ?? 'ชำระเงินแล้ว'); ?>
                                    </span>
                                </div>
                                <div>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <a href="tracking.php?track=<?php echo urlencode($order['tracking_id'] ?? $order['order_id']); ?>" 
                                           class="btn btn-primary btn-sm" 
                                           style="padding: 0.38rem 0.85rem; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;" 
                                           title="ติดตามสถานะการส่งมอบสัตว์เลี้ยงแบบเรียลไทม์">
                                            📍 ติดตามส่งมอบ
                                        </a>
                                        <a href="order_letter.php?id=<?php echo urlencode($order['order_id']); ?>" target="_blank" 
                                           class="btn btn-secondary btn-sm" 
                                           style="padding: 0.38rem 0.85rem; font-size: 0.8rem; font-weight: 700; color: #92400E; background: #FEF3C7; border-color: #FCD34D; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;" 
                                           title="เปิดดูจดหมายตอบรับและใบรับประกันอย่างเป็นทางการ">
                                            📜 จดหมายตอบรับ
                                        </a>
                                        <a href="vaccine_reminder.php" 
                                           class="btn btn-secondary btn-sm" 
                                           style="padding: 0.38rem 0.85rem; font-size: 0.8rem; font-weight: 700; color: #065F46; background: #ECFDF5; border-color: #A7F3D0; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;" 
                                           title="เปิดดูสมุดสุขภาพและตารางนัดหมายวัคซีน">
                                            💉 สมุดวัคซีน
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Body: List of Cats -->
                            <div style="padding: 1.2rem 1.4rem;">
                                <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.8rem;">
                                    น้องแมวในรายการนี้ (จำนวน: <?php echo $order['cat_count']; ?> ตัว)
                                </div>

                                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.6rem 0; border-bottom: 1px dashed var(--border-color); flex-wrap: wrap;">
                                            <div style="display: flex; align-items: center; gap: 1rem;">
                                                <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" 
                                                     style="width: 58px; height: 58px; border-radius: 10px; object-fit: cover; border: 1.5px solid var(--border-color);">
                                                <div>
                                                    <strong style="color: var(--text-main); font-size: 0.95rem; display: block;"><?php echo $item['name']; ?></strong>
                                                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">
                                                        <?php echo $item['breed']; ?> &bull; เพศ: <?php echo $item['gender']; ?> (x<?php echo $item['qty']; ?>)
                                                    </div>
                                                    <a href="vaccine_reminder.php?cat_name=<?php echo urlencode($item['name']); ?>&breed=<?php echo urlencode($item['breed']); ?>&image=<?php echo urlencode($item['image']); ?>" 
                                                       class="btn-vaccine-link" 
                                                       style="font-size: 0.78rem; font-weight: 800; color: #059669; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 2px 8px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                                        💉 ดูตารางวัคซีนน้องตัวนี้ ➔
                                                    </a>
                                                </div>
                                            </div>
                                            <span style="font-weight: 700; color: var(--primary-coral); font-family: 'Outfit'; font-size: 1.05rem;">
                                                <?php echo number_format($item['price'] * $item['qty']); ?> ฿
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Order Footer: Payment & Total Amount -->
                                <div style="margin-top: 1.2rem; padding-top: 1rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
                                    <div>
                                        <span style="font-size: 0.78rem; color: var(--text-muted); display: block;">ช่องทางชำระเงินที่ใช้:</span>
                                        <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary);">
                                            <?php echo $order['payment_channel']; ?>
                                        </span>
                                    </div>
                                    <div style="text-align: right;">
                                        <span style="font-size: 0.82rem; color: var(--text-muted);">ยอดรวมสุทธิ:</span>
                                        <span style="font-size: 1.3rem; font-weight: 800; color: var(--primary-coral); font-family: 'Outfit'; display: block;">
                                            <?php echo number_format($order['total']); ?> ฿
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- =========================================================
         TAB 4: สะสมแต้ม Paw Points & ระดับสมาชิก (Loyalty & Tiers)
         ========================================================= -->
    <?php
    $user_points = intval($currUser['paw_points'] ?? 150);

    // Tier calculation
    if ($user_points >= 5000) {
        $current_tier_key = 'diamond';
        $current_tier_name = 'Diamond Paw VIP';
        $current_tier_icon = '👑';
        $current_tier_color = '#7C3AED';
        $current_tier_badge_bg = 'linear-gradient(135deg, #7C3AED 0%, #A855F7 100%)';
        $current_discount = '15%';
        $next_tier_name = 'Max Level (ระดับสูงสุด)';
        $next_tier_target = 5000;
        $points_needed = 0;
        $tier_progress_percent = 100;
    } elseif ($user_points >= 1500) {
        $current_tier_key = 'gold';
        $current_tier_name = 'Gold Paw VIP';
        $current_tier_icon = '🥇';
        $current_tier_color = '#EA580C';
        $current_tier_badge_bg = 'linear-gradient(135deg, #EA580C 0%, #F97316 100%)';
        $current_discount = '10%';
        $next_tier_name = 'Diamond Paw VIP 👑';
        $next_tier_target = 5000;
        $points_needed = 5000 - $user_points;
        $tier_progress_percent = min(100, max(5, round((($user_points - 1500) / (5000 - 1500)) * 100)));
    } elseif ($user_points >= 500) {
        $current_tier_key = 'silver';
        $current_tier_name = 'Silver Paw';
        $current_tier_icon = '🥈';
        $current_tier_color = '#475569';
        $current_tier_badge_bg = 'linear-gradient(135deg, #64748B 0%, #94A3B8 100%)';
        $current_discount = '7%';
        $next_tier_name = 'Gold Paw VIP 🥇';
        $next_tier_target = 1500;
        $points_needed = 1500 - $user_points;
        $tier_progress_percent = min(100, max(5, round((($user_points - 500) / (1500 - 500)) * 100)));
    } else {
        $current_tier_key = 'bronze';
        $current_tier_name = 'Bronze Paw';
        $current_tier_icon = '🥉';
        $current_tier_color = '#B45309';
        $current_tier_badge_bg = 'linear-gradient(135deg, #D97706 0%, #F59E0B 100%)';
        $current_discount = '5%';
        $next_tier_name = 'Silver Paw 🥈';
        $next_tier_target = 500;
        $points_needed = 500 - $user_points;
        $tier_progress_percent = min(100, max(5, round(($user_points / 500) * 100)));
    }

    // Global Scale for the big visual tube (0 to 5,000 points)
    $global_progress_percent = min(100, max(3, round(($user_points / 5000) * 100)));
    ?>

    <div id="tab-points" class="profile-tab-panel" style="<?php echo $active_tab === 'points' ? 'display: block;' : 'display: none;'; ?>">
        <div class="checkout-block">
            <div class="checkout-block-header" style="margin-bottom: 1.5rem;">
                <h3 class="checkout-block-title">
                    <span>🎁 ระบบสะสมแต้ม Paw Points & ระดับสมาชิก (Loyalty Tiers)</span>
                </h3>
                <span class="badge" style="background: rgba(255,107,74,0.12); color: var(--primary-coral); font-weight: 700;">
                    🐾 คลับคนรักแมว VIP
                </span>
            </div>

            <!-- 1. Hero Summary Card -->
            <div style="background: linear-gradient(135deg, #FF6B4A 0%, #FF8E72 50%, #FFA885 100%); color: #FFFFFF; border-radius: var(--radius-lg); padding: 2.2rem; box-shadow: 0 12px 30px rgba(255,107,74,0.3); margin-bottom: 2rem; position: relative; overflow: hidden;">
                <!-- Decorative Paw watermark -->
                <div style="position: absolute; right: -20px; bottom: -30px; font-size: 10rem; opacity: 0.12; user-select: none; pointer-events: none;">🐾</div>

                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem; position: relative; z-index: 1;">
                    <div>
                        <span style="font-size: 0.82rem; font-weight: 800; background: rgba(255,255,255,0.25); padding: 4px 14px; border-radius: 999px; letter-spacing: 0.5px; text-transform: uppercase;">
                            🐾 PURRFECT REWARDS CLUB
                        </span>
                        <h2 style="font-size: 2.6rem; font-weight: 900; margin: 0.7rem 0 0.2rem 0; font-family: 'Outfit'; letter-spacing: -0.5px;">
                            <?php echo number_format($user_points); ?> <span style="font-size: 1.3rem; font-weight: 600;">Paw Points</span>
                        </h2>
                        <p style="margin: 0; font-size: 0.95rem; opacity: 0.95; line-height: 1.5;">
                            มูลค่าเทียบเท่าส่วนลดเงินสด <strong>฿<?php echo number_format($user_points); ?> บาท</strong> (อัตรา 100 พอยท์ = 100 บาท)
                        </p>
                    </div>

                    <!-- Current Tier Badge Box -->
                    <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); padding: 1.2rem 1.6rem; border-radius: var(--radius-md); box-shadow: 0 6px 16px rgba(0,0,0,0.1); min-width: 220px; text-align: center;">
                        <span style="font-size: 0.78rem; color: #64748B; font-weight: 700; display: block; margin-bottom: 4px;">ระดับสมาชิกปัจจุบันของคุณ:</span>
                        <div style="font-size: 1.4rem; font-weight: 800; color: <?php echo $current_tier_color; ?>; display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <span><?php echo $current_tier_icon; ?></span>
                            <span><?php echo $current_tier_name; ?></span>
                        </div>
                        <div style="font-size: 0.82rem; color: var(--primary-coral); font-weight: 700; margin-top: 4px; background: rgba(255,107,74,0.1); padding: 2px 8px; border-radius: 999px; display: inline-block;">
                            สิทธิพิเศษ: ส่วนลด <?php echo $current_discount; ?> ทุกรายการ
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Interactive Progress Tube (หลอดแต้มเลื่อนระดับ) -->
            <div style="background: var(--bg-card); border: 2px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.8rem; margin-bottom: 2rem; box-shadow: var(--shadow-sm);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.8rem; margin-bottom: 1rem;">
                    <div>
                        <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0; display: flex; align-items: center; gap: 6px;">
                            <span>📊 หลอดแต้มสะสมเพื่อเลื่อนขั้นสู่ระดับถัดไป</span>
                        </h4>
                        <p style="font-size: 0.88rem; color: var(--text-secondary); margin: 0;">
                            <?php if ($user_points >= 5000): ?>
                                🎉 <strong>ยินดีด้วยครับ!</strong> คุณสะสมครบระดับสูงสุด <strong>Diamond Paw VIP</strong> ได้รับสิทธิประโยชน์สูงสุดตลอดชีพ
                            <?php else: ?>
                                คุณมี <strong><?php echo number_format($user_points); ?></strong> แต้ม • ขาดอีกเพียง <strong style="color: var(--primary-coral);"><?php echo number_format($points_needed); ?> แต้ม</strong> จะได้เลื่อนขั้นสู่ <strong style="color: #92400E;"><?php echo $next_tier_name; ?></strong>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div style="background: var(--primary-coral-soft); color: var(--primary-coral); font-weight: 800; font-size: 0.9rem; padding: 6px 14px; border-radius: 999px; border: 1px solid rgba(255,107,74,0.3);">
                        ⚡ เป้าหมายถัดไป: <?php echo number_format($next_tier_target); ?> แต้ม (สำเร็จ <?php echo $tier_progress_percent; ?>%)
                    </div>
                </div>

                <!-- The Visual Animated Progress Bar (หลอดแก้วแต้ม) -->
                <div style="position: relative; margin: 1.8rem 0 2.6rem 0;">
                    <!-- Outer Tube Track -->
                    <div style="background: #E2E8F0; height: 26px; border-radius: 999px; overflow: hidden; position: relative; box-shadow: inset 0 2px 5px rgba(0,0,0,0.12); border: 2px solid #CBD5E1;">
                        <!-- Inner Gradient Fill -->
                        <div style="width: <?php echo $global_progress_percent; ?>%; height: 100%; background: linear-gradient(90deg, #FF9E7A 0%, #FF6B4A 45%, #F59E0B 80%, #7C3AED 100%); border-radius: 999px; transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 0 12px rgba(255,107,74,0.6); position: relative;">
                            <!-- Animated Light Shimmer -->
                            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.4) 50%, rgba(255,255,255,0) 100%); animation: shimmerBar 2.5s infinite;"></div>
                        </div>
                    </div>

                    <!-- Milestone Checkpoints along the tube -->
                    <div style="display: flex; justify-content: space-between; position: absolute; top: -7px; left: 0; right: 0; pointer-events: none;">
                        <!-- Node 1: Bronze (0) -->
                        <div style="text-align: center; width: 40px; margin-left: -5px;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: <?php echo $user_points >= 0 ? '#10B981' : '#FFFFFF'; ?>; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 3px solid #FFFFFF; box-shadow: 0 3px 8px rgba(0,0,0,0.15); margin: 0 auto;">
                                🥉
                            </div>
                            <span style="font-size: 0.72rem; font-weight: 800; color: var(--text-main); display: block; margin-top: 6px;">Bronze</span>
                            <span style="font-size: 0.68rem; color: var(--text-muted);">0 แต้ม</span>
                        </div>

                        <!-- Node 2: Silver (500) -->
                        <div style="text-align: center; width: 50px;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: <?php echo $user_points >= 500 ? '#10B981' : '#FFFFFF'; ?>; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 3px solid <?php echo $user_points >= 500 ? '#10B981' : '#CBD5E1'; ?>; box-shadow: 0 3px 8px rgba(0,0,0,0.15); margin: 0 auto;">
                                <?php echo $user_points >= 500 ? '✓' : '🥈'; ?>
                            </div>
                            <span style="font-size: 0.72rem; font-weight: 800; color: var(--text-main); display: block; margin-top: 6px;">Silver</span>
                            <span style="font-size: 0.68rem; color: var(--text-muted);">500 แต้ม</span>
                        </div>

                        <!-- Node 3: Gold VIP (1,500) -->
                        <div style="text-align: center; width: 60px;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: <?php echo $user_points >= 1500 ? '#10B981' : '#FFFFFF'; ?>; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 3px solid <?php echo $user_points >= 1500 ? '#10B981' : '#CBD5E1'; ?>; box-shadow: 0 3px 8px rgba(0,0,0,0.15); margin: 0 auto;">
                                <?php echo $user_points >= 1500 ? '✓' : '🥇'; ?>
                            </div>
                            <span style="font-size: 0.72rem; font-weight: 800; color: var(--text-main); display: block; margin-top: 6px;">Gold VIP</span>
                            <span style="font-size: 0.68rem; color: var(--text-muted);">1,500 แต้ม</span>
                        </div>

                        <!-- Node 4: Diamond VIP (5,000) -->
                        <div style="text-align: center; width: 70px; margin-right: -10px;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: <?php echo $user_points >= 5000 ? '#7C3AED' : '#FFFFFF'; ?>; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 3px solid <?php echo $user_points >= 5000 ? '#7C3AED' : '#CBD5E1'; ?>; box-shadow: 0 3px 8px rgba(0,0,0,0.15); margin: 0 auto;">
                                <?php echo $user_points >= 5000 ? '👑' : '💎'; ?>
                            </div>
                            <span style="font-size: 0.72rem; font-weight: 800; color: var(--text-main); display: block; margin-top: 6px;">Diamond</span>
                            <span style="font-size: 0.68rem; color: var(--text-muted);">5,000 แต้ม</span>
                        </div>
                    </div>
                </div>

                <div style="background: var(--bg-card-subtle); border-radius: var(--radius-sm); padding: 10px 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; font-size: 0.82rem; color: var(--text-secondary);">
                    <span>💡 <strong>คำแนะนำ:</strong> ช้อปปิ้งน้องแมวหรือสินค้าทุก 100 บาท จะได้รับ 1 Paw Point ทันทีเพื่อขยับหลอดแต้ม</span>
                    <a href="products.php" class="btn btn-primary btn-sm" style="padding: 4px 12px; font-size: 0.78rem;">
                        🛒 ช้อปปิ้งเพื่อสะสมแต้ม
                    </a>
                </div>
            </div>

            <!-- 3. Tiers Breakdown: สะสมเท่าไรถึงจะเลื่อนขั้น -->
            <div style="margin-bottom: 2.5rem;">
                <h4 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main); margin-bottom: 1.2rem; display: flex; align-items: center; gap: 8px;">
                    <span>🏆 เกณฑ์คะแนนสะสม & สิทธิพิเศษของแต่ละระดับสมาชิก</span>
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.2rem;">
                    <!-- Tier 1: Bronze -->
                    <div style="background: var(--bg-card); border: 2px solid <?php echo $current_tier_key === 'bronze' ? '#B45309' : '#E2E8F0'; ?>; border-radius: var(--radius-md); padding: 1.5rem; position: relative; box-shadow: <?php echo $current_tier_key === 'bronze' ? '0 6px 16px rgba(180,83,9,0.15)' : 'none'; ?>;">
                        <?php if ($current_tier_key === 'bronze'): ?>
                            <span style="position: absolute; top: 12px; right: 12px; background: #B45309; color: #FFF; font-size: 0.68rem; font-weight: 800; padding: 3px 10px; border-radius: 999px;">✓ ระดับปัจจุบัน</span>
                        <?php endif; ?>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span style="font-size: 2.2rem; line-height: 1;">🥉</span>
                            <div>
                                <h4 style="font-size: 1.15rem; font-weight: 800; color: #B45309; margin: 0;">Bronze Paw</h4>
                                <span style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted);">สะสม 0 – 499 แต้ม</span>
                            </div>
                        </div>
                        <div style="background: #FEF3C7; color: #92400E; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 12px;">
                            ส่วนลด 5% ทุกรายการ
                        </div>
                        <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.84rem; color: var(--text-secondary); line-height: 1.7;">
                            <li>✓ สมัครสมาชิกใหม่ รับสิทธิ์ทันที</li>
                            <li>✓ รับโค้ดส่วนลดต้อนรับ 15% (ใช้ครั้งแรก)</li>
                            <li>✓ สะสมแต้ม: ทุก 100 บาท = 1 Paw Point</li>
                            <li>✓ รับข่าวสาร & ดีลลับก่อนใคร</li>
                        </ul>
                    </div>

                    <!-- Tier 2: Silver -->
                    <div style="background: <?php echo $current_tier_key === 'silver' ? '#FFFBF5' : 'var(--bg-card)'; ?>; border: 2px solid <?php echo $current_tier_key === 'silver' ? '#F59E0B' : '#E2E8F0'; ?>; border-radius: var(--radius-md); padding: 1.5rem; position: relative; box-shadow: <?php echo $current_tier_key === 'silver' ? '0 6px 16px rgba(245,158,11,0.2)' : 'none'; ?>;">
                        <?php if ($current_tier_key === 'silver'): ?>
                            <span style="position: absolute; top: 12px; right: 12px; background: #F59E0B; color: #FFF; font-size: 0.68rem; font-weight: 800; padding: 3px 10px; border-radius: 999px;">✓ ระดับปัจจุบัน</span>
                        <?php elseif ($user_points < 500): ?>
                            <span style="position: absolute; top: 12px; right: 12px; background: #64748B; color: #FFF; font-size: 0.68rem; font-weight: 800; padding: 3px 10px; border-radius: 999px;">🎯 เป้าหมายถัดไป</span>
                        <?php endif; ?>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span style="font-size: 2.2rem; line-height: 1;">🥈</span>
                            <div>
                                <h4 style="font-size: 1.15rem; font-weight: 800; color: #475569; margin: 0;">Silver Paw</h4>
                                <span style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted);">สะสม 500 – 1,499 แต้ม</span>
                            </div>
                        </div>
                        <div style="background: #F1F5F9; color: #334155; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 12px;">
                            ส่วนลด 7% ทุกรายการ
                        </div>
                        <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.84rem; color: var(--text-secondary); line-height: 1.7;">
                            <li>✓ <strong>ฟรี! ค่าจัดส่ง Pet Taxi ติดแอร์</strong> 1 ครั้ง</li>
                            <li>✓ อัตราสะสมแต้มคูณ <strong>1.2 เท่า</strong></li>
                            <li>✓ รับของขวัญวันเกิด 200 Paw Points</li>
                            <li>✓ บริการจองคิวตรวจสุขภาพล่วงหน้า</li>
                        </ul>
                    </div>

                    <!-- Tier 3: Gold VIP -->
                    <div style="background: <?php echo $current_tier_key === 'gold' ? '#FFF7ED' : 'var(--bg-card)'; ?>; border: 2px solid <?php echo $current_tier_key === 'gold' ? '#EA580C' : '#E2E8F0'; ?>; border-radius: var(--radius-md); padding: 1.5rem; position: relative; box-shadow: <?php echo $current_tier_key === 'gold' ? '0 6px 16px rgba(234,88,12,0.2)' : 'none'; ?>;">
                        <?php if ($current_tier_key === 'gold'): ?>
                            <span style="position: absolute; top: 12px; right: 12px; background: #EA580C; color: #FFF; font-size: 0.68rem; font-weight: 800; padding: 3px 10px; border-radius: 999px;">✓ ระดับปัจจุบัน</span>
                        <?php endif; ?>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span style="font-size: 2.2rem; line-height: 1;">🥇</span>
                            <div>
                                <h4 style="font-size: 1.15rem; font-weight: 800; color: #C2410C; margin: 0;">Gold Paw VIP</h4>
                                <span style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted);">สะสม 1,500 – 4,999 แต้ม</span>
                            </div>
                        </div>
                        <div style="background: #FFEDD5; color: #9A3412; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 12px;">
                            ส่วนลด 10% ทุกรายการ
                        </div>
                        <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.84rem; color: var(--text-secondary); line-height: 1.7;">
                            <li>✓ <strong>ฟรี! บริการตรวจสุขภาพประจำปี 1 ปี</strong></li>
                            <li>✓ สายด่วนสัตวแพทย์ส่วนตัวให้คำปรึกษา 24 ชม.</li>
                            <li>✓ อัตราสะสมแต้มคูณ <strong>1.5 เท่า</strong></li>
                            <li>✓ สิทธิ์เลือกจับจองลูกแมวครอกใหม่ก่อนใคร</li>
                        </ul>
                    </div>

                    <!-- Tier 4: Diamond VIP -->
                    <div style="background: <?php echo $current_tier_key === 'diamond' ? '#FAF5FF' : 'var(--bg-card)'; ?>; border: 2px solid <?php echo $current_tier_key === 'diamond' ? '#7C3AED' : '#E2E8F0'; ?>; border-radius: var(--radius-md); padding: 1.5rem; position: relative; box-shadow: <?php echo $current_tier_key === 'diamond' ? '0 6px 16px rgba(124,58,237,0.2)' : 'none'; ?>;">
                        <?php if ($current_tier_key === 'diamond'): ?>
                            <span style="position: absolute; top: 12px; right: 12px; background: #7C3AED; color: #FFF; font-size: 0.68rem; font-weight: 800; padding: 3px 10px; border-radius: 999px;">✓ ระดับปัจจุบัน</span>
                        <?php endif; ?>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span style="font-size: 2.2rem; line-height: 1;">👑</span>
                            <div>
                                <h4 style="font-size: 1.15rem; font-weight: 800; color: #7C3AED; margin: 0;">Diamond VIP</h4>
                                <span style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted);">สะสมครบ 5,000+ แต้ม</span>
                            </div>
                        </div>
                        <div style="background: #F3E8FF; color: #6B21A8; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 12px;">
                            ส่วนลด 15% VIP ตลอดชีพ
                        </div>
                        <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.84rem; color: var(--text-secondary); line-height: 1.7;">
                            <li>✓ <strong>ฟรี! จัดส่ง Pet Taxi & เครื่องบิน ตลอดชีพ</strong></li>
                            <li>✓ สัตวแพทย์ On-Call เยี่ยมตรวจสุขภาพถึงบ้าน</li>
                            <li>✓ อัตราสะสมแต้มคูณ <strong>2.0 เท่า (Super Points)</strong></li>
                            <li>✓ ของขวัญพรีเมียม VIP Welcome Set ทุกปี</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 4. ทำอะไรบ้างถึงจะได้แต้ม (Ways to Earn Points Guide) -->
            <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 2rem;">
                <h4 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                    <span>🎯 รวมวิธีสะสมแต้ม Paw Points (ทำอะไรบ้างถึงจะได้แต้ม?)</span>
                </h4>
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                    ทำภารกิจง่ายๆ เหล่านี้เพื่อรับคะแนนสะสมเข้ากระเป๋าของคุณทันที:
                </p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem;">
                    <!-- Action 1: Shopping -->
                    <div style="border: 1.5px solid #FFE4D6; background: #FFF9F6; border-radius: var(--radius-md); padding: 1.2rem; display: flex; gap: 1rem; align-items: flex-start;">
                        <span style="font-size: 2.2rem; line-height: 1;">🛒</span>
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <h5 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin: 0;">สั่งซื้อน้องแมว & สินค้า</h5>
                                <span style="background: var(--primary-coral); color: #FFF; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">+1 แต้ม / 100บ.</span>
                            </div>
                            <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                                ทุกยอดการสั่งซื้อ 100 บาท รับทันที 1 Paw Point เช่น รับเลี้ยงน้องแมว 25,000 บาท ได้รับทันที <strong>250 แต้ม</strong>
                            </p>
                        </div>
                    </div>

                    <!-- Action 2: Register -->
                    <div style="border: 1.5px solid #FEF3C7; background: #FFFDF5; border-radius: var(--radius-md); padding: 1.2rem; display: flex; gap: 1rem; align-items: flex-start;">
                        <span style="font-size: 2.2rem; line-height: 1;">✨</span>
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <h5 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin: 0;">สมัครสมาชิกใหม่</h5>
                                <span style="background: #D97706; color: #FFF; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">+100 แต้ม</span>
                            </div>
                            <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                                ลงทะเบียนเปิดบัญชีสมาชิกใหม่ รับแต้มโบนัสเริ่มต้นทันที <strong>100 Paw Points</strong> พร้อมโค้ดส่วนลด 15%
                            </p>
                        </div>
                    </div>

                    <!-- Action 3: Review -->
                    <div style="border: 1.5px solid #E0E7FF; background: #F8FAFF; border-radius: var(--radius-md); padding: 1.2rem; display: flex; gap: 1rem; align-items: flex-start;">
                        <span style="font-size: 2.2rem; line-height: 1;">⭐</span>
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <h5 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin: 0;">เขียนรีวิวความประทับใจ</h5>
                                <span style="background: #4F46E5; color: #FFF; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">+50 แต้ม</span>
                            </div>
                            <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                                รีวิวความน่ารักและการดูแลหลังรับน้องแมวไป พร้อมแนบรูปถ่าย รับทันที <strong>50 Paw Points</strong> ต่อรายการ
                            </p>
                        </div>
                    </div>

                    <!-- Action 4: Birthday -->
                    <div style="border: 1.5px solid #FCE7F3; background: #FFF5F9; border-radius: var(--radius-md); padding: 1.2rem; display: flex; gap: 1rem; align-items: flex-start;">
                        <span style="font-size: 2.2rem; line-height: 1;">🎂</span>
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <h5 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin: 0;">ของขวัญเดือนเกิด</h5>
                                <span style="background: #DB2777; color: #FFF; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">+200 แต้ม</span>
                            </div>
                            <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                                ฉลองวันเกิดของคุณด้วยแต้มของขวัญพิเศษ <strong>200 Paw Points</strong> โอนเข้าบัญชีอัตโนมัติในเดือนเกิด
                            </p>
                        </div>
                    </div>

                    <!-- Action 5: Referral -->
                    <div style="border: 1.5px solid #D1FAE5; background: #F6FEFA; border-radius: var(--radius-md); padding: 1.2rem; display: flex; gap: 1rem; align-items: flex-start;">
                        <span style="font-size: 2.2rem; line-height: 1;">🤝</span>
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <h5 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin: 0;">แนะนำเพื่อนมารับเลี้ยง</h5>
                                <span style="background: #059669; color: #FFF; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">+300 แต้ม</span>
                            </div>
                            <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                                ชวนเพื่อนมารับเลี้ยงน้องแมว เมื่อเพื่อนสั่งซื้อครั้งแรก คุณรับ <strong>300 Paw Points</strong> เพื่อนรับส่วนลด 10%
                            </p>
                        </div>
                    </div>

                    <!-- Action 6: Cat Quiz -->
                    <div style="border: 1.5px solid #EDE9FE; background: #FBF9FF; border-radius: var(--radius-md); padding: 1.2rem; display: flex; gap: 1rem; align-items: flex-start;">
                        <span style="font-size: 2.2rem; line-height: 1;">🧭</span>
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <h5 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin: 0;">ทำแบบประเมินค้นหาแมวที่ใช่</h5>
                                <span style="background: #7C3AED; color: #FFF; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">+20 แต้ม</span>
                            </div>
                            <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                                ปรึกษาและตอบคำถามแบบทดสอบความพร้อมในการเลี้ยงน้องแมวผ่านระบบ รับฟรีทันที <strong>20 Paw Points</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Points Calculator Simulator (เครื่องคำนวณแต้มจำลอง) -->
            <div style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); color: #FFFFFF; border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.2rem;">
                    <div>
                        <h4 style="font-size: 1.2rem; font-weight: 800; margin: 0 0 4px 0; color: #F8FAFC;">
                            🧮 เครื่องคำนวณแต้มสะสมจากยอดซื้อ (Points Calculator)
                        </h4>
                        <p style="font-size: 0.85rem; color: #94A3B8; margin: 0;">
                            ลองกรอกยอดซื้อที่ต้องการเพื่อดูจำนวน Paw Points ที่คุณจะได้รับทันที:
                        </p>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.2rem;">
                    <div style="position: relative; max-width: 260px; width: 100%;">
                        <input type="number" id="sim-amount-input" value="25000" min="0" step="500" class="form-input" style="background: #334155; border: 1.5px solid #475569; color: #FFF; font-size: 1.2rem; font-weight: 800; padding: 10px 45px 10px 14px; border-radius: 8px; width: 100%;" oninput="calculateSimPoints()">
                        <span style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-weight: 700;">฿</span>
                    </div>

                    <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; padding: 8px 18px; display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 0.85rem; color: #CBD5E1;">จะได้รับแต้ม:</span>
                        <span id="sim-points-result" style="font-family: 'Outfit'; font-size: 1.6rem; font-weight: 900; color: #FBBF24;">+250 พอยท์</span>
                        <span style="font-size: 0.82rem; color: #94A3B8;">(มูลค่าส่วนลด ฿250 บาท)</span>
                    </div>
                </div>

                <div style="font-size: 0.8rem; color: #94A3B8;">
                    * ยิ่งระดับสมาชิกสูงขึ้น คุณจะได้รับตัวคูณแต้มโบนัสพิเศษ (Silver x1.2, Gold VIP x1.5, Diamond x2.0)
                </div>
            </div>
        </div>
    </div>

    <script>
    function calculateSimPoints() {
        const input = document.getElementById('sim-amount-input');
        const result = document.getElementById('sim-points-result');
        let val = parseFloat(input.value) || 0;
        if (val < 0) val = 0;
        const pts = Math.floor(val / 100);
        result.innerText = `+${pts.toLocaleString()} พอยท์`;
    }
    </script>

    <!-- =========================================================
         TAB 4: กล่องจดหมาย & ข่าวสารจากร้านค้า (Inbox & Newsletters)
         ========================================================= -->
    <div id="tab-inbox" class="profile-tab-panel" style="<?php echo $active_tab === 'inbox' ? 'display: block;' : 'display: none;'; ?>">
        <div class="checkout-block">
            <div class="checkout-block-header">
                <h3 class="checkout-block-title">
                    <span>📬 กล่องจดหมาย & ข่าวสารพิเศษสำหรับคุณ</span>
                    <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">(พบ <?php echo count($user_messages); ?> ข้อความ)</span>
                </h3>
                <a href="welcome_deal.php" class="btn btn-primary btn-sm">
                    🎁 หน้าดีลสมาชิกใหม่
                </a>
            </div>

            <?php if (empty($user_messages)): ?>
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--text-muted);">
                    <span style="font-size: 3.5rem; display: block; margin-bottom: 0.5rem;">📭</span>
                    <h4 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">ยังไม่มีข้อความในกล่องจดหมาย</h4>
                    <p style="font-size: 0.9rem; margin-bottom: 1.5rem;">ข่าวสารและสิทธิพิเศษจากทางร้านจะปรากฏที่นี่เมื่อมีการส่งโปรโมชันใหม่</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php foreach ($user_messages as $msg): ?>
                        <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm);">
                            <!-- Message Header -->
                            <div style="background: <?php echo ($msg['type'] ?? '') === 'welcome' ? 'linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%)' : 'var(--bg-card-subtle)'; ?>; padding: 1rem 1.4rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.8rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        <span style="font-size: 0.75rem; font-weight: 800; padding: 0.2rem 0.6rem; border-radius: 9999px; background: <?php echo ($msg['type'] ?? '') === 'welcome' ? '#D97706' : 'var(--primary-coral)'; ?>; color: #FFFFFF;">
                                            <?php echo ($msg['type'] ?? '') === 'welcome' ? '🎉 ต้อนรับสมาชิกใหม่' : '📢 ข่าวสารจากร้านค้า'; ?>
                                        </span>
                                        <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0;">
                                            <?php echo htmlspecialchars($msg['title']); ?>
                                        </h4>
                                    </div>
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">
                                    🕒 <?php echo htmlspecialchars($msg['sent_at']); ?>
                                </div>
                            </div>

                            <!-- Message Body -->
                            <div style="padding: 1.4rem;">
                                <p style="font-size: 0.95rem; line-height: 1.6; color: var(--text-secondary); margin-bottom: 1rem;">
                                    <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                </p>

                                <?php if (!empty($msg['sender_name'])): ?>
                                    <div style="display: inline-flex; align-items: center; gap: 8px; background: var(--bg-page); border: 1px solid var(--border-color); padding: 6px 12px; border-radius: 999px; margin-bottom: 1rem;">
                                        <img src="<?php echo htmlspecialchars(!empty($msg['sender_avatar']) ? $msg['sender_avatar'] : 'assets/images/logo.png'); ?>" alt="Sender" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--primary-coral);" onerror="this.src='assets/images/logo.png'">
                                        <span style="font-size: 0.78rem; color: var(--text-muted);">ผู้จัดส่งข้อความ:</span>
                                        <strong style="font-size: 0.82rem; color: var(--text-main);"><?php echo htmlspecialchars($msg['sender_name']); ?></strong>
                                        <?php if (!empty($msg['sender_email'])): ?>
                                            <span style="font-size: 0.75rem; color: var(--text-muted);">(<?php echo htmlspecialchars($msg['sender_email']); ?>)</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($msg['voucher_code'])): ?>
                                    <!-- Voucher Box for Welcome -->
                                    <div style="background: linear-gradient(135deg, rgba(255,117,86,0.08) 0%, rgba(255,90,95,0.12) 100%); border: 2px dashed var(--primary-coral); border-radius: 10px; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 1rem;">
                                        <div>
                                            <div style="font-size: 0.82rem; color: var(--primary-coral); font-weight: 700;">🎁 คูปองส่วนลดพิเศษ 15% ของคุณ:</div>
                                            <div style="font-family: 'Outfit'; font-size: 1.4rem; font-weight: 900; color: var(--primary-coral); letter-spacing: 1px;">
                                                <?php echo htmlspecialchars($msg['voucher_code']); ?>
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 8px;">
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars($msg['voucher_code']); ?>'); alert('✓ คัดลอกโค้ดส่วนลดเรียบร้อยแล้ว!');">
                                                คัดลอกโค้ด 📋
                                            </button>
                                            <a href="welcome_deal.php" class="btn btn-primary btn-sm">
                                                ใช้โค้ดช้อปเลย 🛒
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Featured Items Attached to Message -->
                                <?php if (!empty($msg['items']) && is_array($msg['items'])): ?>
                                    <div style="margin-top: 1rem; border-top: 1px dashed var(--border-color); padding-top: 1rem;">
                                        <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.8rem;">
                                            🐾 สินค้าและน้องแมวที่แนะนำในข่าวสารนี้:
                                        </div>
                                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px;">
                                            <?php foreach ($msg['items'] as $itemKey): 
                                                $prod = $cats[$itemKey] ?? ($starter_bundles[$itemKey] ?? null);
                                                if ($prod):
                                            ?>
                                                <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: 8px; padding: 10px; display: flex; align-items: center; gap: 10px;">
                                                    <img src="assets/images/<?php echo htmlspecialchars($prod['image']); ?>" alt="" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color);">
                                                    <div style="overflow: hidden; flex: 1;">
                                                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                            <?php echo htmlspecialchars($prod['name']); ?>
                                                        </div>
                                                        <div style="font-size: 0.78rem; color: var(--primary-coral); font-weight: 700;">
                                                            <?php echo number_format($prod['price']); ?> ฿
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- =========================================================
         TAB 6: คูปองส่วนลด & รางวัลจากวงล้อนำโชค (Lucky Wheel & Coupons)
         ========================================================= -->
    <div id="tab-coupons" class="profile-tab-panel" style="<?php echo $active_tab === 'coupons' ? 'display: block;' : 'display: none;'; ?>">
        <div class="checkout-block">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                <div>
                    <h3 class="checkout-block-title" style="margin-bottom: 0.3rem;">
                        🎁 คูปอง & ของรางวัลของคุณ (My Coupons & Lucky Rewards)
                    </h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0;">
                        รวบรวมของรางวัลที่ได้จากการหมุนวงล้อนำโชค และโค้ดส่วนลดสิทธิพิเศษสำหรับสมาชิก Purrfect Shop
                    </p>
                </div>
                <a href="lucky_wheel.php" class="btn btn-primary btn-sm" id="coupons-wheel-link" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700;">
                    🎡 ไปหมุนวงล้อรับรางวัลฟรี (3 ครั้ง/วัน) ➔
                </a>
            </div>

            <!-- ส่วนที่ 1: รางวัลที่ได้รับจากวงล้อนำโชค (Dynamic จาก LocalStorage) -->
            <div style="margin-bottom: 2.5rem;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 1rem;">
                    <span style="font-size: 1.3rem;">🎡</span>
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin: 0;">
                        ของรางวัลที่สุ่มได้จากวงล้อนำโชค (Lucky Wheel Rewards)
                    </h4>
                    <span id="lucky-count-pill" style="background: var(--accent-mint); color: #065F46; font-size: 0.75rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">0 รายการ</span>
                </div>

                <div id="lucky-rewards-container">
                    <!-- Default Empty State -->
                    <div id="lucky-rewards-empty" style="background: var(--bg-card-subtle); border: 2px dashed var(--border-color); border-radius: var(--radius-md); padding: 2.5rem 1.5rem; text-align: center; color: var(--text-muted);">
                        <div style="font-size: 3rem; margin-bottom: 0.6rem;">🎯</div>
                        <h5 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">ยังไม่มีรายการของรางวัลจากวงล้อ</h5>
                        <p style="font-size: 0.88rem; max-width: 500px; margin: 0 auto 1.2rem auto;">
                            คุณมีสิทธิ์หมุนวงล้อนำโชคฟรีวันละ 3 ครั้ง เพื่อลุ้นรับส่วนลดสูงสุด 25%, ขนมฟรีซดราย, ชุด Starter Kit และ Paw Points!
                        </p>
                        <a href="lucky_wheel.php" class="btn btn-primary" id="coupons-wheel-empty-link" style="display: inline-flex; align-items: center; gap: 6px;">
                            🎡 หมุนวงล้อนำโชคเลย (ฟรี 3 ครั้ง/วัน) ➔
                        </a>
                    </div>
                    <!-- Dynamic Grid for Won Prizes -->
                    <div id="lucky-rewards-grid" style="display: none; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;"></div>
                </div>
            </div>

            <!-- ส่วนที่ 2: คูปองส่วนลดและสิทธิพิเศษทั้งหมดของร้าน (All Active Store Coupons) -->
            <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 1rem;">
                    <span style="font-size: 1.3rem;">🎫</span>
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin: 0;">
                        คูปองส่วนลดประจำร้าน & สิทธิ์สมาชิก (Active Store Coupons)
                    </h4>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.2rem;">
                    <!-- Coupon 1: PURR25NEW -->
                    <div style="background: linear-gradient(135deg, rgba(255, 107, 74, 0.06) 0%, rgba(255, 140, 66, 0.12) 100%); border: 1.5px dashed var(--primary-coral); border-radius: var(--radius-md); padding: 1.2rem; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                        <span style="position: absolute; top: 12px; right: 12px; background: #FF5A5F; color: #fff; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">HOT DEAL</span>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.6rem;">
                                <span style="font-size: 1.8rem;">🎉</span>
                                <div>
                                    <h5 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0;">ส่วนลดต้อนรับสมาชิก 25%</h5>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">ใช้ได้กับการรับเลี้ยงน้องแมวทุกตัว</span>
                                </div>
                            </div>
                            <p style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.4;">
                                ลดทันที 25% สำหรับการสั่งจองและรับเลี้ยงน้องแมวทุกสายพันธุ์ ไม่มียอดขั้นต่ำ
                            </p>
                        </div>
                        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                            <div>
                                <span style="font-size: 0.7rem; color: var(--text-muted); display: block;">รหัสคูปอง:</span>
                                <strong style="font-family: 'Outfit'; font-size: 1.1rem; color: var(--primary-coral); letter-spacing: 1px;">PURR25NEW</strong>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="copyCouponCode('PURR25NEW', this)" style="padding: 4px 10px; font-size: 0.75rem;">คัดลอก 📋</button>
                                <a href="cart.php?apply_coupon=PURR25NEW" class="btn btn-primary btn-sm coupon-apply-btn" data-code="PURR25NEW" style="padding: 4px 10px; font-size: 0.75rem;">ใช้เลย 🛒</a>
                            </div>
                        </div>
                    </div>

                    <!-- Coupon 2: CAT10OFF -->
                    <div style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.06) 0%, rgba(37, 99, 235, 0.12) 100%); border: 1.5px dashed #3B82F6; border-radius: var(--radius-md); padding: 1.2rem; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                        <span style="position: absolute; top: 12px; right: 12px; background: #3B82F6; color: #fff; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">FLASH 10%</span>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.6rem;">
                                <span style="font-size: 1.8rem;">⚡</span>
                                <div>
                                    <h5 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0;">Flash Deal ลด 10%</h5>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">ลดทันทีทุกยอดคำสั่งซื้อ</span>
                                </div>
                            </div>
                            <p style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.4;">
                                รับส่วนลด 10% สำหรับการสั่งซื้อน้องแมวและแพ็กเกจดูแลสุขภาพครบวงจร
                            </p>
                        </div>
                        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                            <div>
                                <span style="font-size: 0.7rem; color: var(--text-muted); display: block;">รหัสคูปอง:</span>
                                <strong style="font-family: 'Outfit'; font-size: 1.1rem; color: #2563EB; letter-spacing: 1px;">CAT10OFF</strong>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="copyCouponCode('CAT10OFF', this)" style="padding: 4px 10px; font-size: 0.75rem;">คัดลอก 📋</button>
                                <a href="cart.php?apply_coupon=CAT10OFF" class="btn btn-primary btn-sm coupon-apply-btn" data-code="CAT10OFF" style="padding: 4px 10px; font-size: 0.75rem;">ใช้เลย 🛒</a>
                            </div>
                        </div>
                    </div>

                    <!-- Coupon 3: WELCOME5 -->
                    <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.06) 0%, rgba(5, 150, 105, 0.12) 100%); border: 1.5px dashed #10B981; border-radius: var(--radius-md); padding: 1.2rem; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                        <span style="position: absolute; top: 12px; right: 12px; background: #10B981; color: #fff; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">VIP 5%</span>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.6rem;">
                                <span style="font-size: 1.8rem;">🌟</span>
                                <div>
                                    <h5 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0;">ส่วนลดสมาชิกถาวร 5%</h5>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">สิทธิ์สำหรับสมาชิกทุกคน</span>
                                </div>
                            </div>
                            <p style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.4;">
                                สิทธิพิเศษเฉพาะสมาชิก Purrfect Shop ใช้ลดเพิ่มได้ทุกออเดอร์ไม่มีวันหมดอายุ
                            </p>
                        </div>
                        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                            <div>
                                <span style="font-size: 0.7rem; color: var(--text-muted); display: block;">รหัสคูปอง:</span>
                                <strong style="font-family: 'Outfit'; font-size: 1.1rem; color: #059669; letter-spacing: 1px;">WELCOME5</strong>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="copyCouponCode('WELCOME5', this)" style="padding: 4px 10px; font-size: 0.75rem;">คัดลอก 📋</button>
                                <a href="cart.php?apply_coupon=WELCOME5" class="btn btn-primary btn-sm coupon-apply-btn" data-code="WELCOME5" style="padding: 4px 10px; font-size: 0.75rem;">ใช้เลย 🛒</a>
                            </div>
                        </div>
                    </div>

                    <!-- Coupon 4: KITFREE100 -->
                    <div style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.06) 0%, rgba(217, 119, 6, 0.12) 100%); border: 1.5px dashed #F59E0B; border-radius: var(--radius-md); padding: 1.2rem; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                        <span style="position: absolute; top: 12px; right: 12px; background: #F59E0B; color: #fff; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">STARTER SET</span>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.6rem;">
                                <span style="font-size: 1.8rem;">📦</span>
                                <div>
                                    <h5 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0;">รับฟรี Purrfect Starter Kit</h5>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">มูลค่ารวม 2,500 บาท</span>
                                </div>
                            </div>
                            <p style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.4;">
                                รับฟรีเซ็ตของใช้น้องแมวแรกเกิด ชามอาหาร กระบะทรายพรีเมียม และของเล่นเสริมทักษะ
                            </p>
                        </div>
                        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                            <div>
                                <span style="font-size: 0.7rem; color: var(--text-muted); display: block;">รหัสคูปอง:</span>
                                <strong style="font-family: 'Outfit'; font-size: 1.1rem; color: #D97706; letter-spacing: 1px;">KITFREE100</strong>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="copyCouponCode('KITFREE100', this)" style="padding: 4px 10px; font-size: 0.75rem;">คัดลอก 📋</button>
                                <a href="cart.php?apply_coupon=KITFREE100" class="btn btn-primary btn-sm coupon-apply-btn" data-code="KITFREE100" style="padding: 4px 10px; font-size: 0.75rem;">ใช้เลย 🛒</a>
                            </div>
                        </div>
                    </div>

                    <!-- Coupon 5: FREESHIP -->
                    <div style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.06) 0%, rgba(124, 58, 237, 0.12) 100%); border: 1.5px dashed #8B5CF6; border-radius: var(--radius-md); padding: 1.2rem; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                        <span style="position: absolute; top: 12px; right: 12px; background: #8B5CF6; color: #fff; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">FREE TAXI</span>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.6rem;">
                                <span style="font-size: 1.8rem;">🚚</span>
                                <div>
                                    <h5 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0;">ฟรี ค่าส่ง VIP Pet Taxi</h5>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">รถตู้ปรับอากาศส่งถึงบ้าน</span>
                                </div>
                            </div>
                            <p style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.4;">
                                บริการส่งมอบน้องแมวถึงหน้าบ้านด้วยรถตู้ควบคุมอุณหภูมิและพี่เลี้ยงดูแลตลอดทาง
                            </p>
                        </div>
                        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                            <div>
                                <span style="font-size: 0.7rem; color: var(--text-muted); display: block;">รหัสคูปอง:</span>
                                <strong style="font-family: 'Outfit'; font-size: 1.1rem; color: #7C3AED; letter-spacing: 1px;">FREESHIP</strong>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="copyCouponCode('FREESHIP', this)" style="padding: 4px 10px; font-size: 0.75rem;">คัดลอก 📋</button>
                                <a href="cart.php?apply_coupon=FREESHIP" class="btn btn-primary btn-sm coupon-apply-btn" data-code="FREESHIP" style="padding: 4px 10px; font-size: 0.75rem;">ใช้เลย 🛒</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================
         TAB 7: สมุดวัคซีน & แผนสุขภาพ (Cat Health & Vaccines)
         ========================================================= -->
    <div id="tab-vaccine" class="profile-tab-panel" style="<?php echo $active_tab === 'vaccine' ? 'display: block;' : 'display: none;'; ?>">
        <div class="checkout-block">
            <!-- Tab Header -->
            <div class="checkout-block-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-bottom: 1.5px solid var(--border-color); padding-bottom: 1.2rem; margin-bottom: 1.5rem;">
                <div>
                    <h3 class="checkout-block-title" style="margin: 0; display: flex; align-items: center; gap: 8px; font-size: 1.35rem;">
                        <span>💉 สมุดวัคซีน & ปฏิทินสุขภาพน้องแมว</span>
                    </h3>
                    <p style="font-size: 0.88rem; color: var(--text-muted); margin: 4px 0 0 0;">
                        ตรวจเช็คสถานะวัคซีนของน้องแมวแต่ละตัว พร้อมดูวันนัดหมายบนปฏิทินแบบเรียลไทม์
                    </p>
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="vaccine_reminder.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; padding: 0.65rem 1.3rem; font-weight: 800; font-size: 0.88rem; text-decoration: none; border-radius: 12px;">
                        🩺 เปิดคำนวณตารางวัคซีนฉบับเต็ม ➔
                    </a>
                </div>
            </div>

            <!-- Two-Column Layout: Left = Cats List & Filters | Right = Calendar & Appointments -->
            <div class="vaccine-twocol-grid" style="display: grid; grid-template-columns: 1.05fr 1fr; gap: 1.8rem; align-items: start;">
                
                <!-- ================= LEFT COLUMN: CATS LIST & VACCINE STATUS FILTER ================= -->
                <div class="vaccine-left-col">
                    <!-- Filter Controls Header -->
                    <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 1.2rem; margin-bottom: 1.2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; flex-wrap: wrap; gap: 6px;">
                            <span style="font-size: 0.9rem; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: 6px;">
                                🔍 ฟิลเตอร์ตรวจสถานะวัคซีน:
                            </span>
                            <span id="filter-count-badge" style="font-size: 0.75rem; background: #E2E8F0; color: #475569; font-weight: 800; padding: 2px 8px; border-radius: 999px;">
                                แสดงทั้งหมด
                            </span>
                        </div>

                        <!-- Status Filter Buttons -->
                        <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 0.8rem;">
                            <button type="button" class="vaccine-filter-chip active" onclick="filterVaccineCats('all', this)">
                                🐾 ทั้งหมด
                            </button>
                            <button type="button" class="vaccine-filter-chip" onclick="filterVaccineCats('due', this)">
                                ⚠️ ถึงกำหนด / เร็วๆ นี้
                            </button>
                            <button type="button" class="vaccine-filter-chip" onclick="filterVaccineCats('pending', this)">
                                ⏳ รอฉีด
                            </button>
                            <button type="button" class="vaccine-filter-chip" onclick="filterVaccineCats('completed', this)">
                                ✅ ฉีดครบแล้ว
                            </button>
                        </div>

                        <!-- Quick Search Input -->
                        <div style="position: relative;">
                            <input type="text" id="vaccineCatSearchInput" oninput="searchVaccineCats(this.value)" 
                                   placeholder="พิมพ์ชื่อ หรือสายพันธุ์น้องแมวเพื่อค้นหา..." 
                                   style="width: 100%; padding: 0.55rem 0.9rem 0.55rem 2.2rem; border-radius: 10px; border: 1.5px solid #CBD5E1; font-size: 0.82rem; background: #FFFFFF;">
                            <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.9rem; color: #94A3B8;">🔎</span>
                        </div>
                    </div>

                    <!-- Cats List Container -->
                    <div id="profile-vaccine-cats-container" style="display: flex; flex-direction: column; gap: 1rem;">
                        <!-- Dynamically populated or rendered from PHP -->
                        <?php 
                        // Fallback sample cats if empty
                        $renderCats = !empty($adoptedCats) ? $adoptedCats : [
                            ['id' => 'cat_persian', 'name' => 'น้องปุยหิมะ', 'breed' => 'เปอร์เซีย (Persian)', 'image' => 'cat_persian.jpg', 'age' => '3 เดือน', 'gender' => 'เมีย'],
                            ['id' => 'cat_ragdoll', 'name' => 'น้องคอตตอน', 'breed' => 'แร็กดอลล์ (Ragdoll)', 'image' => 'cat_ragdoll.jpg', 'age' => '2.5 เดือน', 'gender' => 'เมีย'],
                            ['id' => 'cat_americanshorthair', 'name' => 'น้องการ์ฟิลด์', 'breed' => 'American Shorthair', 'image' => 'cat_americanshorthair.jpg', 'age' => '2.5 เดือน', 'gender' => 'ผู้']
                        ];
                        ?>
                        <?php foreach ($renderCats as $idx => $c): ?>
                            <div class="cat-vaccine-item-card <?php echo $idx === 0 ? 'selected-cat-card' : ''; ?>" 
                                 data-cat-name="<?php echo htmlspecialchars($c['name']); ?>"
                                 data-cat-breed="<?php echo htmlspecialchars($c['breed']); ?>"
                                 data-cat-image="<?php echo htmlspecialchars($c['image']); ?>"
                                 data-cat-age="<?php echo htmlspecialchars($c['age']); ?>"
                                 onclick="selectCatForCalendar('<?php echo htmlspecialchars(addslashes($c['name'])); ?>', '<?php echo htmlspecialchars(addslashes($c['breed'])); ?>', '<?php echo htmlspecialchars(addslashes($c['image'])); ?>', this)"
                                 style="background: #FFFFFF; border: 2px solid <?php echo $idx === 0 ? '#10B981' : '#E2E8F0'; ?>; border-radius: 16px; padding: 1.1rem; box-shadow: 0 4px 14px rgba(0,0,0,0.03); cursor: pointer; transition: all 0.25s ease; position: relative;">
                                
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 0.8rem;">
                                    <div style="display: flex; align-items: center; gap: 12px; overflow: hidden;">
                                        <img src="assets/images/<?php echo htmlspecialchars($c['image']); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>" 
                                             style="width: 60px; height: 60px; border-radius: 14px; object-fit: cover; border: 2px solid #10B981; flex-shrink: 0;">
                                        <div style="overflow: hidden;">
                                            <strong style="font-size: 1.05rem; color: #1E293B; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?php echo htmlspecialchars($c['name']); ?>
                                            </strong>
                                            <span style="display: inline-block; font-size: 0.72rem; background: #D1FAE5; color: #065F46; font-weight: 800; padding: 2px 7px; border-radius: 999px; margin-top: 2px;">
                                                <?php echo htmlspecialchars($c['breed']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <span class="cat-status-badge" style="font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 999px; background: #EFF6FF; color: #1E40AF; white-space: nowrap;">
                                        ⏳ ติดตามวัคซีน
                                    </span>
                                </div>

                                <!-- Vaccine Progress bar -->
                                <div style="background: #F8FAFC; border-radius: 10px; padding: 0.6rem 0.8rem; margin-bottom: 0.8rem; border: 1px solid #E2E8F0;">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 4px;">
                                        <span>ความคืบหน้าการฉีด:</span>
                                        <span class="cat-progress-text" style="color: #059669;">2/6 เข็ม (33%)</span>
                                    </div>
                                    <div style="width: 100%; height: 6px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                                        <div class="cat-progress-bar" style="width: 33%; height: 100%; background: linear-gradient(90deg, #10B981, #059669); border-radius: 999px;"></div>
                                    </div>
                                </div>

                                <!-- Card Action Footer -->
                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span style="font-size: 0.75rem; color: #059669; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                                        📅 คลิกเพื่อดูนัดบนปฏิทิน
                                    </span>
                                    <a href="vaccine_reminder.php?cat_name=<?php echo urlencode($c['name']); ?>&breed=<?php echo urlencode($c['breed']); ?>&image=<?php echo urlencode($c['image']); ?>" 
                                       onclick="event.stopPropagation();"
                                       class="btn btn-secondary btn-sm btn-vaccine-link" 
                                       style="font-size: 0.75rem; font-weight: 800; padding: 3px 9px; border-radius: 8px; color: #059669; background: #ECFDF5; border-color: #A7F3D0; text-decoration: none;">
                                        💉 สมุดวัคซีน ➔
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ================= RIGHT COLUMN: CALENDAR & APPOINTMENTS ================= -->
                <div class="vaccine-right-col" style="background: #FFFFFF; border: 2px solid #E2E8F0; border-radius: 24px; padding: 1.6rem; box-shadow: 0 8px 25px rgba(0,0,0,0.04); position: sticky; top: 1rem;">
                    
                    <!-- Selected Cat Banner on Calendar -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); border: 1.5px solid #A7F3D0; border-radius: 14px; padding: 0.75rem 1rem; margin-bottom: 1.2rem;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 1.4rem;">📅</span>
                            <div>
                                <span style="font-size: 0.72rem; color: #047857; font-weight: 800; text-transform: uppercase; display: block;">ปฏิทินนัดหมายสำหรับ:</span>
                                <strong id="cal-selected-cat-name" style="font-size: 0.95rem; color: #064E3B;">น้องปุยหิมะ (ทุกรายการ)</strong>
                            </div>
                        </div>
                        <button type="button" onclick="showAllCatsOnCalendar()" class="btn btn-secondary btn-sm" style="font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 8px; background: #FFFFFF; color: #065F46; border: 1px solid #6EE7B7;">
                            แสดงแมวทั้งหมด
                        </button>
                    </div>

                    <!-- Calendar Header & Navigation -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 id="calendarMonthTitle" style="font-size: 1.15rem; font-weight: 900; color: #1E293B; margin: 0;">
                            กันยายน 2026
                        </h4>
                        <div style="display: flex; gap: 6px; align-items: center;">
                            <button type="button" onclick="changeCalendarMonth(-1)" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-weight: 800; border-radius: 8px; font-size: 0.8rem;">
                                ◀
                            </button>
                            <button type="button" onclick="goToTodayCalendar()" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-weight: 800; border-radius: 8px; font-size: 0.75rem;">
                                วันนี้
                            </button>
                            <button type="button" onclick="changeCalendarMonth(1)" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-weight: 800; border-radius: 8px; font-size: 0.8rem;">
                                ▶
                            </button>
                        </div>
                    </div>

                    <!-- Calendar Days of Week Header -->
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: 800; font-size: 0.75rem; color: #64748B; margin-bottom: 6px;">
                        <span style="color: #EF4444;">อา.</span>
                        <span>จ.</span>
                        <span>อ.</span>
                        <span>พ.</span>
                        <span>พฤ.</span>
                        <span>ศ.</span>
                        <span style="color: #3B82F6;">ส.</span>
                    </div>

                    <!-- Calendar Grid Container -->
                    <div id="calendarGridDays" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 1.4rem;">
                        <!-- Generated dynamically by JS -->
                    </div>

                    <!-- Selected Date Appointment Details Section -->
                    <div id="calendarEventDetailsBox" style="background: #FFFFFF; border: 2px solid #E2E8F0; border-radius: 18px; padding: 1.2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
                        <div style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); border: 1.5px solid #10B981; border-radius: 14px; padding: 0.65rem 1rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.12);">
                            <div id="selectedDateLabel" style="font-size: 0.92rem; font-weight: 800; color: #064E3B; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                📋 นัดหมายวันที่: <span style="background: #FFFFFF; color: #047857; font-weight: 900; padding: 0.2rem 0.65rem; border-radius: 8px; border: 1.5px solid #6EE7B7; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">2026-09-30</span>
                            </div>
                            <span id="appointmentCountBadge" style="font-size: 0.75rem; background: #059669; color: #FFFFFF; font-weight: 800; padding: 3px 10px; border-radius: 999px; box-shadow: 0 2px 6px rgba(5, 150, 105, 0.3);">
                                1 รายการ
                            </span>
                        </div>

                        <!-- Appointments List -->
                        <div id="calendarEventsList" style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <!-- Dynamically generated -->
                        </div>
                    </div>

                    <!-- Quick LINE & Vet Appointment CTA -->
                    <div style="margin-top: 1.2rem; text-align: center;">
                        <a href="https://line.me" target="_blank" class="btn btn-primary" style="width: 100%; padding: 0.7rem; font-weight: 800; font-size: 0.88rem; border-radius: 12px; background: #06C755; border: none; box-shadow: 0 4px 12px rgba(6,199,85,0.25); display: inline-flex; align-items: center; justify-content: center; gap: 6px; text-decoration: none; color: #fff;">
                            💬 ปรึกษาสัตวแพทย์ & จองคิวฉีดวัคซีนผ่าน LINE
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
/* Modern Luxury Profile Tab Navigation (Card Blocks Grid) */
.profile-tabs-nav {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 1rem !important;
    margin-bottom: 2.2rem !important;
    padding: 1.25rem !important;
    background: #FFFFFF !important;
    border: 1.5px solid #E2E8F0 !important;
    border-radius: 26px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
}

@media (max-width: 990px) {
    .profile-tabs-nav {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 640px) {
    .profile-tabs-nav {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
        padding: 0.85rem !important;
    }
}

.profile-nav-btn {
    display: flex !important;
    align-items: center !important;
    text-align: left !important;
    gap: 12px !important;
    padding: 1rem 1.15rem !important;
    border-radius: 18px !important;
    background: #FFFFFF !important;
    border: 1.5px solid #E2E8F0 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03) !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    cursor: pointer !important;
    text-decoration: none !important;
    position: relative !important;
    user-select: none;
    width: 100% !important;
}

.profile-nav-btn:hover {
    background: #FAFAFA !important;
    border-color: #CBD5E1 !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06) !important;
}

.profile-nav-btn.active {
    background: linear-gradient(135deg, #FF7556 0%, #FF533D 100%) !important;
    color: #FFFFFF !important;
    border-color: #FF533D !important;
    box-shadow: 0 8px 24px rgba(255, 117, 86, 0.35) !important;
    transform: translateY(-2px) !important;
}

.nav-btn-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.profile-nav-btn.active .nav-btn-icon-wrap {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #FFFFFF !important;
}

.nav-btn-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
    flex: 1;
}

.nav-btn-top {
    display: flex;
    align-items: center;
    gap: 6px;
    justify-content: space-between;
    flex-wrap: wrap;
}

.profile-nav-btn .nav-label {
    font-size: 0.92rem;
    font-weight: 800;
    color: #1E293B;
    line-height: 1.25;
    transition: color 0.2s ease;
}

.profile-nav-btn.active .nav-label {
    color: #FFFFFF !important;
}

.nav-desc {
    font-size: 0.75rem;
    color: #94A3B8;
    font-weight: 500;
    line-height: 1.35;
    display: block;
    transition: color 0.2s ease;
}

.profile-nav-btn.active .nav-desc {
    color: rgba(255, 255, 255, 0.88) !important;
}

.profile-nav-btn .nav-badge {
    font-size: 0.72rem;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 999px;
    line-height: 1;
    display: inline-block;
    flex-shrink: 0;
}

.nav-badge-coral {
    background: rgba(255, 117, 86, 0.15);
    color: var(--primary-coral);
    border: 1px solid rgba(255, 117, 86, 0.3);
}
.nav-badge-mint {
    background: #D1FAE5;
    color: #065F46;
    border: 1px solid #A7F3D0;
}
.nav-badge-amber {
    background: #FEF3C7;
    color: #92400E;
    border: 1px solid #FCD34D;
}
.nav-badge-blue {
    background: #DBEAFE;
    color: #1E40AF;
    border: 1px solid #BFDBFE;
}
.profile-nav-btn.active .nav-badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.4) !important;
}

.profile-nav-link-track {
    background: #FFFDF9 !important;
    border-color: #FED7AA !important;
}
.profile-nav-link-track:hover {
    border-color: #FB923C !important;
    background: #FFF7ED !important;
}
.profile-nav-link-track .nav-label {
    color: #C2410C !important;
}

.profile-nav-link-admin {
    background: linear-gradient(135deg, #1E1B4B 0%, #312E81 100%) !important;
    border-color: #F59E0B !important;
    color: #FCD34D !important;
    box-shadow: 0 4px 15px rgba(30, 27, 75, 0.25) !important;
}
.profile-nav-link-admin:hover {
    background: linear-gradient(135deg, #2E2A72 0%, #4338CA 100%) !important;
    border-color: #FBBF24 !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 22px rgba(49, 46, 129, 0.35) !important;
}
.profile-nav-link-admin .nav-label {
    color: #FCD34D !important;
}
.profile-nav-link-admin .nav-desc {
    color: rgba(252, 211, 77, 0.75) !important;
}

.nav-pulse-dot {
    width: 8px;
    height: 8px;
    background: #10B981;
    border-radius: 50%;
    display: inline-block;
    animation: pulseDot 2s infinite ease-in-out;
}
@keyframes pulseDot {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.3); opacity: 0.7; }
}
.cat-filter-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 0.65rem 1.25rem !important;
    border-radius: 999px !important;
    font-size: 0.88rem !important;
    font-weight: 700 !important;
    color: var(--text-secondary) !important;
    background: var(--bg-card) !important;
    border: 1.5px solid var(--border-color) !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03) !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    text-decoration: none !important;
}
.cat-filter-btn:hover {
    color: var(--primary-coral) !important;
    border-color: rgba(255, 117, 86, 0.45) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 16px rgba(255, 117, 86, 0.18) !important;
    background: #FFFFFF !important;
}
.cat-filter-btn.active {
    background: linear-gradient(135deg, #FF7556 0%, #FF533D 100%) !important;
    color: #FFFFFF !important;
    border-color: transparent !important;
    box-shadow: 0 6px 18px rgba(255, 117, 86, 0.35) !important;
    transform: translateY(-1px) !important;
}
.btn-primary {
    background: linear-gradient(135deg, #FF7556 0%, #FF533D 100%) !important;
    color: #FFFFFF !important;
    border: none !important;
    border-radius: 12px !important;
    font-weight: 800 !important;
    box-shadow: 0 4px 14px rgba(255, 117, 86, 0.28) !important;
    transition: all 0.25s ease !important;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #FF533D 0%, #E03E2D 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 22px rgba(255, 117, 86, 0.4) !important;
}
.btn-secondary {
    background: #F8FAFC !important;
    color: #334155 !important;
    border: 1.5px solid #CBD5E1 !important;
    border-radius: 12px !important;
    font-weight: 700 !important;
    transition: all 0.25s ease !important;
}
.btn-secondary:hover {
    background: #EDF2F7 !important;
    border-color: #94A3B8 !important;
    color: #0F172A !important;
    transform: translateY(-2px) !important;
}
.vaccine-filter-chip {
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 800;
    border: 1.5px solid #CBD5E1;
    background: #FFFFFF;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s ease;
}
.vaccine-filter-chip:hover {
    border-color: #10B981;
    color: #059669;
}
.vaccine-filter-chip.active {
    background: #10B981;
    color: #FFFFFF;
    border-color: #10B981;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}
.cat-vaccine-item-card:hover {
    border-color: #10B981 !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.12) !important;
}
.cat-vaccine-item-card.selected-cat-card {
    border-color: #10B981 !important;
    background: #F0FDF4 !important;
    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.18) !important;
}
.cal-day-cell {
    min-height: 44px;
    padding: 4px 2px;
    border-radius: 12px;
    border: 1.5px solid transparent;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    font-size: 0.82rem;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    background: #FAFAFA;
}
.cal-day-cell:hover {
    background: #F1F5F9;
    border-color: #CBD5E1;
    transform: translateY(-1px);
}
.cal-day-cell.other-month {
    color: #CBD5E1;
    background: transparent;
}
.cal-day-cell.has-event {
    border: 1.5px solid #10B981 !important;
    background: #F0FDF4 !important;
    color: #065F46 !important;
    font-weight: 800 !important;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.15) !important;
}
.cal-day-cell.today {
    border-color: #FF7556 !important;
    background: rgba(255, 117, 86, 0.08) !important;
    color: var(--primary-coral) !important;
    font-weight: 900 !important;
}
.cal-day-cell.selected-day {
    background: linear-gradient(135deg, #059669 0%, #10B981 100%) !important;
    color: #FFFFFF !important;
    border-color: #047857 !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35) !important;
    transform: scale(1.05);
    z-index: 2;
}
.cal-event-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    margin-top: 2px;
    box-shadow: 0 0 4px rgba(0,0,0,0.25);
    border: 1px solid #FFFFFF;
}
@media (max-width: 860px) {
    .vaccine-twocol-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
function switchProfileTab(tabName, btnElement) {
    // Hide all panels
    document.querySelectorAll('.profile-tab-panel').forEach(p => p.style.display = 'none');
    
    // Show chosen panel
    const target = document.getElementById('tab-' + tabName);
    if (target) target.style.display = 'block';

    // Update active button state
    document.querySelectorAll('.profile-nav-btn, .cat-filter-btn').forEach(b => b.classList.remove('active'));
    
    if (btnElement) {
        btnElement.classList.add('active');
    } else {
        const btn = document.getElementById('btn-tab-' + tabName);
        if (btn) btn.classList.add('active');
    }

    if (tabName === 'vaccine') {
        renderCalendar();
    }

    // Update URL query string without reload
    const isStatic = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');
    const base = isStatic ? 'profile.html' : 'profile.php';
    window.history.replaceState(null, null, base + '?tab=' + tabName);
}

function copyCouponCode(code, btn) {
    navigator.clipboard.writeText(code).then(() => {
        const origText = btn.innerHTML;
        btn.innerHTML = 'คัดลอกแล้ว! ✓';
        btn.style.background = '#10B981';
        btn.style.color = '#FFFFFF';
        btn.style.borderColor = '#10B981';
        setTimeout(() => {
            btn.innerHTML = origText;
            btn.style.background = '';
            btn.style.color = '';
            btn.style.borderColor = '';
        }, 2000);
    }).catch(() => {
        alert('คูปองของคุณคือ: ' + code);
    });
}

// ================= VACCINE CALENDAR & FILTER LOGIC =================
const standardSchedulePlan = [
    { id: 'vac_1', ageWeeks: 8, title: 'วัคซีนรวมแมว (FPLV/FHV/FCV) เข็ม 1', icon: '💉', color: '#3B82F6' },
    { id: 'vac_2', ageWeeks: 9, title: 'ถ่ายพยาธิ & หยอดเห็บหมัด Spot-on', icon: '💊', color: '#10B981' },
    { id: 'vac_3', ageWeeks: 12, title: 'วัคซีนรวม เข็ม 2 + ลิวคีเมีย เข็ม 1', icon: '🛡️', color: '#8B5CF6' },
    { id: 'vac_4', ageWeeks: 16, title: 'วัคซีนพิษสุนัขบ้า + ลิวคีเมีย เข็ม 2', icon: '👑', color: '#EA580C' },
    { id: 'vac_5', ageWeeks: 24, title: 'ตรวจสุขภาพ 6 เดือน & วางแผนทำหมัน', icon: '🩺', color: '#059669' },
    { id: 'vac_6', ageWeeks: 52, title: 'วัคซีนรวม + พิษสุนัขบ้า กระตุ้นประจำปี', icon: '🎂', color: '#D97706' }
];

let calDate = new Date();
let selectedCatFilter = 'all';
let selectedCalCat = null;
let selectedCalDayStr = null;

function getCatVaccineSchedule(catName, breed, ageStr) {
    let monthsAgo = 2.5;
    if (ageStr) {
        if (ageStr.includes('3')) monthsAgo = 3;
        else if (ageStr.includes('2.5')) monthsAgo = 2.5;
        else if (ageStr.includes('2')) monthsAgo = 2;
        else if (ageStr.includes('4')) monthsAgo = 4;
    }
    const catDob = new Date();
    catDob.setDate(catDob.getDate() - Math.round(monthsAgo * 30.5));

    const savedChecks = JSON.parse(localStorage.getItem(`cat_health_${catName}`) || '{}');

    return standardSchedulePlan.map(item => {
        const targetDate = new Date(catDob);
        targetDate.setDate(targetDate.getDate() + (item.ageWeeks * 7));
        const dateStr = targetDate.toISOString().split('T')[0];
        const isDone = savedChecks[item.id] === true;
        const isPast = targetDate < new Date();
        let status = isDone ? 'completed' : (isPast ? 'due' : 'pending');

        return {
            ...item,
            catName,
            breed,
            targetDate,
            dateStr,
            isDone,
            status
        };
    });
}

function getAllCatsList() {
    const cards = document.querySelectorAll('.cat-vaccine-item-card');
    const list = [];
    cards.forEach(card => {
        list.push({
            name: card.getAttribute('data-cat-name') || 'น้องแมว',
            breed: card.getAttribute('data-cat-breed') || 'สายพันธุ์แท้',
            image: card.getAttribute('data-cat-image') || 'cat_british.jpg',
            age: card.getAttribute('data-cat-age') || '2.5 เดือน',
            element: card
        });
    });
    return list;
}

function filterVaccineCats(statusFilter, btn) {
    selectedCatFilter = statusFilter;
    document.querySelectorAll('.vaccine-filter-chip').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const cards = document.querySelectorAll('.cat-vaccine-item-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const catName = card.getAttribute('data-cat-name') || '';
        const breed = card.getAttribute('data-cat-breed') || '';
        const age = card.getAttribute('data-cat-age') || '';
        const schedule = getCatVaccineSchedule(catName, breed, age);

        const doneCount = schedule.filter(s => s.isDone).length;
        const hasDue = schedule.some(s => s.status === 'due');
        const hasPending = schedule.some(s => s.status === 'pending');

        let match = false;
        if (statusFilter === 'all') {
            match = true;
        } else if (statusFilter === 'completed' && doneCount === schedule.length) {
            match = true;
        } else if (statusFilter === 'due' && hasDue) {
            match = true;
        } else if (statusFilter === 'pending' && hasPending && doneCount < schedule.length) {
            match = true;
        }

        if (match) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const badge = document.getElementById('filter-count-badge');
    if (badge) {
        badge.textContent = `พบ ${visibleCount} ตัว`;
    }
}

function searchVaccineCats(query) {
    const cleanQ = query.trim().toLowerCase();
    const cards = document.querySelectorAll('.cat-vaccine-item-card');
    cards.forEach(card => {
        const name = (card.getAttribute('data-cat-name') || '').toLowerCase();
        const breed = (card.getAttribute('data-cat-breed') || '').toLowerCase();
        if (!cleanQ || name.includes(cleanQ) || breed.includes(cleanQ)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function selectCatForCalendar(catName, breed, image, cardElem) {
    selectedCalCat = catName;
    document.querySelectorAll('.cat-vaccine-item-card').forEach(c => c.classList.remove('selected-cat-card'));
    if (cardElem) cardElem.classList.add('selected-cat-card');

    document.getElementById('cal-selected-cat-name').textContent = `${catName} (${breed})`;
    renderCalendar();
}

function showAllCatsOnCalendar() {
    selectedCalCat = null;
    document.querySelectorAll('.cat-vaccine-item-card').forEach(c => c.classList.remove('selected-cat-card'));
    document.getElementById('cal-selected-cat-name').textContent = 'น้องแมวทุกตัว (All Cats)';
    renderCalendar();
}

function changeCalendarMonth(delta) {
    calDate.setMonth(calDate.getMonth() + delta);
    renderCalendar();
}

function goToTodayCalendar() {
    calDate = new Date();
    selectedCalDayStr = new Date().toISOString().split('T')[0];
    renderCalendar();
}

function renderCalendar() {
    const grid = document.getElementById('calendarGridDays');
    if (!grid) return;

    const year = calDate.getFullYear();
    const month = calDate.getMonth();

    const monthNamesThai = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    document.getElementById('calendarMonthTitle').textContent = `${monthNamesThai[month]} ${year + 543} (${year})`;

    // Gather all events for calendar
    const cats = getAllCatsList();
    const activeCats = selectedCalCat ? cats.filter(c => c.name === selectedCalCat) : cats;
    
    const eventsMap = {}; // dateStr -> array of events
    activeCats.forEach(cat => {
        const sched = getCatVaccineSchedule(cat.name, cat.breed, cat.age);
        sched.forEach(item => {
            if (!eventsMap[item.dateStr]) eventsMap[item.dateStr] = [];
            eventsMap[item.dateStr].push(item);
        });
    });

    const firstDayIndex = new Date(year, month, 1).getDay();
    const totalDaysInMonth = new Date(year, month + 1, 0).getDate();
    const prevMonthDays = new Date(year, month, 0).getDate();

    const todayStr = new Date().toISOString().split('T')[0];
    if (!selectedCalDayStr) selectedCalDayStr = todayStr;

    let html = '';

    // Previous month filler days
    for (let i = firstDayIndex - 1; i >= 0; i--) {
        const d = prevMonthDays - i;
        html += `<div class="cal-day-cell other-month"><span>${d}</span></div>`;
    }

    // Days of current month
    for (let day = 1; day <= totalDaysInMonth; day++) {
        const dStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayEvents = eventsMap[dStr] || [];
        const isToday = dStr === todayStr;
        const isSelected = dStr === selectedCalDayStr;
        const hasEvents = dayEvents.length > 0;

        let dotsHtml = '';
        if (hasEvents) {
            dotsHtml = `
                <div style="display: flex; gap: 2px; justify-content: center; margin-top: 3px; flex-wrap: wrap;">
                    ${dayEvents.slice(0, 3).map(ev => `<span class="cal-event-dot" style="background: ${ev.color};" title="${ev.title}"></span>`).join('')}
                </div>
            `;
        }

        html += `
            <div class="cal-day-cell ${hasEvents ? 'has-event' : ''} ${isToday ? 'today' : ''} ${isSelected ? 'selected-day' : ''}" 
                 onclick="selectCalendarDate('${dStr}')" 
                 title="${hasEvents ? dayEvents.length + ' นัดหมาย: ' + dayEvents.map(e => e.title).join(', ') : ''}">
                <span style="display: inline-block; line-height: 1;">${day}</span>
                ${dotsHtml}
            </div>
        `;
    }

    grid.innerHTML = html;

    renderCalendarEventsDetails(eventsMap);
}

function selectCalendarDate(dateStr) {
    selectedCalDayStr = dateStr;
    renderCalendar();
}

function renderCalendarEventsDetails(eventsMap) {
    const container = document.getElementById('calendarEventsList');
    const badge = document.getElementById('appointmentCountBadge');
    const label = document.getElementById('selectedDateLabel');
    if (!container) return;

    const dateObj = new Date(selectedCalDayStr);
    const dateFormatted = dateObj.toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric', weekday: 'short' });
    if (label) {
        label.innerHTML = `📋 นัดหมายวันที่: <span style="background: #FFFFFF; color: #047857; font-weight: 900; padding: 0.2rem 0.7rem; border-radius: 8px; border: 1.5px solid #6EE7B7; box-shadow: 0 2px 4px rgba(0,0,0,0.04); display: inline-flex; align-items: center; gap: 4px;">📅 ${dateFormatted}</span>`;
    }

    const dayEvents = eventsMap[selectedCalDayStr] || [];
    if (badge) badge.textContent = `${dayEvents.length} รายการ`;

    if (dayEvents.length === 0) {
        // Show upcoming in current month or empty guide
        const allMonthEvents = [];
        Object.keys(eventsMap).sort().forEach(k => {
            if (k.startsWith(selectedCalDayStr.substring(0, 7))) {
                eventsMap[k].forEach(ev => allMonthEvents.push(ev));
            }
        });

        if (allMonthEvents.length > 0) {
            container.innerHTML = `
                <div style="font-size: 0.8rem; color: #64748B; margin-bottom: 4px; font-weight: 700;">
                    ไม่มีนัดในวันนี้ แต่มีนัดหมายอื่นๆ ในเดือนนี้:
                </div>
                ${allMonthEvents.slice(0, 3).map(ev => renderSingleEventCard(ev)).join('')}
            `;
        } else {
            container.innerHTML = `
                <div style="text-align: center; padding: 1.4rem; color: #64748B; font-size: 0.85rem; background: #F8FAFC; border-radius: 12px; border: 1px dashed #CBD5E1;">
                    ✨ ไม่มีนัดหมายฉีดวัคซีนในวันที่เลือก<br>
                    <span style="font-size: 0.75rem; color: #94A3B8;">(คลิกเลือกวันที่ที่มีกรอบสีเขียวและจุดสีบนปฏิทินเพื่อดูรายละเอียด)</span>
                </div>
            `;
        }
    } else {
        container.innerHTML = dayEvents.map(ev => renderSingleEventCard(ev)).join('');
    }
}

function renderSingleEventCard(ev) {
    const isStatic = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');
    const trackerUrl = (isStatic ? 'vaccine_reminder.html' : 'vaccine_reminder.php') + `?cat_name=${encodeURIComponent(ev.catName)}&breed=${encodeURIComponent(ev.breed)}`;

    const eventColor = ev.color || '#10B981';

    return `
        <div style="background: #FFFFFF; border: 1.5px solid ${ev.isDone ? '#A7F3D0' : eventColor}; border-left: 5.5px solid ${eventColor}; border-radius: 14px; padding: 0.85rem 1rem; box-shadow: 0 3px 10px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between; gap: 10px; transition: transform 0.2s ease;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.4rem; background: ${eventColor}18; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: 1.5px solid ${eventColor}35; flex-shrink: 0;">
                    ${ev.icon}
                </span>
                <div>
                    <strong style="font-size: 0.9rem; color: #1E293B; display: block; margin-bottom: 2px;">
                        ${ev.title}
                    </strong>
                    <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                        <span style="font-size: 0.72rem; color: #475569; font-weight: 700;">
                            🐾 ${ev.catName} (${ev.breed})
                        </span>
                        <span style="font-size: 0.7rem; background: ${eventColor}15; color: ${eventColor}; font-weight: 800; padding: 1px 7px; border-radius: 6px; border: 1px solid ${eventColor}30;">
                            📅 ${ev.dateStr}
                        </span>
                    </div>
                </div>
            </div>
            <div style="text-align: right; flex-shrink: 0;">
                <a href="${trackerUrl}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 8px; color: #059669; background: #ECFDF5; border-color: #A7F3D0; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    บันทึก ✓
                </a>
            </div>
        </div>
    `;
}

// Client-side hydration for Profile & Lucky Wheel Rewards & Vaccine Cats
document.addEventListener('DOMContentLoaded', () => {
    const isStatic = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');
    
    // Adjust links if static
    if (isStatic) {
        const wheelLink = document.getElementById('coupons-wheel-link');
        if (wheelLink) wheelLink.href = 'lucky_wheel.html';
        const wheelEmptyLink = document.getElementById('coupons-wheel-empty-link');
        if (wheelEmptyLink) wheelEmptyLink.href = 'lucky_wheel.html';
        document.querySelectorAll('.coupon-apply-btn').forEach(btn => {
            const code = btn.getAttribute('data-code');
            if (code) btn.href = 'cart.html?apply_coupon=' + encodeURIComponent(code);
        });
        document.querySelectorAll('.btn-vaccine-link').forEach(btn => {
            if (btn.href) btn.href = btn.href.replace('vaccine_reminder.php', 'vaccine_reminder.html');
        });
    }

    // 1. Sync User Profile from LocalStorage
    try {
        const loggedUser = JSON.parse(localStorage.getItem('cat_shop_logged_user') || 'null');
        if (loggedUser) {
            if (loggedUser.fullname) {
                const fn = document.getElementById('profile-fullname-text');
                if (fn) fn.textContent = loggedUser.fullname;
            }
            if (loggedUser.username) {
                const un = document.getElementById('profile-username-text');
                if (un) un.textContent = loggedUser.username;
            }
            if (loggedUser.email) {
                const em = document.getElementById('profile-email-text');
                if (em) em.textContent = loggedUser.email;
            }
            if (loggedUser.phone) {
                const ph = document.getElementById('profile-phone-text');
                if (ph) ph.textContent = loggedUser.phone;
            }
            if (loggedUser.avatar) {
                const av = document.getElementById('header-avatar-preview');
                if (av) av.src = loggedUser.avatar;
            }
            if (loggedUser.paw_points !== undefined) {
                const pt = document.getElementById('profile-points-text');
                if (pt) pt.textContent = Number(loggedUser.paw_points).toLocaleString();
            }
        }
    } catch(e) {}

    // 2. Sync Lucky Wheel Rewards from LocalStorage
    try {
        const rewards = JSON.parse(localStorage.getItem('cat_shop_my_rewards') || '[]');
        const badge = document.getElementById('rewards-counter-badge');
        const countPill = document.getElementById('lucky-count-pill');
        const emptyBox = document.getElementById('lucky-rewards-empty');
        const gridBox = document.getElementById('lucky-rewards-grid');

        if (badge) badge.textContent = rewards.length;
        if (countPill) countPill.textContent = rewards.length + ' รายการ';

        if (rewards && rewards.length > 0 && gridBox && emptyBox) {
            emptyBox.style.display = 'none';
            gridBox.style.display = 'grid';
            gridBox.innerHTML = '';

            rewards.forEach(rew => {
                const card = document.createElement('div');
                card.style.cssText = 'background: linear-gradient(135deg, rgba(254, 243, 199, 0.5) 0%, rgba(253, 230, 138, 0.4) 100%); border: 1.5px solid #F59E0B; border-radius: var(--radius-md); padding: 1.2rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: var(--shadow-sm); position: relative;';
                
                const cartHref = isStatic 
                    ? 'cart.html?apply_coupon=' + encodeURIComponent(rew.code || '')
                    : 'cart.php?apply_coupon=' + encodeURIComponent(rew.code || '');

                card.innerHTML = `
                    <div>
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 0.6rem;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 2rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">${rew.icon || '🎁'}</span>
                                <div>
                                    <h5 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0;">${rew.title}</h5>
                                    <span style="font-size: 0.72rem; color: #B45309; font-weight: 700;">🎡 รางวัลจากวงล้อนำโชค</span>
                                </div>
                            </div>
                            <span style="background: #D97706; color: #FFFFFF; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 999px; white-space: nowrap;">WON</span>
                        </div>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.8rem; line-height: 1.4;">
                            ${rew.desc}
                        </p>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.8rem;">
                            🕒 ได้รับเมื่อ: ${rew.date_formatted || new Date(rew.date).toLocaleDateString('th-TH')}
                        </div>
                    </div>
                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <div>
                            <span style="font-size: 0.7rem; color: var(--text-muted); display: block;">รหัสรับสิทธิ์:</span>
                            <strong style="font-family: 'Outfit'; font-size: 1.05rem; color: #D97706; letter-spacing: 0.8px;">${rew.code || 'LUCKYPRIZE'}</strong>
                        </div>
                        <div style="display: flex; gap: 6px;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="copyCouponCode('${rew.code || ''}', this)" style="padding: 4px 10px; font-size: 0.75rem;">คัดลอก 📋</button>
                            <a href="${cartHref}" class="btn btn-primary btn-sm" style="padding: 4px 10px; font-size: 0.75rem;">ใช้เลย 🛒</a>
                        </div>
                    </div>
                `;
                gridBox.appendChild(card);
            });
        }
    } catch(e) {}

    // 3. Sync Vaccine Cats from LocalStorage if available
    try {
        const storedOrders = JSON.parse(localStorage.getItem('cat_shop_my_orders') || '[]');
        const vaccineContainer = document.getElementById('profile-vaccine-cats-container');
        if (storedOrders && storedOrders.length > 0 && vaccineContainer) {
            const localCats = [];
            storedOrders.forEach(ord => {
                if (ord.items && Array.isArray(ord.items)) {
                    ord.items.forEach(it => {
                        localCats.push({
                            id: it.id,
                            name: it.name || 'น้องแมว',
                            breed: it.breed || 'สายพันธุ์แท้',
                            image: it.image || 'cat_british.jpg',
                            age: it.age || '2.5 เดือน',
                            gender: it.gender || 'ไม่ระบุ'
                        });
                    });
                }
            });

            if (localCats.length > 0) {
                const targetFile = isStatic ? 'vaccine_reminder.html' : 'vaccine_reminder.php';
                vaccineContainer.innerHTML = localCats.map((c, idx) => `
                    <div class="cat-vaccine-item-card ${idx === 0 ? 'selected-cat-card' : ''}" 
                         data-cat-name="${c.name}"
                         data-cat-breed="${c.breed}"
                         data-cat-image="${c.image}"
                         data-cat-age="${c.age}"
                         onclick="selectCatForCalendar('${c.name.replace(/'/g, "\\'")}', '${c.breed.replace(/'/g, "\\'")}', '${c.image.replace(/'/g, "\\'")}', this)"
                         style="background: #FFFFFF; border: 2px solid ${idx === 0 ? '#10B981' : '#E2E8F0'}; border-radius: 16px; padding: 1.1rem; box-shadow: 0 4px 14px rgba(0,0,0,0.03); cursor: pointer; transition: all 0.25s ease; position: relative;">
                        
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 0.8rem;">
                            <div style="display: flex; align-items: center; gap: 12px; overflow: hidden;">
                                <img src="assets/images/${c.image}" alt="${c.name}" 
                                     style="width: 60px; height: 60px; border-radius: 14px; object-fit: cover; border: 2px solid #10B981; flex-shrink: 0;">
                                <div style="overflow: hidden;">
                                    <strong style="font-size: 1.05rem; color: #1E293B; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        ${c.name}
                                    </strong>
                                    <span style="display: inline-block; font-size: 0.72rem; background: #D1FAE5; color: #065F46; font-weight: 800; padding: 2px 7px; border-radius: 999px; margin-top: 2px;">
                                        ${c.breed}
                                    </span>
                                </div>
                            </div>
                            <span class="cat-status-badge" style="font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 999px; background: #EFF6FF; color: #1E40AF; white-space: nowrap;">
                                ⏳ ติดตามวัคซีน
                            </span>
                        </div>

                        <div style="background: #F8FAFC; border-radius: 10px; padding: 0.6rem 0.8rem; margin-bottom: 0.8rem; border: 1px solid #E2E8F0;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 4px;">
                                <span>ความคืบหน้าการฉีด:</span>
                                <span class="cat-progress-text" style="color: #059669;">2/6 เข็ม (33%)</span>
                            </div>
                            <div style="width: 100%; height: 6px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                                <div class="cat-progress-bar" style="width: 33%; height: 100%; background: linear-gradient(90deg, #10B981, #059669); border-radius: 999px;"></div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span style="font-size: 0.75rem; color: #059669; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                                📅 คลิกเพื่อดูนัดบนปฏิทิน
                            </span>
                            <a href="${targetFile}?cat_name=${encodeURIComponent(c.name)}&breed=${encodeURIComponent(c.breed)}&image=${encodeURIComponent(c.image)}" 
                               onclick="event.stopPropagation();"
                               class="btn btn-secondary btn-sm btn-vaccine-link" 
                               style="font-size: 0.75rem; font-weight: 800; padding: 3px 9px; border-radius: 8px; color: #059669; background: #ECFDF5; border-color: #A7F3D0; text-decoration: none;">
                                💉 สมุดวัคซีน ➔
                            </a>
                        </div>
                    </div>
                `).join('');
            }
        }
    // 4. Hydrate Orders from localStorage (for client-side/test orders)
    try {
        const localOrders = JSON.parse(localStorage.getItem('cat_shop_orders') || '[]');
        if (localOrders.length > 0) {
            const orderContainer = document.querySelector('#tab-orders .checkout-block');
            const emptyState = orderContainer ? orderContainer.querySelector('div[style*="text-align: center"]') : null;
            let ordersListDiv = orderContainer ? orderContainer.querySelector('div[style*="flex-direction: column"]') : null;

            if (emptyState) {
                emptyState.remove();
            }

            if (!ordersListDiv && orderContainer) {
                ordersListDiv = document.createElement('div');
                ordersListDiv.style.display = 'flex';
                ordersListDiv.style.flexDirection = 'column';
                ordersListDiv.style.gap = '1.5rem';
                orderContainer.appendChild(ordersListDiv);
            }

            const isStatic = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');
            const trackingTarget = isStatic ? 'tracking.html' : 'tracking.php';
            const vaccineTarget = isStatic ? 'vaccine_reminder.html' : 'vaccine_reminder.php';

            localOrders.forEach(ord => {
                const orderKey = 'order-' + (ord.order_id || '').replace(/[^a-zA-Z0-9_-]/g, '');
                const existing = document.getElementById(orderKey);
                if (!existing && ordersListDiv) {
                    const trkId = ord.tracking_id || ('TRACK-TH-' + (ord.order_id || '9999').replace(/[^a-zA-Z0-9]/g, ''));
                    const grandTotal = parseFloat(ord.total || 0);
                    const items = ord.items || [];
                    const itemsCount = items.length;
                    const itemsHtml = items.map(it => `
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.6rem 0; border-bottom: 1px dashed var(--border-color); flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <img src="assets/images/${it.image || 'cat_persian.jpg'}" alt="${it.name || 'น้องแมว'}" 
                                     style="width: 58px; height: 58px; border-radius: 10px; object-fit: cover; border: 1.5px solid var(--border-color);" onerror="this.src='assets/images/logo.png'">
                                <div>
                                    <strong style="color: var(--text-main); font-size: 0.95rem; display: block;">${it.name || 'น้องแมว'}</strong>
                                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">
                                        ${it.breed || ''} • เพศ: ${it.gender || 'ไม่ระบุ'} (x${it.qty || 1})
                                    </div>
                                    <a href="${vaccineTarget}?cat_name=${encodeURIComponent(it.name || '')}&breed=${encodeURIComponent(it.breed || '')}&image=${encodeURIComponent(it.image || '')}" 
                                       class="btn-vaccine-link" 
                                       style="font-size: 0.78rem; font-weight: 800; color: #059669; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 2px 8px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                        💉 ดูตารางวัคซีนน้องตัวนี้ ➔
                                    </a>
                                </div>
                            </div>
                            <span style="font-weight: 700; color: var(--primary-coral); font-family: 'Outfit'; font-size: 1.05rem;">
                                ${(it.price * (it.qty || 1)).toLocaleString('th-TH')} ฿
                            </span>
                        </div>
                    `).join('');

                    const orderCard = document.createElement('div');
                    orderCard.id = orderKey;
                    orderCard.style.cssText = 'background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm); transition: var(--transition);';
                    orderCard.innerHTML = `
                        <div style="background: var(--bg-card-subtle); padding: 1rem 1.4rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.8rem;">
                            <div>
                                <span style="font-size: 0.78rem; color: var(--text-muted); display: block;">รหัสคำสั่งจอง:</span>
                                <strong style="font-family: 'Outfit'; font-size: 1.1rem; color: var(--primary-coral);">${ord.order_id}</strong>
                                <div style="margin-top: 4px;">
                                    <span style="font-size: 0.78rem; font-weight: 800; background: #EFF6FF; color: #1D4ED8; padding: 2px 8px; border-radius: 6px; border: 1px solid #BFDBFE; display: inline-flex; align-items: center; gap: 4px; font-family: 'Outfit', sans-serif;">
                                        🚚 รหัส Tracking: ${trkId}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <span style="font-size: 0.78rem; color: var(--text-muted); display: block;">วันที่ทำรายการ:</span>
                                <span style="font-size: 0.88rem; font-weight: 600; color: var(--text-main);">${ord.created_at ? new Date(ord.created_at).toLocaleString('th-TH') : 'ล่าสุด'}</span>
                            </div>
                            <div>
                                <span style="font-size: 0.78rem; color: var(--text-muted); display: block;">สถานะ:</span>
                                <span style="background: var(--accent-mint-soft); color: #065F46; padding: 0.2rem 0.7rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.3);">
                                    ✓ ชำระเงินแล้ว / เตรียมจัดส่ง
                                </span>
                            </div>
                            <div>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    <a href="${trackingTarget}?track_id=${encodeURIComponent(trkId)}&order_id=${encodeURIComponent(ord.order_id)}" 
                                       class="btn btn-primary btn-sm" 
                                       style="padding: 0.38rem 0.85rem; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                                        📍 ติดตามส่งมอบ
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 1.2rem 1.4rem;">
                            <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.8rem;">
                                น้องแมวในรายการนี้ (จำนวน: ${itemsCount} ตัว)
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                                ${itemsHtml}
                            </div>
                            <div style="margin-top: 1.2rem; padding-top: 1rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
                                <div>
                                    <span style="font-size: 0.78rem; color: var(--text-muted); display: block;">ช่องทางชำระเงินที่ใช้:</span>
                                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary);">ชำระเงินออนไลน์ / โอนเงินผ่านระบบ</span>
                                </div>
                                <div style="text-align: right;">
                                    <span style="font-size: 0.82rem; color: var(--text-muted);">ยอดรวมสุทธิ:</span>
                                    <span style="font-size: 1.3rem; font-weight: 800; color: var(--primary-coral); font-family: 'Outfit'; display: block;">
                                        ${grandTotal.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ฿
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;
                    ordersListDiv.prepend(orderCard);
                }
            });

            // Update badge counts
            const allRenderedCards = ordersListDiv.querySelectorAll(':scope > div');
            const badgeCount = document.getElementById('orders-tab-count');
            const headerCount = document.querySelector('#tab-orders .checkout-block-title span:last-child');
            if (badgeCount) badgeCount.textContent = allRenderedCards.length;
            if (headerCount) headerCount.textContent = `(พบ ${allRenderedCards.length} รายการ)`;
        }
    } catch(e) {
        console.error('Error hydrating profile orders:', e);
    }

    // 5. Initialize Calendar
    renderCalendar();

    // 6. Handle initial tab selection from URL params
    const urlParams = new URLSearchParams(window.location.search);
    const initialTab = urlParams.get('tab');
    if (initialTab && document.getElementById('tab-' + initialTab)) {
        switchProfileTab(initialTab, document.getElementById('btn-tab-' + initialTab));
    }
});
</script>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
