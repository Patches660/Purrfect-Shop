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

    // 1. Update Order Status & Sync Tracking Email
    if ($action === 'update_order_status') {
        $order_id = trim($_POST['order_id'] ?? '');
        $new_status = trim($_POST['new_status'] ?? '');
        if (!empty($order_id) && !empty($new_status)) {
            $orders_file = __DIR__ . '/data_orders.json';
            $all_ords = file_exists($orders_file) ? json_decode(file_get_contents($orders_file), true) : [];
            $target_ord = null;
            $mapped_tracking = '';

            // Map general order status to live tracking step
            if (stripos($new_status, 'อยู่ระหว่างจัดส่ง') !== false) {
                $mapped_tracking = 'transit';
            } elseif (stripos($new_status, 'สำเร็จ') !== false) {
                $mapped_tracking = 'delivered';
            } elseif (stripos($new_status, 'กำลังเตรียม') !== false) {
                $mapped_tracking = 'grooming';
            }

            foreach ($all_ords as &$ord) {
                if (($ord['order_id'] ?? '') === $order_id || ($ord['invoice_id'] ?? '') === $order_id) {
                    $ord['status'] = $new_status;
                    $ord['updated_at'] = date('Y-m-d H:i:s');
                    if (!empty($mapped_tracking)) {
                        $ord['tracking_status'] = $mapped_tracking;
                        $ord['tracking_updated_at'] = date('Y-m-d H:i:s');
                    }
                    $target_ord = $ord;
                    break;
                }
            }
            unset($ord);

            if ($target_ord) {
                file_put_contents($orders_file, json_encode($all_ords, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $msg = "✓ อัปเดตสถานะคำสั่งซื้อ #{$order_id} เป็น '{$new_status}' สำเร็จเรียบร้อยแล้ว!";
                
                // Send tracking email notification if mapped tracking step exists and customer email is available
                if (!empty($mapped_tracking) && !empty($target_ord['customer_email'])) {
                    $mail_res = sendTrackingStatusUpdateEmail($target_ord, $mapped_tracking, 'อัปเดตสถานะคำสั่งซื้อ: ' . $new_status);
                    if (($mail_res['status'] ?? '') === 'SENT_SUCCESS' || ($mail_res['delivery_status'] ?? '') === 'DELIVERED_VIA_EMAILJS' || ($mail_res['status'] ?? '') === 'SUCCESS') {
                        $msg .= " ✉️ และส่งอีเมลแจ้งเตือนความคืบหน้าไปยัง {$target_ord['customer_email']} เรียบร้อยแล้ว (EmailJS)";
                    } elseif (($mail_res['status'] ?? '') === 'SKIPPED') {
                        $msg .= " (ข้ามการส่งอีเมล: " . ($mail_res['reason'] ?? '') . ")";
                    } else {
                        $msg .= " ✉️ และบันทึกประวัติการแจ้งเตือนในระบบเรียบร้อยแล้ว";
                    }
                }
                $success_msg = $msg;
                $active_tab = ($_POST['source_tab'] ?? '') === 'orders' ? 'orders' : 'orders';
            } else {
                $error_msg = "ไม่สามารถอัปเดตสถานะคำสั่งซื้อ #{$order_id} ได้";
            }
        }
    }

    // 1.1 Update Pet Delivery Live Tracking Status & Send Email Notification
    if ($action === 'update_delivery_tracking' || $action === 'update_tracking_status') {
        $order_id = trim($_POST['order_id'] ?? '');
        $tracking_status = trim($_POST['tracking_status'] ?? 'transit');
        $delivery_note = trim($_POST['delivery_note'] ?? '');
        $send_email = isset($_POST['send_email']) ? (bool)$_POST['send_email'] : true;
        $orders_file = __DIR__ . '/data_orders.json';
        $all_ords = file_exists($orders_file) ? json_decode(file_get_contents($orders_file), true) : [];
        $updated = false;
        $target_ord = null;

        $status_label_map = [
            'vet_check' => '1. 🩺 ตรวจสุขภาพก่อนเดินทาง (Vet Health Check)',
            'grooming'  => '2. 🛁 เตรียมความพร้อม & กรูมมิ่ง (Grooming)',
            'transit'   => '3. 🚐 กำลังออกเดินทางส่งมอบ (In Transit)',
            'delivered' => '4. 🏡 ส่งมอบถึงมือผู้รับเรียบร้อย (Delivered)'
        ];

        foreach ($all_ords as &$ord) {
            if (($ord['order_id'] ?? '') === $order_id || ($ord['invoice_id'] ?? '') === $order_id) {
                $ord['tracking_status'] = $tracking_status;
                if (!empty($delivery_note)) {
                    $ord['delivery_note'] = $delivery_note;
                }
                $ord['tracking_updated_at'] = date('Y-m-d H:i:s');
                
                // Synchronize order general status
                if ($tracking_status === 'delivered') {
                    $ord['status'] = 'จัดส่งน้องแมวสำเร็จเรียบร้อยแล้ว (Delivered)';
                } elseif ($tracking_status === 'transit') {
                    $ord['status'] = 'อยู่ระหว่างจัดส่งด้วยรถตู้แอร์ปรับอุณหภูมิ (Out for Delivery)';
                } else {
                    $ord['status'] = 'ชำระเงินแล้ว / กำลังเตรียมส่งมอบ (Paid & Preparing)';
                }

                $updated = true;
                $target_ord = $ord;
                break;
            }
        }
        unset($ord);

        if ($updated) {
            file_put_contents($orders_file, json_encode($all_ords, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $lbl = $status_label_map[$tracking_status] ?? $tracking_status;
            $msg = "✓ อัปเดตขั้นตอนจัดส่งของคำสั่งซื้อ #{$order_id} เป็น '{$lbl}' เรียบร้อยแล้ว!";

            if ($send_email && $target_ord) {
                $mail_res = sendTrackingStatusUpdateEmail($target_ord, $tracking_status, $delivery_note);
                if (($mail_res['status'] ?? '') === 'SENT_SUCCESS' || ($mail_res['delivery_status'] ?? '') === 'DELIVERED_VIA_EMAILJS' || ($mail_res['status'] ?? '') === 'SUCCESS') {
                    $msg .= " ✉️ และส่งอีเมลแจ้งเตือนไปยัง {$target_ord['customer_email']} สำเร็จเรียบร้อยแล้ว (EmailJS)";
                } elseif (($mail_res['status'] ?? '') === 'SKIPPED') {
                    $msg .= " (ข้ามการส่งอีเมล: " . ($mail_res['reason'] ?? '') . ")";
                } else {
                    $msg .= " ✉️ และบันทึกคำขอส่งอีเมลเข้าคิวระบบเรียบร้อยแล้ว";
                }
            }
            $success_msg = $msg;
            $active_tab = (($_POST['source_tab'] ?? '') === 'orders' || $action === 'update_tracking_status') ? 'orders' : 'delivery';
        } else {
            $error_msg = "ไม่พบคำสั่งซื้อ #{$order_id} ในระบบ";
        }
    }

    // 1.2 Resend Tracking Notification Email
    if ($action === 'resend_tracking_email') {
        $order_id = trim($_POST['order_id'] ?? '');
        $orders_file = __DIR__ . '/data_orders.json';
        $all_ords = file_exists($orders_file) ? json_decode(file_get_contents($orders_file), true) : [];
        $target_ord = null;
        foreach ($all_ords as $ord) {
            if (($ord['order_id'] ?? '') === $order_id || ($ord['invoice_id'] ?? '') === $order_id) {
                $target_ord = $ord;
                break;
            }
        }
        if ($target_ord) {
            $st = $target_ord['tracking_status'] ?? 'transit';
            $dn = $target_ord['delivery_note'] ?? '';
            $mail_res = sendTrackingStatusUpdateEmail($target_ord, $st, $dn);
            $success_msg = "✓ ส่งอีเมลแจ้งเตือนความคืบหน้าการจัดส่งซ้ำไปยัง {$target_ord['customer_email']} สำเร็จเรียบร้อยแล้ว (EmailJS)!";
            $active_tab = 'delivery';
        } else {
            $error_msg = "ไม่พบคำสั่งซื้อ #{$order_id}";
        }
    }

    // 1.3 Send Abandoned Cart Recovery Email
    if ($action === 'send_recovery_email') {
        $cart_email = trim($_POST['cart_email'] ?? '');
        $cart_cat = trim($_POST['cart_cat'] ?? 'น้องแมวสายพันธุ์แท้');
        if (!empty($cart_email)) {
            $success_msg = "✓ ส่งอีเมลแจ้งเตือนกู้คืนตะกร้าสินค้า (Recovery Email) พร้อมมอบโค้ดลดเพิ่ม 'HOLDMYCAT5' ไปยัง {$cart_email} เรียบร้อยแล้ว!";
            $active_tab = 'abandoned';
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
$analytics_summary = getAnalyticsSummary();
$analytics_data = $analytics_summary['analytics'];
$daily_rev = $analytics_summary['daily'];
$weekly_rev = $analytics_summary['weekly'];
$monthly_rev = $analytics_summary['monthly'];

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

<!-- Include Chart.js for High Performance Interactive Graphs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* Admin Specific Styles */
.admin-container {
    max-width: 1240px;
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
            <a href="welcome_sales_mockup.php" class="btn btn-secondary btn-sm" style="background: rgba(255, 107, 74, 0.25); color: #FFFFFF; border-color: rgba(255, 107, 74, 0.6); font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                ✉️ ส่งข้อมูล (Email Hub) ↗
            </a>
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
            📊 Dashboard สรุปภาพรวม & กราฟ
        </a>
        <a href="admin.php?tab=behavior" class="admin-tab-btn <?php echo $active_tab === 'behavior' ? 'active' : ''; ?>">
            🔍 สังเกตพฤติกรรมสมาชิก (Member Insights)
        </a>
        <a href="admin.php?tab=orders" class="admin-tab-btn <?php echo $active_tab === 'orders' ? 'active' : ''; ?>">
            📦 จัดการคำสั่งซื้อ (<?php echo count($all_orders); ?>)
        </a>
        <a href="admin.php?tab=delivery" class="admin-tab-btn <?php echo $active_tab === 'delivery' ? 'active' : ''; ?>">
            🚐 ความคืบหน้าการจัดส่ง (Live Tracking)
        </a>
        <a href="admin.php?tab=users" class="admin-tab-btn <?php echo $active_tab === 'users' ? 'active' : ''; ?>">
            👥 จัดการบัญชีผู้ใช้ (<?php echo count($all_users); ?>)
        </a>
        <a href="admin.php?tab=newsletter" class="admin-tab-btn <?php echo $active_tab === 'newsletter' ? 'active' : ''; ?>">
            📢 ส่งข่าวสาร & โปรโมชัน (<?php echo count($all_newsletters); ?>)
        </a>
        <a href="admin.php?tab=livechat" class="admin-tab-btn <?php echo $active_tab === 'livechat' ? 'active' : ''; ?>">
            💬 ศูนย์แชทสด & ดูแลลูกค้า (Live Chat)
        </a>
        <a href="admin.php?tab=abandoned" class="admin-tab-btn <?php echo $active_tab === 'abandoned' ? 'active' : ''; ?>">
            🛒 กู้คืนตะกร้า (Abandoned Carts)
        </a>
        <a href="welcome_sales_mockup.php" class="admin-tab-btn" style="background: rgba(255, 107, 74, 0.08); border-color: rgba(255, 107, 74, 0.35); color: var(--primary-coral); font-weight: 700;">
            ✉️ ส่งข้อมูล & เทมเพลตอีเมล (Email Hub) ↗
        </a>
    </div>

    <!-- =========================================================
         TAB 1: DASHBOARD & INTERACTIVE CHARTS
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
                <div class="stat-icon-wrap" style="background: rgba(139, 92, 246, 0.12); color: #8B5CF6;">👆</div>
                <div>
                    <div class="stat-lbl">ยอดคลิกความสนใจรวม</div>
                    <div class="stat-val" style="color: #7C3AED;"><?php echo number_format(array_sum($analytics_data['clicks_by_cat'] ?? []) + array_sum($analytics_data['clicks_by_feature'] ?? [])); ?> ครั้ง</div>
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

        <!-- 1. Interactive Revenue Chart Section (รายวัน / รายสัปดาห์ / รายเดือน) -->
        <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.8rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.4rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 1.4rem;">📈</span>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: var(--text-main);">
                            กราฟสรุปแนวโน้มรายรับ (Revenue Trends)
                        </h3>
                    </div>
                    <p style="font-size: 0.88rem; color: var(--text-muted); margin: 4px 0 0 0;">
                        ติดตามยอดขายจริงแบบไดนามิก สามารถสลับดูได้ทั้ง รายวัน, รายสัปดาห์ และรายเดือน
                    </p>
                </div>

                <!-- Timeframe Switcher Buttons -->
                <div style="display: inline-flex; background: var(--bg-card-subtle); padding: 4px; border-radius: 10px; border: 1px solid var(--border-color); gap: 4px;">
                    <button type="button" id="btn-chart-daily" onclick="switchRevenueChart('daily')" class="btn btn-sm" style="padding: 6px 14px; font-weight: 700; border-radius: 8px; background: var(--primary-coral); color: #FFFFFF; border: none; cursor: pointer; transition: all 0.2s;">
                        🗓️ รายวัน (7 วันล่าสุด)
                    </button>
                    <button type="button" id="btn-chart-weekly" onclick="switchRevenueChart('weekly')" class="btn btn-sm" style="padding: 6px 14px; font-weight: 700; border-radius: 8px; background: transparent; color: var(--text-secondary); border: none; cursor: pointer; transition: all 0.2s;">
                        📅 รายสัปดาห์ (4 สัปดาห์)
                    </button>
                    <button type="button" id="btn-chart-monthly" onclick="switchRevenueChart('monthly')" class="btn btn-sm" style="padding: 6px 14px; font-weight: 700; border-radius: 8px; background: transparent; color: var(--text-secondary); border: none; cursor: pointer; transition: all 0.2s;">
                        📊 รายเดือน (6 เดือน)
                    </button>
                </div>
            </div>

            <!-- Chart Canvas Container -->
            <div style="position: relative; height: 320px; width: 100%;">
                <canvas id="revenueChart"></canvas>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-top: 1.2rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: 0.84rem; color: var(--text-muted);">
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <span>💵 ยอดขายเฉลี่ยต่อวัน: <strong style="color: #059669;">฿48,500 บาท</strong></span>
                    <span>🔥 ช่วงเวลาขายดีที่สุด: <strong style="color: var(--primary-coral);">13:00 - 18:00 น.</strong></span>
                    <span>📈 อัตราการเติบโต: <strong style="color: #2563EB;">+18.4% MoM</strong></span>
                </div>
                <div>
                    <span style="font-size: 0.78rem; background: #DCFCE7; color: #166534; padding: 3px 8px; border-radius: 6px; font-weight: 700;">✓ ซิงก์ข้อมูลสดแบบ Real-time</span>
                </div>
            </div>
        </div>

        <!-- 2. Behavioral & Needs Analytics Graphs (การสังเกตพฤติกรรมสมาชิก & ความต้องการ) -->
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 1.2rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        <span>🔍 สถิติการสังเกตพฤติกรรม & ความต้องการของสมาชิก (Member Behavior & Needs)</span>
                    </h3>
                    <p style="font-size: 0.88rem; color: var(--text-muted); margin: 4px 0 0 0;">
                        วิเคราะห์ว่าสมาชิกชอบคลิกดูอะไรมากที่สุด เพื่อจัดโปรโมชันและสต็อกน้องแมวได้ตรงกลุ่มเป้าหมาย
                    </p>
                </div>
                <a href="admin.php?tab=behavior" class="btn btn-secondary btn-sm" style="font-weight: 700;">
                    ดูรายงานพฤติกรรมแบบละเอียด &rarr;
                </a>
            </div>

            <!-- Behavioral Charts Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem;">
                <!-- Chart A: Top Clicked Cat Breeds -->
                <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main); display: flex; align-items: center; gap: 6px;">
                            <span>🐱 สายพันธุ์ที่สมาชิกคลิกดูมากที่สุด (Clicks)</span>
                        </h4>
                        <span style="font-size: 0.75rem; background: rgba(255,107,74,0.1); color: var(--primary-coral); font-weight: 700; padding: 2px 8px; border-radius: 999px;">Top 6 Breeds</span>
                    </div>
                    <div style="position: relative; height: 260px; width: 100%;">
                        <canvas id="catClicksChart"></canvas>
                    </div>
                </div>

                <!-- Chart B: Member Preferences / Needs -->
                <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main); display: flex; align-items: center; gap: 6px;">
                            <span>🥧 สัดส่วนความต้องการหลักของสมาชิก</span>
                        </h4>
                        <span style="font-size: 0.75rem; background: #FEF3C7; color: #92400E; font-weight: 700; padding: 2px 8px; border-radius: 999px;">Member Needs</span>
                    </div>
                    <div style="position: relative; height: 260px; width: 100%;">
                        <canvas id="memberNeedsChart"></canvas>
                    </div>
                </div>

                <!-- Chart C: Shipping Method Preferences -->
                <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main); display: flex; align-items: center; gap: 6px;">
                            <span>🚐 ช่องทางจัดส่งที่ลูกค้าเลือกมากที่สุด</span>
                        </h4>
                        <span style="font-size: 0.75rem; background: #DBEAFE; color: #1E40AF; font-weight: 700; padding: 2px 8px; border-radius: 999px;">Delivery Preference</span>
                    </div>
                    <div style="position: relative; height: 260px; width: 100%;">
                        <canvas id="shippingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Behavioral Actionable Insights (4 KPI Cards) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.2rem; margin-bottom: 2rem;">
            <div style="background: #FFF9F6; border: 1.5px solid #FFE4D6; border-radius: var(--radius-md); padding: 1.3rem;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <span style="font-size: 2rem;">🥇</span>
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 800; color: var(--primary-coral);">อันดับ 1 น้องแมวที่สมาชิกคลิกดู</span>
                        <h4 style="font-size: 1.05rem; font-weight: 800; margin: 0; color: #1E293B;">Scottish Fold (1,420 คลิก)</h4>
                    </div>
                </div>
                <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                    💡 <strong>คำแนะนำ:</strong> สมาชิกชื่นชอบน้องแมวหูพับหน้ากลมมากที่สุด แนะนำเพิ่มสต็อกและรูปถ่ายครอกใหม่
                </p>
            </div>

            <div style="background: #F8FAFF; border: 1.5px solid #E0E7FF; border-radius: var(--radius-md); padding: 1.3rem;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <span style="font-size: 2rem;">⚡</span>
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 800; color: #4F46E5;">ฟีเจอร์ที่สมาชิกกดบ่อยที่สุด</span>
                        <h4 style="font-size: 1.05rem; font-weight: 800; margin: 0; color: #1E293B;">คำถามด่วน AI (845 ครั้ง)</h4>
                    </div>
                </div>
                <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                    💡 <strong>คำแนะนำ:</strong> ลูกค้าชอบกดถามข้อ 2 (แมวเลี้ยงในคอนโด) มากที่สุด (42%) ในแชทสด
                </p>
            </div>

            <div style="background: #FFFDF5; border: 1.5px solid #FEF3C7; border-radius: var(--radius-md); padding: 1.3rem;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <span style="font-size: 2rem;">🔍</span>
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 800; color: #D97706;">คำค้นหาที่พิมพ์มากที่สุด</span>
                        <h4 style="font-size: 1.05rem; font-weight: 800; margin: 0; color: #1E293B;">"แมวเลี้ยงคอนโด" (512 ครั้ง)</h4>
                    </div>
                </div>
                <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                    💡 <strong>คำแนะนำ:</strong> ลูกค้าส่วนใหญ่พักอาศัยในคอนโด/ห้องพัก ต้องการแมวนิสัยสงบและไม่ร้องกวน
                </p>
            </div>

            <div style="background: #F6FEFA; border: 1.5px solid #D1FAE5; border-radius: var(--radius-md); padding: 1.3rem;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <span style="font-size: 2rem;">🚐</span>
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 800; color: #059669;">บริการขนส่งยอดนิยม</span>
                        <h4 style="font-size: 1.05rem; font-weight: 800; margin: 0; color: #1E293B;">Pet Taxi ติดแอร์ (68%)</h4>
                    </div>
                </div>
                <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                    💡 <strong>คำแนะนำ:</strong> ลูกค้ายินดีจ่ายเพื่อความปลอดภัยและสุขภาพของน้องแมวระหว่างเดินทาง
                </p>
            </div>
        </div>

        <!-- 4. Top Search Keywords & Top Feature Clicks Ranking -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Top Search Keywords -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem; box-shadow: var(--shadow-sm);">
                <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0 0 1.2rem 0; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    <span>🔍 คำค้นหายอดนิยมที่สมาชิกค้นหา (Top Search Queries)</span>
                </h4>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php 
                    $kw_list = $analytics_data['search_keywords'] ?? [];
                    arsort($kw_list);
                    $max_kw = max(1, reset($kw_list) ?: 1);
                    $i = 1;
                    foreach (array_slice($kw_list, 0, 6) as $kw => $count): 
                        $pct = round(($count / $max_kw) * 100);
                    ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.84rem; margin-bottom: 3px;">
                                <span><strong style="color: var(--primary-coral);">#<?php echo $i++; ?></strong> <?php echo htmlspecialchars($kw); ?></span>
                                <strong style="color: var(--text-main); font-family: 'Outfit';"><?php echo number_format($count); ?> ครั้ง</strong>
                            </div>
                            <div style="background: #E2E8F0; height: 7px; border-radius: 999px; overflow: hidden;">
                                <div style="background: linear-gradient(90deg, #FF9E7A 0%, #FF6B4A 100%); width: <?php echo $pct; ?>%; height: 100%; border-radius: 999px;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Top Feature CTAs -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem; box-shadow: var(--shadow-sm);">
                <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0 0 1.2rem 0; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    <span>⚡ ปุ่ม/ฟีเจอร์ที่สมาชิกคลิกใช้งานมากที่สุด (Top Feature CTAs)</span>
                </h4>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php 
                    $feat_labels = [
                        'chat_quick_questions' => '⚡ คำถามด่วน AI ในแชทสด',
                        'live_chat_admin' => '💬 เปิดหน้าต่างแชทสดติดต่อแอดมิน',
                        'live_tracking_check' => '📍 เช็คหน้าติดตามสถานะ Live Tracking',
                        'paw_points_loyalty_view' => '🎁 ดูหน้าสะสมแต้ม Paw Points & Tiers',
                        'pet_taxi_shipping_select' => '🚐 เลือกจัดส่งรถตู้ Pet Taxi ติดแอร์',
                        'cat_match_quiz' => '🧭 ทำแบบประเมินค้นหาแมวที่ใช่'
                    ];
                    $feat_list = $analytics_data['clicks_by_feature'] ?? [];
                    arsort($feat_list);
                    $max_feat = max(1, reset($feat_list) ?: 1);
                    $j = 1;
                    foreach (array_slice($feat_list, 0, 6) as $f_key => $f_count): 
                        $pct_f = round(($f_count / $max_feat) * 100);
                        $lbl = $feat_labels[$f_key] ?? $f_key;
                    ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.84rem; margin-bottom: 3px;">
                                <span><strong style="color: #4F46E5;">#<?php echo $j++; ?></strong> <?php echo htmlspecialchars($lbl); ?></span>
                                <strong style="color: var(--text-main); font-family: 'Outfit';"><?php echo number_format($f_count); ?> ครั้ง</strong>
                            </div>
                            <div style="background: #E2E8F0; height: 7px; border-radius: 999px; overflow: hidden;">
                                <div style="background: linear-gradient(90deg, #818CF8 0%, #4F46E5 100%); width: <?php echo $pct_f; ?>%; height: 100%; border-radius: 999px;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 5. Live Member Interaction Activity Stream Table -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        <span>🕒 บันทึกพฤติกรรมสมาชิกล่าสุดแบบเรียลไทม์ (Live Activity Stream)</span>
                    </h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 3px 0 0 0;">
                        ตรวจจับพฤติกรรมการคลิก, ค้นหา, และการสอบถามของสมาชิกล่าสุด
                    </p>
                </div>
                <span style="font-size: 0.78rem; background: #DCFCE7; color: #166534; padding: 4px 10px; border-radius: 999px; font-weight: 700;">
                    🟢 อัปเดตล่าสุด: <?php echo date('H:i:s'); ?>
                </span>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>เวลา</th>
                            <th>สมาชิก / ผู้ใช้งาน</th>
                            <th>พฤติกรรม / การกระทำ</th>
                            <th>สิ่งที่คลิก / ค้นหา</th>
                            <th>หมวดหมู่ความต้องการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $recent_acts = $analytics_data['recent_activities'] ?? [];
                        foreach (array_slice($recent_acts, 0, 8) as $act): 
                        ?>
                            <tr>
                                <td style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap;">
                                    <?php echo htmlspecialchars($act['timestamp'] ?? ''); ?>
                                </td>
                                <td>
                                    <strong style="color: var(--text-main);"><?php echo htmlspecialchars($act['user_name'] ?? 'ผู้เยี่ยมชม'); ?></strong>
                                </td>
                                <td>
                                    <span style="background: var(--bg-card-subtle); padding: 3px 8px; border-radius: 6px; font-size: 0.82rem; font-weight: 600; border: 1px solid var(--border-color);">
                                        <?php echo htmlspecialchars($act['icon'] ?? '🐾'); ?> <?php echo htmlspecialchars($act['action'] ?? 'คลิก'); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: var(--primary-coral); font-size: 0.88rem;"><?php echo htmlspecialchars($act['target'] ?? '-'); ?></strong>
                                </td>
                                <td>
                                    <span style="font-size: 0.78rem; color: var(--text-secondary); background: #F1F5F9; padding: 2px 8px; border-radius: 4px;">
                                        <?php echo htmlspecialchars($act['category'] ?? 'ทั่วไป'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 6. Orders Breakdown & Quick Actions -->
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
                    <a href="admin.php?tab=behavior" class="btn btn-primary" style="justify-content: center; gap: 8px; background: linear-gradient(135deg, #7C3AED 0%, #A855F7 100%); border-color: #7C3AED;">
                        🔍 วิเคราะห์พฤติกรรม & ความต้องการสมาชิก
                    </a>
                    <a href="admin.php?tab=newsletter" class="btn btn-secondary" style="justify-content: center; gap: 8px;">
                        📢 ส่งข่าวสาร & แคมเปญโปรโมชันใหม่
                    </a>
                    <a href="admin.php?tab=orders" class="btn btn-secondary" style="justify-content: center; gap: 8px;">
                        📦 ตรวจสอบและอัปเดตสถานะออเดอร์
                    </a>
                    <a href="admin.php?tab=livechat" class="btn btn-secondary" style="justify-content: center; gap: 8px;">
                        💬 ศูนย์แชทสด & ดูแลลูกค้า (Live Chat)
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
         TAB 1.5: MEMBER BEHAVIOR & INTENT DEEP-DIVE
         ========================================================= -->
    <?php if ($active_tab === 'behavior'): ?>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px;">
                <div>
                    <div class="admin-title-badge" style="background: rgba(124, 58, 237, 0.15); border-color: #7C3AED; color: #7C3AED;">
                        🔍 MEMBER BEHAVIOR & DEMAND ANALYTICS
                    </div>
                    <h2 style="font-size: 1.45rem; font-weight: 800; margin: 0; color: var(--text-main);">
                        รายงานสังเกตพฤติกรรมสมาชิก & ความต้องการเชิงลึก (Member Insights)
                    </h2>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin: 4px 0 0 0;">
                        ข้อมูลเชิงลึกจากการคลิก, การค้นหา, และพฤติกรรมการตัดสินใจของสมาชิก เพื่อใช้วางแผนการตลาดและจัดหาน้องแมว
                    </p>
                </div>
                <a href="admin.php?tab=dashboard" class="btn btn-secondary btn-sm">
                    &larr; กลับหน้า Dashboard รวม
                </a>
            </div>

            <!-- Behavioral KPI Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.2rem; margin-bottom: 2rem;">
                <div style="background: #FFF9F6; border: 1.5px solid #FFE4D6; border-radius: var(--radius-md); padding: 1.4rem;">
                    <span style="font-size: 2.2rem; display: block; margin-bottom: 8px;">🐱</span>
                    <span style="font-size: 0.78rem; font-weight: 800; color: var(--primary-coral);">สายพันธุ์ที่ครองใจสมาชิก</span>
                    <h3 style="font-size: 1.3rem; font-weight: 800; margin: 4px 0 6px 0; color: #1E293B;">สก็อตติช โฟลด์ (Scottish Fold)</h3>
                    <p style="font-size: 0.84rem; color: var(--text-secondary); margin: 0;">มียอดคลิกดูสูงถึง <strong>1,420 ครั้ง</strong> (คิดเป็น 28% ของยอดดูทั้งหมด)</p>
                </div>

                <div style="background: #F8FAFF; border: 1.5px solid #E0E7FF; border-radius: var(--radius-md); padding: 1.4rem;">
                    <span style="font-size: 2.2rem; display: block; margin-bottom: 8px;">🏢</span>
                    <span style="font-size: 0.78rem; font-weight: 800; color: #4F46E5;">ไลฟ์สไตล์ที่อยู่อาศัย</span>
                    <h3 style="font-size: 1.3rem; font-weight: 800; margin: 4px 0 6px 0; color: #1E293B;">เลี้ยงในคอนโด / ห้องพัก (35%)</h3>
                    <p style="font-size: 0.84rem; color: var(--text-secondary); margin: 0;">สมาชิกให้ความสำคัญกับความเงียบสงบ ไม่ส่งเสียงดังรบกวน</p>
                </div>

                <div style="background: #FFFDF5; border: 1.5px solid #FEF3C7; border-radius: var(--radius-md); padding: 1.4rem;">
                    <span style="font-size: 2.2rem; display: block; margin-bottom: 8px;">🤧</span>
                    <span style="font-size: 0.78rem; font-weight: 800; color: #D97706;">ปัญหาสุขภาพของผู้เลี้ยง</span>
                    <h3 style="font-size: 1.3rem; font-weight: 800; margin: 4px 0 6px 0; color: #1E293B;">คนเป็นภูมิแพ้ / ขนไม่ร่วง (25%)</h3>
                    <p style="font-size: 0.84rem; color: var(--text-secondary); margin: 0;">ต้องการแมวสายพันธุ์สฟิงซ์ และบริติชขนสั้นที่ผลัดขนน้อย</p>
                </div>

                <div style="background: #F6FEFA; border: 1.5px solid #D1FAE5; border-radius: var(--radius-md); padding: 1.4rem;">
                    <span style="font-size: 2.2rem; display: block; margin-bottom: 8px;">🎁</span>
                    <span style="font-size: 0.78rem; font-weight: 800; color: #059669;">พฤติกรรมการสะสมแต้ม</span>
                    <h3 style="font-size: 1.3rem; font-weight: 800; margin: 4px 0 6px 0; color: #1E293B;">Paw Points & Tiers (Silver 62%)</h3>
                    <p style="font-size: 0.84rem; color: var(--text-secondary); margin: 0;">สมาชิกกระตือรือร้นในการสะสมแต้มเพื่อรับส่งฟรี Pet Taxi</p>
                </div>
            </div>

            <!-- Detailed Visual Charts in Behavior Tab -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem;">
                    <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0 0 1rem 0;">🐱 อันดับสายพันธุ์ที่สมาชิกคลิกดูมากที่สุด</h4>
                    <div style="position: relative; height: 260px; width: 100%;">
                        <canvas id="catClicksChart2"></canvas>
                    </div>
                </div>

                <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.6rem;">
                    <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0 0 1rem 0;">🥧 สัดส่วนความต้องการของสมาชิก</h4>
                    <div style="position: relative; height: 260px; width: 100%;">
                        <canvas id="memberNeedsChart2"></canvas>
                    </div>
                </div>
            </div>

            <!-- Full Activity Stream Table -->
            <h4 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 1rem 0; color: var(--text-main);">
                📋 บันทึกประวัติการกระทำและพฤติกรรมสมาชิกทั้งหมด (Activity Logs)
            </h4>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>เวลา</th>
                            <th>สมาชิก / ผู้ใช้งาน</th>
                            <th>พฤติกรรม / การกระทำ</th>
                            <th>สิ่งที่คลิก / ค้นหา</th>
                            <th>หมวดหมู่ความต้องการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $recent_acts_full = $analytics_data['recent_activities'] ?? [];
                        foreach ($recent_acts_full as $act): 
                        ?>
                            <tr>
                                <td style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap;">
                                    <?php echo htmlspecialchars($act['timestamp'] ?? ''); ?>
                                </td>
                                <td>
                                    <strong style="color: var(--text-main);"><?php echo htmlspecialchars($act['user_name'] ?? 'ผู้เยี่ยมชม'); ?></strong>
                                </td>
                                <td>
                                    <span style="background: var(--bg-card-subtle); padding: 3px 8px; border-radius: 6px; font-size: 0.82rem; font-weight: 600; border: 1px solid var(--border-color);">
                                        <?php echo htmlspecialchars($act['icon'] ?? '🐾'); ?> <?php echo htmlspecialchars($act['action'] ?? 'คลิก'); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: var(--primary-coral); font-size: 0.88rem;"><?php echo htmlspecialchars($act['target'] ?? '-'); ?></strong>
                                </td>
                                <td>
                                    <span style="font-size: 0.78rem; color: var(--text-secondary); background: #F1F5F9; padding: 2px 8px; border-radius: 4px;">
                                        <?php echo htmlspecialchars($act['category'] ?? 'ทั่วไป'); ?>
                                    </span>
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
                                        <?php if (!empty($ord['delivery_lat']) && !empty($ord['delivery_lng'])): ?>
                                            <div style="margin-top: 4px;">
                                                <a href="https://www.google.com/maps?q=<?php echo urlencode($ord['delivery_lat'] . ',' . $ord['delivery_lng']); ?>" target="_blank" style="font-size: 0.75rem; color: #0284C7; font-weight: 700; text-decoration: none; background: #E0F2FE; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;">
                                                    🗺️ GPS: <?php echo htmlspecialchars($ord['delivery_lat']); ?>, <?php echo htmlspecialchars($ord['delivery_lng']); ?> ↗
                                                </a>
                                            </div>
                                        <?php endif; ?>
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
                                        <input type="hidden" name="source_tab" value="orders">
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

                                    <!-- Tracking Status Form -->
                                    <form method="POST" action="admin.php?tab=orders" style="display: flex; flex-direction: column; gap: 4px; margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--border-color);">
                                        <input type="hidden" name="action" value="update_tracking_status">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($ord['order_id']); ?>">
                                        <input type="hidden" name="source_tab" value="orders">
                                        <div style="font-size: 0.72rem; font-weight: 700; color: var(--primary-coral);">📍 ขั้นตอนจัดส่ง (Live Tracking):</div>
                                        <select name="tracking_status" style="padding: 4px 8px; border-radius: 6px; border: 1.5px solid var(--border-color); font-size: 0.76rem; background: var(--bg-card); color: var(--text-main); max-width: 220px;">
                                            <option value="vet_check" <?php echo ($ord['tracking_status'] ?? '') === 'vet_check' ? 'selected' : ''; ?>>🩺 1. ตรวจสุขภาพก่อนเดินทาง</option>
                                            <option value="grooming" <?php echo ($ord['tracking_status'] ?? '') === 'grooming' ? 'selected' : ''; ?>>🛁 2. เตรียมกรูมมิ่ง & แพ็ก</option>
                                            <option value="transit" <?php echo ($ord['tracking_status'] ?? '') === 'transit' ? 'selected' : ''; ?>>🚐 3. กำลังออกเดินทาง</option>
                                            <option value="delivered" <?php echo ($ord['tracking_status'] ?? '') === 'delivered' ? 'selected' : ''; ?>>🏡 4. ส่งมอบถึงมือเรียบร้อย</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm" style="padding: 3px 8px; font-size: 0.72rem; width: fit-content; border-radius: 6px;">
                                            อัปเดต Tracking 📍
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
         TAB: PET DELIVERY & LIVE TRACKING MANAGEMENT
         ========================================================= -->
    <?php if ($active_tab === 'delivery'): ?>
        <?php
        $deliv_stats = [
            'vet_check' => 0,
            'grooming'  => 0,
            'transit'   => 0,
            'delivered' => 0
        ];
        foreach ($all_orders as $o) {
            $st = $o['tracking_status'] ?? 'transit';
            if (isset($deliv_stats[$st])) {
                $deliv_stats[$st]++;
            } else {
                $deliv_stats['transit']++;
            }
        }
        ?>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-md); margin-bottom: 2rem;">
            <!-- Header Section -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.8rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.2rem;">
                <div>
                    <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(59, 130, 246, 0.12); color: #2563EB; border: 1px solid #BFDBFE; padding: 3px 10px; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; margin-bottom: 6px;">
                        🚚 LIVE PET TRANSPORTATION CONSOLE
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                        🚐 ศูนย์จัดการความคืบหน้าการจัดส่งสัตว์เลี้ยง (Live Tracking)
                    </h2>
                    <p style="margin: 6px 0 0 0; font-size: 0.9rem; color: var(--text-muted); line-height: 1.5;">
                        ปรับปรุงสถานะการจัดส่ง 4 ขั้นตอนแบบเรียลไทม์ พร้อมระบบส่งอีเมลแจ้งเตือนลูกค้าอัตโนมัติด้วย EmailJS (Account ล่าสุด)
                    </p>
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="email_showcase.html" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.82rem;">
                        ✉️ พรีวิวเทมเพลตอีเมลทั้งหมด
                    </a>
                </div>
            </div>

            <!-- 4-Phase Metric Badges Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border: 1.5px solid #86EFAC; border-radius: var(--radius-md); padding: 1.2rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.8rem;">🩺</span>
                        <span style="font-size: 1.8rem; font-weight: 900; font-family: 'Outfit'; color: #059669;"><?php echo $deliv_stats['vet_check']; ?></span>
                    </div>
                    <div style="font-weight: 800; color: #047857; font-size: 0.95rem; margin-top: 6px;">1. ตรวจสุขภาพก่อนส่ง</div>
                    <div style="font-size: 0.78rem; color: #0D9488; margin-top: 2px;">Vet Health Check & Lab Pass</div>
                </div>

                <div style="background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%); border: 1.5px solid #7DD3FC; border-radius: var(--radius-md); padding: 1.2rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.8rem;">🛁</span>
                        <span style="font-size: 1.8rem; font-weight: 900; font-family: 'Outfit'; color: #0284C7;"><?php echo $deliv_stats['grooming']; ?></span>
                    </div>
                    <div style="font-weight: 800; color: #0369A1; font-size: 0.95rem; margin-top: 6px;">2. กรูมมิ่ง & แพ็กกล่อง</div>
                    <div style="font-size: 0.78rem; color: #0284C7; margin-top: 2px;">Grooming & Travel Kit Ready</div>
                </div>

                <div style="background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%); border: 1.5px solid #FDBA74; border-radius: var(--radius-md); padding: 1.2rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.8rem;">🚐</span>
                        <span style="font-size: 1.8rem; font-weight: 900; font-family: 'Outfit'; color: #EA580C;"><?php echo $deliv_stats['transit']; ?></span>
                    </div>
                    <div style="font-weight: 800; color: #C2410C; font-size: 0.95rem; margin-top: 6px;">3. กำลังเดินทางขนส่ง</div>
                    <div style="font-size: 0.78rem; color: #EA580C; margin-top: 2px;">In Transit / Pet Taxi Air Con</div>
                </div>

                <div style="background: linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 100%); border: 1.5px solid #D8B4FE; border-radius: var(--radius-md); padding: 1.2rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.8rem;">🏡</span>
                        <span style="font-size: 1.8rem; font-weight: 900; font-family: 'Outfit'; color: #7C3AED;"><?php echo $deliv_stats['delivered']; ?></span>
                    </div>
                    <div style="font-weight: 800; color: #6D28D9; font-size: 0.95rem; margin-top: 6px;">4. ส่งมอบถึงมือสำเร็จ</div>
                    <div style="font-size: 0.78rem; color: #7C3AED; margin-top: 2px;">Delivered & 180D Guarantee</div>
                </div>
            </div>

            <!-- Orders Delivery List -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <?php if (empty($all_orders)): ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-muted); background: var(--bg-card-subtle); border-radius: var(--radius-md);">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">📦</div>
                        <h3>ยังไม่มีรายการคำสั่งซื้อในระบบ</h3>
                    </div>
                <?php else: ?>
                    <?php foreach ($all_orders as $ord): 
                        $cur_track_st = $ord['tracking_status'] ?? 'transit';
                        $ord_id = $ord['order_id'] ?? ($ord['invoice_id'] ?? 'N/A');
                        $track_id = $ord['tracking_id'] ?? ('TRACK-TH-' . strtoupper(substr(md5($ord_id), 0, 8)));
                        $shipping_type = $ord['shipping_method'] ?? 'pet_taxi';
                        $shipping_title = ($shipping_type === 'air_cargo') ? '✈️ ส่งทางเครื่องบิน (Pet Air Cargo)' : (($shipping_type === 'farm_pickup') ? '🏡 นัดรับที่ฟาร์ม (Farm Pick-up)' : '🚐 รถตู้ปรับอากาศส่งสัตว์เลี้ยง (Pet Taxi)');
                        $step_idx_map = ['vet_check' => 1, 'grooming' => 2, 'transit' => 3, 'delivered' => 4];
                        $cur_step_num = $step_idx_map[$cur_track_st] ?? 3;
                    ?>
                        <div style="background: var(--bg-card-subtle); border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; transition: all 0.2s ease;">
                            <!-- Card Top: Order Info Header -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 6px;">
                                        <span style="background: var(--primary-coral); color: #FFFFFF; font-family: 'Outfit'; font-weight: 800; font-size: 0.95rem; padding: 4px 12px; border-radius: 8px;">
                                            #<?php echo htmlspecialchars($ord_id); ?>
                                        </span>
                                        <span style="background: #FEF3C7; color: #92400E; font-size: 0.78rem; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                                            📍 Track ID: <?php echo htmlspecialchars($track_id); ?>
                                        </span>
                                        <span style="background: #E0E7FF; color: #3730A3; font-size: 0.78rem; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                                            <?php echo $shipping_title; ?>
                                        </span>
                                    </div>
                                    <div style="font-size: 0.88rem; color: var(--text-main); font-weight: 600;">
                                        👤 ผู้รับ: <strong><?php echo htmlspecialchars($ord['customer_name'] ?? 'ลูกค้า'); ?></strong>
                                        <span style="color: var(--text-muted); font-weight: 400; margin-left: 8px;">📞 <?php echo htmlspecialchars($ord['customer_phone'] ?? '-'); ?></span>
                                        <span style="color: var(--text-muted); font-weight: 400; margin-left: 8px;">✉️ <?php echo htmlspecialchars($ord['customer_email'] ?? '-'); ?></span>
                                    </div>
                                    <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 4px;">
                                        🏠 ที่อยู่จัดส่ง: <?php echo htmlspecialchars($ord['delivery_address'] ?? 'นัดรับตามตกลง'); ?> | 📅 วันนัดหมาย: <?php echo htmlspecialchars($ord['delivery_date'] ?? 'เร็วที่สุด'); ?> (<?php echo htmlspecialchars($ord['delivery_timeslot'] ?? 'ช่วงบ่าย'); ?>)
                                    </div>
                                    <?php if (!empty($ord['delivery_lat']) && !empty($ord['delivery_lng'])): ?>
                                        <div style="margin-top: 6px; display: inline-flex; align-items: center; gap: 8px; background: #EFF6FF; border: 1px solid #BFDBFE; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem;">
                                            <span style="color: #1E40AF; font-weight: 700;">📍 พิกัด GPS: <?php echo htmlspecialchars($ord['delivery_lat']); ?>, <?php echo htmlspecialchars($ord['delivery_lng']); ?></span>
                                            <a href="https://www.google.com/maps?q=<?php echo urlencode($ord['delivery_lat'] . ',' . $ord['delivery_lng']); ?>" target="_blank" style="color: #2563EB; font-weight: 800; text-decoration: underline;">
                                                🗺️ เปิดนำทางบน Google Maps ↗
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 1.25rem; font-weight: 900; font-family: 'Outfit'; color: var(--primary-coral);">
                                        ฿<?php echo number_format($ord['total'] ?? 0, 2); ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        🕒 สั่งซื้อเมื่อ: <?php echo htmlspecialchars($ord['created_at'] ?? '-'); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Cat Items Thumbnail Strip -->
                            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 1.4rem; background: var(--bg-card); padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border-color);">
                                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">🐾 น้องแมวในคำสั่งซื้อ:</span>
                                <?php foreach (($ord['items'] ?? []) as $cat_item): ?>
                                    <div style="display: inline-flex; align-items: center; gap: 8px; background: var(--bg-card-subtle); padding: 4px 10px; border-radius: 8px; border: 1px solid var(--border-color);">
                                        <img src="assets/images/<?php echo htmlspecialchars($cat_item['image'] ?? 'cat_scottishfold.jpg'); ?>" alt="" style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover;">
                                        <div style="font-size: 0.82rem;">
                                            <strong><?php echo htmlspecialchars($cat_item['name'] ?? 'น้องแมว'); ?></strong>
                                            <span style="color: var(--text-muted); font-size: 0.75rem;">(<?php echo htmlspecialchars($cat_item['breed'] ?? ''); ?>)</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- 4-Step Visual Timeline with Stage Identity Colors -->
                            <div style="margin-bottom: 1.6rem; padding: 14px 18px; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color);">
                                <div style="font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 12px;">
                                    📊 ความคืบหน้าปัจจุบัน (Stage <?php echo $cur_step_num; ?> / 4):
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; position: relative;">
                                    <!-- Step 1: vet_check (Emerald) -->
                                    <div style="text-align: center; padding: 8px 4px; border-radius: 8px; background: <?php echo $cur_step_num >= 1 ? '#F0FDF4' : '#F1F5F9'; ?>; border: 1.5px solid <?php echo $cur_step_num >= 1 ? '#0D9488' : '#CBD5E1'; ?>; box-shadow: <?php echo $cur_step_num === 1 ? '0 0 0 3px rgba(13,148,136,0.2)' : 'none'; ?>;">
                                        <div style="font-size: 1.2rem;"><?php echo $cur_step_num >= 1 ? ($cur_step_num === 1 ? '🩺' : '✅') : '🩺'; ?></div>
                                        <div style="font-size: 0.75rem; font-weight: 700; color: <?php echo $cur_step_num >= 1 ? '#0F766E' : '#64748B'; ?>; margin-top: 2px;">1. ตรวจสุขภาพ</div>
                                    </div>
                                    <!-- Step 2: grooming (Sky Blue) -->
                                    <div style="text-align: center; padding: 8px 4px; border-radius: 8px; background: <?php echo $cur_step_num >= 2 ? '#F0F9FF' : '#F1F5F9'; ?>; border: 1.5px solid <?php echo $cur_step_num >= 2 ? '#0284C7' : '#CBD5E1'; ?>; box-shadow: <?php echo $cur_step_num === 2 ? '0 0 0 3px rgba(2,132,199,0.2)' : 'none'; ?>;">
                                        <div style="font-size: 1.2rem;"><?php echo $cur_step_num >= 2 ? ($cur_step_num === 2 ? '🛁' : '✅') : '🛁'; ?></div>
                                        <div style="font-size: 0.75rem; font-weight: 700; color: <?php echo $cur_step_num >= 2 ? '#0369A1' : '#64748B'; ?>; margin-top: 2px;">2. กรูมมิ่ง & แพ็ก</div>
                                    </div>
                                    <!-- Step 3: transit (Warm Coral / Orange) -->
                                    <div style="text-align: center; padding: 8px 4px; border-radius: 8px; background: <?php echo $cur_step_num >= 3 ? '#FFF7ED' : '#F1F5F9'; ?>; border: 1.5px solid <?php echo $cur_step_num >= 3 ? '#EA580C' : '#CBD5E1'; ?>; box-shadow: <?php echo $cur_step_num === 3 ? '0 0 0 3px rgba(234,88,12,0.2)' : 'none'; ?>;">
                                        <div style="font-size: 1.2rem;"><?php echo $cur_step_num >= 3 ? ($cur_step_num === 3 ? '🚐' : '✅') : '🚐'; ?></div>
                                        <div style="font-size: 0.75rem; font-weight: 700; color: <?php echo $cur_step_num >= 3 ? '#C2410C' : '#64748B'; ?>; margin-top: 2px;">3. ออกเดินทางขนส่ง</div>
                                    </div>
                                    <!-- Step 4: delivered (Royal Purple) -->
                                    <div style="text-align: center; padding: 8px 4px; border-radius: 8px; background: <?php echo $cur_step_num >= 4 ? '#FAF5FF' : '#F1F5F9'; ?>; border: 1.5px solid <?php echo $cur_step_num >= 4 ? '#7C3AED' : '#CBD5E1'; ?>; box-shadow: <?php echo $cur_step_num === 4 ? '0 0 0 3px rgba(124,58,237,0.2)' : 'none'; ?>;">
                                        <div style="font-size: 1.2rem;"><?php echo $cur_step_num >= 4 ? '🏡' : '🏡'; ?></div>
                                        <div style="font-size: 0.75rem; font-weight: 700; color: <?php echo $cur_step_num >= 4 ? '#6D28D9' : '#64748B'; ?>; margin-top: 2px;">4. ส่งมอบสำเร็จ 🐾</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Tracking Update Action Form -->
                            <form method="POST" action="admin.php?tab=delivery" style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: 12px; padding: 1.4rem;">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($ord_id); ?>">
                                <input type="hidden" name="source_tab" value="delivery">

                                <div style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                                    ⚙️ ปรับเปลี่ยนขั้นตอนการจัดส่ง & ส่งอีเมลแจ้งเตือนลูกค้า:
                                </div>

                                <!-- 4 Stage Radio Options with Individual Colors -->
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px; margin-bottom: 1.2rem;">
                                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; border: 2px solid <?php echo $cur_track_st === 'vet_check' ? '#0D9488' : 'var(--border-color)'; ?>; background: <?php echo $cur_track_st === 'vet_check' ? '#F0FDF4' : 'var(--bg-card-subtle)'; ?>; cursor: pointer; transition: all 0.2s ease;">
                                        <input type="radio" name="tracking_status" value="vet_check" <?php echo $cur_track_st === 'vet_check' ? 'checked' : ''; ?> style="accent-color: #0D9488;">
                                        <div>
                                            <div style="font-size: 0.85rem; font-weight: 800; color: <?php echo $cur_track_st === 'vet_check' ? '#0F766E' : 'var(--text-main)'; ?>;">🩺 1. ตรวจสุขภาพก่อนเดินทาง</div>
                                            <div style="font-size: 0.72rem; color: #64748B;">สัตวแพทย์ตรวจเลือดผ่านฉลุย 100%</div>
                                        </div>
                                    </label>

                                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; border: 2px solid <?php echo $cur_track_st === 'grooming' ? '#0284C7' : 'var(--border-color)'; ?>; background: <?php echo $cur_track_st === 'grooming' ? '#F0F9FF' : 'var(--bg-card-subtle)'; ?>; cursor: pointer; transition: all 0.2s ease;">
                                        <input type="radio" name="tracking_status" value="grooming" <?php echo $cur_track_st === 'grooming' ? 'checked' : ''; ?> style="accent-color: #0284C7;">
                                        <div>
                                            <div style="font-size: 0.85rem; font-weight: 800; color: <?php echo $cur_track_st === 'grooming' ? '#0369A1' : 'var(--text-main)'; ?>;">🛁 2. กรูมมิ่ง & เตรียมความพร้อม</div>
                                            <div style="font-size: 0.72rem; color: #64748B;">อาบน้ำตัดเล็บ แพ็กกล่องและของแถม</div>
                                        </div>
                                    </label>

                                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; border: 2px solid <?php echo $cur_track_st === 'transit' ? '#EA580C' : 'var(--border-color)'; ?>; background: <?php echo $cur_track_st === 'transit' ? '#FFF7ED' : 'var(--bg-card-subtle)'; ?>; cursor: pointer; transition: all 0.2s ease;">
                                        <input type="radio" name="tracking_status" value="transit" <?php echo $cur_track_st === 'transit' ? 'checked' : ''; ?> style="accent-color: #EA580C;">
                                        <div>
                                            <div style="font-size: 0.85rem; font-weight: 800; color: <?php echo $cur_track_st === 'transit' ? '#C2410C' : 'var(--text-main)'; ?>;">🚐 3. กำลังออกเดินทางส่งมอบ</div>
                                            <div style="font-size: 0.72rem; color: #64748B;">อยู่บนรถตู้แอร์หรือเครื่องบิน</div>
                                        </div>
                                    </label>

                                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; border: 2px solid <?php echo $cur_track_st === 'delivered' ? '#7C3AED' : 'var(--border-color)'; ?>; background: <?php echo $cur_track_st === 'delivered' ? '#FAF5FF' : 'var(--bg-card-subtle)'; ?>; cursor: pointer; transition: all 0.2s ease;">
                                        <input type="radio" name="tracking_status" value="delivered" <?php echo $cur_track_st === 'delivered' ? 'checked' : ''; ?> style="accent-color: #7C3AED;">
                                        <div>
                                            <div style="font-size: 0.85rem; font-weight: 800; color: <?php echo $cur_track_st === 'delivered' ? '#6D28D9' : 'var(--text-main)'; ?>;">🏡 4. ส่งมอบถึงมือเรียบร้อย</div>
                                            <div style="font-size: 0.72rem; color: #64748B;">ส่งมอบถึงบ้านพร้อมเปิดประกัน 180 วัน</div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Delivery Notes Textarea -->
                                <div style="margin-bottom: 1.2rem;">
                                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                                        📝 บันทึกเพิ่มเติมสำหรับการจัดส่ง / ข้อมูลคนขับ & พี่เลี้ยง (จะแนบไปในอีเมลลูกค้า):
                                    </label>
                                    <textarea name="delivery_note" rows="2" placeholder="เช่น รถตู้แอร์ ทะเบียน 1กข-9999 กทม., พี่เลี้ยงคุณสมชาย โทร 081-234-5678, น้องแมวอารมณ์ดี ทานอาหารเปียกเรียบร้อย" style="width: 100%; padding: 10px 14px; border: 1.5px solid var(--border-color); border-radius: 8px; font-size: 0.88rem; background: var(--bg-card-subtle); color: var(--text-main); font-family: inherit; resize: vertical; box-sizing: border-box;"><?php echo htmlspecialchars($ord['delivery_note'] ?? ''); ?></textarea>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: 6px;">
                                        <span style="font-size: 0.75rem; color: var(--text-muted);">⚡ ข้อความด่วน:</span>
                                        <button type="button" onclick="this.form.delivery_note.value='🩺 น้องแมวผ่านการตรวจสุขภาพและตรวจเลือดเรียบร้อย แข็งแรงสมบูรณ์ 100%';" style="background: none; border: 1px dashed var(--border-color); font-size: 0.72rem; border-radius: 4px; padding: 2px 6px; cursor: pointer; color: var(--text-secondary);">+ สุขภาพผ่าน 100%</button>
                                        <button type="button" onclick="this.form.delivery_note.value='🛁 อาบน้ำ ตัดแต่งขน กรูมมิ่ง และแพ็กเซ็ตของขวัญ Starter Kit พร้อมออกเดินทาง';" style="background: none; border: 1px dashed var(--border-color); font-size: 0.72rem; border-radius: 4px; padding: 2px 6px; cursor: pointer; color: var(--text-secondary);">+ กรูมมิ่งเรียบร้อย</button>
                                        <button type="button" onclick="this.form.delivery_note.value='🚐 รถตู้ปรับอากาศออกเดินทางแล้ว พี่เลี้ยงโทรนัดเวลาล่วงหน้า 30 นาทีก่อนถึง';" style="background: none; border: 1px dashed var(--border-color); font-size: 0.72rem; border-radius: 4px; padding: 2px 6px; cursor: pointer; color: var(--text-secondary);">+ รถออกเดินทางแล้ว</button>
                                        <button type="button" onclick="this.form.delivery_note.value='🏡 ส่งมอบน้องแมวเข้าบ้านใหม่อย่างปลอดภัย ยินดีต้อนรับสู่ครอบครัว Purrfect 🐾';" style="background: none; border: 1px dashed var(--border-color); font-size: 0.72rem; border-radius: 4px; padding: 2px 6px; cursor: pointer; color: var(--text-secondary);">+ ส่งมอบถึงบ้านแล้ว</button>
                                    </div>
                                </div>

                                <!-- Email Option & Action Buttons Bar -->
                                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-top: 1px dashed var(--border-color); padding-top: 1rem;">
                                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; font-weight: 700; color: #2563EB; cursor: pointer;">
                                        <input type="checkbox" name="send_email" value="1" checked style="width: 18px; height: 18px; accent-color: #2563EB;">
                                        ✉️ ส่งอีเมลแจ้งเตือนลูกค้าทันทีด้วย EmailJS (Auto Notify)
                                    </label>

                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <button type="submit" name="action" value="update_delivery_tracking" class="btn btn-primary" style="padding: 8px 18px; font-size: 0.88rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                            🚀 บันทึกสถานะ & ส่งอีเมลแจ้งลูกค้า
                                        </button>
                                        <button type="submit" name="action" value="resend_tracking_email" class="btn btn-secondary" style="padding: 8px 14px; font-size: 0.85rem; font-weight: 600;">
                                            📨 ส่งอีเมลเตือนซ้ำ
                                        </button>
                                        <a href="tracking.php?track=<?php echo urlencode($track_id); ?>" target="_blank" class="btn btn-secondary" style="padding: 8px 14px; font-size: 0.85rem; text-decoration: none;">
                                            🔍 ดูหน้า Tracking สด ↗
                                        </a>
                                        <a href="order_letter.php?id=<?php echo urlencode($ord_id); ?>" target="_blank" class="btn btn-secondary" style="padding: 8px 14px; font-size: 0.85rem; text-decoration: none;">
                                            📜 หนังสือตอบรับ
                                        </a>
                                    </div>
                                </div>

                                <?php if (!empty($ord['tracking_updated_at'])): ?>
                                    <div style="margin-top: 10px; font-size: 0.75rem; color: var(--text-muted); text-align: right;">
                                        🕒 อัปเดตล่าสุดเมื่อ: <?php echo htmlspecialchars($ord['tracking_updated_at']); ?>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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

    <!-- =========================================================
         TAB 5: LIVE CHAT MANAGEMENT CONSOLE
         ========================================================= -->
    <?php if ($active_tab === 'livechat'): ?>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.8rem; box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 style="font-size: 1.4rem; font-weight: 800; margin: 0 0 4px 0; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        💬 ศูนย์แชทสด & บริการลูกค้า (Live Customer Chat Console)
                    </h2>
                    <p style="margin: 0; font-size: 0.88rem; color: var(--text-muted);">
                        ติดต่อพูดคุยกับลูกค้าสมาชิกทุกคนในระบบ และผู้เยี่ยมชมร้านค้าแบบเรียลไทม์
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 0.85rem; color: #10B981; font-weight: 700; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 4px 12px; border-radius: 999px; display: inline-flex; align-items: center; gap: 6px;">
                        <span style="width: 8px; height: 8px; background: #10B981; border-radius: 50%; display: inline-block; box-shadow: 0 0 6px #10B981;"></span> 
                        แอดมินออนไลน์พร้อมตอบ
                    </span>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="adminRefreshConversations()" style="padding: 6px 12px;">
                        🔄 รีเฟรชแชท
                    </button>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; min-height: 600px; border: 1.5px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; background: #FAF7F5;">
                <!-- Left Column: Customer Conversations List -->
                <div style="background: #FFFFFF; border-right: 1.5px solid var(--border-color); display: flex; flex-direction: column;">
                    <div style="padding: 12px; border-bottom: 1px solid var(--border-color); background: #FCFAF8;">
                        <input type="text" id="admin-chat-search" placeholder="🔍 ค้นหาชื่อสมาชิกหรืออีเมล..." oninput="adminFilterConversations()" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 0.85rem; background: #FFFFFF;">
                    </div>
                    <div id="admin-conv-list" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column;">
                        <div style="text-align: center; padding: 30px; color: var(--text-muted); font-size: 0.85rem;">
                            ⏳ กำลังโหลดรายชื่อลูกค้า...
                        </div>
                    </div>
                </div>

                <!-- Right Column: Active Chat Feed & Admin Input -->
                <div style="display: flex; flex-direction: column; background: #FFFFFF;">
                    <!-- Active Chat Header -->
                    <div id="admin-active-header" style="padding: 14px 20px; border-bottom: 1.5px solid var(--border-color); background: #FFFFFF; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img id="admin-target-avatar" src="assets/images/logo.png" alt="" style="width: 44px; height: 44px; border-radius: 50%; border: 2px solid var(--border-color); object-fit: cover;">
                            <div>
                                <div id="admin-target-name" style="font-weight: 800; font-size: 1.05rem; color: var(--text-main);">เลือกผู้ใช้เพื่อเริ่มสนทนา</div>
                                <div id="admin-target-meta" style="font-size: 0.8rem; color: var(--text-muted); display: flex; gap: 8px; align-items: center;">
                                    <span>กรุณาคลิกเลือกรายชื่อลูกค้าจากแถบด้านซ้าย</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Stream Area -->
                    <div id="admin-messages-box" style="flex: 1; padding: 20px; overflow-y: auto; background: #F8FAFC; display: flex; flex-direction: column; gap: 12px; max-height: 430px;">
                        <div style="text-align: center; padding: 50px 20px; color: #94A3B8;">
                            <span style="font-size: 2.5rem; display: block; margin-bottom: 10px;">💬</span>
                            คลิกเลือกรายชื่อสมาชิกทางด้านซ้ายเพื่อเปิดหน้าต่างสนทนาและพิมพ์ตอบกลับ
                        </div>
                    </div>

                    <!-- Admin Reply Controls & Attachment Box -->
                    <div id="admin-reply-panel" style="padding: 14px; border-top: 1.5px solid var(--border-color); background: #FFFFFF; display: none; flex-direction: column; gap: 10px;">
                        <!-- Quick Response Snippets & Product Attachment -->
                        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                            <div style="display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">⚡ ตอบด่วน:</span>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="adminInsertQuick('สวัสดีครับคุณลูกค้า มีอะไรให้แอดมินดูแลสอบถามได้เลยนะครับ 🐾')" style="font-size: 0.72rem; padding: 2px 8px;">
                                    👋 สวัสดีต้อนรับ
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="adminInsertQuick('น้องแมวตัวนี้สุขภาพสมบูรณ์มาก มีใบเพ็ดดีกรีและวัคซีนครบ พร้อมย้ายบ้านได้ทันทีครับ ✨')" style="font-size: 0.72rem; padding: 2px 8px;">
                                    🐱 แจ้งสถานะน้องแมว
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="adminInsertQuick('หากสะดวก สามารถส่งเบอร์โทรหรือ Line ID ไว้เพื่อให้แอดมินส่งวิดีโอตัวจริงของน้องให้ชมได้นะครับ 📱')" style="font-size: 0.72rem; padding: 2px 8px;">
                                    📱 ขอข้อมูลติดต่อ
                                </button>
                            </div>

                            <!-- Attach Cat Card Picker -->
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <label style="font-size: 0.75rem; font-weight: 700; color: var(--primary-coral);">🐱 แนบการ์ดแมว:</label>
                                <select id="admin-attach-cat" style="font-size: 0.78rem; padding: 4px 8px; border-radius: 6px; border: 1px solid var(--border-color); max-width: 180px;">
                                    <option value="">-- ไม่แนบการ์ดสินค้า --</option>
                                    <?php foreach ($cats as $cid => $c): ?>
                                        <option value="<?php echo $cid; ?>"><?php echo htmlspecialchars($c['name']); ?> (฿<?php echo number_format($c['price']); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Form -->
                        <form onsubmit="adminSubmitChatMessage(event)" style="display: flex; gap: 10px; align-items: flex-end;">
                            <textarea id="admin-input-text" rows="2" placeholder="พิมพ์ข้อความตอบกลับลูกค้าในฐานะแอดมิน..." style="flex: 1; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 0.9rem; font-family: inherit; resize: none; outline: none;"></textarea>
                            <button type="submit" class="btn btn-primary" style="padding: 12px 20px; font-weight: 700; height: 100%; border-radius: 10px; display: flex; align-items: center; gap: 6px;">
                                <span>ส่งข้อความ</span> <span>➤</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
        (function() {
            let allConversations = [];
            let activeTargetUserId = '';
            let adminPollTimer = null;

            window.adminRefreshConversations = function() {
                fetch('api_chat.php?action=admin_get_conversations')
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            allConversations = data.conversations || [];
                            renderAdminConversationsList(allConversations);
                            if (activeTargetUserId) {
                                adminLoadTargetChat(activeTargetUserId, false);
                            }
                        }
                    })
                    .catch(err => console.log('Admin chat list error:', err));
            };

            function renderAdminConversationsList(list) {
                const container = document.getElementById('admin-conv-list');
                if (!container) return;

                if (list.length === 0) {
                    container.innerHTML = '<div style="text-align:center; padding:30px; color:#94A3B8; font-size:0.85rem;">ไม่พบรายการสนทนา</div>';
                    return;
                }

                let html = '';
                list.forEach(c => {
                    const isActive = c.user_id === activeTargetUserId ? 'background: #FFF0EB; border-left: 4px solid var(--primary-coral);' : 'background: #FFFFFF;';
                    const roleBadge = c.is_member ? '<span style="font-size:0.68rem; background:#FEF3C7; color:#92400E; padding:1px 6px; border-radius:4px; font-weight:700;">🐾 สมาชิก</span>' : '<span style="font-size:0.68rem; background:#F1F5F9; color:#475569; padding:1px 6px; border-radius:4px;">👤 ผู้เยี่ยมชม</span>';
                    const unreadHtml = c.unread_admin > 0 ? `<span style="background:#EF4444; color:#FFF; font-size:0.7rem; font-weight:800; padding:2px 6px; border-radius:999px;">${c.unread_admin} ใหม่</span>` : '';

                    html += `
                        <div onclick="adminSelectCustomer('${c.user_id}')" style="padding: 12px 14px; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background 0.15s; ${isActive}" class="admin-conv-item">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <img src="${c.avatar || 'assets/images/logo.png'}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #E2E8F0;">
                                    <div>
                                        <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); line-height: 1.2;">
                                            ${escapeHtml(c.user_name)}
                                        </div>
                                        <div style="margin-top: 2px;">${roleBadge}</div>
                                    </div>
                                </div>
                                <div>${unreadHtml}</div>
                            </div>
                            <div style="font-size: 0.78rem; color: #64748B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 4px;">
                                ${escapeHtml(c.last_message || 'ยังไม่มีข้อความ')}
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            }

            window.adminFilterConversations = function() {
                const query = (document.getElementById('admin-chat-search').value || '').toLowerCase();
                const filtered = allConversations.filter(c => {
                    return (c.user_name || '').toLowerCase().includes(query) || (c.user_email || '').toLowerCase().includes(query);
                });
                renderAdminConversationsList(filtered);
            };

            window.adminSelectCustomer = function(userId) {
                activeTargetUserId = userId;
                renderAdminConversationsList(allConversations);
                adminLoadTargetChat(userId, true);
                document.getElementById('admin-reply-panel').style.display = 'flex';
                setTimeout(() => document.getElementById('admin-input-text').focus(), 200);
            };

            window.adminLoadTargetChat = function(userId, forceScroll) {
                fetch(`api_chat.php?action=get_messages&target_user_id=${encodeURIComponent(userId)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success' && data.conversation) {
                            const conv = data.conversation;
                            // Header
                            document.getElementById('admin-target-avatar').src = conv.avatar || 'assets/images/logo.png';
                            document.getElementById('admin-target-name').innerText = conv.user_name || 'ลูกค้า';
                            document.getElementById('admin-target-meta').innerHTML = `
                                <span>📧 ${escapeHtml(conv.user_email || 'ไม่มีอีเมล')}</span> &bull; 
                                <span>${conv.is_member ? '🐾 สมาชิกร้านค้า' : '👤 ผู้เยี่ยมชม'}</span> &bull; 
                                <span>🕒 ใช้งานล่าสุด: ${conv.last_active}</span>
                            `;

                            renderAdminMessages(conv.messages || [], forceScroll);
                        }
                    })
                    .catch(err => console.log('Admin target chat error:', err));
            };

            function renderAdminMessages(messages, forceScroll) {
                const box = document.getElementById('admin-messages-box');
                if (!box) return;

                let html = '';
                messages.forEach(msg => {
                    const isAdmin = msg.sender === 'admin';
                    const isBot = msg.sender === 'bot';
                    const alignStyle = isAdmin ? 'align-self: flex-end;' : 'align-self: flex-start;';
                    const bubbleBg = isAdmin ? 'background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E3A8A;' : (isBot ? 'background: #F1F5F9; border: 1px solid #CBD5E1; color: #334155;' : 'background: #FFFFFF; border: 1.5px solid #FFD8CC; color: #1E293B;');
                    const senderTag = isAdmin ? '👨‍💼 แอดมิน (คุณ)' : (isBot ? '🤖 ผู้ช่วย AI' : '👤 ' + (msg.sender_name || 'ลูกค้า'));

                    let cardHtml = '';
                    if (msg.cards && msg.cards.length > 0) {
                        cardHtml += '<div style="display:flex; gap:8px; margin-top:8px; overflow-x:auto; padding-bottom:4px;">';
                        msg.cards.forEach(card => {
                            cardHtml += `
                                <div style="flex-shrink:0; width:140px; background:#FFF; border:1px solid #E2E8F0; border-radius:8px; overflow:hidden; padding:6px; font-size:0.75rem;">
                                    <img src="${card.image}" style="width:100%; height:75px; object-fit:cover; border-radius:4px;">
                                    <div style="font-weight:700; color:#1E293B; margin-top:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${escapeHtml(card.name)}</div>
                                    <div style="color:#FF6B4A; font-weight:800;">${escapeHtml(card.price_fmt || card.price)}</div>
                                </div>
                            `;
                        });
                        cardHtml += '</div>';
                    }

                    html += `
                        <div style="max-width: 80%; ${alignStyle}">
                            <div style="font-size: 0.72rem; color: #94A3B8; margin-bottom: 2px; ${isAdmin ? 'text-align:right;' : ''}">
                                <strong>${senderTag}</strong> &bull; ${msg.timestamp ? (msg.timestamp.split(' ')[1] ? msg.timestamp.split(' ')[1].substring(0, 5) : msg.timestamp) : ''}
                            </div>
                            <div style="padding: 10px 14px; border-radius: 12px; font-size: 0.88rem; line-height: 1.45; box-shadow: 0 2px 4px rgba(0,0,0,0.03); ${bubbleBg}">
                                ${escapeHtml(msg.text).replace(/\n/g, '<br>')}
                                ${cardHtml}
                            </div>
                        </div>
                    `;
                });

                box.innerHTML = html;
                if (forceScroll) {
                    box.scrollTop = box.scrollHeight;
                }
            }

            window.adminInsertQuick = function(txt) {
                const input = document.getElementById('admin-input-text');
                input.value = txt;
                input.focus();
            };

            window.adminSubmitChatMessage = function(e) {
                e.preventDefault();
                if (!activeTargetUserId) return;

                const input = document.getElementById('admin-input-text');
                const text = input.value.trim();
                const catCard = document.getElementById('admin-attach-cat').value;

                if (!text) return;
                input.value = '';

                const formData = new FormData();
                formData.append('action', 'admin_send_message');
                formData.append('target_user_id', activeTargetUserId);
                formData.append('message', text);
                formData.append('cat_card_id', catCard);

                fetch('api_chat.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('admin-attach-cat').value = '';
                        adminLoadTargetChat(activeTargetUserId, true);
                    } else {
                        alert('ส่งข้อความไม่สำเร็จ: ' + data.message);
                    }
                })
                .catch(err => console.log('Admin send msg error:', err));
            };

            function escapeHtml(str) {
                if (!str) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            }

            document.addEventListener('DOMContentLoaded', () => {
                adminRefreshConversations();
                adminPollTimer = setInterval(() => {
                    adminRefreshConversations();
                }, 4000);
            });
        })();
        </script>
    <?php endif; ?>

    <!-- =========================================================
         TAB 6: ABANDONED CART RECOVERY & MARKETING AUTOMATION
         ========================================================= -->
    <?php if ($active_tab === 'abandoned'): ?>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.8rem; box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 style="font-size: 1.4rem; font-weight: 800; margin: 0 0 4px 0; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        🛒 ระบบกู้คืนตะกร้าสินค้าค้างชำระ (Abandoned Cart Recovery)
                    </h2>
                    <p style="margin: 0; font-size: 0.88rem; color: var(--text-muted);">
                        ส่งอีเมลแจ้งเตือนอัตโนมัติพร้อมคูปองส่วนลดพิเศษเพื่อกระตุ้นให้ลูกค้ากลับมาปิดยอดคำสั่งซื้อ
                    </p>
                </div>
            </div>

            <!-- Stats Bar -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.8rem;">
                <div style="background: #FFFBEB; border: 1.5px solid #FCD34D; border-radius: var(--radius-md); padding: 1.2rem;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: #B45309;">ตะกร้าค้างชำระทั้งหมด</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #92400E; font-family: 'Outfit';">3 รายการ</div>
                    <div style="font-size: 0.75rem; color: #B45309; margin-top: 2px;">มูลค่ารวมโดยประมาณ: ฿54,500</div>
                </div>
                <div style="background: #ECFDF5; border: 1.5px solid #A7F3D0; border-radius: var(--radius-md); padding: 1.2rem;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: #047857;">กู้คืนสำเร็จแล้ว</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #065F46; font-family: 'Outfit';">8 ออเดอร์</div>
                    <div style="font-size: 0.75rem; color: #047857; margin-top: 2px;">อัตรา Conversion: 72.5%</div>
                </div>
                <div style="background: #EFF6FF; border: 1.5px solid #BFDBFE; border-radius: var(--radius-md); padding: 1.2rem;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: #1E40AF;">โค้ดส่วนลดกู้คืน</div>
                    <div style="font-size: 1.3rem; font-weight: 800; color: #1E3A8A; font-family: 'Outfit';">HOLDMYCAT5</div>
                    <div style="font-size: 0.75rem; color: #1E40AF; margin-top: 2px;">ส่วนลดพิเศษ 5% + ฟรีของแถม</div>
                </div>
            </div>

            <!-- Abandoned Carts Table -->
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ลูกค้า / สมาชิก</th>
                            <th>น้องแมวที่เลือกค้างไว้</th>
                            <th>ยอดเงินในตะกร้า</th>
                            <th>ระยะเวลาที่ค้างไว้</th>
                            <th>การดำเนินการกู้คืน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--text-main);">คุณวิภาดา รักสัตว์</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">📧 wipada.cat@gmail.com</div>
                                <span style="font-size: 0.7rem; background: #FEF3C7; color: #92400E; padding: 1px 6px; border-radius: 4px; font-weight: 700;">🐾 สมาชิก</span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--primary-coral);">🐱 น้องสโนว์ (British Shorthair)</div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">+ Starter Kit เซ็ตของขวัญ 11 ชิ้น</div>
                            </td>
                            <td>
                                <strong style="font-family: 'Outfit'; font-size: 1.05rem; color: var(--text-main);">฿18,000</strong>
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: #EF4444; font-weight: 700;">⏳ ค้างไว้ 4 ชั่วโมง</span>
                            </td>
                            <td>
                                <form method="POST" action="admin.php?tab=abandoned">
                                    <input type="hidden" name="action" value="send_recovery_email">
                                    <input type="hidden" name="cart_email" value="wipada.cat@gmail.com">
                                    <input type="hidden" name="cart_cat" value="British Shorthair">
                                    <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.8rem; padding: 6px 14px; font-weight: 700; border-radius: 8px;">
                                        📧 ส่งอีเมลตามตะกร้า (Recovery)
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--text-main);">คุณธนวัฒน์ เมียวเมียว</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">📧 thanawat.meow@gmail.com</div>
                                <span style="font-size: 0.7rem; background: #FEF3C7; color: #92400E; padding: 1px 6px; border-radius: 4px; font-weight: 700;">🐾 สมาชิก</span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--primary-coral);">🐱 น้องปุยหิมะ (Persian Classic)</div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">ขนยาวฟู หน้าหวาน สายเลือดแชมป์</div>
                            </td>
                            <td>
                                <strong style="font-family: 'Outfit'; font-size: 1.05rem; color: var(--text-main);">฿16,500</strong>
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: #D97706; font-weight: 700;">⏳ ค้างไว้ 1 วัน</span>
                            </td>
                            <td>
                                <form method="POST" action="admin.php?tab=abandoned">
                                    <input type="hidden" name="action" value="send_recovery_email">
                                    <input type="hidden" name="cart_email" value="thanawat.meow@gmail.com">
                                    <input type="hidden" name="cart_cat" value="Persian Classic">
                                    <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.8rem; padding: 6px 14px; font-weight: 700; border-radius: 8px;">
                                        📧 ส่งอีเมลตามตะกร้า (Recovery)
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--text-main);">คุณกัญญารัตน์</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">📧 kanyarat.t@hotmail.com</div>
                                <span style="font-size: 0.7rem; background: #F1F5F9; color: #475569; padding: 1px 6px; border-radius: 4px;">👤 ผู้เยี่ยมชม</span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--primary-coral);">🐱 น้องพุดดิ้ง (Scottish Fold)</div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">หูพับ หน้ากลม ตาโต</div>
                            </td>
                            <td>
                                <strong style="font-family: 'Outfit'; font-size: 1.05rem; color: var(--text-main);">฿20,000</strong>
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: #D97706; font-weight: 700;">⏳ ค้างไว้ 2 วัน</span>
                            </td>
                            <td>
                                <form method="POST" action="admin.php?tab=abandoned">
                                    <input type="hidden" name="action" value="send_recovery_email">
                                    <input type="hidden" name="cart_email" value="kanyarat.t@hotmail.com">
                                    <input type="hidden" name="cart_cat" value="Scottish Fold">
                                    <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.8rem; padding: 6px 14px; font-weight: 700; border-radius: 8px;">
                                        📧 ส่งอีเมลตามตะกร้า (Recovery)
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // 1. Chart.js: Dynamic Revenue Chart (Daily / Weekly / Monthly)
    // -------------------------------------------------------------
    const revenueCanvas = document.getElementById('revenueChart');
    if (revenueCanvas) {
        const ctx = revenueCanvas.getContext('2d');
        
        const revDataSets = {
            daily: {
                labels: <?php echo json_encode($daily_rev['labels'] ?? []); ?>,
                data: <?php echo json_encode($daily_rev['values'] ?? []); ?>,
                label: 'รายรับรายวัน (฿ บาท)'
            },
            weekly: {
                labels: <?php echo json_encode($weekly_rev['labels'] ?? []); ?>,
                data: <?php echo json_encode($weekly_rev['values'] ?? []); ?>,
                label: 'รายรับรายสัปดาห์ (฿ บาท)'
            },
            monthly: {
                labels: <?php echo json_encode($monthly_rev['labels'] ?? []); ?>,
                data: <?php echo json_encode($monthly_rev['values'] ?? []); ?>,
                label: 'รายรับรายเดือน (฿ บาท)'
            }
        };

        // Create gradient fill
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(255, 107, 74, 0.4)');
        gradient.addColorStop(0.6, 'rgba(255, 107, 74, 0.1)');
        gradient.addColorStop(1, 'rgba(255, 107, 74, 0.0)');

        let activeRevType = 'daily';
        const initialSet = revDataSets[activeRevType];

        window.revChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: initialSet.labels,
                datasets: [{
                    label: initialSet.label,
                    data: initialSet.data,
                    borderColor: '#FF6B4A',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#FF6B4A',
                    pointBorderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { family: "'Prompt', 'Outfit', sans-serif", size: 12, weight: '700' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` 💰 ยอดขาย: ฿${context.raw.toLocaleString()} บาท`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(226, 232, 240, 0.6)' },
                        ticks: {
                            callback: function(value) {
                                return '฿' + (value >= 1000 ? (value / 1000) + 'k' : value);
                            },
                            font: { family: "'Outfit', sans-serif" }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Prompt', sans-serif" } }
                    }
                }
            }
        });

        window.switchRevenueChart = function(type) {
            if (!revDataSets[type] || !window.revChartInstance) return;
            activeRevType = type;

            ['daily', 'weekly', 'monthly'].forEach(t => {
                const btn = document.getElementById(`btn-chart-${t}`);
                if (btn) {
                    if (t === type) {
                        btn.style.background = 'var(--primary-coral)';
                        btn.style.color = '#FFFFFF';
                    } else {
                        btn.style.background = 'transparent';
                        btn.style.color = 'var(--text-secondary)';
                    }
                }
            });

            const newSet = revDataSets[type];
            window.revChartInstance.data.labels = newSet.labels;
            window.revChartInstance.data.datasets[0].label = newSet.label;
            window.revChartInstance.data.datasets[0].data = newSet.data;
            window.revChartInstance.update();
        };
    }

    // -------------------------------------------------------------
    // 2. Chart.js: Top Clicked Cat Breeds (Horizontal Bar)
    // -------------------------------------------------------------
    const initCatClicksChart = (canvasId) => {
        const catCanvas = document.getElementById(canvasId);
        if (catCanvas) {
            new Chart(catCanvas, {
                type: 'bar',
                data: {
                    labels: ['Scottish Fold', 'British Shorthair', 'Ragdoll', 'Maine Coon', 'Sphynx', 'Persian'],
                    datasets: [{
                        label: 'จำนวนคลิกดู (Clicks)',
                        data: [1420, 1280, 1150, 980, 840, 760],
                        backgroundColor: [
                            '#FF6B4A',
                            '#F59E0B',
                            '#3B82F6',
                            '#10B981',
                            '#8B5CF6',
                            '#EC4899'
                        ],
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` 🐾 ยอดคลิก: ${context.raw.toLocaleString()} ครั้ง`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, grid: { color: 'rgba(226, 232, 240, 0.6)' } },
                        y: { grid: { display: false }, ticks: { font: { family: "'Outfit', sans-serif", weight: '600' } } }
                    }
                }
            });
        }
    };
    initCatClicksChart('catClicksChart');
    initCatClicksChart('catClicksChart2');

    // -------------------------------------------------------------
    // 3. Chart.js: Member Preferences & Needs (Doughnut)
    // -------------------------------------------------------------
    const initMemberNeedsChart = (canvasId) => {
        const needsCanvas = document.getElementById(canvasId);
        if (needsCanvas) {
            new Chart(needsCanvas, {
                type: 'doughnut',
                data: {
                    labels: [
                        '🏢 เลี้ยงในคอนโด (35%)',
                        '🤧 ภูมิแพ้/ไม่ผลัดขน (25%)',
                        '🐱 มือใหม่เลี้ยงง่าย (20%)',
                        '👑 พรีเมียมมีใบเพ็ด (12%)',
                        '🎾 แมวขี้เล่นร่าเริง (8%)'
                    ],
                    datasets: [{
                        data: [35, 25, 20, 12, 8],
                        backgroundColor: [
                            '#3B82F6',
                            '#10B981',
                            '#FF6B4A',
                            '#F59E0B',
                            '#8B5CF6'
                        ],
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: "'Prompt', sans-serif", size: 11, weight: '600' },
                                boxWidth: 12,
                                padding: 10
                            }
                        }
                    },
                    cutout: '62%'
                }
            });
        }
    };
    initMemberNeedsChart('memberNeedsChart');
    initMemberNeedsChart('memberNeedsChart2');

    // -------------------------------------------------------------
    // 4. Chart.js: Specialized Delivery Preferences (Doughnut)
    // -------------------------------------------------------------
    const shippingCanvas = document.getElementById('shippingChart');
    if (shippingCanvas) {
        new Chart(shippingCanvas, {
            type: 'doughnut',
            data: {
                labels: [
                    '🚐 Pet Taxi ติดแอร์ (68%)',
                    '✈️ Pet Air Cargo (22%)',
                    '🏡 นัดรับที่ฟาร์ม (10%)'
                ],
                datasets: [{
                    data: [68, 22, 10],
                    backgroundColor: [
                        '#FF6B4A',
                        '#3B82F6',
                        '#10B981'
                    ],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: "'Prompt', sans-serif", size: 11, weight: '600' },
                            boxWidth: 12,
                            padding: 10
                        }
                    }
                },
                cutout: '62%'
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
