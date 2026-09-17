<?php
require_once __DIR__ . '/data.php';

// Security check: Must be admin
if (!isAdmin()) {
    require_once __DIR__ . '/header.php';
    ?>
    <div style="max-width: 600px; margin: 4rem auto; text-align: center; background: var(--bg-card); padding: 3rem 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-md);">
        <div style="font-size: 4rem; margin-bottom: 1rem;">🔒</div>
        <h2 style="color: #EF4444; font-size: 1.5rem; margin-bottom: 0.8rem;">จำกัดสิทธิ์เฉพาะผู้ดูแลระบบ (Admin Only)</h2>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem; line-height: 1.6;">
            คุณไม่มีสิทธิ์เข้าถึงหน้านี้ กรุณาเข้าสู่ระบบด้วยบัญชีผู้ดูแลระบบ (Admin) เช่น <strong>admin</strong> (รหัสผ่าน <strong>admin123</strong>) หรือ <strong>Meow</strong>
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="login.php" class="btn btn-primary">เข้าสู่ระบบด้วยบัญชี Admin 🔑</a>
            <a href="index.php" class="btn btn-secondary">กลับหน้าหลัก 🏠</a>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/footer.php';
    exit;
}

$active_tab = $_GET['tab'] ?? 'dashboard';
$success_msg = "";
$error_msg = "";

// -------------------------------------------------------------
// POST Handlers
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // 1. Update Order Status
    if ($action === 'update_order_status') {
        $order_id = trim($_POST['order_id'] ?? '');
        $new_status = trim($_POST['new_status'] ?? '');
        if (!empty($order_id) && !empty($new_status)) {
            if (updateOrderStatus($order_id, $new_status)) {
                $success_msg = "✓ อัปเดตสถานะคำสั่งซื้อ #{$order_id} เป็น '{$new_status}' สำเร็จเรียบร้อยแล้ว!";
                $active_tab = 'orders';
            } else {
                $error_msg = "ไม่สามารถอัปเดตสถานะคำสั่งซื้อ #{$order_id} ได้";
            }
        }
    }

    // 2. Delete Order
    if ($action === 'delete_order') {
        $order_id = trim($_POST['order_id'] ?? '');
        if (!empty($order_id)) {
            if (deleteOrder($order_id)) {
                $success_msg = "✓ ลบคำสั่งซื้อ #{$order_id} สำเร็จแล้ว";
                $active_tab = 'orders';
            } else {
                $error_msg = "ไม่สามารถลบคำสั่งซื้อได้";
            }
        }
    }

    // 3. Update User Role
    if ($action === 'update_user_role') {
        $user_id = trim($_POST['user_id'] ?? '');
        $new_role = trim($_POST['new_role'] ?? 'customer');
        if (!empty($user_id)) {
            if (updateUserRole($user_id, $new_role)) {
                $role_name = $new_role === 'admin' ? 'ผู้ดูแลระบบ (Admin)' : 'ลูกค้าสมาชิก (Customer)';
                $success_msg = "✓ อัปเดตบทบาทของสมาชิกรหัส {$user_id} เป็น '{$role_name}' เรียบร้อยแล้ว!";
                $active_tab = 'users';
            } else {
                $error_msg = "ไม่สามารถอัปเดตบทบาทผู้ใช้ได้";
            }
        }
    }

    // 4. Delete User
    if ($action === 'delete_user') {
        $user_id = trim($_POST['user_id'] ?? '');
        if (!empty($user_id)) {
            $res = deleteUser($user_id);
            if ($res['success']) {
                $success_msg = "✓ " . $res['message'];
                $active_tab = 'users';
            } else {
                $error_msg = $res['message'];
            }
        }
    }

    // 5. Add User by Admin
    if ($action === 'add_user') {
        $data = [
            'fullname' => trim($_POST['fullname'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '123456',
            'role' => $_POST['role'] ?? 'customer'
        ];
        if (empty($data['username']) || empty($data['email'])) {
            $error_msg = "กรุณากรอกชื่อผู้ใช้และอีเมล";
        } else {
            $res = addUserByAdmin($data);
            if ($res['success']) {
                $success_msg = "✓ เพิ่มบัญชีผู้ใช้ @{$data['username']} ในระบบเรียบร้อยแล้ว!";
                $active_tab = 'users';
            } else {
                $error_msg = $res['message'];
            }
        }
    }

    // 6. Broadcast Newsletter Campaign
    if ($action === 'send_newsletter') {
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $target_audience = $_POST['target_audience'] ?? 'all';
        $selected_items = $_POST['selected_items'] ?? [];

        if (empty($title) || empty($message)) {
            $error_msg = "กรุณากรอกหัวข้อข่าวสารและข้อความประชาสัมพันธ์";
        } else {
            $campaign = sendNewsletterCampaign($title, $message, $selected_items, $target_audience, $_SESSION['user']['username'] ?? 'admin');
            $success_msg = "✓ ส่งแคมเปญข่าวสาร '{$title}' ไปยังสมาชิก ({$target_audience}) เรียบร้อยแล้ว! สมาชิกสามารถเปิดอ่านได้ในหน้าโปรไฟล์ทันที";
            $active_tab = 'newsletter';
        }
    }
}

// Fetch all data
$all_users = getUsers();
$all_orders = getOrders();
$all_newsletters = getNewsletters();

// Compute Dashboard Metrics
$total_revenue = 0;
$status_counts = [
    'preparing' => 0,
    'delivering' => 0,
    'delivered' => 0,
    'cancelled' => 0
];

foreach ($all_orders as $o) {
    $total_revenue += $o['total'] ?? 0;
    $status = $o['status'] ?? '';
    if (stripos($status, 'กำลังเตรียม') !== false || stripos($status, 'ชำระเงินแล้ว') !== false) {
        $status_counts['preparing']++;
    } elseif (stripos($status, 'จัดส่ง') !== false && stripos($status, 'สำเร็จ') === false) {
        $status_counts['delivering']++;
    } elseif (stripos($status, 'สำเร็จ') !== false) {
        $status_counts['delivered']++;
    } elseif (stripos($status, 'ยกเลิก') !== false) {
        $status_counts['cancelled']++;
    }
}

$admin_count = 0;
$customer_count = 0;
foreach ($all_users as $u) {
    if (($u['role'] ?? '') === 'admin') {
        $admin_count++;
    } else {
        $customer_count++;
    }
}

require_once __DIR__ . '/header.php';
?>

<style>
/* Admin Specific Styles */
.admin-container {
    max-width: 1200px;
    margin: 0 auto 4rem auto;
    padding: 0 15px;
}

.admin-header {
    background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
    color: #FFFFFF;
    border-radius: var(--radius-lg);
    padding: 2rem 2.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-md);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.admin-title-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 117, 86, 0.2);
    border: 1px solid var(--primary-coral);
    color: #FF8A65;
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 700;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
}

