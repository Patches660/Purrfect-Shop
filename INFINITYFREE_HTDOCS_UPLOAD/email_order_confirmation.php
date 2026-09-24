<?php
/**
 * email_order_confirmation.php
 * Renders the HTML body for Order Confirmation emails with Product Images.
 * Style: Warm Coral and Cream — "ใบยืนยันคำสั่งซื้อ" theme
 */

if (!function_exists('getOrderProductImageUrl')) {
    function getOrderProductImageUrl(array $item, string $baseUrl = ''): string {
        $img = trim($item['image'] ?? '');
        $id  = trim($item['id'] ?? '');

        // If already full HTTP/HTTPS URL
        if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
            return $img;
        }

        $is_local = empty($baseUrl) || (strpos($baseUrl, 'localhost') !== false || strpos($baseUrl, '127.0.0.1') !== false);

        // Curated high-res Unsplash CDN images matching all cat breeds
        $cdnMap = [
            'cat_british'            => 'https://images.unsplash.com/photo-1592194996308-7b43878e84a6?w=200&auto=format&fit=crop&q=80',
            'cat_persian'            => 'https://images.unsplash.com/photo-1518791841217-8f162f1e1131?w=200&auto=format&fit=crop&q=80',
            'cat_sphynx'             => 'https://images.unsplash.com/photo-1513360309081-38f0762daed1?w=200&auto=format&fit=crop&q=80',
            'cat_siamese'            => 'https://images.unsplash.com/photo-1513245543132-31f507417b26?w=200&auto=format&fit=crop&q=80',
            'cat_mainecoon'          => 'https://images.unsplash.com/photo-1561948955-570b270e7c36?w=200&auto=format&fit=crop&q=80',
            'cat_ragdoll'            => 'https://images.unsplash.com/photo-1543852786-1cf6624b9987?w=200&auto=format&fit=crop&q=80',
            'cat_bengal'             => 'https://images.unsplash.com/photo-1518791841217-8f162f1e1131?w=200&auto=format&fit=crop&q=80',
            'cat_scottish'           => 'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=200&auto=format&fit=crop&q=80',
            'cat_munchkin'           => 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?w=200&auto=format&fit=crop&q=80',
            'cat_russian'            => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200&auto=format&fit=crop&q=80',
            'cat_abyssinian'         => 'https://images.unsplash.com/photo-1548802673-380ab8ebc7b7?w=200&auto=format&fit=crop&q=80',
            'cat_americanshorthair'  => 'https://images.unsplash.com/photo-1573865526739-10659fec78a5?w=200&auto=format&fit=crop&q=80',
            'cat_norwegian'          => 'https://images.unsplash.com/photo-1535930891776-0c2dfb7fda1a?w=200&auto=format&fit=crop&q=80',
            'cat_japanese_bobtail'   => 'https://images.unsplash.com/photo-1561948955-570b270e7c36?w=200&auto=format&fit=crop&q=80',
            'cat_siberian'           => 'https://images.unsplash.com/photo-1535930891776-0c2dfb7fda1a?w=200&auto=format&fit=crop&q=80',
            'cat_turkish_angora'     => 'https://images.unsplash.com/photo-1518791841217-8f162f1e1131?w=200&auto=format&fit=crop&q=80',
            'cat_turkish_van'        => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200&auto=format&fit=crop&q=80',
            'cat_cornish_rex'        => 'https://images.unsplash.com/photo-1513360309081-38f0762daed1?w=200&auto=format&fit=crop&q=80',
            'cat_devon_rex'          => 'https://images.unsplash.com/photo-1513360309081-38f0762daed1?w=200&auto=format&fit=crop&q=80',
            'cat_singapura'          => 'https://images.unsplash.com/photo-1548802673-380ab8ebc7b7?w=200&auto=format&fit=crop&q=80',
            'cat_american_curl'      => 'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=200&auto=format&fit=crop&q=80',
            'cat_bombay'             => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200&auto=format&fit=crop&q=80',
            'cat_burmese'            => 'https://images.unsplash.com/photo-1513245543132-31f507417b26?w=200&auto=format&fit=crop&q=80',
            'cat_chartreux'          => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200&auto=format&fit=crop&q=80',
            'cat_khao_manee'         => 'https://images.unsplash.com/photo-1518791841217-8f162f1e1131?w=200&auto=format&fit=crop&q=80',
            'cat_korat'              => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200&auto=format&fit=crop&q=80',
            'cat_savannah'           => 'https://images.unsplash.com/photo-1518791841217-8f162f1e1131?w=200&auto=format&fit=crop&q=80',
            'cat_egyptian_mau'       => 'https://images.unsplash.com/photo-1518791841217-8f162f1e1131?w=200&auto=format&fit=crop&q=80',
            'cat_scottish_straight'  => 'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=200&auto=format&fit=crop&q=80',
            'cat_somali'             => 'https://images.unsplash.com/photo-1548802673-380ab8ebc7b7?w=200&auto=format&fit=crop&q=80',
            'cat_australian_mist'    => 'https://images.unsplash.com/photo-1592194996308-7b43878e84a6?w=200&auto=format&fit=crop&q=80',
            'cat_kurilian_bobtail'   => 'https://images.unsplash.com/photo-1561948955-570b270e7c36?w=200&auto=format&fit=crop&q=80',
        ];

        $cleanKey = strtolower(str_replace(['.jpg', '.jpeg', '.png', 'assets/images/'], '', $img));
        if (empty($cleanKey)) {
            $cleanKey = strtolower($id);
        }

        if ($is_local) {
            if (isset($cdnMap[$cleanKey])) {
                return $cdnMap[$cleanKey];
            }
            if (isset($cdnMap[$id])) {
                return $cdnMap[$id];
            }
            return 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200&auto=format&fit=crop&q=80';
        }

        if (!empty($img)) {
            $relPath = (strpos($img, 'assets/') === 0) ? $img : 'assets/images/' . ltrim($img, '/');
            return rtrim($baseUrl, '/') . '/' . ltrim($relPath, '/');
        }

        return 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200&auto=format&fit=crop&q=80';
    }
}

