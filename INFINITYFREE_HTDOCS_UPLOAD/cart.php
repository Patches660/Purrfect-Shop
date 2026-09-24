<?php
require_once __DIR__ . '/data.php';

$checkout_success = false;
$checkout_error = "";
$invoice_data = [];
$invoice_subtotal = 0;
$invoice_discount = 0;
$invoice_vat = 0;
$invoice_total = 0;
$invoice_id = "";
$customer_name = "คุณผู้รับเลี้ยงใจดี";
$customer_phone = "";
$customer_email = "";
$delivery_address = "";
$delivery_lat = "";
$delivery_lng = "";
$delivery_date = "";
$delivery_timeslot = "";
$special_notes = "";
$paid_channel = "";
$paid_details = "";
$is_test_checkout = false;

// Autofill customer info if user is logged in
$currUser = getCurrentUser();
$default_name = $currUser ? $currUser['fullname'] : '';
$default_email = $currUser ? $currUser['email'] : '';
$default_phone = $currUser ? $currUser['phone'] : '';
$default_address = $currUser ? ($currUser['delivery_address'] ?? '') : '';
$default_lat = $currUser ? ($currUser['delivery_lat'] ?? ($currUser['lat'] ?? '13.7563')) : '13.7563';
$default_lng = $currUser ? ($currUser['delivery_lng'] ?? ($currUser['lng'] ?? '100.5018')) : '100.5018';
$default_bank = $currUser ? ($currUser['bank_name'] ?? 'ธนาคารกสิกรไทย (KBANK)') : 'ธนาคารกสิกรไทย (KBANK)';
$default_bank_account = $currUser ? ($currUser['bank_account'] ?? '') : '';
$user_points = $currUser ? intval($currUser['paw_points'] ?? 150) : 150;

// Handle Cart GET Actions (remove, clear)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'remove' && isset($_GET['id'])) {
        $remove_id = $_GET['id'];
        if (isset($_SESSION['cart'][$remove_id])) {
            unset($_SESSION['cart'][$remove_id]);
        }
        header("Location: cart.php");
        exit;
    }
    
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        header("Location: cart.php");
        exit;
    }
}

// Handle Cart POST Checkout Action
if (($_SERVER['REQUEST_METHOD'] ?? '')  === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    if (!empty($_SESSION['cart'])) {
        $is_test_checkout = isset($_POST['is_test_mode']);
        
        $customer_name = trim($_POST['customer_name'] ?? '');
        $customer_phone = trim($_POST['customer_phone'] ?? '');
        $customer_email = trim($_POST['customer_email'] ?? '');
        $delivery_address = trim($_POST['delivery_address'] ?? '');
        $delivery_lat = trim($_POST['delivery_lat'] ?? '');
        $delivery_lng = trim($_POST['delivery_lng'] ?? '');
        if (empty($delivery_lat)) $delivery_lat = '13.7563';
        if (empty($delivery_lng)) $delivery_lng = '100.5018';

        $delivery_date = trim($_POST['delivery_date'] ?? date('Y-m-d', strtotime('+3 days')));
        $delivery_timeslot = trim($_POST['delivery_timeslot'] ?? 'ช่วงบ่าย (13:00 - 17:00 น.)');
        $special_notes = trim($_POST['special_notes'] ?? '');
        $payment_channel = trim($_POST['payment_channel'] ?? 'bank_transfer');

        if ($is_test_checkout) {
            // TEST MODE: Skip validations and auto-fill test details
            if (empty($customer_name)) $customer_name = "ผู้ทดสอบระบบ (Test Adopter)";
            if (empty($customer_phone)) $customer_phone = "089-999-9999";
            if (empty($customer_email)) $customer_email = "tester@purrfectshop.com";
            if (empty($delivery_address)) $delivery_address = "99/99 อาคารทดสอบระบบ ถนนนวัตกรรม แขวงลาดยาว เขตจตุจักร กรุงเทพฯ 10900";
            if (empty($delivery_lat)) $delivery_lat = '13.8282';
            if (empty($delivery_lng)) $delivery_lng = '100.5615';
            
            $paid_channel = "🧪 โหมดทดสอบระบบ (TEST MODE)";
            $paid_details = "ข้ามการตรวจสอบข้อมูลการชำระเงินและเลขบัญชีสำหรับทดสอบระบบ";
            $checkout_success = true;
        } else {
            // Real Checkout: Validate fields
            if (empty($customer_name) || empty($customer_phone) || empty($delivery_address)) {
                $checkout_error = "กรุณากรอกชื่อ-นามสกุล, เบอร์โทรศัพท์ และที่อยู่จัดส่งให้ครบถ้วน หรือเลือกเครื่องหมาย 'โหมดทดสอบ (TEST)' เพื่อทดสอบระบบทันที";
            } else {
                // Payment Channel Specific Processing
                if ($payment_channel === 'bank_transfer') {
                    $user_bank = trim($_POST['user_bank'] ?? '');
                    $user_account = trim($_POST['user_bank_account'] ?? '');
                    if (empty($user_account)) {
                        $checkout_error = "กรุณากรอกเลขที่บัญชีของคุณ หรือเลือกช่อง 'โหมดทดสอบ (TEST)' เพื่อข้ามการตรวจสอบ";
                    } else {
                        $paid_channel = "🏦 โอนเงินผ่านบัญชีธนาคาร";
                        $paid_details = "ธนาคาร: " . htmlspecialchars($user_bank) . " | เลขที่บัญชี: " . htmlspecialchars($user_account);
                        $checkout_success = true;
                    }
                } elseif ($payment_channel === 'promptpay') {
                    $paid_channel = "📱 พร้อมเพย์ (PromptPay QR Code)";
                    $paid_details = "สแกนชำระผ่าน PromptPay เบอร์ 089-123-4567 เรียบร้อยแล้ว";
                    $checkout_success = true;
                } elseif ($payment_channel === 'credit_card') {
                    $card_num = trim($_POST['card_number'] ?? '');
                    if (empty($card_num) || strlen(str_replace(' ', '', $card_num)) < 12) {
                        $checkout_error = "กรุณากรอกหมายเลขบัตรเครดิต/เดบิตให้ถูกต้อง หรือเลือกช่อง 'โหมดทดสอบ (TEST)'";
                    } else {
                        $last4 = substr(str_replace(' ', '', $card_num), -4);
                        $paid_channel = "💳 บัตรเครดิต / เดบิต";
                        $paid_details = "บัตรเครดิตลงท้ายด้วย •••• " . $last4;
                        $checkout_success = true;
                    }
                } elseif ($payment_channel === 'cod') {
                    $paid_channel = "🤝 ชำระเงินเมื่อส่งมอบน้องแมว (Cash on Handover)";
                    $paid_details = "ชำระเงินสดหรือโอนจ่ายกับเจ้าหน้าที่ผู้ส่งมอบ ณ วันที่นัดหมาย";
                    $checkout_success = true;
                } elseif ($payment_channel === 'paypal') {
                    $paypal_email = trim($_POST['paypal_email'] ?? '');
                    if (empty($paypal_email)) {
                        $paypal_email = $currUser ? $currUser['email'] : 'adopter@paypal.com';
                    }
                    $paid_channel = "🅿️ PayPal (Global Payment Gateway)";
                    $paid_details = "ชำระผ่านบัญชี PayPal: " . htmlspecialchars($paypal_email);
                    $checkout_success = true;
                } elseif ($payment_channel === 'paw_points') {
                    $pts_subtotal = getCartSubtotal();
                    $pts_disc = getMemberDiscount($pts_subtotal);
                    $pts_needed = floor(($pts_subtotal - $pts_disc) * 1.07);
                    if ($pts_needed <= 0) $pts_needed = 150;
                    $paid_channel = "🐾 ชำระด้วยแต้มสะสม (Paw Points & Tiers)";
                    $paid_details = "หักแต้มสะสม Paw Points สมาชิก จำนวน " . number_format($pts_needed) . " พอยท์ สมบูรณ์";
                    $checkout_success = true;
                } else {
                    $paid_channel = "ชำระเงินสำเร็จ";
                    $paid_details = "ดำเนินการผ่านระบบร้านค้า";
                    $checkout_success = true;
                }
            }
        }

        // On Success: Calculate and prepare invoice
        if ($checkout_success) {
            $invoice_data = $_SESSION['cart'];
            $invoice_subtotal = getCartSubtotal();
            $invoice_discount = getMemberDiscount($invoice_subtotal);
            $after_discount = $invoice_subtotal - $invoice_discount;
            $invoice_vat = round($after_discount * 0.07, 2);
            $invoice_total = $after_discount + $invoice_vat;
            $invoice_id = 'PFC-' . date('ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));
            $tracking_id = 'TRACK-TH-' . date('ym') . '-' . strtoupper(substr(md5(uniqid()), 0, 5));
            $shipping_method = trim($_POST['shipping_method'] ?? 'pet_taxi');
            $paw_points_earned = floor($invoice_total / 100);

            // Save Order to JSON
            $newOrder = [
                'order_id' => $invoice_id,
                'invoice_id' => $invoice_id,
                'tracking_id' => $tracking_id,
                'tracking_status' => 'vet_check',
                'shipping_method' => $shipping_method,
                'user_id' => $currUser ? $currUser['id'] : 'guest',
                'username' => $currUser ? $currUser['username'] : 'guest',
                'customer_name' => $customer_name,
                'customer_phone' => $customer_phone,
                'customer_email' => $customer_email,
                'delivery_address' => $delivery_address,
                'delivery_lat' => $delivery_lat,
                'delivery_lng' => $delivery_lng,
                'delivery_date' => $delivery_date,
                'delivery_timeslot' => $delivery_timeslot,
                'items' => array_values($invoice_data),
                'cat_count' => count($invoice_data),
                'subtotal' => $invoice_subtotal,
                'discount' => $invoice_discount,
                'vat' => $invoice_vat,
                'total' => $invoice_total,
                'paw_points_earned' => $paw_points_earned,
                'payment_channel' => $paid_channel,
                'payment_details' => $paid_details,
                'status' => 'ชำระเงินแล้ว / ตรวจสุขภาพก่อนส่งมอบ (Paid & Vet Check)',
                'created_at' => date('Y-m-d H:i:s')
            ];
            saveOrder($newOrder);

            // Clear Cart ก่อน (ทำก่อน email เพื่อไม่ให้ cart ค้าง)
            $_SESSION['cart'] = [];

            // Send Order Confirmation Email (non-blocking — ส่งหลัง clear cart เสมอ)
            // Queue ถูกบันทึกทันที, การส่งจริงอาจใช้เวลา (ไม่ block checkout)
            if (!empty($newOrder['customer_email'])) {
                ignore_user_abort(true);
                sendOrderConfirmationEmail($newOrder);
            }
        }
    }
}

require_once __DIR__ . '/header.php';

$cart_subtotal = getCartSubtotal();
$cart_discount = getMemberDiscount($cart_subtotal);
$cart_after_discount = $cart_subtotal - $cart_discount;
$cart_vat = round($cart_after_discount * 0.07, 2);
$cart_grand_total = $cart_after_discount + $cart_vat;
?>

<!-- Leaflet Interactive Map Picker CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
/* Shipping Option Cards Dynamic Selection Style */
.shipping-option-card {
    border: 2px solid var(--border-color);
    background: var(--bg-card);
    border-radius: 12px;
    padding: 12px 14px;
    cursor: pointer;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    user-select: none;
}
.shipping-option-card:hover {
    border-color: var(--primary-coral);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 74, 0.15);
}
.shipping-option-card.active,
.shipping-option-card:has(input[type="radio"]:checked) {
    border-color: var(--primary-coral) !important;
    background: #FFF7F3 !important;
    box-shadow: 0 4px 14px rgba(255, 107, 74, 0.22) !important;
}
[data-theme="dark"] .shipping-option-card.active,
[data-theme="dark"] .shipping-option-card:has(input[type="radio"]:checked) {
    background: rgba(255, 107, 74, 0.15) !important;
    border-color: var(--primary-coral) !important;
}

