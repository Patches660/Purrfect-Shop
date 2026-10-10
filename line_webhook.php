<?php
/**
 * line_webhook.php - LINE OA Smart Bot Webhook with Custom Styled Menus & Dify AI Knowledge Base
 * Purrfect Shop Cattery & Boutique
 */
date_default_timezone_set('Asia/Bangkok');
header('Content-Type: application/json; charset=utf-8');

// 1. ตั้งค่า LINE Developers & Dify API (โหลดจาก config_secrets.php หรือ Environment Variables)
$secrets = [];
if (file_exists(__DIR__ . '/config_secrets.php')) {
    $secrets = require __DIR__ . '/config_secrets.php';
}

$channelAccessToken = getenv('LINE_CHANNEL_ACCESS_TOKEN') ?: ($secrets['line_channel_access_token'] ?? '');
$channelSecret      = getenv('LINE_CHANNEL_SECRET')       ?: ($secrets['line_channel_secret'] ?? '');
$difyApiKey         = getenv('DIFY_API_KEY')              ?: ($secrets['dify_api_key'] ?? '');

// 2. รับข้อมูล Event จาก LINE Webhook
$content = file_get_contents('php://input');

// บันทึก Log สำหรับตรวจสอบ (Debug)
@file_put_contents(__DIR__ . '/line_webhook_log.txt', date('Y-m-d H:i:s') . " - Incoming: " . $content . "\n", FILE_APPEND);

$events = json_decode($content, true);

if (!empty($events['events'])) {
    foreach ($events['events'] as $event) {
        if ($event['type'] === 'message' && ($event['message']['type'] ?? '') === 'text') {
            $replyToken = $event['replyToken'] ?? '';
            $userMsg = trim($event['message']['text'] ?? '');
            $userId = $event['source']['userId'] ?? '';

            if (!empty($userMsg) && (!empty($replyToken) || !empty($userId))) {
                handleLineBotMessage($replyToken, $userMsg, $userId, $channelAccessToken, $difyApiKey);
            }
        }
    }
}

http_response_code(200);
echo "OK";
exit;

/**
 * ฟังก์ชันดึงชื่อ Nickname / DisplayName ของผู้ใช้งานจาก LINE
 */
function getLineUserProfile($userId, $token) {
    if (empty($userId)) return null;
    $url = "https://api.line.me/v2/bot/profile/" . urlencode($userId);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode === 200) {
        $data = json_decode($res, true);
        return $data['displayName'] ?? null;
    }
    return null;
}

/**
 * ฟังก์ชันสร้างชุดปุ่ม Quick Reply หมวด 1 และ หมวด 2
 */
function getQuickReplies($category = 1) {
    if ($category == 2) {
        // หมวด 2: บริการและเครื่องมือเสริม
        return [
            ['type' => 'action', 'action' => ['type' => 'message', 'label' => '⭐ เมนูหลัก', 'text' => '⭐ เมนูหลัก']],
            ['type' => 'action', 'action' => ['type' => 'message', 'label' => '💉 วัคซีน', 'text' => '💉 วัคซีน']],
            ['type' => 'action', 'action' => ['type' => 'message', 'label' => '🎡 วงล้อ', 'text' => '🎡 วงล้อ']],
            ['type' => 'action', 'action' => ['type' => 'message', 'label' => '📜 ใบเพ็ดดีกรี', 'text' => '📜 ใบเพ็ดดีกรี']],
            ['type' => 'action', 'action' => ['type' => 'message', 'label' => '🎯 จองคิวนัดดูตัว', 'text' => '🎯 จองคิวนัดดูตัว']],
            ['type' => 'action', 'action' => ['type' => 'message', 'label' => '🥇 บัตรสะสมแต้ม', 'text' => '🥇 บัตรสะสมแต้ม']],
            ['type' => 'action', 'action' => ['type' => 'message', 'label' => '🤔 คำนวณค่าเลี้ยงแมว', 'text' => '🤔 คำนวณค่าเลี้ยงแมว']]
        ];
    }

    // หมวด 1: เมนูหลัก (ค่าเริ่มต้น)
    return [
        ['type' => 'action', 'action' => ['type' => 'message', 'label' => '🐱 แนะนำแมว', 'text' => '🐱 แนะนำแมว']],
        ['type' => 'action', 'action' => ['type' => 'message', 'label' => '🛒 สั่งซื้อสินค้า', 'text' => '🛒 สั่งซื้อสินค้า']],
        ['type' => 'action', 'action' => ['type' => 'message', 'label' => '💰 โปรโมชั่น', 'text' => '💰 โปรโมชั่น']],
        ['type' => 'action', 'action' => ['type' => 'message', 'label' => '📋 สมัครสมาชิก', 'text' => '📋 สมัครสมาชิก']],
        ['type' => 'action', 'action' => ['type' => 'message', 'label' => '🎫 คูปอง', 'text' => '🎫 คูปอง']],
        ['type' => 'action', 'action' => ['type' => 'message', 'label' => '📞 ติดต่อเรา', 'text' => '📞 ติดต่อเรา']],
        ['type' => 'action', 'action' => ['type' => 'message', 'label' => '💵 ชำระเงิน', 'text' => '💵 ชำระเงิน']],
        ['type' => 'action', 'action' => ['type' => 'message', 'label' => '🗓️ แบบสอบถาม', 'text' => '🗓️ แบบสอบถาม']],
        ['type' => 'action', 'action' => ['type' => 'message', 'label' => '🚚 ติดตามสินค้า', 'text' => '🚚 ติดตามสินค้า']],
        ['type' => 'action', 'action' => ['type' => 'message', 'label' => '📚 เมนูเพิ่มเติม', 'text' => '📚 เมนูเพิ่มเติม']]
    ];
}

