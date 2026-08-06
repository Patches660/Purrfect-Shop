<?php
require_once __DIR__ . '/data.php';

// Handle Add to Cart actions
$added_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_cat') {
        $id = $_POST['cat_id'];
        addToCart($id, 'cat');
        $added_message = "🐾 เพิ่ม " . $cats[$id]['name'] . " ลงในตะกร้าเรียบร้อยแล้ว!";
    } elseif ($_POST['action'] === 'add_program') {
        $id = $_POST['program_id'];
        addToCart($id, 'program');
        $added_message = "💻 เพิ่ม " . $programs[$id]['name'] . " ลงในตะกร้าเรียบร้อยแล้ว!";
    } elseif ($_POST['action'] === 'add_custom') {
        $base_id = $_POST['base_scale'];
        $final_price = floatval($_POST['custom_price']);
        $notes = $_POST['custom_notes'];
        
        $base_name = $programs[$base_id]['name'];
        addToCart($base_id, 'program', $final_price, $notes);
        $added_message = "⚡ เพิ่มโปรแกรมแบบกำหนดเอง (" . $base_name . ") ลงในตะกร้าเรียบร้อยแล้ว!";
    }
}

// Check initial query filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

require_once __DIR__ . '/header.php';
?>

<!-- Added Alert Banner -->
<?php if (!empty($added_message)): ?>
    <div style="background: rgba(0, 242, 254, 0.15); border: 1px solid var(--primary-cyan); border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; text-shadow: 0 0 10px rgba(0,242,254,0.3); font-weight: 500;">
        <span><?php echo $added_message; ?></span>
        <a href="cart.php" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.8rem; font-family: 'Orbitron';">ไปที่ตะกร้า 🛒</a>
    </div>
<?php endif; ?>

<!-- Header -->
<div class="section-header">
    <h1 class="section-title">ศูนย์รวมสินค้าและบริการ</h1>
    <p class="section-subtitle">เลือกซื้อคู่หูแมวไซเบอร์ หรือคำนวณสเปกโปรแกรมตามขอบเขตความต้องการ</p>
</div>

<!-- Filters Menu -->
<div class="catalog-filters">
    <button class="filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>" onclick="filterCatalog('all')">ALL PRODUCTS</button>
    <button class="filter-btn <?php echo $filter === 'cat' ? 'active' : ''; ?>" onclick="filterCatalog('cat')">CYBER CATS 🐾</button>
    <button class="filter-btn <?php echo $filter === 'program' ? 'active' : ''; ?>" onclick="filterCatalog('program')">PROGRAMMING SCOPES 💻</button>
</div>

<!-- Cats Section -->
<div class="catalog-item-group" id="cat-group" style="<?php echo ($filter === 'all' || $filter === 'cat') ? '' : 'display:none;'; ?>">
    <h2 class="catalog-section-title">🐱 สายพันธุ์แมวไซเบอร์ (Cyber Cats)</h2>
    <div class="cards-grid">
        <?php foreach ($cats as $key => $cat): ?>
            <div class="glass-card">
                <div class="card-top">
                    <!-- Icon representation or design instead of broken image links -->
                    <div class="card-icon-wrapper">
                        <?php 
                        if ($key == 'cat_british') echo '🇬🇧';
                        elseif ($key == 'cat_persian') echo '🇮🇷';
                        elseif ($key == 'cat_sphynx') echo '👽';
                        else echo '🇹🇭';
                        ?>
                    </div>
                    <h3 class="card-title"><?php echo $cat['name']; ?></h3>
                    <p class="card-desc"><?php echo $cat['description']; ?></p>
                </div>
                
                <div>
                    <h4 style="font-size: 0.85rem; color: var(--primary-cyan); font-family: 'Orbitron'; margin-bottom: 0.5rem; text-transform: uppercase;">ความสามารถพิเศษ</h4>
                    <ul class="card-features" style="margin-bottom: 1.5rem;">
                        <?php foreach ($cat['features'] as $feat): ?>
                            <li><?php echo $feat; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="card-price-row">
                        <div class="card-price">
                            <span class="price-label">ราคาเช่า/ซื้อขาด</span>
                            <span class="price-val"><?php echo number_format($cat['price']); ?> ฿</span>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="add_cat">
                            <input type="hidden" name="cat_id" value="<?php echo $cat['id']; ?>">
                            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.2rem; font-size: 0.85rem;">สั่งเลี้ยง 🐾</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Programs Section -->
