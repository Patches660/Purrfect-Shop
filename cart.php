<?php
require_once __DIR__ . '/data.php';

$checkout_success = false;
$invoice_data = [];
$invoice_total = 0;
$invoice_id = "";

// Handle Cart Actions
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
    
    if ($action === 'checkout') {
        if (!empty($_SESSION['cart'])) {
            $checkout_success = true;
            $invoice_data = $_SESSION['cart'];
            $invoice_total = getCartTotal();
            $invoice_id = 'CCS-' . strtoupper(substr(md5(uniqid()), 0, 8));
            // Clear cart
            $_SESSION['cart'] = [];
        }
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="section-header">
    <h1 class="section-title">ตะกร้าสินค้า</h1>
    <p class="section-subtitle">ตรวจสอบรายการแมวและสเปกโปรแกรมที่คุณเลือกสรร</p>
</div>

<?php if ($checkout_success): ?>
    <!-- Invoice Card Display -->
    <div class="invoice-card">
        <div class="invoice-header">
            <span style="font-size: 2.2rem; display: block; margin-bottom: 0.5rem;">🎉</span>
            <h2 style="font-family: 'Orbitron'; font-weight: 800; font-size: 1.6rem; color: var(--primary-gold);">ORDER SUCCESSFUL</h2>
            <div class="invoice-badge">ใบเสร็จชำระเงินจำลอง</div>
            <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-muted);">
                รหัสคำสั่งซื้อ: <strong style="color: white; font-family: 'Orbitron';"><?php echo $invoice_id; ?></strong><br>
                วันที่สั่งซื้อ: <?php echo date('d-m-Y H:i:s'); ?>
            </p>
        </div>

        <div class="invoice-item-list">
            <h3 style="font-family: 'Orbitron'; font-size: 1rem; border-bottom: 1px solid var(--border-glass); padding-bottom: 0.5rem; margin-bottom: 1rem;">รายการสั่งซื้อ</h3>
            
            <?php foreach ($invoice_data as $item): ?>
                <div class="invoice-row">
                    <div>
                        <strong><?php echo $item['name']; ?></strong>
                        <?php if (!empty($item['details'])): ?>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.1rem;">
                                โน้ต: <?php echo htmlspecialchars($item['details']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <span style="font-family: 'Orbitron';"><?php echo number_format($item['price']); ?> ฿</span>
                </div>
            <?php endforeach; ?>

            <?php 
            $subtotal = $invoice_total;
            $vat = $subtotal * 0.07;
            $total_paid = $subtotal + $vat;
            ?>

            <div class="invoice-row" style="margin-top: 1.5rem; border-top: 1px dashed var(--border-glass); padding-top: 1rem; color: var(--text-muted);">
                <span>ราคารวมสินค้า:</span>
                <span><?php echo number_format($subtotal); ?> ฿</span>
            </div>
            <div class="invoice-row" style="color: var(--text-muted);">
                <span>ภาษีมูลค่าเพิ่ม (VAT 7%):</span>
                <span><?php echo number_format($vat, 2); ?> ฿</span>
            </div>
            <div class="invoice-row invoice-total">
                <span>ยอดเงินที่ชำระทั้งหมด:</span>
                <span><?php echo number_format($total_paid, 2); ?> ฿</span>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                ขอบคุณที่สั่งซื้อสินค้าและโปรแกรมกับ <strong>Cat Cyber Shop</strong><br>
                ทีมงานพัฒนาของเรา (นำโดย Pattanun 66040233148 IT) จะติดต่อคุณเพื่อส่งมอบโดยเร็วที่สุด!
            </p>
            <a href="products.php" class="btn btn-primary">ช้อปปิ้งต่อ 🐱💻</a>
        </div>
    </div>

<?php else: ?>
    <!-- Cart Overview Display -->
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <div class="empty-icon">🛒</div>
            <h2 style="font-family: 'Orbitron'; font-weight: 700; margin-bottom: 0.8rem;">ตะกร้าว่างเปล่า</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">คุณยังไม่มีแมวหรือสเปกโปรแกรมงานสั่งทำใดๆ ในตะกร้าสินค้า</p>
            <a href="products.php" class="btn btn-primary">ดูหน้าร้านและสั่งซื้อสินค้า 💻</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <!-- Cart Items List -->
            <div class="cart-items-wrapper">
                <?php foreach ($_SESSION['cart'] as $key => $item): ?>
                    <div class="cart-item">
                        <div class="item-info">
                            <div class="item-pic">
                                <?php echo $item['type'] === 'cat' ? '🐱' : '💻'; ?>
                            </div>
                            <div class="item-details">
                                <h4><?php echo $item['name']; ?></h4>
                                <p>
                                    <?php 
                                    if ($item['type'] === 'program') {
                                        echo 'ขอบเขต: ' . htmlspecialchars($item['details']);
                                    } else {
                                        echo 'เพื่อนคู่หู debug แบบขี้เซา';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="item-pricing">
                            <span class="item-price"><?php echo number_format($item['price']); ?> ฿</span>
                            <a href="cart.php?action=remove&id=<?php echo urlencode($key); ?>" class="remove-btn" title="ลบรายการนี้">🗑️</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div style="margin-top: 1rem; display: flex; justify-content: space-between;">
                    <a href="products.php" class="btn btn-secondary" style="font-size: 0.85rem;">← เลือกซื้อสินค้าเพิ่ม</a>
                    <a href="cart.php?action=clear" class="btn btn-secondary" style="font-size: 0.85rem; border-color: var(--accent-pink); color: var(--accent-pink);">ล้างตะกร้าสินค้า 🧹</a>
                </div>
            </div>

            <!-- Checkout Box -->
            <div>
                <div class="cart-summary">
                    <h3 class="summary-title">สรุปยอดคำสั่งซื้อ</h3>
                    
                    <div class="summary-row">
                        <span>จำนวนสินค้า:</span>
                        <span><?php echo getCartCount(); ?> ชิ้น</span>
                    </div>
                    <div class="summary-row">
                        <span>ราคาไม่รวมภาษี:</span>
                        <span><?php echo number_format(getCartTotal()); ?> ฿</span>
                    </div>
                    <div class="summary-row">
                        <span>ภาษีมูลค่าเพิ่ม (VAT 7%):</span>
                        <span><?php echo number_format(getCartTotal() * 0.07, 2); ?> ฿</span>
                    </div>
                    
                    <div class="summary-row total-row">
                        <span>รวมสุทธิ:</span>
                        <span><?php echo number_format(getCartTotal() * 1.07, 2); ?> ฿</span>
                    </div>

                    <a href="cart.php?action=checkout" class="btn btn-danger" style="width: 100%; margin-top: 1.5rem; text-align: center;">
                        สั่งซื้อและออกใบเสร็จ 🚀
                    </a>
                    
                    <p style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-top: 1rem;">
                        *ระบบนี้เป็นร้านค้าจำลองเพื่อส่งชิ้นงานประกอบการทดสอบ (Workshop 2567 Semester 4)
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