/* Location Input Mode Selection Cards (Manual vs GPS) */
.location-mode-card {
    border: 2px solid var(--border-color);
    background: var(--bg-card);
    border-radius: 12px;
    padding: 12px 14px;
    cursor: pointer;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
}
.location-mode-card:hover {
    border-color: #047857;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
}
.location-mode-card.active,
.location-mode-card:has(input[type="radio"]:checked) {
    border-color: #047857 !important;
    background: #ECFDF5 !important;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.2) !important;
}
[data-theme="dark"] .location-mode-card.active,
[data-theme="dark"] .location-mode-card:has(input[type="radio"]:checked) {
    background: rgba(16, 185, 129, 0.15) !important;
    border-color: #10B981 !important;
}
</style>

<!-- Stepper Indicator -->
<div class="checkout-stepper">
    <div class="step-item <?php echo empty($_SESSION['cart']) && !$checkout_success ? 'active' : ''; ?>">
        <span class="step-number">1</span>
        <span>เลือกน้องแมว</span>
    </div>
    <span class="step-arrow">→</span>
    <div class="step-item <?php echo !empty($_SESSION['cart']) && !$checkout_success ? 'active' : ''; ?>">
        <span class="step-number">2</span>
        <span>ข้อมูลจัดส่ง & ชำระเงิน</span>
    </div>
    <span class="step-arrow">→</span>
    <div class="step-item <?php echo $checkout_success ? 'active' : ''; ?>">
        <span class="step-number">3</span>
        <span>ใบเสร็จ & ใบรับรอง</span>
    </div>
</div>

<?php if ($checkout_success): ?>
    <!-- =========================================================
         OFFICIAL ADOPTION CERTIFICATE & INVOICE (ใบเสร็จรับเงิน & ส่งมอบ)
         ========================================================= -->
    <div class="invoice-card">
        <div class="invoice-header">
            <span style="font-size: 3.2rem; display: block; margin-bottom: 0.5rem;">🐾📜</span>
            <h2 style="font-size: 2rem; font-weight: 800; color: var(--primary-coral); margin-bottom: 0.2rem;">
                ใบเสร็จรับเงิน & สัญญาการรับเลี้ยงน้องแมว
            </h2>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem;">
                PURRFECT OFFICIAL ADOPTION CERTIFICATE & PAYMENT RECEIPT
            </div>
            <div class="invoice-stamp-circle">
                ✓ ได้รับการรับรองสุขภาพและสายพันธุ์แท้ 100%
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-top: 1.8rem; text-align: left; background: var(--bg-card-subtle); padding: 1.2rem 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); font-size: 0.88rem;">
                <div>
                    <span style="color: var(--text-muted); font-size: 0.78rem; display: block;">รหัสคำสั่งจอง:</span>
                    <strong style="font-family: 'Outfit', sans-serif; font-size: 1.05rem; color: var(--primary-coral);"><?php echo $invoice_id; ?></strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); font-size: 0.78rem; display: block;">หมายเลข Tracking พัสดุ:</span>
                    <strong style="font-family: 'Outfit', sans-serif; font-size: 1.05rem; color: #047857;"><?php echo $tracking_id; ?></strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); font-size: 0.78rem; display: block;">Paw Points ที่ได้รับ:</span>
                    <strong style="color: #B45309; font-size: 1rem;">🐾 +<?php echo number_format($paw_points_earned); ?> พอยท์</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); font-size: 0.78rem; display: block;">สถานะการส่งมอบ:</span>
                    <a href="tracking.php?track=<?php echo urlencode($tracking_id); ?>" class="btn btn-primary btn-sm" style="padding: 4px 12px; font-size: 0.78rem; border-radius: 999px; font-weight: 700; margin-top: 2px;">
                        📍 ติดตามการจัดส่งสด
                    </a>
                </div>
            </div>
        </div>

        <!-- Adopter & Delivery Summary Box -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.4rem; margin-bottom: 1.8rem; font-size: 0.9rem;">
            <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.8rem; display: flex; align-items: center; gap: 0.4rem;">
                👤 ข้อมูลผู้รับอุปการะ & นัดหมายส่งมอบ
            </h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; color: var(--text-secondary);">
                <div><strong>ผู้รับเลี้ยง:</strong> <?php echo htmlspecialchars($customer_name); ?></div>
                <div><strong>เบอร์โทรศัพท์:</strong> <?php echo htmlspecialchars($customer_phone); ?></div>
                <div><strong>อีเมล:</strong> <?php echo htmlspecialchars($customer_email ?: '-'); ?></div>
                <div><strong>วันที่นัดส่งมอบ:</strong> <?php echo htmlspecialchars($delivery_date); ?> (<?php echo htmlspecialchars($delivery_timeslot); ?>)</div>
                <div style="grid-column: 1 / -1;"><strong>สถานที่ส่งมอบ:</strong> <?php echo nl2br(htmlspecialchars($delivery_address)); ?></div>
                <?php if (!empty($delivery_lat) && !empty($delivery_lng)): ?>
                    <div style="grid-column: 1 / -1; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; background: #EFF6FF; border: 1px solid #BFDBFE; padding: 8px 12px; border-radius: 8px;">
                        <span style="font-size: 0.85rem; color: #1E40AF;">
                            📍 <strong>พิกัดแผนที่ (GPS Coordinates):</strong> Lat: <?php echo htmlspecialchars($delivery_lat); ?>, Lng: <?php echo htmlspecialchars($delivery_lng); ?>
                        </span>
                        <a href="https://www.google.com/maps?q=<?php echo urlencode($delivery_lat . ',' . $delivery_lng); ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 3px 10px; font-size: 0.78rem; background: #FFFFFF; color: #0284C7; border-color: #93C5FD; text-decoration: none; font-weight: 700;">
                            🗺️ เปิดดูบน Google Maps ↗
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (!empty($special_notes)): ?>
                    <div style="grid-column: 1 / -1; color: var(--text-muted); font-size: 0.82rem;">
                        <strong>โน้ตพิเศษ:</strong> <?php echo htmlspecialchars($special_notes); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Itemized Table -->
        <div style="margin-bottom: 1.8rem;">
            <h4 style="font-size: 1rem; font-weight: 700; border-bottom: 1.5px solid var(--border-color); padding-bottom: 0.6rem; margin-bottom: 1rem;">
                รายการน้องแมวที่ส่งมอบ (Itemized Details)
            </h4>
            
            <?php foreach ($invoice_data as $item): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.8rem 0; border-bottom: 1px dashed var(--border-color);">
                    <div>
                        <strong style="color: var(--text-main); font-size: 1.05rem;"><?php echo $item['name']; ?></strong>
                        <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 0.2rem;">
                            สายพันธุ์: <?php echo $item['breed']; ?> • เพศ: <?php echo $item['gender']; ?> • อายุ: <?php echo $item['age']; ?> (x<?php echo $item['qty']; ?>)
                        </div>
                        <div style="font-size: 0.75rem; color: var(--accent-mint); font-weight: 600; margin-top: 0.15rem;">
                            ✓ ฉีดวัคซีนครบ • ฝังไมโครชิปมาตรฐาน • ใบเพ็ดดีกรีแท้
                        </div>
                    </div>
                    <span style="font-weight: 800; font-size: 1.15rem; color: var(--primary-coral); font-family: 'Outfit';">
                        <?php echo number_format($item['price'] * $item['qty']); ?> ฿
                    </span>
                </div>
            <?php endforeach; ?>

            <div style="margin-top: 1.5rem; padding-top: 0.8rem;">
                <div class="summary-row">
                    <span>ราคาสินสอดรวม:</span>
                    <span><?php echo number_format($invoice_subtotal); ?> ฿</span>
                </div>
                
                <?php if ($invoice_discount > 0): ?>
                    <div class="summary-row" style="color: var(--accent-mint); font-weight: 600;">
                        <span>ส่วนลดสมาชิกพิเศษ (5%):</span>
                        <span>-<?php echo number_format($invoice_discount); ?> ฿</span>
                    </div>
                <?php endif; ?>

                <div class="summary-row">
                    <span>บริการรถตู้ปรับอากาศส่งสัตว์เลี้ยง (Pet Transport):</span>
                    <span style="color: var(--accent-mint); font-weight: 600;">ฟรีโปรโมชั่นพิเศษ (0 ฿)</span>
                </div>

                <div class="summary-row">
                    <span>ภาษีมูลค่าเพิ่ม (VAT 7%):</span>
                    <span><?php echo number_format($invoice_vat, 2); ?> ฿</span>
                </div>

                <div class="summary-row total">
                    <span>ยอดชำระสุทธิ:</span>
                    <span><?php echo number_format($invoice_total, 2); ?> ฿</span>
                </div>

                <!-- Payment Channel Details -->
                <div class="summary-row" style="padding-top: 0.9rem; margin-top: 0.9rem; border-top: 1px dashed var(--border-color);">
                    <span>ช่องทางที่ทำรายการ:</span>
                    <span style="text-align: right;">
                        <strong style="color: var(--primary-coral); display: block;"><?php echo $paid_channel; ?></strong>
                        <span style="font-size: 0.82rem; color: var(--text-secondary);"><?php echo $paid_details; ?></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Official Cattery Guarantee & Signatures -->
        <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.2rem; margin-bottom: 2rem; font-size: 0.82rem; color: var(--text-secondary);">
            <div style="font-weight: 700; color: var(--primary-coral); margin-bottom: 0.3rem;">
                🛡️ การรับประกันสุขภาพและความคุ้มครอง (Health Guarantee):
            </div>
            <p style="line-height: 1.6; margin-bottom: 0.6rem;">
                ฟาร์ม <strong>Purrfect Shop</strong> รับประกันสุขภาพน้องแมวเป็นเวลา 30 วัน จากโรคติดต่อทางพันธุกรรมและโรคไข้หัด/ลิวคีเมีย หากพบความผิดปกติสามารถนำส่งตรวจรักษา ณ โรงพยาบาลสัตว์พันธมิตรได้โดยไม่มีค่าใช้จ่ายเพิ่มเติม
            </p>
            <div style="display: flex; justify-content: space-between; align-items: flex-end; padding-top: 1rem; border-top: 1px dashed var(--border-color); margin-top: 0.8rem;">
                <div>
                    <span>ลายมือชื่อสัตวแพทย์ประจำฟาร์ม: <em>น.สพ. ปพัฒน์ รักษาสัตว์</em></span>
                </div>
                <div style="text-align: right;">
                    <span style="font-weight: 700; color: var(--text-main);">Purrfect Cattery Certified Stamp</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons (Print & Shop) -->
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;" class="btn-print-hide">
            <button type="button" class="btn btn-secondary" onclick="window.print()">
                🖨️ พิมพ์ใบเสร็จ / บันทึก PDF
            </button>
            <a href="products.php" class="btn btn-primary">
                เลือกชมน้องแมวตัวอื่นต่อ 🐱
            </a>
        </div>
    </div>

