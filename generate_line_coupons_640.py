import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

base_dir = os.path.dirname(os.path.abspath(__file__))
line_dir = os.path.join(base_dir, "LINE_ASSETS")
dir_coupon = os.path.join(line_dir, "4_COUPON")
os.makedirs(dir_coupon, exist_ok=True)

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
    img = Image.new("RGBA", (w, h), (255, 255, 255, 255))
    draw = ImageDraw.Draw(img)
    for y in range(h):
        t = y / max(1, h - 1)
        r = int(c1[0] + t * (c2[0] - c1[0]))
        g = int(c1[1] + t * (c2[1] - c1[1]))
        b = int(c1[2] + t * (c2[2] - c1[2]))
        draw.line([(0, y), (w, y)], fill=(r, g, b, 255))
    return img

def create_horizontal_grad(w, h, c1, c2):
    img = Image.new("RGBA", (w, h), (255, 255, 255, 255))
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

def draw_sparkle(draw, cx, cy, r=12, fill=(245, 158, 11)):
    draw.polygon([(cx, cy - r), (cx + 2, cy), (cx, cy + r), (cx - 2, cy)], fill=fill)
    draw.polygon([(cx - r, cy), (cx, cy + 2), (cx + r, cy), (cx, cy - 2)], fill=fill)
    d = int(r * 0.45)
    draw.polygon([(cx - d, cy - d), (cx, cy), (cx + d, cy + d), (cx, cy)], fill=fill)
    draw.polygon([(cx + d, cy - d), (cx, cy), (cx - d, cy + d), (cx, cy)], fill=fill)