function renderOrderConfirmationEmail(
    string $customerName,
    string $customerEmail,
    string $orderId,
    array  $items,
    float  $subtotal,
    float  $discount,
    float  $vat,
    float  $total,
    string $paymentChannel    = '',
    string $deliveryDate      = '',
    string $deliveryAddress   = '',
    string $deliveryTimeslot  = ''
): string {

    $email_base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000');
    $displayName    = !empty($customerName)  ? htmlspecialchars($customerName)  : 'คุณลูกค้า';
    $displayEmail   = htmlspecialchars($customerEmail);
    $displayOrderId = htmlspecialchars($orderId);

    $fSub   = number_format($subtotal,  2);
    $fDisc  = number_format($discount,  2);
    $fVat   = number_format($vat,       2);
    $fTotal = number_format($total,     2);
    $hasDisc = $discount > 0;

    // Thai delivery date
    $deliveryDisplay = '';
    if (!empty($deliveryDate)) {
        $ts = strtotime($deliveryDate);
        $thMonths = ['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
        $d = (int) date('j', $ts);
        $m = (int) date('n', $ts);
        $y = (int) date('Y', $ts) + 543;
        $deliveryDisplay = "$d {$thMonths[$m]} $y";
    }

    // Item rows with Product Images
    $itemRowsHtml = '';
    foreach ($items as $item) {
        $iName   = htmlspecialchars($item['name'] ?? $item['cat_name'] ?? 'น้องแมวสายพันธุ์แท้');
        $iBreed  = htmlspecialchars($item['breed'] ?? '');
        $iGender = htmlspecialchars($item['gender'] ?? '');
        $iAge    = htmlspecialchars($item['age'] ?? '');
        $iQty    = (int)   ($item['qty'] ?? $item['quantity'] ?? 1);
        $iPrice  = (float) ($item['price'] ?? 0);
        $iSub    = number_format($iQty * $iPrice, 2);
        $iUP     = number_format($iPrice, 2);

        $imgUrl = getOrderProductImageUrl($item, $email_base_url);

        $badgeInfo = [];
        if (!empty($iBreed)) $badgeInfo[] = $iBreed;
        if (!empty($iGender)) $badgeInfo[] = $iGender;
        if (!empty($iAge)) $badgeInfo[] = "อายุ $iAge";
        $badgeText = htmlspecialchars(implode(' · ', $badgeInfo));

        $subDetail = !empty($badgeText)
            ? "<div style='font-size:12px;color:#8c6d5e;line-height:1.4;'>{$badgeText}</div>"
            : '';

        $itemRowsHtml .= "
            <tr>
              <td style='padding:12px 14px;border-bottom:1px solid #f0e8e0;vertical-align:middle;'>
                <table cellpadding='0' cellspacing='0' border='0' style='border-collapse:collapse;'>
                  <tr>
                    <td style='width:62px;vertical-align:middle;padding-right:12px;'>
                      <img src='{$imgUrl}' alt='{$iName}' width='56' height='56' style='width:56px;height:56px;border-radius:12px;object-fit:cover;display:block;border:1.5px solid #e8d0c2;box-shadow:0 2px 6px rgba(224,124,90,0.15);'>
                    </td>
                    <td style='vertical-align:middle;'>
                      <div style='font-size:14px;font-weight:700;color:#5c3d2e;line-height:1.3;margin-bottom:3px;'>
                        🐱 {$iName}
                      </div>
                      {$subDetail}
                    </td>
                  </tr>
                </table>
              </td>
              <td style='padding:12px 8px;border-bottom:1px solid #f0e8e0;color:#6b5344;font-size:13px;text-align:center;vertical-align:middle;font-weight:600;'>{$iQty}</td>
              <td style='padding:12px 8px;border-bottom:1px solid #f0e8e0;color:#888;font-size:13px;text-align:right;vertical-align:middle;'>{$iUP} &#3647;</td>
              <td style='padding:12px 14px;border-bottom:1px solid #f0e8e0;color:#e07c5a;font-weight:800;font-size:14px;text-align:right;vertical-align:middle;'>{$iSub} &#3647;</td>
            </tr>";
    }

    $discountRow = $hasDisc ? "
            <tr>
              <td colspan='3' style='padding:10px 14px;color:#4caf50;font-size:13px;text-align:right;font-weight:600;'>&#x1F49A; ส่วนลดสมาชิก VIP</td>
              <td style='padding:10px 14px;color:#4caf50;font-weight:700;font-size:14px;text-align:right;'>- {$fDisc} &#3647;</td>
            </tr>" : '';

    $paymentDisplay = !empty($paymentChannel) ? htmlspecialchars($paymentChannel) : 'ชำระเงินแล้ว';
    $addressDisplay = !empty($deliveryAddress) ? nl2br(htmlspecialchars($deliveryAddress)) : '-';
    $slotDisplay    = !empty($deliveryTimeslot) ? ' &middot; ' . htmlspecialchars($deliveryTimeslot) : '';

    ob_start();
    echo '<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>ยืนยันคำสั่งซื้อ — Purrfect Shop</title></head>
<body style="margin:0;padding:0;background-color:#fff8f4;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;-webkit-font-smoothing:antialiased;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#fff8f4;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 6px 28px rgba(224,124,90,.14);border:1px solid #f5dbcb;">

<!-- Header Banner -->
<tr><td style="background:linear-gradient(135deg,#e07c5a 0%,#f0a07a 50%,#f5c5a3 100%);padding:36px 32px;text-align:center;">
<div style="font-size:46px;margin-bottom:6px;">&#x1F6D2;&#x2705;</div>
<h1 style="margin:0;color:#ffffff;font-size:25px;font-weight:800;letter-spacing:0.5px;">ยืนยันคำสั่งซื้อสำเร็จ!</h1>
<p style="margin:8px 0 0;color:rgba(255,255,255,.95);font-size:15px;font-weight:500;">ขอบคุณที่รับเลี้ยงน้องแมวกับ Purrfect Shop นะครับ &#x1F43E;</p>
</td></tr>

<!-- Order ID Badge -->
<tr><td style="background:#fdf0e8;padding:16px 32px;text-align:center;border-bottom:2px dashed #f0c4a0;">
<span style="font-size:11px;color:#b87a5a;letter-spacing:2px;text-transform:uppercase;font-weight:700;">หมายเลขคำสั่งซื้อ (ORDER ID)</span><br>
<span style="font-size:22px;font-weight:800;color:#e07c5a;letter-spacing:3px;">' . $displayOrderId . '</span>
</td></tr>

<!-- Greeting -->
<tr><td style="padding:26px 32px 14px;">
<p style="margin:0;font-size:15px;color:#5c3d2e;line-height:1.7;">
สวัสดีครับ คุณ <strong>' . $displayName . '</strong> &#x1F60A;<br>
เราได้รับคำสั่งซื้อของคุณเรียบร้อยแล้ว รายการน้องแมวที่คุณเลือกรับเลี้ยงมีดังนี้ครับ:
</p></td></tr>

<!-- Items Table with Product Images -->
<tr><td style="padding:0 28px 24px;">
<table width="100%" cellpadding="0" cellspacing="0" style="border:1.5px solid #f0e0d6;border-radius:12px;overflow:hidden;background:#ffffff;">
<tr style="background:#fdf0e8;border-bottom:1.5px solid #f0e0d6;">
  <th style="padding:12px 14px;text-align:left;color:#b87a5a;font-size:13px;font-weight:700;">สินค้าน้องแมว</th>
  <th style="padding:12px 8px;text-align:center;color:#b87a5a;font-size:13px;font-weight:700;width:55px;">จำนวน</th>
  <th style="padding:12px 8px;text-align:right;color:#b87a5a;font-size:13px;font-weight:700;width:90px;">ราคา/ตัว</th>
  <th style="padding:12px 14px;text-align:right;color:#b87a5a;font-size:13px;font-weight:700;width:95px;">รวม</th>
</tr>' . $itemRowsHtml . $discountRow . '
<tr style="background:#fffcf9;">
  <td colspan="3" style="padding:8px 14px;color:#888;font-size:13px;text-align:right;">ภาษีมูลค่าเพิ่ม (VAT 7%)</td>
  <td style="padding:8px 14px;color:#888;font-size:13px;text-align:right;">' . $fVat . ' &#3647;</td>
</tr>
<tr style="background:#fff0e8;">
  <td colspan="3" style="padding:14px 14px;color:#5c3d2e;font-size:15px;font-weight:800;text-align:right;border-top:2px solid #f0c4a0;">ยอดชำระสุทธิ</td>
  <td style="padding:14px 14px;color:#e07c5a;font-size:18px;font-weight:900;text-align:right;border-top:2px solid #f0c4a0;">' . $fTotal . ' &#3647;</td>
</tr>
</table></td></tr>';

    // Delivery Info
    echo '<tr><td style="padding:0 28px 24px;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#fdf5f0;border-radius:12px;border-left:4px solid #e07c5a;border:1px solid #f5dfd2;">
<tr><td style="padding:18px 20px;">
<p style="margin:0 0 12px;font-size:14px;font-weight:800;color:#b87a5a;letter-spacing:0.5px;">&#x1F4E6; ข้อมูลการจัดส่งและชำระเงิน</p>
<table width="100%" cellpadding="0" cellspacing="0">';
    if (!empty($deliveryDisplay)) {
        echo '<tr>
          <td style="padding:5px 0;color:#8c6d5e;font-size:13px;width:120px;font-weight:600;">&#x1F4C5; วันที่จัดส่ง:</td>
          <td style="padding:5px 0;color:#5c3d2e;font-size:13px;font-weight:700;">' . $deliveryDisplay . $slotDisplay . '</td>
        </tr>';
    }
    if (!empty($deliveryAddress)) {
        echo '<tr>
          <td style="padding:5px 0;color:#8c6d5e;font-size:13px;vertical-align:top;font-weight:600;">&#x1F4CD; สถานที่จัดส่ง:</td>
          <td style="padding:5px 0;color:#5c3d2e;font-size:13px;line-height:1.5;">' . $addressDisplay . '</td>
        </tr>';
    }
    echo '<tr>
      <td style="padding:5px 0;color:#8c6d5e;font-size:13px;font-weight:600;">&#x1F4B3; ช่องทางชำระเงิน:</td>
      <td style="padding:5px 0;color:#5c3d2e;font-size:13px;font-weight:700;">' . $paymentDisplay . '</td>
    </tr>
    </table></td></tr></table></td></tr>';

    // Tip Box
    echo '<tr><td style="padding:0 28px 24px;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#fff9eb;border-radius:12px;border-left:4px solid #f59e0b;border:1px solid #fee6b6;">
<tr><td style="padding:16px 18px;">
<p style="margin:0;font-size:13px;color:#92400e;line-height:1.6;">
&#x1F43E; <strong>เตรียมพร้อมรับน้องแมว:</strong> กรุณาเตรียมกระบะทราย อาหาร และที่นอนเพื่อต้อนรับน้องๆ ล่วงหน้านะครับ ทีมงาน Purrfect จะติดต่อยืนยันก่อนวันส่งมอบอีกครั้งค่ะ &#x1F63A;
</p></td></tr></table></td></tr>';

    // CTA Button
    echo '<tr><td style="padding:0 28px 30px;text-align:center;">
<a href="' . htmlspecialchars($email_base_url) . '" style="display:inline-block;background:linear-gradient(135deg,#e07c5a,#f0a07a);color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:14px 36px;border-radius:50px;letter-spacing:.5px;box-shadow:0 4px 16px rgba(224,124,90,.35);">
&#x1F3E0; กลับหน้าร้าน Purrfect Shop
</a></td></tr>';

    // Footer
    echo '<tr><td style="background:#fdf0e8;padding:22px 28px;text-align:center;border-top:1px solid #f0d0b8;">
<p style="margin:0 0 6px;font-size:22px;">&#x1F43E;</p>
<p style="margin:0 0 4px;font-size:13px;color:#b87a5a;font-weight:700;">Purrfect Cattery &amp; Boutique</p>
<p style="margin:0;font-size:12px;color:#bba090;">อีเมลนี้ถูกส่งอัตโนมัติจากระบบร้านค้า &middot; กรุณาอย่าตอบกลับอีเมลนี้</p>
<p style="margin:6px 0 0;font-size:12px;color:#bba090;">ส่งถึง: ' . $displayEmail . ' &middot; หมายเลขออเดอร์: ' . $displayOrderId . '</p>
</td></tr>

</table></td></tr></table></body></html>';
    return ob_get_clean();
}
