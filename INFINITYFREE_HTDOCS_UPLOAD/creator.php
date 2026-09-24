<?php 
require_once __DIR__ . '/header.php'; 
?>

<div class="creator-container">
    <div class="creator-card">
        <div class="creator-header-banner"></div>
        <div class="creator-avatar-wrap">
            <div class="creator-avatar">👨‍💻</div>
        </div>

        <div class="creator-body">
            <h1 class="creator-name">Purrfect Cattery Team</h1>
            <p class="creator-role">IT Student & Web Developer 🐾</p>

            <div class="creator-info-grid">
                <div class="creator-info-item">
                    <div class="creator-info-label">STUDENT ID / รหัสนิสิต</div>
                    <div class="creator-info-val" style="color: var(--primary-coral);">TH-CAT-8899</div>
                </div>

                <div class="creator-info-item">
                    <div class="creator-info-label">MAJOR / สาขาวิชา</div>
                    <div class="creator-info-val">Information Technology (IT)</div>
                </div>

                <div class="creator-info-item">
                    <div class="creator-info-label">FACULTY / คณะ</div>
                    <div class="creator-info-val">Purrfect Boutique Cattery</div>
                </div>

                <div class="creator-info-item">
                    <div class="creator-info-label">PROJECT / ภาคการศึกษา</div>
                    <div class="creator-info-val" style="color: var(--accent-mint);">Workshop 2567 (Semester 4)</div>
                </div>
            </div>

            <div style="background: var(--bg-page); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; text-align: left; margin-bottom: 2rem; font-size: 0.92rem; color: var(--text-secondary); line-height: 1.7;">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.6rem;">
                    💡 เกี่ยวกับโครงการร้านค้า Cat Shop
                </h3>
                <p style="margin-bottom: 0.8rem;">
                    โครงการ <strong>Cat Shop (อาณาจักรน้องแมวสายพันธุ์แท้)</strong> ได้รับการพัฒนาขึ้นโดยมีเป้าหมายเพื่อฝึกฝนทักษะการสร้างเว็บแอปพลิเคชันเชิงพาณิชย์ (E-Commerce Web Application) ด้วยภาษา PHP, HTML5, Modern CSS และ JavaScript โดยเน้นประสบการณ์ผู้ใช้ (UX/UI) ที่น่ารัก อบอุ่น สวยงาม
                </p>
                <p>
                    ระบบมาพร้อมฟังก์ชันการแนะนำสายพันธุ์น้องแมวตามหมวดหมู่ไลฟ์สไตล์ (Recommendation System), ระบบคัดกรองอัจฉริยะ (Smart Cat Matcher), ระบบสมัครสมาชิกพร้อมการขอความยินยอม (Consent) ทางอีเมลและเบอร์โทรศัพท์ตามหลัก PDPA, ระบบจัดการตะกร้าสินค้า และคำนวณส่วนลดสมาชิกอัตโนมัติ
                </p>
            </div>

            <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                <a href="products.php" class="btn btn-primary">
                    เลือกชมน้องแมวในร้าน 🐱
                </a>
                <a href="index.php" class="btn btn-secondary">
                    กลับสู่หน้าแรก 🏠
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
