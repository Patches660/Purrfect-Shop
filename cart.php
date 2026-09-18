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
$default_bank = $currUser ? ($currUser['bank_name'] ?? 'ธนาคารกสิกรไทย (KBANK)') : 'ธนาคารกสิกรไทย (KBANK)';
$default_bank_account = $currUser ? ($currUser['bank_account'] ?? '') : '';

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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    if (!empty($_SESSION['cart'])) {
        $is_test_checkout = isset($_POST['is_test_mode']);
        
        $customer_name = trim($_POST['customer_name'] ?? '');
        $customer_phone = trim($_POST['customer_phone'] ?? '');
        $customer_email = trim($_POST['customer_email'] ?? '');
        $delivery_address = trim($_POST['delivery_address'] ?? '');
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
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 0.8rem;">
                                <label style="border: 1.5px solid var(--primary-coral); background: #FFF7F3; border-radius: 10px; padding: 10px; cursor: pointer; display: flex; align-items: flex-start; gap: 8px;">
                                    <input type="radio" name="shipping_method" value="pet_taxi" checked style="margin-top: 3px; accent-color: var(--primary-coral);">
                                    <div>
                                        <strong style="font-size: 0.88rem; color: var(--text-main); display: block;">🚐 รถตู้ Pet Taxi ปรับอากาศ</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">คุมอุณหภูมิ 24-26°C + พี่เลี้ยงดูแล</span>
                                        <span style="font-size: 0.75rem; color: #047857; font-weight: 700;">ฟรี (โปรโมชั่นฟาร์ม)</span>
                                    </div>
                                </label>

                                <label style="border: 1.5px solid var(--border-color); background: var(--bg-card); border-radius: 10px; padding: 10px; cursor: pointer; display: flex; align-items: flex-start; gap: 8px;">
                                    <input type="radio" name="shipping_method" value="air_cargo" style="margin-top: 3px; accent-color: var(--primary-coral);">
                                    <div>
                                        <strong style="font-size: 0.88rem; color: var(--text-main); display: block;">✈️ เครื่องบิน Pet Air Cargo</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">มาตรฐาน IATA สำหรับต่างจังหวัด</span>
                                        <span style="font-size: 0.75rem; color: #B45309; font-weight: 700;">+฿1,200</span>
                                    </div>
                                </label>

                                <label style="border: 1.5px solid var(--border-color); background: var(--bg-card); border-radius: 10px; padding: 10px; cursor: pointer; display: flex; align-items: flex-start; gap: 8px;">
                                    <input type="radio" name="shipping_method" value="farm_pickup" style="margin-top: 3px; accent-color: var(--primary-coral);">
                                    <div>
                                        <strong style="font-size: 0.88rem; color: var(--text-main); display: block;">🏡 นัดรับด้วยตนเองที่ฟาร์ม</strong>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Meet & Greet รับคำแนะนำฟรี</span>
                                        <span style="font-size: 0.75rem; color: #047857; font-weight: 700;">ฟรี (กิฟต์เซ็ตพิเศษ)</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="delivery_address">ที่อยู่สำหรับจัดส่งน้องแมว (บ้าน / คอนโด / จังหวัด / รหัสไปรษณีย์) *</label>
                            <textarea name="delivery_address" id="delivery_address" class="form-control" rows="2" 
                                      placeholder="เช่น 123/45 หมู่บ้านสุขใจ ซอยอารีย์ ถนนพหลโยธิน แขวงสามเสนใน เขตพญาไท กรุงเทพฯ 10400" required><?php echo htmlspecialchars($default_address); ?></textarea>
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
                        <div class="payment-methods-grid" id="payment-methods-grid">
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
                        </div>

                        <!-- Dynamic Channel Detail Panels -->
                        <div id="payment-details-container">
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
    const panels = ['bank_transfer', 'promptpay', 'credit_card', 'cod'];
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

        // Visual feedback
        if (paymentContainer) paymentContainer.style.opacity = '0.5';
        if (methodsGrid) methodsGrid.style.opacity = '0.5';
        if (testNotice) testNotice.style.display = 'block';
    } else {
        // Restore constraints
        if (custName) custName.required = true;
        if (custPhone) custPhone.required = true;
        if (delivAddr) delivAddr.required = true;

        const currentChannel = document.querySelector('input[name="payment_channel"]:checked')?.value || 'bank_transfer';
        if (bankAccountInput) bankAccountInput.required = (currentChannel === 'bank_transfer');

        if (paymentContainer) paymentContainer.style.opacity = '1';
        if (methodsGrid) methodsGrid.style.opacity = '1';
        if (testNotice) testNotice.style.display = 'none';
    }
}
</script>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
