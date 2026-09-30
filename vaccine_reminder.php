<?php
require_once __DIR__ . '/header.php';

// Fetch adopted cats for logged-in user
$currUser = getCurrentUser();
$user_orders = [];
if ($currUser) {
    $user_orders = getUserOrders($currUser['email']);
    if (empty($user_orders)) $user_orders = getUserOrders($currUser['username']);
    if (empty($user_orders)) $user_orders = getUserOrders($currUser['id']);
}

$adoptedCats = [];
foreach ($user_orders as $ord) {
    foreach ($ord['items'] ?? [] as $it) {
        $adoptedCats[] = [
            'id' => $it['id'] ?? '',
            'name' => $it['name'] ?? 'น้องแมว',
            'breed' => $it['breed'] ?? 'สายพันธุ์แท้',
            'image' => $it['image'] ?? 'cat_british.jpg',
            'age' => $it['age'] ?? '2.5 เดือน',
            'gender' => $it['gender'] ?? 'ไม่ระบุ',
            'order_id' => $ord['order_id'] ?? '',
            'created_at' => $ord['created_at'] ?? ''
        ];
    }
}
?>

<div class="container" style="max-width: 1150px; margin: 2rem auto 5rem auto; padding: 0 1rem;">
    <!-- Hero Banner -->
    <div class="health-hero-banner" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 50%, #A7F3D0 100%); border-radius: 28px; padding: 3rem 2rem; text-align: center; border: 2px solid #6EE7B7; box-shadow: 0 12px 35px rgba(16, 185, 129, 0.12); margin-bottom: 2rem;">
        <div style="display: inline-flex; align-items: center; gap: 8px; background: #059669; color: #fff; padding: 0.45rem 1.25rem; border-radius: 999px; font-weight: 800; font-size: 0.9rem; margin-bottom: 1rem; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);">
            🩺 CAT HEALTH & VACCINE TRACKER
        </div>
        <h1 class="health-main-title" style="font-size: 2.5rem; font-weight: 900; color: #064E3B; margin-bottom: 0.8rem;">
            ระบบบันทึกสุขภาพ & ตารางนัดฉีดวัคซีนน้องแมว 🐾💉
        </h1>
        <p class="health-sub-desc" style="font-size: 1.15rem; color: #047857; max-width: 780px; margin: 0 auto; line-height: 1.6;">
            วางแผนและบันทึกประวัติสุขภาพของเจ้านาย คำนวณตารางวัคซีนมาตรฐานสากล ถ่ายพยาธิ และหยอดยาป้องกันเห็บหมัดตามวันเกิดอัตโนมัติ
        </p>
    </div>

    <!-- Adopted Cats Showcase Section -->
    <div class="adopted-cats-wrapper" style="background: #FFFFFF; border: 2px solid #E2E8F0; border-radius: 24px; padding: 1.8rem; margin-bottom: 2.5rem; box-shadow: 0 8px 25px rgba(0,0,0,0.04);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; gap: 10px;">
            <div>
                <h3 style="font-size: 1.25rem; font-weight: 900; color: #1E293B; margin: 0; display: flex; align-items: center; gap: 8px;">
                    🐾 น้องแมวของคุณ (คลิกเพื่อโหลดตารางวัคซีนทันที)
                </h3>
                <p style="font-size: 0.85rem; color: #64748B; margin: 3px 0 0 0;">
                    เลือกน้องแมวที่เคยสั่งซื้อเพื่อกรอกข้อมูลและคำนวณวันนัดหมายอัตโนมัติ
                </p>
            </div>
            <a href="profile.php?tab=orders" class="btn btn-secondary btn-sm" style="font-size: 0.8rem; font-weight: 700; padding: 0.45rem 1rem; border-radius: 10px; text-decoration: none;">
                📦 ดูประวัติคำสั่งซื้อทั้งหมด
            </a>
        </div>

        <!-- Cats Grid / Carousel -->
        <div id="adoptedCatsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 1rem;">
            <?php if (!empty($adoptedCats)): ?>
                <?php foreach ($adoptedCats as $idx => $cat): ?>
                    <div class="adopted-cat-card" 
                         onclick="selectAdoptedCat('<?php echo htmlspecialchars(addslashes($cat['name'])); ?>', '<?php echo htmlspecialchars(addslashes($cat['breed'])); ?>', '<?php echo htmlspecialchars(addslashes($cat['age'])); ?>', 'assets/images/<?php echo htmlspecialchars($cat['image']); ?>', this)"
                         style="background: #F8FAFC; border: 2px solid #E2E8F0; border-radius: 16px; padding: 1rem; cursor: pointer; transition: all 0.25s ease; display: flex; align-items: center; gap: 12px; position: relative;">
                        <img src="assets/images/<?php echo htmlspecialchars($cat['image']); ?>" 
                             alt="<?php echo htmlspecialchars($cat['name']); ?>" 
                             style="width: 58px; height: 58px; border-radius: 12px; object-fit: cover; border: 2px solid #10B981; flex-shrink: 0;">
                        <div style="overflow: hidden; flex: 1;">
                            <strong style="display: block; font-size: 0.95rem; color: #1E293B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </strong>
                            <span style="display: inline-block; font-size: 0.72rem; background: #D1FAE5; color: #065F46; font-weight: 800; padding: 2px 7px; border-radius: 999px; margin-top: 2px;">
                                <?php echo htmlspecialchars($cat['breed']); ?>
                            </span>
                            <div style="font-size: 0.72rem; color: #64748B; margin-top: 3px;">
                                📅 ออเดอร์: <?php echo htmlspecialchars($cat['order_id']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback Mock Cards for Demo / Guest -->
                <div class="adopted-cat-card active-cat-card" 
                     onclick="selectAdoptedCat('น้องปุยหิมะ', 'Persian', '3 เดือน', 'assets/images/cat_persian.jpg', this)"
                     style="background: #ECFDF5; border: 2px solid #10B981; border-radius: 16px; padding: 1rem; cursor: pointer; transition: all 0.25s ease; display: flex; align-items: center; gap: 12px;">
                    <img src="assets/images/cat_persian.jpg" alt="น้องปุยหิมะ" style="width: 58px; height: 58px; border-radius: 12px; object-fit: cover; border: 2px solid #10B981; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <strong style="display: block; font-size: 0.95rem; color: #064E3B;">น้องปุยหิมะ</strong>
                        <span style="display: inline-block; font-size: 0.72rem; background: #10B981; color: #fff; font-weight: 800; padding: 2px 7px; border-radius: 999px; margin-top: 2px;">
                            เปอร์เซีย (Persian)
                        </span>
                        <div style="font-size: 0.72rem; color: #059669; margin-top: 3px;">✨ แมวตัวอย่าง (คลิกเพื่อดู)</div>
                    </div>
                </div>

                <div class="adopted-cat-card" 
                     onclick="selectAdoptedCat('น้องคอตตอน', 'Ragdoll', '2.5 เดือน', 'assets/images/cat_ragdoll.jpg', this)"
                     style="background: #F8FAFC; border: 2px solid #E2E8F0; border-radius: 16px; padding: 1rem; cursor: pointer; transition: all 0.25s ease; display: flex; align-items: center; gap: 12px;">
                    <img src="assets/images/cat_ragdoll.jpg" alt="น้องคอตตอน" style="width: 58px; height: 58px; border-radius: 12px; object-fit: cover; border: 2px solid #CBD5E1; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <strong style="display: block; font-size: 0.95rem; color: #1E293B;">น้องคอตตอน</strong>
                        <span style="display: inline-block; font-size: 0.72rem; background: #EDE9FE; color: #6D28D9; font-weight: 800; padding: 2px 7px; border-radius: 999px; margin-top: 2px;">
                            แร็กดอลล์ (Ragdoll)
                        </span>
                        <div style="font-size: 0.72rem; color: #64748B; margin-top: 3px;">✨ แมวตัวอย่าง (คลิกเพื่อดู)</div>
                    </div>
                </div>

                <div class="adopted-cat-card" 
                     onclick="selectAdoptedCat('น้องการ์ฟิลด์', 'American Shorthair', '2.5 เดือน', 'assets/images/cat_americanshorthair.jpg', this)"
                     style="background: #F8FAFC; border: 2px solid #E2E8F0; border-radius: 16px; padding: 1rem; cursor: pointer; transition: all 0.25s ease; display: flex; align-items: center; gap: 12px;">
                    <img src="assets/images/cat_americanshorthair.jpg" alt="น้องการ์ฟิลด์" style="width: 58px; height: 58px; border-radius: 12px; object-fit: cover; border: 2px solid #CBD5E1; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <strong style="display: block; font-size: 0.95rem; color: #1E293B;">น้องการ์ฟิลด์</strong>
                        <span style="display: inline-block; font-size: 0.72rem; background: #FEF3C7; color: #92400E; font-weight: 800; padding: 2px 7px; border-radius: 999px; margin-top: 2px;">
                            American Shorthair
                        </span>
                        <div style="font-size: 0.72rem; color: #64748B; margin-top: 3px;">✨ แมวตัวอย่าง (คลิกเพื่อดู)</div>
                    </div>
                </div>

                <div class="adopted-cat-card" 
                     onclick="selectAdoptedCat('น้องมิลค์กี้', 'Khao Manee', '2 เดือน', 'assets/images/cat_khao_manee.jpg', this)"
                     style="background: #F8FAFC; border: 2px solid #E2E8F0; border-radius: 16px; padding: 1rem; cursor: pointer; transition: all 0.25s ease; display: flex; align-items: center; gap: 12px;">
                    <img src="assets/images/cat_khao_manee.jpg" alt="น้องมิลค์กี้" style="width: 58px; height: 58px; border-radius: 12px; object-fit: cover; border: 2px solid #CBD5E1; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <strong style="display: block; font-size: 0.95rem; color: #1E293B;">น้องมิลค์กี้</strong>
                        <span style="display: inline-block; font-size: 0.72rem; background: #DBEAFE; color: #1E40AF; font-weight: 800; padding: 2px 7px; border-radius: 999px; margin-top: 2px;">
                            ขาวมณี (Khao Manee)
                        </span>
                        <div style="font-size: 0.72rem; color: #64748B; margin-top: 3px;">✨ แมวตัวอย่าง (คลิกเพื่อดู)</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Grid (Profile Form Left, Interactive Timeline Right) -->
    <div class="health-main-grid" style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 2rem; align-items: start;">
        
        <!-- Left: Cat Health Profile Form -->
        <div style="background: #fff; border-radius: 24px; padding: 2rem; border: 2px solid #E2E8F0; box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: sticky; top: 1rem;">
            
            <!-- Active Cat Photo Showcase -->
            <div id="activeCatPreviewBox" style="display: flex; align-items: center; gap: 12px; background: #F0FDF4; border: 1.5px solid #BBF7D0; border-radius: 16px; padding: 0.9rem 1.1rem; margin-bottom: 1.5rem;">
                <img id="selectedCatImg" src="assets/images/cat_persian.jpg" alt="Selected Cat" style="width: 60px; height: 60px; border-radius: 12px; object-fit: cover; border: 2px solid #10B981; box-shadow: 0 4px 10px rgba(16,185,129,0.2);">
                <div>
                    <span style="font-size: 0.75rem; color: #047857; font-weight: 800; text-transform: uppercase;">กำลังคำนวณให้น้อง:</span>
                    <strong id="selectedCatNameLabel" style="font-size: 1.1rem; color: #064E3B; display: block; line-height: 1.2;">น้องปุยหิมะ</strong>
                    <span id="selectedCatBreedLabel" style="font-size: 0.8rem; color: #059669; font-weight: 700;">เปอร์เซีย (Persian)</span>
                </div>
            </div>

            <h2 style="font-size: 1.25rem; font-weight: 900; color: #1E293B; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 8px;">
                📝 ข้อมูลและวันเกิดน้องแมว
            </h2>

            <form id="catHealthForm" onsubmit="calculateVaccineSchedule(event)">
                <div style="margin-bottom: 1.1rem;">
                    <label style="display: block; font-weight: 800; font-size: 0.85rem; color: #334155; margin-bottom: 0.4rem;">
                        ชื่อน้องแมว (Cat Name) *
                    </label>
                    <input type="text" id="catNameInput" class="form-control" placeholder="เช่น น้องโมจิ, มีมี่..." required value="น้องปุยหิมะ" style="width: 100%; padding: 0.65rem 0.9rem; border-radius: 12px; border: 1.5px solid #CBD5E1; font-weight: 700;">
                </div>

                <div style="margin-bottom: 1.1rem;">
                    <label style="display: block; font-weight: 800; font-size: 0.85rem; color: #334155; margin-bottom: 0.4rem;">
                        สายพันธุ์น้องแมว (Breed)
                    </label>
                    <select id="catBreedSelect" class="form-control" style="width: 100%; padding: 0.65rem 0.9rem; border-radius: 12px; border: 1.5px solid #CBD5E1; font-weight: 700;">
                        <option value="Persian">Persian (เปอร์เซีย)</option>
                        <option value="British Shorthair">British Shorthair (บริติช ช็อตแฮร์)</option>
                        <option value="Ragdoll">Ragdoll (แร็กดอลล์)</option>
                        <option value="Scottish Fold">Scottish Fold (สก็อตติช โฟลด์)</option>
                        <option value="Bengal">Bengal (เบงกอล)</option>
                        <option value="Khao Manee">Khao Manee (ขาวมณี)</option>
                        <option value="American Shorthair">American Shorthair (อเมริกันช็อตแฮร์)</option>
                        <option value="Maine Coon">Maine Coon (เมนคูน)</option>
                        <option value="Siamese">Siamese (แมววิเชียรมาศ)</option>
                        <option value="Sphynx">Sphynx (สฟิงซ์)</option>
                        <option value="Other">สายพันธุ์อื่นๆ</option>
                    </select>
                </div>

                <div style="margin-bottom: 1.1rem;">
                    <label style="display: block; font-weight: 800; font-size: 0.85rem; color: #334155; margin-bottom: 0.4rem;">
                        วันเกิด / วันเริ่มนับอายุ *
                    </label>
                    <input type="date" id="catDobInput" class="form-control" required style="width: 100%; padding: 0.65rem 0.9rem; border-radius: 12px; border: 1.5px solid #CBD5E1; font-weight: 700;">
                </div>

                <div style="margin-bottom: 1.3rem;">
                    <label style="display: block; font-weight: 800; font-size: 0.85rem; color: #334155; margin-bottom: 0.4rem;">
                        น้ำหนักปัจจุบัน (กก.)
                    </label>
                    <input type="number" step="0.1" id="catWeightInput" class="form-control" placeholder="เช่น 2.5" value="2.8" style="width: 100%; padding: 0.65rem 0.9rem; border-radius: 12px; border: 1.5px solid #CBD5E1; font-weight: 700;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-weight: 800; font-size: 1rem; border-radius: 14px; background: linear-gradient(135deg, #059669 0%, #10B981 100%); border: none; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); cursor: pointer; transition: all 0.25s ease;">
                    ⚡ คำนวณตารางวัคซีนใหม่
                </button>
            </form>

            <!-- Health Progress Card -->
            <div style="margin-top: 1.8rem; padding: 1.2rem; background: #F8FAFC; border-radius: 16px; border: 1px solid #E2E8F0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
                    <span style="font-size: 0.85rem; font-weight: 800; color: #334155;">ความคืบหน้าการรับวัคซีน:</span>
                    <strong id="healthProgressVal" style="color: #059669; font-size: 0.95rem;">0%</strong>
                </div>
                <div style="width: 100%; height: 9px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                    <div id="healthProgressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #10B981, #059669); border-radius: 999px; transition: width 0.3s ease;"></div>
                </div>
                <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.5rem; text-align: center;">
                    ติ๊ก ✓ ที่รายการวัคซีนเมื่อน้องฉีดเสร็จเพื่อบันทึกประวัติ
                </div>
            </div>

            <div style="margin-top: 1.2rem; text-align: center; display: flex; flex-direction: column; gap: 8px;">
                <a href="profile.php?tab=vaccine" style="font-size: 0.82rem; font-weight: 800; color: #059669; text-decoration: none;">
                    👤 จัดการสมุดสุขภาพในโปรไฟล์ ➔
                </a>
                <a href="pedigree.php" style="font-size: 0.82rem; font-weight: 800; color: var(--primary-coral); text-decoration: none;">
                    🩺 ค้นหาใบรับรองสุขภาพ & เลขไมโครชิป ➔
                </a>
            </div>
        </div>

        <!-- Right: Vaccine Schedule Timeline -->
        <div style="background: #fff; border-radius: 24px; padding: 2.2rem; border: 2px solid #E2E8F0; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 900; color: #1E293B; margin: 0;">
                        📋 ตารางวัคซีน & แผนดูแลสุขภาพ
                    </h2>
                    <span id="timelineCatTitle" style="font-size: 0.85rem; color: #059669; font-weight: 800;">
                        (สำหรับ: น้องปุยหิมะ • เปอร์เซีย)
                    </span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm" style="font-size: 0.8rem; font-weight: 800; padding: 0.45rem 1rem; border-radius: 10px; background: #F1F5F9; color: #334155; border: 1px solid #CBD5E1;">
                        🖨️ พิมพ์สมุดวัคซีน
                    </button>
                </div>
            </div>

            <!-- Timeline Items -->
            <div id="vaccineTimelineList" style="display: grid; gap: 1.2rem;">
                <!-- Dynamically generated timeline items -->
            </div>

            <!-- Veterinary Advice Box -->
            <div style="margin-top: 2rem; background: linear-gradient(135deg, #FEF3C7 0%, #FFFBEB 100%); border: 2px solid #FCD34D; border-radius: 16px; padding: 1.2rem; display: flex; gap: 12px; align-items: start;">
                <span style="font-size: 1.8rem;">💡</span>
                <div>
                    <strong style="color: #92400E; font-size: 0.95rem;">คำแนะนำจากสัตวแพทย์ Purrfect Cattery:</strong>
                    <p style="font-size: 0.82rem; color: #B45309; margin: 4px 0 0 0; line-height: 1.5;">
                        • ก่อนพาไปฉีดวัคซีน ควรตรวจวัดไข้และให้น้องแมวมีสุขภาพสมบูรณ์แข็งแรง ไม่ถ่ายเหลว<br>
                        • งดอาบน้ำอย่างน้อย 7 วันหลังฉีดวัคซีน เพื่อป้องกันการเป็นไข้หวัด<br>
                        • ฟาร์ม Purrfect Shop มีการฉีดวัคซีนเข็มแรกและหยอดถ่ายพยาธิให้เรียบร้อยก่อนส่งมอบทุกตัว 100%
                    </p>
                </div>
            </div>

            <!-- Partner Clinic Contact -->
            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="https://line.me" target="_blank" class="btn btn-primary" style="padding: 0.85rem 2rem; font-weight: 800; font-size: 0.95rem; border-radius: 14px; background: #06C755; border: none; box-shadow: 0 4px 15px rgba(6,199,85,0.3); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: #fff;">
                    💬 ปรึกษาสัตวแพทย์ & จองคิวฉีดวัคซีนผ่าน LINE
                </a>
            </div>
        </div>

    </div>
</div>

<style>
.adopted-cat-card:hover {
    border-color: #10B981 !important;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.15);
}
.adopted-cat-card.active-cat-card {
    background: #ECFDF5 !important;
    border-color: #10B981 !important;
    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.18);
}
.timeline-card {
    border: 2px solid #E2E8F0;
    border-radius: 16px;
    padding: 1.2rem;
    background: #FAFAFA;
    transition: all 0.2s ease;
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 1rem;
    align-items: center;
}
.timeline-card.completed {
    background: #ECFDF5;
    border-color: #A7F3D0;
}
.timeline-card:hover {
    border-color: #10B981;
    box-shadow: 0 6px 15px rgba(16, 185, 129, 0.08);
}
.badge-age {
    background: #EFF6FF;
    color: #1E40AF;
    font-size: 0.75rem;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 999px;
    display: inline-block;
}
@media (max-width: 768px) {
    .health-main-grid {
        grid-template-columns: 1fr !important;
    }
    .timeline-card {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
const standardSchedule = [
    {
        id: 'vac_1',
        ageWeeks: 8,
        title: 'วัคซีนรวมแมว (FPLV/FHV/FCV) เข็มที่ 1',
        desc: 'ป้องกันโรคไข้หัดแมว, ไวรัสหลอดลมอักเสบติดต่อ และคาลิซิไวรัสในแมว',
        type: 'วัคซีนหลัก (Core Vaccine)',
        icon: '💉',
        badgeCol: '#3B82F6'
    },
    {
        id: 'vac_2',
        ageWeeks: 9,
        title: 'ถ่ายพยาธิรอบแรก & หยอดป้องกันเห็บหมัด',
        desc: 'ตรวจสุขภาพแรกเข้า ถ่ายพยาธิในทางเดินอาหาร และหยอดยา Spot-on',
        type: 'การป้องกันพื้นฐาน',
        icon: '💊',
        badgeCol: '#10B981'
    },
    {
        id: 'vac_3',
        ageWeeks: 12,
        title: 'วัคซีนรวมแมว เข็มที่ 2 + วัคซีนลิวคีเมีย (FeLV เข็ม 1)',
        desc: 'กระตุ้นภูมิคุ้มกันโรคติดต่อร้ายแรง และป้องกันโรคมะเร็งเม็ดเลือดขาวลิวคีเมีย',
        type: 'วัคซีนกระตุ้น',
        icon: '🛡️',
        badgeCol: '#8B5CF6'
    },
    {
        id: 'vac_4',
        ageWeeks: 16,
        title: 'วัคซีนป้องกันโรคพิษสุนัขบ้า (Rabies) + ลิวคีเมีย เข็ม 2',
        desc: 'ป้องกันโรคพิษสุนัขบ้าตามกฎหมาย และเสริมภูมิคุ้มกันลิวคีเมียรอบสมบูรณ์',
        type: 'วัคซีนตามกฎหมาย',
        icon: '👑',
        badgeCol: '#EA580C'
    },
    {
        id: 'vac_5',
        ageWeeks: 24,
        title: 'ตรวจสุขภาพช่วง 6 เดือน & วางแผนทำหมัน',
        desc: 'ตรวจเลือดเช็คค่าตับไต ตรวจความพร้อมร่างกาย และปรึกษาการผ่าตัดทำหมัน',
        type: 'ตรวจสุขภาพ 6 เดือน',
        icon: '🩺',
        badgeCol: '#059669'
    },
    {
        id: 'vac_6',
        ageWeeks: 52,
        title: 'วัคซีนรวม + พิษสุนัขบ้า กระตุ้นประจำปี (Annual Booster)',
        desc: 'ฉีดกระตุ้นภูมิคุ้มกันปีละ 1 ครั้ง ตลอดช่วงชีวิตของน้องแมว',
        type: 'ประจำปี (Annual)',
        icon: '🎂',
        badgeCol: '#D97706'
    }
];

// Set Default DOB to ~2.5 months ago
const defaultDob = new Date();
defaultDob.setMonth(defaultDob.getMonth() - 2);
defaultDob.setDate(defaultDob.getDate() - 15);
document.getElementById('catDobInput').value = defaultDob.toISOString().split('T')[0];

function selectAdoptedCat(name, breed, ageStr, imageSrc, cardElem) {
    // Set form fields
    document.getElementById('catNameInput').value = name;
    
    // Match breed select
    const breedSelect = document.getElementById('catBreedSelect');
    let matched = false;
    for (let i = 0; i < breedSelect.options.length; i++) {
        if (breed.toLowerCase().includes(breedSelect.options[i].value.toLowerCase()) || 
            breedSelect.options[i].text.toLowerCase().includes(breed.toLowerCase())) {
            breedSelect.selectedIndex = i;
            matched = true;
            break;
        }
    }
    if (!matched) {
        breedSelect.value = 'Other';
    }

    // Estimate DOB based on age string if possible
    let monthsAgo = 2.5;
    if (ageStr) {
        if (ageStr.includes('3')) monthsAgo = 3;
        else if (ageStr.includes('2.5')) monthsAgo = 2.5;
        else if (ageStr.includes('2')) monthsAgo = 2;
        else if (ageStr.includes('4')) monthsAgo = 4;
    }
    const catDob = new Date();
    catDob.setDate(catDob.getDate() - Math.round(monthsAgo * 30.5));
    document.getElementById('catDobInput').value = catDob.toISOString().split('T')[0];

    // Update showcase card
    if (imageSrc) {
        document.getElementById('selectedCatImg').src = imageSrc;
    }
    document.getElementById('selectedCatNameLabel').textContent = name;
    document.getElementById('selectedCatBreedLabel').textContent = breed;

    // Update active highlight on cards
    document.querySelectorAll('.adopted-cat-card').forEach(c => c.classList.remove('active-cat-card'));
    if (cardElem) {
        cardElem.classList.add('active-cat-card');
    }

    // Recalculate schedule
    calculateVaccineSchedule();
}

function calculateVaccineSchedule(e) {
    if (e) e.preventDefault();
    
    const catName = document.getElementById('catNameInput').value.trim() || 'น้องแมว';
    const catBreed = document.getElementById('catBreedSelect').value;
    const catDobStr = document.getElementById('catDobInput').value;
    
    if (!catDobStr) return;
    const dob = new Date(catDobStr);
    
    document.getElementById('timelineCatTitle').textContent = `(สำหรับ: ${catName} • ${catBreed})`;
    document.getElementById('selectedCatNameLabel').textContent = catName;
    document.getElementById('selectedCatBreedLabel').textContent = catBreed;

    // Load saved checklist from localStorage
    const savedChecks = JSON.parse(localStorage.getItem(`cat_health_${catName}`) || '{}');

    const container = document.getElementById('vaccineTimelineList');
    container.innerHTML = standardSchedule.map((item, idx) => {
        // Calculate target date
        const targetDate = new Date(dob);
        targetDate.setDate(targetDate.getDate() + (item.ageWeeks * 7));
        const formattedDate = targetDate.toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric' });
        
        const isDone = savedChecks[item.id] === true;

        return `
            <div class="timeline-card ${isDone ? 'completed' : ''}" id="card_${item.id}">
                <div style="font-size: 2rem; background: #fff; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.06); border: 1px solid #E2E8F0;">
                    ${item.icon}
                </div>

                <div>
                    <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 4px; flex-wrap: wrap;">
                        <span class="badge-age" style="background: ${item.badgeCol}15; color: ${item.badgeCol};">
                            อายุ ~${item.ageWeeks} สัปดาห์
                        </span>
                        <span style="font-size: 0.78rem; font-weight: 800; color: #64748B;">
                            📅 กำหนดนัด: ${formattedDate}
                        </span>
                    </div>
                    <strong style="font-size: 0.95rem; color: #1E293B; display: block; margin-bottom: 2px;">
                        ${item.title}
                    </strong>
                    <div style="font-size: 0.8rem; color: #64748B;">
                        ${item.desc}
                    </div>
                </div>

                <div style="text-align: right;">
                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 800; color: ${isDone ? '#059669' : '#64748B'};">
                        <input type="checkbox" onchange="toggleVaccineDone('${item.id}', '${catName}', this)" ${isDone ? 'checked' : ''} style="width: 18px; height: 18px; accent-color: #10B981; cursor: pointer;">
                        <span>${isDone ? '✓ ฉีดแล้ว' : 'รอฉีด'}</span>
                    </label>
                </div>
            </div>
        `;
    }).join('');

    updateProgress(catName);
}

function toggleVaccineDone(vacId, catName, checkbox) {
    const savedChecks = JSON.parse(localStorage.getItem(`cat_health_${catName}`) || '{}');
    savedChecks[vacId] = checkbox.checked;
    localStorage.setItem(`cat_health_${catName}`, JSON.stringify(savedChecks));

    const card = document.getElementById(`card_${vacId}`);
    if (card) {
        if (checkbox.checked) {
            card.classList.add('completed');
            checkbox.nextElementSibling.textContent = '✓ ฉีดแล้ว';
            checkbox.nextElementSibling.style.color = '#059669';
        } else {
            card.classList.remove('completed');
            checkbox.nextElementSibling.textContent = 'รอฉีด';
            checkbox.nextElementSibling.style.color = '#64748B';
        }
    }
    updateProgress(catName);
}

function updateProgress(catName) {
    const savedChecks = JSON.parse(localStorage.getItem(`cat_health_${catName}`) || '{}');
    const completedCount = standardSchedule.filter(s => savedChecks[s.id] === true).length;
    const percent = Math.round((completedCount / standardSchedule.length) * 100);

    document.getElementById('healthProgressVal').textContent = `${percent}% (${completedCount}/${standardSchedule.length})`;
    document.getElementById('healthProgressBar').style.width = `${percent}%`;
}

// Client-side LocalStorage Hydration for Adopted Cats & URL Query Params
document.addEventListener('DOMContentLoaded', () => {
    try {
        const storedOrders = JSON.parse(localStorage.getItem('cat_shop_my_orders') || '[]');
        const grid = document.getElementById('adoptedCatsGrid');
        
        if (storedOrders && storedOrders.length > 0 && grid) {
            const localCats = [];
            storedOrders.forEach(ord => {
                if (ord.items && Array.isArray(ord.items)) {
                    ord.items.forEach(it => {
                        localCats.push({
                            id: it.id,
                            name: it.name || 'น้องแมว',
                            breed: it.breed || 'สายพันธุ์แท้',
                            image: it.image || 'cat_british.jpg',
                            age: it.age || '2.5 เดือน',
                            order_id: ord.order_id || ''
                        });
                    });
                }
            });

            if (localCats.length > 0) {
                grid.innerHTML = localCats.map((cat, idx) => `
                    <div class="adopted-cat-card ${idx === 0 ? 'active-cat-card' : ''}" 
                         onclick="selectAdoptedCat('${cat.name.replace(/'/g, "\\'")}', '${cat.breed.replace(/'/g, "\\'")}', '${cat.age.replace(/'/g, "\\'")}', 'assets/images/${cat.image}', this)"
                         style="background: ${idx === 0 ? '#ECFDF5' : '#F8FAFC'}; border: 2px solid ${idx === 0 ? '#10B981' : '#E2E8F0'}; border-radius: 16px; padding: 1rem; cursor: pointer; transition: all 0.25s ease; display: flex; align-items: center; gap: 12px;">
                        <img src="assets/images/${cat.image}" 
                             alt="${cat.name}" 
                             style="width: 58px; height: 58px; border-radius: 12px; object-fit: cover; border: 2px solid #10B981; flex-shrink: 0;">
                        <div style="overflow: hidden; flex: 1;">
                            <strong style="display: block; font-size: 0.95rem; color: #1E293B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${cat.name}
                            </strong>
                            <span style="display: inline-block; font-size: 0.72rem; background: #D1FAE5; color: #065F46; font-weight: 800; padding: 2px 7px; border-radius: 999px; margin-top: 2px;">
                                ${cat.breed}
                            </span>
                            <div style="font-size: 0.72rem; color: #64748B; margin-top: 3px;">
                                📅 ออเดอร์: ${cat.order_id || 'รายการของฉัน'}
                            </div>
                        </div>
                    </div>
                `).join('');

                // Auto-select first cat
                selectAdoptedCat(localCats[0].name, localCats[0].breed, localCats[0].age, 'assets/images/' + localCats[0].image, grid.children[0]);
            }
        }
    } catch(e) {}

    // Check URL parameters: ?cat_name=...&breed=...&dob=...&image=...
    const urlParams = new URLSearchParams(window.location.search);
    const paramCatName = urlParams.get('cat_name');
    const paramBreed = urlParams.get('breed');
    const paramDob = urlParams.get('dob');
    const paramImg = urlParams.get('image');

    if (paramCatName) {
        document.getElementById('catNameInput').value = paramCatName;
        document.getElementById('selectedCatNameLabel').textContent = paramCatName;
    }
    if (paramBreed) {
        const breedSelect = document.getElementById('catBreedSelect');
        for (let i = 0; i < breedSelect.options.length; i++) {
            if (breedSelect.options[i].value.toLowerCase().includes(paramBreed.toLowerCase()) ||
                breedSelect.options[i].text.toLowerCase().includes(paramBreed.toLowerCase())) {
                breedSelect.selectedIndex = i;
                break;
            }
        }
        document.getElementById('selectedCatBreedLabel').textContent = paramBreed;
    }
    if (paramDob) {
        document.getElementById('catDobInput').value = paramDob;
    }
    if (paramImg) {
        document.getElementById('selectedCatImg').src = paramImg.startsWith('http') || paramImg.startsWith('assets/') ? paramImg : 'assets/images/' + paramImg;
    }

    // Initial calculation
    calculateVaccineSchedule();
});
</script>

<?php
require_once __DIR__ . '/footer.php';
?>
