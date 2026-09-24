<?php
/**
 * Purrfect Cat Cyber Shop
 * Template 2: Content รูปภาพสำหรับส่ง Email หลังจากลูกค้า สมัครสมาชิก
 * (Theme: Luxury Midnight Slate & Gold • VIP Membership Onboarding Pack)
 * EmailJS Template ID: template_xt7cq3g
 */

require_once __DIR__ . '/data.php';

/**
 * ฟังก์ชันสร้างเนื้อหา HTML Email สำหรับส่งหลังลูกค้าสมัครสมาชิกใหม่
 * สไตล์: Luxury Black & Gold VIP Club (หรูหรา พรีเมียม เอกสิทธิ์เฉพาะสมาชิก)
 */
function renderMemberWelcomeEmail($recipient_name = 'คุณสุพรรษา วงศ์สวัสดิ์', $recipient_email = 'customer.vip@gmail.com', $voucher_code = 'WELCOME15', $member_id = '', $sender_info = []) {
    $email_base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000');
    $recipient_name = htmlspecialchars($recipient_name);
    $recipient_email = htmlspecialchars($recipient_email);
    $voucher_code = htmlspecialchars($voucher_code);
    if (empty($member_id)) {
        $member_id = strtoupper(substr(md5($recipient_email), 0, 8));
    }

    $sender_name = !empty($sender_info['name']) ? htmlspecialchars($sender_info['name']) : 'Purrfect Cattery & Boutique 🐾';
    $sender_email = !empty($sender_info['email']) ? htmlspecialchars($sender_info['email']) : 'purrfect.cattery.shop@gmail.com';
    $sender_avatar = !empty($sender_info['avatar']) ? $sender_info['avatar'] : 'assets/images/logo.png';
    $sender_avatar_url = (strpos($sender_avatar, 'http') === 0) ? $sender_avatar : ($email_base_url . '/' . ltrim($sender_avatar, '/'));

    // Smart CDN Image Fallback: When running on localhost, Gmail cannot reach localhost:8000,
    // so we provide crystal-clear public HTTPS CDN images so cats and logos display in email clients 100%!
    $is_local = (strpos($email_base_url, 'localhost') !== false || strpos($email_base_url, '127.0.0.1') !== false);
    $logo_img_url = $is_local ? 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200&auto=format&fit=crop&q=80' : "{$email_base_url}/assets/images/logo.png";
    $cat_scottish_url = $is_local ? 'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=600&auto=format&fit=crop&q=80' : "{$email_base_url}/assets/images/cat_scottish.jpg";
    $cat_british_url = $is_local ? 'https://images.unsplash.com/photo-1592194996308-7b43878e84a6?w=600&auto=format&fit=crop&q=80' : "{$email_base_url}/assets/images/cat_british.jpg";
    $cat_ragdoll_url = $is_local ? 'https://images.unsplash.com/photo-1543852786-1cf6624b9987?w=600&auto=format&fit=crop&q=80' : "{$email_base_url}/assets/images/cat_ragdoll.jpg";

    if ($is_local && (strpos($sender_avatar_url, 'localhost') !== false || strpos($sender_avatar_url, '127.0.0.1') !== false)) {
        $sender_avatar_url = 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?w=200&auto=format&fit=crop&q=80';
    }

    $join_date = date('d/m/Y');

    return <<<HTML
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👑 ยินดีต้อนรับสมาชิก VIP - Purrfect Shop</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0B0F19; font-family: 'Sukhumvit Set', 'Prompt', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #1E293B;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #0B0F19; padding: 25px 0 50px 0;">
        <tr>
            <td align="center">
                <!-- Main Email Card: Luxury Midnight & Gold Border Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 640px; background-color: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.5); border: 2px solid #D97706;">
                    
                    <!-- Top VIP Club Gold Bar -->
                    <tr>
                        <td style="background: linear-gradient(90deg, #78350F 0%, #D97706 50%, #B45309 100%); padding: 10px 25px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="left">
                                        <span style="font-size: 11px; font-weight: 900; color: #FEF3C7; letter-spacing: 2px; text-transform: uppercase;">
                                            👑 OFFICIAL VIP MEMBER ONBOARDING PACK
                                        </span>
                                    </td>
                                    <td align="right">
                                        <span style="font-size: 11px; font-weight: 800; color: #FFFBEB; letter-spacing: 1px;">
                                            LEVEL: VIP GOLD
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Luxury Midnight Header with 3D-styled Digital VIP Member Card -->
                    <tr>
                        <td align="center" style="background: linear-gradient(145deg, #0F172A 0%, #1E1B4B 50%, #0F172A 100%); padding: 35px 25px 30px 25px; text-align: center;">
                            <img src="{$logo_img_url}" alt="Purrfect Shop Logo" width="80" height="80" style="display: block; margin: 0 auto 18px auto; border-radius: 50%; border: 3px solid #F59E0B; box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4); object-fit: cover;">
                            
                            <!-- Digital VIP Metal Card Graphic Simulation -->
                            <div style="display: inline-block; background: linear-gradient(135deg, #1F2937 0%, #111827 50%, #030712 100%); border: 2px solid #F59E0B; border-radius: 16px; padding: 18px 22px; margin-bottom: 20px; text-align: left; box-shadow: 0 10px 30px rgba(0,0,0,0.6), inset 0 1px 1px rgba(245,158,11,0.5); max-width: 340px; width: 100%;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td>
                                            <!-- Smart Chip Graphic Simulation -->
                                            <div style="width: 38px; height: 26px; background: linear-gradient(135deg, #FCD34D 0%, #D97706 100%); border-radius: 6px; border: 1px solid #78350F; margin-bottom: 10px;"></div>
                                        </td>
                                        <td align="right" valign="top">
                                            <span style="font-size: 11px; font-weight: 900; color: #FCD34D; letter-spacing: 1.5px; text-transform: uppercase;">
                                                ⭐ VIP MEMBER
                                            </span>
                                            <div style="font-size: 18px;">🐾</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 4px 0 10px 0;">
                                            <div style="font-family: 'Consolas', monospace; font-size: 18px; font-weight: 900; color: #FFFFFF; letter-spacing: 3px;">
                                                #VIP-{$member_id}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div style="font-size: 9px; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px;">MEMBER NAME</div>
                                            <div style="font-size: 13px; font-weight: 800; color: #FEF3C7;">
                                                คุณ{$recipient_name}
                                            </div>
                                        </td>
                                        <td align="right">
                                            <div style="font-size: 9px; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px;">JOIN DATE</div>
                                            <div style="font-size: 11px; font-weight: 700; color: #FCD34D;">
                                                {$join_date}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <h1 style="color: #FFFFFF; font-size: 26px; font-weight: 800; margin: 0 0 8px 0; line-height: 1.35; text-shadow: 0 2px 4px rgba(0,0,0,0.4);">
                                ยินดีต้อนรับสู่ครอบครัว Purrfect Shop! 🎉
                            </h1>
                            <p style="color: #CBD5E1; font-size: 14.5px; margin: 0 auto; max-width: 500px; line-height: 1.6;">
                                เรียน คุณ <strong>{$recipient_name}</strong> บัญชีสมาชิกของคุณเปิดใช้งานเรียบร้อยแล้ว พร้อมรับ <strong>แพ็กเกจของขวัญต้อนรับสมาชิกใหม่มูลค่ารวม 5,500 บาท</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Distinct Voucher: Gold Foil Luxury Ticket -->
                    <tr>
                        <td style="padding: 26px 24px 10px 24px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 50%, #FDE68A 100%); border: 2.5px solid #F59E0B; border-radius: 16px; text-align: center; padding: 24px 20px; box-shadow: 0 6px 20px rgba(245, 158, 11, 0.25);">
                                <tr>
                                    <td>
                                        <div style="display: inline-block; background: #78350F; color: #FEF3C7; font-size: 11px; font-weight: 900; padding: 5px 16px; border-radius: 999px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 10px;">
                                            👑 EXCLUSIVE WELCOME CODE &bull; ลด 15%
                                        </div>
                                        <div style="font-family: 'Consolas', 'Courier New', monospace; font-size: 34px; font-weight: 900; color: #B45309; letter-spacing: 5px; margin: 6px 0; text-shadow: 0 2px 4px rgba(180, 83, 9, 0.25);">
                                            {$voucher_code}
                                        </div>
                                        <div style="font-size: 16px; font-weight: 800; color: #78350F; margin-bottom: 4px;">
                                            ลดทันที 15% จากค่าสินสอดน้องแมวทุกสายพันธุ์ในร้าน
                                        </div>
                                        <div style="font-size: 12px; color: #047857; font-weight: 700;">
                                            ⏰ โค้ดนี้มีอายุ 48 ชั่วโมงนับจากเวลาที่สมัครสมาชิก &bull; ไม่มีขั้นต่ำ
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 4 Exclusive VIP Member Privileges Grid -->
                    <tr>
                        <td style="padding: 16px 24px 22px 24px;">
                            <div style="font-size: 16px; font-weight: 800; color: #0F172A; margin-bottom: 14px; text-align: center;">
                                👑 4 เอกสิทธิ์เฉพาะสมาชิก VIP เท่านั้น:
                            </div>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td width="50%" valign="top" style="padding: 5px;">
                                        <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; padding: 14px 12px;">
                                            <div style="display: flex; gap: 8px; align-items: flex-start;">
                                                <span style="font-size: 24px; line-height: 1;">🩺</span>
                                                <div>
                                                    <div style="font-weight: 800; font-size: 12.5px; color: #0F172A; margin-bottom: 2px;">ตรวจแล็บ & สุขภาพฟรี</div>
                                                    <div style="font-size: 11px; color: #64748B; line-height: 1.35;">ตรวจ FIV/FeLV ลบ 100% พร้อมใบแพทย์ (มูลค่า 2,500.-)</div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td width="50%" valign="top" style="padding: 5px;">
                                        <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; padding: 14px 12px;">
                                            <div style="display: flex; gap: 8px; align-items: flex-start;">
                                                <span style="font-size: 24px; line-height: 1;">🚐</span>
                                                <div>
                                                    <div style="font-weight: 800; font-size: 12.5px; color: #0F172A; margin-bottom: 2px;">VIP Chauffeur แอร์ฟรี</div>
                                                    <div style="font-size: 11px; color: #64748B; line-height: 1.35;">พี่เลี้ยงดูแลตลอดทาง ส่งถึงหน้าบ้านฟรี (มูลค่า 1,500.-)</div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" valign="top" style="padding: 5px;">
                                        <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; padding: 14px 12px;">
                                            <div style="display: flex; gap: 8px; align-items: flex-start;">
                                                <span style="font-size: 24px; line-height: 1;">🛡️</span>
                                                <div>
                                                    <div style="font-weight: 800; font-size: 12.5px; color: #0F172A; margin-bottom: 2px;">การันตีสุขภาพ 180 วัน</div>
                                                    <div style="font-size: 11px; color: #64748B; line-height: 1.35;">ครอบคลุมโรคพันธุกรรม ปรึกษาสัตวแพทย์ 24 ชม.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td width="50%" valign="top" style="padding: 5px;">
                                        <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; padding: 14px 12px;">
                                            <div style="display: flex; gap: 8px; align-items: flex-start;">
                                                <span style="font-size: 24px; line-height: 1;">🎂</span>
                                                <div>
                                                    <div style="font-weight: 800; font-size: 12.5px; color: #0F172A; margin-bottom: 2px;">ของขวัญวันเกิดแมวฟรี</div>
                                                    <div style="font-size: 11px; color: #64748B; line-height: 1.35;">รับ Birthday Box ขนมและของเล่นส่งฟรีทุกปี</div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Featured Kittens Gallery (Purebred & Pedigree Showcase) -->
                    <tr>
                        <td style="padding: 16px 24px 22px 24px; border-top: 1px solid #F1F5F9;">
                            <div style="font-size: 16px; font-weight: 800; color: #0F172A; margin-bottom: 4px; text-align: center;">
                                🐾 น้องแมวสายพันธุ์แท้ 100% พร้อมย้ายบ้านทันที
                            </div>
                            <div style="font-size: 12.5px; color: #64748B; text-align: center; margin-bottom: 16px;">
                                มีใบเพ็ดดีกรีรับรองสายพันธุ์ครบถ้วน ฉีดวัคซีนแล้ว 2 เข็ม
                            </div>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <!-- Cat 1 -->
                                    <td width="32%" valign="top" style="padding: 4px;">
                                        <div style="border: 1.5px solid #FDE68A; border-radius: 14px; overflow: hidden; background: #FFFFFF; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                                            <img src="{$cat_scottish_url}" alt="Scottish Fold" width="100%" style="height: 140px; object-fit: cover; display: block;">
                                            <div style="padding: 10px 8px;">
                                                <span style="display: inline-block; background: #FEF3C7; color: #B45309; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-bottom: 4px;">🥇 CFA Pedigree</span>
                                                <div style="font-weight: 800; font-size: 13px; color: #0F172A;">น้องโมจิ</div>
                                                <div style="font-size: 11px; color: #64748B; margin-bottom: 4px;">สก็อตติช โฟลด์ หูพับ</div>
                                                <div>
                                                    <span style="font-size: 10px; color: #94A3B8; text-decoration: line-through;">22,000 ฿</span>
                                                    <span style="font-size: 14px; font-weight: 900; color: #D97706;">18,700 ฿</span>
                                                </div>
                                                <a href="{$email_base_url}/products.php" style="display: block; background: #D97706; color: #FFFFFF; text-decoration: none; font-size: 11px; font-weight: 800; padding: 6px 0; border-radius: 6px; margin-top: 8px;">
                                                    รับเลี้ยงน้อง 🐾
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Cat 2 -->
                                    <td width="32%" valign="top" style="padding: 4px;">
                                        <div style="border: 1.5px solid #FDE68A; border-radius: 14px; overflow: hidden; background: #FFFFFF; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                                            <img src="{$cat_british_url}" alt="British Shorthair" width="100%" style="height: 140px; object-fit: cover; display: block;">
                                            <div style="padding: 10px 8px;">
                                                <span style="display: inline-block; background: #FEF3C7; color: #B45309; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-bottom: 4px;">🥇 WCF Pedigree</span>
                                                <div style="font-weight: 800; font-size: 13px; color: #0F172A;">น้องบราวนี่</div>
                                                <div style="font-size: 11px; color: #64748B; margin-bottom: 4px;">บริติช ช็อตแฮร์ แท้</div>
                                                <div>
                                                    <span style="font-size: 10px; color: #94A3B8; text-decoration: line-through;">25,000 ฿</span>
                                                    <span style="font-size: 14px; font-weight: 900; color: #D97706;">21,250 ฿</span>
                                                </div>
                                                <a href="{$email_base_url}/products.php" style="display: block; background: #D97706; color: #FFFFFF; text-decoration: none; font-size: 11px; font-weight: 800; padding: 6px 0; border-radius: 6px; margin-top: 8px;">
                                                    รับเลี้ยงน้อง 🐾
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Cat 3 -->
                                    <td width="32%" valign="top" style="padding: 4px;">
                                        <div style="border: 1.5px solid #FDE68A; border-radius: 14px; overflow: hidden; background: #FFFFFF; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                                            <img src="{$cat_ragdoll_url}" alt="Ragdoll" width="100%" style="height: 140px; object-fit: cover; display: block;">
                                            <div style="padding: 10px 8px;">
                                                <span style="display: inline-block; background: #FEF3C7; color: #B45309; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-bottom: 4px;">🥇 TICA Champion</span>
                                                <div style="font-weight: 800; font-size: 13px; color: #0F172A;">น้องสโนว์</div>
                                                <div style="font-size: 11px; color: #64748B; margin-bottom: 4px;">แร็กดอลล์ ตาสีฟ้าฟู</div>
                                                <div>
                                                    <span style="font-size: 10px; color: #94A3B8; text-decoration: line-through;">32,000 ฿</span>
                                                    <span style="font-size: 14px; font-weight: 900; color: #D97706;">27,200 ฿</span>
                                                </div>
                                                <a href="{$email_base_url}/products.php" style="display: block; background: #D97706; color: #FFFFFF; text-decoration: none; font-size: 11px; font-weight: 800; padding: 6px 0; border-radius: 6px; margin-top: 8px;">
                                                    รับเลี้ยงน้อง 🐾
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 3 Onboarding Steps -->
                    <tr>
                        <td style="padding: 10px 24px 22px 24px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background: #FFFBEB; border: 1.5px solid #FDE68A; border-radius: 14px; padding: 16px;">
                                <tr>
                                    <td>
                                        <div style="font-size: 13px; font-weight: 900; color: #92400E; margin-bottom: 6px; text-transform: uppercase;">
                                            ⚡ ขั้นตอนง่ายๆ ในการใช้สิทธิ์รับเลี้ยงน้องแมว:
                                        </div>
                                        <div style="font-size: 12.5px; color: #451A03; line-height: 1.6;">
                                            1. เข้าสู่ระบบบัญชีของคุณที่ <a href="{$email_base_url}/login.php" style="color: #B45309; font-weight: 800;">Purrfect Shop Login</a><br>
                                            2. เลือกชมน้องแมวที่ถูกชะตาแล้วกดเพิ่มลงในตะกร้าการจอง<br>
                                            3. กรอกโค้ด <strong>{$voucher_code}</strong> ในหน้าสรุปยอด เพื่อรับส่วนลด 15% ทันที!
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Call To Action Button -->
                    <tr>
                        <td align="center" style="padding: 5px 24px 28px 24px; text-align: center;">
                            <a href="{$email_base_url}/welcome_deal.php" style="display: inline-block; background: linear-gradient(135deg, #D97706 0%, #B45309 100%); color: #FFFFFF; font-size: 16px; font-weight: 900; text-decoration: none; padding: 14px 36px; border-radius: 999px; box-shadow: 0 6px 20px rgba(180, 83, 9, 0.4);">
                                👑 เข้าสู่หน้าดีลพิเศษสมาชิก VIP &rarr;
                            </a>
                            <div style="font-size: 11.5px; color: #64748B; margin-top: 10px;">
                                รับสิทธิ์ปรึกษาสัตวแพทย์และผู้เชี่ยวชาญก่อนตัดสินใจรับเลี้ยงฟรี
                            </div>
                        </td>
                    </tr>

                    <!-- Personal Sign-off & Store Seal -->
                    <tr>
                        <td style="background: #0F172A; border-top: 2px solid #D97706; padding: 25px 24px; text-align: center; color: #CBD5E1;">
                            <img src="{$sender_avatar_url}" alt="Sender Avatar" width="55" height="55" style="border-radius: 50%; border: 2px solid #F59E0B; margin-bottom: 8px; object-fit: cover;">
                            <div style="font-size: 13px; font-weight: 800; color: #FEF3C7; margin-bottom: 2px;">
                                {$sender_name}
                            </div>
                            <div style="font-size: 11px; color: #94A3B8; margin-bottom: 12px;">
                                Founder & Head of Customer Relations &bull; Purrfect Cattery & Boutique
                            </div>
                            <div style="font-size: 11px; color: #64748B; line-height: 1.6;">
                                อีเมลติดต่อทีมงาน: <a href="mailto:{$sender_email}" style="color: #FCD34D;">{$sender_email}</a><br>
                                &copy; 2026 Purrfect Shop. พัฒนาโดย Purrfect Cattery Team 🐾
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
HTML;
}