<?php else: ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <!-- Empty Cart -->
        <div style="text-align: center; padding: 4rem 1.5rem; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); max-width: 580px; margin: 0 auto; box-shadow: var(--shadow-sm);">
            <div style="font-size: 4.5rem; margin-bottom: 1rem;">🧺</div>
            <h2 style="font-size: 1.7rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.6rem;">
                ยังไม่มีน้องแมวในตะกร้าของคุณ
            </h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.95rem; line-height: 1.6;">
                ค้นหาน้องแมวสายพันธุ์ที่ใช่เพื่อมอบความรักและความอบอุ่นให้กับบ้านของคุณ
            </p>
            <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                <a href="products.php" class="btn btn-primary btn-lg">
                    เลือกชมน้องแมวทั้งหมด 🐾
                </a>
                <a href="recommend.php" class="btn btn-secondary btn-lg">
                    ดูระบบแนะนำสายพันธุ์ 🌟
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- =========================================================
             2-COLUMN PROFESSIONAL CHECKOUT FORM
             ========================================================= -->
        <form method="POST" action="cart.php" id="checkout-form">
            <input type="hidden" name="action" value="checkout">

            <div class="cart-layout">
                <!-- Left Column: Items, Adopter Info, Payment Channels -->
                <div class="cart-left-col">
                    
                    <?php if (!empty($checkout_error)): ?>
                        <div class="alert-box alert-danger" style="margin-bottom: 1.5rem;">
                            ⚠️ <?php echo htmlspecialchars($checkout_error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- BLOCK 1: Selected Kitten List -->
                    <div class="checkout-block">
                        <div class="checkout-block-header">
                            <h3 class="checkout-block-title">
                                <span>🐾 1. รายการน้องแมวที่เลือกรับเลี้ยง</span>
                                <span style="font-size: 0.82rem; font-weight: 500; color: var(--text-muted);">(<?php echo getCartCount(); ?> ตัว)</span>
                            </h3>
                            <a href="cart.php?action=clear" style="font-size: 0.82rem; color: #EF4444; text-decoration: none; font-weight: 600;">
                                ล้างตะกร้าทั้งหมด 🧹
                            </a>
                        </div>

                        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                            <div class="cart-item">
                                <div class="cart-item-left">
                                    <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="cart-item-thumb">
                                    <div>
                                        <h4 class="cart-item-name"><?php echo $item['name']; ?></h4>
                                        <div class="cart-item-sub">
                                            <span><?php echo $item['breed']; ?></span> &bull; 
                                            <span><?php echo $item['gender']; ?></span> &bull; 
                                            <span><?php echo $item['age']; ?></span>
                                        </div>
                                        <div style="font-size: 0.78rem; color: var(--accent-mint); font-weight: 600; margin-top: 0.2rem;">
                                            ✓ ฉีดวัคซีนแล้ว 2 เข็ม • ตรวจสุขภาพพร้อมส่งมอบ
                                        </div>
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 1.5rem;">
                                    <div class="cart-item-price">
                                        <?php echo number_format($item['price'] * $item['qty']); ?> ฿
                                    </div>
                                    <a href="cart.php?action=remove&id=<?php echo urlencode($id); ?>" 
                                       style="color: #EF4444; text-decoration: none; font-size: 1.1rem; padding: 0.4rem;" 
                                       title="ลบรายการ">
                                        🗑️
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Free Welcome Kit Banner -->
                        <div style="background: var(--bg-card-subtle); border: 1px dashed var(--border-hover); border-radius: var(--radius-md); padding: 0.9rem 1.2rem; margin-top: 1.2rem; display: flex; align-items: center; gap: 0.8rem; font-size: 0.85rem;">
                            <span style="font-size: 1.8rem;">🎁</span>
                            <div>
                                <strong style="color: var(--primary-coral); display: block;">ฟรี! Kitten Starter Kit ประจำตัวน้องแมว:</strong>
                                <span style="color: var(--text-secondary);">อาหารสูตรลูกแมวเกรดพรีเมียม 2 กก. + ทรายแมวเต้าหู้ + ชามอาหาร + สมุดสุขภาพและใบเพ็ดดีกรี</span>
                            </div>
                        </div>
                    </div>

                    <!-- BLOCK 2: Adopter Information & Delivery Details -->
                    <div class="checkout-block">
                        <div class="checkout-block-header">
                            <h3 class="checkout-block-title">
                                <span>📍 2. ข้อมูลผู้รับอุปการะ & สถานที่ส่งมอบ</span>
                            </h3>
                            <span style="font-size: 0.78rem; color: var(--text-muted);">รถตู้ปรับอากาศส่งถึงหน้าบ้าน</span>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="customer_name">ชื่อ - นามสกุล ผู้รับอุปการะ *</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control" 
                                       placeholder="เช่น กิตติพงษ์ รักแมว" 
                                       value="<?php echo htmlspecialchars($default_name); ?>" required>
                            </div>

                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="customer_phone">เบอร์โทรศัพท์สำหรับติดต่อส่งมอบ *</label>
                                <input type="tel" name="customer_phone" id="customer_phone" class="form-control" 
                                       placeholder="เช่น 0891234567" 
                                       value="<?php echo htmlspecialchars($default_phone); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="customer_email">อีเมล (สำหรับรับเอกสารใบเสร็จและใบเพ็ดดีกรี)</label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control" 
                                   placeholder="เช่น yourname@example.com" 
                                   value="<?php echo htmlspecialchars($default_email); ?>">
                        </div>

                        <!-- Specialized Pet Delivery Options -->
                        <div class="form-group" style="margin-bottom: 1.2rem;">
                            <label style="font-size: 0.88rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.6rem; display: block;">
                                🚐 เลือกรูปแบบการจัดส่งสัตว์เลี้ยงเฉพาะทาง (Specialized Pet Delivery) *
                            </label>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 0.8rem;" id="shippingMethodsContainer">
                                <label class="shipping-option-card active" onclick="selectShippingOption(this)">
                                    <input type="radio" name="shipping_method" value="pet_taxi" checked style="margin-top: 3px; accent-color: var(--primary-coral);" onchange="selectShippingOption(this.parentElement)">
                                    <div>
                                        <strong style="font-size: 0.88rem; color: var(--text-main); display: block;">🚐 รถตู้ Pet Taxi ปรับอากาศ</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">คุมอุณหภูมิ 24-26°C + พี่เลี้ยงดูแล</span>
                                        <span style="font-size: 0.75rem; color: #047857; font-weight: 700;">ฟรี (โปรโมชั่นฟาร์ม)</span>
                                    </div>
                                </label>

                                <label class="shipping-option-card" onclick="selectShippingOption(this)">
                                    <input type="radio" name="shipping_method" value="air_cargo" style="margin-top: 3px; accent-color: var(--primary-coral);" onchange="selectShippingOption(this.parentElement)">
                                    <div>
                                        <strong style="font-size: 0.88rem; color: var(--text-main); display: block;">✈️ เครื่องบิน Pet Air Cargo</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">มาตรฐาน IATA สำหรับต่างจังหวัด</span>
                                        <span style="font-size: 0.75rem; color: #B45309; font-weight: 700;">+฿1,200</span>
                                    </div>
                                </label>

                                <label class="shipping-option-card" onclick="selectShippingOption(this)">
                                    <input type="radio" name="shipping_method" value="farm_pickup" style="margin-top: 3px; accent-color: var(--primary-coral);" onchange="selectShippingOption(this.parentElement)">
                                    <div>
                                        <strong style="font-size: 0.88rem; color: var(--text-main); display: block;">🏡 นัดรับด้วยตนเองที่ฟาร์ม</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Meet & Greet รับคำแนะนำฟรี</span>
                                        <span style="font-size: 0.75rem; color: #047857; font-weight: 700;">ฟรี (กิฟต์เซ็ตพิเศษ)</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Address Mode Radio Selector: กรอกที่อยู่เอง VS เลือกพิกัดผ่าน GPS -->
                        <div class="form-group" style="margin-bottom: 1.1rem;">
                            <label style="font-size: 0.88rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem; display: block;">
                                📍 เลือกวิธีการระบุสถานที่จัดส่งน้องแมว (Delivery Location Mode) *
                            </label>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.8rem;" id="addressModeContainer">
                                <label class="location-mode-card active" id="modeCardManual" onclick="switchAddressMode('manual')">
                                    <input type="radio" name="address_input_mode" value="manual" checked style="margin-top: 3px; accent-color: #047857;" onchange="switchAddressMode('manual')">
                                    <div>
                                        <strong style="font-size: 0.88rem; color: var(--text-main); display: block;">✍️ กรอกที่อยู่เอง (Manual Address)</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 2px;">พิมพ์บ้านเลขที่ ซอย ถนน แขวง/เขต</span>
                                    </div>
                                </label>

                                <label class="location-mode-card" id="modeCardGps" onclick="switchAddressMode('gps')">
                                    <input type="radio" name="address_input_mode" value="gps" style="margin-top: 3px; accent-color: #047857;" onchange="switchAddressMode('gps')">
                                    <div>
                                        <strong style="font-size: 0.88rem; color: #047857; display: block;">📍 เลือกพิกัดผ่านแผนที่ & GPS</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 2px;">ดึง GPS อัตโนมัติ หรือปักหมุด Google Maps</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0.8rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 6px;">
                                <label for="delivery_address" style="margin-bottom: 0; font-weight: 700;">
                                    ที่อยู่สำหรับจัดส่งน้องแมว (บ้าน / คอนโด / จังหวัด / รหัสไปรษณีย์) *
                                </label>
                                <span id="addressSourceBadge" style="font-size: 0.75rem; color: #1E40AF; background: #EFF6FF; border: 1px solid #BFDBFE; padding: 2px 8px; border-radius: 999px; font-weight: 600;">
                                    ✍️ โหมดกรอกที่อยู่เอง
                                </span>
                            </div>

                            <!-- Textarea with Auto-grow height (not resizable by user drag, grows automatically with text) -->
                            <textarea name="delivery_address" id="delivery_address" class="form-control" rows="2" 
                                      placeholder="เช่น 123/45 หมู่บ้านสุขใจ ซอยอารีย์ ถนนพหลโยธิน แขวงสามเสนใน เขตพญาไท กรุงเทพฯ 10400" 
                                      style="resize: none; overflow-y: hidden; min-height: 56px; line-height: 1.5; transition: height 0.15s ease; box-sizing: border-box;"
                                      oninput="autoResizeAddress(this)" onfocus="autoResizeAddress(this)" required><?php echo htmlspecialchars($default_address); ?></textarea>
                        </div>

                        <!-- GPS Status Alert Banner -->
                        <div id="gpsStatusBanner" style="display: none; margin-bottom: 8px; padding: 6px 12px; border-radius: 6px; font-size: 0.78rem; font-weight: 600;"></div>

                        <!-- Interactive Google Maps / GPS Live Picker Container -->
                        <div id="mapPickerContainer" style="margin-bottom: 1.2rem; background: var(--bg-card); border: 1.5px solid #BFDBFE; border-radius: var(--radius-md); padding: 12px; display: none;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                                <div style="font-size: 0.88rem; font-weight: 700; color: #1E40AF; display: flex; align-items: center; gap: 6px;">
                                    🗺️ แผนที่ระบุพิกัด GPS & Google Maps สำหรับจัดส่ง
                                </div>
                                <div style="display: flex; gap: 4px; align-items: center; flex-wrap: wrap;">
                                    <button type="button" id="btnCurrentGPS" class="btn btn-secondary btn-sm" onclick="locateCurrentGPS()" style="padding: 2px 8px; font-size: 0.72rem; font-weight: 700; background: #ECFDF5; color: #047857; border-color: #A7F3D0; display: inline-flex; align-items: center; gap: 4px; cursor: pointer;">
                                        📍 ดึง GPS ปัจจุบัน
                                    </button>
                                    <button type="button" onclick="switchMapTileLayer('google_road')" id="btnLayerRoad" style="padding: 2px 8px; font-size: 0.72rem; border-radius: 4px; border: 1px solid #93C5FD; background: #DBEAFE; color: #1E40AF; font-weight: 700; cursor: pointer;">🗺️ Google ถนน</button>
                                    <button type="button" onclick="switchMapTileLayer('google_satellite')" id="btnLayerSat" style="padding: 2px 8px; font-size: 0.72rem; border-radius: 4px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-secondary); cursor: pointer;">🛰️ ดาวเทียม</button>
                                    <button type="button" onclick="switchMapTileLayer('osm')" id="btnLayerOsm" style="padding: 2px 8px; font-size: 0.72rem; border-radius: 4px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-secondary); cursor: pointer;">🌍 มาตรฐาน</button>
                                </div>
                            </div>

                            <!-- Quick Preset Cities / Locations Strip -->
                            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-bottom: 8px;">
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">⚡ ปักหมุดด่วน:</span>
                                <button type="button" onclick="setPresetCoordinate(13.7466, 100.5349, 'สยาม / ปทุมวัน กรุงเทพฯ')" style="background: var(--bg-card); border: 1px solid var(--border-color); font-size: 0.72rem; border-radius: 6px; padding: 2px 7px; cursor: pointer; color: var(--text-secondary);">📍 สยาม/กทม.</button>
                                <button type="button" onclick="setPresetCoordinate(13.8037, 100.5532, 'จตุจักร / ลาดพร้าว กรุงเทพฯ')" style="background: var(--bg-card); border: 1px solid var(--border-color); font-size: 0.72rem; border-radius: 6px; padding: 2px 7px; cursor: pointer; color: var(--text-secondary);">📍 จตุจักร</button>
                                <button type="button" onclick="setPresetCoordinate(13.8621, 100.5144, 'เมืองนนทบุรี นนทบุรี')" style="background: var(--bg-card); border: 1px solid var(--border-color); font-size: 0.72rem; border-radius: 6px; padding: 2px 7px; cursor: pointer; color: var(--text-secondary);">📍 นนทบุรี</button>
                                <button type="button" onclick="setPresetCoordinate(18.7883, 98.9853, 'เมืองเชียงใหม่ เชียงใหม่')" style="background: var(--bg-card); border: 1px solid var(--border-color); font-size: 0.72rem; border-radius: 6px; padding: 2px 7px; cursor: pointer; color: var(--text-secondary);">📍 เชียงใหม่</button>
                                <button type="button" onclick="setPresetCoordinate(12.9276, 100.8771, 'พัทยา / บางละมุง ชลบุรี')" style="background: var(--bg-card); border: 1px solid var(--border-color); font-size: 0.72rem; border-radius: 6px; padding: 2px 7px; cursor: pointer; color: var(--text-secondary);">📍 ชลบุรี/พัทยา</button>
                                <button type="button" onclick="setPresetCoordinate(7.8804, 98.3923, 'เมืองภูเก็ต ภูเก็ต')" style="background: var(--bg-card); border: 1px solid var(--border-color); font-size: 0.72rem; border-radius: 6px; padding: 2px 7px; cursor: pointer; color: var(--text-secondary);">📍 ภูเก็ต</button>
                            </div>

                            <!-- Search Place & Google Maps URL Input Strip -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                                <div style="display: flex; gap: 4px;">
                                    <input type="text" id="mapSearchInput" placeholder="🔍 ค้นหาสถานที่ (เช่น สยาม, เซ็นทรัล, ม.เกษตร)..." class="form-control" style="font-size: 0.82rem; padding: 5px 10px;" onkeydown="if(event.key==='Enter'){event.preventDefault(); searchLocationOnMap();}">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="searchLocationOnMap()" style="padding: 5px 10px; font-size: 0.78rem; font-weight: 700; white-space: nowrap;">
                                        ค้นหา
                                    </button>
                                </div>
                                <div style="display: flex; gap: 4px;">
                                    <input type="text" id="gmapsPasteInput" placeholder="📋 หรือวางลิงก์ Google Maps / พิกัด (13.75, 100.50)..." class="form-control" style="font-size: 0.82rem; padding: 5px 10px;" oninput="parseAndSetGoogleMapsInput(this.value)" onpaste="setTimeout(() => parseAndSetGoogleMapsInput(this.value), 100)">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="parseAndSetGoogleMapsInput(document.getElementById('gmapsPasteInput').value)" style="padding: 5px 10px; font-size: 0.78rem; font-weight: 700; white-space: nowrap; background: #EFF6FF; color: #1E40AF; border-color: #BFDBFE;">
                                        แปลงพิกัด
                                    </button>
                                </div>
                            </div>

                            <!-- Leaflet / Google Maps Canvas -->
                            <div id="deliveryMap" style="height: 300px; width: 100%; border-radius: 8px; border: 1.5px solid var(--border-color); z-index: 1;"></div>

                            <!-- Coordinates Inputs Strip -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 8px; background: var(--bg-card-subtle); padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color);">
                                <div>
                                    <label style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 2px;">📍 ละติจูด (Latitude):</label>
                                    <input type="number" step="any" name="delivery_lat" id="delivery_lat" class="form-control" style="font-size: 0.8rem; padding: 4px 8px; font-family: 'Outfit', monospace;" value="<?php echo htmlspecialchars($_POST['delivery_lat'] ?? $default_lat); ?>" oninput="onManualCoordinateChange()">
                                </div>
                                <div>
                                    <label style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 2px;">📍 ลองจิจูด (Longitude):</label>
                                    <input type="number" step="any" name="delivery_lng" id="delivery_lng" class="form-control" style="font-size: 0.8rem; padding: 4px 8px; font-family: 'Outfit', monospace;" value="<?php echo htmlspecialchars($_POST['delivery_lng'] ?? $default_lng); ?>" oninput="onManualCoordinateChange()">
                                </div>
                            </div>

                            <!-- Bottom Coordinates & Action Strip -->
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--border-color);">
                                <div style="font-size: 0.82rem; color: var(--text-main);">
                                    📍 <strong>พิกัด GPS ปัจจุบัน:</strong> 
                                    <span id="display_lat_lng" style="font-family: 'Outfit', monospace; font-weight: 700; color: var(--primary-coral);">
                                        Lat: <?php echo htmlspecialchars($default_lat); ?>, Lng: <?php echo htmlspecialchars($default_lng); ?>
                                    </span>
                                </div>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="reverseGeocodeAddress()" style="font-size: 0.75rem; padding: 4px 10px; background: #FEF3C7; color: #92400E; border-color: #FCD34D; font-weight: 700;">
                                        ✍️ ดึงชื่อที่อยู่อัตโนมัติ
                                    </button>
                                    <a id="googleMapsLink" href="https://www.google.com/maps?q=<?php echo urlencode($default_lat . ',' . $default_lng); ?>" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 4px 10px; background: #F1F5F9; color: #0284C7; border-color: #CBD5E1; text-decoration: none; font-weight: 700;">
                                        🌐 เปิดดูบน Google Maps ↗
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="delivery_date">วันที่สะดวกรับน้องแมว</label>
                                <input type="date" name="delivery_date" id="delivery_date" class="form-control" 
                                       value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>" 
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="delivery_timeslot">ช่วงเวลาที่สะดวกรับ</label>
                                <select name="delivery_timeslot" id="delivery_timeslot" class="form-control">
                                    <option value="ช่วงเช้า (09:00 - 12:00 น.)">ช่วงเช้า (09:00 - 12:00 น.)</option>
                                    <option value="ช่วงบ่าย (13:00 - 17:00 น.)" selected>ช่วงบ่าย (13:00 - 17:00 น.)</option>
                                    <option value="ช่วงเย็น (17:00 - 20:00 น.)">ช่วงเย็น (17:00 - 20:00 น.)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 1rem; margin-bottom: 0;">
                            <label for="special_notes">หมายเหตุเพิ่มเติมถึงพี่เลี้ยง (ถ้ามี)</label>
                            <input type="text" name="special_notes" id="special_notes" class="form-control" 
                                   placeholder="เช่น นัดรับที่ล็อบบี้คอนโด, ขอให้โทรแจ้งล่วงหน้า 30 นาที">
                        </div>
                    </div>

                    <!-- BLOCK 3: Select Payment Channel -->
                    <div class="checkout-block">
                        <div class="checkout-block-header">
                            <h3 class="checkout-block-title">
                                <span>💳 3. เลือกช่องทางชำระเงิน (Payment Channel)</span>
                            </h3>
                            <span style="font-size: 0.78rem; color: var(--accent-mint); font-weight: 600;">🔒 เข้ารหัสปลอดภัย SSL 256-bit</span>
                        </div>

                        <!-- TEST MODE Sandbox Toggle (ตามคำขอ) -->
                        <div class="test-mode-box">
                            <label class="test-mode-label">
                                <input type="checkbox" 
                                       id="test_skip_account" 
                                       name="is_test_mode" 
                                       value="1" 
                                       class="test-checkbox-input"
                                       onchange="toggleTestMode(this.checked)">
                                <div>
                                    <span style="font-weight: 700; color: #B45309; font-size: 0.95rem; display: flex; align-items: center; gap: 0.3rem;">
                                        🧪 โหมดทดสอบระบบ (TEST MODE)
                                    </span>
                                    <span style="font-size: 0.82rem; color: #78350F; line-height: 1.4; display: block; margin-top: 0.2rem;">
                                        ติ๊กเครื่องหมายถูกนี้เพื่อ <strong>ข้ามการกรอกข้อมูลชำระเงินและเลขที่บัญชีทั้งหมด</strong> เหมาะสำหรับอาจารย์และผู้ตรวจเพื่อทดสอบการรับเลี้ยงได้ในคลิกเดียว
                                    </span>
                                </div>
                            </label>
                        </div>

                        <!-- Payment Channels Cards Grid -->
                        <div class="payment-methods-grid" id="payment-methods-grid" style="transition: opacity 0.25s ease, filter 0.25s ease;">
                            <!-- Option 1: Bank Transfer -->
                            <label class="payment-method-card active" onclick="switchPaymentChannel('bank_transfer')">
                                <input type="radio" name="payment_channel" value="bank_transfer" class="payment-method-radio" checked>
                                <span class="payment-method-icon">🏦</span>
                                <div class="payment-method-info">
                                    <h5>โอนผ่านบัญชีธนาคาร</h5>
                                    <p>KBANK, SCB, BBL, KTB</p>
                                </div>
                            </label>

                            <!-- Option 2: PromptPay -->
                            <label class="payment-method-card" onclick="switchPaymentChannel('promptpay')">
                                <input type="radio" name="payment_channel" value="promptpay" class="payment-method-radio">
                                <span class="payment-method-icon">📱</span>
                                <div class="payment-method-info">
                                    <h5>สแกนพร้อมเพย์ (QR)</h5>
                                    <p>ทุกแอปธนาคาร ไม่มีค่าธรรมเนียม</p>
                                </div>
                            </label>

                            <!-- Option 3: Credit/Debit Card -->
                            <label class="payment-method-card" onclick="switchPaymentChannel('credit_card')">
                                <input type="radio" name="payment_channel" value="credit_card" class="payment-method-radio">
                                <span class="payment-method-icon">💳</span>
                                <div class="payment-method-info">
                                    <h5>บัตรเครดิต / เดบิต</h5>
                                    <p>Visa, Mastercard, JCB</p>
                                </div>
                            </label>

                            <!-- Option 4: Pay on Handover -->
                            <label class="payment-method-card" onclick="switchPaymentChannel('cod')">
                                <input type="radio" name="payment_channel" value="cod" class="payment-method-radio">
                                <span class="payment-method-icon">🤝</span>
                                <div class="payment-method-info">
                                    <h5>ชำระเมื่อส่งมอบน้องแมว</h5>
                                    <p>ตรวจสุขภาพหน้าบ้านแล้วจ่าย</p>
                                </div>
                            </label>

                            <!-- Option 5: PayPal -->
                            <label class="payment-method-card" onclick="switchPaymentChannel('paypal')">
                                <input type="radio" name="payment_channel" value="paypal" class="payment-method-radio">
                                <span class="payment-method-icon">🅿️</span>
                                <div class="payment-method-info">
                                    <h5>PayPal</h5>
                                    <p>บัตรเดบิต/เครดิตทั่วโลก & PayPal</p>
                                </div>
                            </label>

                            <!-- Option 6: Paw Points -->
                            <label class="payment-method-card" onclick="switchPaymentChannel('paw_points')">
                                <input type="radio" name="payment_channel" value="paw_points" class="payment-method-radio">
                                <span class="payment-method-icon">🐾</span>
                                <div class="payment-method-info">
                                    <h5>ชำระผ่านแต้มสะสม</h5>
                                    <p>ใช้ Paw Points แลกรับเลี้ยง</p>
                                </div>
                            </label>
                        </div>

                        <!-- Dynamic Channel Detail Panels -->
                        <div id="payment-details-container" style="transition: opacity 0.25s ease, filter 0.25s ease;">
                            <!-- 1. Bank Transfer Details -->
                            <div id="panel-bank_transfer" class="payment-detail-box">
                                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--primary-coral); margin-bottom: 0.8rem;">
                                    🏦 บัญชีธนาคารของร้านสำหรับโอนเงิน:
                                </h4>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 1rem; font-size: 0.85rem;">
                                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 8px;">
                                        <strong style="color: #047857;">ธนาคารกสิกรไทย (KBANK)</strong>
                                        <div style="font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 700; margin: 0.2rem 0;">098-7-65432-1</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">ชื่อบัญชี: บจก. เพอร์เฟกต์ แคท ช็อป</div>
                                    </div>
                                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 8px;">
                                        <strong style="color: #4338CA;">ธนาคารไทยพาณิชย์ (SCB)</strong>
                                        <div style="font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 700; margin: 0.2rem 0;">123-4-56789-0</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">ชื่อบัญชี: บจก. เพอร์เฟกต์ แคท ช็อป</div>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label for="user_bank" style="font-size: 0.82rem;">ธนาคารของผู้โอน *</label>
                                        <select name="user_bank" id="user_bank" class="form-control" style="padding: 0.55rem 0.8rem; font-size: 0.88rem;">
                                            <option value="ธนาคารกสิกรไทย (KBANK)">ธนาคารกสิกรไทย (KBANK)</option>
                                            <option value="ธนาคารไทยพาณิชย์ (SCB)">ธนาคารไทยพาณิชย์ (SCB)</option>
                                            <option value="ธนาคารกรุงเทพ (BBL)">ธนาคารกรุงเทพ (BBL)</option>
                                            <option value="ธนาคารกรุงไทย (KTB)">ธนาคารกรุงไทย (KTB)</option>
                                            <option value="ธนาคารทหารไทยธนชาต (TTB)">ธนาคารทหารไทยธนชาต (TTB)</option>
                                            <option value="พร้อมเพย์ / PromptPay">พร้อมเพย์ / PromptPay</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label for="user_bank_account" style="font-size: 0.82rem;">เลขที่บัญชีของคุณ *</label>
                                        <input type="text" name="user_bank_account" id="user_bank_account" class="form-control" 
                                               placeholder="เช่น 123-4-56789-0 หรือ 0812345678" 
                                               value="<?php echo htmlspecialchars($default_bank_account); ?>"
                                               style="padding: 0.55rem 0.8rem; font-size: 0.88rem;" required>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. PromptPay Details -->
                            <div id="panel-promptpay" class="payment-detail-box" style="display: none; text-align: center;">
                                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--primary-coral); margin-bottom: 0.6rem;">
                                    📱 สแกน QR Code พร้อมเพย์เพื่อชำระเงิน
                                </h4>
                                <div style="display: inline-block; background: var(--bg-card); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); margin-bottom: 0.8rem;">
                                    <div style="font-size: 4rem; line-height: 1;">📲</div>
                                    <div style="font-family: 'Outfit'; font-weight: 800; font-size: 1.15rem; color: #1E3A8A; margin-top: 0.4rem;">PROMPTPAY</div>
                                    <div style="font-family: 'Outfit'; font-weight: 700; color: var(--text-main);">089-123-4567</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">บจก. เพอร์เฟกต์ แคท ช็อป</div>
                                </div>
                                <p style="font-size: 0.82rem; color: var(--text-secondary);">
                                    สามารถบันทึกภาพหน้าจอ หรือใช้แอปธนาคารสแกนยอด <strong><?php echo number_format($cart_grand_total, 2); ?> ฿</strong> ได้ทันที
                                </p>
                            </div>

                            <!-- 3. Credit Card Details -->
                            <div id="panel-credit_card" class="payment-detail-box" style="display: none;">
                                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--primary-coral); margin-bottom: 0.8rem;">
                                    💳 ระบุข้อมูลบัตรเครดิต / เดบิต
                                </h4>
                                <div class="form-group" style="margin-bottom: 0.8rem;">
                                    <label for="card_number" style="font-size: 0.82rem;">หมายเลขบัตร 16 หลัก</label>
                                    <input type="text" name="card_number" id="card_number" class="form-control" 
                                           placeholder="4123 4567 8901 2345" maxlength="19">
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label for="card_expiry" style="font-size: 0.82rem;">วันหมดอายุ (MM/YY)</label>
                                        <input type="text" name="card_expiry" id="card_expiry" class="form-control" placeholder="12/28" maxlength="5">
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label for="card_cvv" style="font-size: 0.82rem;">รหัสหลังบัตร (CVV)</label>
                                        <input type="password" name="card_cvv" id="card_cvv" class="form-control" placeholder="•••" maxlength="4">
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Cash on Handover Details -->
                            <div id="panel-cod" class="payment-detail-box" style="display: none;">
                                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--primary-coral); margin-bottom: 0.5rem;">
                                    🤝 ชำระเงินสดในวันส่งมอบน้องแมว
                                </h4>
                                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6;">
                                    เจ้าหน้าที่จะเดินทางพร้อมน้องแมวด้วยรถปรับอากาศ เมื่อถึงที่หมาย คุณสามารถตรวจเช็กสุขภาพ ความแข็งแรง และเอกสารเพ็ดดีกรีของน้องแมวก่อน จากนั้นจึงชำระเงินสดหรือโอนจ่ายตรงกับเจ้าหน้าที่ส่งมอบ
                                </p>
                            </div>

                            <!-- 5. PayPal Details -->
                            <div id="panel-paypal" class="payment-detail-box" style="display: none;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.8rem; flex-wrap: wrap; gap: 8px;">
                                    <h4 style="font-size: 0.95rem; font-weight: 700; color: #0284C7; margin-bottom: 0; display: flex; align-items: center; gap: 0.4rem;">
                                        🅿️ ชำระเงินผ่าน PayPal / บัตรสากล (Global Gateway)
                                    </h4>
                                    <span style="font-size: 0.75rem; background: #E0F2FE; color: #0369A1; padding: 2px 8px; border-radius: 999px; font-weight: 700;">Global Checkout</span>
                                </div>
                                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1rem;">
                                    รองรับบัญชี PayPal ทั่วโลก หรือบัตรเครดิต/เดบิตสากล (Visa, Mastercard, AMEX) ผ่านระบบความปลอดภัยมาตรฐานโลก
                                </p>
                                <div class="form-group" style="margin-bottom: 0.8rem;">
                                    <label for="paypal_email" style="font-size: 0.82rem;">อีเมลบัญชี PayPal ของคุณ</label>
                                    <input type="email" name="paypal_email" id="paypal_email" class="form-control" 
                                           placeholder="เช่น your-paypal-account@email.com" 
                                           value="<?php echo htmlspecialchars($default_email); ?>">
                                </div>
                                <div style="background: var(--bg-card); border: 1px dashed var(--border-color); border-radius: 8px; padding: 0.8rem 1rem; font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.6rem;">
                                    <span style="font-size: 1.2rem;">🔒</span>
                                    <span>ระบบเชื่อมต่อตรงกับเซิร์ฟเวอร์ความปลอดภัยของ PayPal พร้อมหักชำระยอด <strong><?php echo number_format($cart_grand_total, 2); ?> ฿</strong> ทันทีที่ยืนยัน</span>
                                </div>
                            </div>

                            <!-- 6. Paw Points Details -->
                            <div id="panel-paw_points" class="payment-detail-box" style="display: none;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.8rem; flex-wrap: wrap; gap: 8px;">
                                    <h4 style="font-size: 0.95rem; font-weight: 700; color: #B45309; margin-bottom: 0; display: flex; align-items: center; gap: 0.4rem;">
                                        🐾 ชำระด้วยแต้มสะสมสมาชิก (Paw Points Redemption)
                                    </h4>
                                    <span style="font-size: 0.75rem; background: #FEF3C7; color: #92400E; padding: 2px 8px; border-radius: 999px; font-weight: 700;">Paw Points System</span>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 1rem; font-size: 0.85rem;">
                                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 8px;">
                                        <span style="color: var(--text-muted); font-size: 0.75rem; display: block;">แต้มสะสมของคุณปัจจุบัน:</span>
                                        <strong style="color: #D97706; font-size: 1.15rem; font-family: 'Outfit';">🐾 <?php echo number_format($user_points); ?> พอยท์</strong>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem;">ระดับสมาชิก: <strong><?php echo $currUser ? htmlspecialchars($currUser['tier'] ?? 'Gold Member') : 'สมาชิกระดับทั่วไป'; ?></strong></div>
                                    </div>
                                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 8px;">
                                        <span style="color: var(--text-muted); font-size: 0.75rem; display: block;">อัตราแลกแต้มแทนเงินสด:</span>
                                        <strong style="color: #047857; font-size: 1.05rem; font-family: 'Outfit';">100 แต้ม = 100 ฿</strong>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem;">ใช้แต้มสะสมครอบคลุมยอดสินสอดทั้งหมด</div>
                                    </div>
                                </div>

                                <div style="background: #FFFBEB; border: 1px solid #FCD34D; border-radius: 8px; padding: 0.85rem 1rem; font-size: 0.82rem; color: #92400E; line-height: 1.5;">
                                    ✨ <strong>สิทธิ์แลกรับเลี้ยงด้วยแต้ม:</strong> เมื่อกดยืนยันคำสั่งจอง ระบบจะทำการหักแต้มสะสม Paw Points สมาชิกตามยอดสุทธิและมอบสิทธิ์การรับเลี้ยงทันที
                                </div>
                            </div>
                        </div>

                        <!-- TEST Mode Live Banner Notification -->
                        <div id="test-mode-notice" style="display: none; background: #FEF3C7; border: 1.5px dashed #F59E0B; border-radius: 8px; padding: 0.8rem 1rem; font-size: 0.85rem; color: #92400E; margin-top: 1rem;">
                            ✓ <strong>เปิดใช้งานโหมด TEST แล้ว:</strong> ข้ามการกรอกข้อมูลชำระเงินและที่อยู่ทั้งหมด สามารถกดปุ่มยืนยันรับเลี้ยงด้านล่างเพื่อทดสอบระบบได้ทันที!
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sticky Summary & Trust Seals -->
                <div class="cart-right-col">
                    <div class="cart-summary-card">
                        <h3 class="cart-summary-title">สรุปยอดคำสั่งจอง</h3>

                        <?php if (!isUserLoggedIn()): ?>
                            <!-- Member discount prompt -->
                            <div style="background: var(--accent-amber-soft); border: 1px solid #FCD34D; border-radius: var(--radius-md); padding: 0.85rem 1rem; margin-bottom: 1.2rem; font-size: 0.85rem; color: #92400E;">
                                💡 <strong>ประหยัดทันที 5%:</strong> <a href="login.php" style="color: #B45309; font-weight: 700;">เข้าสู่ระบบ</a> หรือ <a href="register.php" style="color: #B45309; font-weight: 700;">สมัครสมาชิกใหม่</a> เพื่อรับสิทธิพิเศษ!
                            </div>
                        <?php else: ?>
                            <div style="background: var(--accent-mint-soft); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: var(--radius-md); padding: 0.8rem 1rem; margin-bottom: 1.2rem; font-size: 0.85rem; color: #065F46;">
                                ✓ <strong>สิทธิพิเศษสมาชิก:</strong> ลดทันที 5% (คุณ<?php echo htmlspecialchars($_SESSION['user']['username']); ?>)
                            </div>
                        <?php endif; ?>

                        <div class="summary-row">
                            <span>ราคาสินสอดรวม:</span>
                            <span><?php echo number_format($cart_subtotal); ?> ฿</span>
                        </div>

                        <?php if ($cart_discount > 0): ?>
                            <div class="summary-row" style="color: var(--accent-mint); font-weight: 600;">
                                <span>ส่วนลดสมาชิก (5%):</span>
                                <span>-<?php echo number_format($cart_discount); ?> ฿</span>
                            </div>
                        <?php endif; ?>

                        <div class="summary-row">
                            <span>ค่าจัดส่งรถตู้ปรับอากาศ (Pet Transport):</span>
                            <span style="color: var(--accent-mint); font-weight: 600;">ฟรีโปรโมชั่น (0 ฿)</span>
                        </div>

                        <div class="summary-row">
                            <span>ภาษีมูลค่าเพิ่ม (VAT 7%):</span>
                            <span><?php echo number_format($cart_vat, 2); ?> ฿</span>
                        </div>

                        <div class="summary-row total">
                            <span>ยอดชำระสุทธิ:</span>
                            <span><?php echo number_format($cart_grand_total, 2); ?> ฿</span>
                        </div>

                        <div style="margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.95rem; font-size: 1.05rem;">
                                ยืนยันคำสั่งจองและรับเลี้ยง 🐾
                            </button>
                        </div>

                        <!-- Trust and Guarantees List -->
                        <div style="margin-top: 1.8rem; padding-top: 1.2rem; border-top: 1px solid var(--border-color);">
                            <div class="trust-badge-item">
                                <span class="trust-badge-icon">🩺</span>
                                <div class="trust-badge-text">
                                    <strong>รับประกันสุขภาพ 30 วัน</strong>
                                    <span>ตรวจไข้หัดและลิวคีเมียก่อนส่งมอบทุกตัว</span>
                                </div>
                            </div>
                            <div class="trust-badge-item">
                                <span class="trust-badge-icon">📜</span>
                                <div class="trust-badge-text">
                                    <strong>ใบเพ็ดดีกรีรับรองสายพันธุ์</strong>
                                    <span>สายเลือดแชมป์มาตรฐานสากล WCF/CFA</span>
                                </div>
                            </div>
                            <div class="trust-badge-item">
                                <span class="trust-badge-icon">🚗</span>
                                <div class="trust-badge-text">
                                    <strong>จัดส่งด้วยรถปรับอากาศสำหรับสัตว์เลี้ยง</strong>
                                    <span>มีพี่เลี้ยงดูแลตลอดเส้นทางอย่างปลอดภัย</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>

