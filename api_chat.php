<?php
// api_chat.php - Realtime Live Chat & Smart Bot Backend
date_default_timezone_set('Asia/Bangkok');
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/data.php';

$chat_file = __DIR__ . '/data_chats.json';

// Helper: Load chats
function loadChats($chat_file) {
    if (!file_exists($chat_file)) {
        return ['conversations' => []];
    }
    $json = file_get_contents($chat_file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : ['conversations' => []];
}

// Helper: Save chats
function saveChats($chat_file, $data) {
    file_put_contents($chat_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Identify user / session
$current_user = getCurrentUser();
$is_admin = $current_user && ($current_user['role'] ?? '') === 'admin';

$user_id = '';
$user_name = '';
$user_email = '';
$is_member = false;
$user_avatar = 'assets/images/logo.png';

if ($current_user) {
    $user_id = $current_user['id'] ?? ('u_' . md5($current_user['email'] ?? 'guest'));
    $user_name = $current_user['fullname'] ?? ($current_user['username'] ?? 'สมาชิก');
    $user_email = $current_user['email'] ?? '';
    $is_member = true;
    if (!empty($current_user['avatar'])) {
        $user_avatar = $current_user['avatar'];
    }
} else {
    if (!isset($_SESSION['chat_guest_id'])) {
        $_SESSION['chat_guest_id'] = 'guest_' . substr(md5(session_id() . time()), 0, 10);
    }
    $user_id = $_SESSION['chat_guest_id'];
    $user_name = 'ผู้เยี่ยมชม (' . substr($user_id, -4) . ')';
    $user_email = 'guest@purrfect.shop';
    $is_member = false;
}

$action = $_REQUEST['action'] ?? 'get_messages';
$data_store = loadChats($chat_file);

// Ensure conversation exists for user
function ensureConversation(&$data_store, $uid, $name, $email, $is_mem, $avatar) {
    if (!isset($data_store['conversations'][$uid])) {
        $data_store['conversations'][$uid] = [
            'user_id' => $uid,
            'user_name' => $name,
            'user_email' => $email,
            'is_member' => $is_mem,
            'avatar' => $avatar,
            'unread_admin' => 0,
            'unread_user' => 0,
            'last_active' => date('Y-m-d H:i:s'),
            'messages' => [
                [
                    'id' => 'msg_init_' . time(),
                    'sender' => 'bot',
                    'sender_name' => 'Purrfect Bot 🐾',
                    'text' => 'สวัสดีครับยินดีต้อนรับสู่ Purrfect Shop! 🐾 มีอะไรให้บอทหรือแอดมินดูแล สามารถเลือกคำถามด่วนด้านล่าง หรือพิมพ์ข้อความพูดคุยได้เลยครับ',
                    'cards' => [],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ]
        ];
    }
}

// -------------------------------------------------------------
// Action 1: Get Messages for current user or Admin target
// -------------------------------------------------------------
if ($action === 'get_messages') {
    $target_uid = $user_id;
    if ($is_admin && !empty($_GET['target_user_id'])) {
        $target_uid = trim($_GET['target_user_id']);
    }

    ensureConversation($data_store, $target_uid, $user_name, $user_email, $is_member, $user_avatar);
    
    // If admin is viewing, clear unread_admin
    if ($is_admin && !empty($_GET['target_user_id'])) {
        if (isset($data_store['conversations'][$target_uid])) {
            $data_store['conversations'][$target_uid]['unread_admin'] = 0;
            saveChats($chat_file, $data_store);
        }
    } else {
        // Customer viewing, clear unread_user
        if (isset($data_store['conversations'][$target_uid])) {
            $data_store['conversations'][$target_uid]['unread_user'] = 0;
            saveChats($chat_file, $data_store);
        }
    }

    echo json_encode([
        'status' => 'success',
        'current_user' => [
            'id' => $user_id,
            'name' => $user_name,
            'is_member' => $is_member,
            'is_admin' => $is_admin,
            'avatar' => $user_avatar
        ],
        'conversation' => $data_store['conversations'][$target_uid] ?? null
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Helper: Search AI Knowledge Base (78 Q&A dataset)
function findBotKnowledgeAnswer($user_query) {
    global $cats;
    $kb_file = __DIR__ . '/chatbot_knowledge_base.json';
    if (!file_exists($kb_file)) {
        return null;
    }
    $kb = json_decode(file_get_contents($kb_file), true);
    if (!is_array($kb) || empty($kb)) {
        return null;
    }

    $raw_query = trim($user_query);
    if (empty($raw_query)) return null;

    $query_lower = mb_strtolower($raw_query, 'UTF-8');
    
    // Stopwords in Thai that could cause false positives if matched alone
    $stopwords = ['ไหม', 'มั้ย', 'ได้', 'หรือ', 'และ', 'กับ', 'ให้', 'ของ', 'ที่', 'ใน', 'การ', 'ความ', 'ทาง', 'เป็น', 'อยู่', 'ช่วย', 'หน่อย', 'ครับ', 'ค่ะ', 'นะ', 'คะ', 'กี่', 'ตัว', 'ทำ', 'ยัง', 'ไง', 'บ้าง', 'มี', 'ถ้า', 'หาก', 'อยาก', 'จะ', 'ขอ'];

    $best_score = 0;
    $best_item = null;

    // Cat breed keyword mapping to cat IDs for card attachments
    $breed_map = [
        'บริติช' => 'cat_british',
        'british' => 'cat_british',
        'สกอตติช' => 'cat_scottish',
        'scottish' => 'cat_scottish',
        'หูพับ' => 'cat_scottish',
        'เปอร์เซีย' => 'cat_persian',
        'persian' => 'cat_persian',
        'เมนคูน' => 'cat_mainecoon',
        'maine coon' => 'cat_mainecoon',
        'สฟิงซ์' => 'cat_sphynx',
        'sphynx' => 'cat_sphynx',
        'ไร้ขน' => 'cat_sphynx',
        'แร็กดอลล์' => 'cat_ragdoll',
        'ragdoll' => 'cat_ragdoll',
        'เบงกอล' => 'cat_bengal',
        'bengal' => 'cat_bengal',
        'ลายเสือ' => 'cat_bengal',
        'มันช์กิ้น' => 'cat_munchkin',
        'munchkin' => 'cat_munchkin',
        'ขาสั้น' => 'cat_munchkin',
        'วิเชียรมาศ' => 'cat_siamese',
        'siamese' => 'cat_siamese',
        'อเมริกัน' => 'cat_americanshorthair',
        'american' => 'cat_americanshorthair',
        'รัสเซียน' => 'cat_russian',
        'russian' => 'cat_russian',
        'ไซบีเรียน' => 'cat_siberian',
        'siberian' => 'cat_siberian',
        'เดวอน' => 'cat_devon_rex',
        'devon' => 'cat_devon_rex',
        'คอร์นิช' => 'cat_cornish_rex',
        'cornish' => 'cat_cornish_rex',
    ];

    foreach ($kb as $item) {
        $score = 0;
        $q_text = mb_strtolower($item['question'], 'UTF-8');
        $ans_text = mb_strtolower($item['answer'], 'UTF-8');
        $cat_text = mb_strtolower($item['category'], 'UTF-8');
        $tags = array_map(function($t) { return mb_strtolower($t, 'UTF-8'); }, $item['tags'] ?? []);

        // 1. Tag matching (Tags are high-value keywords!)
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                if ($tag === $query_lower) {
                    $score += 80;
                } elseif (mb_strpos($query_lower, $tag) !== false) {
                    $score += 40 + (mb_strlen($tag, 'UTF-8') * 5);
                } elseif (mb_strpos($tag, $query_lower) !== false && mb_strlen($query_lower, 'UTF-8') >= 3) {
                    $score += 30;
                }
            }
        }

        // 2. Full question exact or direct containment
        if (mb_strpos($q_text, $query_lower) !== false || mb_strpos($query_lower, $q_text) !== false) {
            $score += 60;
        }

        // 3. Sliding window on query (filtering stopwords)
        $len = mb_strlen($query_lower, 'UTF-8');
        for ($w = 3; $w <= min(6, $len); $w++) {
            for ($i = 0; $i <= $len - $w; $i++) {
                $sub = mb_substr($query_lower, $i, $w, 'UTF-8');
                if (in_array($sub, $stopwords)) continue;

                if (mb_strpos($q_text, $sub) !== false) {
                    $score += $w * 3;
                }
                if (mb_strpos($ans_text, $sub) !== false) {
                    $score += $w;
                }
            }
        }

        // Category relevance bonus
        if (mb_strpos($query_lower, $cat_text) !== false) {
            $score += 15;
        }

        if ($score > $best_score) {
            $best_score = $score;
            $best_item = $item;
        }
    }

    // Determine relevant cards
    $cards = [];
    $matched_cat_ids = [];
    foreach ($breed_map as $kw => $cid) {
        if (mb_strpos($query_lower, $kw) !== false && !in_array($cid, $matched_cat_ids)) {
            $matched_cat_ids[] = $cid;
        }
    }
    // If best item has tags matching breed
    if ($best_item && !empty($best_item['tags'])) {
        foreach ($best_item['tags'] as $t) {
            $t_l = mb_strtolower($t, 'UTF-8');
            foreach ($breed_map as $kw => $cid) {
                if ((mb_strpos($t_l, $kw) !== false || mb_strpos($kw, $t_l) !== false) && !in_array($cid, $matched_cat_ids)) {
                    $matched_cat_ids[] = $cid;
                }
            }
        }
    }

    if (!empty($matched_cat_ids)) {
        foreach (array_slice($matched_cat_ids, 0, 3) as $cid) {
            if (isset($cats[$cid])) {
                $c = $cats[$cid];
                $cards[] = [
                    'id' => $c['id'],
                    'name' => $c['name'],
                    'breed' => $c['breed'],
                    'price' => $c['price'],
                    'price_fmt' => '฿' . number_format($c['price']),
                    'image' => 'assets/images/' . $c['image'],
                    'hair' => $c['hair_label'] ?? 'ขนสวยสุขภาพดี',
                    'highlight' => $c['highlights'][0] ?? 'สายพันธุ์แท้ 100% มีใบเพ็ด',
                    'link' => 'products.php'
                ];
            }
        }
    }

    // Threshold check (score >= 20 means strong relevance)
    if ($best_score >= 20 && $best_item) {
        return [
            'found' => true,
            'score' => $best_score,
            'id' => $best_item['id'],
            'category' => $best_item['category'],
            'question' => $best_item['question'],
            'answer' => $best_item['answer'],
            'cards' => $cards
        ];
    }

    // Default friendly assistant fallback
    $fallback_text = "ขอบคุณสำหรับข้อความครับ! 🐾 ผู้ช่วย Purrfect Bot ได้รับคำถามแล้ว\n\n" .
        "💡 คุณลูกค้าสามารถสอบถามข้อมูลร้านได้ทันที เช่น:\n" .
        "• ข้อมูลและราคาน้องแมวสายพันธุ์ต่างๆ (เช่น บริติช, สฟิงซ์, แร็กดอลล์)\n" .
        "• เวลาเปิดทำการ, แผนที่ร้าน, เบอร์โทรติดต่อ หรือช่องทาง LINE OA\n" .
        "• การรับประกันสุขภาพ 180 วัน, การฉีดวัคซีน, และไมโครชิป\n" .
        "• การจัดส่งฟรีทั่วไทยด้วยรถ Pet Taxi ควบคุมอุณหภูมิ\n" .
        "• โปรโมชั่นโค้ดส่วนลด และ Starter Kit 11 ชิ้น\n" .
        "หรือกดปุ่ม ⚡ คำถาม ด้านล่างเพื่อดูคำถามด่วนยอดนิยมได้เลยครับ!";

    return [
        'found' => false,
        'score' => $best_score,
        'answer' => $fallback_text,
        'cards' => $cards
    ];
}

// -------------------------------------------------------------
// Action 2: User / Customer sends a message
// -------------------------------------------------------------
if ($action === 'send_message') {
    $text = trim($_POST['message'] ?? '');
    if (empty($text)) {
        echo json_encode(['status' => 'error', 'message' => 'ข้อความว่างเปล่า'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    ensureConversation($data_store, $user_id, $user_name, $user_email, $is_member, $user_avatar);

    $msg = [
        'id' => 'msg_' . uniqid(),
        'sender' => 'customer',
        'sender_name' => $user_name,
        'text' => htmlspecialchars($text, ENT_QUOTES, 'UTF-8'),
        'cards' => [],
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $data_store['conversations'][$user_id]['messages'][] = $msg;

    // AI Knowledge Base Automated Reply
    $bot_reply = findBotKnowledgeAnswer($text);
    $bot_msg = null;
    if ($bot_reply) {
        $bot_msg = [
            'id' => 'msg_bot_' . uniqid(),
            'sender' => 'bot',
            'sender_name' => 'Purrfect Bot 🐾',
            'text' => $bot_reply['answer'],
            'cards' => $bot_reply['cards'] ?? [],
            'timestamp' => date('Y-m-d H:i:s', time() + 1)
        ];
        $data_store['conversations'][$user_id]['messages'][] = $bot_msg;
    }

    $data_store['conversations'][$user_id]['last_active'] = date('Y-m-d H:i:s');
    $data_store['conversations'][$user_id]['unread_admin'] = ($data_store['conversations'][$user_id]['unread_admin'] ?? 0) + 1;

    saveChats($chat_file, $data_store);

    echo json_encode([
        'status' => 'success',
        'message' => 'ส่งข้อความสำเร็จ',
        'data' => $msg,
        'bot_message' => $bot_msg
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// -------------------------------------------------------------
// Action 2.5: Search Knowledge Base directly (API endpoint)
// -------------------------------------------------------------
if ($action === 'search_knowledge_base') {
    $query = trim($_REQUEST['q'] ?? '');
    $res = findBotKnowledgeAnswer($query);
    echo json_encode([
        'status' => 'success',
        'query' => $query,
        'result' => $res
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// -------------------------------------------------------------
// Action 3: Quick Reply / Smart Bot 6 Questions
// -------------------------------------------------------------
if ($action === 'quick_reply') {
    $option = intval($_POST['option'] ?? 0);
    ensureConversation($data_store, $user_id, $user_name, $user_email, $is_member, $user_avatar);

    $quick_prompts = [
        1 => '🐱 แนะนำน้องแมวยอดนิยม เลี้ยงง่าย สำหรับมือใหม่',
        2 => '🏢 มีน้องแมวพันธุ์ไหนที่เหมาะกับเลี้ยงในคอนโด/ห้องพักบ้าง?',
        3 => '🤧 เป็นภูมิแพ้ แนะนำน้องแมวขนไม่ร่วง/ผลัดขนน้อยหน่อยครับ',
        4 => '👑 ขอดูแมวสายพันธุ์พรีเมียม ขนฟูหน้าหวาน มีใบเพ็ดดีกรี',
        5 => '🎁 ตอนนี้มีโปรโมชั่นหรือดีลส่วนลดสมาชิกใหม่อะไรบ้าง?',
        6 => '🩺 การรับประกันสุขภาพและบริการหลังการขายมีอะไรบ้าง?'
    ];

    if (!isset($quick_prompts[$option])) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบคำถามด่วน'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $prompt_text = $quick_prompts[$option];

    // Append customer message
    $cust_msg = [
        'id' => 'msg_q_c_' . uniqid(),
        'sender' => 'customer',
        'sender_name' => $user_name,
        'text' => $prompt_text,
        'cards' => [],
        'timestamp' => date('Y-m-d H:i:s')
    ];
    $data_store['conversations'][$user_id]['messages'][] = $cust_msg;

    // Build bot response based on cats catalog in data.php
    global $cats;
    $bot_reply_text = "";
    $cards = [];

    switch ($option) {
        case 1:
            // ยอดนิยม มือใหม่
            $bot_reply_text = "นี่คือน้องแมวยอดนิยมอันดับ 1 เลี้ยงง่าย เป็นมิตร สุขภาพแข็งแรง เหมาะสำหรับมือใหม่ที่เพิ่งเริ่มเลี้ยงที่สุดครับ 🐾";
            $sample_ids = ['cat_british', 'cat_americanshorthair', 'cat_scottish'];
            foreach ($sample_ids as $cid) {
                if (isset($cats[$cid])) {
                    $c = $cats[$cid];
                    $cards[] = [
                        'id' => $c['id'],
                        'name' => $c['name'],
                        'breed' => $c['breed'],
                        'price' => $c['price'],
                        'price_fmt' => '฿' . number_format($c['price']),
                        'image' => 'assets/images/' . $c['image'],
                        'hair' => $c['hair_label'] ?? 'ขนสั้น',
                        'highlight' => $c['highlights'][0] ?? 'เลี้ยงง่าย เป็นมิตร',
                        'link' => 'products.php'
                    ];
                }
            }
            break;

        case 2:
            // เลี้ยงคอนโด รักสงบ
            $bot_reply_text = "สำหรับเพื่อนๆ ที่พักอาศัยในคอนโดหรือทาวน์โฮม แนะนำสายพันธุ์ที่รักความสงบ ไม่ส่งเสียงดังรบกวนข้างห้อง และปรับตัวเก่งครับ 🏢✨";
            $sample_ids = ['cat_persian', 'cat_russian', 'cat_ragdoll'];
            foreach ($sample_ids as $cid) {
                if (isset($cats[$cid])) {
                    $c = $cats[$cid];
                    $cards[] = [
                        'id' => $c['id'],
                        'name' => $c['name'],
                        'breed' => $c['breed'],
                        'price' => $c['price'],
                        'price_fmt' => '฿' . number_format($c['price']),
                        'image' => 'assets/images/' . $c['image'],
                        'hair' => $c['hair_label'] ?? 'ขนนุ่ม',
                        'highlight' => $c['highlights'][0] ?? 'เงียบสงบ ไม่ส่งเสียงดัง',
                        'link' => 'recommend.php?cat=condo'
                    ];
                }
            }
            break;

        case 3:
            // คนเป็นภูมิแพ้ ขนไม่ร่วง
            $bot_reply_text = "ผู้ที่เป็นโรคภูมิแพ้ขนสัตว์ หรือไม่ชอบกวาดขนแมว แนะนำกลุ่มสายพันธุ์ Hypoallergenic / ขนสั้นพิเศษ หรือไร้ขน 100% เหล่านี้ครับ 🤧🌿";
            $sample_ids = ['cat_sphynx', 'cat_devon_rex', 'cat_cornish_rex'];
            foreach ($sample_ids as $cid) {
                if (isset($cats[$cid])) {
                    $c = $cats[$cid];
                    $cards[] = [
                        'id' => $c['id'],
                        'name' => $c['name'],
                        'breed' => $c['breed'],
                        'price' => $c['price'],
                        'price_fmt' => '฿' . number_format($c['price']),
                        'image' => 'assets/images/' . $c['image'],
                        'hair' => $c['hair_label'] ?? 'ขนร่วง 0%',
                        'highlight' => $c['highlights'][0] ?? 'ขนร่วง 0% ไร้ภูมิแพ้',
                        'link' => 'recommend.php?cat=low_shed'
                    ];
                }
            }
            break;

        case 4:
            // พรีเมียม ขนฟู ใบเพ็ด
            $bot_reply_text = "ขอนำเสนอน้องแมวเกรดพรีเมียม ขนฟูอลังการ หน้าหวาน และมีใบเพ็ดดีกรี CFA/WCF สายเลือดแชมป์สากล 100% ครับ 👑💎";
            $sample_ids = ['cat_mainecoon', 'cat_ragdoll', 'cat_siberian'];
            foreach ($sample_ids as $cid) {
                if (isset($cats[$cid])) {
                    $c = $cats[$cid];
                    $cards[] = [
                        'id' => $c['id'],
                        'name' => $c['name'],
                        'breed' => $c['breed'],
                        'price' => $c['price'],
                        'price_fmt' => '฿' . number_format($c['price']),
                        'image' => 'assets/images/' . $c['image'],
                        'hair' => $c['hair_label'] ?? 'ขนยาวฟูพรีเมียม',
                        'highlight' => $c['highlights'][0] ?? 'สายเลือดแชมป์สากล',
                        'link' => 'products.php'
                    ];
                }
            }
            break;

        case 5:
            // โปรโมชั่น & ส่วนลด
            $bot_reply_text = "🎉 สิทธิพิเศษและโค้ดส่วนลดสุดฮอตประจำเดือนนี้:\n\n" .
                "• 🏷️ โค้ด `NEWMEMBER5`: รับส่วนลด 5% ทุกรายการ สำหรับสมาชิกใหม่\n" .
                "• 🏷️ โค้ด `CATNEWS10`: รับส่วนลด 10% เมื่อสมัครรับจดหมายข่าว\n" .
                "• 🏷️ โค้ด `PURRFECT10`: ส่วนลด 10% สำหรับยอดสั่งซื้อ 25,000฿ ขึ้นไป\n" .
                "• 🎁 ฟรี! Starter Kit เซ็ตของขวัญต้อนรับน้องแมว 11 รายการมูลค่า 4,500 บาท ฟรีทุกออเดอร์!";
            $sample_ids = ['cat_british', 'cat_munchkin'];
            foreach ($sample_ids as $cid) {
                if (isset($cats[$cid])) {
                    $c = $cats[$cid];
                    $cards[] = [
                        'id' => $c['id'],
                        'name' => $c['name'],
                        'breed' => $c['breed'],
                        'price' => $c['price'],
                        'price_fmt' => '฿' . number_format($c['price']),
                        'image' => 'assets/images/' . $c['image'],
                        'hair' => 'ดีลพิเศษลด 5-10%',
                        'highlight' => 'แถมฟรี Starter Kit 11 ชิ้น',
                        'link' => 'welcome_deal.php'
                    ];
                }
            }
            break;

        case 6:
            // สุขภาพ & ประกัน
            $bot_reply_text = "🩺 การันตีมาตรฐานสุขภาพระดับสากลจาก Purrfect Cattery:\n\n" .
                "1. 💉 วัคซีนครบ 2 เข็ม + ถ่ายพยาธิ + หยดยาป้องกันเห็บหมัด\n" .
                "2. 🔬 ตรวจผลแล็บ ปลอดโรคลิวคีเมีย (FeLV) และเอดส์แมว (FIV) 100%\n" .
                "3. 🛡️ การันตีคุ้มครองสุขภาพโรคร้ายแรงนานถึง 180 วันเต็ม\n" .
                "4. 🚗 บริการจัดส่งด้วยรถ Pet Taxi ควบคุมอุณหภูมิถึงหน้าบ้านทั่วประเทศ\n" .
                "5. 👨‍⚕️ ปรึกษาสัตวแพทย์ประจำฟาร์มฟรีตลอดอายุขัยของน้องแมวครับ!";
            break;
    }

    $bot_msg = [
        'id' => 'msg_q_b_' . uniqid(),
        'sender' => 'bot',
        'sender_name' => 'Purrfect Advisor 🐾',
        'text' => $bot_reply_text,
        'cards' => $cards,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $data_store['conversations'][$user_id]['messages'][] = $bot_msg;
    $data_store['conversations'][$user_id]['last_active'] = date('Y-m-d H:i:s');

    saveChats($chat_file, $data_store);

    echo json_encode([
        'status' => 'success',
        'customer_message' => $cust_msg,
        'bot_message' => $bot_msg
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// -------------------------------------------------------------
// Action 4: Admin Get All Conversations & Registered Users
// -------------------------------------------------------------
if ($action === 'admin_get_conversations') {
    if (!$is_admin) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Admin only.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Load registered users from data_users.json
    $users_file = __DIR__ . '/data_users.json';
    $reg_users = [];
    if (file_exists($users_file)) {
        $reg_users = json_decode(file_get_contents($users_file), true) ?: [];
    }

    // Build unified list
    $conversations_list = [];
    $seen_ids = [];

    // 1. Existing conversations
    foreach ($data_store['conversations'] as $uid => $conv) {
        $seen_ids[$uid] = true;
        $last_msg = end($conv['messages']);
        $conversations_list[] = [
            'user_id' => $uid,
            'user_name' => $conv['user_name'] ?? 'ลูกค้า',
            'user_email' => $conv['user_email'] ?? '',
            'is_member' => $conv['is_member'] ?? false,
            'avatar' => $conv['avatar'] ?? 'assets/images/logo.png',
            'unread_admin' => $conv['unread_admin'] ?? 0,
            'last_active' => $conv['last_active'] ?? '2026-09-01 00:00:00',
            'last_message' => $last_msg ? $last_msg['text'] : '',
            'message_count' => count($conv['messages'] ?? [])
        ];
    }

    // 2. Registered members who haven't chatted yet
    foreach ($reg_users as $u) {
        $uid = $u['id'] ?? '';
        if ($uid && !isset($seen_ids[$uid])) {
            $conversations_list[] = [
                'user_id' => $uid,
                'user_name' => $u['fullname'] ?? ($u['username'] ?? 'สมาชิก'),
                'user_email' => $u['email'] ?? '',
                'is_member' => true,
                'avatar' => $u['avatar'] ?? 'assets/images/logo.png',
                'unread_admin' => 0,
                'last_active' => $u['registered_at'] ?? '2026-09-01 00:00:00',
                'last_message' => '(สมาชิกยังไม่มีประวัติการแชท - คลิกเพื่อเริ่มคุย)',
                'message_count' => 0
            ];
        }
    }

    // Sort by last_active DESC
    usort($conversations_list, function($a, $b) {
        return strcmp($b['last_active'], $a['last_active']);
    });

    echo json_encode([
        'status' => 'success',
        'conversations' => $conversations_list
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// -------------------------------------------------------------
// Action 5: Admin Sends Message to a specific User
// -------------------------------------------------------------
if ($action === 'admin_send_message') {
    if (!$is_admin) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $target_uid = trim($_POST['target_user_id'] ?? '');
    $text = trim($_POST['message'] ?? '');
    $cat_card_id = trim($_POST['cat_card_id'] ?? '');

    if (empty($target_uid) || empty($text)) {
        echo json_encode(['status' => 'error', 'message' => 'กรุณาระบุผู้รับและข้อความ'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Lookup user details from reg_users if not in conversations
    if (!isset($data_store['conversations'][$target_uid])) {
        $users_file = __DIR__ . '/data_users.json';
        $reg_users = file_exists($users_file) ? json_decode(file_get_contents($users_file), true) : [];
        $t_name = 'ลูกค้า ' . $target_uid;
        $t_email = '';
        $t_avatar = 'assets/images/logo.png';
        foreach ($reg_users as $ru) {
            if (($ru['id'] ?? '') === $target_uid) {
                $t_name = $ru['fullname'] ?? ($ru['username'] ?? 'สมาชิก');
                $t_email = $ru['email'] ?? '';
                $t_avatar = $ru['avatar'] ?? 'assets/images/logo.png';
                break;
            }
        }
        ensureConversation($data_store, $target_uid, $t_name, $t_email, true, $t_avatar);
    }

    $cards = [];
    if (!empty($cat_card_id)) {
        global $cats;
        if (isset($cats[$cat_card_id])) {
            $c = $cats[$cat_card_id];
            $cards[] = [
                'id' => $c['id'],
                'name' => $c['name'],
                'breed' => $c['breed'],
                'price' => $c['price'],
                'price_fmt' => '฿' . number_format($c['price']),
                'image' => 'assets/images/' . $c['image'],
                'hair' => $c['hair_label'] ?? 'ขนสั้น',
                'highlight' => $c['highlights'][0] ?? 'แนะนำโดยแอดมิน',
                'link' => 'products.php'
            ];
        }
    }

    $msg = [
        'id' => 'msg_adm_' . uniqid(),
        'sender' => 'admin',
        'sender_name' => '👨‍💼 แอดมิน Purrfect Shop',
        'text' => htmlspecialchars($text, ENT_QUOTES, 'UTF-8'),
        'cards' => $cards,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $data_store['conversations'][$target_uid]['messages'][] = $msg;
    $data_store['conversations'][$target_uid]['last_active'] = date('Y-m-d H:i:s');
    $data_store['conversations'][$target_uid]['unread_user'] = ($data_store['conversations'][$target_uid]['unread_user'] ?? 0) + 1;

    saveChats($chat_file, $data_store);

    echo json_encode([
        'status' => 'success',
        'message' => 'ส่งข้อความสำเร็จ',
        'data' => $msg
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action'], JSON_UNESCAPED_UNICODE);
?>
