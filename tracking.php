<?php
// tracking.php - Live Pet Delivery Timeline Tracking
require_once __DIR__ . '/header.php';

$orders_file = __DIR__ . '/data_orders.json';
$all_orders = file_exists($orders_file) ? json_decode(file_get_contents($orders_file), true) : [];

$track_id = trim($_GET['track'] ?? ($_GET['invoice'] ?? ''));
$found_order = null;

if (!empty($track_id)) {
    foreach ($all_orders as $ord) {
        if (($ord['tracking_id'] ?? '') === $track_id || ($ord['invoice_id'] ?? '') === $track_id) {
            $found_order = $ord;
            break;
        }
    }
}

// Fallback to latest order if logged in member
if (!$found_order && $logged_user) {
    foreach ($all_orders as $ord) {
        if (($ord['user_id'] ?? '') === $logged_user['id'] || ($ord['customer_email'] ?? '') === $logged_user['email']) {
            $found_order = $ord;
            break;
        }
    }
}

// Tracking Steps Definition
$steps = [
    'vet_check' => [
        'step_num' => 1,
        'title' => 'ตรวจสุขภาพก่อนเดินทาง (Vet Health Check & Lab Clear)',
        'icon' => '🩺',
        'desc' => 'สัตวแพทย์ประจำฟาร์มตรวจร่างกาย ตรวจเลือดปลอดโรค FIV/FeLV 100% พร้อมตรวจอุณหภูมิและความสมบูรณ์ของร่างกายน้องแมว',
        'badge' => 'ผ่านการรับรองสุขภาพ'
    ],
    'grooming' => [
        'step_num' => 2,
        'title' => 'เตรียมความพร้อม & กรูมมิ่ง (Grooming & Travel Kit)',
        'icon' => '🛁',
        'desc' => 'น้องแมวได้รับการอาบน้ำ กรูมมิ่ง ตัดเล็บ เช็ดหู และจัดเตรียมกล่องเดินทางปรับอากาศพร้อมเซ็ตของขวัญ Starter Kit 11 รายการ',
        'badge' => 'พร้อมออกเดินทาง'
    ],
    'transit' => [
        'step_num' => 3,
        'title' => 'กำลังออกเดินทางส่งมอบ (In Transit / Pet Taxi)',
        'icon' => '🚐',
        'desc' => 'น้องแมวอยู่บนรถปรับอากาศควบคุมอุณหภูมิ 25°C หรือสายการบิน Pet Cargo พร้อมพี่เลี้ยงผู้เชี่ยวชาญดูแลป้อนน้ำและสังเกตอาการตลอดทาง',
        'badge' => 'อยู่ระหว่างเดินทาง'
    ],
    'delivered' => [
        'step_num' => 4,
        'title' => 'ส่งมอบถึงมือผู้รับเรียบร้อย (Delivered & Home Welcome 🐾)',
        'icon' => '🏡',
        'desc' => 'ส่งมอบน้องแมวถึงมือคุณลูกค้าหน้าบ้านเรียบร้อย พร้อมเซ็นตรวจรับและเปิดใช้งานการรับประกันสุขภาพ 180 วันสมบูรณ์',
        'badge' => 'ส่งมอบสำเร็จ'
    ]
];

$current_status = $found_order['tracking_status'] ?? 'transit';
$status_order_map = ['vet_check' => 1, 'grooming' => 2, 'transit' => 3, 'delivered' => 4];
$current_step_num = $status_order_map[$current_status] ?? 3;
?>