<?php endif; ?>

<script>
// Switch Payment Channels Tabs
function switchPaymentChannel(channel) {
    // Update Radio card styles
    const cards = document.querySelectorAll('.payment-method-card');
    cards.forEach(c => {
        c.classList.remove('active');
        const radio = c.querySelector('input[type="radio"]');
        if (radio && radio.value === channel) {
            c.classList.add('active');
            radio.checked = true;
        }
    });

    // Toggle detail panels
    const panels = ['bank_transfer', 'promptpay', 'credit_card', 'cod', 'paypal', 'paw_points'];
    panels.forEach(p => {
        const elem = document.getElementById('panel-' + p);
        if (elem) {
            elem.style.display = (p === channel) ? 'block' : 'none';
        }
    });

    // Update required status
    const bankAccountInput = document.getElementById('user_bank_account');
    const isTest = document.getElementById('test_skip_account')?.checked;
    if (bankAccountInput && !isTest) {
        bankAccountInput.required = (channel === 'bank_transfer');
    }
}

// Toggle TEST Mode Checkbox (ตามคำขอ)
function toggleTestMode(isTest) {
    const bankAccountInput = document.getElementById('user_bank_account');
    const custName = document.getElementById('customer_name');
    const custPhone = document.getElementById('customer_phone');
    const delivAddr = document.getElementById('delivery_address');
    const testNotice = document.getElementById('test-mode-notice');
    const paymentContainer = document.getElementById('payment-details-container');
    const methodsGrid = document.getElementById('payment-methods-grid');

    if (isTest) {
        // Remove required constraints
        if (bankAccountInput) bankAccountInput.required = false;
        if (custName) custName.required = false;
        if (custPhone) custPhone.required = false;
        if (delivAddr) delivAddr.required = false;

        // Visual feedback & disable interaction on payment options
        if (methodsGrid) {
            methodsGrid.style.opacity = '0.35';
            methodsGrid.style.filter = 'grayscale(85%)';
            methodsGrid.style.pointerEvents = 'none';
            methodsGrid.style.userSelect = 'none';
            methodsGrid.style.cursor = 'not-allowed';
            methodsGrid.querySelectorAll('input, button, label').forEach(el => el.setAttribute('disabled', 'disabled'));
        }
        if (paymentContainer) {
            paymentContainer.style.opacity = '0.35';
            paymentContainer.style.filter = 'grayscale(85%)';
            paymentContainer.style.pointerEvents = 'none';
            paymentContainer.style.userSelect = 'none';
            paymentContainer.style.cursor = 'not-allowed';
            paymentContainer.querySelectorAll('input, select, button, textarea').forEach(el => el.setAttribute('disabled', 'disabled'));
        }
        if (testNotice) testNotice.style.display = 'block';
    } else {
        // Restore constraints
        if (custName) custName.required = true;
        if (custPhone) custPhone.required = true;
        if (delivAddr) delivAddr.required = true;

        const currentChannel = document.querySelector('input[name="payment_channel"]:checked')?.value || 'bank_transfer';
        if (bankAccountInput) bankAccountInput.required = (currentChannel === 'bank_transfer');

        // Restore visual & interactive state
        if (methodsGrid) {
            methodsGrid.style.opacity = '1';
            methodsGrid.style.filter = 'none';
            methodsGrid.style.pointerEvents = 'auto';
            methodsGrid.style.userSelect = 'auto';
            methodsGrid.style.cursor = 'auto';
            methodsGrid.querySelectorAll('input, button, label').forEach(el => el.removeAttribute('disabled'));
        }
        if (paymentContainer) {
            paymentContainer.style.opacity = '1';
            paymentContainer.style.filter = 'none';
            paymentContainer.style.pointerEvents = 'auto';
            paymentContainer.style.userSelect = 'auto';
            paymentContainer.style.cursor = 'auto';
            paymentContainer.querySelectorAll('input, select, button, textarea').forEach(el => el.removeAttribute('disabled'));
        }
        if (testNotice) testNotice.style.display = 'none';
    }
}

