<?php
require_once __DIR__ . '/data.php';

// If already logged in, redirect to home
if (isUserLoggedIn()) {
    header("Location: index.php");
    exit;
}

$errors = [];
$success_msg = "";

$fullname = "";
$username = "";
$email = "";
$phone = "";
$consent_email = true;
$consent_phone = false;
$consent_terms = false;

if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $consent_email = isset($_POST['consent_email']);
    $consent_phone = isset($_POST['consent_phone']);
    $consent_terms = isset($_POST['consent_terms']);

    // Validations
    if (empty($fullname)) {
        $errors[] = "กรุณากรอกชื่อ-นามสกุลของคุณ";
    }
    if (empty($username) || strlen($username) < 3) {
        $errors[] = "ชื่อผู้ใช้ต้องมีความยาวอย่างน้อย 3 ตัวอักษร";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "กรุณากรอกอีเมลให้ถูกต้อง (เช่น name@example.com)";
    }
    if (empty($phone) || !preg_match('/^[0-9\-\+]{9,15}$/', $phone)) {
        $errors[] = "กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (เช่น 0812345678)";
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร";
    }
    if ($password !== $confirm_password) {
        $errors[] = "รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน";
    }
    if (!$consent_terms) {
        $errors[] = "กรุณาทำเครื่องหมายยินยอมตามนโยบายความเป็นส่วนตัวและข้อกำหนดการใช้งาน";
    }

    if (empty($errors)) {
        $res = registerUser($fullname, $username, $email, $phone, $password, $consent_email, $consent_phone, $consent_terms);
        if ($res['success']) {
            // Redirect newly registered customer to the VIP Welcome Deal sales page
            header("Location: welcome_deal.php?registered=1");
            exit;
        } else {
            $errors[] = $res['message'];
        }
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon-circle">🐱</div>
            <h1 class="auth-title">สมัครสมาชิก Cat Shop</h1>
            <p class="auth-subtitle">
                ร่วมเป็นครอบครัวทาสแมว รับสิทธิพิเศษส่วนลดทันที 5% ทุกออเดอร์ 🐾
            </p>
            <div style="background: linear-gradient(135deg, #0B0F19 0%, #1E293B 100%); border: 1px solid #F59E0B; border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-top: 1rem; text-align: left; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                <span style="font-size: 1.5rem;">👑</span>
                <div style="font-size: 0.84rem; color: #F1F5F9; line-height: 1.45;">
                    <strong style="color: #FBBF24;">สิทธิพิเศษต้อนรับ VIP:</strong> สมัครแล้วระบบจะส่งอีเมล <strong>VIP Member Card</strong> และโค้ดลด 15% (<code style="color: #FCD34D;">WELCOME15</code>) เข้าอีเมลของคุณทันที!
                </div>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert-box alert-danger">
                <div>
                    <strong>⚠️ กรุณาตรวจสอบข้อมูล:</strong>
                    <ul style="margin-top: 0.3rem; margin-left: 1.2rem;">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" id="register-form">
            <div class="form-group">
                <label for="fullname">ชื่อ - นามสกุล *</label>
                <input type="text" id="fullname" name="fullname" class="form-control" 
                       placeholder="เช่น สมชาย ใจดี" value="<?php echo htmlspecialchars($fullname); ?>" required>
            </div>

            <div class="form-group">
                <label for="username">ชื่อผู้ใช้ (Username) *</label>
                <input type="text" id="username" name="username" class="form-control" 
                       placeholder="เช่น catlover99" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">อีเมล (Email) *</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="เช่น yourname@gmail.com" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">เบอร์โทรศัพท์ (Phone Number) *</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       placeholder="เช่น 0891234567" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="password">รหัสผ่าน *</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="อย่างน้อย 6 ตัวอักษร" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">ยืนยันรหัสผ่าน *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                           placeholder="พิมพ์รหัสผ่านซ้ำอีกครั้ง" required>
                </div>
            </div>

            <!-- Consent Section -->
            <div class="consent-box">
                <div class="consent-header">
                    <span>📋 การให้ความยินยอมและการคุ้มครองข้อมูล (Consent)</span>
                </div>

                <!-- Email Consent -->
                <label class="consent-item">
                    <input type="checkbox" name="consent_email" class="consent-checkbox" <?php echo $consent_email ? 'checked' : ''; ?>>
                    <div class="consent-text">
                        <strong>ยินยอมรับข่าวสารทางอีเมล:</strong> ข้าพเจ้ายินยอมให้ <strong>Cat Shop</strong> ส่งข้อมูลข่าวสาร โปรโมชั่นพิเศษ สายพันธุ์น้องแมวเข้าใหม่ และเกร็ดความรู้การดูแลสัตว์เลี้ยงผ่านทาง <u>อีเมล</u>
                    </div>
                </label>

                <!-- Phone Consent -->
                <label class="consent-item">
                    <input type="checkbox" name="consent_phone" class="consent-checkbox" <?php echo $consent_phone ? 'checked' : ''; ?>>
                    <div class="consent-text">
                        <strong>ยินยอมให้ติดต่อทางเบอร์โทรศัพท์:</strong> ข้าพเจ้ายินยอมให้เจ้าหน้าที่ติดต่อแจ้งเตือนสถานะการจองน้องแมว การประสานงานจัดส่ง และข้อเสนอพิเศษผ่านทาง <u>เบอร์โทรศัพท์ / SMS</u>
                    </div>
                </label>

                <!-- Terms and PDPA Consent -->
                <label class="consent-item" style="border-top: 1px solid #FFE6D9; padding-top: 0.8rem; margin-top: 0.8rem;">
                    <input type="checkbox" name="consent_terms" class="consent-checkbox" required <?php echo $consent_terms ? 'checked' : ''; ?>>
                    <div class="consent-text">
                        <strong>ข้อกำหนดและนโยบายความเป็นส่วนตัว (PDPA) *:</strong> ข้าพเจ้ายอมรับข้อกำหนด เงื่อนไขการให้บริการ และนโยบายการคุ้มครองข้อมูลส่วนบุคคลของร้าน Cat Shop
                    </div>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.9rem; font-size: 1.05rem;">
                ยืนยันการสมัครสมาชิก 🐾
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.8rem; padding-top: 1.2rem; border-top: 1px solid var(--border-color); font-size: 0.92rem; color: var(--text-secondary);">
            มีบัญชีสมาชิกอยู่แล้ว? <a href="login.php" style="color: var(--primary-coral); font-weight: 700; text-decoration: none;">เข้าสู่ระบบที่นี่</a>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
