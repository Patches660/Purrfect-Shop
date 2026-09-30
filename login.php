<?php
require_once __DIR__ . '/data.php';

// If user explicitly asks to switch account or log out first
if (isset($_GET['switch']) || isset($_GET['logout_first'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    unset($_SESSION['user']);
}

// 1-Click Quick Demo Login Handlers
if (isset($_GET['quick_admin']) || (isset($_POST['quick_login']) && $_POST['quick_login'] === 'admin')) {
    $res = loginUser('admin', 'admin123');
    if ($res['success']) {
        header("Location: admin.php?msg=admin_logged_in");
        exit;
    }
}

if (isset($_GET['quick_user']) || (isset($_POST['quick_login']) && $_POST['quick_login'] === 'user')) {
    $res = loginUser('catlover', '123456');
    if ($res['success']) {
        header("Location: index.php?msg=login_success");
        exit;
    }
}

// If already logged in and not switching
if (isUserLoggedIn()) {
    if (isAdmin()) {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$errors = [];
$identifier = "";

if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST' && empty($_POST['quick_login'])) {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identifier)) {
        $errors[] = "กรุณากรอกอีเมลหรือชื่อผู้ใช้";
    }
    if (empty($password)) {
        $errors[] = "กรุณากรอกรหัสผ่าน";
    }

    if (empty($errors)) {
        $res = loginUser($identifier, $password);
        if ($res['success']) {
            if (isAdmin()) {
                header("Location: admin.php?msg=admin_logged_in");
            } elseif (getCartCount() > 0) {
                header("Location: cart.php?msg=login_success");
            } else {
                header("Location: index.php?msg=login_success");
            }
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
            <div class="auth-icon-circle">🔑</div>
            <h1 class="auth-title">เข้าสู่ระบบสมาชิก & ผู้ดูแลระบบ</h1>
            <p class="auth-subtitle">เข้าสู่ระบบเพื่อรับส่วนลด 5% หรือเข้าสู่ระบบจัดการหลังบ้าน</p>
        </div>



        <?php if (!empty($errors)): ?>
            <div class="alert-box alert-danger">
                <div>
                    <strong>⚠️ เกิดข้อผิดพลาด:</strong>
                    <ul style="margin-top: 0.3rem; margin-left: 1.2rem;">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="identifier">อีเมล หรือ ชื่อผู้ใช้</label>
                <input type="text" id="identifier" name="identifier" class="form-control" 
                       placeholder="เช่น admin หรือ catlover" 
                       value="<?php echo htmlspecialchars($identifier); ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="รหัสผ่านของคุณ" required>
            </div>

            <div style="margin-top: 0.5rem; margin-bottom: 1.5rem; font-size: 0.84rem; color: var(--text-muted); line-height: 1.5;">
                💡 <strong>ข้อมูลบัญชีสำหรับทดสอบ:</strong><br>
                &bull; <strong>👑 ผู้ดูแลระบบ (Admin):</strong> User: <code>admin</code> / Pass: <code>admin123</code> (หรือ User: <code>Meow</code>)<br>
                &bull; <strong>🐱 ลูกค้าสมาชิก (Customer):</strong> User: <code>catlover</code> / Pass: <code>123456</code>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.9rem; font-size: 1.05rem;">
                เข้าสู่ระบบ 🐾
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.8rem; padding-top: 1.2rem; border-top: 1px solid var(--border-color); font-size: 0.92rem; color: var(--text-secondary);">
            ยังไม่ได้เป็นสมาชิก? <a href="register.php" style="color: var(--primary-coral); font-weight: 700; text-decoration: none;">สมัครสมาชิกใหม่ที่นี่ (รับส่วนลด 5%)</a>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