// -------------------------------------------------------------
// Auto-expanding Delivery Address Textarea (Cannot be manually resized, auto-grows with content)
// -------------------------------------------------------------
function autoResizeAddress(elem) {
    const ta = elem || document.getElementById('delivery_address');
    if (!ta) return;
    ta.style.height = 'auto';
    const newHeight = Math.max(56, ta.scrollHeight);
    ta.style.height = newHeight + 'px';
}

// Switch Specialized Pet Delivery Options (เปลี่ยนสีกรอบและพื้นหลังทันทีเมื่อคลิก)
function selectShippingOption(cardElem) {
    if (!cardElem) return;
    const allCards = document.querySelectorAll('.shipping-option-card');
    allCards.forEach(c => c.classList.remove('active'));
    cardElem.classList.add('active');
    const radio = cardElem.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
}

// Switch Address Input Mode (Manual Textarea vs GPS Map Picker)
function switchAddressMode(mode) {
    const cardManual = document.getElementById('modeCardManual');
    const cardGps = document.getElementById('modeCardGps');
    const mapContainer = document.getElementById('mapPickerContainer');
    const badge = document.getElementById('addressSourceBadge');
    const radioManual = cardManual?.querySelector('input[type="radio"]');
    const radioGps = cardGps?.querySelector('input[type="radio"]');

    if (mode === 'gps') {
        if (cardManual) cardManual.classList.remove('active');
        if (cardGps) cardGps.classList.add('active');
        if (radioGps) radioGps.checked = true;
        if (mapContainer) mapContainer.style.display = 'block';
        if (badge) {
            badge.textContent = '📍 โหมดพิกัด GPS & Google Maps';
            badge.style.background = '#ECFDF5';
            badge.style.borderColor = '#A7F3D0';
            badge.style.color = '#047857';
        }
        setTimeout(() => {
            initDeliveryMap();
            if (mapInstance) {
                mapInstance.invalidateSize();
                mapInstance.setView([currentLat, currentLng], mapInstance.getZoom());
            }
        }, 150);
    } else {
        if (cardGps) cardGps.classList.remove('active');
        if (cardManual) cardManual.classList.add('active');
        if (radioManual) radioManual.checked = true;
        if (mapContainer) mapContainer.style.display = 'none';
        if (badge) {
            badge.textContent = '✍️ โหมดกรอกที่อยู่เอง';
            badge.style.background = '#EFF6FF';
            badge.style.borderColor = '#BFDBFE';
            badge.style.color = '#1E40AF';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    autoResizeAddress();
    const testCheckbox = document.getElementById('test_skip_account');
    if (testCheckbox) {
        toggleTestMode(testCheckbox.checked);
    }
    const currentAddressMode = document.querySelector('input[name="address_input_mode"]:checked')?.value || 'manual';
    switchAddressMode(currentAddressMode);
});

// -------------------------------------------------------------
// Interactive Google Maps & GPS Geolocation Functions
// -------------------------------------------------------------
let mapInstance = null;
let markerInstance = null;
let currentTileLayer = null;
let currentLat = parseFloat(document.getElementById('delivery_lat')?.value) || 13.756331;
let currentLng = parseFloat(document.getElementById('delivery_lng')?.value) || 100.501765;

const tileLayerUrls = {
    google_road: {
        url: 'https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
        attribution: '© Google Maps & Purrfect Cat Transport',
        maxZoom: 20
    },
    google_satellite: {
        url: 'https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
        attribution: '© Google Maps Satellite & Purrfect Cat Transport',
        maxZoom: 20
    },
    osm: {
        url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        attribution: '© OpenStreetMap Contributors & Purrfect Cat Transport',
        maxZoom: 19
    }
};

function showGpsStatus(msg, isSuccess = true) {
    const banner = document.getElementById('gpsStatusBanner');
    if (!banner) return;
    banner.style.display = 'block';
    if (isSuccess) {
        banner.style.background = '#ECFDF5';
        banner.style.border = '1px solid #A7F3D0';
        banner.style.color = '#065F46';
    } else {
        banner.style.background = '#FEF2F2';
        banner.style.border = '1px solid #FECACA';
        banner.style.color = '#991B1B';
    }
    banner.innerHTML = msg;
    setTimeout(() => {
        if (banner) banner.style.display = 'none';
    }, 7000);
}

function initDeliveryMap() {
    if (mapInstance) {
        mapInstance.invalidateSize();
        return;
    }
    const mapDiv = document.getElementById('deliveryMap');
    if (!mapDiv) return;

    mapInstance = L.map('deliveryMap', {
        center: [currentLat, currentLng],
        zoom: 14,
        zoomControl: true
    });

    // Default to Google Maps Road Layer
    switchMapTileLayer('google_road');

    // Reliable inline SVG Pin Icon (never breaks due to external CDN)
    const customIcon = L.divIcon({
        className: 'custom-cat-pin',
        html: `<div style="background: linear-gradient(135deg, #FF6B4A, #FF8E72); color: white; width: 40px; height: 40px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(255,107,74,0.5); border: 2.5px solid #FFFFFF; cursor: pointer;">
                 <span style="transform: rotate(45deg); font-size: 20px; line-height: 1;">🐾</span>
               </div>`,
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -38]
    });

    markerInstance = L.marker([currentLat, currentLng], {
        draggable: true,
        icon: customIcon
    }).addTo(mapInstance);

    markerInstance.bindPopup("🐾 <strong>จุดส่งมอบน้องแมว (GPS)</strong><br>คลิกหรือลากหมุดบนแผนที่เพื่อระบุตำแหน่ง").openPopup();

    markerInstance.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        updateSelectedCoordinates(pos.lat, pos.lng, true);
        reverseGeocodeAddress(pos.lat, pos.lng);
    });

    mapInstance.on('click', function(e) {
        markerInstance.setLatLng(e.latlng);
        updateSelectedCoordinates(e.latlng.lat, e.latlng.lng, true);
        reverseGeocodeAddress(e.latlng.lat, e.latlng.lng);
    });

    setTimeout(() => {
        if (mapInstance) mapInstance.invalidateSize();
    }, 300);
}

