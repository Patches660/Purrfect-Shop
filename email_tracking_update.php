<?php
// email_tracking_update.php - HTML Email Template for Live Pet Delivery Tracking Updates
function renderTrackingStatusEmail($customerName, $customerEmail, $orderId, $trackingId, $currentStatus, $orderData, $deliveryNote = '') {
    $steps = [
        'vet_check' => [
            'num' => 1,
            'title' => 'ตรวจสุขภาพก่อนเดินทาง (Vet Health Check)',
            'icon' => '🩺',
            'desc' => 'สัตวแพทย์ประจำฟาร์มตรวจสุขภาพ ตรวจเลือดปลอดโรค 100% พร้อมตรวจความพร้อมของร่างกาย',
            'badge' => 'ผ่านการรับรองสุขภาพเรียบร้อย',
            'color' => '#0D9488',
            'bg' => '#F0FDF4',
            'border' => '#6EE7B7',
            'title_color' => '#0F766E',
            'desc_color' => '#134E4A'
        ],
        'grooming' => [
            'num' => 2,
            'title' => 'เตรียมความพร้อม & กรูมมิ่ง (Grooming & Travel Kit)',
            'icon' => '🛁',
            'desc' => 'น้องแมวได้รับการอาบน้ำ กรูมมิ่ง ตัดเล็บ และจัดเตรียมกล่องเดินทางปรับอากาศพร้อมเซ็ตของขวัญ',
            'badge' => 'พร้อมออกเดินทาง',
            'color' => '#0284C7',
            'bg' => '#F0F9FF',
            'border' => '#7DD3FC',
            'title_color' => '#0369A1',
            'desc_color' => '#0C4A6E'
        ],
        'transit' => [
            'num' => 3,
            'title' => 'กำลังออกเดินทางส่งมอบ (In Transit / Pet Taxi)',
            'icon' => '🚐',
            'desc' => 'น้องแมวอยู่บนรถตู้ปรับอากาศควบคุมอุณหภูมิ 25°C หรือเครื่องบิน พร้อมพี่เลี้ยงดูแลตลอดทาง',
            'badge' => 'อยู่ระหว่างเดินทาง',
            'color' => '#EA580C',
            'bg' => '#FFF7ED',
            'border' => '#FDBA74',
            'title_color' => '#C2410C',
            'desc_color' => '#7C2D12'
        ],
        'delivered' => [
            'num' => 4,
            'title' => 'ส่งมอบถึงมือผู้รับเรียบร้อย (Delivered 🐾)',
            'icon' => '🏡',
            'desc' => 'ส่งมอบน้องแมวถึงมือคุณลูกค้าหน้าบ้านเรียบร้อย พร้อมเปิดใช้งานการรับประกันสุขภาพ 180 วัน',
            'badge' => 'ส่งมอบสำเร็จ',
            'color' => '#7C3AED',
            'bg' => '#FAF5FF',
            'border' => '#D8B4FE',
            'title_color' => '#6D28D9',
            'desc_color' => '#4C1D95'
        ]
    ];

    $status_order_map = ['vet_check' => 1, 'grooming' => 2, 'transit' => 3, 'delivered' => 4];
    $current_step_num = $status_order_map[$currentStatus] ?? 3;
    $currStepInfo = $steps[$currentStatus] ?? $steps['transit'];

    $shipping_method = $orderData['shipping_method'] ?? 'pet_taxi';
    $shipping_label = ($shipping_method === 'air_cargo') ? '✈️ ส่งทางเครื่องบิน (Pet Air Cargo)' : (($shipping_method === 'farm_pickup') ? '🏡 นัดรับที่ฟาร์ม (Farm Pick-up)' : '🚐 รถตู้ปรับอากาศส่งสัตว์เลี้ยง (Pet Taxi Express)');

    require_once __DIR__ . '/email_order_confirmation.php';

    $items = $orderData['items'] ?? [];
    $catNames = [];
    $itemsCardsHtml = '';

    foreach ($items as $it) {
        $catNames[] = $it['name'] . (!empty($it['breed']) ? ' (' . $it['breed'] . ')' : '');

        $cat_img = function_exists('getOrderProductImageUrl') 
            ? getOrderProductImageUrl($it, 'https://patches660.github.io/Purrfect-Shop-Files')
            : 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200&auto=format&fit=crop&q=80';
        
        $cat_name = htmlspecialchars($it['name'] ?? 'น้องแมวสายพันธุ์แท้');
        $cat_breed = htmlspecialchars($it['breed'] ?? 'Purrfect Cattery');
        $cat_age = !empty($it['age']) ? htmlspecialchars($it['age']) : '';
        $cat_gender = !empty($it['gender']) ? htmlspecialchars($it['gender']) : '';
        $cat_qty = intval($it['qty'] ?? 1);
        $cat_sub = array_filter([$cat_breed, $cat_gender, $cat_age, ($cat_qty > 1 ? "จำนวน: {$cat_qty} ตัว" : '')]);
        $cat_sub_str = implode(' • ', $cat_sub);

        $itemsCardsHtml .= <<<HTML
            <tr>
                <td width="72" valign="middle" style="padding: 10px 12px 10px 0; border-bottom: 1px dashed #FFD5CB;">
                    <img src="{$cat_img}" alt="{$cat_name}" width="65" height="65" style="width: 65px; height: 65px; border-radius: 12px; object-fit: cover; border: 2px solid #FF8A65; display: block; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                </td>
                <td valign="middle" style="padding: 10px 0; border-bottom: 1px dashed #FFD5CB;">
                    <div style="font-weight: 800; font-size: 14px; color: #1E293B;">{$cat_name}</div>
                    <div style="font-size: 11.5px; color: #64748B; margin-top: 2px;">{$cat_sub_str}</div>
                    <div style="font-size: 10.5px; color: #059669; font-weight: 700; margin-top: 3px;">✓ สุขภาพสมบูรณ์ • ฉีดวัคซีนครบ • ฝังไมโครชิป • เพ็ดดีกรีแท้</div>
                </td>
            </tr>
HTML;
    }
    $catNamesStr = !empty($catNames) ? implode(', ', $catNames) : 'น้องแมวสายพันธุ์แท้ Purrfect';

    $homepageUrl = 'https://patches660.github.io/Purrfect-Shop-Files/';
    $trackingUrl = $homepageUrl;

    $gps_html = '';
    if (!empty($orderData['delivery_lat']) && !empty($orderData['delivery_lng'])) {
        $lat = htmlspecialchars($orderData['delivery_lat']);
        $lng = htmlspecialchars($orderData['delivery_lng']);
        $gmap_url = 'https://www.google.com/maps?q=' . urlencode($lat . ',' . $lng);
        $gps_html = "<br><a href=\"{$gmap_url}\" target=\"_blank\" style=\"color: #0284C7; font-size: 11px; text-decoration: underline;\">🗺️ พิกัด GPS: {$lat}, {$lng} (เปิดดูบน Google Maps ↗)</a>";
    }

    $noteHtml = '';
    if (!empty($deliveryNote)) {
        $noteHtml = <<<HTML
                    <tr>
                        <td style="padding: 0 25px 15px 25px;">
                            <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 14px 16px; color: #166534; font-size: 13px;">
                                <strong>📝 บันทึกจากพี่เลี้ยง & เจ้าหน้าที่จัดส่ง:</strong><br>
                                <span style="display: block; margin-top: 4px; color: #15803D; line-height: 1.5;">{$deliveryNote}</span>
                            </div>
                        </td>
                    </tr>
HTML;
    }

    $petsHtml = '';
    if (!empty($itemsCardsHtml)) {
        $petsHtml = <<<HTML
                    <!-- Purchased Kittens Showcase Card with Real Images -->
                    <tr>
                        <td style="padding: 0 25px 15px 25px;">
                            <div style="background: linear-gradient(135deg, #FFF7F3 0%, #FFF0EB 100%); border: 1.5px solid #FFD0C0; border-radius: 12px; padding: 14px 16px;">
                                <h4 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 800; color: #EA580C; display: flex; align-items: center; gap: 6px;">
                                    🐾 น้องแมวที่กำลังจัดส่งส่งมอบ (Purchased Kittens)
                                </h4>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size: 12px;">
                                    {$itemsCardsHtml}
                                </table>
                            </div>
                        </td>
                    </tr>
HTML;
    }

    $html = <<<HTML
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อัปเดตสถานะการจัดส่งน้องแมว - Purrfect Shop</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F8FAFC; font-family: 'Prompt', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1E293B;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #F8FAFC; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table width="100%" max-width="620" border="0" cellspacing="0" cellpadding="0" style="max-width: 620px; background-color: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #E2E8F0;">
                    
                    <!-- Header Banner -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); padding: 30px 25px; text-align: center; color: #FFFFFF;">
                            <div style="font-size: 38px; margin-bottom: 6px;">🚐🐾📍</div>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.5px; color: #FFFFFF;">
                                อัปเดตความคืบหน้าการจัดส่งสัตว์เลี้ยง
                            </h1>
                            <p style="margin: 6px 0 0 0; font-size: 13px; color: #94A3B8;">
                                Purrfect Cattery & Boutique • Live Pet Delivery Tracking
                            </p>
                        </td>
                    </tr>

                    <!-- Tracking Number Strip with Dynamic Stage Color -->
                    <tr>
                        <td style="background-color: {$currStepInfo['bg']}; padding: 14px 25px; border-bottom: 1.5px solid {$currStepInfo['border']}; text-align: center;">
                            <span style="font-size: 12px; color: {$currStepInfo['title_color']}; font-weight: 700; text-transform: uppercase;">หมายเลขติดตามพัสดุ (Tracking ID):</span>
                            <div style="font-size: 20px; font-weight: 900; color: {$currStepInfo['color']}; letter-spacing: 1px; margin-top: 2px;">
                                {$trackingId}
                            </div>
                            <span style="font-size: 12px; color: #64748B;">รหัสคำสั่งซื้อ: #{$orderId}</span>
                        </td>
                    </tr>

                    <!-- Current Status Alert Box (Stage Theme) -->
                    <tr>
                        <td style="padding: 25px 25px 15px 25px;">
                            <div style="background-color: {$currStepInfo['bg']}; border: 2px solid {$currStepInfo['color']}; border-radius: 12px; padding: 18px 20px; box-shadow: 0 3px 12px rgba(0,0,0,0.04);">
                                <div style="display: flex; align-items: center; margin-bottom: 6px;">
                                    <span style="font-size: 26px; margin-right: 10px;">{$currStepInfo['icon']}</span>
                                    <div>
                                        <span style="font-size: 11px; font-weight: 800; background: {$currStepInfo['color']}; color: #FFFFFF; padding: 3px 10px; border-radius: 999px; text-transform: uppercase;">สถานะล่าสุด (Step {$current_step_num}/4)</span>
                                        <h2 style="margin: 4px 0 0 0; font-size: 16px; font-weight: 800; color: {$currStepInfo['title_color']};">
                                            {$currStepInfo['title']}
                                        </h2>
                                    </div>
                                </div>
                                <p style="margin: 8px 0 0 0; font-size: 13px; color: {$currStepInfo['desc_color']}; line-height: 1.55;">
                                    {$currStepInfo['desc']}
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Admin / Delivery Nanny Note -->
                    {$noteHtml}

                    <!-- 4-Step Timeline Box with Distinct Stage Colors -->
                    <tr>
                        <td style="padding: 10px 25px 20px 25px;">
                            <h3 style="font-size: 14px; font-weight: 800; color: #334155; margin: 0 0 12px 0; text-transform: uppercase;">
                                📍 ขั้นตอนไทม์ไลน์การส่งมอบ (4 Delivery Stages)
                            </h3>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size: 12px;">
