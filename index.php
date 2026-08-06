<?php 
require_once __DIR__ . '/header.php'; 
?>

<!-- Hero Banner Area -->
<section class="hero">
    <div class="hero-glow"></div>
    <h1 class="hero-title">
        <span class="gradient-text-1">CAT</span> 
        <span class="gradient-text-2">CYBER</span> SHOP
    </h1>
    <p class="hero-slogan">
        ขายแมวพร้อมกับ ขายโปรแกรมตามความต้องการผู้ใช้ แบ่งตามขนาดขอบเขต
    </p>
    <p style="color: var(--text-muted); max-width: 600px; margin-bottom: 2rem; font-size: 0.95rem;">
        ยินดีต้อนรับสู่แหล่งจำหน่ายแมว AI เพื่อนคู่ใจเขียนโค้ดและบริการพัฒนาซอฟต์แวร์ระดับมืออาชีพตามขอบเขตงานของคุณ!
    </p>
    <div class="hero-actions">
        <a href="products.php" class="btn btn-primary">ดูสินค้าทั้งหมด 🐱💻</a>
        <a href="creator.php" class="btn btn-secondary">ข้อมูลผู้จัดทำ 👨‍💻</a>
    </div>
</section>

<!-- Unique Concept Section -->
<section class="features-section">
    <div class="section-header">
        <h2 class="section-title">ทำไมต้องเลือกเรา?</h2>
        <p class="section-subtitle">ความลงตัวแห่งโลกไอทีและการเลี้ยงแมว</p>
    </div>
    
    <div class="cards-grid">
        <!-- Feature 1 -->
        <div class="glass-card">
            <div class="card-top">
                <div class="card-icon-wrapper">🐱</div>
                <h3 class="card-title">Cyber Cats เพื่อนแท้โปรแกรมเมอร์</h3>
                <p class="card-desc">แมวของเราได้รับการฝึกฝนให้เข้าสังคมกับเหล่านักพัฒนา พร้อมชิปวิเคราะห์และเสียงร้องความต้านทานระดับโอห์มต่ำที่จะบำบัดความเครียดของคุณได้อย่างดี</p>
            </div>
            <ul class="card-features">
                <li>ลดความเครียดทันที 200%</li>
                <li>ไม่รบกวนช่วงเวลาสำคัญ (ยกเว้นตอนหิว)</li>
                <li>ผ่านการทดสอบกับเซิร์ฟเวอร์จริง</li>
            </ul>
        </div>
        
        <!-- Feature 2 -->
        <div class="glass-card pink-accent">
            <div class="card-top">
                <div class="card-icon-wrapper">💻</div>
                <h3 class="card-title">ซอฟต์แวร์แบ่งตามขอบเขต (Scope)</h3>
                <p class="card-desc">เราจัดสรรราคาและการส่งมอบงานโปรแกรมมิ่งตามความต้องการจริง แบ่งตามขนาดขอบเขตอย่างเป็นธรรม เพื่อให้ประหยัดงบประมาณและตรงจุดมากที่สุด</p>
            </div>
            <ul class="card-features">
                <li>จ่ายตามขนาดของฟีเจอร์ที่ต้องการ</li>
                <li>ระบบประเมินราคาอัจฉริยะล่วงหน้า</li>
                <li>ส่งมอบงานตรงเวลา มีประกันคุณภาพ</li>
            </ul>
        </div>
        
        <!-- Feature 3 -->
        <div class="glass-card">
            <div class="card-top">
                <div class="card-icon-wrapper">⚡</div>
                <h3 class="card-title">บริการส่งมอบแบบ Cyberpunk</h3>
                <p class="card-desc">ส่งแมวผ่านกล่องควบคุมอุณหภูมิความปลอดภัยสูง และซอร์สโค้ดผ่าน Git repository ส่วนตัวอย่างรวดเร็ว พร้อมเอกสารประกอบสัญญางานครบวงจร</p>
            </div>
            <ul class="card-features">
                <li>จัดส่งถึงหน้าบ้านอย่างปลอดภัย</li>
                <li>รับประกันคุณภาพทั้งโค้ดและแมว</li>
                <li>บริการให้คำปรึกษาตลอดอายุสัญญา</li>
            </ul>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="features-section">
    <div class="section-header">
        <h2 class="section-title">สินค้าเด่นของเรา</h2>
        <p class="section-subtitle">แมวสายพันธุ์ท็อปฮิตและโปรแกรมขนาดเริ่มต้น</p>
    </div>
    
    <div class="cards-grid">
        <!-- Featured Cat -->
        <div class="glass-card">
            <div class="card-top">
                <div class="card-icon-wrapper">🐈</div>
                <span class="price-label" style="color: var(--accent-pink); font-weight: 700; margin-bottom: 0.5rem; display: block;">POPULAR</span>
                <h3 class="card-title">Cyber British Shorthair</h3>
                <p class="card-desc">เพื่อนช่วยตรวจโค้ดขวัญใจนักเรียน นักศึกษา และโปรแกรมเมอร์มืออาชีพ พร้อมชิปเตือนเมื่อเขียนโค้ดซ้ำซ้อน</p>
            </div>
            <div class="card-price-row">
                <div class="card-price">
                    <span class="price-label">เริ่มต้นที่</span>
                    <span class="price-val">15,000 ฿</span>
                </div>
                <a href="products.php?filter=cat" class="btn btn-secondary btn-sm" style="padding: 0.5rem 1rem; font-size: 0.8rem;">เลือกดูแมว</a>
            </div>
        </div>

        <!-- Featured Program -->
        <div class="glass-card pink-accent">
            <div class="card-top">
                <div class="card-icon-wrapper">⚙️</div>
                <span class="price-label" style="color: var(--primary-cyan); font-weight: 700; margin-bottom: 0.5rem; display: block;">HOT DEALS</span>
                <h3 class="card-title">Medium Scope App</h3>
                <p class="card-desc">ระบบเว็บแอปพลิเคชันจัดการข้อมูลทั่วไป พร้อมฐานข้อมูลและระบบสมาชิกครบถ้วน ส่งมอบภายใน 3 สัปดาห์</p>
            </div>
            <div class="card-price-row">
                <div class="card-price">
                    <span class="price-label">เริ่มต้นที่</span>
                    <span class="price-val">15,000 ฿</span>
                </div>
                <a href="products.php?filter=program" class="btn btn-secondary btn-sm" style="padding: 0.5rem 1rem; font-size: 0.8rem; border-color: var(--accent-pink); color: var(--accent-pink);">สั่งทำโปรแกรม</a>
            </div>
        </div>
    </div>
</section>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