function switchMapTileLayer(layerType) {
    if (!mapInstance) return;
    if (currentTileLayer) {
        mapInstance.removeLayer(currentTileLayer);
    }
    const config = tileLayerUrls[layerType] || tileLayerUrls.google_road;
    currentTileLayer = L.tileLayer(config.url, {
        maxZoom: config.maxZoom,
        attribution: config.attribution
    }).addTo(mapInstance);

    // Update active button styles
    const btnRoad = document.getElementById('btnLayerRoad');
    const btnSat = document.getElementById('btnLayerSat');
    const btnOsm = document.getElementById('btnLayerOsm');

    [btnRoad, btnSat, btnOsm].forEach(btn => {
        if (btn) {
            btn.style.background = 'var(--bg-card)';
            btn.style.color = 'var(--text-secondary)';
            btn.style.borderColor = 'var(--border-color)';
            btn.style.fontWeight = 'normal';
        }
    });

    if (layerType === 'google_road' && btnRoad) {
        btnRoad.style.background = '#DBEAFE';
        btnRoad.style.color = '#1E40AF';
        btnRoad.style.borderColor = '#93C5FD';
        btnRoad.style.fontWeight = '700';
    } else if (layerType === 'google_satellite' && btnSat) {
        btnSat.style.background = '#FEF3C7';
        btnSat.style.color = '#92400E';
        btnSat.style.borderColor = '#FCD34D';
        btnSat.style.fontWeight = '700';
    } else if (layerType === 'osm' && btnOsm) {
        btnOsm.style.background = '#ECFDF5';
        btnOsm.style.color = '#065F46';
        btnOsm.style.borderColor = '#A7F3D0';
        btnOsm.style.fontWeight = '700';
    }
}

