<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/data.php';

// Detect active page name
$current_page = basename($_SERVER['PHP_SELF']);
$logged_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purrfect Shop - เพราะทุกบ้านควรมีเจ้าเหมียว 🐾</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="apple-touch-icon" href="assets/images/favicon.png">
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('cat_shop_theme') || 'warm';
                document.documentElement.setAttribute('data-theme', savedTheme);
            } catch(e) {}
        })();
    </script>
</head>
<body>
    <header class="site-header">
        <!-- 1. Top Utility Bar: Announcements, Theme Switcher, Admin Console & User Profile -->
        <div class="top-utility-bar">
            <div class="top-utility-container">
                <div class="top-utility-left">
                    <span class="top-badge-pill">🐾 Purrfect Cattery & Boutique</span>
                    <span class="top-utility-tagline">ฟาร์มเพาะพันธุ์น้องแมวสายพันธุ์แท้มาตรฐานสากล • การันตีสุขภาพ 180 วัน</span>
                </div>
                
                <div class="top-utility-right">
                    <!-- Theme Loop Switcher Button -->
                    <button type="button" class="theme-loop-btn" onclick="cycleTheme()" title="กดเพื่อสลับสีพื้นหลังแบบวนลูป (Loop)">
                        🎨 <span id="theme-current-name">อบอุ่น คอรัล</span> 🔄
                    </button>

                    <!-- Admin Console Shortcut (Only visible for Admins) -->
                    <?php if ($logged_user && isAdmin()): ?>
                        <a href="admin.php" class="top-admin-btn" title="เข้าสู่ระบบจัดการหลังบ้าน (Admin Control Panel)">
                            🛠️ จัดการระบบ (Admin)
                        </a>
                    <?php endif; ?>

                    <!-- User Account / Profile Badge -->
                    <?php if ($logged_user): ?>
                        <div style="display: inline-flex; align-items: center; gap: 0.6rem;">
                            <a href="subscribe.php" class="btn btn-sm <?php echo $current_page == 'subscribe.php' ? 'active' : ''; ?>" style="background: rgba(255, 117, 86, 0.12); color: var(--primary-coral); border: 1.5px solid var(--primary-coral); font-weight: 700; padding: 0.35rem 0.85rem; font-size: 0.82rem; border-radius: 999px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="รับข้อมูลข่าวสารและโปรโมชัน">
                                📬 รับข้อมูลข่าวสาร
                            </a>
                            <a href="profile.php" class="user-badge <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>" title="คลิกเพื่อจัดการโปรไฟล์ ข้อมูลชำระเงิน และประวัติคำสั่งซื้อ">
                                <img src="<?php echo htmlspecialchars($logged_user['avatar'] ?? 'assets/images/logo.png'); ?>" alt="Avatar" class="user-avatar-sm" style="width: 22px; height: 22px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--primary-coral);">
                                <span>คุณ<?php echo htmlspecialchars($logged_user['username']); ?></span>
                                <span class="discount-pill">ลด 5%</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div style="display: inline-flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                            <!-- ปุ่มรับข้อมูลข่าวสาร (สำหรับผู้ใช้ที่ยังไม่ต้องการสมัครสมาชิก) ตำแหน่งอยู่ทางซ้ายของ เข้าสู่ระบบ -->
                            <a href="subscribe.php" class="btn btn-sm <?php echo $current_page == 'subscribe.php' ? 'active' : ''; ?>" style="background: rgba(255, 117, 86, 0.12); color: var(--primary-coral); border: 1.5px solid var(--primary-coral); font-weight: 700; padding: 0.35rem 0.85rem; font-size: 0.82rem; border-radius: 999px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="รับข้อมูลข่าวสารโดยยังไม่ต้องสมัครสมาชิก">
                                📬 รับข้อมูลข่าวสาร
                            </a>
                            <a href="login.php" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.85rem; font-size: 0.82rem;">
                                เข้าสู่ระบบ 🔑
                            </a>
                            <a href="register.php" class="btn btn-primary btn-sm" style="padding: 0.35rem 0.9rem; font-size: 0.82rem;">
                                สมัครสมาชิก ✨
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 2. Main Navigation Bar: Brand Logo, Page Links & Shopping Cart -->
        <div class="main-nav-bar">
            <div class="nav-container">
                <a href="index.php" class="logo">
                    <img src="assets/images/logo.png" alt="Purrfect Shop Logo" class="logo-img">
                    <div class="logo-title-group">
                        <span class="logo-text">Purrfect Shop 🐾</span>
                        <span class="logo-tagline">เพราะทุกบ้านควรมีเจ้าเหมียว</span>
                    </div>
                </a>
                
                <nav>
                    <ul class="nav-menu">
                        <li>
                            <a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                                🏠 หน้าแรก
                            </a>
                        </li>
                        <li>
                            <a href="products.php" class="nav-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>">
                                🐱 น้องแมวทั้งหมด
                            </a>
                        </li>
                        <li>
                            <a href="recommend.php" class="nav-link <?php echo $current_page == 'recommend.php' ? 'active' : ''; ?>">
                                🌟 แนะนำสำหรับคุณ
                            </a>
                        </li>
                        <li>
                            <a href="welcome_deal.php" class="nav-link <?php echo $current_page == 'welcome_deal.php' ? 'active' : ''; ?>" style="position: relative;">
                                🎁 ดีลสมาชิกใหม่
                                <span style="background: #FF5A5F; color: #fff; font-size: 0.65rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 999px; vertical-align: middle; margin-left: 2px;">HOT</span>
                            </a>
                        </li>
                        <?php if (isAdmin()): ?>
                        <li>
                            <a href="welcome_sales_mockup.php" class="nav-link <?php echo $current_page == 'welcome_sales_mockup.php' ? 'active' : ''; ?>">
                                ✉️ ส่งข้อมูล
                            </a>
                        </li>
                        <?php endif; ?>
                        <li>
                            <a href="creator.php" class="nav-link <?php echo $current_page == 'creator.php' ? 'active' : ''; ?>">
                                👨‍💻 ผู้จัดทำ
                            </a>
                        </li>
                        <li class="cart-badge-container">
                            <a href="cart.php" class="nav-link nav-cart-btn <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>">
                                🛒 ตะกร้า
                                <?php 
                                $count = getCartCount();
                                if ($count > 0): 
                                ?>
                                    <span class="cart-badge" id="cart-badge-val"><?php echo $count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <main>
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div style="max-width: 1200px; margin: 1.5rem auto 0 auto; padding: 1rem 1.5rem; background: #ECFDF5; border: 1.5px solid #10B981; border-radius: 12px; color: #065F46; font-weight: 700; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);">
                <span><?php echo $_SESSION['flash_success']; ?></span>
                <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #065F46;">&times;</button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div style="max-width: 1200px; margin: 1.5rem auto 0 auto; padding: 1rem 1.5rem; background: #FEF2F2; border: 1.5px solid #EF4444; border-radius: 12px; color: #991B1B; font-weight: 700; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);">
                <span><?php echo $_SESSION['flash_error']; ?></span>
                <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #991B1B;">&times;</button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
