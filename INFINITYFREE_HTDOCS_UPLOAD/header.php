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

    <!-- Google Analytics 4 (GA4) Simulation Script -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-PURRFECTCAT88"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-PURRFECTCAT88', {
            'page_title': document.title,
            'transport_type': 'beacon'
        });
    </script>

    <!-- Meta Pixel (Facebook Pixel) Simulation Script -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '123456789012345');
        fbq('track', 'PageView');
    </script>

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

                    <!-- User Account / Profile Badge & Paw Points -->
                    <?php if ($logged_user): ?>
                        <div style="display: inline-flex; align-items: center; gap: 0.6rem;">
                            <!-- Loyalty Points Badge -->
                            <a href="profile.php?tab=points" class="top-badge-pill" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #92400E; border: 1px solid #F59E0B; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: transform 0.2s ease;" title="ดูระบบสะสมแต้ม Paw Points & Tiers ของคุณ">
                                🐾 <?php echo number_format($logged_user['paw_points'] ?? 150); ?> พอยท์
                            </a>
                            <a href="subscribe.php" class="btn btn-sm <?php echo $current_page == 'subscribe.php' ? 'active' : ''; ?>" style="background: rgba(255, 117, 86, 0.12); color: var(--primary-coral); border: 1.5px solid var(--primary-coral); font-weight: 700; padding: 0.35rem 0.85rem; font-size: 0.82rem; border-radius: 999px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="รับข้อมูลข่าวสารและโปรโมชัน">
                                📬 ข่าวสาร
                            </a>
                            <a href="profile.php" class="user-badge <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>" title="คลิกเพื่อจัดการโปรไฟล์และประวัติคำสั่งซื้อ">
                                <img src="<?php echo htmlspecialchars($logged_user['avatar'] ?? 'assets/images/logo.png'); ?>" alt="Avatar" class="user-avatar-sm" style="width: 22px; height: 22px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--primary-coral);">
                                <span>คุณ<?php echo htmlspecialchars($logged_user['username']); ?></span>
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

        <!-- 2. Main Branding & Primary Navigation Bar -->
        <div class="main-nav-bar">
            <div class="nav-container">
                <a href="index.php" class="site-logo">
                    <img src="assets/images/logo.png" alt="Purrfect Shop Logo" class="logo-img">
                    <div class="logo-text">
                        <span class="logo-title">Purrfect Shop</span>
                        <span class="logo-subtitle">Cattery & Boutique</span>
                    </div>
                </a>

                <!-- 3-Line Hamburger Menu Button (Mobile & Tablet) -->
                <button type="button" class="mobile-menu-toggle-btn" id="mobile-menu-btn" onclick="toggleMobileDrawer()" aria-label="เปิดเมนูการนำทางและหมวดหมู่" title="เปิดเมนู ☰">
                    <span class="hamburger-bar"></span>
                    <span class="hamburger-bar"></span>
                    <span class="hamburger-bar"></span>
                </button>
                
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
                        <li>
                            <a href="tracking.php" class="nav-link <?php echo $current_page == 'tracking.php' ? 'active' : ''; ?>">
                                📍 ติดตามการจัดส่ง
                            </a>
                        </li>
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

    <!-- =========================================================
         MOBILE NAVIGATION DRAWER (OFF-CANVAS SLIDING MENU)
         ========================================================= -->
    <!-- Drawer Backdrop -->
    <div id="mobile-drawer-backdrop" class="mobile-drawer-backdrop" onclick="closeMobileDrawer()"></div>

    <!-- Mobile Drawer Content -->
    <aside id="mobile-nav-drawer" class="mobile-nav-drawer" aria-label="เมนูหลักสำหรับมือถือ">
        <div class="drawer-header">
            <div class="drawer-branding">
                <img src="assets/images/logo.png" alt="Logo" class="drawer-logo-img">
                <div>
                    <strong class="drawer-title">Purrfect Shop</strong>
                    <span class="drawer-subtitle">เมนูด่วน & หมวดหมู่บริการ</span>
                </div>
            </div>
            <button type="button" class="drawer-close-btn" onclick="closeMobileDrawer()" aria-label="ปิดเมนู">✕</button>
        </div>

        <div class="drawer-body">
            <!-- หมวดที่ 1: บัญชีผู้ใช้ & การตั้งค่า -->
            <div class="drawer-section">
                <div class="drawer-section-title">
                    <span>⚙️ หมวดที่ 1: บัญชีผู้ใช้ & การตั้งค่า</span>
                </div>
                <div class="drawer-grid">
                    <!-- Theme Loop Switcher -->
                    <button type="button" class="drawer-action-btn theme-drawer-btn" onclick="cycleTheme(); updateDrawerThemeName();" title="กดเพื่อสลับธีมสี">
                        <span class="drawer-btn-icon">🎨</span>
                        <span class="drawer-btn-label">ธีม: <strong id="drawer-theme-current-name">อบอุ่น คอรัล</strong></span>
                        <span class="drawer-btn-badge">🔄 สลับสี</span>
                    </button>

                    <!-- User Profile / Login / Register -->
                    <?php if ($logged_user): ?>
                        <a href="profile.php?tab=points" class="drawer-link-item" onclick="closeMobileDrawer()">
                            <span class="drawer-link-icon">🐾</span>
                            <div class="drawer-link-info">
                                <span class="drawer-link-title">Paw Points สะสม</span>
                                <span class="drawer-link-sub"><?php echo number_format($logged_user['paw_points'] ?? 150); ?> พอยท์</span>
                            </div>
                            <span class="drawer-badge-pill" style="background: #FEF3C7; color: #92400E; border: 1px solid #F59E0B;">ดูพอยท์ ↗</span>
                        </a>

                        <a href="subscribe.php" class="drawer-link-item <?php echo $current_page == 'subscribe.php' ? 'active' : ''; ?>" onclick="closeMobileDrawer()">
                            <span class="drawer-link-icon">📬</span>
                            <div class="drawer-link-info">
                                <span class="drawer-link-title">รับข่าวสาร & โปรโมชัน</span>
                                <span class="drawer-link-sub">สิทธิพิเศษและกิจกรรมรายเดือน</span>
                            </div>
                        </a>

                        <a href="profile.php" class="drawer-link-item <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>" onclick="closeMobileDrawer()">
                            <img src="<?php echo htmlspecialchars($logged_user['avatar'] ?? 'assets/images/logo.png'); ?>" alt="Avatar" class="drawer-avatar">
                            <div class="drawer-link-info">
                                <span class="drawer-link-title">คุณ<?php echo htmlspecialchars($logged_user['username']); ?></span>
                                <span class="drawer-link-sub">จัดการข้อมูลส่วนตัว & คำสั่งซื้อ</span>
                            </div>
                        </a>

                        <?php if (isAdmin()): ?>
                            <a href="admin.php" class="drawer-link-item drawer-admin-link" onclick="closeMobileDrawer()">
                                <span class="drawer-link-icon">🛠️</span>
                                <div class="drawer-link-info">
                                    <span class="drawer-link-title">จัดการระบบหลังบ้าน (Admin)</span>
                                    <span class="drawer-link-sub">จัดการน้องแมว, ออเดอร์ & Tracking</span>
                                </div>
                                <span class="drawer-badge-pill" style="background: #FFEDD5; color: #C2410C; border: 1px solid #FB923C;">ADMIN</span>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="subscribe.php" class="drawer-link-item <?php echo $current_page == 'subscribe.php' ? 'active' : ''; ?>" onclick="closeMobileDrawer()">
                            <span class="drawer-link-icon">📬</span>
                            <div class="drawer-link-info">
                                <span class="drawer-link-title">รับข้อมูลข่าวสาร</span>
                                <span class="drawer-link-sub">รับสิทธิ์โปรโมชัน & ข่าวกิจกรรม</span>
                            </div>
                        </a>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 4px;">
                            <a href="login.php" class="btn btn-secondary btn-sm" style="text-align: center; justify-content: center; font-size: 0.85rem; padding: 0.5rem;" onclick="closeMobileDrawer()">
                                เข้าสู่ระบบ 🔑
                            </a>
                            <a href="register.php" class="btn btn-primary btn-sm" style="text-align: center; justify-content: center; font-size: 0.85rem; padding: 0.5rem;" onclick="closeMobileDrawer()">
                                สมัครสมาชิก ✨
                            </a>
                        </div>
                    <?php endif; ?>

                    <a href="index.php" class="drawer-link-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" onclick="closeMobileDrawer()">
                        <span class="drawer-link-icon">🏠</span>
                        <div class="drawer-link-info">
                            <span class="drawer-link-title">หน้าแรก (Home)</span>
                            <span class="drawer-link-sub">ศูนย์รวมน้องแมว & โปรโมชันเด่น</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- หมวดที่ 2: เมนูหลัก & บริการร้านค้า -->
            <div class="drawer-section">
                <div class="drawer-section-title">
                    <span>🐱 หมวดที่ 2: เมนูบริการ & เลือกชมน้องแมว</span>
                </div>
                <div class="drawer-grid">
                    <a href="products.php" class="drawer-link-item <?php echo $current_page == 'products.php' ? 'active' : ''; ?>" onclick="closeMobileDrawer()">
                        <span class="drawer-link-icon">🐱</span>
                        <div class="drawer-link-info">
                            <span class="drawer-link-title">น้องแมวทั้งหมด</span>
                            <span class="drawer-link-sub">สายพันธุ์แท้ 100% พร้อมย้ายบ้าน</span>
                        </div>
                    </a>

                    <a href="recommend.php" class="drawer-link-item <?php echo $current_page == 'recommend.php' ? 'active' : ''; ?>" onclick="closeMobileDrawer()">
                        <span class="drawer-link-icon">🌟</span>
                        <div class="drawer-link-info">
                            <span class="drawer-link-title">แนะนำสำหรับคุณ</span>
                            <span class="drawer-link-sub">ระบบค้นหาสายพันธุ์ตามไลฟ์สไตล์</span>
                        </div>
                    </a>

                    <a href="welcome_deal.php" class="drawer-link-item <?php echo $current_page == 'welcome_deal.php' ? 'active' : ''; ?>" onclick="closeMobileDrawer()">
                        <span class="drawer-link-icon">🎁</span>
                        <div class="drawer-link-info">
                            <span class="drawer-link-title">ดีลสมาชิกใหม่</span>
                            <span class="drawer-link-sub">คูปองส่วนลดพิเศษ & Starter Kit</span>
                        </div>
                        <span class="drawer-badge-pill" style="background: #FF5A5F; color: #FFFFFF; font-weight: 800;">HOT</span>
                    </a>

                    <a href="tracking.php" class="drawer-link-item <?php echo $current_page == 'tracking.php' ? 'active' : ''; ?>" onclick="closeMobileDrawer()">
                        <span class="drawer-link-icon">📍</span>
                        <div class="drawer-link-info">
                            <span class="drawer-link-title">ติดตามการจัดส่ง</span>
                            <span class="drawer-link-sub">ระบบติดตามสถานะสด 4 ขั้นตอน</span>
                        </div>
                    </a>

                    <a href="creator.php" class="drawer-link-item <?php echo $current_page == 'creator.php' ? 'active' : ''; ?>" onclick="closeMobileDrawer()">
                        <span class="drawer-link-icon">👨‍💻</span>
                        <div class="drawer-link-info">
                            <span class="drawer-link-title">ผู้จัดทำ</span>
                            <span class="drawer-link-sub">ประวัติ & ข้อมูลโครงงาน</span>
                        </div>
                    </a>

                    <a href="cart.php" class="drawer-link-item <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>" style="background: rgba(255, 107, 74, 0.08); border-color: rgba(255, 107, 74, 0.28);" onclick="closeMobileDrawer()">
                        <span class="drawer-link-icon">🛒</span>
                        <div class="drawer-link-info">
                            <span class="drawer-link-title" style="color: var(--primary-coral); font-weight: 800;">ตะกร้าสินค้า</span>
                            <span class="drawer-link-sub">ดำเนินการจอง & สรุปยอดชำระ</span>
                        </div>
                        <?php 
                        $drawer_cart_count = getCartCount();
                        if ($drawer_cart_count > 0): 
                        ?>
                            <span class="drawer-badge-pill" style="background: var(--primary-coral); color: #FFFFFF; font-weight: 800;"><?php echo $drawer_cart_count; ?> ตัว</span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <script>
    function toggleMobileDrawer() {
        const drawer = document.getElementById('mobile-nav-drawer');
        const backdrop = document.getElementById('mobile-drawer-backdrop');
        const btn = document.getElementById('mobile-menu-btn');
        if (!drawer || !backdrop) return;
        
        const isOpen = drawer.classList.contains('open');
        if (isOpen) {
            closeMobileDrawer();
        } else {
            drawer.classList.add('open');
            backdrop.classList.add('open');
            if (btn) btn.classList.add('open');
            document.body.style.overflow = 'hidden';
            updateDrawerThemeName();
        }
    }

    function closeMobileDrawer() {
        const drawer = document.getElementById('mobile-nav-drawer');
        const backdrop = document.getElementById('mobile-drawer-backdrop');
        const btn = document.getElementById('mobile-menu-btn');
        if (drawer) drawer.classList.remove('open');
        if (backdrop) backdrop.classList.remove('open');
        if (btn) btn.classList.remove('open');
        document.body.style.overflow = '';
    }

    function updateDrawerThemeName() {
        const currentNameEl = document.getElementById('theme-current-name');
        const drawerThemeEl = document.getElementById('drawer-theme-current-name');
        if (currentNameEl && drawerThemeEl) {
            drawerThemeEl.textContent = currentNameEl.textContent;
        }
    }

    // Sync theme label on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', () => {
        updateDrawerThemeName();
    });
    </script>

    <script>
    window.trackPurrfectBehavior = function(type, target, category) {
        try {
            const formData = new FormData();
            formData.append('action', 'track');
            formData.append('event_type', type);
            formData.append('target', target);
            formData.append('category', category || 'general');
            if (navigator.sendBeacon) {
                navigator.sendBeacon('api_analytics.php', formData);
            } else {
                fetch('api_analytics.php', { method: 'POST', body: formData });
            }
        } catch (e) {}
    };
    </script>
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