<div class="container" style="max-width: 960px; margin: 2rem auto 4rem auto;">
    <!-- Top Search / Banner -->
    <div style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); border-radius: var(--radius-lg); color: #FFFFFF; padding: 2.5rem 2rem; margin-bottom: 2rem; text-align: center; box-shadow: var(--shadow-md);">
        <span style="font-size: 2.8rem; display: block; margin-bottom: 0.5rem;">🚐🐾📍</span>
        <h1 style="font-size: 1.85rem; font-weight: 800; margin: 0 0 0.4rem 0;">ระบบติดตามการส่งมอบสัตว์เลี้ยงแบบเรียลไทม์</h1>
        <p style="color: #94A3B8; font-size: 0.95rem; margin: 0 0 1.5rem 0;">
            Live Pet Delivery Timeline Tracking • รถตู้ควบคุมอุณหภูมิ & พี่เลี้ยงดูแลตลอดเส้นทาง
        </p>

        <!-- Search Bar -->
        <form method="GET" action="tracking.php" style="display: flex; gap: 8px; max-width: 520px; margin: 0 auto;">
            <input type="text" name="track" placeholder="กรอกหมายเลข Tracking เช่น TRACK-TH-8899 หรือรหัสใบเสร็จ..." 
                   value="<?php echo htmlspecialchars($track_id); ?>" 
                   style="flex: 1; padding: 12px 18px; border-radius: 999px; border: 1.5px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); color: #FFFFFF; font-size: 0.95rem; outline: none;">
            <button type="submit" class="btn btn-primary" style="border-radius: 999px; padding: 12px 24px; font-weight: 700; white-space: nowrap;">
                🔍 ค้นหาสถานะ
            </button>
        </form>
    </div>

    <?php if ($found_order): ?>
        <?php 
            $shipping_method = $found_order['shipping_method'] ?? 'pet_taxi';
            $shipping_label = ($shipping_method === 'air_cargo') ? '✈️ ส่งทางเครื่องบิน (Pet Air Cargo)' : (($shipping_method === 'farm_pickup') ? '🏡 นัดรับที่ฟาร์ม (Farm Pick-up)' : '🚐 รถตู้ปรับอากาศส่งสัตว์เลี้ยง (Pet Taxi Express)');
        ?>
        <!-- Order Header Card -->
        <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.8rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; border-bottom: 1.5px solid var(--border-color); padding-bottom: 1.2rem; margin-bottom: 1.4rem;">
                <div>
                    <span style="font-size: 0.78rem; font-weight: 700; color: var(--primary-coral); text-transform: uppercase; letter-spacing: 0.5px;">หมายเลขติดตามพัสดุ (Tracking No.)</span>
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); font-family: 'Outfit', sans-serif; margin: 2px 0 0 0;">
                        <?php echo htmlspecialchars($found_order['tracking_id'] ?? ('TRACK-TH-' . substr(md5($found_order['invoice_id'] ?? '1234'), 0, 8))); ?>
                    </h2>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 0.78rem; color: var(--text-muted); display: block;">ประเภทการจัดส่ง</span>
                    <strong style="color: var(--primary-coral); font-size: 0.95rem;"><?php echo $shipping_label; ?></strong>
                </div>
            </div>

            <!-- Pet & Customer Summary Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.2rem; font-size: 0.88rem;">
                <div style="background: var(--bg-card-subtle); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <div style="color: var(--text-muted); font-size: 0.76rem; font-weight: 700; margin-bottom: 4px;">🐱 น้องแมวที่ส่งมอบ</div>
                    <?php if (!empty($found_order['items'])): ?>
                        <?php foreach ($found_order['items'] as $it): ?>
                            <div style="font-weight: 700; color: var(--text-main); margin-bottom: 2px;">
                                🐾 <?php echo htmlspecialchars($it['name']); ?> (<?php echo htmlspecialchars($it['breed']); ?>)
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="font-weight: 700;">น้องแมวสายพันธุ์แท้ Purrfect</div>
                    <?php endif; ?>
                </div>

                <div style="background: var(--bg-card-subtle); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <div style="color: var(--text-muted); font-size: 0.76rem; font-weight: 700; margin-bottom: 4px;">👤 ผู้รับมอบ & ปลายทาง</div>
                    <div style="font-weight: 700; color: var(--text-main);">
                        <?php echo htmlspecialchars($found_order['customer_name'] ?? 'คุณลูกค้า'); ?>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 2px;">
                        📍 <?php echo htmlspecialchars($found_order['delivery_address'] ?? 'กรุงเทพฯ และปริมณฑล'); ?>
                    </div>
                </div>

                <div style="background: var(--bg-card-subtle); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <div style="color: var(--text-muted); font-size: 0.76rem; font-weight: 700; margin-bottom: 4px;">📅 นัดหมายวันส่งมอบ</div>
                    <div style="font-weight: 700; color: #047857; font-size: 1rem;">
                        <?php echo htmlspecialchars($found_order['delivery_date'] ?? date('d/m/Y')); ?>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">
                        ช่วงเวลา: <?php echo htmlspecialchars($found_order['delivery_timeslot'] ?? '10:00 - 14:00 น.'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4-Step Interactive Timeline -->
        <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); padding: 2.2rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin: 0 0 2rem 0; display: flex; align-items: center; gap: 8px;">
                📍 ขั้นตอนการดูแล & ไทม์ไลน์การส่งมอบสด
            </h3>

            <div style="display: flex; flex-direction: column; gap: 1.8rem; position: relative;">
                <?php foreach ($steps as $key => $s): 
                    $is_done = $s['step_num'] <= $current_step_num;
                    $is_current = $s['step_num'] === $current_step_num;
                ?>
                    <div style="display: flex; gap: 1.4rem; position: relative;">
                        <!-- Timeline Icon Node -->
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <div style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; z-index: 2; transition: all 0.3s;
                                <?php echo $is_current ? 'background: linear-gradient(135deg, #FF6B4A, #FF8E72); color:#FFF; box-shadow: 0 0 0 5px rgba(255,107,74,0.25);' : ($is_done ? 'background: #10B981; color:#FFF;' : 'background: #F1F5F9; color:#94A3B8; border: 2px solid #CBD5E1;'); ?>">
                                <?php echo $is_done ? ($is_current ? $s['icon'] : '✓') : $s['step_num']; ?>
                            </div>
                            <?php if ($s['step_num'] < 4): ?>
                                <div style="width: 3px; flex: 1; min-height: 40px; margin-top: 4px;
                                    <?php echo $s['step_num'] < $current_step_num ? 'background: #10B981;' : 'background: #E2E8F0;'; ?>"></div>
                            <?php endif; ?>
                        </div>

                        <!-- Step Content Box -->
                        <div style="flex: 1; background: <?php echo $is_current ? '#FFF7F3' : ($is_done ? '#F8FAF9' : '#FAFAFA'); ?>; border: 1.5px solid <?php echo $is_current ? '#FFC4B0' : ($is_done ? '#A7F3D0' : '#E2E8F0'); ?>; border-radius: var(--radius-md); padding: 1.2rem 1.4rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; flex-wrap: wrap; gap: 6px;">
                                <h4 style="font-size: 1.05rem; font-weight: 800; margin: 0; color: <?php echo $is_current ? 'var(--primary-coral)' : ($is_done ? '#065F46' : 'var(--text-muted)'); ?>;">
                                    <?php echo $s['icon']; ?> <?php echo $s['title']; ?>
                                </h4>
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 999px;
                                    <?php echo $is_current ? 'background: #FF6B4A; color: #FFF;' : ($is_done ? 'background: #10B981; color: #FFF;' : 'background: #E2E8F0; color: #64748B;'); ?>">
                                    <?php echo $is_current ? '● กำลังดำเนินการ' : ($is_done ? '✓ สำเร็จแล้ว' : 'รอดำเนินการ'); ?>
                                </span>
                            </div>
                            <p style="margin: 0; font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">
                                <?php echo $s['desc']; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Driver / Pet Caretaker Contact Card -->
        <div style="background: linear-gradient(135deg, #FFF9F6 0%, #FFF0EB 100%); border: 1.5px solid #FFD0C0; border-radius: var(--radius-lg); padding: 1.6rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.2rem;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: #FF6B4A; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 4px 12px rgba(255,107,74,0.3);">
                    👨‍⚕️
                </div>
                <div>
                    <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0 0 2px 0;">
                        ผู้ดูแล & พี่เลี้ยงประจำรถจัดส่ง: <strong>คุณสมศักดิ์ ดูแลแมวดี</strong>
                    </h4>
                    <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0;">
                        ผ่านการอบรมการพยาบาลสัตว์เลี้ยงฉุกเฉิน • รถตู้ควบคุมอุณหภูมิ 25°C ทะเบียน 1กข-8899 กทม.
                    </p>
                </div>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="button" class="btn btn-primary" onclick="togglePurrfectChat()" style="font-weight: 700; font-size: 0.88rem; padding: 10px 18px;">
                    💬 แชทขอดูวิดีโอสด
                </button>
            </div>
        </div>

    <?php else: ?>
        <!-- Not Found / Sample State -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 3rem 2rem; text-align: center; box-shadow: var(--shadow-sm);">
            <span style="font-size: 3rem; display: block; margin-bottom: 0.5rem;">🔍</span>
            <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--text-main); margin: 0 0 0.5rem 0;">
                ไม่พบข้อมูลหมายเลขพัสดุที่คุณค้นหา
            </h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 480px; margin: 0 auto 1.5rem auto;">
                กรุณาตรวจสอบรหัสติดตามพัสดุ หรือเข้าสู่ระบบสมาชิกเพื่อดูประวัติการสั่งจองน้องแมวของคุณ
            </p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="profile.php" class="btn btn-secondary">
                    👤 ไปที่โปรไฟล์ของฉัน
                </a>
                <a href="products.php" class="btn btn-primary">
                    🐱 เลือกชมน้องแมว
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