/**
 * ฟังก์ชันสร้างการ์ดเมสเสจแคตตาล็อกน้องแมว 9 สายพันธุ์ + การ์ดปิดท้าย (Image Carousel Template)
 */
function getCatCardCarouselMessage() {
    $baseUrl = 'https://raw.githubusercontent.com/Patches660/Purrfect-Shop/main/docs/LINE_ASSETS/3_CARD_MESSAGE/RATIO_1.11_1';
    $siteUrl = 'https://patches660.github.io/Purrfect-Shop';

    return [
        'type' => 'template',
        'altText' => '🐱 แคตตาล็อกน้องแมว Purrfect Shop สายพันธุ์แท้ 100%',
        'template' => [
            'type' => 'image_carousel',
            'columns' => [
                [
                    'imageUrl' => "{$baseUrl}/card_1_british_1110x1000_ratio1.11.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'จองน้องแมว',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_british"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/card_2_scottish_1110x1000_ratio1.11.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'จองน้องแมว',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_scottish"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/card_3_mainecoon_1110x1000_ratio1.11.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'จองน้องแมว',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_mainecoon"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/card_5_persian_1110x1000_ratio1.11.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'จองน้องแมว',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_persian"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/card_6_ragdoll_1110x1000_ratio1.11.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'จองน้องแมว',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_ragdoll"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/card_7_munchkin_1110x1000_ratio1.11.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'จองน้องแมว',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_munchkin"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/card_8_bengal_1110x1000_ratio1.11.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'จองน้องแมว',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_bengal"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/card_9_sphynx_1110x1000_ratio1.11.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'จองน้องแมว',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_sphynx"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/card_10_siamese_1110x1000_ratio1.11.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'จองน้องแมว',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_siamese"
                    ]
                ],
                [
                    'imageUrl' => 'https://raw.githubusercontent.com/Patches660/Purrfect-Shop/main/docs/LINE_ASSETS/4_COUPON/coupon_4_toys_lifestyle_25pct_640x640.png',
                    'action' => [
                        'type' => 'uri',
                        'label' => 'COUPON 25%',
                        'uri' => 'https://lin.ee/ncuvc7i'
                    ]
                ]
            ]
        ]
    ];
}

/**
 * ฟังก์ชันสร้างชุดริชเมสเสจโปรโมชั่น 5 แบนเนอร์ (Image Carousel Template)
 */
function getPromotionRichCarouselMessage() {
    $baseUrl = 'https://raw.githubusercontent.com/Patches660/Purrfect-Shop/main/docs/LINE_ASSETS/1_RICH_MESSAGE';
    $siteUrl = 'https://patches660.github.io/Purrfect-Shop';

    return [
        'type' => 'template',
        'altText' => '🎁 รวมโปรโมชั่น & ริชเมสเสจดีลลับสุดคุ้ม Purrfect Shop',
        'template' => [
            'type' => 'image_carousel',
            'columns' => [
                [
                    'imageUrl' => "{$baseUrl}/rich_message_1040x1040_square.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'รับโปร 10%',
                        'uri' => "{$siteUrl}/welcome_deal.html"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/rich_message_persian_1040x1040.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'โปรเปอร์เซีย',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_persian"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/rich_message_ragdoll_1040x1040.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'โปรแร็กดอลล์',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_ragdoll"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/rich_message_munchkin_1040x1040.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'โปรมันช์กิ้น',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_munchkin"
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/rich_message_bengal_1040x1040.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'โปรเบงกอล',
                        'uri' => "{$siteUrl}/cat_detail.html?id=cat_bengal"
                    ]
                ]
            ]
        ]
    ];
}

/**
 * ฟังก์ชันสร้างชุดคูปองส่วนลด 5 ใบ (Image Carousel Template)
 */
function getCouponCarouselMessage() {
    $baseUrl = 'https://raw.githubusercontent.com/Patches660/Purrfect-Shop/main/docs/LINE_ASSETS/4_COUPON';

    return [
        'type' => 'template',
        'altText' => '🎫 คลังคูปองส่วนลด 25% Purrfect Shop',
        'template' => [
            'type' => 'image_carousel',
            'columns' => [
                [
                    'imageUrl' => "{$baseUrl}/coupon_5_flash_sale_25pct_640x640.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'รับคูปอง 25%',
                        'uri' => 'https://lin.ee/OLStfcg'
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/coupon_4_toys_lifestyle_25pct_640x640.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'รับคูปอง 25%',
                        'uri' => 'https://lin.ee/ncuvc7i'
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/coupon_3_food_treats_25pct_640x640.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'รับคูปอง 25%',
                        'uri' => 'https://lin.ee/77sr9BY'
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/coupon_2_adoption_25pct_640x640.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'รับคูปอง 25%',
                        'uri' => 'https://lin.ee/6zBQFW9'
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/coupon_1_welcome_25pct_640x640.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'รับคูปอง 25%',
                        'uri' => 'https://lin.ee/zlfniRI'
                    ]
                ]
            ]
        ]
    ];
}

/**
 * ฟังก์ชันสร้างชุดบัตรสะสมแต้มและบัตรแลกของรางวัล (Image Carousel Template)
 */
