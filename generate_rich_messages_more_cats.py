import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

base_dir = os.path.dirname(os.path.abspath(__file__))
line_dir = os.path.join(base_dir, "LINE_ASSETS")
dir_rich_msg = os.path.join(line_dir, "1_RICH_MESSAGE")
assets_img_dir = os.path.join(base_dir, "assets", "images")

os.makedirs(dir_rich_msg, exist_ok=True)

# Fonts
font_bold_path = "C:/Windows/Fonts/LeelaUIb.ttf" if os.path.exists("C:/Windows/Fonts/LeelaUIb.ttf") else "C:/Windows/Fonts/tahomabd.ttf"
font_reg_path = "C:/Windows/Fonts/LeelawUI.ttf" if os.path.exists("C:/Windows/Fonts/LeelawUI.ttf") else "C:/Windows/Fonts/tahoma.ttf"

def get_font(size, bold=False):
    p = font_bold_path if bold else font_reg_path
    try:
        return ImageFont.truetype(p, int(size))
    except:
        return ImageFont.load_default()

def create_vertical_grad(w, h, c1, c2):
    img = Image.new("RGBA", (w, h))
    draw = ImageDraw.Draw(img)
    for y in range(h):
        t = y / max(1, h - 1)
        r = int(c1[0] + t * (c2[0] - c1[0]))
        g = int(c1[1] + t * (c2[1] - c1[1]))
        b = int(c1[2] + t * (c2[2] - c1[2]))
        draw.line([(0, y), (w, y)], fill=(r, g, b, 255))
    return img

def create_horizontal_grad(w, h, c1, c2):
    img = Image.new("RGBA", (w, h))
    draw = ImageDraw.Draw(img)
    for x in range(w):
        t = x / max(1, w - 1)
        r = int(c1[0] + t * (c2[0] - c1[0]))
        g = int(c1[1] + t * (c2[1] - c1[1]))
        b = int(c1[2] + t * (c2[2] - c1[2]))
        draw.line([(x, 0), (x, h)], fill=(r, g, b, 255))
    return img