// Standalone Web Preview Router
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $currUser = getCurrentUser();
    $recipient_name = trim($_GET['name'] ?? ($currUser['fullname'] ?? 'คุณสุพรรษา วงศ์สวัสดิ์'));
    $recipient_email = trim($_GET['email'] ?? ($currUser['email'] ?? 'customer.vip@gmail.com'));
    $voucher_code = trim($_GET['code'] ?? 'WELCOME15');
    $member_id = trim($_GET['member_id'] ?? strtoupper(substr(md5($recipient_email), 0, 8)));
    $current_view = $_GET['view'] ?? 'desktop';

    $success_msg = "";
    $error_msg = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_test_welcome_email') {
        $target_email = trim($_POST['target_email'] ?? $recipient_email);
        $target_name = trim($_POST['target_name'] ?? $recipient_name);

        if (empty($target_email) || !filter_var($target_email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "กรุณากรอกอีเมลที่ถูกต้องสำหรับทดสอบ";
        } else {
            $html_content = renderMemberWelcomeEmail($target_name, $target_email, $voucher_code, $member_id);
            $subject = "👑 [ต้อนรับสมาชิก VIP] Purrfect Shop + โค้ดส่วนลด 15% ({$voucher_code})";

            $res = sendActualEmail($target_email, $target_name, $subject, $html_content, $voucher_code, '', [], 'welcome');
            if (!empty($res['delivery_status']) && $res['delivery_status'] === 'DELIVERED_VIA_EMAILJS') {
                $success_msg = "🎉 ยิงส่งอีเมลต้อนรับ VIP ไปยัง {$target_email} ผ่าน EmailJS (Template: template_xt7cq3g) สำเร็จจริง 100%! ตรวจสอบได้ที่ Inbox หรือ Spam ของคุณทันที";
            } elseif (!empty($res['smtp_result']) && $res['smtp_result']['success']) {
                $success_msg = "🎉 ส่งอีเมลต้อนรับ VIP ไปยัง {$target_email} ผ่าน Live SMTP สำเร็จจริง 100%! ตรวจสอบได้ที่ Inbox ของคุณทันที";
            } elseif (!empty($res['smtp_result']) && !$res['smtp_result']['success']) {
                $error_msg = "❌ ส่งผ่าน SMTP ล้มเหลว: " . $res['smtp_result']['message'] . " (แต่ได้บันทึกลง Outbox จำลองแล้ว)";
            } elseif (!empty($res['delivery_status']) && $res['delivery_status'] === 'EMAILJS_FAILED') {
                $error_msg = "❌ ส่งผ่าน EmailJS ล้มเหลว: " . ($res['server_notice'] ?? '') . " (บันทึกลง Outbox จำลองแล้ว)";
            } else {
                $success_msg = "✓ บันทึกการส่งในโหมด Outbox จำลองสำเร็จ! (เปิดใช้งาน EmailJS หรือ SMTP เพื่อส่งเข้ากล่องจดหมายจริง)";
            }
        }
    }

    $rendered_email_html = renderMemberWelcomeEmail($recipient_name, $recipient_email, $voucher_code, $member_id);
    require_once __DIR__ . '/header.php';