function getRewardCardCarouselMessage() {
    $baseUrl = 'https://raw.githubusercontent.com/Patches660/Purrfect-Shop/main/docs/LINE_ASSETS/5_REWARD_CARD';
    $rewardUrl = 'https://u.lin.ee/PHZxZGW';

    return [
        'type' => 'template',
        'altText' => '💳 บัตรสะสมแต้ม Purrfect Paw Points & ของรางวัล',
        'template' => [
            'type' => 'image_carousel',
            'columns' => [
                [
                    'imageUrl' => "{$baseUrl}/reward_voucher_minor_discount10_640x640.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'เปิดบัตรแต้ม',
                        'uri' => $rewardUrl
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/reward_voucher_major_starter_kit_640x640.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'Starter Kit',
                        'uri' => $rewardUrl
                    ]
                ],
                [
                    'imageUrl' => "{$baseUrl}/reward_voucher_major_discount45_640x640.png",
                    'action' => [
                        'type' => 'uri',
                        'label' => 'Grand Reward',
                        'uri' => $rewardUrl
                    ]
                ]
            ]
        ]
    ];
}

/**
 * ฟังก์ชันสร้างการ์ดแบบสอบถาม LINE Survey (Buttons Template)
 */
function getSurveyCardMessage($surveyUrl = 'https://patches660.github.io/Purrfect-Shop/quiz.html') {
    $imageUrl = 'https://raw.githubusercontent.com/Patches660/Purrfect-Shop/main/docs/LINE_ASSETS/6_SURVEY/line_survey_cover_780x510.png';

    return [
        'type' => 'template',
        'altText' => '📋 แบบสำรวจความต้องการรับเลี้ยงแมว Purrfect Shop',
        'template' => [
            'type' => 'buttons',
            'thumbnailImageUrl' => $imageUrl,
            'imageAspectRatio' => 'rectangle',
            'imageSize' => 'cover',
            'title' => 'แบบสำรวจความต้องการรับเลี้ยงแมว',
            'text' => "ตอบแบบสอบถามได้ถึง:\n12/10/2026 23:59",
            'actions' => [
                [
                    'type' => 'uri',
                    'label' => 'ตอบแบบสอบถาม',
                    'uri' => $surveyUrl
                ]
            ]
        ]
    ];
}

/**
 * ฟังก์ชันค้นหาข้อมูลความรู้ 78 ข้อใน Knowledge Base
 */
function searchLocalKnowledge($query) {
    $kb_file = __DIR__ . '/chatbot_knowledge_base.json';
    if (!file_exists($kb_file)) return null;
    $kb = json_decode(file_get_contents($kb_file), true);
    if (!is_array($kb)) return null;

    $q_lower = mb_strtolower(trim($query), 'UTF-8');
    $stopwords = ['ไหม', 'มั้ย', 'ได้', 'หรือ', 'และ', 'กับ', 'ให้', 'ของ', 'ที่', 'ใน', 'การ', 'ความ', 'ทาง', 'เป็น', 'อยู่', 'ช่วย', 'หน่อย', 'ครับ', 'ค่ะ', 'นะ', 'คะ', 'กี่', 'ตัว', 'ทำ', 'ยัง', 'ไง', 'บ้าง', 'มี', 'ถ้า', 'หาก', 'อยาก', 'จะ', 'ขอ'];
    $best_score = 0;
    $best_item = null;

    foreach ($kb as $item) {
        $score = 0;
        $q_text = mb_strtolower($item['question'] ?? '', 'UTF-8');
        $ans_text = mb_strtolower($item['answer'] ?? '', 'UTF-8');
        $tags = array_map(function($t) { return mb_strtolower($t, 'UTF-8'); }, $item['tags'] ?? []);

        foreach ($tags as $tag) {
            if (!empty($tag)) {
                if ($tag === $q_lower) $score += 80;
                elseif (mb_strpos($q_lower, $tag) !== false) $score += 40 + (mb_strlen($tag, 'UTF-8') * 5);
                elseif (mb_strpos($tag, $q_lower) !== false && mb_strlen($q_lower, 'UTF-8') >= 3) $score += 30;
            }
        }

        if (mb_strpos($q_text, $q_lower) !== false || mb_strpos($q_lower, $q_text) !== false) {
            $score += 60;
        }

        $len = mb_strlen($q_lower, 'UTF-8');
        for ($w = 3; $w <= min(6, $len); $w++) {
            for ($i = 0; $i <= $len - $w; $i++) {
                $sub = mb_substr($q_lower, $i, $w, 'UTF-8');
                if (in_array($sub, $stopwords)) continue;
                if (mb_strpos($q_text, $sub) !== false) $score += $w * 3;
                if (mb_strpos($ans_text, $sub) !== false) $score += $w;
            }
        }

        if ($score > $best_score) {
            $best_score = $score;
            $best_item = $item;
        }
    }

    if ($best_score >= 20 && $best_item) {
        return $best_item['answer'] ?? null;
    }
    return null;
}

/**
 * ฟังก์ชันประมวลผลข้อความและตอบกลับ LINE
 */