HTML;

    foreach ($steps as $sKey => $s) {
        $isPastOrCurrent = $s['num'] <= $current_step_num;
        $isCurrent = $s['num'] === $current_step_num;
        
        $bulletBg = $isCurrent ? $s['color'] : ($isPastOrCurrent ? $s['color'] : '#E2E8F0');
        $bulletColor = $isPastOrCurrent ? '#FFFFFF' : '#94A3B8';
        $iconMark = $isCurrent ? '👉' : ($isPastOrCurrent ? '✓' : $s['num']);
        $textColor = $isCurrent ? $s['title_color'] : ($isPastOrCurrent ? '#1E293B' : '#94A3B8');
        $fontWeight = $isCurrent ? 'bold' : 'normal';
        $badgeBg = $isPastOrCurrent ? $s['bg'] : '#F1F5F9';
        $badgeBorder = $isPastOrCurrent ? $s['border'] : '#E2E8F0';
        $badgeText = $isPastOrCurrent ? $s['title_color'] : '#94A3B8';

        $html .= <<<HTML
                                <tr>
                                    <td width="30" valign="top" style="padding-bottom: 12px;">
                                        <div style="width: 24px; height: 24px; border-radius: 50%; background-color: {$bulletBg}; color: {$bulletColor}; text-align: center; line-height: 24px; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.08);">
                                            {$iconMark}
                                        </div>
                                    </td>
                                    <td valign="top" style="padding-bottom: 12px; color: {$textColor}; font-weight: {$fontWeight};">
                                        <div style="font-size: 13px; font-weight: 800; color: {$textColor};">
                                            {$s['icon']} {$s['title']}
                                        </div>
                                        <div style="display: inline-block; font-size: 10.5px; font-weight: 700; color: {$badgeText}; background: {$badgeBg}; border: 1px solid {$badgeBorder}; padding: 1px 8px; border-radius: 4px; margin-top: 3px;">
                                            {$s['badge']}
                                        </div>
                                    </td>
                                </tr>
HTML;
    }

    $html .= <<<HTML
                            </table>
                        </td>
                    </tr>

                    <!-- Purchased Kittens Showcase Card with Images -->
                    {$petsHtml}

                    <!-- Order Details Card -->
                    <tr>
                        <td style="padding: 0 25px 20px 25px;">
                            <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 16px;">
                                <h4 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 800; color: #1E293B;">
                                    👤 ข้อมูลผู้รับมอบ & นัดหมายส่งมอบ
                                </h4>
                                <table width="100%" border="0" cellspacing="0" cellpadding="4" style="font-size: 12px; color: #475569;">
                                    <tr>
                                        <td width="35%" style="color: #64748B;">🐱 น้องแมว:</td>
                                        <td><strong style="color: #FF6B4A;">{$catNamesStr}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B;">👤 ผู้รับมอบ:</td>
                                        <td><strong>{$customerName}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B;">🚐 วิธีการจัดส่ง:</td>
                                        <td><strong>{$shipping_label}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B;">📍 ที่อยู่ปลายทาง:</td>
                                        <td>{$orderData['delivery_address']}{$gps_html}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B;">📅 นัดหมายวันส่ง:</td>
                                        <td><strong style="color: #047857;">{$orderData['delivery_date']} ({$orderData['delivery_timeslot']})</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <!-- CTA Button -->
                    <tr>
                        <td style="padding: 10px 25px 25px 25px; text-align: center;">
                            <a href="{$homepageUrl}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #FF6B4A 0%, #FF8E72 100%); color: #FFFFFF; font-size: 15px; font-weight: 800; text-decoration: none; padding: 14px 32px; border-radius: 999px; box-shadow: 0 4px 15px rgba(255,107,74,0.35);">
                                🏠 เยี่ยมชมเว็บไซต์ Purrfect Shop บน GitHub Pages 🐾
                            </a>
                            <div style="font-size: 11px; color: #94A3B8; margin-top: 10px;">
                                เข้าชมหน้าแรกของฟาร์มได้ที่: <a href="{$homepageUrl}" target="_blank" style="color: #FF6B4A; text-decoration: underline;">{$homepageUrl}</a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer & Guarantee -->
                    <tr>
                        <td style="background-color: #F1F5F9; padding: 20px 25px; border-top: 1px solid #E2E8F0; text-align: center; font-size: 11px; color: #64748B; line-height: 1.6;">
                            🛡️ <strong>Purrfect 180-Day Guarantee:</strong> รับประกันสุขภาพและสายพันธุ์แท้ 180 วัน<br>
                            📞 สอบถามหรือประสานงานพี่เลี้ยงจัดส่ง: <strong>089-123-4567</strong> • Line: <strong>@purrfect-cattery</strong><br>
                            © 2026 Purrfect Cattery & Boutique. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

    return $html;
}