def draw_paw(draw, cx, cy, size, fill=(255, 107, 74, 180)):
    pad_w = int(size * 0.5)
    pad_h = int(size * 0.4)
    draw.ellipse([cx - pad_w//2, cy - pad_h//2, cx + pad_w//2, cy + pad_h//2], fill=fill)
    toe_r = int(size * 0.16)
    draw.ellipse([cx - int(size * 0.3) - toe_r, cy - int(size * 0.3) - toe_r, cx - int(size * 0.3) + toe_r, cy - int(size * 0.3) + toe_r], fill=fill)
    draw.ellipse([cx - int(size * 0.1) - toe_r, cy - int(size * 0.42) - toe_r, cx - int(size * 0.1) + toe_r, cy - int(size * 0.42) + toe_r], fill=fill)
    draw.ellipse([cx + int(size * 0.12) - toe_r, cy - int(size * 0.42) - toe_r, cx + int(size * 0.12) + toe_r, cy - int(size * 0.42) + toe_r], fill=fill)
    draw.ellipse([cx + int(size * 0.32) - toe_r, cy - int(size * 0.3) - toe_r, cx + int(size * 0.32) + toe_r, cy - int(size * 0.3) + toe_r], fill=fill)

def draw_sparkle(draw, cx, cy, r=14, fill=(245, 158, 11)):
    draw.polygon([(cx, cy - r), (cx + 2, cy), (cx, cy + r), (cx - 2, cy)], fill=fill)
    draw.polygon([(cx - r, cy), (cx, cy + 2), (cx + r, cy), (cx, cy - 2)], fill=fill)
    d = int(r * 0.45)
    draw.polygon([(cx - d, cy - d), (cx, cy), (cx + d, cy + d), (cx, cy)], fill=fill)
    draw.polygon([(cx + d, cy - d), (cx, cy), (cx - d, cy + d), (cx, cy)], fill=fill)

def draw_checkmark(draw, cx, cy, size=14, color=(16, 185, 129)):
    draw.line([(cx - size//2, cy), (cx - size//6, cy + size//2), (cx + size//2, cy - size//2)], fill=color, width=3)

def render_rich_message_card(config, output_filename):
    w, h = 1040, 1040
    # Background
    bg = create_vertical_grad(w, h, config['bg_c1'], config['bg_c2'])
    draw = ImageDraw.Draw(bg)

    # Decorative background sparkles & paws
    for p in config.get('paws', []):
        draw_paw(draw, p[0], p[1], p[2], p[3])
    for s in config.get('sparkles', []):
        draw_sparkle(draw, s[0], s[1], s[2], s[3])

    # Top Header Banner (0 to 180)
    hdr_h = 180
    hdr_grad = create_horizontal_grad(w, hdr_h, config['hdr_c1'], config['hdr_c2'])
    bg.paste(hdr_grad, (0, 0))

    # Top Header Badge (Dynamic Width)
    top_badge_font = get_font(18, True)
    t_bbox = draw.textbbox((0, 0), config['badge_top_text'], font=top_badge_font)
    t_text_w = t_bbox[2] - t_bbox[0]
    badge_w = t_text_w + 75
    draw.rounded_rectangle([35, 18, 35 + badge_w, 56], radius=18, fill=config['badge_top_bg'], outline=(255, 255, 255, 180), width=2)
    draw_paw(draw, 58, 37, 22, config['badge_top_icon_color'])
    draw.text((78, 25), config['badge_top_text'], font=top_badge_font, fill=config['badge_top_txtcolor'])

    # Title & Subtitle
    draw.text((35, 68), config['title_th'], font=get_font(44, True), fill=(255, 255, 255))
    draw.text((38, 126), config['subtitle_th'], font=get_font(23, False), fill=config['subtitle_color'])

    # Right Tag Badge (CFA / TICA / WCF - Dynamic Width)
    tag_font = get_font(22, True)
    tag_bbox = draw.textbbox((0, 0), config['tag_right'], font=tag_font)
    tag_text_w = tag_bbox[2] - tag_bbox[0]
    tag_box_w = tag_text_w + 65
    tag_x2 = 1005
    tag_x1 = tag_x2 - tag_box_w
    draw.rounded_rectangle([tag_x1, 26, tag_x2, 78], radius=22, fill=(255, 255, 255), outline=config['accent_color'], width=3)
    draw_sparkle(draw, tag_x1 + 25, 52, 10, config['accent_color'])
    draw.text((tag_x1 + 45, 39), config['tag_right'], font=tag_font, fill=config['accent_color'])

    # Center Cat Image Card (Box: x=40, y=198, w=960, h=510)
    card_x, card_y, card_w, card_h = 40, 198, 960, 510
    
    # Shadow behind photo
    shadow = Image.new("RGBA", (card_w + 20, card_h + 20), (0, 0, 0, 0))
    s_draw = ImageDraw.Draw(shadow)
    s_draw.rounded_rectangle([10, 10, card_w + 10, card_h + 10], radius=32, fill=(0, 0, 0, 55))
    shadow = shadow.filter(ImageFilter.GaussianBlur(12))
    bg.paste(shadow, (card_x - 10, card_y - 10), shadow)

    # Cat image handling
    cat_src_path = os.path.join(assets_img_dir, config['cat_image'])
    cat_box_w = 480
    cat_box_h = card_h

    # Main Card Base
    draw.rounded_rectangle([card_x, card_y, card_x + card_w, card_y + card_h], radius=30, fill=config['card_bg'], outline=config['card_border'], width=3)

    if os.path.exists(cat_src_path):
        cat_img = Image.open(cat_src_path).convert("RGBA")
        src_w, src_h = cat_img.size
        ratio = max(cat_box_w / src_w, cat_box_h / src_h)
        new_w = int(src_w * ratio)
        new_h = int(src_h * ratio)
        cat_img = cat_img.resize((new_w, new_h), Image.Resampling.LANCZOS)
        
        left = (new_w - cat_box_w) // 2
        top = (new_h - cat_box_h) // 2
        cat_cropped = cat_img.crop((left, top, left + cat_box_w, top + cat_box_h))

        mask = Image.new("L", (cat_box_w, cat_box_h), 0)
        m_draw = ImageDraw.Draw(mask)
        m_draw.rounded_rectangle([0, 0, cat_box_w + 30, cat_box_h], radius=30, fill=255)
        bg.paste(cat_cropped, (card_x, card_y), mask)
        
        draw.line([(card_x + cat_box_w, card_y), (card_x + cat_box_w, card_y + card_h)], fill=config['card_border'], width=3)

    # Overlay badge on top of cat photo (Dynamic Width)
    pbadge_font = get_font(21, True)
    pb_bbox = draw.textbbox((0, 0), config['photo_badge'], font=pbadge_font)
    pb_w = pb_bbox[2] - pb_bbox[0] + 65
    draw.rounded_rectangle([card_x + 20, card_y + 20, card_x + 20 + pb_w, card_y + 68], radius=16, fill=(0, 0, 0, 195), outline=(255, 255, 255, 120), width=2)
    draw_paw(draw, card_x + 42, card_y + 44, 20, (255, 220, 100))
    draw.text((card_x + 60, card_y + 30), config['photo_badge'], font=pbadge_font, fill=(255, 235, 140))

    # Right Side of Card: Details & Highlights
    rx = card_x + 505
    ry = card_y + 28

    draw.text((rx, ry), config['right_title'], font=get_font(29, True), fill=config['text_dark'])
    draw.text((rx, ry + 40), config['right_desc'], font=get_font(20, False), fill=config['text_muted'])

    # 3 Highlight Bullet Boxes
    bullet_y = ry + 115
    for idx, (num_str, htitle, hdesc) in enumerate(config['highlights']):
        by = bullet_y + (idx * 105)
        draw.rounded_rectangle([rx, by, rx + 420, by + 90], radius=18, fill=config['bullet_bg'], outline=config['bullet_border'], width=2)
        # Number badge circle
        draw.ellipse([rx + 15, by + 18, rx + 65, by + 68], fill=config['icon_bg'])
        draw.text((rx + 33, by + 26), num_str, font=get_font(24, True), fill=config['icon_color'])
        # Text details
        draw.text((rx + 80, by + 16), htitle, font=get_font(22, True), fill=config['text_dark'])
        draw.text((rx + 80, by + 48), hdesc, font=get_font(18, False), fill=config['text_muted'])

    # Bottom Area: Pricing & CTA Section (y=728 to 1005)
    bot_y = 728
    bot_h = 272

    draw.rounded_rectangle([card_x, bot_y, card_x + card_w, bot_y + bot_h], radius=30, fill=(255, 255, 255), outline=config['card_border'], width=3)

    # Left: Price Info
    px = card_x + 35
    draw.text((px, bot_y + 25), "ค่าสินสอดพิเศษ (พร้อมส่งมอบย้ายบ้าน):", font=get_font(22, False), fill=config['text_muted'])
    
    # Original Price Strike-through
    draw.text((px, bot_y + 64), config['price_original'], font=get_font(26, True), fill=(160, 160, 160))
    orig_bbox = draw.textbbox((px, bot_y + 64), config['price_original'], font=get_font(26, True))
    draw.line([(orig_bbox[0] - 2, (orig_bbox[1] + orig_bbox[3]) // 2), (orig_bbox[2] + 4, (orig_bbox[1] + orig_bbox[3]) // 2)], fill=(220, 38, 38), width=3)

    # Special Price Big Text
    draw.text((px + 140, bot_y + 54), config['price_special'], font=get_font(54, True), fill=config['price_color'])
    draw.text((px + 400, bot_y + 78), "บาท", font=get_font(26, True), fill=config['text_dark'])

    # Bonus Gift Tag Box
    draw.rounded_rectangle([px, bot_y + 138, px + 445, bot_y + 195], radius=14, fill=config['gift_bg'], outline=config['gift_border'], width=2)
    draw_sparkle(draw, px + 22, bot_y + 166, 12, config['gift_color'])
    draw.text((px + 42, bot_y + 150), config['gift_text'], font=get_font(21, True), fill=config['gift_color'])

    # Guarantee Bullet Line
    draw_checkmark(draw, px + 12, bot_y + 228, 14, (16, 185, 129))
    draw.text((px + 28, bot_y + 215), config['guarantee_text'], font=get_font(20, False), fill=config['text_muted'])

    # Right: Big CTA Button
    btn_x = card_x + 510
    btn_y = bot_y + 45
    btn_w = 410
    btn_h = 165

    # Button Shadow
    btn_shadow = Image.new("RGBA", (btn_w + 20, btn_h + 20), (0, 0, 0, 0))
    bs_draw = ImageDraw.Draw(btn_shadow)
    bs_draw.rounded_rectangle([10, 10, btn_w + 10, btn_h + 10], radius=28, fill=config['btn_shadow'])
    btn_shadow = btn_shadow.filter(ImageFilter.GaussianBlur(10))
    bg.paste(btn_shadow, (btn_x - 10, btn_y - 10), btn_shadow)

    # Button Gradient
    btn_grad = create_horizontal_grad(btn_w, btn_h, config['btn_c1'], config['btn_c2'])
    btn_mask = Image.new("L", (btn_w, btn_h), 0)
    bm_draw = ImageDraw.Draw(btn_mask)
    bm_draw.rounded_rectangle([0, 0, btn_w, btn_h], radius=26, fill=255)
    bg.paste(btn_grad, (btn_x, btn_y), btn_mask)

    # Button Border & Inner Highlights
    draw.rounded_rectangle([btn_x, btn_y, btn_x + btn_w, btn_y + btn_h], radius=26, outline=(255, 255, 255, 220), width=3)

    # Button Texts
    draw.text((btn_x + 35, btn_y + 35), config['btn_main_text'], font=get_font(34, True), fill=(255, 255, 255))
    draw.text((btn_x + 38, btn_y + 92), config['btn_sub_text'], font=get_font(20, False), fill=(255, 245, 230))

    # Save
    final_path = os.path.join(dir_rich_msg, output_filename)
    bg.convert("RGB").save(final_path, quality=95)
    print(f"✓ Saved Rich Message: {final_path}")

# ==============================================================================
# CONFIGURATIONS FOR 4 NEW RICH MESSAGES
# ==============================================================================

configs = [
    # 1. PERSIAN (น้องปุยหิมะ - เปอร์เซีย)
    {
        'output_filename': 'rich_message_persian_1040x1040.png',
        'bg_c1': (255, 243, 247),
        'bg_c2': (254, 235, 242),
        'hdr_c1': (225, 29, 72),      # Deep Rose
        'hdr_c2': (244, 63, 94),      # Coral Rose
        'badge_top_bg': (159, 18, 57), # Dark Rose
        'badge_top_icon_color': (255, 220, 130),
        'badge_top_text': 'PURRFECT PREMIUM SPOTLIGHT',
        'badge_top_txtcolor': (255, 255, 255),
        'title_th': 'น้องปุยหิมะ (Persian Classic)',
        'subtitle_th': 'ราชินีแห่งแมวขนยาว หน้าหวาน นิสัยสุภาพ นิ่งสงบ',
        'subtitle_color': (255, 230, 238),
        'tag_right': 'CFA Pedigree',
        'accent_color': (225, 29, 72),
        'cat_image': 'cat_persian.jpg',
        'photo_badge': 'เพศเมีย | อายุ 3 เดือน',
        'card_bg': (255, 255, 255),
        'card_border': (251, 182, 206),
        'text_dark': (30, 41, 59),
        'text_muted': (100, 116, 139),
        'right_title': 'จุดเด่น & บุคลิกนิสัย',
        'right_desc': 'ขนหนานุ่มดั่งปุยเมฆ พร้อมย้ายบ้านทันที',
        'highlights': [
            ('1', 'ขนนุ่มฟูระดับพรีเมียม', 'ขนยาวหนาสองชั้น นุ่มฟูหน้าหวานสะกดใจ'),
            ('2', 'อารมณ์ดี เรียบร้อย', 'ไม่ส่งเสียงรบกวน เหมาะกับคอนโดและบ้าน'),
            ('3', 'ตรวจสุขภาพ & วัคซีนครบ', 'วัคซีน 2 เข็ม + ถ่ายพยาธิ + ใบเพ็ด CFA')
        ],
        'bullet_bg': (255, 241, 246),
        'bullet_border': (253, 215, 228),
        'icon_bg': (225, 29, 72),
        'icon_color': (255, 255, 255),
        'price_original': '22,000.-',
        'price_special': '16,500',
        'price_color': (225, 29, 72),
        'gift_bg': (255, 237, 213),
        'gift_border': (253, 186, 116),
        'gift_text': 'ฟรี! Starter Kit & อาหารพรีเมียม 2,500.-',
        'gift_color': (194, 65, 12),
        'guarantee_text': 'รับประกันสุขภาพ 14 วันเต็ม | ยินดีรับบัตรเครดิต',
        'btn_c1': (225, 29, 72),
        'btn_c2': (244, 63, 94),
        'btn_shadow': (225, 29, 72, 100),
        'btn_main_text': 'จองน้องเปอร์เซีย >>',
        'btn_sub_text': 'แตะเพื่อดูรูปเพิ่ม & นัดดูตัวจริง',
        'sparkles': [(870, 140, 16, (255, 215, 0)), (150, 720, 12, (244, 63, 94, 150))],
        'paws': [(960, 240, 36, (244, 63, 94, 40)), (80, 880, 48, (244, 63, 94, 30))]
    },

    # 2. RAGDOLL (น้องคอตตอน - แร็กดอลล์)
    {
        'output_filename': 'rich_message_ragdoll_1040x1040.png',
        'bg_c1': (240, 249, 255),
        'bg_c2': (224, 242, 254),
        'hdr_c1': (2, 132, 199),       # Sky Blue
        'hdr_c2': (59, 130, 246),      # Royal Blue
        'badge_top_bg': (7, 89, 133),  # Deep Ocean Blue
        'badge_top_icon_color': (125, 211, 252),
        'badge_top_text': 'BLUE EYES PRINCESS SPOTLIGHT',
        'badge_top_txtcolor': (255, 255, 255),
        'title_th': 'น้องคอตตอน (Ragdoll Princess)',
        'subtitle_th': 'เจ้าหญิงตาสีฟ้าคราม ตัวนุ่มนิ่มดั่งตุ๊กตาผ้า',
        'subtitle_color': (224, 242, 254),
        'tag_right': 'TICA Certified',
        'accent_color': (2, 132, 199),
        'cat_image': 'cat_ragdoll.jpg',
        'photo_badge': 'เพศเมีย | อายุ 2.5 เดือน',
        'card_bg': (255, 255, 255),
        'card_border': (186, 230, 253),
        'text_dark': (15, 23, 42),
        'text_muted': (71, 85, 105),
        'right_title': 'จุดเด่น & บุคลิกนิสัย',
        'right_desc': 'ตาสีฟ้าสะกดใจ ขนนุ่มดั่งปุยฝ้าย ไม่กางเล็บ',
        'highlights': [
            ('1', 'ตาสีฟ้าครามประกายคริสตัล', 'สายพันธุ์แท้ มาร์คกิ้งคมสวยสมบูรณ์แบบ'),
            ('2', 'ตัวนิ่มอุ้มง่ายเป็นมิตรสุดๆ', 'ขี้อ้อน ปลอดภัยมากสำหรับบ้านที่มีเด็ก'),
            ('3', 'ตรวจยีน HCM ปกติ 100%', 'ตรวจพันธุกรรมโรคหัวใจผ่าน + วัคซีนครบ')
        ],
        'bullet_bg': (240, 249, 255),
        'bullet_border': (186, 230, 253),
        'icon_bg': (2, 132, 199),
        'icon_color': (255, 255, 255),
        'price_original': '35,000.-',
        'price_special': '29,000',
        'price_color': (2, 132, 199),
        'gift_bg': (224, 231, 255),
        'gift_border': (199, 210, 254),
        'gift_text': 'ฟรี! กระเป๋าแคปซูลอวกาศ + Starter Kit',
        'gift_color': (67, 56, 202),
        'guarantee_text': 'เพ็ดดีกรี TICA นำเข้า | ส่งฟรีกรุงเทพฯ-ปริมณฑล',
        'btn_c1': (2, 132, 199),
        'btn_c2': (59, 130, 246),
        'btn_shadow': (2, 132, 199, 100),
        'btn_main_text': 'จองน้องแร็กดอลล์ >>',
        'btn_sub_text': 'แตะเพื่อรับสิทธิ์ & วิดีโอคอลดูตัว',
        'sparkles': [(870, 140, 16, (147, 197, 253)), (120, 720, 12, (2, 132, 199, 150))],
        'paws': [(960, 240, 36, (2, 132, 199, 40)), (70, 890, 48, (2, 132, 199, 30))]
    },

    # 3. MUNCHKIN (น้องชอร์ตตี้ - มันช์กิ้น ขาสั้น)
    {
        'output_filename': 'rich_message_munchkin_1040x1040.png',
        'bg_c1': (255, 247, 237),
        'bg_c2': (254, 243, 199),
        'hdr_c1': (234, 88, 12),      # Warm Orange
        'hdr_c2': (245, 158, 11),     # Amber Gold
        'badge_top_bg': (154, 52, 18), # Deep Amber
        'badge_top_icon_color': (254, 215, 170),
        'badge_top_text': 'SHORT LEGS CUTE HERO',
        'badge_top_txtcolor': (255, 255, 255),
        'title_th': 'น้องชอร์ตตี้ (Munchkin Legs)',
        'subtitle_th': 'เจ้าเหมียวขาสั้นเตี้ยดุ๊กดิ๊ก น่ารักขี้อ้อน เรียกรอยยิ้ม',
        'subtitle_color': (254, 243, 199),
        'tag_right': 'WCF Pedigree',
        'accent_color': (234, 88, 12),
        'cat_image': 'cat_munchkin.jpg',
        'photo_badge': 'เพศผู้ | อายุ 2 เดือน',
        'card_bg': (255, 255, 255),
        'card_border': (254, 215, 170),
        'text_dark': (30, 41, 59),
        'text_muted': (100, 116, 139),
        'right_title': 'จุดเด่น & บุคลิกนิสัย',
        'right_desc': 'วิ่งดุ๊กดิ๊ก ปรับตัวเก่ง เป็นมิตรและสดใสมาก',
        'highlights': [
            ('1', 'ขาสั้นเตี้ยแท้มาตรฐาน WCF', 'ขาสั้นดุ๊กดิ๊ก ร่างกายแข็งแรง วิ่งเล่นคล่องแคล่ว'),
            ('2', 'ขี้เล่น อารมณ์ดี ร่าเริงตลอดวัน', 'ชอบเล่นของเล่น เข้ากับคนง่าย ไม่ดื้อไม่ซน'),
            ('3', 'ตรวจสุขภาพข้อ & วัคซีนครบ', 'ตรวจสุขภาพข้อต่อผ่านฉลุย + วัคซีน 2 เข็ม')
        ],
        'bullet_bg': (255, 247, 237),
        'bullet_border': (254, 215, 170),
        'icon_bg': (234, 88, 12),
        'icon_color': (255, 255, 255),
        'price_original': '29,000.-',
        'price_special': '24,000',
        'price_color': (234, 88, 12),
        'gift_bg': (254, 243, 199),
        'gift_border': (253, 230, 138),
        'gift_text': 'ฟรี! คอนโดแมวมินิ + เซ็ตของเล่นดุ๊กดิ๊ก',
        'gift_color': (180, 83, 9),
        'guarantee_text': 'รับประกันขาสั้นแท้ 100% | มีบริการตรวจสุขภาพซ้ำ',
        'btn_c1': (234, 88, 12),
        'btn_c2': (245, 158, 11),
        'btn_shadow': (234, 88, 12, 100),
        'btn_main_text': 'จองน้องมันช์กิ้น >>',
        'btn_sub_text': 'แตะเพื่อรับคลิปดุ๊กดิ๊ก & โปรพิเศษ',
        'sparkles': [(880, 140, 16, (255, 220, 100)), (130, 720, 12, (234, 88, 12, 150))],
        'paws': [(960, 240, 36, (234, 88, 12, 40)), (70, 890, 48, (234, 88, 12, 30))]
    },

    # 4. BENGAL (น้องจากัวร์ - เบงกอล เสือดาวจิ๋ว)
    {
        'output_filename': 'rich_message_bengal_1040x1040.png',
        'bg_c1': (24, 24, 27),        # Dark Zinc
        'bg_c2': (39, 39, 42),
        'hdr_c1': (217, 119, 6),      # Amber Gold
        'hdr_c2': (180, 83, 9),       # Metallic Bronze Gold
        'badge_top_bg': (120, 53, 15), # Deep Gold Bronze
        'badge_top_icon_color': (254, 240, 138),
        'badge_top_text': 'MINI LEOPARD EXCLUSIVE',
        'badge_top_txtcolor': (255, 240, 160),
        'title_th': 'น้องจากัวร์ (Bengal Rosetted)',
        'subtitle_th': 'เสือดาวจิ๋ว ลายกุหลาบทองคำ ขนประกายกลิตเตอร์',
        'subtitle_color': (254, 240, 138),
        'tag_right': 'Rosette Grade A',
        'accent_color': (245, 158, 11),
        'cat_image': 'cat_bengal.jpg',
        'photo_badge': 'เพศผู้ | อายุ 3 เดือน',
        'card_bg': (30, 41, 59),
        'card_border': (217, 119, 6),
        'text_dark': (255, 255, 255),
        'text_muted': (203, 213, 225),
        'right_title': 'จุดเด่น & บุคลิกนิสัย',
        'right_desc': 'ลวดลายเสือดาวหายาก ปราดเปรียวและฉลาดหลักแหลม',
        'highlights': [
            ('1', 'ลาย Rosette ชัดเจนประกายทอง', 'ลายดอกกุหลาบคมชัด ขนสะท้อนแสง Glittering'),
            ('2', 'ปราดเปรียว ฉลาด รักการเล่นน้ำ', 'กล้ามเนื้อแข็งแรง สามารถฝึกจูงเดินเล่นได้'),
            ('3', 'สายเลือดแชมป์ WCF แท้ 100%', 'วัคซีนครบ 2 เข็ม + ฝังไมโครชิปสากลเรียบร้อย')
        ],
        'bullet_bg': (15, 23, 42),
        'bullet_border': (180, 83, 9),
        'icon_bg': (217, 119, 6),
        'icon_color': (255, 255, 255),
        'price_original': '32,000.-',
        'price_special': '26,000',
        'price_color': (251, 191, 36),
        'gift_bg': (69, 26, 3),
        'gift_border': (217, 119, 6),
        'gift_text': 'ฟรี! สายจูงพรีเมียม + น้ำพุแมวสแตนเลส',
        'gift_color': (254, 240, 138),
        'guarantee_text': 'รับประกันสายพันธุ์แท้ตลอดชีพ | ฝังไมโครชิปฟรี',
        'btn_c1': (217, 119, 6),
        'btn_c2': (245, 158, 11),
        'btn_shadow': (217, 119, 6, 120),
        'btn_main_text': 'จองน้องเบงกอล >>',
        'btn_sub_text': 'แตะเพื่อรับใบเพ็ดดีกรี & คลิปชมลายดอก',
        'sparkles': [(880, 140, 16, (251, 191, 36)), (130, 720, 12, (245, 158, 11, 200))],
        'paws': [(960, 240, 36, (245, 158, 11, 60)), (70, 890, 48, (245, 158, 11, 40))]
    }
]

def main():
    print("🎨 Rendering 4 High-Quality Rich Messages with dynamic badges...")
    for cfg in configs:
        render_rich_message_card(cfg, cfg['output_filename'])

    # Update README in 1_RICH_MESSAGE
    readme_path = os.path.join(dir_rich_msg, "README_RICH_MESSAGE.txt")
    with open(readme_path, "w", encoding="utf-8") as f:
        f.write("""================================================================================
📸 รวมไฟล์ริชเมสเสจ (Rich Messages) - Purrfect Shop
================================================================================

📌 รายการไฟล์ริชเมสเสจขนาด 1040 x 1040 px (Square 1:1) สำหรับยิงขายแมวแต่ละสายพันธุ์:

1. 🌸 น้องปุยหิมะ - เปอร์เซีย (Persian Classic)
   - ไฟล์: rich_message_persian_1040x1040.png
   - จุดเด่น: ราชินีขนฟู หน้าหวาน นิ่งสงบ ค่าสินสอด 16,500.- (เพ็ดดีกรี CFA)
   - ลิงก์ที่แนะนำ: https://patches660.github.io/Purrfect-Shop/products.html?breed=persian

2. 💎 น้องคอตตอน - แร็กดอลล์ (Ragdoll Princess)
   - ไฟล์: rich_message_ragdoll_1040x1040.png
   - จุดเด่น: เจ้าหญิงตาสีฟ้าคราม ตัวนุ่มนิ่มดั่งตุ๊กตาผ้า ค่าสินสอด 29,000.- (เพ็ดดีกรี TICA)
   - ลิงก์ที่แนะนำ: https://patches660.github.io/Purrfect-Shop/products.html?breed=ragdoll

3. 🐾 น้องชอร์ตตี้ - มันช์กิ้น ขาสั้น (Munchkin Short Legs)
   - ไฟล์: rich_message_munchkin_1040x1040.png
   - จุดเด่น: ขาสั้นเตี้ยดุ๊กดิ๊ก วิ่งน่ารัก ร่าเริงอารมณ์ดี ค่าสินสอด 24,000.- (เพ็ดดีกรี WCF)
   - ลิงก์ที่แนะนำ: https://patches660.github.io/Purrfect-Shop/products.html?breed=munchkin

4. 🐆 น้องจากัวร์ - เบงกอล เสือดาวจิ๋ว (Bengal Rosetted)
   - ไฟล์: rich_message_bengal_1040x1040.png
   - จุดเด่น: ลายโรเซ็ตต์ประกายทอง ขน Glittering เท่หรูหรา ค่าสินสอด 26,000.- (สายเลือดแชมป์)
   - ลิงก์ที่แนะนำ: https://patches660.github.io/Purrfect-Shop/products.html?breed=bengal

5. 🐱 แบนเนอร์รวมโปรโมชั่นร้าน (Generic Shop & Deal)
   - rich_message_1040x1040_square.png (แบนเนอร์รวมโปรโมชั่น 1040x1040)
   - rich_message_1040x520_compact.png (แบนเนอร์ผืนผ้าแนวนอน 1040x520)

--------------------------------------------------------------------------------
🛠️ วิธีนำไปตั้งค่าใน LINE Official Account Manager (manager.line.biz):
--------------------------------------------------------------------------------
1. เข้าไปที่เมนู "เครื่องมือแสดงผล (Display Tools)" -> "ริชเมสเสจ (Rich Messages)"
2. คลิก "สร้างใหม่ (Create new)"
3. ตั้งชื่อริชเมสเสจ (เช่น: โปรโมชั่นน้องเปอร์เซีย / น้องแร็กดอลล์ / น้องมันช์กิ้น / น้องเบงกอล)
4. เลือกเทมเพลต: แบบ "1 ช่องเต็ม (Full Image)" หรือ "แบ่ง 2 ช่อง (ครึ่งบนรูป / ครึ่งล่างปุ่มกด)"
5. อัปโหลดไฟล์รูปภาพที่ต้องการจากโฟลเดอร์นี้
6. กำหนด Action เป็น "ลิงก์ (Link)" และใส่ URL หน้าร้านหรือหน้าสินค้า
7. บันทึก และนำไปใช้ส่ง Broadcast ให้ลูกค้าได้ทันที!
""")
    print(f"✓ Updated README: {readme_path}")
    print("🎉 All 4 Cat Rich Messages Rendered & Saved Successfully!")

if __name__ == "__main__":
    main()