def draw_checkmark(draw, cx, cy, size=12, color=(16, 185, 129)):
    draw.line([(cx - size//2, cy), (cx - size//6, cy + size//2), (cx + size//2, cy - size//2)], fill=color, width=2)

def draw_perforated_line(draw, x1, y1, x2, y2, dot_len=6, space_len=5, fill=(200, 200, 200), width=2):
    dx = x2 - x1
    dy = y2 - y1
    dist = math.hypot(dx, dy)
    steps = int(dist // (dot_len + space_len))
    for i in range(steps + 1):
        t_start = (i * (dot_len + space_len)) / dist
        t_end = min(1.0, (i * (dot_len + space_len) + dot_len) / dist)
        sx = int(x1 + t_start * dx)
        sy = int(y1 + t_start * dy)
        ex = int(x1 + t_end * dx)
        ey = int(y1 + t_end * dy)
        draw.line([(sx, sy), (ex, ey)], fill=fill, width=width)

def render_coupon_640(config, output_filename):
    w, h = 640, 640
    # 1. Main Background
    bg = create_vertical_grad(w, h, config['outer_c1'], config['outer_c2'])
    draw = ImageDraw.Draw(bg)

    # 2. Card Dimensions
    cx, cy, cw, ch = 28, 28, 584, 584
    notch_y = 390
    notch_r = 18

    # Smooth Drop Shadow (clipped inside canvas)
    shadow_mask = Image.new("L", (w, h), 0)
    sm_draw = ImageDraw.Draw(shadow_mask)
    sm_draw.rounded_rectangle([cx - 4, cy - 4, cx + cw + 4, cy + ch + 4], radius=30, fill=180)
    shadow_layer = shadow_mask.filter(ImageFilter.GaussianBlur(8))
    
    # Composite soft shadow
    shadow_img = Image.new("RGBA", (w, h), (0, 0, 0, 0))
    s_draw = ImageDraw.Draw(shadow_img)
    s_draw.rounded_rectangle([cx - 2, cy + 2, cx + cw + 2, cy + ch + 6], radius=28, fill=(0, 0, 0, 45))
    shadow_img = shadow_img.filter(ImageFilter.GaussianBlur(8))
    bg.paste(shadow_img, (0, 0), shadow_img)

    # 3. Create Ticket Card Surface
    card = Image.new("RGBA", (cw, ch), (255, 255, 255, 255))
    c_draw = ImageDraw.Draw(card)

    # Card Body Background
    c_draw.rounded_rectangle([0, 0, cw, ch], radius=28, fill=config['card_bg'], outline=config['card_border'], width=3)

    # Top Header Gradient within card
    hdr_h = 105
    hdr_grad = create_horizontal_grad(cw, hdr_h, config['hdr_c1'], config['hdr_c2'])
    
    hdr_mask = Image.new("L", (cw, hdr_h), 0)
    hm_draw = ImageDraw.Draw(hdr_mask)
    hm_draw.rounded_rectangle([0, 0, cw, hdr_h + 30], radius=28, fill=255)
    card.paste(hdr_grad, (0, 0), hdr_mask)

    # Top Header Elements
    # Brand Pill
    pill_font = get_font(13, True)
    pill_text = "PURRFECT CAT SHOP  |  OFFICIAL COUPON"
    pill_bbox = c_draw.textbbox((0, 0), pill_text, font=pill_font)
    pill_w = pill_bbox[2] - pill_bbox[0] + 40
    c_draw.rounded_rectangle([(cw - pill_w) // 2, 12, (cw + pill_w) // 2, 36], radius=12, fill=(0, 0, 0, 80))
    draw_sparkle(c_draw, (cw - pill_w) // 2 + 12, 24, 7, (255, 230, 140))
    draw_sparkle(c_draw, (cw + pill_w) // 2 - 12, 24, 7, (255, 230, 140))
    c_draw.text(((cw - pill_w) // 2 + 20, 14), pill_text, font=pill_font, fill=(255, 245, 210))

    # Header Title
    title_font = get_font(26, True)
    t_bbox = c_draw.textbbox((0, 0), config['header_title'], font=title_font)
    t_w = t_bbox[2] - t_bbox[0]
    c_draw.text(((cw - t_w) // 2, 42), config['header_title'], font=title_font, fill=(255, 255, 255))

    # Header Subtitle
    sub_font = get_font(15, False)
    s_bbox = c_draw.textbbox((0, 0), config['header_subtitle'], font=sub_font)
    s_w = s_bbox[2] - s_bbox[0]
    c_draw.text(((cw - s_w) // 2, 76), config['header_subtitle'], font=sub_font, fill=config['hdr_sub_color'])

    # 4. Middle Section: Big 25% OFF Hero Box
    c_draw.rounded_rectangle([25, 120, cw - 25, 370], radius=20, fill=config['hero_bg'], outline=config['hero_border'], width=2)

    # Decorative background in hero box
    draw_paw(c_draw, 70, 170, 48, config['hero_deco_color'])
    draw_paw(c_draw, cw - 70, 320, 52, config['hero_deco_color'])
    draw_sparkle(c_draw, cw - 80, 160, 14, config['accent_color'])
    draw_sparkle(c_draw, 80, 330, 12, config['accent_color'])

    # Ribbon Tag
    ribbon_font = get_font(14, True)
    ribbon_text = config['badge_ribbon']
    r_bbox = c_draw.textbbox((0, 0), ribbon_text, font=ribbon_font)
    r_w = r_bbox[2] - r_bbox[0] + 36
    c_draw.rounded_rectangle([(cw - r_w) // 2, 134, (cw + r_w) // 2, 164], radius=15, fill=config['accent_color'])
    c_draw.text(((cw - r_w) // 2 + 18, 138), ribbon_text, font=ribbon_font, fill=(255, 255, 255))

    # Giant "25% OFF"
    num_font = get_font(84, True)
    num_str = "25%"
    n_bbox = c_draw.textbbox((0, 0), num_str, font=num_font)
    n_w = n_bbox[2] - n_bbox[0]

    off_font = get_font(34, True)
    off_str = "OFF"
    o_bbox = c_draw.textbbox((0, 0), off_str, font=off_font)
    o_w = o_bbox[2] - o_bbox[0]

    th_off_font = get_font(20, True)
    th_off_str = "ส่วนลดพิเศษ"
    to_bbox = c_draw.textbbox((0, 0), th_off_str, font=th_off_font)
    to_w = to_bbox[2] - to_bbox[0]

    total_hero_w = n_w + 18 + max(o_w, to_w)
    start_hero_x = (cw - total_hero_w) // 2

    c_draw.text((start_hero_x, 172), num_str, font=num_font, fill=config['accent_color'])
    c_draw.text((start_hero_x + n_w + 18, 184), off_str, font=off_font, fill=config['text_dark'])
    c_draw.text((start_hero_x + n_w + 18, 226), th_off_str, font=th_off_font, fill=config['accent_color'])

    # Highlight description line in hero box
    hl_font = get_font(20, True)
    hl_bbox = c_draw.textbbox((0, 0), config['hero_highlight'], font=hl_font)
    hl_w = hl_bbox[2] - hl_bbox[0]
    c_draw.text(((cw - hl_w) // 2, 280), config['hero_highlight'], font=hl_font, fill=config['text_dark'])

    # Condition snippet under hero
    cs_font = get_font(15, False)
    cs_bbox = c_draw.textbbox((0, 0), config['hero_condition'], font=cs_font)
    cs_w = cs_bbox[2] - cs_bbox[0]
    c_draw.text(((cw - cs_w) // 2, 318), config['hero_condition'], font=cs_font, fill=config['text_muted'])

    # 5. Perforated Divider at notch_y = 390
    draw_perforated_line(c_draw, 35, notch_y, cw - 35, notch_y, dot_len=7, space_len=6, fill=config['perf_color'], width=2)

    # 6. Bottom Section (y=405 to 570)
    code_w = 460
    code_h = 58
    code_x = (cw - code_w) // 2
    code_y = 406

    c_draw.rounded_rectangle([code_x, code_y, code_x + code_w, code_y + code_h], radius=16, fill=config['code_bg'], outline=config['code_border'], width=2)

    draw_sparkle(c_draw, code_x + 28, code_y + 29, 10, config['accent_color'])
    c_draw.text((code_x + 48, code_y + 16), "รหัสโค้ด:", font=get_font(17, False), fill=config['text_muted'])
    
    # Promo Code Text (Big Bold)
    c_draw.text((code_x + 130, code_y + 11), config['promo_code'], font=get_font(25, True), fill=config['accent_color'])

    # "ใช้คูปองนี้ >" pill button on the right
    c_draw.rounded_rectangle([code_x + code_w - 145, code_y + 9, code_x + code_w - 12, code_y + code_h - 9], radius=12, fill=config['accent_color'])
    c_draw.text((code_x + code_w - 130, code_y + 16), "ใช้คูปองนี้ >", font=get_font(15, True), fill=(255, 255, 255))

    # Terms & Conditions
    terms_y = 478
    for idx, t_text in enumerate(config['terms']):
        ty = terms_y + (idx * 26)
        c_draw.ellipse([35, ty + 6, 43, ty + 14], fill=config['accent_color'])
        c_draw.text((54, ty), t_text, font=get_font(14, False), fill=config['text_muted'])

    # Expiry Date & LINE OA Stamp
    c_draw.text((cw - 225, terms_y + 24), config['expiry_text'], font=get_font(13, True), fill=config['expiry_color'])
    draw_checkmark(c_draw, cw - 215, terms_y + 60, 12, (16, 185, 129))
    c_draw.text((cw - 200, terms_y + 52), "สิทธิ์เฉพาะเพื่อน LINE OA", font=get_font(13, False), fill=(16, 185, 129))

    # 7. Cut Scallop Notches on Left and Right of the Card
    notch_mask = Image.new("L", (cw, ch), 255)
    nm_draw = ImageDraw.Draw(notch_mask)
    nm_draw.ellipse([-notch_r, notch_y - notch_r, notch_r, notch_y + notch_r], fill=0)
    nm_draw.ellipse([cw - notch_r, notch_y - notch_r, cw + notch_r, notch_y + notch_r], fill=0)

    # Paste Card onto Background with Notch Mask
    bg.paste(card, (cx, cy), notch_mask)

    # Re-draw the notch border arcs onto bg
    bg_draw = ImageDraw.Draw(bg)
    bg_draw.arc([cx - notch_r, cy + notch_y - notch_r, cx + notch_r, cy + notch_y + notch_r], start=270, end=90, fill=config['card_border'], width=3)
    bg_draw.arc([cx + cw - notch_r, cy + notch_y - notch_r, cx + cw + notch_r, cy + notch_y + notch_r], start=90, end=270, fill=config['card_border'], width=3)

    # Save final 640x640 image
    final_path = os.path.join(dir_coupon, output_filename)
    bg.convert("RGB").save(final_path, quality=95)
    print(f"✓ Saved 640x640 Coupon: {final_path}")

# ==============================================================================
# CONFIGURATIONS FOR 5 DISTINCT 25% DISCOUNT COUPONS
# ==============================================================================

coupon_configs = [
    # 1. NEW FRIEND / WELCOME COUPON (คูปองต้อนรับเพื่อนใหม่ 25%)
    {
        'output_filename': 'coupon_1_welcome_25pct_640x640.png',
        'outer_c1': (255, 237, 213),
        'outer_c2': (254, 215, 170),
        'hdr_c1': (234, 88, 12),       # Coral Orange
        'hdr_c2': (249, 115, 22),      # Tangerine
        'hdr_sub_color': (254, 243, 199),
        'header_title': 'คูปองต้อนรับทาสแมวใหม่',
        'header_subtitle': 'ยินดีต้อนรับสู่ครอบครัว Purrfect Shop',
        'card_bg': (255, 255, 255),
        'card_border': (253, 186, 116),
        'hero_bg': (255, 247, 237),
        'hero_border': (254, 215, 170),
        'hero_deco_color': (254, 215, 170, 90),
        'badge_ribbon': '• WELCOME NEW FRIEND •',
        'accent_color': (234, 88, 12),
        'text_dark': (30, 41, 59),
        'text_muted': (100, 116, 139),
        'hero_highlight': 'ลดทันที 25% ทุกคำสั่งซื้อแรก',
        'hero_condition': 'ช้อปครบ 500 บาทขึ้นไป ลดสูงสุด 1,000 บาท',
        'perf_color': (253, 186, 116),
        'code_bg': (255, 247, 237),
        'code_border': (254, 215, 170),
        'promo_code': 'PURR25NEW',
        'terms': [
            'ใช้ได้ 1 สิทธิ์ ต่อ 1 บัญชีผู้ใช้งาน LINE',
            'ใช้ได้กับสินค้าทุกหมวดหมู่ในร้าน',
            'ไม่สามารถแลกเปลี่ยนหรือทอนเป็นเงินสดได้'
        ],
        'expiry_text': 'หมดเขต: 31 ต.ค. 2026',
        'expiry_color': (194, 65, 12),
        'outer_sparkles': [(40, 40, 12, (249, 115, 22)), (600, 600, 14, (249, 115, 22))],
        'outer_paws': [(605, 45, 28, (234, 88, 12, 60)), (35, 600, 32, (234, 88, 12, 60))]
    },

    # 2. CAT ADOPTION PRIVILEGE (คูปองส่วนลด 25% ค่าสินสอดน้องแมว)
    {
        'output_filename': 'coupon_2_adoption_25pct_640x640.png',
        'outer_c1': (254, 243, 199),
        'outer_c2': (253, 230, 138),
        'hdr_c1': (180, 83, 9),        # Royal Gold
        'hdr_c2': (217, 119, 6),       # Amber Bronze
        'hdr_sub_color': (254, 240, 138),
        'header_title': 'คูปองค่าสินสอดรับน้องแมว',
        'header_subtitle': 'สิทธิพิเศษจองน้องแมวเกรดพรีเมียมทุกสายพันธุ์',
        'card_bg': (255, 255, 255),
        'card_border': (245, 158, 11),
        'hero_bg': (255, 251, 235),
        'hero_border': (253, 230, 138),
        'hero_deco_color': (253, 230, 138, 90),
        'badge_ribbon': '• VIP ADOPTION PRIVILEGE •',
        'accent_color': (180, 83, 9),
        'text_dark': (30, 41, 59),
        'text_muted': (100, 116, 139),
        'hero_highlight': 'ลด 25% ค่าสินสอดน้องแมวทุกตัว',
        'hero_condition': 'แถมฟรี Starter Kit 2,500.- + ตรวจสุขภาพครบ',
        'perf_color': (245, 158, 11),
        'code_bg': (255, 251, 235),
        'code_border': (253, 230, 138),
        'promo_code': 'ADOPT25VIP',
        'terms': [
            'ใช้ได้กับการจองน้องแมวทุกสายพันธุ์ในร้าน',
            'รวมใบเพ็ดดีกรี WCF/CFA/TICA แท้ + วัคซีนครบ',
            'มีบริการส่งมอบถึงบ้านฟรีกรุงเทพฯ-ปริมณฑล'
        ],
        'expiry_text': 'จำกัด 10 สิทธิ์แรก',
        'expiry_color': (180, 83, 9),
        'outer_sparkles': [(40, 40, 12, (217, 119, 6)), (600, 600, 14, (217, 119, 6))],
        'outer_paws': [(605, 45, 28, (180, 83, 9, 60)), (35, 600, 32, (180, 83, 9, 60))]
    },

    # 3. PREMIUM FOOD & TREATS (คูปองลด 25% อาหารและขนมแมว)
    {
        'output_filename': 'coupon_3_food_treats_25pct_640x640.png',
        'outer_c1': (209, 250, 229),
        'outer_c2': (167, 243, 208),
        'hdr_c1': (5, 150, 105),       # Emerald Green
        'hdr_c2': (16, 185, 129),      # Mint Teal
        'hdr_sub_color': (209, 250, 229),
        'header_title': 'คูปองอาหาร & ทรีตแมว 25%',
        'header_subtitle': 'อาหารเกรนฟรี ขนมเลีย & Freeze Dried พรีเมียม',
        'card_bg': (255, 255, 255),
        'card_border': (110, 231, 183),
        'hero_bg': (240, 253, 244),
        'hero_border': (167, 243, 208),
        'hero_deco_color': (167, 243, 208, 90),
        'badge_ribbon': '• HEALTHY CAT FEAST •',
        'accent_color': (5, 150, 105),
        'text_dark': (15, 23, 42),
        'text_muted': (71, 85, 105),
        'hero_highlight': 'ลด 25% หมวดอาหาร & โภชนาการ',
        'hero_condition': 'เมื่อมียอดสั่งซื้ออาหาร/ขนมขั้นต่ำ 400 บาท',
        'perf_color': (110, 231, 183),
        'code_bg': (240, 253, 244),
        'code_border': (167, 243, 208),
        'promo_code': 'YUMMY25CAT',
        'terms': [
            'ใช้ได้กับอาหารเปียก, อาหารเม็ด และขนมแมวทุกแบรนด์',
            'ไม่จำกัดจำนวนชิ้นในการสั่งซื้อต่อครั้ง',
            'จัดส่งด่วนระบบควบคุมอุณหภูมิถึงหน้าบ้าน'
        ],
        'expiry_text': 'หมดเขต: 31 ต.ค. 2026',
        'expiry_color': (5, 150, 105),
        'outer_sparkles': [(40, 40, 12, (16, 185, 129)), (600, 600, 14, (16, 185, 129))],
        'outer_paws': [(605, 45, 28, (5, 150, 105, 60)), (35, 600, 32, (5, 150, 105, 60))]
    },

    # 4. CAT TOYS & LIFESTYLE (คูปองลด 25% คอนโด & อุปกรณ์ของเล่น)
    {
        'output_filename': 'coupon_4_toys_lifestyle_25pct_640x640.png',
        'outer_c1': (243, 232, 255),
        'outer_c2': (233, 213, 255),
        'hdr_c1': (126, 34, 206),      # Purple Violet
        'hdr_c2': (147, 51, 234),      # Lavender Neon
        'hdr_sub_color': (243, 232, 255),
        'header_title': 'คูปองคอนโด & ของเล่นแมว 25%',
        'header_subtitle': 'ที่นอน กระบะทราย คอนโดไม้ และของเล่นฝึกทักษะ',
        'card_bg': (255, 255, 255),
        'card_border': (216, 180, 254),
        'hero_bg': (250, 245, 255),
        'hero_border': (233, 213, 255),
        'hero_deco_color': (233, 213, 255, 90),
        'badge_ribbon': '• PLAY & LIFESTYLE DEAL •',
        'accent_color': (126, 34, 206),
        'text_dark': (15, 23, 42),
        'text_muted': (71, 85, 105),
        'hero_highlight': 'ลด 25% ของเล่น & เฟอร์นิเจอร์แมว',
        'hero_condition': 'ช้อปอุปกรณ์แมวครบ 600 บาท ลดทันที',
        'perf_color': (216, 180, 254),
        'code_bg': (250, 245, 255),
        'code_border': (233, 213, 255),
        'promo_code': 'PLAY25MEOW',
        'terms': [
            'ใช้ได้กับหมวดของเล่น, คอนโดแมว, และห้องน้ำแมว',
            'รับประกันคุณภาพสินค้าเกรดนำเข้า ปลอดภัย 100%',
            'บริการประกอบฟรีสำหรับคอนโดแมวขนาดใหญ่'
        ],
        'expiry_text': 'หมดเขต: 31 ต.ค. 2026',
        'expiry_color': (126, 34, 206),
        'outer_sparkles': [(40, 40, 12, (147, 51, 234)), (600, 600, 14, (147, 51, 234))],
        'outer_paws': [(605, 45, 28, (126, 34, 206, 60)), (35, 600, 32, (126, 34, 206, 60))]
    },

    # 5. MONTHLY FLASH SALE (คูปองแฟลชเซลล์ประจำเดือน 25% ทั้งร้าน)
    {
        'output_filename': 'coupon_5_flash_sale_25pct_640x640.png',
        'outer_c1': (255, 228, 230),
        'outer_c2': (254, 205, 211),
        'hdr_c1': (225, 29, 72),       # Rose Red
        'hdr_c2': (244, 63, 94),       # Bright Coral
        'hdr_sub_color': (255, 228, 230),
        'header_title': 'FLASH SALE COUPON 25%',
        'header_subtitle': 'ดีลลับฉลองประจำเดือน สิทธิพิเศษเฉพาะ LINE Friends',
        'card_bg': (255, 255, 255),
        'card_border': (251, 113, 133),
        'hero_bg': (255, 241, 242),
        'hero_border': (254, 205, 211),
        'hero_deco_color': (254, 205, 211, 90),
        'badge_ribbon': '• MIDNIGHT FLASH DEAL •',
        'accent_color': (225, 29, 72),
        'text_dark': (15, 23, 42),
        'text_muted': (71, 85, 105),
        'hero_highlight': 'ลด 25% ทั้งร้าน ทุกหมวดหมู่',
        'hero_condition': 'ไม่มีขั้นต่ำ | ลดสูงสุด 1,500 บาทต่อบิล',
        'perf_color': (251, 113, 133),
        'code_bg': (255, 241, 242),
        'code_border': (254, 205, 211),
        'promo_code': 'FLASH25NOW',
        'terms': [
            'ใช้ได้กับสินค้าทุกรายการในร้าน (รวมน้องแมวและอุปกรณ์)',
            'ใช้ได้ 1 ครั้ง ต่อ 1 บัญชีผู้ใช้งาน',
            'โค้ดมีอายุการใช้งาน 24 ชั่วโมงหลังจากกดรับ'
        ],
        'expiry_text': 'ด่วน! ดีล 24 ชั่วโมง',
        'expiry_color': (225, 29, 72),
        'outer_sparkles': [(40, 40, 12, (244, 63, 94)), (600, 600, 14, (244, 63, 94))],
        'outer_paws': [(605, 45, 28, (225, 29, 72, 60)), (35, 600, 32, (225, 29, 72, 60))]
    }
]

def main():
    print("🎟️ Generating 5 Refined LINE OA 25% Discount Coupons (640x640 px)...")
    for cfg in coupon_configs:
        render_coupon_640(cfg, cfg['output_filename'])

    readme_path = os.path.join(dir_coupon, "README_COUPON.txt")
    with open(readme_path, "w", encoding="utf-8") as f:
        f.write("""================================================================================
🎟️ รวมไฟล์คูปองส่วนลด 25% (LINE OA Coupons 640x640 px) - Purrfect Shop
================================================================================

📌 สเปกขนาดรูปภาพคูปอง LINE Official Account มาตรฐาน:
- ขนาดภาพที่แนะนำ: 640 x 640 px (Square 1:1)
- ฟอร์แมต: PNG หรือ JPG (ขนาดไม่เกิน 10 MB)

--------------------------------------------------------------------------------
📁 รายการไฟล์ภาพคูปอง 25% ทั้ง 5 แบบในโฟลเดอร์นี้:
--------------------------------------------------------------------------------
1. 🧡 คูปองต้อนรับทาสแมวใหม่ (Welcome New Friend)
   - ไฟล์: coupon_1_welcome_25pct_640x640.png
   - โค้ด: PURR25NEW
   - สิทธิประโยชน์: ลด 25% คำสั่งซื้อแรก ขั้นต่ำ 500.- (ลดสูงสุด 1,000.-)

2. 👑 คูปองค่าสินสอดรับน้องแมว (Adoption Privilege)
   - ไฟล์: coupon_2_adoption_25pct_640x640.png
   - โค้ด: ADOPT25VIP
   - สิทธิประโยชน์: ลด 25% ค่าสินสอดน้องแมวทุกสายพันธุ์ + ฟรี Starter Kit

3. 🌿 คูปองอาหาร & ทรีตแมวพรีเมียม (Food & Treats Feast)
   - ไฟล์: coupon_3_food_treats_25pct_640x640.png
   - โค้ด: YUMMY25CAT
   - สิทธิประโยชน์: ลด 25% หมวดอาหารและขนมแมว ช้อปครบ 400.-

4. 💜 คูปองคอนโด & ของเล่นแมว (Toys & Lifestyle)
   - ไฟล์: coupon_4_toys_lifestyle_25pct_640x640.png
   - โค้ด: PLAY25MEOW
   - สิทธิประโยชน์: ลด 25% คอนโด ที่นอน กระบะทราย ของเล่น ช้อปครบ 600.-

5. ⚡ คูปอง Flash Sale ประจำเดือน (Midnight Flash Deal)
   - ไฟล์: coupon_5_flash_sale_25pct_640x640.png
   - โค้ด: FLASH25NOW
   - สิทธิประโยชน์: ลด 25% ทั้งร้าน ทุกหมวดหมู่ ไม่มีขั้นต่ำ

--------------------------------------------------------------------------------
🛠️ วิธีนำไปตั้งค่าใน LINE Official Account Manager (manager.line.biz):
--------------------------------------------------------------------------------
1. เข้าไปที่เมนู "เครื่องมือการตลาด (Marketing Tools)" -> "คูปอง (Coupons)"
2. คลิก "สร้างใหม่ (Create new)"
3. กรอกชื่อคูปอง เช่น: "คูปองส่วนลด 25% ต้อนรับเพื่อนใหม่"
4. อัปโหลดรูปภาพขนาด 640x640 px จากโฟลเดอร์นี้
5. กำหนดอายุการใช้งาน และจำนวนครั้งที่ใช้ได้ (เช่น 1 ครั้ง / บัญชี)
6. ใส่รหัสคูปอง (Promo Code) และเงื่อนไขการใช้งาน
7. บันทึก และนำไปแนบในข้อความต้อนรับ (Greeting Message) หรือส่งบรอดแคสต์ได้ทันที!
""")
    print(f"✓ Created README: {readme_path}")
    print("🎉 All 5 LINE Coupons (640x640) Rendered & Saved Successfully!")

if __name__ == "__main__":
    main()