function handleLineBotMessage($replyToken, $userMsg, $userId, $channelToken, $difyKey = '') {
    $activeCategory = 1;
    $replyText = "";

    // 1. ตรวจสอบการกดสลับหน้าเมนู
    if (in_array($userMsg, ['เมนูเพิ่มเติม', '📚 เมนูเพิ่มเติม', 'หมวด 2', 'หมวด2', 'บริการเพิ่มเติม', 'หน้า 2'])) {
        $activeCategory = 2;
        $replyText = "⎛⎝ ≽  >  ⩊   < ≼ ⎠⎞\n\n" .
                     "ยินดีต้อนรับสู่เมนูบริการพิเศษค่ะ!\n" .
                     "แตะปุ่มลัดด้านล่างเพื่อเลือกดูข้อมูลที่ต้องการได้ทันทีนะคะ\n\n" .
                     "⭐ เมนูหลัก ➡️ กลับสู่หน้าแรก & แคตตาล็อกน้องแมว\n\n" .
                     "💉 วัคซีน ➡️ สมุดวัคซีนดิจิทัล & ระบบแจ้งเตือนนัดหมายแพทย์\n" .
                     "🎡 วงล้อ ➡️ หมุนลุ้นรับคูปองส่วนลด 2,500.- & Starter Kit ฟรี\n" .
                     "📜 ใบเพ็ดดีกรี ➡️ ใบเพ็ดดีกรีแท้ & การันตีตรวจโรคพันธุกรรม 100%\n" .
                     "🎯 จองคิวนัดดูตัว ➡️ นัดหมายเข้าชมฟาร์ม & Video Call ตัวจริง\n" .
                     "🥇 บัตรสะสมแต้ม ➡️ เช็คคะแนน Paw Points & แลกรับของรางวัลพรีเมียม\n" .
                     "🤔 คำนวณค่าเลี้ยงแมว ➡️ วางแผนงบอาหาร ทราย & ค่าใช้จ่ายรายเดือน";
    }
    elseif (in_array($userMsg, ['เมนูหลัก', '⭐ เมนูหลัก', 'หมวด 1', 'หมวด1', 'หน้า 1', 'กลับ'])) {
        $activeCategory = 1;
        $replyText = "⚞^◕⩊◕^⚟\n\n" .
                     "กลับสู่เมนูหลักเรียบร้อยแล้วค่ะ!\n" .
                     "แตะปุ่มลัดด้านล่างเพื่อเลือกดูข้อมูลที่ต้องการได้ทันทีนะคะ 👇✨\n\n" .
                     "🐱 แนะนำแมว ➡️ ค้นหาน้องแมวสายพันธุ์ที่เหมาะกับคุณ\n" .
                     "🛒 สั่งซื้อสินค้า ➡️ จองน้องแมว\n" .
                     "💰 โปรโมชั่น ➡️ รวมดีลเด็ด & ของแถมประจำเดือน\n" .
                     "📋 สมัครสมาชิก ➡️ ลงทะเบียนรับสิทธิพิเศษ & ส่วนลด\n" .
                     "🎫 คูปอง ➡️ กดรับโค้ดส่วนลด 10% - 25%\n" .
                     "📞 ติดต่อเรา ➡️ คุยกับแอดมิน เบอร์โทร & แผนที่ร้าน\n" .
                     "💵 ชำระเงิน ➡️ เลขบัญชีธนาคาร & ผ่อนชำระ 0%\n" .
                     "🗓️ แบบสอบถาม ➡️ ทำแบบทดสอบค้นหาน้องแมวสายพันธุ์ที่ใช่สำหรับคุณ\n" .
                     "🚚 ติดตามสินค้า ➡️ ตรวจสอบสถานะการจัดส่งสด & พิกัดรถส่งน้องแมว\n\n" .
                     "📚 เมนูเพิ่มเติม ➡️ รวมคำถามที่พบบ่อย & ช่องทางติดต่อฟาร์ม\n\n\n" .
                     "👇 แตะเลือกหัวข้อที่สนใจด้านล่างนี้ได้เลยค่ะ 🐾";
    }
    // 2. คำทักทาย (ดึง Nickname จาก LINE)
    elseif (in_array(mb_strtolower($userMsg, 'UTF-8'), ['สวัสดี', 'สวัสดีครับ', 'สวัสดีค่ะ', 'หวัดดี', 'หวัดดีครับ', 'หวัดดีค่ะ', 'hello', 'hi', 'hey', 'start'])) {
        $nickname = getLineUserProfile($userId, $channelToken);
        $nameText = $nickname ? "คุณ " . $nickname : "คุณลูกค้า";
        $activeCategory = 1;
        $replyText = "สวัสดีครับ{$nameText}! 🐾 ยินดีต้อนรับสู่ Purrfect Shop ค่ะ\n" .
                     "ฟาร์มแมวสายพันธุ์แท้ 100% มีใบเพ็ดดีกรี และบริการจัดส่ง Pet Taxi ปลอดภัยทั่วไทย\n\n" .
                     "⚞^◕⩊◕^⚟ สามารถแตะปุ่มลัดด้านล่าง หรือพิมพ์สอบถามน้องแมวได้เลยนะคะ 👇✨";
    }
    // 3. คำสั่งเฉพาะตามปุ่ม หมวด 1
    elseif (in_array($userMsg, ['แนะนำแมว', '🐱 แนะนำแมว', 'แนะนำน้องแมว'])) {
        $activeCategory = 1;
        $replyText = "🌟 ไม่แน่ใจว่าจะเลี้ยงน้องแมวพันธุ์ไหนดี? 🐱💭\n\n" .
                     "ลองทำแบบทดสอบ \"Smart Cat Matcher\" ระบบประมวลผล 6 มิติที่จะช่วยค้นหาสายพันธุ์ที่ตรงกับไลฟ์สไตล์ของคุณมากที่สุด! (เช่น เลี้ยงในคอนโด, ขนไม่ร่วง, รักสงบ หรือขี้อ้อน)\n\n" .
                     "👇 ทำแบบทดสอบฟรี ใช้เวลาเพียง 30 วินาที:\n" .
                     "🔗 ทำแบบทดสอบ: https://patches660.github.io/Purrfect-Shop/recommend.html";
    }
    elseif (in_array($userMsg, ['สั่งซื้อสินค้า', '🛒 สั่งซื้อสินค้า', 'แคตตาล็อกแมว', 'ดูแมว', 'น้องแมว'])) {
        $activeCategory = 1;
        $customMessages = [
            [
                'type' => 'text',
                'text' => "🐾 รวมน้องแมวสายพันธุ์แท้ & สินค้าพรีเมียม Purrfect Shop 🐱✨\n\n" .
                          "ยินดีต้อนรับค่ะคุณลูกค้า! ร้านเรามีน้องแมวสายพันธุ์แท้ 100% พร้อมใบเพ็ดดีกรีและตรวจสุขภาพครบถ้วน\n\n\n" .
                          "👇 เลือกชมน้องแมวตัวจริง & ดูค่าสินสอดได้ที่นี่เลยค่ะ:\n" .
                          "🔗 ลิงก์หน้าร้าน: https://patches660.github.io/Purrfect-Shop/products.html"
            ],
            [
                'type' => 'text',
                'text' => "🏆 รวมน้องแมวทั้ง 9 สายพันธุ์ยอดนิยมในร้าน:\n" .
                          "🐱 1. British Shorthair (บริติช ช็อตแฮร์) - ขนแน่นนุ่มฟู หน้ากลมแป้น สุขุมเรียบร้อย\n" .
                          "🧸 2. Scottish Fold (สกอตติช โฟลด์) - หูพับสนิท ตากลมโต ขี้อ้อนน่าเอ็นดู\n" .
                          "🦁 3. Maine Coon (เมนคูน) - ยักษ์ใหญ่ใจดี สง่างามดั่งสิงโต สายเลือดแชมป์\n" .
                          "👑 4. Persian Classic (เปอร์เซีย) - ราชินีขนฟู หน้าหวาน สุภาพนิ่งสงบ"
            ],
            [
                'type' => 'text',
                'text' => "💎 5. Ragdoll (แร็กดอลล์) - เจ้าหญิงตาสีฟ้าคราม ตัวนุ่มนิ่มดั่งตุ๊กตาผ้า\n" .
                          "🐾 6. Munchkin (มันช์กิ้น ขาสั้น) - ขาสั้นเตี้ยดุ๊กดิ๊ก น่ารักขี้เล่น เรียกรอยยิ้ม\n" .
                          "🐆 7. Bengal Rosetted (เบงกอล) - เสือดาวจิ๋ว ลายกุหลาบทองคำ ปราดเปรียวฉลาด\n" .
                          "✨ 8. Canadian Sphynx (สฟิงซ์) - ไร้ขน 100% ผิวนุ่มอุ่นดั่งลูกพีช หมดห่วงเรื่องภูมิแพ้\n" .
                          "🧧 9. Siamese (วิเชียรมาศ) - แต้ม 9 จุดมงคล ตาสีฟ้าคราม เฉลียวฉลาดนำโชคลาภ"
            ],
            getCatCardCarouselMessage()
        ];
    }
    elseif (in_array($userMsg, ['โปรโมชั่น', '💰 โปรโมชั่น', 'โปร', 'ส่วนลด', 'ดีล', 'promotion'])) {
        $activeCategory = 1;
        $customMessages = [
            [
                'type' => 'text',
                'text' => "🎉 รวมโปรโมชั่น & ดีลลับสุดพิเศษ Purrfect Shop 🐾✨\n" .
                          "สวัสดีค่ะคุณลูกค้า! เดือนนี้เรามีโปรโมชั่นและโค้ดส่วนลดสุดคุ้มมามอบให้คนรักแมวทุกคนค่ะ 🐱💖\n\n" .
                          "🏷️ รวมโค้ดส่วนลดประจำเดือน:\n" .
                          "🎟️ โค้ด: CAT10OFF\n" .
                          "   • ลดทันที 10% (เมื่อช้อปขั้นต่ำ 300.-)\n" .
                          "   • จำกัด 1 สิทธิ์ต่อท่าน / ใช้ได้ภายใน 1 เดือน\n\n" .
                          "🎁 สิทธิพิเศษเมื่อรับเลี้ยงน้องแมววันนี้!\n\n" .
                          "👇 กดดูรายละเอียดโปรโมชั่นและดีลทั้งหมดได้ที่นี่ค่ะ:\n" .
                          "🔗 ลิงก์โปรโมชั่น: https://patches660.github.io/Purrfect-Shop/welcome_deal.html"
            ],
            getPromotionRichCarouselMessage()
        ];
    }
    elseif (in_array($userMsg, ['สมัครสมาชิก', '📋 สมัครสมาชิก'])) {
        $activeCategory = 1;
        $replyText = "✨ สิทธิประโยชน์สำหรับสมาชิก Purrfect Member:\n" .
                     "1. รับส่วนลด 5% ทันทีทุกออเดอร์\n" .
                     "2. สะสมแต้ม Purr Points ทุกยอดซื้อ (100 บาท = 1 แต้ม แลกของรางวัลได้)\n" .
                     "3. สิทธิ์หมุนวงล้อลุ้นโชครับคูปองสูงสุด 50% ทุกวัน\n" .
                     "4. ฟรี! ปรึกษาสัตวแพทย์ประจำฟาร์มตลอดอายุขัย\n\n" .
                     "🔗 สมัครสมาชิกฟรี: https://patches660.github.io/Purrfect-Shop/register.html";
    }
    elseif (in_array($userMsg, ['คูปอง', '🎫 คูปอง', 'coupon', 'code', 'โค้ดส่วนลด'])) {
        $activeCategory = 1;
        $customMessages = [
            [
                'type' => 'text',
                'text' => "🎟️ คลังคูปองส่วนลด 25% สุดคุ้มจาก Purrfect Shop 🐾✨\n" .
                          "กดแตะเลื่อนดูคูปองด้านล่าง แล้วกดรับสิทธิ์หรือคัดลอกโค้ดไปใช้ช้อปปิ้งได้ทันทีค่ะ 🐱💖\n\n" .
                          "🏷️ รวมคูปองส่วนลด 25% ประจำเดือน:\n" .
                          "• ⚡ Flash Sale 25% (โค้ด: FLASH25NOW)\n" .
                          "• 💜 คอนโด & ของเล่น 25% (โค้ด: PLAY25MEOW)\n" .
                          "• 🌿 อาหาร & ขนมแมว 25% (โค้ด: YUMMY25CAT)\n" .
                          "• 👑 ค่าสินสอดน้องแมว 25% (โค้ด: ADOPT25VIP)\n" .
                          "• 🧡 ต้อนรับทาสแมวใหม่ 25% (โค้ด: PURR25NEW)\n\n" .
                          "👇 กดรับคูปองและเช็คเงื่อนไขทั้งหมดได้ที่นี่ค่ะ:\n" .
                          "🔗 ลิงก์เก็บคูปอง: https://patches660.github.io/Purrfect-Shop/welcome_deal.html"
            ],
            getCouponCarouselMessage()
        ];
    }
    elseif (in_array($userMsg, ['ติดต่อเรา', '📞 ติดต่อเรา'])) {
        $activeCategory = 1;
        $replyText = "📞 ข้อมูลติดต่อ & เวลาเปิดทำการ Purrfect Shop:\n" .
                     "🏢 ที่ตั้ง: เลขที่ 88/9 อาคารเพอร์เฟกต์ ทาวเวอร์ ถ.สุขุมวิท 71 แขวงพระโขนงเหนือ เขตวัฒนา กทม. 10110 (ใกล้ BTS พระโขนง)\n" .
                     "⏰ เวลาเปิดทำการ: ทุกวัน 09:00 – 19:00 น.\n" .
                     "📱 โทร: 02-888-9999 หรือ 095-888-7777\n" .
                     "💬 LINE OA: @purrfectshop\n" .
                     "📧 อีเมล: contact@purrfectshop.com";
    }
    elseif (in_array($userMsg, ['ชำระเงิน', '💵 ชำระเงิน'])) {
        $activeCategory = 1;
        $replyText = "💳 ช่องทางการชำระเงิน:\n" .
                     "1. โอนเงินผ่านธนาคาร / QR Code พร้อมเพย์ (ฟรีค่าธรรมเนียม)\n" .
                     "2. บัตรเครดิต / บัตรเดบิต (Visa, Mastercard, JCB)\n" .
                     "3. ผ่อนชำระ 0% นานสูงสุด 10 เดือน (สำหรับยอด 15,000 บาทขึ้นไป)\n" .
                     "4. เก็บเงินปลายทาง (COD สำหรับสินค้าอุปกรณ์และอาหาร)\n\n" .
                     "ปลอดภัย 100% มีใบกำกับภาษีและใบเสร็จรับเงินถูกต้องครับ";
    }
    elseif (in_array($userMsg, ['แบบสอบถาม', '🗓️ แบบสอบถาม', 'survey', 'แบบสำรวจ', 'ทำแบบสอบถาม'])) {
        $activeCategory = 1;
        $customMessages = [
            [
                'type' => 'text',
                'text' => "📋 ร่วมแชร์ความคิดเห็นกับ Purrfect Shop 🐱✨\n\n" .
                          "เพื่อพัฒนาการบริการและคัดสรรสิ่งที่ดีที่สุดสำหรับน้องแมว เราขอเชิญคุณลูกค้าร่วมตอบแบบสอบถามสั้นๆ 5 ข้อ (ใช้เวลาเพียง 1-2 นาทีค่ะ) 📝\n\n" .
                          "🎁 สิทธิพิเศษหลังตอบแบบสอบถามเสร็จ:\n" .
                          "• รับทันที! คูปองส่วนลด 100 บาท (ไม่มีขั้นต่ำ) 🎟️\n" .
                          "• สิทธิ์ลุ้นรับชุดขนมแมวเลียพรีเมียม 1 เซ็ตฟรี 🐟\n\n" .
                          "👇 แตะลิงก์ด้านล่างเพื่อเริ่มทำแบบสอบถามได้เลยค่ะ:\n" .
                          "👉 คลิกทำแบบสอบถาม: https://patches660.github.io/Purrfect-Shop/quiz.html\n\n" .
                          "กราบขอบพระคุณทุกความคิดเห็นและข้อเสนอแนะที่มีคุณค่านะคะ 🙏❤️"
            ],
            getSurveyCardMessage()
        ];
    }
    elseif (in_array($userMsg, ['ติดตามสินค้า', '🚚 ติดตามสินค้า'])) {
        $activeCategory = 1;
        $replyText = "🚚 ระบบติดตามการจัดส่ง Real-time (Pet Taxi Tracking):\n" .
                     "สามารถกรอกหมายเลข Tracking Number เพื่อดูตำแหน่งรถจัดส่ง สถานะสุขภาพ และข้อมูลพี่เลี้ยงประจำรถได้ทันทีครับ\n\n" .
                     "🔗 ตรวจสอบสถานะจัดส่ง: https://patches660.github.io/Purrfect-Shop/tracking.html";
    }
    // 4. คำสั่งเฉพาะตามปุ่ม หมวด 2 (ดึงตามข้อความตอบกลับอัตโนมัติที่คุณตั้งไว้)
    elseif (in_array($userMsg, ['จองคิวนัดดูตัว', '🎯 จองคิวนัดดูตัว'])) {
        $activeCategory = 2;
        $replyText = "🏠 Private Cattery Visit & Video Call\n" .
                     "━━━━━━━━━━━━━━━━━━\n" .
                     "สนใจรับน้องแมวสายพันธุ์แท้เข้าบ้าน? จองคิวนัดหมายเข้าชมฟาร์มแบบ VIP ล่วงหน้า หรือนัด Video Call ดูตัวจริงกับพี่เลี้ยงได้ทันทีค่ะ 🐾\n\n" .
                     "🔗 จองคิวออนไลน์ได้ที่: https://patches660.github.io/Purrfect-Shop/booking.html";
    }
    elseif (in_array($userMsg, ['วัคซีน', '💉 วัคซีน'])) {
        $activeCategory = 2;
        $replyText = "🩺 Cat Vaccination Passport & Reminder\n" .
                     "━━━━━━━━━━━━━━━━━━\n" .
                     "ติดตามสุขภาพน้องแมวได้อย่างใกล้ชิด! สมุดประวัติวัคซีนดิจิทัล บันทึกการฉีดวัคซีนครบ 2 เข็ม ถ่ายพยาธิ ไมโครชิป พร้อมระบบแจ้งเตือนนัดหมายแพทย์ประจำปีฟรีค่ะ 💉✨\n\n" .
                     "🔗 ตรวจสอบประวัติวัคซีน: https://patches660.github.io/Purrfect-Shop/vaccine_reminder.html";
    }
    elseif (in_array($userMsg, ['บัตรสะสมแต้ม', '🥇 บัตรสะสมแต้ม', 'paw points', 'reward card', 'สะสมแต้ม', 'แต้ม'])) {
        $activeCategory = 2;
        $customMessages = [
            [
                'type' => 'text',
                'text' => "🐾 ต้อนรับสู่ Purrfect Paw Points Club ค่ะ! 💳✨\n\n" .
                          "สะสมแต้มความสุข แลกรับของรางวัลสุดพรีเมียมสำหรับเจ้านายตัวโปรดได้ง่ายๆ ทุกยอดช้อปปิ้งและรับเลี้ยงน้องแมว 🐱🎁\n\n" .
                          "🌟 กติกาการสะสม Paw Points:\n" .
                          "• ช้อปสินค้า/อาหาร/ทรายแมว ครบทุก 500 บาท = รับ 1 แต้ม\n" .
                          "• รับเลี้ยงน้องแมวสายพันธุ์แท้ 1 ตัว = รับ 1 แต้มทันที\n" .
                          "• 🎁 พิเศษ! สมาชิกใหม่รับฟรี 1 แต้มทันทีเมื่อเปิดบัตร"
            ],
            [
                'type' => 'text',
                'text' => "🏆 ตารางของรางวัลสุดคุ้ม:\n" .
                          "🥉 สะสมครบ 3 แต้ม ➔ แลกรับคูปองส่วนลด 10% (ใช้ลดสินค้าในร้าน)\n" .
                          "🥇 สะสมครบ 5 แต้ม ➔ แลกฟรี! Starter Kit มูลค่า 350.- หรือ ส่วนลดสินสอดสูงสุด 45%!\n\n" .
                          "👇 แตะปุ่มด้านล่างเพื่อเปิดดูบัตรสะสมแต้มและเช็คแต้มของคุณได้เลยค่ะ:\n" .
                          "👉 คลิกเปิดบัตรสะสมแต้มดิจิทัล: https://u.lin.ee/PHZxZGW\n\n" .
                          "💬 หรือแจ้งเบอร์โทรศัพท์/สลิปโอนเงินกับแอดมิน เพื่อให้แอดมินสะสมแต้มให้ได้เลยนะคะ 🙏"
            ],
            [
                'type' => 'image',
                'originalContentUrl' => 'https://files.catbox.moe/2bvd6y.png',
                'previewImageUrl' => 'https://files.catbox.moe/2bvd6y.png'
            ],
            [
                'type' => 'image',
                'originalContentUrl' => 'https://files.catbox.moe/nu30dq.png',
                'previewImageUrl' => 'https://files.catbox.moe/nu30dq.png'
            ]
        ];
    }
    elseif (in_array($userMsg, ['วงล้อ', '🎡 วงล้อ', 'วงล้อลุ้นโชค'])) {
        $activeCategory = 2;
        $replyText = "🎁 Daily Lucky Wheel หมุนวงล้อลุ้นโชคฟรี!\n" .
                     "━━━━━━━━━━━━━━━━━━\n" .
                     "สิทธิพิเศษสำหรับลูกค้าคนสำคัญ! หมุนวงล้อลุ้นรับคูปองส่วนลด 2,500.- & Starter Kit ฟรี 11 รายการ หมุนได้ทุกวันวันละ 1 ครั้งค่ะ 🎡🎉\n\n" .
                     "🔗 หมุนวงล้อลุ้นโชค: https://patches660.github.io/Purrfect-Shop/lucky_wheel.html";
    }
    elseif (in_array($userMsg, ['คำนวณค่าเลี้ยงแมว', '🤔 คำนวณค่าเลี้ยงแมว'])) {
        $activeCategory = 2;
        $replyText = "🧮 Smart Cat Expense Calculator\n" .
                     "━━━━━━━━━━━━━━━━━━\n" .
                     "วางแผนงบประมาณการดูแลเจ้านายอย่างมั่นใจ! คำนวณปริมาณอาหาร ทรายแมว และค่าใช้จ่ายรายเดือนตามสายพันธุ์ อายุ และน้ำหนักตัวของน้องแมวค่ะ 🐱💡\n\n" .
                     "🔗 ใช้งานโปรแกรมคำนวณ: https://patches660.github.io/Purrfect-Shop/calculator.html";
    }
    elseif (in_array($userMsg, ['ใบเพ็ดดีกรี', '📜 ใบเพ็ดดีกรี'])) {
        $activeCategory = 2;
        $replyText = "🏆 Verified Digital Pedigree & Health Guarantee\n" .
                     "━━━━━━━━━━━━━━━━━━\n" .
                     "หมดกังวลเรื่องสุขภาพ! น้องแมวทุกตัวมีใบเพ็ดดีกรีแท้ (CFA / WCF / TICA) สายเลือดแชมป์สากล พร้อมการันตีตรวจโรคพันธุกรรมและโรคร้ายแรง 180 วันเต็มค่ะ 📜🛡️\n\n" .
                     "🔗 ตรวจสอบใบเพ็ดดีกรี: https://patches660.github.io/Purrfect-Shop/pedigree.html";
    }
    // 5. กรณีพิมพ์คำถามอิสระทั่วไป ➔ ถาม Dify AI (หรือค้นหาจากฐานข้อมูล 78 ข้อ)
    else {
        $aiAnswer = "";
        if (!empty($difyKey)) {
            $aiAnswer = askDifyAI($userMsg, $userId, $difyKey);
        }
        
        if (empty($aiAnswer)) {
            $aiAnswer = searchLocalKnowledge($userMsg);
        }

        if (!empty($aiAnswer)) {
            $replyText = $aiAnswer;
        } else {
            $replyText = "ขอบคุณสำหรับข้อความครับ! 🐾 เจ้าหน้าที่หรือ Purrfect Bot ได้รับคำถามแล้ว\n\n" .
                         "คุณลูกค้าสามารถสอบถามข้อมูลสายพันธุ์, ราคา, การจัดส่งฟรี Pet Taxi หรือแตะเลือกเมนูด่วนด้านล่างนี้ได้เลยครับ 👇";
        }
    }

    // 6. ประกอบ Payload ข้อความพร้อมปุ่ม Quick Reply ส่งกลับ LINE
    $quickReplies = getQuickReplies($activeCategory);

    if (!empty($customMessages) && is_array($customMessages)) {
        $messages = $customMessages;
        // แนบปุ่ม Quick Reply ไว้ที่ข้อความสุดท้าย
        $lastIdx = count($messages) - 1;
        $messages[$lastIdx]['quickReply'] = [
            'items' => $quickReplies
        ];
    } else {
        $messages = [
            [
                'type' => 'text',
                'text' => $replyText,
                'quickReply' => [
                    'items' => $quickReplies
                ]
            ]
        ];
    }

    if (!empty($replyToken)) {
        $payload = [
            'replyToken' => $replyToken,
            'messages' => $messages
        ];
        sendLineApiRequest('https://api.line.me/v2/bot/message/reply', $payload, $channelToken);
    } elseif (!empty($userId)) {
        $payload = [
            'to' => $userId,
            'messages' => $messages
        ];
        sendLineApiRequest('https://api.line.me/v2/bot/message/push', $payload, $channelToken);
    }
}

/**
 * ฟังก์ชันยิง HTTP POST ส่งข้อความกลับไปที่ LINE Messaging API
 */
function sendLineApiRequest($url, $payload, $token) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=UTF-8',
        'Authorization: Bearer ' . $token
    ]);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    @file_put_contents(__DIR__ . '/line_webhook_log.txt', date('Y-m-d H:i:s') . " - URL: {$url} - Status: {$httpCode}, response: {$result}\n", FILE_APPEND);
    return $result;
}

/**
 * ฟังก์ชันยิงคำถามไปที่ Dify AI Chat API
 */
function askDifyAI($query, $user_id, $api_key) {
    $url = 'https://api.dify.ai/v1/chat-messages';
    $data = [
        'inputs' => new stdClass(),
        'query' => $query,
        'response_mode' => 'blocking',
        'user' => $user_id ?: 'user_line'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json; charset=UTF-8'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    @file_put_contents(__DIR__ . '/line_webhook_log.txt', date('Y-m-d H:i:s') . " - Dify API Status: {$httpCode}, Answer: " . mb_substr($response, 0, 100) . "\n", FILE_APPEND);

    if ($httpCode === 200) {
        $res_json = json_decode($response, true);
        return $res_json['answer'] ?? null;
    }
    return null;
}
