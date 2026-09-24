<?php
/**
 * generate_github_pages.php
 * Static HTML generator for GitHub Pages export (https://github.com/Patches660/Purrfect-Shop)
 */

$rootDir = __DIR__;
$exportDir = $rootDir . '/GITHUB_PAGES_EXPORT';
$docsDir = $rootDir . '/docs';

// Ensure export directories exist
if (!is_dir($exportDir)) {
    mkdir($exportDir, 0777, true);
}
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0777, true);
}

// Copy assets folder recursively
function copyDir($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0777, true);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copyDir($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

copyDir($rootDir . '/assets', $exportDir . '/assets');
copyDir($rootDir . '/assets', $docsDir . '/assets');

// Pages to render
$pages = [
    'index.php' => 'index.html',
    'products.php' => 'products.html',
    'recommend.php' => 'recommend.html',
    'welcome_deal.php' => 'welcome_deal.html',
    'cart.php' => 'cart.html',
    'tracking.php' => 'tracking.html',
    'creator.php' => 'creator.html',
    'subscribe.php' => 'subscribe.html',
    'login.php' => 'login.html',
    'register.php' => 'register.html',
    'order_letter.php' => 'order_letter.html',
];

// Helper to convert php links to html in output
function convertPhpToHtmlLinks($html) {
    // Replace .php with .html in href and action attributes
    $patterns = [
        '/href="index\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="index.html$2"',
        '/href="products\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="products.html$2"',
        '/href="recommend\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="recommend.html$2"',
        '/href="welcome_deal\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="welcome_deal.html$2"',
        '/href="cart\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="cart.html$2"',
        '/href="tracking\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="tracking.html$2"',
        '/href="creator\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="creator.html$2"',
        '/href="subscribe\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="subscribe.html$2"',
        '/href="login\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="login.html$2"',
        '/href="register\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="register.html$2"',
        '/href="order_letter\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="order_letter.html$2"',
        '/href="profile\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="index.html#profile"',
        '/href="admin\.php(\?[^"]*)?(#[^"]*)?"/' => 'href="index.html#admin"',
        '/<form method="POST" action="login\.php">/' => '<form onsubmit="handleStaticLogin(event)"><script>function handleStaticLogin(e){e.preventDefault();var id=document.getElementById(\'identifier\').value;var pass=document.getElementById(\'password\').value;var res=clientLogin(id,pass);if(res.success){alert(\'✓ เข้าสู่ระบบสำเร็จ! ยินดีต้อนรับคุณ \' + res.user.username + \' 🐾\');window.location.href=\'index.html\';}else{alert(\'⚠️ \' + res.message);}}</script>',
        '/<form method="POST" action="register\.php">/' => '<form onsubmit="handleStaticRegister(event)"><script>function handleStaticRegister(e){e.preventDefault();var fn=document.getElementById(\'fullname\').value;var un=document.getElementById(\'username\').value;var em=document.getElementById(\'email\').value;var ph=document.getElementById(\'phone\').value;var pw=document.getElementById(\'password\').value;var res=clientRegister(fn,un,em,ph,pw);if(res.success){alert(\'✓ สมัครสมาชิกสำเร็จ! ยินดีต้อนรับคุณ \' + res.user.username + \' 🐾 (รับส่วนลด 5% ทันที)\');window.location.href=\'welcome_deal.html\';}else{alert(\'⚠️ \' + res.message);}}</script>',
        '/action="index\.php(#[^"]*)?"/' => 'action="index.html$1" onsubmit="event.preventDefault(); alert(\'ระบบเดโมบน GitHub Pages: บันทึกข้อมูลจำลองสำเร็จ 🐾\');"',
        '/action="products\.php(#[^"]*)?"/' => 'action="products.html$1" onsubmit="event.preventDefault(); alert(\'เพิ่มน้องแมวลงตะกร้าจำลองเรียบร้อยแล้ว 🐾\');"',
        '/action="recommend\.php(#[^"]*)?"/' => 'action="recommend.html$1"',
        '/action="cart\.php(#[^"]*)?"/' => 'action="cart.html$1"',
        '/action="tracking\.php(#[^"]*)?"/' => 'action="tracking.html$1"',
        '/action="subscribe\.php(#[^"]*)?"/' => 'action="subscribe.html$1" onsubmit="event.preventDefault(); alert(\'ขอบคุณที่สมัครรับข่าวสาร Purrfect Shop 🐾\');"',
    ];

    foreach ($patterns as $pattern => $replacement) {
        $html = preg_replace($pattern, $replacement, $html);
    }

    return $html;
}

// Copy JSON database files to export
$jsonFiles = ['data_users.json', 'data_chats.json', 'data_orders.json', 'data_newsletters.json'];
foreach ($jsonFiles as $jf) {
    if (file_exists($rootDir . '/' . $jf)) {
        copy($rootDir . '/' . $jf, $exportDir . '/' . $jf);
        copy($rootDir . '/' . $jf, $docsDir . '/' . $jf);
        echo "✓ Copied JSON Data: {$jf}\n";
    }
}

// Render each PHP page by executing CLI php
foreach ($pages as $phpFile => $htmlFile) {
    if (!file_exists($rootDir . '/' . $phpFile)) {
        continue;
    }

    $cmd = 'php "' . $rootDir . '/' . $phpFile . '"';
    $renderedHtml = shell_exec($cmd);

    if ($renderedHtml) {
        $convertedHtml = convertPhpToHtmlLinks($renderedHtml);

        // Save to GITHUB_PAGES_EXPORT
        file_put_contents($exportDir . '/' . $htmlFile, $convertedHtml);

        // Save to docs/
        file_put_contents($docsDir . '/' . $htmlFile, $convertedHtml);

        // Also save to root if it's index.html
        if ($htmlFile === 'index.html' || $htmlFile === 'products.html' || $htmlFile === 'recommend.html') {
            file_put_contents($rootDir . '/' . $htmlFile, $convertedHtml);
        }

        echo "✓ Exported: {$phpFile} -> {$htmlFile}\n";
    }
}

// Copy email_showcase.html if exists
if (file_exists($rootDir . '/email_showcase.html')) {
    copy($rootDir . '/email_showcase.html', $exportDir . '/email_showcase.html');
    copy($rootDir . '/email_showcase.html', $docsDir . '/email_showcase.html');
    echo "✓ Copied: email_showcase.html\n";
}

// Add a GitHub Pages README in GITHUB_PAGES_EXPORT
$readmeContent = <<<MARKDOWN
# 🐱 Purrfect Shop - Static Live Preview for GitHub Pages

ยินดีต้อนรับสู่ **Purrfect Shop Cattery & Boutique** (เวอร์ชัน Static Web Preview สำหรับ GitHub Pages)

🔗 **GitHub Repository:** [https://github.com/Patches660/Purrfect-Shop](https://github.com/Patches660/Purrfect-Shop)

---

## 🌟 หน้าเว็บเดโมที่สามารถเข้าชมได้โดยตรง:
- 🏠 **[หน้าแรก (Homepage)](index.html)** - แบนเนอร์, หมวดหมู่น้องแมว, สิทธิพิเศษสมาชิก
- 🐱 **[น้องแมวทั้งหมด (Cat Catalog)](products.html)** - แคตตาล็อกสายพันธุ์ ค้นหาและตัวกรองราคา/อายุ/ขน
- 🌟 **[ระบบแนะนำสายพันธุ์ (Smart Matcher 6D)](recommend.html)** - แบบทดสอบประมวลผลสายพันธุ์ที่ใช่ 6 มิติ
- 🎁 **[ดีลสมาชิกใหม่ (Welcome Deals)](welcome_deal.html)** - โปรโมชันลด 5% และ Starter Kit
- 🛒 **[ตะกร้ารับเลี้ยง & สั่งซื้อ (Cart & Checkout)](cart.html)** - จำลองการสั่งซื้อและช่องทางชำระเงิน
- 📍 **[ระบบติดตามการจัดส่ง (Tracking System)](tracking.html)** - ความคืบหน้า 4 ระดับและข้อมูลคนขับ
- 👨‍💻 **[ผู้จัดทำ (Creator Profile)](creator.html)** - ข้อมูลผู้พัฒนาระบบ
- 📬 **[รับข่าวสาร (Newsletter Subscribe)](subscribe.html)** - สมัครรับจดหมายข่าว
- 🔑 **[เข้าสู่ระบบ (Login)](login.html)** / ✨ **[สมัครสมาชิก (Register)](register.html)**
- 💌 **[Showcase แม่แบบอีเมล (Email Showcase)](email_showcase.html)**

---

## 🚀 วิธีเปิดใช้งาน GitHub Pages บน GitHub Repo:
1. เข้าไปที่ **Settings** ของ Repository [https://github.com/Patches660/Purrfect-Shop](https://github.com/Patches660/Purrfect-Shop)
2. ไปที่เมนู **Pages** ทางซ้ายมือ
3. ในส่วน **Build and deployment > Source**:
   - เลือก Branch: `main`
   - เลือก Folder: `/docs` (หรือ `/ (root)` หากอัปโหลดไฟล์จากโฟลเดอร์นี้ไปไว้ที่ root)
4. กด **Save** ระบบจะสร้างลิงก์สำหรับเข้าชมออนไลน์ เช่น: `https://patches660.github.io/Purrfect-Shop/`
MARKDOWN;

file_put_contents($exportDir . '/README.md', $readmeContent);
file_put_contents($docsDir . '/README.md', $readmeContent);

echo "\n✨ GitHub Pages Export Complete!\n";