function updateSelectedCoordinates(lat, lng, syncInputs = true) {
    lat = parseFloat(lat).toFixed(6);
    lng = parseFloat(lng).toFixed(6);
    currentLat = parseFloat(lat);
    currentLng = parseFloat(lng);

    const latInput = document.getElementById('delivery_lat');
    const lngInput = document.getElementById('delivery_lng');
    const displaySpan = document.getElementById('display_lat_lng');
    const gmapLink = document.getElementById('googleMapsLink');

    if (latInput && syncInputs) latInput.value = lat;
    if (lngInput && syncInputs) lngInput.value = lng;
    if (displaySpan) displaySpan.textContent = `Lat: ${lat}, Lng: ${lng}`;
    if (gmapLink) gmapLink.href = `https://www.google.com/maps?q=${lat},${lng}`;
}

function onManualCoordinateChange() {
    const latVal = parseFloat(document.getElementById('delivery_lat')?.value);
    const lngVal = parseFloat(document.getElementById('delivery_lng')?.value);
    if (!isNaN(latVal) && !isNaN(lngVal) && latVal >= -90 && latVal <= 90 && lngVal >= -180 && lngVal <= 180) {
        updateSelectedCoordinates(latVal, lngVal, false);
        if (mapInstance && markerInstance) {
            mapInstance.setView([latVal, lngVal], mapInstance.getZoom());
            markerInstance.setLatLng([latVal, lngVal]);
        }
    }
}

