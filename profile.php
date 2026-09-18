<?php
require_once __DIR__ . '/data.php';

// Check if user is logged in
if (!isUserLoggedIn()) {
    header("Location: login.php?redirect=profile.php");
    exit;
}

$currUser = getCurrentUser();
$success_msg = "";
$error_msg = "";
$active_tab = $_GET['tab'] ?? 'profile';

// Handle Profile Updates (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
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
foreach ($user_orders as $ord) {
    $total_cats_adopted += $ord['cat_count'] ?? 0;
    $total_spent += $ord['total'] ?? 0;
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
                <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.2rem;">
                    <?php echo htmlspecialchars($currUser['fullname']); ?>
                </h2>
                <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                    @<?php echo htmlspecialchars($currUser['username']); ?> &bull; <?php echo htmlspecialchars($currUser['email']); ?>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <span style="background: var(--primary-coral-soft); color: var(--primary-coral); font-size: 0.78rem; font-weight: 700; padding: 0.25rem 0.75rem; border-radius: 9999px; border: 1px solid rgba(255, 117, 86, 0.3);">
                        🌟 สมาชิกทางการ (ส่วนลดพิเศษ 5%)
                    </span>
                    <span style="background: var(--accent-amber-soft); color: #B45309; font-size: 0.78rem; font-weight: 600; padding: 0.25rem 0.75rem; border-radius: 9999px;">
                        📞 <?php echo htmlspecialchars($currUser['phone']); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Summary Counters -->
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.8rem 1.2rem; text-align: center; min-width: 110px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">รับเลี้ยงน้องแมว</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary-coral); font-family: 'Outfit';">
                    <?php echo $total_cats_adopted; ?> ตัว
                </div>
            </div>
            <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.8rem 1.2rem; text-align: center; min-width: 120px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">ยอดที่ชำระแล้ว</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--accent-mint); font-family: 'Outfit';">
                    <?php echo number_format($total_spent); ?> ฿
                </div>
            </div>
            <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.8rem 1.2rem; text-align: center; min-width: 100px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">คำสั่งจอง</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); font-family: 'Outfit';">
                    <?php echo count($user_orders); ?> ครั้ง
                </div>
            </div>

            <!-- Logout Button -->
            <a href="logout.php" 
               class="btn btn-secondary" 
               onclick="return confirm('คุณต้องการออกจากระบบ Cat Shop ใช่หรือไม่?');" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.3rem; border-color: rgba(239, 68, 68, 0.4); color: #EF4444; font-weight: 700; background: rgba(239, 68, 68, 0.06); border-radius: var(--radius-md); text-decoration: none;" 
               title="กดเพื่อออกจากระบบ">
                ออกจากระบบ 🚪
            </a>
        </div>
    </div>

    <!-- Navigation Tabs for Profile -->
    <div style="display: flex; gap: 0.6rem; margin-bottom: 1.8rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.8rem; flex-wrap: wrap;">
        <button type="button" 
                class="cat-filter-btn <?php echo $active_tab === 'profile' ? 'active' : ''; ?>" 
                onclick="switchProfileTab('profile', this)">
            👤 ข้อมูลส่วนตัว & รูปโปรไฟล์
        </button>
        <button type="button" 
                class="cat-filter-btn <?php echo $active_tab === 'payment' ? 'active' : ''; ?>" 
                onclick="switchProfileTab('payment', this)">
            💳 ข้อมูลการชำระเงิน & ที่อยู่จัดส่ง
        </button>
        <button type="button" 
                class="cat-filter-btn <?php echo $active_tab === 'orders' ? 'active' : ''; ?>" 
                onclick="switchProfileTab('orders', this)">
            📦 ประวัติการสั่งซื้อ & แมวที่ชำระแล้ว (<?php echo count($user_orders); ?>)
        </button>
        <button type="button" 
                class="cat-filter-btn <?php echo $active_tab === 'points' ? 'active' : ''; ?>" 
                onclick="switchProfileTab('points', this)">
            🎁 สะสมแต้ม Paw Points & Tiers
        </button>
        <button type="button" 
                class="cat-filter-btn <?php echo $active_tab === 'inbox' ? 'active' : ''; ?>" 
                onclick="switchProfileTab('inbox', this)">
            📬 กล่องจดหมาย & ข่าวสารร้านค้า (<?php echo count($user_messages); ?>)
        </button>
        <a href="tracking.php" class="cat-filter-btn" style="text-decoration: none; color: var(--primary-coral); border-color: var(--primary-coral);">
            📍 ติดตามการจัดส่งสด
        </a>
        <?php if (isAdmin()): ?>
            <a href="admin.php" class="cat-filter-btn" style="background: #1E293B; color: #FBBF24; border-color: #F59E0B; text-decoration: none;" title="เปิดหน้าจัดการระบบ">
                🛠️ จัดการระบบ (Admin Panel) &rarr;
            </a>
        <?php endif; ?>
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
                            ['src' => 'assets/images/cat_scottishfold.jpg', 'label' => 'สก็อตติช'],
                            ['src' => 'assets/images/cat_persian.jpg', 'label' => 'เปอร์เซีย'],
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
                                    <div style="display: flex; gap: 6px;">
                                        <a href="tracking.php?track=<?php echo urlencode($order['tracking_id'] ?? $order['order_id']); ?>" 
                                           class="btn btn-primary btn-sm" 
                                           style="padding: 0.35rem 0.75rem; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;" 
                                           title="ติดตามสถานะการส่งมอบสัตว์เลี้ยงแบบเรียลไทม์">
                                            📍 ติดตามส่งมอบ
                                        </a>
                                        <a href="order_letter.php?id=<?php echo urlencode($order['order_id']); ?>" target="_blank" 
                                           class="btn btn-secondary btn-sm" 
                                           style="padding: 0.35rem 0.75rem; font-size: 0.8rem; font-weight: 700; color: #92400E; background: #FEF3C7; border-color: #FCD34D; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;" 
                                           title="เปิดดูจดหมายตอบรับและใบรับประกันอย่างเป็นทางการ">
                                            📜 จดหมายตอบรับ
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
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.5rem 0; border-bottom: 1px dashed var(--border-color);">
                                            <div style="display: flex; align-items: center; gap: 1rem;">
                                                <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" 
                                                     style="width: 55px; height: 55px; border-radius: 8px; object-fit: cover; border: 1.5px solid var(--border-color);">
                                                <div>
                                                    <strong style="color: var(--text-main); font-size: 0.95rem;"><?php echo $item['name']; ?></strong>
                                                    <div style="font-size: 0.8rem; color: var(--text-muted);">
                                                        <?php echo $item['breed']; ?> &bull; เพศ: <?php echo $item['gender']; ?> (x<?php echo $item['qty']; ?>)
                                                    </div>
                                                </div>
                                            </div>
                                            <span style="font-weight: 700; color: var(--primary-coral); font-family: 'Outfit';">
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
    <div id="tab-points" class="profile-tab-panel" style="<?php echo $active_tab === 'points' ? 'display: block;' : 'display: none;'; ?>">
        <div class="checkout-block">
            <h3 class="checkout-block-title" style="margin-bottom: 1.4rem;">
                <span>🎁 ระบบสะสมแต้ม Paw Points & ระดับสมาชิก (Loyalty Tiers)</span>
            </h3>

            <!-- Points Card -->
            <div style="background: linear-gradient(135deg, #FF6B4A 0%, #FF8E72 50%, #FFA885 100%); color: #FFFFFF; border-radius: var(--radius-lg); padding: 2.2rem; box-shadow: 0 10px 25px rgba(255,107,74,0.35); margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <span style="font-size: 0.85rem; font-weight: 700; background: rgba(255,255,255,0.25); padding: 4px 12px; border-radius: 999px;">🐾 PURRFECT REWARDS CLUB</span>
                        <h2 style="font-size: 2.4rem; font-weight: 800; margin: 0.6rem 0 0.2rem 0; font-family: 'Outfit';">
                            <?php echo number_format($currUser['paw_points'] ?? 150); ?> <span style="font-size: 1.2rem; font-weight: 600;">Paw Points</span>
                        </h2>
                        <p style="margin: 0; font-size: 0.9rem; opacity: 0.95;">
                            ทุกๆ ยอดสั่งซื้อ 100 บาท = 1 Paw Point • 100 พอยท์ แลกส่วนลดได้ 100 บาท
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.8rem; opacity: 0.9; display: block;">ระดับสมาชิกปัจจุบัน:</span>
                        <div style="background: #FFFFFF; color: #B45309; padding: 6px 16px; border-radius: 999px; font-weight: 800; font-size: 1rem; margin-top: 4px; display: inline-block; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            🥉 Bronze Paw (ลด 5%)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tiers Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.2rem; margin-bottom: 2rem;">
                <!-- Bronze -->
                <div style="background: var(--bg-card); border: 2px solid #E2E8F0; border-radius: var(--radius-md); padding: 1.4rem;">
                    <span style="font-size: 2rem; display: block; margin-bottom: 6px;">🥉</span>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">Bronze Paw</h4>
                    <span style="font-size: 0.78rem; color: var(--text-muted);">สำหรับสมาชิกทุกคน</span>
                    <ul style="margin: 1rem 0 0 0; padding-left: 1.2rem; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.7;">
                        <li>✓ ส่วนลด 5% ทุกรายการ</li>
                        <li>✓ รับข่าวสาร & ดีลลับก่อนใคร</li>
                        <li>✓ สะสมแต้มทุกการจอง</li>
                    </ul>
                </div>

                <!-- Silver -->
                <div style="background: #FFFBF5; border: 2px solid #F59E0B; border-radius: var(--radius-md); padding: 1.4rem; position: relative;">
                    <span style="position: absolute; top: 12px; right: 12px; background: #F59E0B; color: #FFF; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">เป้าหมายถัดไป</span>
                    <span style="font-size: 2rem; display: block; margin-bottom: 6px;">🥈</span>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: #92400E; margin: 0 0 4px 0;">Silver Paw</h4>
                    <span style="font-size: 0.78rem; color: var(--text-muted);">ยอดสะสมครบ 20,000 บาท</span>
                    <ul style="margin: 1rem 0 0 0; padding-left: 1.2rem; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.7;">
                        <li>✓ ส่วนลด 7% ทุกรายการ</li>
                        <li>✓ <strong>ฟรี! ค่าจัดส่ง Pet Taxi ทั่วไทย</strong></li>
                        <li>✓ แต้มคูณ 1.2 เท่า</li>
                    </ul>
                </div>

                <!-- Gold VIP -->
                <div style="background: #FFF7ED; border: 2px solid #EA580C; border-radius: var(--radius-md); padding: 1.4rem;">
                    <span style="font-size: 2rem; display: block; margin-bottom: 6px;">🥇</span>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: #C2410C; margin: 0 0 4px 0;">Gold Paw VIP</h4>
                    <span style="font-size: 0.78rem; color: var(--text-muted);">ยอดสะสมครบ 50,000 บาท</span>
                    <ul style="margin: 1rem 0 0 0; padding-left: 1.2rem; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.7;">
                        <li>✓ <strong>ส่วนลด 10% ตลอดชีพ</strong></li>
                        <li>✓ ฟรี! บริการตรวจสุขภาพประจำปี 1 ปี</li>
                        <li>✓ สายด่วนสัตวแพทย์ส่วนตัว 24 ชม.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

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
</div>

<script>
function switchProfileTab(tabName, btnElement) {
    // Hide all panels
    document.querySelectorAll('.profile-tab-panel').forEach(p => p.style.display = 'none');
    
    // Show chosen panel
    const target = document.getElementById('tab-' + tabName);
    if (target) target.style.display = 'block';

    // Update active button state
    document.querySelectorAll('.cat-filter-btn').forEach(b => b.classList.remove('active'));
    if (btnElement) btnElement.classList.add('active');

    // Update URL hash/query without reload
    window.history.replaceState(null, null, 'profile.php?tab=' + tabName);
}
</script>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