.admin-tabs-nav {
    display: flex;
    gap: 8px;
    margin-bottom: 2rem;
    border-bottom: 2px solid var(--border-color);
    padding-bottom: 10px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    white-space: nowrap;
}

.admin-tab-btn {
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 700;
    background: var(--bg-card);
    border: 1.5px solid var(--border-color);
    color: var(--text-secondary);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.admin-tab-btn:hover {
    background: var(--bg-card-subtle);
    color: var(--primary-coral);
    border-color: var(--primary-coral);
}

.admin-tab-btn.active {
    background: var(--primary-coral);
    color: #FFFFFF;
    border-color: var(--primary-coral);
    box-shadow: 0 4px 12px rgba(255, 117, 86, 0.3);
}

/* Stats Cards Grid */
.stat-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.2rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 1.4rem 1.6rem;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 1.2rem;
}

.stat-icon-wrap {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
}

.stat-val {
    font-family: 'Outfit', sans-serif;
    font-size: 1.7rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1.2;
}

.stat-lbl {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--text-muted);
}

/* Responsive Table Wrapper */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    background: var(--bg-card);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.92rem;
}

.admin-table th {
    background: var(--bg-card-subtle);
    padding: 12px 16px;
    font-weight: 700;
    color: var(--text-main);
    text-align: left;
    border-bottom: 2px solid var(--border-color);
    white-space: nowrap;
}

.admin-table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
    color: var(--text-main);
}

.admin-table tr:last-child td {
    border-bottom: none;
}

.admin-table tr:hover {
    background: rgba(0,0,0,0.015);
}

