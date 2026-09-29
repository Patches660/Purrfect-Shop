<?php
/**
 * LINE OA Webhook Chatbot with Quick Reply Buttons
 * Purrfect Cat Shop
 */

// 1. Configuration: Put your Channel Access Token & Channel Secret from LINE Developers Console
$channelAccessToken = 'YOUR_CHANNEL_ACCESS_TOKEN'; // ใส่ Channel Access Token จาก LINE Developers ที่นี่
$channelSecret = 'YOUR_CHANNEL_SECRET';             // ใส่ Channel Secret ที่นี่

// 2. Receive incoming webhook payload
$content = file_get_contents('php://input');
$events = json_decode($content, true);

if (!empty($events['events'])) {
    foreach ($events['events'] as $event) {
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            $replyToken = $event['replyToken'];
            $userMsg = trim($event['message']['text']);

            handleUserMessage($replyToken, $userMsg, $channelAccessToken);
        }
    }
}
http_response_code(200);
echo "OK";

/**
 * Handle incoming user message and reply with Quick Reply buttons
 */
function handleUserMessage($replyToken, $userMsg, $token) {
    // Quick Reply Items (ปุ่มกดแบบในรูป)
    $quickReplyItems = [
        [
            'type' => 'action',
            'action' => [
                'type' => 'message',
                'label' => '🐱 ดูน้องแมว',
                'text' => 'ดูน้องแมว'
            ]
        ],
        [
            'type' => 'action',
            'action' => [
                'type' => 'message',
                'label' => '🛒 สั่งซื้อสินค้า',
                'text' => 'สั่งซื้อสินค้า'
            ]
        ],
        [
            'type' => 'action',
            'action' => [
                'type' => 'message',
                'label' => '💰 โปรโมชั่น',
                'text' => 'โปรโมชั่น'
            ]
        ],
        [
            'type' => 'action',
            'action' => [
                'type' => 'message',
                'label' => '🚚 ข้อมูลจัดส่ง',
                'text' => 'ข้อมูลจัดส่ง'
            ]
        ],
        [
            'type' => 'action',
            'action' => [
                'type' => 'message',
                'label' => '📍 หน้าร้าน/เวลา',
                'text' => 'หน้าร้าน'
            ]
        ],
        [
            'type' => 'action',
            'action' => [
                'type' => 'message',
                'label' => '💳 เลขบัญชี',
                'text' => 'เลขบัญชี'
            ]
        ]
    ];

    // Response logic based on user message
    if (in_array($userMsg, ['เมนูหลัก', 'menu', 'Menu', 'เริ่มต้น', 'ช่วยเหลือ'])) {
        $replyText = "🔄 กลับสู่เมนูหลักเรียบร้อยแล้วค่ะ! เลือกหัวข้อที่สนใจได้เลยนะคะ 👇🐾";
    } 
    elseif (in_array($userMsg, ['โปรโมชั่น', 'โปร', 'promotion', 'คูปอง'])) {
        $replyText = "🎉 โปรโมชั่นพิเศษประจำเดือนนี้!\n------------------------------------\n✨ จองน้องแมววันนี้ รับฟรี Starter Kit มูลค่า 2,500 บาท!\n✨ สมาชิก LINE รับคูปองส่วนลด 25% (โค้ด: PURR25NEW)\n✨ บริการจัดส่งฟรีกรุงเทพฯ และปริมณฑล 🚚💨";
    }
    elseif (in_array($userMsg, ['ดูน้องแมว', 'สินค้า', 'แมว', 'ราคา'])) {
        $replyText = "🐾 รวมน้องแมวสายพันธุ์แท้ 100% พร้อมย้ายบ้าน:\n• บริติช ช็อตแฮร์ (18,000.-)\n• เปอร์เซีย หน้าหวาน (16,500.-)\n• แร็กดอลล์ ตาสีฟ้า (29,000.-)\n• มันช์กิ้น ขาสั้น (24,000.-)\n• เบงกอล ลายเสือดาว (26,000.-)\n\n🔗 ดูรูปและประวัติน้องแมวทั้งหมด: https://patches660.github.io/Purrfect-Shop/products.html";
    }
    elseif (in_array($userMsg, ['ข้อมูลจัดส่ง', 'การจัดส่ง', 'ส่งของ'])) {
        $replyText = "🚚 ข้อมูลการจัดส่งน้องแมวและสินค้า:\n• จัดส่งโดยรถยนต์ควบคุมอุณหภูมิ มีพี่เลี้ยงดูแลตลอดทาง\n• กรุงเทพฯ-ปริมณฑล ส่งฟรีถึงหน้าบ้านใน 24 ชม.\n• ต่างจังหวัด จัดส่งทางเครื่องบิน (รับที่สนามบิน) หรือ Pet Taxi ปลอดภัย 100%";
    }
    elseif (in_array($userMsg, ['หน้าร้าน', 'เวลา', 'ที่ตั้ง', 'แผนที่'])) {
        $replyText = "📍 ที่ตั้งหน้าร้าน Purrfect Shop:\n🏢 123/45 ซอยสุขุมวิท 55 แขวงคลองตันเหนือ เขตวัฒนา กทม.\n⏰ เปิดบริการทุกวัน: 10:00 - 20:00 น.\n📞 โทร: 089-123-4567";
    }
    elseif (in_array($userMsg, ['เลขบัญชี', 'ชำระเงิน', 'โอนเงิน', 'จ่ายเงิน'])) {
        $replyText = "💳 ช่องทางการชำระเงิน:\n🏦 ธนาคารกสิกรไทย (KBANK)\n• เลขที่บัญชี: 123-4-56789-0\n• ชื่อบัญชี: บจก. เพอร์เฟกต์ ช็อป แคทเทอรี่\n\n✓ รองรับบัตรเครดิต & ผ่อนชำระ 0% สูงสุด 10 เดือนค่ะ";
    }
    elseif (in_array($userMsg, ['สั่งซื้อสินค้า', 'สั่งซื้อ', 'จองแมว'])) {
        $replyText = "🛒 สั่งซื้อสินค้า & จองน้องแมว:\nคุณลูกค้าสามารถเลือกชมสินค้าและทำรายการผ่านหน้าเว็บได้ทันทีที่:\n🔗 https://patches660.github.io/Purrfect-Shop/cart.html\n\nหรือแจ้งชื่อน้องแมวที่สนใจในแชทนี้ได้เลยนะคะ แอดมินพร้อมดูแลค่ะ 💖";
    }
    else {
        $replyText = "ยินดีต้อนรับสู่ Purrfect Shop ค่ะ 🐾\nสามารถแตะเลือกเมนูด้านล่างนี้เพื่อดูข้อมูลได้เลยนะคะ 👇";
    }

    // Build payload with text message AND quick reply buttons
    $payload = [
        'replyToken' => $replyToken,
        'messages' => [
            [
                'type' => 'text',
                'text' => $replyText,
                'quickReply' => [
                    'items' => $quickReplyItems
                ]
            ]
        ]
    ];

    sendLineReply($payload, $token);
}

/**
 * Send HTTP POST to LINE Messaging API
 */
function sendLineReply($payload, $token) {
    $ch = curl_init('https://api.line.me/v2/bot/message/reply');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charSet=UTF-8',
        'Authorization: Bearer ' . $token
    ]);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
?>