<div class="catalog-item-group" id="program-group" style="<?php echo ($filter === 'all' || $filter === 'program') ? '' : 'display:none;'; ?>">
    <h2 class="catalog-section-title" style="margin-top: 5rem;">💻 บริการพัฒนาซอฟต์แวร์ตามขนาดขอบเขต (Programming Scopes)</h2>
    <div class="cards-grid">
        <?php foreach ($programs as $key => $prog): ?>
            <div class="glass-card pink-accent">
                <div class="card-top">
                    <div class="card-icon-wrapper">
                        <?php 
                        if ($key == 'prog_small') echo '⚙️';
                        elseif ($key == 'prog_medium') echo '🖥️';
                        else echo '🏢';
                        ?>
                    </div>
                    <h3 class="card-title"><?php echo $prog['name']; ?></h3>
                    <p class="card-desc"><?php echo $prog['description']; ?></p>
                </div>
                
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.8rem; font-size: 0.85rem;">
                        <span style="color: var(--text-muted);">ระยะเวลาส่งมอบ:</span>
                        <span style="color: var(--accent-pink); font-weight: 700;"><?php echo $prog['duration']; ?></span>
                    </div>
                    
                    <h4 style="font-size: 0.85rem; color: var(--accent-pink); font-family: 'Orbitron'; margin-bottom: 0.5rem; text-transform: uppercase;">ขอบเขตงานมาตรฐาน</h4>
                    <ul class="card-features" style="margin-bottom: 1.5rem;">
                        <?php foreach ($prog['details'] as $det): ?>
                            <li><?php echo $det; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="card-price-row">
                        <div class="card-price">
                            <span class="price-label">ราคาเริ่มต้น</span>
                            <span class="price-val"><?php echo number_format($prog['price']); ?> ฿</span>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="add_program">
                            <input type="hidden" name="program_id" value="<?php echo $prog['id']; ?>">
                            <button type="submit" class="btn btn-danger" style="padding: 0.6rem 1.2rem; font-size: 0.85rem;">สั่งทำโปรแกรม 💻</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Dynamic Calculator Form -->
    <div class="configurator-wrapper" id="custom-estimator">
        <div class="section-header" style="text-align: left; margin-bottom: 2rem;">
            <h2 class="section-title" style="font-size: 1.8rem; background: var(--gradient-cyan); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">⚡ เครื่องคำนวณประเมินงบประมาณตามความต้องการ</h2>
            <p class="section-subtitle">เลือกและปรับแต่งขนาดฟังก์ชันเพิ่มเติมเพื่อประกอบการส่งสเปกงานลงตะกร้า</p>
        </div>
        
        <form method="POST" action="" id="calculator-form">
            <input type="hidden" name="action" value="add_custom">
            <input type="hidden" name="custom_price" id="form-custom-price" value="5000">
            <input type="hidden" name="custom_notes" id="form-custom-notes" value="">

            <div class="configurator-grid">
                <!-- Inputs Section -->
                <div class="configurator-options">
                    <div class="form-group">
                        <label for="base_scale">1. เลือกขนาดขอบเขตโครงงานพื้นฐาน (Base Scale)</label>
                        <select name="base_scale" id="base_scale" onchange="calculatePrice()">
                            <option value="prog_small" data-price="5000">Small Scope (เริ่มต้น 5,000 ฿)</option>
                            <option value="prog_medium" data-price="15000">Medium Scope (เริ่มต้น 15,000 ฿)</option>
                            <option value="prog_large" data-price="45000">Large/Enterprise Scope (เริ่มต้น 45,000 ฿)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>2. เลือกเพิ่มฟังก์ชันที่ต้องการ (Add-ons)</label>
                        <div class="checkbox-options">
                            <label class="checkbox-label">
                                <input type="checkbox" id="opt_auth" value="3000" onchange="calculatePrice()">
                                <span>ระบบล็อกอิน & สิทธิ์ผู้ใช้ (+3,000 ฿)</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" id="opt_db" value="4000" onchange="calculatePrice()">
                                <span>จัดการฐานข้อมูล MySQL (+4,000 ฿)</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" id="opt_pay" value="5000" onchange="calculatePrice()">
                                <span>เชื่อมช่องทางชำระเงิน (+5,000 ฿)</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" id="opt_express" value="3000" onchange="calculatePrice()">
                                <span>งานด่วน (จัดทำครึ่งเวลา) (+3,000 ฿)</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="user_req">3. รายละเอียดความต้องการเพิ่มเติม (User Requirements)</label>
                        <textarea name="user_req" id="user_req" rows="3" placeholder="ระบุการทำงานของโปรแกรมที่อยากได้ เช่น บอทสำหรับสุ่มแจกของรางวัล, ระบบจองโต๊ะร้านอาหารพร้อมแจ้งเตือนไลน์ ฯลฯ" oninput="calculatePrice()"></textarea>
                    </div>
                </div>

                <!-- Preview Invoice Box -->
                <div>
                    <div class="configurator-preview">
                        <div>
                            <div class="preview-header">
                                <h3 class="preview-title">สรุปราคางานสั่งทำ</h3>
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">รายละเอียดความต้องการจะถูกแนบไปกับระบบหลังสั่งซื้อ</p>
                            </div>
                            <ul class="preview-list">
                                <li>
                                    <span>ขอบเขตตั้งต้น:</span>
                                    <span class="cost-val" id="lbl-base">5,000 ฿</span>
                                </li>
                                <li id="row-auth" style="display: none;">
                                    <span>ระบบสมาชิกและความปลอดภัย:</span>
                                    <span class="cost-val">+3,000 ฿</span>
                                </li>
                                <li id="row-db" style="display: none;">
                                    <span>จัดการฐานข้อมูลภายนอก:</span>
                                    <span class="cost-val">+4,000 ฿</span>
                                </li>
                                <li id="row-pay" style="display: none;">
                                    <span>เชื่อมเกตเวย์ชำระเงิน:</span>
                                    <span class="cost-val">+5,000 ฿</span>
                                </li>
                                <li id="row-express" style="display: none;">
                                    <span>จัดส่งด่วนพิเศษ (Express):</span>
                                    <span class="cost-val">+3,000 ฿</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div>
                            <div class="preview-total">
                                <span class="total-title">ยอดรวมประเมิน:</span>
                                <span class="total-price" id="lbl-total">5,000 ฿</span>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width: 100%;">หยิบใส่ตะกร้า (สเปกพิเศษ) 🛒</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function filterCatalog(type) {
    // URL fallback
    window.history.pushState(null, '', 'products.php?filter=' + type);
    
    // UI toggle
    const catGroup = document.getElementById('cat-group');
    const programGroup = document.getElementById('program-group');
    
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    if (type === 'all') {
        catGroup.style.display = 'block';
        programGroup.style.display = 'block';
        event.currentTarget.classList.add('active');
    } else if (type === 'cat') {
        catGroup.style.display = 'block';
        programGroup.style.display = 'none';
        event.currentTarget.classList.add('active');
    } else if (type === 'program') {
        catGroup.style.display = 'none';
        programGroup.style.display = 'block';
        event.currentTarget.classList.add('active');
    }
}
</script>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