/* Product selector cards in Newsletter */
.item-selection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 12px;
    max-height: 380px;
    overflow-y: auto;
    padding: 12px;
    border: 1.5px solid var(--border-color);
    border-radius: var(--radius-md);
    background: var(--bg-card-subtle);
}

.item-select-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.item-select-card:hover {
    border-color: var(--primary-coral);
}

.item-select-card input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--primary-coral);
}

.item-select-thumb {
    width: 44px;
    height: 44px;
    border-radius: 6px;
    object-fit: cover;
}

@media (max-width: 768px) {
    .admin-header {
        padding: 1.5rem;
    }
    .stat-cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="admin-container">
    <!-- Header Banner -->
    <div class="admin-header">
        <div>
            <div class="admin-title-badge">🛠️ MANAGEMENT CONSOLE</div>
            <h1 style="font-size: 1.85rem; font-weight: 800; margin: 0 0 0.3rem 0;">ระบบจัดการหลังบ้าน (Admin Control Panel)</h1>
            <p style="color: #94A3B8; margin: 0; font-size: 0.95rem;">
                ยินดีต้อนรับคุณ <strong><?php echo htmlspecialchars($_SESSION['user']['fullname'] ?? 'Admin'); ?></strong> (สิทธิ์: ผู้ดูแลระบบสูงสุด)
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="index.php" class="btn btn-secondary btn-sm" style="background: rgba(255,255,255,0.1); color: #FFFFFF; border-color: rgba(255,255,255,0.2);">
                🏠 กลับหน้าร้านค้า
            </a>
            <a href="profile.php" class="btn btn-secondary btn-sm" style="background: rgba(255,255,255,0.1); color: #FFFFFF; border-color: rgba(255,255,255,0.2);">
                👤 โปรไฟล์ของฉัน
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    <?php if (!empty($success_msg)): ?>
        <div class="alert-box alert-success" style="margin-bottom: 1.5rem;">
            <?php echo htmlspecialchars($success_msg); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="alert-box alert-danger" style="margin-bottom: 1.5rem;">
            ⚠️ <?php echo htmlspecialchars($error_msg); ?>
        </div>
    <?php endif; ?>

    <!-- Navigation Tabs -->
    <div class="admin-tabs-nav">
        <a href="admin.php?tab=dashboard" class="admin-tab-btn <?php echo $active_tab === 'dashboard' ? 'active' : ''; ?>">
            📊 Dashboard สรุปภาพรวม
        </a>
        <a href="admin.php?tab=orders" class="admin-tab-btn <?php echo $active_tab === 'orders' ? 'active' : ''; ?>">
            📦 จัดการคำสั่งซื้อ (<?php echo count($all_orders); ?>)
        </a>
        <a href="admin.php?tab=users" class="admin-tab-btn <?php echo $active_tab === 'users' ? 'active' : ''; ?>">
            👥 จัดการบัญชีผู้ใช้ (<?php echo count($all_users); ?>)
        </a>
        <a href="admin.php?tab=newsletter" class="admin-tab-btn <?php echo $active_tab === 'newsletter' ? 'active' : ''; ?>">
            📢 ส่งข่าวสาร & โปรโมชัน (<?php echo count($all_newsletters); ?>)
        </a>
    </div>

    <!-- =========================================================
         TAB 1: DASHBOARD
         ========================================================= -->
    <?php if ($active_tab === 'dashboard'): ?>
        <!-- Stat Cards Grid -->
        <div class="stat-cards-grid">
            <div class="stat-card">
                <div class="stat-icon-wrap" style="background: rgba(16, 185, 129, 0.12); color: #10B981;">💰</div>
                <div>
                    <div class="stat-lbl">ยอดขายสะสมสุทธิ</div>
                    <div class="stat-val" style="color: #059669;"><?php echo number_format($total_revenue); ?> ฿</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrap" style="background: rgba(255, 117, 86, 0.12); color: var(--primary-coral);">📦</div>
                <div>
                    <div class="stat-lbl">คำสั่งซื้อทั้งหมด</div>
                    <div class="stat-val" style="color: var(--primary-coral);"><?php echo count($all_orders); ?> รายการ</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrap" style="background: rgba(59, 130, 246, 0.12); color: #3B82F6;">🐱</div>
                <div>
                    <div class="stat-lbl">น้องแมวในระบบ</div>
                    <div class="stat-val"><?php echo count($cats); ?> ตัว</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrap" style="background: rgba(245, 158, 11, 0.12); color: #F59E0B;">👥</div>
                <div>
                    <div class="stat-lbl">สมาชิกลูกค้า / Admin</div>
                    <div class="stat-val"><?php echo $customer_count; ?> / <?php echo $admin_count; ?></div>
                </div>
            </div>
        </div>

        <!-- Orders Breakdown & Quick Actions -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Status Breakdown -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem; box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.2rem; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    🚚 สถานะการจัดส่งคำสั่งซื้อ
                </h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #FEF3C7; border-radius: 8px; color: #92400E;">
                        <span style="font-weight: 600;">⏳ กำลังเตรียมส่งมอบ (Preparing)</span>
                        <strong style="font-family: 'Outfit'; font-size: 1.2rem;"><?php echo $status_counts['preparing']; ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #DBEAFE; border-radius: 8px; color: #1E40AF;">
                        <span style="font-weight: 600;">🚐 อยู่ระหว่างจัดส่ง (Out for Delivery)</span>
                        <strong style="font-family: 'Outfit'; font-size: 1.2rem;"><?php echo $status_counts['delivering']; ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #DCFCE7; border-radius: 8px; color: #166534;">
                        <span style="font-weight: 600;">✓ ส่งมอบสำเร็จแล้ว (Delivered)</span>
                        <strong style="font-family: 'Outfit'; font-size: 1.2rem;"><?php echo $status_counts['delivered']; ?></strong>
                    </div>
                </div>
            </div>

            <!-- Quick Management Shortcuts -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem; box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.2rem; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    ⚡ เครื่องมือด่วน (Quick Actions)
                </h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="admin.php?tab=newsletter" class="btn btn-primary" style="justify-content: center; gap: 8px;">
                        📢 ส่งข่าวสาร & แคมเปญโปรโมชันใหม่
                    </a>
                    <a href="admin.php?tab=orders" class="btn btn-secondary" style="justify-content: center; gap: 8px;">
                        📦 ตรวจสอบและอัปเดตสถานะออเดอร์
                    </a>
                    <a href="admin.php?tab=users" class="btn btn-secondary" style="justify-content: center; gap: 8px;">
                        👥 ตรวจสอบบัญชีลูกค้า / แต่งตั้ง Admin
                    </a>
                    <a href="welcome_sales_mockup.php" target="_blank" class="btn btn-secondary" style="justify-content: center; gap: 8px;">
                        ✉️ ระบบส่งข้อมูล (ส่งอีเมล Content ขายสินค้า & ตั้งค่า SMTP)
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem; box-shadow: var(--shadow-sm);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; gap: 10px;">
                <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: var(--text-main);">
                    🕒 คำสั่งซื้อล่าสุด (Recent Orders)
                </h3>
                <a href="admin.php?tab=orders" style="color: var(--primary-coral); font-weight: 600; font-size: 0.9rem; text-decoration: none;">
                    ดูทั้งหมด &rarr;
                </a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>รหัสออเดอร์</th>
                            <th>ผู้สั่งจอง</th>
                            <th>น้องแมวที่รับเลี้ยง</th>
                            <th>ยอดสุทธิ</th>
                            <th>สถานะ</th>
                            <th>เอกสาร</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($all_orders, 0, 5) as $ord): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--primary-coral); font-family: 'Outfit';">
                                        #<?php echo htmlspecialchars($ord['order_id']); ?>
                                    </strong>
                                    <div style="font-size: 0.78rem; color: var(--text-muted);"><?php echo htmlspecialchars($ord['created_at']); ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?php echo htmlspecialchars($ord['customer_name']); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($ord['customer_phone']); ?></div>
                                </td>
                                <td>
                                    <?php echo count($ord['items']); ?> รายการ
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">
                                        <?php echo htmlspecialchars($ord['items'][0]['name'] ?? ''); ?>
                                    </div>
                                </td>
                                <td>
                                    <strong style="font-family: 'Outfit'; color: var(--text-main);">
                                        <?php echo number_format($ord['total'], 2); ?> ฿
                                    </strong>
                                </td>
                                <td>
                                    <span style="font-size: 0.8rem; font-weight: 600; background: var(--bg-card-subtle); padding: 4px 10px; border-radius: 9999px; border: 1px solid var(--border-color);">
                                        <?php echo htmlspecialchars($ord['status'] ?? 'ยืนยันแล้ว'); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="order_letter.php?id=<?php echo urlencode($ord['order_id']); ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.8rem;">
                                        📜 จดหมายตอบรับ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- =========================================================
         TAB 2: ORDERS MANAGEMENT
         ========================================================= -->
    <?php if ($active_tab === 'orders'): ?>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.8rem; box-shadow: var(--shadow-sm);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0; color: var(--text-main);">
                        📦 รายการคำสั่งซื้อทั้งหมด (<?php echo count($all_orders); ?>)
                    </h2>
                    <p style="font-size: 0.88rem; color: var(--text-muted); margin: 4px 0 0 0;">
                        ปรับปรุงสถานะการส่งมอบ และออกหนังสือตอบรับการรับเลี้ยงอย่างเป็นทางการ
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>รหัสคำสั่งจอง</th>
                            <th>ข้อมูลผู้รับเลี้ยง</th>
                            <th>รายการที่เลือก</th>
                            <th>ยอดเงินสุทธิ & ชำระเงิน</th>
                            <th>สถานะคำสั่งซื้อ & อัปเดต</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_orders as $ord): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--primary-coral); font-family: 'Outfit'; font-size: 1rem;">
                                        #<?php echo htmlspecialchars($ord['order_id']); ?>
                                    </strong>
                                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 4px;">
                                        🕒 <?php echo htmlspecialchars($ord['created_at']); ?>
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">
                                        <?php echo htmlspecialchars($ord['customer_name']); ?>
                                    </div>
                                    <div style="font-size: 0.82rem; color: var(--text-muted);">
                                        📞 <?php echo htmlspecialchars($ord['customer_phone']); ?><br>
                                        ✉️ <?php echo htmlspecialchars($ord['customer_email']); ?><br>
                                        🏠 <?php echo htmlspecialchars(mb_strimwidth($ord['delivery_address'] ?? '', 0, 35, '...')); ?>
                                    </div>
                                </td>

                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <?php foreach ($ord['items'] as $item): ?>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="" style="width: 36px; height: 36px; border-radius: 6px; object-fit: cover; border: 1px solid var(--border-color);">
                                                <div style="font-size: 0.85rem;">
                                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                    <span style="color: var(--text-muted); font-size: 0.78rem;">(x<?php echo $item['qty']; ?>)</span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight: 800; font-family: 'Outfit'; font-size: 1.1rem; color: var(--primary-coral);">
                                        <?php echo number_format($ord['total'], 2); ?> ฿
                                    </div>
                                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                                        <?php echo htmlspecialchars($ord['payment_channel']); ?>
                                    </div>
                                </td>

                                <td>
                                    <form method="POST" action="admin.php?tab=orders" style="display: flex; flex-direction: column; gap: 6px;">
                                        <input type="hidden" name="action" value="update_order_status">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($ord['order_id']); ?>">
                                        <select name="new_status" style="padding: 6px 10px; border-radius: 6px; border: 1.5px solid var(--border-color); font-size: 0.82rem; background: var(--bg-card); color: var(--text-main); max-width: 220px;">
                                            <option value="ชำระเงินแล้ว / กำลังเตรียมส่งมอบ (Paid & Preparing)" <?php echo stripos($ord['status'], 'กำลังเตรียม') !== false ? 'selected' : ''; ?>>
                                                ⏳ กำลังเตรียมส่งมอบ
                                            </option>
                                            <option value="อยู่ระหว่างจัดส่งด้วยรถตู้แอร์ปรับอุณหภูมิ (Out for Delivery)" <?php echo stripos($ord['status'], 'อยู่ระหว่างจัดส่ง') !== false ? 'selected' : ''; ?>>
                                                🚐 อยู่ระหว่างจัดส่ง
                                            </option>
                                            <option value="จัดส่งน้องแมวสำเร็จเรียบร้อยแล้ว (Delivered)" <?php echo stripos($ord['status'], 'สำเร็จ') !== false ? 'selected' : ''; ?>>
                                                ✓ จัดส่งสำเร็จเรียบร้อยแล้ว
                                            </option>
                                            <option value="ยกเลิกคำสั่งซื้อ (Cancelled)" <?php echo stripos($ord['status'], 'ยกเลิก') !== false ? 'selected' : ''; ?>>
                                                ❌ ยกเลิกคำสั่งซื้อ
                                            </option>
                                        </select>
                                        <button type="submit" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.78rem; width: fit-content;">
                                            บันทึกสถานะ 💾
                                        </button>
                                    </form>
                                </td>

                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <a href="order_letter.php?id=<?php echo urlencode($ord['order_id']); ?>" target="_blank" class="btn btn-primary btn-sm" style="padding: 5px 10px; font-size: 0.8rem; text-decoration: none; text-align: center;">
                                            📜 ดูจดหมายตอบรับ
                                        </a>
                                        <form method="POST" action="admin.php?tab=orders" onsubmit="return confirm('ยืนยันลบคำสั่งซื้อ #<?php echo $ord['order_id']; ?> ใช่หรือไม่?');">
                                            <input type="hidden" name="action" value="delete_order">
                                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($ord['order_id']); ?>">
                                            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.75rem; color: #EF4444; border-color: rgba(239, 68, 68, 0.3); width: 100%;">
                                                🗑️ ลบออเดอร์
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- =========================================================
         TAB 3: USERS MANAGEMENT
         ========================================================= -->
    <?php if ($active_tab === 'users'): ?>
        <div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
            <!-- User List Table -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.8rem; box-shadow: var(--shadow-sm);">
                <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0 0 1.2rem 0; color: var(--text-main);">
                    👥 รายชื่อบัญชีผู้ใช้ในระบบ (<?php echo count($all_users); ?>)
                </h2>

                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ผู้ใช้งาน</th>
                                <th>การติดต่อ</th>
                                <th>บทบาท (Role)</th>
                                <th>สลับสิทธิ์</th>
                                <th>ลบ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_users as $u): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <img src="<?php echo htmlspecialchars($u['avatar'] ?? 'assets/images/logo.png'); ?>" alt="" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--primary-coral);">
                                            <div>
                                                <strong style="color: var(--text-main); font-size: 0.95rem;">
                                                    <?php echo htmlspecialchars($u['fullname']); ?>
                                                </strong>
                                                <div style="font-size: 0.8rem; color: var(--text-muted);">
                                                    @<?php echo htmlspecialchars($u['username']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div style="font-size: 0.85rem; color: var(--text-main);"><?php echo htmlspecialchars($u['email']); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($u['phone']); ?></div>
                                    </td>

                                    <td>
                                        <?php if (($u['role'] ?? '') === 'admin'): ?>
                                            <span style="background: #FEF3C7; color: #92400E; font-weight: 700; font-size: 0.78rem; padding: 4px 10px; border-radius: 9999px; border: 1px solid rgba(245, 158, 11, 0.4);">
                                                👑 ผู้ดูแลระบบ (Admin)
                                            </span>
                                        <?php else: ?>
                                            <span style="background: var(--bg-card-subtle); color: var(--text-secondary); font-weight: 600; font-size: 0.78rem; padding: 4px 10px; border-radius: 9999px; border: 1px solid var(--border-color);">
                                                👤 ลูกค้าสมาชิก (Customer)
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if (($u['username'] ?? '') === 'admin'): ?>
                                            <span style="font-size: 0.78rem; color: var(--text-muted); font-style: italic;">Master Admin</span>
                                        <?php else: ?>
                                            <form method="POST" action="admin.php?tab=users">
                                                <input type="hidden" name="action" value="update_user_role">
                                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($u['id']); ?>">
                                                <?php if (($u['role'] ?? '') === 'admin'): ?>
                                                    <input type="hidden" name="new_role" value="customer">
                                                    <button type="submit" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.75rem;">
                                                        ลดสิทธิ์เป็นลูกค้า
                                                    </button>
                                                <?php else: ?>
                                                    <input type="hidden" name="new_role" value="admin">
                                                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 4px 8px; font-size: 0.75rem; background: #D97706; border-color: #D97706;">
                                                        แต่งตั้งเป็น Admin 👑
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if (($u['username'] ?? '') !== 'admin'): ?>
                                            <form method="POST" action="admin.php?tab=users" onsubmit="return confirm('ยืนยันลบบัญชี @<?php echo $u['username']; ?> ใช่หรือไม่?');">
                                                <input type="hidden" name="action" value="delete_user">
                                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($u['id']); ?>">
                                                <button type="submit" style="background: none; border: none; color: #EF4444; cursor: pointer; font-size: 1rem;" title="ลบบัญชี">
                                                    🗑️
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add User Form -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem; box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 1rem 0; color: var(--text-main);">
                    ➕ เพิ่มบัญชีผู้ใช้ใหม่
                </h3>
                <form method="POST" action="admin.php?tab=users" style="display: flex; flex-direction: column; gap: 12px;">
                    <input type="hidden" name="action" value="add_user">

                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 4px;">ชื่อ-นามสกุล *</label>
                        <input type="text" name="fullname" required placeholder="เช่น สมชาย ใจดี" class="form-input" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card-subtle);">
                    </div>

                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 4px;">ชื่อผู้ใช้ (Username) *</label>
                        <input type="text" name="username" required placeholder="เช่น somchai_admin" class="form-input" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card-subtle);">
                    </div>

                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 4px;">อีเมล *</label>
                        <input type="email" name="email" required placeholder="somchai@example.com" class="form-input" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card-subtle);">
                    </div>

                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 4px;">เบอร์โทรศัพท์</label>
                        <input type="text" name="phone" placeholder="08xxxxxxxx" class="form-input" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card-subtle);">
                    </div>

                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 4px;">รหัสผ่านเริ่มต้น</label>
                        <input type="password" name="password" value="123456" class="form-input" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card-subtle);">
                    </div>

                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 4px;">ระดับสิทธิ์ (Role)</label>
                        <select name="role" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card-subtle); color: var(--text-main);">
                            <option value="customer">👤 ลูกค้าสมาชิก (Customer)</option>
                            <option value="admin">👑 ผู้ดูแลระบบ (Admin)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 8px; justify-content: center;">
                        บันทึกบัญชีใหม่ ✨
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- =========================================================
         TAB 4: NEWSLETTER & MARKETING BROADCAST
         ========================================================= -->
    <?php if ($active_tab === 'newsletter'): ?>
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.5rem; align-items: start;">
            <!-- Broadcast Composer Form -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.8rem; box-shadow: var(--shadow-sm);">
                <div style="margin-bottom: 1.4rem;">
                    <span style="background: rgba(255, 117, 86, 0.12); color: var(--primary-coral); font-size: 0.78rem; font-weight: 800; padding: 3px 10px; border-radius: 9999px;">
                        MARKETING BROADCAST SYSTEM
                    </span>
                    <h2 style="font-size: 1.35rem; font-weight: 800; margin: 6px 0 2px 0; color: var(--text-main);">
                        📢 ส่งข่าวสาร & โปรโมชันถึงสมาชิก (แบบที่ 2)
                    </h2>
                    <p style="font-size: 0.88rem; color: var(--text-muted); margin: 0;">
                        แอดมินสามารถกดเลือกน้องแมวหรือแพ็กเกจสินค้าเพื่อส่งโปรโมชันไปยังสมาชิกระบบได้เอง
                    </p>
                </div>

                <form method="POST" action="admin.php?tab=newsletter">
                    <input type="hidden" name="action" value="send_newsletter">

                    <div style="margin-bottom: 14px;">
                        <label style="font-weight: 700; font-size: 0.9rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            📌 หัวข้อข่าวสาร / แคมเปญ *
                        </label>
                        <input type="text" name="title" required 
                               placeholder="เช่น 📢 ด่วน! เปิดจองลูกแมวสายพันธุ์นำเข้าล็อตใหม่ พร้อมรับเซ็ตของขวัญฟรี" 
                               style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-size: 0.95rem;">
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="font-weight: 700; font-size: 0.9rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            👥 กลุ่มเป้าหมายที่ต้องการส่งถึง
                        </label>
                        <select name="target_audience" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main);">
                            <option value="all">🌐 สมาชิกทุกคนในระบบ (All Registered Members)</option>
                            <option value="subscribers_only">📩 เฉพาะสมาชิกที่ยินยอมรับข่าวสารทางอีเมล (Subscribers Only)</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="font-weight: 700; font-size: 0.9rem; color: var(--text-main); display: block; margin-bottom: 6px;">
                            📝 ข้อความข่าวสาร & รายละเอียดโปรโมชัน *
                        </label>
                        <textarea name="message" rows="4" required 
                                  placeholder="ระบุข้อความแนะนำน้องแมว ส่วนลด หรือสิทธิพิเศษที่สมาชิกจะได้รับ..." 
                                  style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-family: inherit; font-size: 0.92rem;"></textarea>
                    </div>

                    <!-- Product Selector for Campaign -->
                    <div style="margin-bottom: 18px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label style="font-weight: 700; font-size: 0.9rem; color: var(--text-main);">
                                🐱 เลือกน้องแมว / แพ็กเกจสินค้าที่แนบในข่าวสาร:
                            </label>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">เลือกได้หลายรายการ</span>
                        </div>

                        <div class="item-selection-grid">
                            <!-- Starter Bundles -->
                            <?php foreach ($starter_bundles as $b_id => $bundle): ?>
                                <label class="item-select-card">
                                    <input type="checkbox" name="selected_items[]" value="<?php echo $b_id; ?>">
                                    <img src="assets/images/logo.png" alt="" class="item-select-thumb">
                                    <div style="overflow: hidden;">
                                        <div style="font-weight: 700; font-size: 0.82rem; color: var(--primary-coral); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($bundle['name']); ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            <?php echo number_format($bundle['price']); ?> ฿
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>

                            <!-- Cats List -->
                            <?php foreach ($cats as $c_id => $cat): ?>
                                <label class="item-select-card">
                                    <input type="checkbox" name="selected_items[]" value="<?php echo $c_id; ?>">
                                    <img src="assets/images/<?php echo htmlspecialchars($cat['image']); ?>" alt="" class="item-select-thumb">
                                    <div style="overflow: hidden;">
                                        <div style="font-weight: 700; font-size: 0.82rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            <?php echo htmlspecialchars($cat['breed']); ?> &bull; <?php echo number_format($cat['price']); ?> ฿
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px; font-size: 1rem; font-weight: 700;">
                        🚀 กดยิงข่าวสาร & โปรโมชันถึงสมาชิกทันที
                    </button>
                </form>
            </div>

            <!-- History of Sent Newsletters -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.8rem; box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 1.2rem; font-weight: 800; margin: 0 0 1.2rem 0; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    📜 ประวัติข่าวสารที่ส่งออกไปแล้ว (<?php echo count($all_newsletters); ?>)
                </h3>

                <?php if (empty($all_newsletters)): ?>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">ยังไม่มีประวัติการส่งข่าวสารในระบบ</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 14px; max-height: 750px; overflow-y: auto; padding-right: 4px;">
                        <?php foreach ($all_newsletters as $nl): ?>
                            <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-color); border-radius: 8px; padding: 14px; border-left: 4px solid <?php echo ($nl['type'] ?? '') === 'welcome' ? '#10B981' : 'var(--primary-coral)'; ?>;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 6px;">
                                    <span style="font-weight: 700; font-size: 0.92rem; color: var(--text-main);">
                                        <?php echo htmlspecialchars($nl['title']); ?>
                                    </span>
                                    <span style="font-size: 0.75rem; background: var(--bg-card); padding: 2px 8px; border-radius: 9999px; border: 1px solid var(--border-color); white-space: nowrap;">
                                        <?php echo ($nl['type'] ?? '') === 'welcome' ? '🎉 ต้อนรับสมาชิกใหม่' : '📢 ข่าวสารทั่วไป'; ?>
                                    </span>
                                </div>
                                <p style="font-size: 0.84rem; color: var(--text-secondary); margin: 0 0 8px 0; line-height: 1.5;">
                                    <?php echo htmlspecialchars($nl['message']); ?>
                                </p>
                                
                                <?php if (!empty($nl['items'])): ?>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 8px;">
                                        <?php foreach ($nl['items'] as $itId): 
                                            $itemObj = $cats[$itId] ?? ($starter_bundles[$itId] ?? null);
                                            if ($itemObj):
                                        ?>
                                            <span style="font-size: 0.72rem; background: var(--bg-card); border: 1px solid var(--border-color); padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                                🐱 <?php echo htmlspecialchars($itemObj['name']); ?>
                                            </span>
                                        <?php endif; endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); border-top: 1px dashed var(--border-color); padding-top: 6px;">
                                    <span>ส่งโดย: <strong><?php echo htmlspecialchars($nl['sent_by'] ?? 'ระบบ'); ?></strong></span>
                                    <span>🕒 <?php echo htmlspecialchars($nl['sent_at']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
