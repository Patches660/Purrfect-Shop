<?php
require_once __DIR__ . '/data.php';

if (isUserLoggedIn()) {
    header("Location: index.php");
    exit;
}

$errors = [];
$identifier = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            if (getCartCount() > 0) {
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
            <h1 class="auth-title">เข้าสู่ระบบสมาชิก</h1>
            <p class="auth-subtitle">เข้าสู่ระบบเพื่อรับส่วนลด 5% และจัดการการจองน้องแมว</p>
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
                       placeholder="เช่น catlover หรือ catlover@example.com" 
                       value="<?php echo htmlspecialchars($identifier); ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="รหัสผ่านของคุณ" required>
            </div>

            <div style="margin-top: 0.5rem; margin-bottom: 1.5rem; font-size: 0.85rem; color: var(--text-muted);">
                💡 <em>บัญชีสำหรับทดสอบ: Username: <code>catlover</code> / รหัสผ่าน: <code>123456</code></em>
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
