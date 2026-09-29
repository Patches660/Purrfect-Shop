<?php
require_once __DIR__ . '/header.php';
?>

<div class="container" style="max-width: 1100px; margin: 2rem auto 5rem auto; padding: 0 1rem;">
    <!-- Hero Banner -->
    <div class="calc-hero-banner" style="background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 50%, #FED7AA 100%); border-radius: 28px; padding: 3rem 2rem; text-align: center; border: 2px solid #FDBA74; box-shadow: 0 12px 35px rgba(234, 88, 12, 0.12); margin-bottom: 3rem;">
        <div style="display: inline-flex; align-items: center; gap: 8px; background: #EA580C; color: #fff; padding: 0.45rem 1.25rem; border-radius: 999px; font-weight: 800; font-size: 0.9rem; margin-bottom: 1rem; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);">
            🧮 SMART CAT EXPENSE CALCULATOR
        </div>
        <h1 class="calc-main-title" style="font-size: 2.5rem; font-weight: 900; color: #7C2D12; margin-bottom: 0.8rem;">
            เครื่องคำนวณค่าเลี้ยงดูน้องแมว 🐾💰
        </h1>
        <p class="calc-sub-desc" style="font-size: 1.15rem; color: #9A3412; max-width: 760px; margin: 0 auto; line-height: 1.6;">
            วางแผนงบประมาณการดูแลเจ้านายอย่างมั่นใจ คำนวณค่าอาหาร ทรายแมว วัคซีน และของเล่นต่อเดือนแบบเรียลไทม์
        </p>
    </div>

    <!-- Main Interactive Grid (Inputs on Left, Live Results on Right) -->
    <div class="calc-main-grid" style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 2.5rem; align-items: start;">
        <!-- Left: Options & Toggles -->
        <div class="calc-options-card" style="background: #fff; border-radius: 26px; padding: 2.2rem; border: 2px solid #E2E8F0; box-shadow: 0 10px 30px rgba(0,0,0,0.06);">
            <h2 style="font-size: 1.35rem; font-weight: 900; color: #1E293B; margin-bottom: 1.8rem; display: flex; align-items: center; gap: 8px;">
                ⚙️ ปรับแต่งแผนการเลี้ยงดูของท่าน
            </h2>

            <!-- 1. Cat Breed Size -->
            <div style="margin-bottom: 1.8rem;">
                <label style="display: block; font-weight: 800; color: #0F172A; font-size: 0.95rem; margin-bottom: 0.6rem;">
                    1. ขนาด & สายพันธุ์น้องแมว 🐱
                </label>
                <div class="opt-3col-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.6rem;">
                    <button type="button" class="opt-btn active" data-group="size" data-val="1.0" onclick="selectOpt(this, 'size', 1.0)">
                        <div style="font-size: 1.3rem;">🐱</div>
                        <div style="font-weight: 800; font-size: 0.85rem;">ขนาดกลาง (4kg)</div>
                        <div style="font-size: 0.75rem; color: #64748B;">บริติช / เปอร์เซีย</div>
                    </button>
                    <button type="button" class="opt-btn" data-group="size" data-val="1.2" onclick="selectOpt(this, 'size', 1.2)">
                        <div style="font-size: 1.3rem;">💎</div>
                        <div style="font-weight: 800; font-size: 0.85rem;">ขนาดใหญ่ (6kg)</div>
                        <div style="font-size: 0.75rem; color: #64748B;">แร็กดอลล์ / เบงกอล</div>
                    </button>
                    <button type="button" class="opt-btn" data-group="size" data-val="1.6" onclick="selectOpt(this, 'size', 1.6)">
                        <div style="font-size: 1.3rem;">🦁</div>
                        <div style="font-weight: 800; font-size: 0.85rem;">ยักษ์ใหญ่ (9kg+)</div>
                        <div style="font-size: 0.75rem; color: #64748B;">เมนคูน ไจแอนท์</div>
                    </button>
                </div>
            </div>

            <!-- 2. Food Quality -->
            <div style="margin-bottom: 1.8rem;">
                <label style="display: block; font-weight: 800; color: #0F172A; font-size: 0.95rem; margin-bottom: 0.6rem;">
                    2. เกรดอาหาร & โภชนาการ 🥩
                </label>
                <div style="display: grid; grid-template-columns: 1fr; gap: 0.6rem;">
                    <label class="radio-card" style="display: flex; justify-content: space-between; align-items: center; padding: 0.9rem 1.2rem; border-radius: 14px; border: 2px solid #CBD5E1; cursor: pointer; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="radio" name="food" value="650" checked onchange="calcExpenses()">
                            <div>
                                <strong style="color: #1E293B; font-size: 0.95rem;">เกรดพรีเมียมมาตรฐาน (Premium Kibble)</strong>
                                <div style="font-size: 0.8rem; color: #64748B;">อาหารเม็ดคุณภาพสูงสูตรควบคุมโซเดียม</div>
                            </div>
                        </div>
                        <span style="font-weight: 800; color: #EA580C; font-size: 0.9rem; white-space: nowrap;">~650 ฿/ด.</span>
                    </label>

                    <label class="radio-card" style="display: flex; justify-content: space-between; align-items: center; padding: 0.9rem 1.2rem; border-radius: 14px; border: 2px solid #CBD5E1; cursor: pointer; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="radio" name="food" value="1300" onchange="calcExpenses()">
                            <div>
                                <strong style="color: #1E293B; font-size: 0.95rem;">เกรนฟรี + อาหารเปียก (Grain-Free & Canned)</strong>
                                <div style="font-size: 0.8rem; color: #64748B;">ไร้ธัญพืช บำรุงขนแน่นเงางาม เสริมน้ำในร่างกาย</div>
                            </div>
                        </div>
                        <span style="font-weight: 800; color: #EA580C; font-size: 0.9rem; white-space: nowrap;">~1,300 ฿/ด.</span>
                    </label>

                    <label class="radio-card" style="display: flex; justify-content: space-between; align-items: center; padding: 0.9rem 1.2rem; border-radius: 14px; border: 2px solid #CBD5E1; cursor: pointer; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="radio" name="food" value="2400" onchange="calcExpenses()">
                            <div>
                                <strong style="color: #1E293B; font-size: 0.95rem;">บาร์ฟสด & Freeze Dried (Raw Gourmet)</strong>
                                <div style="font-size: 0.8rem; color: #64748B;">เนื้อสดเกรดมนุษย์ทาน โปรตีนสด 100% ตัวแน่น</div>
                            </div>
                        </div>
                        <span style="font-weight: 800; color: #EA580C; font-size: 0.9rem; white-space: nowrap;">~2,400 ฿/ด.</span>
                    </label>
                </div>
            </div>

            <!-- 3. Cat Litter -->
            <div style="margin-bottom: 1.8rem;">
                <label style="display: block; font-weight: 800; color: #0F172A; font-size: 0.95rem; margin-bottom: 0.6rem;">
                    3. ชนิดทรายแมว 🪴
                </label>
                <div class="opt-3col-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.6rem;">
                    <button type="button" class="opt-btn active" data-group="litter" data-val="280" onclick="selectOpt(this, 'litter', 280)">
                        <div style="font-weight: 800; font-size: 0.85rem;">ทรายภูเขาไฟ</div>
                        <div style="font-size: 0.75rem; color: #64748B;">280 ฿/ด.</div>
                    </button>
                    <button type="button" class="opt-btn" data-group="litter" data-val="450" onclick="selectOpt(this, 'litter', 450)">
                        <div style="font-weight: 800; font-size: 0.85rem;">ทรายเต้าหู้ ทิ้งชักโครก</div>
                        <div style="font-size: 0.75rem; color: #64748B;">450 ฿/ด.</div>
                    </button>
                    <button type="button" class="opt-btn" data-group="litter" data-val="350" onclick="selectOpt(this, 'litter', 350)">
                        <div style="font-weight: 800; font-size: 0.85rem;">ทรายไม้สน Organic</div>
                        <div style="font-size: 0.75rem; color: #64748B;">350 ฿/ด.</div>
                    </button>
                </div>
            </div>

            <!-- 4. Health & Wellness Toggles -->
            <div style="margin-bottom: 1.8rem;">
                <label style="display: block; font-weight: 800; color: #0F172A; font-size: 0.95rem; margin-bottom: 0.6rem;">
                    4. การดูแลสุขภาพ & กรูมมิ่ง 🩺
                </label>
                <div style="display: grid; gap: 0.6rem;">
                    <label class="calc-checkbox-item" style="display: flex; align-items: center; justify-content: space-between; background: #F8FAFC; padding: 0.75rem 1.2rem; border-radius: 12px; border: 1.5px solid #E2E8F0; cursor: pointer; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" id="chk_vet" checked value="350" onchange="calcExpenses()" style="width: 18px; height: 18px; flex-shrink: 0;">
                            <span style="font-size: 0.9rem; font-weight: 700; color: #1E293B;">หยอดยาป้องกันเห็บหมัด/พยาธิหนอนหัวใจ (Revolution)</span>
                        </div>
                        <span style="font-size: 0.85rem; font-weight: 800; color: #0284C7; white-space: nowrap;">+350 ฿/ด.</span>
                    </label>

                    <label class="calc-checkbox-item" style="display: flex; align-items: center; justify-content: space-between; background: #F8FAFC; padding: 0.75rem 1.2rem; border-radius: 12px; border: 1.5px solid #E2E8F0; cursor: pointer; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" id="chk_spa" value="600" onchange="calcExpenses()" style="width: 18px; height: 18px; flex-shrink: 0;">
                            <span style="font-size: 0.9rem; font-weight: 700; color: #1E293B;">อาบน้ำตัดขน สปาแมวเดือนละ 1 ครั้ง</span>
                        </div>
                        <span style="font-size: 0.85rem; font-weight: 800; color: #0284C7; white-space: nowrap;">+600 ฿/ด.</span>
                    </label>

                    <label class="calc-checkbox-item" style="display: flex; align-items: center; justify-content: space-between; background: #F8FAFC; padding: 0.75rem 1.2rem; border-radius: 12px; border: 1.5px solid #E2E8F0; cursor: pointer; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" id="chk_ins" value="450" onchange="calcExpenses()" style="width: 18px; height: 18px; flex-shrink: 0;">
                            <span style="font-size: 0.9rem; font-weight: 700; color: #1E293B;">ประกันภัยสุขภาพสัตว์เลี้ยง (Pet Insurance)</span>
                        </div>
                        <span style="font-size: 0.85rem; font-weight: 800; color: #0284C7; white-space: nowrap;">+450 ฿/ด.</span>
                    </label>
                </div>
            </div>

            <!-- 5. Toys & Snack Slider -->
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                    <label style="font-weight: 800; color: #0F172A; font-size: 0.95rem;">5. ขนม & ของเล่นรายเดือน 🧸</label>
                    <span id="toy-val-txt" style="font-weight: 800; color: #EA580C; font-size: 0.95rem;">300 บาท</span>
                </div>
                <input type="range" id="toy_slider" min="100" max="1500" step="50" value="300" oninput="updateToyTxt(this.value)" style="width: 100%; accent-color: #EA580C;">
            </div>
        </div>

        <!-- Right: Live Result Card -->
        <div class="calc-result-card" style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); color: #fff; border-radius: 28px; padding: 2.5rem; box-shadow: 0 15px 45px rgba(15, 23, 42, 0.25); border: 2px solid #334155; position: sticky; top: 100px;">
            <div style="display: inline-block; background: rgba(234, 88, 12, 0.25); color: #FB923C; border: 1px solid #EA580C; padding: 0.3rem 0.8rem; border-radius: 999px; font-weight: 800; font-size: 0.8rem; margin-bottom: 1rem;">
                📊 สรุปผลการประมาณการค่าใช้จ่าย
            </div>

            <div style="border-bottom: 1.5px solid #334155; padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
                <div style="font-size: 0.9rem; color: #94A3B8;">ค่าใช้จ่ายเฉลี่ยต่อเดือน</div>
                <div style="font-size: 3.2rem; font-weight: 900; color: #FDBA74; line-height: 1.1;">
                    <span id="res_monthly">1,580</span> <span style="font-size: 1.4rem; color: #CBD5E1;">บาท/เดือน</span>
                </div>
            </div>

            <!-- Breakdown Bars -->
            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.85rem; font-weight: 800; color: #E2E8F0; margin-bottom: 0.8rem;">สัดส่วนค่าใช้จ่ายต่อเดือน:</div>
                
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.3rem;">
                    <span style="color: #94A3B8;">🥩 ค่าอาหาร:</span>
                    <strong id="res_food_txt" style="color: #FDBA74;">650 ฿</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.3rem;">
                    <span style="color: #94A3B8;">🪴 ค่าทรายแมว:</span>
                    <strong id="res_litter_txt" style="color: #6EE7B7;">280 ฿</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.3rem;">
                    <span style="color: #94A3B8;">🩺 สุขภาพ & สปา:</span>
                    <strong id="res_health_txt" style="color: #93C5FD;">350 ฿</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.3rem;">
                    <span style="color: #94A3B8;">🧸 ขนม & ของเล่น:</span>
                    <strong id="res_toy_txt" style="color: #F472B6;">300 ฿</strong>
                </div>
            </div>

            <!-- Annual & Lifetime -->
            <div style="background: rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 1.2rem; margin-bottom: 1.8rem; border: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #CBD5E1; font-size: 0.9rem;">📅 รวมทั้งปี (12 เดือน):</span>
                    <strong id="res_yearly" style="font-size: 1.15rem; color: #FDE047;">18,960 ฿</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #CBD5E1; font-size: 0.9rem;">🏡 ตลอดอายุขัย (15 ปี):</span>
                    <strong id="res_lifetime" style="font-size: 1.15rem; color: #6EE7B7;">284,400 ฿</strong>
                </div>
            </div>

            <div style="display: grid; gap: 0.8rem;">
                <a href="products.html" class="btn btn-primary" style="text-align: center; padding: 0.95rem; font-weight: 900; border-radius: 14px; background: linear-gradient(135deg, #EA580C 0%, #F97316 100%); border-color: #EA580C;">
                    🐱 เลือกชมน้องแมวพร้อมย้ายบ้าน >
                </a>
                <a href="welcome_deal.html" class="btn btn-secondary" style="text-align: center; padding: 0.85rem; font-weight: 800; border-radius: 14px; background: rgba(255,255,255,0.1); color: #fff; border-color: rgba(255,255,255,0.2);">
                    🎁 รับ Starter Kit ของแถม 2,500.- ฟรี
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.opt-btn {
    background: #fff;
    border: 2px solid #CBD5E1;
    border-radius: 14px;
    padding: 0.8rem 0.5rem;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s ease;
}
.opt-btn.active {
    border-color: #EA580C;
    background: #FFF7ED;
    box-shadow: 0 4px 12px rgba(234, 88, 12, 0.15);
}

