<?php
require_once __DIR__ . '/header.php';

$bookings_file = __DIR__ . '/data_bookings.json';
$bookings = file_exists($bookings_file) ? json_decode(file_get_contents($bookings_file), true) : [];

$booking_confirmed = null;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_booking') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $line_id = trim($_POST['line_id'] ?? '');
    $service_type = trim($_POST['service_type'] ?? 'farm_visit');
    $breed_interest = trim($_POST['breed_interest'] ?? 'บริติช ช็อตแฮร์ (น้องสโนว์)');
    $booking_date = trim($_POST['booking_date'] ?? date('Y-m-d', strtotime('+2 days')));
    $time_slot = trim($_POST['time_slot'] ?? 'ช่วงบ่าย (14:00 - 15:30 น.)');
    $guests_count = intval($_POST['guests_count'] ?? 1);
    $special_request = trim($_POST['special_request'] ?? '');

    if (!empty($name) && !empty($phone)) {
        $booking_id = 'BK-' . date('Ymd') . '-' . rand(100, 999);
        $service_name = ($service_type === 'video_call') ? 'นัด Video Call ชมความน่ารักสด 1-on-1' : 'เยี่ยมชมฟาร์ม & อุ้มน้องตัวจริง (Farm Visit VIP)';
        
        $new_booking = [
            'id' => $booking_id,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'line_id' => $line_id,
            'service_type' => $service_type,
            'service_name' => $service_name,
            'breed_interest' => $breed_interest,
            'booking_date' => $booking_date,
            'time_slot' => $time_slot,
            'guests_count' => $guests_count,
            'special_request' => $special_request,
            'status' => 'confirmed',
            'created_at' => date('Y-m-d H:i:s')
        ];
        array_unshift($bookings, $new_booking);
        file_put_contents($bookings_file, json_encode($bookings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $booking_confirmed = $new_booking;
    }
}
?>

<div class="container" style="max-width: 1080px; margin: 2rem auto 5rem auto; padding: 0 1rem;">
    <!-- Top Header -->
    <div style="background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 50%, #BFDBFE 100%); border-radius: 28px; padding: 3rem 2rem; text-align: center; border: 2px solid #93C5FD; box-shadow: 0 12px 35px rgba(37, 99, 235, 0.12); margin-bottom: 3rem;">
        <div style="display: inline-flex; align-items: center; gap: 8px; background: #2563EB; color: #fff; padding: 0.45rem 1.25rem; border-radius: 999px; font-weight: 800; font-size: 0.9rem; margin-bottom: 1rem; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">
            📅 VIP APPOINTMENT BOOKING
        </div>
        <h1 style="font-size: 2.5rem; font-weight: 900; color: #1E3A8A; margin-bottom: 0.8rem;">
            จองคิวนัดดูตัว & Video Call เยี่ยมฟาร์ม 🐾✨
        </h1>
        <p style="font-size: 1.15rem; color: #1E40AF; max-width: 720px; margin: 0 auto; line-height: 1.6;">
            เลือกวันเวลาที่สะดวกเพื่อเข้ามาสัมผัสและอุ้มน้องแมวตัวจริงที่ฟาร์มสุขุมวิท 55 หรือนัด Video Call แบบส่วนตัว 1-on-1
        </p>
    </div>

    <?php if ($booking_confirmed): ?>
        <!-- Booking Confirmation Pass / Digital Ticket -->
        <div style="background: #fff; border-radius: 28px; border: 2.5px solid #2563EB; box-shadow: 0 15px 40px rgba(37, 99, 235, 0.2); overflow: hidden; margin-bottom: 3rem;">
            <div style="background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%); color: #fff; padding: 1.8rem; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 0.3rem;">🎉✨</div>
                <h2 style="font-size: 1.8rem; font-weight: 900; margin-bottom: 0.3rem;">จองคิวนัดหมายสำเร็จเรียบร้อย!</h2>
                <p style="font-size: 1rem; opacity: 0.9;">บัตรยืนยันการนัดหมาย VIP Appointment Pass</p>
            </div>

            <div class="booking-pass-grid" style="padding: 2.5rem; display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: center;">
                <div>
                    <div style="display: inline-block; background: #FEF3C7; color: #92400E; padding: 0.3rem 0.8rem; border-radius: 8px; font-weight: 800; font-size: 0.85rem; margin-bottom: 1rem;">
                        รหัสการจอง: <?php echo htmlspecialchars($booking_confirmed['id']); ?>
                    </div>
                    <h3 style="font-size: 1.4rem; font-weight: 900; color: #0F172A; margin-bottom: 1rem;">
                        <?php echo htmlspecialchars($booking_confirmed['service_name']); ?>
                    </h3>
                    
                    <div class="booking-specs-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="background: #F8FAFC; padding: 1rem; border-radius: 14px; border: 1px solid #E2E8F0;">
                            <div style="font-size: 0.8rem; color: #64748B;">📅 วันที่นัดหมาย</div>
                            <div style="font-size: 1.1rem; font-weight: 800; color: #1E293B;"><?php echo htmlspecialchars($booking_confirmed['booking_date']); ?></div>
                        </div>
                        <div style="background: #F8FAFC; padding: 1rem; border-radius: 14px; border: 1px solid #E2E8F0;">
                            <div style="font-size: 0.8rem; color: #64748B;">⏰ ช่วงเวลา</div>
                            <div style="font-size: 1.1rem; font-weight: 800; color: #1E293B;"><?php echo htmlspecialchars($booking_confirmed['time_slot']); ?></div>
                        </div>
                        <div style="background: #F8FAFC; padding: 1rem; border-radius: 14px; border: 1px solid #E2E8F0;">
                            <div style="font-size: 0.8rem; color: #64748B;">🐱 น้องแมวที่สนใจ</div>
                            <div style="font-size: 1.05rem; font-weight: 800; color: var(--primary-coral);"><?php echo htmlspecialchars($booking_confirmed['breed_interest']); ?></div>
                        </div>
                        <div style="background: #F8FAFC; padding: 1rem; border-radius: 14px; border: 1px solid #E2E8F0;">
                            <div style="font-size: 0.8rem; color: #64748B;">👤 ผู้จอง / เบอร์โทร</div>
                            <div style="font-size: 1.05rem; font-weight: 800; color: #1E293B;"><?php echo htmlspecialchars($booking_confirmed['name']); ?> (<?php echo htmlspecialchars($booking_confirmed['phone']); ?>)</div>
                        </div>
                    </div>

                    <p style="font-size: 0.9rem; color: #64748B; line-height: 1.5;">
                        📍 <strong>สถานที่:</strong> ฟาร์ม Purrfect Shop สุขุมวิท 55 (คลองตันเหนือ วัฒนา กทม.) หรือรอรับลิงก์ Video Call ทาง LINE ID ที่แจ้งไว้ค่ะ
                    </p>
                </div>

                <!-- QR Code & Calendar Action -->
                <div style="text-align: center; background: #F0FDF4; padding: 1.8rem; border-radius: 20px; border: 1.5px solid #BBF7D0;">
                    <div style="background: #fff; width: 140px; height: 140px; margin: 0 auto 1rem auto; border-radius: 16px; display: flex; align-items: center; justify-content: center; border: 2px solid #86EFAC; font-size: 3.5rem;">
                        📱
                    </div>
                    <div style="font-weight: 800; color: #166534; font-size: 0.95rem; margin-bottom: 0.5rem;">QR Code ยืนยันการเข้าชม</div>
                    <div style="font-size: 0.8rem; color: #475569; margin-bottom: 1rem;">แสดงที่หน้าเคาน์เตอร์ฟาร์ม</div>
                    <a href="booking.php" class="btn btn-secondary btn-sm" style="width: 100%;">จองคิวนัดหมายใหม่</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Booking Form Card -->
    <div style="background: #fff; border-radius: 28px; padding: 2.5rem; border: 2px solid #E2E8F0; box-shadow: 0 10px 30px rgba(0,0,0,0.06);">
        <form method="POST" action="booking.php" id="booking-form">
            <input type="hidden" name="action" value="create_booking">

            <!-- Step 1: Select Service Mode -->
            <div style="margin-bottom: 2.5rem;">
                <label style="display: block; font-size: 1.15rem; font-weight: 900; color: #0F172A; margin-bottom: 1rem;">
                    1. เลือกรูปแบบการนัดหมายที่สะดวก 🎯
                </label>
                <div class="booking-service-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem;">
                    <label class="service-option" style="cursor: pointer; border: 2.5px solid #2563EB; background: #EFF6FF; border-radius: 20px; padding: 1.5rem; display: flex; gap: 1rem; align-items: center; transition: all 0.2s;">
                        <input type="radio" name="service_type" value="farm_visit" checked style="width: 22px; height: 22px; accent-color: #2563EB;">
                        <div>
                            <div style="font-weight: 900; color: #1E3A8A; font-size: 1.1rem; margin-bottom: 0.2rem;">🏡 เยี่ยมชมฟาร์มจริง (Farm Visit VIP)</div>
                            <div style="font-size: 0.85rem; color: #475569;">เข้ามาอุ้ม เล่น และสัมผัสตัวจริงที่ฟาร์มสุขุมวิท 55 ห้องรับรองส่วนตัว</div>
                        </div>
                    </label>
                    <label class="service-option" style="cursor: pointer; border: 2.5px solid #CBD5E1; background: #fff; border-radius: 20px; padding: 1.5rem; display: flex; gap: 1rem; align-items: center; transition: all 0.2s;">
                        <input type="radio" name="service_type" value="video_call" style="width: 22px; height: 22px; accent-color: #2563EB;">
                        <div>
                            <div style="font-weight: 900; color: #0F172A; font-size: 1.1rem; margin-bottom: 0.2rem;">📱 นัดหมาย Video Call สด 1-on-1</div>
                            <div style="font-size: 0.85rem; color: #475569;">ชมความน่ารักสดๆ ผ่าน LINE Video Call สบายๆ จากที่บ้าน</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Step 2: Choose Breed -->
            <div style="margin-bottom: 2.5rem;">
                <label style="display: block; font-size: 1.15rem; font-weight: 900; color: #0F172A; margin-bottom: 1rem;">
                    2. เลือกน้องแมวหรือสายพันธุ์ที่สนใจ 🐱
                </label>
                <select name="breed_interest" required style="width: 100%; padding: 0.9rem 1.2rem; border-radius: 14px; border: 2px solid #CBD5E1; font-size: 1rem; background: #F8FAFC; font-weight: 700; color: #1E293B;">
                    <option value="บริติช ช็อตแฮร์ (น้องสโนว์)">🐱 บริติช ช็อตแฮร์ (น้องสโนว์) - หน้ากลมแก้มป่อง สุขุมเรียบร้อย</option>
                    <option value="เปอร์เซีย (น้องปุยหิมะ)">👑 เปอร์เซีย (น้องปุยหิมะ) - ราชินีขนฟู หน้าหวาน นิ่งสงบ</option>
                    <option value="แร็กดอลล์ (น้องคอตตอน)">💎 แร็กดอลล์ (น้องคอตตอน) - เจ้าหญิงตาสีฟ้า ตัวนุ่มนิ่มดั่งตุ๊กตาผ้า</option>
                    <option value="เมนคูน (น้องไททัน)">🦁 เมนคูน (น้องไททัน) - ยักษ์ใหญ่ใจดี สายเลือดแชมป์ WCF</option>
                    <option value="มันช์กิ้น ขาสั้น (น้องชอร์ตตี้)">🐾 มันช์กิ้น ขาสั้น (น้องชอร์ตตี้) - ขาสั้นเตี้ยดุ๊กดิ๊ก น่ารักขี้เล่น</option>
                    <option value="เบงกอล (น้องจากัวร์)">🐆 เบงกอล (น้องจากัวร์) - เสือดาวจิ๋ว ลายกุหลาบทองคำ ฉลาด ปราดเปรียว</option>
                    <option value="สฟิงซ์ (น้องซีซาร์)">✨ แคนาเดียน สฟิงซ์ (น้องซีซาร์) - แมวไร้ขน ผิวนุ่มอุ่น หมดห่วงภูมิแพ้</option>
                    <option value="วิเชียรมาศ (น้องมงคล)">🧧 วิเชียรมาศ (น้องมงคล) - แต้ม 9 จุดมงคล ตาสีฟ้าคราม เฉลียวฉลาด</option>
                    <option value="ยังไม่แน่ใจ อยากให้ผู้เชี่ยวชาญช่วยแนะนำ">🌟 ยังไม่แน่ใจ อยากให้ผู้เชี่ยวชาญช่วยแนะนำสายพันธุ์ที่เหมาะ</option>
                </select>
            </div>

            <!-- Step 3: Date & Time Slot -->
            <div style="margin-bottom: 2.5rem;">
                <label style="display: block; font-size: 1.15rem; font-weight: 900; color: #0F172A; margin-bottom: 1rem;">
                    3. เลือกวันและช่วงเวลาที่สะดวก ⏰
                </label>
                <div class="booking-datetime-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 0.4rem;">เลือกวันที่ต้องการนัดหมาย *</label>
                        <input type="date" name="booking_date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" style="width: 100%; padding: 0.85rem 1.2rem; border-radius: 14px; border: 2px solid #CBD5E1; font-size: 1rem; font-weight: 700; color: #1E293B;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 0.4rem;">เลือกช่วงเวลา *</label>
                        <select name="time_slot" required style="width: 100%; padding: 0.85rem 1.2rem; border-radius: 14px; border: 2px solid #CBD5E1; font-size: 1rem; font-weight: 700; color: #1E293B; background: #fff;">
                            <option value="ช่วงเช้า (10:30 - 12:00 น.)">☀️ ช่วงเช้า (10:30 - 12:00 น.) - พร้อมรับรอง</option>
                            <option value="ช่วงบ่าย (13:30 - 15:00 น.)" selected>🌤️ ช่วงบ่าย (13:30 - 15:00 น.) - ยอดนิยม 🔥</option>
                            <option value="ช่วงเย็น (15:30 - 17:00 น.)">⛅ ช่วงเย็น (15:30 - 17:00 น.) - พร้อมรับรอง</option>
                            <option value="ช่วงค่ำ (17:30 - 19:00 น.)">🌙 ช่วงค่ำ (17:30 - 19:00 น.) - หลังเลิกงาน</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 4: Adopter Contact Details -->
            <div style="margin-bottom: 2.5rem;">
                <label style="display: block; font-size: 1.15rem; font-weight: 900; color: #0F172A; margin-bottom: 1rem;">
                    4. ข้อมูลติดต่อของคุณผู้รับเลี้ยง 👤
                </label>
                <div class="booking-contacts-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; margin-bottom: 1.2rem;">
                    <div>
                        <label style="display: block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 0.4rem;">ชื่อ-นามสกุล *</label>
                        <input type="text" name="name" required placeholder="เช่น คุณพิมพ์ลดา สุวรรณเวช" style="width: 100%; padding: 0.85rem 1.2rem; border-radius: 14px; border: 2px solid #CBD5E1; font-size: 1rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 0.4rem;">เบอร์โทรศัพท์มือถือ *</label>
                        <input type="tel" name="phone" required placeholder="เช่น 081-234-5678" style="width: 100%; padding: 0.85rem 1.2rem; border-radius: 14px; border: 2px solid #CBD5E1; font-size: 1rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 0.4rem;">LINE ID (สำหรับส่งพิกัด & Video Call)</label>
                        <input type="text" name="line_id" placeholder="เช่น cat_lover88" style="width: 100%; padding: 0.85rem 1.2rem; border-radius: 14px; border: 2px solid #CBD5E1; font-size: 1rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 0.4rem;">จำนวนผู้เข้าชม (ท่าน)</label>
                        <select name="guests_count" style="width: 100%; padding: 0.85rem 1.2rem; border-radius: 14px; border: 2px solid #CBD5E1; font-size: 1rem; background: #fff;">
                            <option value="1">1 ท่าน</option>
                            <option value="2" selected>2 ท่าน</option>
                            <option value="3">3 ท่าน</option>
                            <option value="4">4 ท่านขึ้นไป (ครอบครัว)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 0.4rem;">คำขอพิเศษ / สิ่งที่ต้องการสอบถามเพิ่มเติม</label>
                    <textarea name="special_request" rows="3" placeholder="เช่น อยากขอดูคลิปตอนน้องเล่นของเล่น, พาน้องแมวเดิมมาทำความรู้จัก..." style="width: 100%; padding: 0.85rem 1.2rem; border-radius: 14px; border: 2px solid #CBD5E1; font-size: 1rem; font-family: inherit;"></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.1rem; font-size: 1.2rem; font-weight: 900; border-radius: 16px; background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%); border-color: #2563EB; box-shadow: 0 8px 25px rgba(37, 99, 235, 0.35);">
                📅 ยืนยันการจองคิวนัดหมาย (ไม่มีค่าใช้จ่าย) ✨
            </button>
        </form>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .booking-pass-grid {
        grid-template-columns: 1fr !important;
        padding: 1.5rem !important;
    }
    .booking-service-grid {
        grid-template-columns: 1fr !important;
    }
    .booking-datetime-grid {
        grid-template-columns: 1fr !important;
    }
    .booking-contacts-grid {
        grid-template-columns: 1fr !important;
    }
    .booking-specs-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
