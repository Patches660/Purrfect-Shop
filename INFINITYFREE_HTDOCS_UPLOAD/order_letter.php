<?php
require_once __DIR__ . '/data.php';

$order_id = trim($_GET['id'] ?? '');
$order = null;

if (!empty($order_id)) {
    $order = getOrderById($order_id);
}

// If not found by exact ID, try fallback to the first order if user is logged in
if (!$order && isUserLoggedIn()) {
    $currUser = getCurrentUser();
    $user_orders = getUserOrders($currUser['email']);
    if (!empty($user_orders)) {
        $order = $user_orders[0];
        $order_id = $order['order_id'];
    }
}

// Page title
$page_title = "จดหมายตอบรับการรับเลี้ยงอย่างเป็นทางการ - Purrfect Shop";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $order ? "จดหมายตอบรับคำสั่งจอง #{$order['order_id']}" : "ไม่พบเอกสารคำสั่งซื้อ"; ?> - Purrfect Shop</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Prompt:wght@300;400;500;600;700;800&family=Sarabun:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --cert-gold: #C59B27;
            --cert-gold-dark: #8C6D1F;
            --cert-gold-light: #FBF6E9;
            --cert-navy: #1A2B49;
            --cert-border: #D4AF37;
        }

        body {
            background: #F4F6F9;
            font-family: 'Sarabun', 'Prompt', sans-serif;
            color: #2D3748;
            margin: 0;
            padding: 20px 10px;
        }

        .cert-outer-wrap {
            max-width: 900px;
            margin: 0 auto;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #FFFFFF;
            padding: 12px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.92rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-print {
            background: linear-gradient(135deg, #FF7556, #E05335);
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(255, 117, 86, 0.35);
        }
        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(255, 117, 86, 0.45);
        }

        .btn-back {
            background: #EDF2F7;
            color: #4A5568;
        }
        .btn-back:hover {
            background: #E2E8F0;
            color: #1A202C;
        }

        /* Certificate Paper Styling */
        .cert-paper {
            background: #FFFFFF;
            border: 12px double var(--cert-gold);
            border-radius: 4px;
            padding: 40px 48px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 8rem;
            font-weight: 900;
            color: rgba(197, 155, 39, 0.04);
            white-space: nowrap;
            user-select: none;
            pointer-events: none;
            z-index: 1;
            font-family: 'Outfit', sans-serif;
            text-transform: uppercase;
        }

        .cert-header {
            text-align: center;
            border-bottom: 2px solid #E2E8F0;
            padding-bottom: 24px;
            position: relative;
            z-index: 2;
        }

        .cert-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--cert-gold);
            padding: 3px;
            margin-bottom: 12px;
        }

        .cert-cattery-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--cert-navy);
            letter-spacing: 1px;
            margin: 0;
            text-transform: uppercase;
        }

        .cert-cattery-sub {
            font-size: 0.95rem;
            color: var(--cert-gold-dark);
            font-weight: 600;
            margin-top: 4px;
        }

        .cert-doc-name {
            font-size: 1.45rem;
            font-weight: 700;
            color: #C0392B;
            margin-top: 16px;
            letter-spacing: 0.5px;
            padding: 6px 20px;
            display: inline-block;
            background: #FFF5F5;
            border: 1px dashed #E53E3E;
            border-radius: 8px;
        }

        .cert-meta-bar {
            display: flex;
            justify-content: space-between;
            background: var(--cert-gold-light);
            border: 1px solid rgba(197, 155, 39, 0.35);
            border-radius: 8px;
            padding: 12px 20px;
            margin: 24px 0;
            font-size: 0.9rem;
            flex-wrap: wrap;
            gap: 12px;
            position: relative;
            z-index: 2;
        }

        .cert-meta-item strong {
            color: var(--cert-navy);
        }

        .cert-body {
            position: relative;
            z-index: 2;
            line-height: 1.75;
            font-size: 1rem;
            color: #334155;
        }

        .cert-greeting {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--cert-navy);
            margin-bottom: 12px;
        }

        .cert-intro-p {
            margin-bottom: 20px;
            text-align: justify;
        }

        .info-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 18px;
            margin: 20px 0;
        }

        .info-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 16px;
        }

        .info-box-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--cert-navy);
            border-bottom: 1.5px solid #E2E8F0;
            padding-bottom: 6px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box p {
            margin: 4px 0;
            font-size: 0.9rem;
        }

        /* Adopted Cats Section */
        .cats-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            overflow: hidden;
        }

        .cats-table th {
            background: #1E293B;
            color: #FFFFFF;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 12px 14px;
            text-align: left;
        }

        .cats-table td {
            padding: 14px;
            border-bottom: 1px solid #E2E8F0;
            vertical-align: middle;
            font-size: 0.92rem;
        }

        .cat-thumb {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            object-fit: cover;
            border: 1.5px solid #CBD5E1;
        }

        /* Health & Safety Guarantee Banner */
        .guarantee-box {
            background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
            border: 1.5px solid #86EFAC;
            border-radius: 10px;
            padding: 18px 22px;
            margin: 24px 0;
        }

        .guarantee-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #166534;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .guarantee-list {
            margin: 0;
            padding-left: 20px;
            font-size: 0.9rem;
            color: #14532D;
            line-height: 1.6;
        }

        /* Signatures and Official Seal */
        .cert-footer {
            margin-top: 36px;
            padding-top: 24px;
            border-top: 2px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
            gap: 24px;
        }

        .official-seal {
            width: 130px;
            height: 130px;
            border: 4px dashed var(--cert-gold);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--cert-gold-dark);
            font-weight: 800;
            font-size: 0.72rem;
            text-transform: uppercase;
            padding: 8px;
            background: rgba(197, 155, 39, 0.05);
            transform: rotate(-8deg);
            box-shadow: 0 0 0 4px rgba(197, 155, 39, 0.15);
        }

        .seal-star {
            font-size: 1.2rem;
            color: var(--cert-gold);
            margin: 2px 0;
        }

        .signature-block {
            text-align: center;
            min-width: 220px;
        }

        .signature-line {
            width: 180px;
            height: 1px;
            background: #475569;
            margin: 12px auto 6px auto;
        }

        .signature-name {
            font-weight: 700;
            color: #0F172A;
            font-size: 0.95rem;
        }

        .signature-title {
            font-size: 0.82rem;
            color: #64748B;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
                color: #000000 !important;
            }
            .action-bar, .no-print {
                display: none !important;
            }
            .cert-paper {
                box-shadow: none !important;
                border: 4px solid var(--cert-gold) !important;
                padding: 24px 30px !important;
            }
            .cert-outer-wrap {
                max-width: 100% !important;
                width: 100% !important;
            }
        }

        @media (max-width: 640px) {
            .cert-paper {
                padding: 20px 16px;
                border-width: 6px;
            }
            .cert-cattery-title {
                font-size: 1.35rem;
            }
            .cert-doc-name {
                font-size: 1.05rem;
            }
            .cert-footer {
                justify-content: center;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="cert-outer-wrap">
    <!-- Top Action Toolbar -->
    <div class="action-bar no-print">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="profile.php?tab=orders" class="btn-action btn-back">
                &larr; กลับหน้ารายการสั่งซื้อ
            </a>
            <?php if (isAdmin()): ?>
                <a href="admin.php?tab=orders" class="btn-action btn-back" style="background: #FEF3C7; color: #92400E;">
                    🛠️ กลับหน้า Admin
                </a>
            <?php endif; ?>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-action btn-print">
                🖨️ พิมพ์เอกสาร / ดาวน์โหลดเป็น PDF
            </button>
        </div>
    </div>

    <?php if (!$order): ?>
        <div class="cert-paper" style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 4rem; margin-bottom: 12px;">📄</div>
            <h2 style="color: #E53E3E; margin-bottom: 8px;">ไม่พบเอกสารยืนยันคำสั่งซื้อ</h2>
            <p style="color: #64748B; margin-bottom: 24px;">ไม่พบรหัสคำสั่งจองที่ระบุ หรือเอกสารอาจยังไม่ได้ถูกสร้างในระบบ</p>
            <a href="profile.php?tab=orders" class="btn-action btn-back">กลับไปตรวจสอบประวัติคำสั่งซื้อ</a>
        </div>
    <?php else: ?>
        <div class="cert-paper">
            <div class="cert-watermark">PURRFECT SHOP</div>

            <!-- Certificate Header -->
            <div class="cert-header">
                <img src="assets/images/logo.png" alt="Purrfect Shop Seal" class="cert-logo">
                <h1 class="cert-cattery-title">PURRFECT CATTERY & BOUTIQUE THAILAND</h1>
                <div class="cert-cattery-sub">
                    ฟาร์มเพาะพันธุ์แมวสายพันธุ์แท้มาตรฐานสากล (TICA / WCF / CFA Member Certified)
                </div>
                <div>
                    <span class="cert-doc-name">
                        📜 หนังสือตอบรับการรับเลี้ยงน้องแมวอย่างเป็นทางการ
                    </span>
                </div>
            </div>

            <!-- Meta Reference Bar -->
            <div class="cert-meta-bar">
                <div class="cert-meta-item">
                    <span>เลขที่ใบคำสั่งจอง:</span> <strong>#<?php echo htmlspecialchars($order['order_id']); ?></strong>
                </div>
                <div class="cert-meta-item">
                    <span>วันที่ออกหนังสือ:</span> <strong><?php echo htmlspecialchars($order['created_at']); ?></strong>
                </div>
                <div class="cert-meta-item">
                    <span>สถานะการตอบรับ:</span> 
                    <strong style="color: #059669;">
                        <?php echo htmlspecialchars($order['status'] ?? 'ยืนยันการรับเลี้ยงเรียบร้อยแล้ว'); ?>
                    </strong>
                </div>
            </div>

            <!-- Certificate Body Content -->
            <div class="cert-body">
                <div class="cert-greeting">
                    เรียน คุณ <?php echo htmlspecialchars($order['customer_name']); ?>,
                </div>
                <p class="cert-intro-p">
                    ทางฟาร์ม <strong>Purrfect Cattery & Boutique Thailand</strong> มีความยินดีเป็นอย่างยิ่งที่ได้มีโอกาสส่งมอบสมาชิกตัวน้อยแสนน่ารักสู่ครอบครัวของคุณ เราขอขอบพระคุณเป็นอย่างสูงที่ท่านได้ให้ความไว้วางใจในการคัดเลือกน้องแมวสายพันธุ์แท้จากฟาร์มของเรา ทางเราขอออกหนังสือฉบับนี้เพื่อยืนยันการจองสิทธิ์การรับเลี้ยงและการเตรียมส่งมอบอย่างเป็นทางการ
                </p>

                <!-- Customer and Delivery Information Grid -->
                <div class="info-card-grid">
                    <div class="info-box">
                        <div class="info-box-title">
                            👤 ข้อมูลผู้รับเลี้ยง (Adopter Information)
                        </div>
                        <p><strong>ชื่อ-นามสกุล:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p><strong>เบอร์โทรศัพท์:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                        <p><strong>อีเมล:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                        <p><strong>บัญชีสมาชิก:</strong> @<?php echo htmlspecialchars($order['username']); ?></p>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            🚚 ข้อมูลการจัดส่งมอบ (Climate-Van Delivery)
                        </div>
                        <p><strong>กำหนดการส่งมอบ:</strong> <?php echo htmlspecialchars($order['delivery_date'] ?? 'ตามนัดหมาย'); ?></p>
                        <p><strong>ช่วงเวลา:</strong> <?php echo htmlspecialchars($order['delivery_timeslot'] ?? 'ช่วงบ่าย (13:00 - 17:00 น.)'); ?></p>
                        <p><strong>สถานที่จัดส่ง:</strong> <?php echo htmlspecialchars($order['delivery_address'] ?? 'รับที่ฟาร์ม'); ?></p>
                        <?php if (!empty($order['delivery_lat']) && !empty($order['delivery_lng'])): ?>
                            <p><strong>พิกัดแผนที่ (GPS):</strong> Lat: <?php echo htmlspecialchars($order['delivery_lat']); ?>, Lng: <?php echo htmlspecialchars($order['delivery_lng']); ?> (<a href="https://www.google.com/maps?q=<?php echo urlencode($order['delivery_lat'] . ',' . $order['delivery_lng']); ?>" target="_blank" style="color: #0284C7; text-decoration: underline;">Google Maps ↗</a>)</p>
                        <?php endif; ?>
                        <p><strong>ยานพาหนะ:</strong> รถตู้แอร์ปรับอุณหภูมิเฉพาะสัตว์เลี้ยง (Pet Nanny Service)</p>
                    </div>
                </div>

                <!-- Adopted Items Table -->
                <div style="font-weight: 700; color: var(--cert-navy); font-size: 1.05rem; margin-top: 24px; margin-bottom: 8px;">
                    🐱 รายการน้องแมว & แพ็กเกจที่ได้รับการยืนยัน
                </div>
                <div class="cats-table-wrap">
                <table class="cats-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">รูปภาพ</th>
                            <th>ชื่อและสายพันธุ์</th>
                            <th>เพศ / อายุ</th>
                            <th>วัคซีน & เพ็ดดีกรี</th>
                            <th style="text-align: right; width: 110px;">มูลค่า (บาท)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td>
                                    <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cat-thumb">
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #1E293B; font-size: 0.95rem;">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </div>
                                    <div style="font-size: 0.82rem; color: #64748B;">
                                        สายพันธุ์: <?php echo htmlspecialchars($item['breed']); ?> (x<?php echo $item['qty']; ?>)
                                    </div>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($item['gender'] ?? 'ไม่ระบุ'); ?></div>
                                    <div style="font-size: 0.8rem; color: #64748B;"><?php echo htmlspecialchars($item['age'] ?? '-'); ?></div>
                                </td>
                                <td style="font-size: 0.82rem; color: #334155;">
                                    <div>✓ <?php echo htmlspecialchars($item['vaccine'] ?? 'ฉีดวัคซีนครบถ้วน'); ?></div>
                                    <div style="color: #059669; font-weight: 600;">✓ <?php echo htmlspecialchars($item['pedigree'] ?? 'มีใบรับรองสายพันธุ์'); ?></div>
                                </td>
                                <td style="text-align: right; font-weight: 700; font-family: 'Outfit'; color: #0F172A;">
                                    <?php echo number_format($item['price'] * $item['qty']); ?> ฿
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: 600; color: #64748B; border: none; padding-top: 8px;">
                                ยอดรวมสินค้า:
                            </td>
                            <td style="text-align: right; font-family: 'Outfit'; font-weight: 600; border: none; padding-top: 8px;">
                                <?php echo number_format($order['subtotal'], 2); ?> ฿
                            </td>
                        </tr>
                        <?php if (($order['discount'] ?? 0) > 0): ?>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: 600; color: #059669; border: none; padding: 2px 14px;">
                                สิทธิพิเศษส่วนลดสมาชิก (5%):
                            </td>
                            <td style="text-align: right; font-family: 'Outfit'; font-weight: 700; color: #059669; border: none; padding: 2px 14px;">
                                -<?php echo number_format($order['discount'], 2); ?> ฿
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: 600; color: #64748B; border: none; padding: 2px 14px;">
                                ภาษีมูลค่าเพิ่ม (VAT 7%):
                            </td>
                            <td style="text-align: right; font-family: 'Outfit'; border: none; padding: 2px 14px;">
                                <?php echo number_format($order['vat'] ?? 0, 2); ?> ฿
                            </td>
                        </tr>
                        <tr style="border-top: 2px solid #0F172A;">
                            <td colspan="4" style="text-align: right; font-weight: 800; font-size: 1.05rem; color: #0F172A;">
                                ยอดชำระสุทธิ (Total Paid):
                            </td>
                            <td style="text-align: right; font-weight: 800; font-size: 1.25rem; color: #E05335; font-family: 'Outfit';">
                                <?php echo number_format($order['total'], 2); ?> ฿
                            </td>
                        </tr>
                    </tfoot>
                </table>
                </div>

                <!-- Health & Warranty Guarantee -->
                <div class="guarantee-box">
                    <div class="guarantee-title">
                        🛡️ การรับประกันสุขภาพมาตรฐานระดับสากล (Comprehensive Health Warranty - 180 วัน)
                    </div>
                    <ul class="guarantee-list">
                        <li><strong>การตรวจคัดกรองพันธุกรรม:</strong> ปราศจากโรคทางพันธุกรรมร้ายแรง เช่น โรคหัวใจโต (HCM), โรคไตวายเรื้อรัง (PKD) ตรวจสอบจากห้องแล็บมาตรฐานสากล</li>
                        <li><strong>การป้องกันโรคติดต่อ 180 วัน:</strong> รับประกันไข้หัดแมว (FPV), มะเร็งเม็ดเลือดขาว (FeLV), และเยื่อบุช่องท้องอักเสบ (FIP) ภายใน 180 วันนับแต่วันรับมอบ</li>
                        <li><strong>ไมโครชิปมาตรฐาน ISO 11784/11785:</strong> ฝังไมโครชิปสากล 15 หลัก พร้อมลงทะเบียนฐานข้อมูลสัตว์เลี้ยงระดับสากล</li>
                        <li><strong>ทีมสัตวแพทย์ดูแลต่อเนื่อง:</strong> ให้คำปรึกษาตลอด 24 ชั่วโมง พร้อมบริการเข้าตรวจสุขภาพฟรี 1 ครั้ง ณ เครือข่าย รพ.สัตว์ชั้นนำ</li>
                    </ul>
                </div>

                <!-- Guidance for New Parents -->
                <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 8px; padding: 14px 18px; font-size: 0.88rem; color: #92400E; margin-bottom: 24px;">
                    <strong>📌 ข้อแนะนำในการเตรียมต้อนรับน้องแมว:</strong>
                    กรุณาเตรียมห้องพักที่เงียบสงบ อุณหภูมิเหมาะสม (24-26°C) อาหารสูตรเดิมที่ฟาร์มมอบให้ ชามน้ำ และกระบะทราย ในช่วง 3-5 วันแรกควรให้น้องปรับตัวอย่างใจเย็น หลีกเลี่ยงการส่งเสียงดัง หากต้องการคำปรึกษาเร่งด่วนสามารถติดต่อสายด่วนสัตวแพทย์ฟาร์มได้ตลอด 24 ชั่วโมง
                </div>

                <!-- Footer Signatures & Stamp -->
                <div class="cert-footer">
                    <div class="signature-block">
                        <div style="font-family: 'Brush Script MT', cursive; font-size: 1.8rem; color: #1E293B;">
                            Purrfect Cattery Team
                        </div>
                        <div class="signature-line"></div>
                        <div class="signature-name">พัทธนันท์ คำหงษ์ษา</div>
                        <div class="signature-title">ผู้ก่อตั้งและหัวหน้าผู้เพาะพันธุ์ (Master Breeder)</div>
                        <div style="font-size: 0.75rem; color: #94A3B8;">ใบอนุญาตเพาะพันธุ์เลขที่: TH-CAT-TH-CAT-8899</div>
                    </div>

                    <!-- Official Golden Seal -->
                    <div class="official-seal">
                        <span>PURRFECT CATTERY</span>
                        <div class="seal-star">★ ★ ★ ★ ★</div>
                        <span>OFFICIAL ADOPTION<br>SEAL OF AUTHENTICITY</span>
                        <div style="font-size: 0.58rem; margin-top: 3px; color: #92400E;">BANGKOK &bull; THAILAND</div>
                    </div>

                    <div class="signature-block">
                        <div style="font-family: 'Brush Script MT', cursive; font-size: 1.8rem; color: #1E293B;">
                            Dr. Purrfect Vet, D.V.M.
                        </div>
                        <div class="signature-line"></div>
                        <div class="signature-name">น.สพ. ผู้เชี่ยวชาญด้านเวชศาสตร์แมว</div>
                        <div class="signature-title">สัตวแพทย์ประจำฟาร์ม (Chief Veterinarian)</div>
                        <div style="font-size: 0.75rem; color: #94A3B8;">เลขที่ใบประกอบโรคศิลปะ: VET-2567-0899</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