@media (max-width: 900px) {
    .calc-main-grid {
        grid-template-columns: 1fr !important;
        gap: 1.5rem !important;
    }
    .calc-result-card {
        position: static !important;
    }
}

@media (max-width: 768px) {
    .calc-hero-banner {
        padding: 2rem 1.2rem !important;
        border-radius: 20px !important;
    }
    .calc-main-title {
        font-size: 1.8rem !important;
    }
    .calc-sub-desc {
        font-size: 1rem !important;
    }
    .calc-options-card, .calc-result-card {
        padding: 1.5rem !important;
        border-radius: 20px !important;
    }
}

@media (max-width: 480px) {
    .opt-3col-grid {
        grid-template-columns: 1fr !important;
    }
    .calc-checkbox-item {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
}
</style>

<script>
let selectedSizeMultiplier = 1.0;
let selectedLitterCost = 280;

function selectOpt(btn, group, val) {
    document.querySelectorAll(`.opt-btn[data-group="${group}"]`).forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (group === 'size') selectedSizeMultiplier = parseFloat(val);
    if (group === 'litter') selectedLitterCost = parseInt(val);
    calcExpenses();
}

function updateToyTxt(val) {
    document.getElementById('toy-val-txt').innerText = parseInt(val).toLocaleString() + ' บาท';
    calcExpenses();
}

function calcExpenses() {
    const foodBase = parseInt(document.querySelector('input[name="food"]:checked').value);
    const foodTotal = Math.round(foodBase * selectedSizeMultiplier);
    const litterTotal = Math.round(selectedLitterCost * (selectedSizeMultiplier > 1.2 ? 1.3 : 1.0));

    let healthTotal = 0;
    if (document.getElementById('chk_vet').checked) healthTotal += parseInt(document.getElementById('chk_vet').value);
    if (document.getElementById('chk_spa').checked) healthTotal += parseInt(document.getElementById('chk_spa').value);
    if (document.getElementById('chk_ins').checked) healthTotal += parseInt(document.getElementById('chk_ins').value);

    const toyTotal = parseInt(document.getElementById('toy_slider').value);

    const monthlyTotal = foodTotal + litterTotal + healthTotal + toyTotal;
    const yearlyTotal = monthlyTotal * 12;
    const lifetimeTotal = yearlyTotal * 15;

    document.getElementById('res_monthly').innerText = monthlyTotal.toLocaleString();
    document.getElementById('res_yearly').innerText = yearlyTotal.toLocaleString() + ' ฿';
    document.getElementById('res_lifetime').innerText = lifetimeTotal.toLocaleString() + ' ฿';

    document.getElementById('res_food_txt').innerText = foodTotal.toLocaleString() + ' ฿';
    document.getElementById('res_litter_txt').innerText = litterTotal.toLocaleString() + ' ฿';
    document.getElementById('res_health_txt').innerText = healthTotal.toLocaleString() + ' ฿';
    document.getElementById('res_toy_txt').innerText = toyTotal.toLocaleString() + ' ฿';
}

document.addEventListener('DOMContentLoaded', calcExpenses);
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