?>
<div style="max-width: 1080px; margin: 2rem auto; padding: 0 1rem;">
    <div style="background: var(--bg-card); border-radius: 16px; border: 1.5px solid var(--border-color); padding: 1.5rem; margin-bottom: 2rem; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <span style="display: inline-block; background: #FEF3C7; color: #92400E; font-size: 0.82rem; font-weight: 800; padding: 4px 12px; border-radius: 999px; border: 1px solid #FDE68A; margin-bottom: 4px;">
                    👑 Template 2: Member Welcome &bull; EmailJS: template_xt7cq3g
                </span>
                <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                    พรีวิวแม่แบบอีเมล: สำหรับส่งหลังลูกค้า สมัครสมาชิกใหม่
                </h2>
                <p style="color: var(--text-muted); font-size: 0.88rem; margin: 4px 0 0 0;">
                    ธีม <strong>Luxury Midnight & Gold VIP Club</strong> ต้อนรับสมาชิกใหม่พร้อมบัตรสมาชิกดิจิทัลและโค้ด 15%
                </p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="?view=desktop" class="btn btn-sm <?php echo $current_view === 'desktop' ? 'btn-primary' : 'btn-secondary'; ?>">🖥️ Desktop</a>
                <a href="?view=mobile" class="btn btn-sm <?php echo $current_view === 'mobile' ? 'btn-primary' : 'btn-secondary'; ?>">📱 Mobile</a>
                <a href="?view=raw" class="btn btn-sm <?php echo $current_view === 'raw' ? 'btn-primary' : 'btn-secondary'; ?>">📄 Raw HTML</a>
                <a href="email_newsletter_subscribe.php" class="btn btn-sm" style="background: #065F46; color: #A7F3D0; font-weight: 800; border: 1px solid #10B981;">
                    📬 สลับไปดูแม่แบบข่าวสาร &rarr;
                </a>
            </div>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div style="background: #ECFDF5; border: 1.5px solid #10B981; border-radius: 10px; padding: 0.8rem 1.2rem; color: #065F46; font-weight: 700; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($success_msg); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div style="background: #FEF2F2; border: 1.5px solid #EF4444; border-radius: 10px; padding: 0.8rem 1.2rem; color: #991B1B; font-weight: 700; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <!-- Quick Test Send Form -->
        <form method="POST" action="email_member_welcome.php?view=<?php echo htmlspecialchars($current_view); ?>" style="background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px; padding: 1rem; display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
            <input type="hidden" name="action" value="send_test_welcome_email">
            <div style="flex: 1; min-width: 220px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">📬 อีเมลสำหรับทดสอบส่งจริง:</label>
                <input type="email" name="target_email" value="<?php echo htmlspecialchars($recipient_email); ?>" required style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-weight: 700;">
            </div>
            <div style="min-width: 160px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">👤 ชื่อผู้รับ:</label>
                <input type="text" name="target_name" value="<?php echo htmlspecialchars($recipient_name); ?>" required style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main);">
            </div>
            <button type="submit" class="btn btn-primary" style="background: #D97706; border-color: #B45309; font-weight: 800; padding: 8px 18px;">
                ⚡ ยิงส่งทดสอบเข้าอีเมลจริง
            </button>
        </form>
    </div>

    <!-- Preview Container -->
    <div style="background: #1E293B; padding: 2rem 1rem; border-radius: 16px; text-align: center;">
        <?php if ($current_view === 'mobile'): ?>
            <div style="width: 375px; margin: 0 auto; background: #0B0F19; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); overflow: hidden; border: 8px solid #334155;">
                <iframe srcdoc="<?php echo htmlspecialchars($rendered_email_html); ?>" style="width: 100%; height: 680px; border: none;"></iframe>
            </div>
        <?php elseif ($current_view === 'raw'): ?>
            <pre style="text-align: left; background: #0F172A; color: #F8FAFC; padding: 1.5rem; border-radius: 12px; overflow-x: auto; font-size: 0.82rem; font-family: monospace; max-height: 600px;"><?php echo htmlspecialchars($rendered_email_html); ?></pre>
        <?php else: ?>
            <div style="max-width: 700px; margin: 0 auto; background: #0B0F19; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.4); overflow: hidden; padding: 10px;">
                <iframe srcdoc="<?php echo htmlspecialchars($rendered_email_html); ?>" style="width: 100%; height: 850px; border: none; border-radius: 12px;"></iframe>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
    require_once __DIR__ . '/footer.php';
}