// Smart Parser: Extracts Lat,Lng from Google Maps URL, Share Link, or Coordinate Strings
function parseAndSetGoogleMapsInput(rawText) {
    if (!rawText) return;
    rawText = rawText.trim();

    // 1. Check for coordinates format: "13.7563, 100.5018" or "13.7563,100.5018"
    const coordRegex = /(-?\d+\.\d{3,})[\s,]+(-?\d+\.\d{3,})/;
    const coordMatch = rawText.match(coordRegex);

    if (coordMatch) {
        const lat = parseFloat(coordMatch[1]);
        const lng = parseFloat(coordMatch[2]);
        if (lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            updateSelectedCoordinates(lat, lng, true);
            if (mapInstance && markerInstance) {
                mapInstance.setView([lat, lng], 16);
                markerInstance.setLatLng([lat, lng]);
            }
            reverseGeocodeAddress(lat, lng);
            showGpsStatus(`✓ แปลงพิกัดจาก Google Maps สำเร็จ: Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`, true);
            return;
        }
    }

    // 2. If it's a URL or place query, search it via Nominatim
    if (rawText.length > 2) {
        document.getElementById('mapSearchInput').value = rawText.replace(/https?:\/\/[^\s]+/g, '').trim() || rawText;
        searchLocationOnMap(rawText);
    }
}

function setPresetCoordinate(lat, lng, addressHint) {
    updateSelectedCoordinates(lat, lng, true);
    const container = document.getElementById('mapPickerContainer');
    if (container && container.style.display === 'none') {
        toggleMapPicker();
    }
    if (mapInstance && markerInstance) {
        mapInstance.setView([lat, lng], 15);
        markerInstance.setLatLng([lat, lng]);
    }
    const addrArea = document.getElementById('delivery_address');
    if (addrArea && (!addrArea.value.trim() || addrArea.value.includes('กรุงเทพฯ') || addrArea.value.includes('อำเภอ') || addrArea.value.includes('เมือง'))) {
        addrArea.value = addressHint;
        autoResizeAddress(addrArea);
    }
    showGpsStatus(`✓ เลือกพิกัด: <strong>${addressHint}</strong> (Lat: ${lat}, Lng: ${lng}) เรียบร้อยแล้ว`, true);
}

function toggleMapPicker() {
    const container = document.getElementById('mapPickerContainer');
    const label = document.getElementById('mapToggleLabel');
    if (!container) return;

    if (container.style.display === 'none' || container.style.display === '') {
        container.style.display = 'block';
        if (label) label.textContent = 'ย่อ แผนที่ GPS';
        setTimeout(() => {
            initDeliveryMap();
            if (mapInstance) {
                mapInstance.invalidateSize();
                mapInstance.setView([currentLat, currentLng], mapInstance.getZoom());
            }
        }, 150);
    } else {
        container.style.display = 'none';
        if (label) label.textContent = 'ขยาย แผนที่ GPS';
    }
}

function locateCurrentGPS() {
    const btn = document.getElementById('btnCurrentGPS');
    const oldText = btn ? btn.innerHTML : '';
    if (btn) btn.innerHTML = '⏳ กำลังหา GPS...';
    showGpsStatus('⏳ กำลังค้นหาตำแหน่งพิกัด GPS อุปกรณ์ของคุณด้วยความแม่นยำสูง...', true);

    const container = document.getElementById('mapPickerContainer');
    if (container && container.style.display === 'none') {
        toggleMapPicker();
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                updateSelectedCoordinates(lat, lng, true);
                initDeliveryMap();
                if (mapInstance && markerInstance) {
                    mapInstance.setView([lat, lng], 16);
                    markerInstance.setLatLng([lat, lng]);
                    markerInstance.openPopup();
                }
                reverseGeocodeAddress(lat, lng);
                if (btn) btn.innerHTML = oldText;
                showGpsStatus(`✓ ตรวจพบตำแหน่ง GPS ของคุณแล้ว: Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)} (ความแม่นยำ ~${Math.round(pos.coords.accuracy || 10)}m)`, true);
            },
            (err) => {
                console.warn('Browser GPS error, trying IP-based fallback:', err);
                fetch('https://ipapi.co/json/')
                    .then(res => res.json())
                    .then(ipData => {
                        if (ipData && ipData.latitude && ipData.longitude) {
                            const lat = ipData.latitude;
                            const lng = ipData.longitude;
                            updateSelectedCoordinates(lat, lng, true);
                            initDeliveryMap();
                            if (mapInstance && markerInstance) {
                                mapInstance.setView([lat, lng], 14);
                                markerInstance.setLatLng([lat, lng]);
                            }
                            reverseGeocodeAddress(lat, lng);
                            showGpsStatus(`✓ ประมาณพิกัดจากพื้นที่เชื่อมต่อ (${ipData.city || 'ประเทศไทย'}): Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)} • คุณสามารถคลิกหรือลากหมุดบนแผนที่เพื่อความแม่นยำได้`, true);
                        } else {
                            throw new Error('No IP coords');
                        }
                    })
                    .catch(() => {
                        showGpsStatus(`⚠️ ไม่สามารถดึงพิกัด GPS ได้ (${err.message}) กรุณาคลิกเลือกตำแหน่งบนแผนที่ Google Maps หรือเลือกพิกัดด่วนด้านบน`, false);
                    })
                    .finally(() => {
                        if (btn) btn.innerHTML = oldText;
                    });
            },
            { enableHighAccuracy: true, timeout: 9000, maximumAge: 0 }
        );
    } else {
        if (btn) btn.innerHTML = oldText;
        showGpsStatus('เบราว์เซอร์ไม่รองรับ GPS กรุณาคลิกเลือกตำแหน่งบนแผนที่แทน', false);
    }
}

function searchLocationOnMap(queryParam) {
    const query = queryParam || document.getElementById('mapSearchInput')?.value.trim();
    if (!query) return;

    showGpsStatus(`⏳ กำลังค้นหาสถานที่ "${query}"...`, true);

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=th&limit=1`)
        .then(res => res.json())
        .then(data => {
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                updateSelectedCoordinates(lat, lng, true);
                initDeliveryMap();
                if (mapInstance && markerInstance) {
                    mapInstance.setView([lat, lng], 15);
                    markerInstance.setLatLng([lat, lng]);
                    markerInstance.openPopup();
                }
                const addrArea = document.getElementById('delivery_address');
                if (addrArea && !addrArea.value.trim()) {
                    addrArea.value = data[0].display_name;
                    autoResizeAddress(addrArea);
                }
                showGpsStatus(`✓ ค้นพบสถานที่: <strong>${data[0].display_name}</strong>`, true);
            } else {
                showGpsStatus(`⚠️ ไม่พบสถานที่: "${query}" กรุณาลองใช้คำค้นหาอื่นหรือคลิกเลือกบนแผนที่`, false);
            }
        })
        .catch(err => {
            console.error('Map search error:', err);
            showGpsStatus('เกิดข้อผิดพลาดในการค้นหาสถานที่', false);
        });
}

function reverseGeocodeAddress(lat, lng) {
    const targetLat = lat || currentLat;
    const targetLng = lng || currentLng;

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${targetLat}&lon=${targetLng}&accept-language=th`)
        .then(res => res.json())
        .then(data => {
            if (data && data.display_name) {
                const addrArea = document.getElementById('delivery_address');
                if (addrArea) {
                    addrArea.value = data.display_name;
                    autoResizeAddress(addrArea);
                    showGpsStatus(`✓ อัปเดตที่อยู่จัดส่งจากพิกัด: <strong>${data.display_name}</strong>`, true);
                }
            }
        })
        .catch(err => console.error(err));
}
</script>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
