import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

base_dir = os.path.dirname(os.path.abspath(__file__))
line_dir = os.path.join(base_dir, "LINE_ASSETS")
dir_rich_msg = os.path.join(line_dir, "1_RICH_MESSAGE")
dir_rich_video = os.path.join(line_dir, "2_RICH_VIDEO_MESSAGE")
dir_card_msg = os.path.join(line_dir, "3_CARD_MESSAGE")

os.makedirs(dir_rich_msg, exist_ok=True)
os.makedirs(dir_rich_video, exist_ok=True)
os.makedirs(dir_card_msg, exist_ok=True)

# Fonts
font_bold_path = "C:/Windows/Fonts/LeelaUIb.ttf" if os.path.exists("C:/Windows/Fonts/LeelaUIb.ttf") else "C:/Windows/Fonts/tahomabd.ttf"
font_reg_path = "C:/Windows/Fonts/LeelawUI.ttf" if os.path.exists("C:/Windows/Fonts/LeelawUI.ttf") else "C:/Windows/Fonts/tahoma.ttf"

def get_font(size, bold=False):
    p = font_bold_path if bold else font_reg_path
    try:
        return ImageFont.truetype(p, int(size))
    except:
        return ImageFont.load_default()

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

def create_rich_gold_gradient(w, h, angle_deg=35):
    stops = [
        (0.00, (255, 107, 74)),
        (0.20, (245, 158, 11)),
        (0.40, (255, 220, 120)),
        (0.60, (255, 120, 80)),
        (0.80, (217, 119, 6)),
        (1.00, (255, 90, 60))
    ]
    rad = math.radians(angle_deg)
    cos_a = math.cos(rad)
    sin_a = math.sin(rad)
    grad = Image.new("RGBA", (w, h))
    pixels = grad.load()
    max_proj = abs(w * cos_a) + abs(h * sin_a)
    for y in range(h):
        for x in range(w):
            proj = (x * cos_a + y * sin_a) / max_proj
            proj = max(0.0, min(1.0, (proj + 0.5) % 1.0))
            c1, c2 = stops[0], stops[-1]
            for i in range(len(stops) - 1):
                if stops[i][0] <= proj <= stops[i+1][0]:
                    c1, c2 = stops[i], stops[i+1]
                    break
            t = (proj - c1[0]) / max(0.0001, (c2[0] - c1[0]))
            r = int(c1[1][0] + t * (c2[1][0] - c1[1][0]))
            g = int(c1[1][1] + t * (c2[1][1] - c1[1][1]))
            b = int(c1[1][2] + t * (c2[1][2] - c1[1][2]))
            pixels[x, y] = (r, g, b, 255)
    return grad

def draw_paw(draw, cx, cy, size, fill=(255, 107, 74, 180)):
    pad_w = int(size * 0.5)
    pad_h = int(size * 0.4)
    draw.ellipse([cx - pad_w//2, cy - pad_h//2, cx + pad_w//2, cy + pad_h//2], fill=fill)
    toe_r = int(size * 0.16)
    draw.ellipse([cx - int(size * 0.3) - toe_r, cy - int(size * 0.3) - toe_r, cx - int(size * 0.3) + toe_r, cy - int(size * 0.3) + toe_r], fill=fill)
    draw.ellipse([cx - int(size * 0.1) - toe_r, cy - int(size * 0.42) - toe_r, cx - int(size * 0.1) + toe_r, cy - int(size * 0.42) + toe_r], fill=fill)
    draw.ellipse([cx + int(size * 0.12) - toe_r, cy - int(size * 0.42) - toe_r, cx + int(size * 0.12) + toe_r, cy - int(size * 0.42) + toe_r], fill=fill)
    draw.ellipse([cx + int(size * 0.32) - toe_r, cy - int(size * 0.3) - toe_r, cx + int(size * 0.32) + toe_r, cy - int(size * 0.3) + toe_r], fill=fill)

def draw_gold_star(draw, cx, cy, r=12, fill=(245, 158, 11)):
    draw.polygon([(cx, cy - r), (cx + 2, cy), (cx, cy + r), (cx - 2, cy)], fill=fill)
    draw.polygon([(cx - r, cy), (cx, cy + 2), (cx + r, cy), (cx, cy - 2)], fill=fill)
    d = int(r * 0.45)
    draw.polygon([(cx - d, cy - d), (cx, cy), (cx + d, cy + d), (cx, cy)], fill=fill)
    draw.polygon([(cx + d, cy - d), (cx, cy), (cx - d, cy + d), (cx, cy)], fill=fill)

# ==============================================================================
# 1. GENERATE RICH MESSAGE ASSETS (1040 x 1040 Square & 1040 x 520 Compact)
# ==============================================================================
def gen_rich_messages():
    print("🎨 Generating Rich Messages...")
    # 1.1 Square 1040 x 1040
    w, h = 1040, 1040
    img = Image.new("RGBA", (w, h), (255, 252, 248))
    draw = ImageDraw.Draw(img)

    # Top header gradient banner
    hdr_h = 160
    hdr_grad = create_horizontal_grad(w, hdr_h, (255, 90, 50), (245, 158, 11))
    img.paste(hdr_grad, (0, 0))

    # Header text
    draw.text((60, 42), "PURRFECT BOUTIQUE & CATTERY", fill=(255, 255, 255), font=get_font(36, bold=True))
    draw.text((60, 95), "ศูนย์รวมน้องแมวสายพันธุ์แท้ 100% • เอกสิทธิ์โปรโมชันเปิดร้านใหม่", fill=(254, 243, 199), font=get_font(24))
    draw_paw(draw, 960, 75, 48, fill=(255, 255, 255, 200))

    # Center Cat Image Showcase with Gold Frame
    cat_p = os.path.join(base_dir, "assets/images/cat_british.jpg")
    if os.path.exists(cat_p):
        c_raw = Image.open(cat_p).convert("RGBA").resize((520, 520), Image.Resampling.LANCZOS)
        c_mask = Image.new("L", (520, 520), 0)
        ImageDraw.Draw(c_mask).rounded_rectangle([0, 0, 520, 520], radius=32, fill=255)
        c_framed = Image.new("RGBA", (520, 520), (0, 0, 0, 0))
        c_framed.paste(c_raw, (0, 0), c_mask)
        ImageDraw.Draw(c_framed).rounded_rectangle([0, 0, 519, 519], radius=32, outline=(245, 158, 11), width=6)
        img.paste(c_framed, (60, 200), c_framed)

    # Right Content Details
    rw_x = 615
    draw.rounded_rectangle([rw_x, 200, 980, 255], radius=16, fill=(255, 237, 230), outline=(255, 107, 74), width=2)
    draw.text((rw_x + 20, 212), "🔥 SPECIAL GRAND OPENING", fill=(225, 65, 38), font=get_font(22, bold=True))

    draw.text((rw_x, 275), "ลดทันที 10%", fill=(225, 65, 38), font=get_font(56, bold=True))
    draw.text((rw_x, 345), "เมื่อช้อปขั้นต่ำ 300.-", fill=(100, 116, 139), font=get_font(24, bold=True))

    # Promo code ticket box
    draw.rounded_rectangle([rw_x, 400, 980, 485], radius=16, fill=(255, 255, 255), outline=(245, 158, 11), width=3)
    draw.text((rw_x + 24, 412), "โค้ดส่วนลด:", fill=(100, 116, 139), font=get_font(18))
    draw.text((rw_x + 24, 435), "CAT10OFF", fill=(217, 119, 6), font=get_font(34, bold=True))
    draw.text((rw_x + 230, 428), "[ ใช้ได้ 1 ครั้ง/เดือน ]", fill=(5, 150, 105), font=get_font(16, bold=True))

    # 3 Bullet Features
    f_y = 515
    features = [
        ("💉 วัคซีนครบ 2 เข็ม + ถ่ายพยาธิ", (5, 150, 105)),
        ("🔬 ตรวจแล็บปลอดโรค FeLV/FIV 100%", (217, 119, 6)),
        ("🛡️ การันตีคุ้มครองสุขภาพ 180 วัน", (225, 65, 38)),
        ("🎁 ฟรี! Starter Kit 11 รายการ (4,500฿)", (147, 51, 234))
    ]
    for text, col in features:
        draw_gold_star(draw, rw_x + 12, f_y + 12, r=8, fill=col)
        draw.text((rw_x + 30, f_y), text, fill=(30, 41, 59), font=get_font(21, bold=True))
        f_y += 50

    # Bottom Full-width CTA Banner
    btn_y = 755
    btn_grad = create_horizontal_grad(920, 120, (255, 90, 50), (245, 158, 11))
    btn_mask = Image.new("L", (920, 120), 0)
    ImageDraw.Draw(btn_mask).rounded_rectangle([0, 0, 920, 120], radius=30, fill=255)
    btn_layer = Image.new("RGBA", (920, 120), (0, 0, 0, 0))
    btn_layer.paste(btn_grad, (0, 0), btn_mask)
    img.paste(btn_layer, (60, btn_y), btn_layer)

    draw = ImageDraw.Draw(img)
    draw.text((120, btn_y + 26), "🐾 เลือกชมน้องแมว & รับสิทธิ์ส่วนลดทันที >", fill=(255, 255, 255), font=get_font(36, bold=True))
    draw.text((120, btn_y + 72), "คลิกเพื่อเปิดหน้าเว็บ Purrfect Shop บน GitHub Pages", fill=(254, 243, 199), font=get_font(22))

    # Footer note
    draw.text((60, 910), "* สิทธิพิเศษเฉพาะสมาชิกผ่าน LINE Official • ส่งฟรีทั่วประเทศด้วยรถ Pet Taxi ควบคุมอุณหภูมิ", fill=(120, 135, 150), font=get_font(19))
    draw.rounded_rectangle([0, 0, w-1, h-1], outline=(255, 107, 74), width=6)

    out_p = os.path.join(dir_rich_msg, "rich_message_1040x1040_square.png")
    img.save(out_p, "PNG")
    print(f"✓ Saved: {out_p}")

    # 1.2 Compact 1040 x 520 (Wide Banner)
    w2, h2 = 1040, 520
    img2 = Image.new("RGBA", (w2, h2), (255, 252, 248))
    bg_grad = create_horizontal_grad(w2, h2, (255, 245, 238), (254, 247, 230))
    img2.paste(bg_grad, (0, 0))
    draw2 = ImageDraw.Draw(img2)

    # Cat Thumb on Left
    if os.path.exists(cat_p):
        c_raw2 = Image.open(cat_p).convert("RGBA").resize((420, 420), Image.Resampling.LANCZOS)
        c_mask2 = Image.new("L", (420, 420), 0)
        ImageDraw.Draw(c_mask2).rounded_rectangle([0, 0, 420, 420], radius=24, fill=255)
        c_framed2 = Image.new("RGBA", (420, 420), (0, 0, 0, 0))
        c_framed2.paste(c_raw2, (0, 0), c_mask2)
        ImageDraw.Draw(c_framed2).rounded_rectangle([0, 0, 419, 419], radius=24, outline=(245, 158, 11), width=4)
        img2.paste(c_framed2, (45, 50), c_framed2)

    # Right Content
    rx2 = 500
    draw2.rounded_rectangle([rx2, 45, 995, 95], radius=14, fill=(255, 107, 74))
    draw2.text((rx2 + 20, 55), "👑 PURRFECT SPECIAL PRIVILEGE", fill=(255, 255, 255), font=get_font(22, bold=True))

    draw2.text((rx2, 115), "ลดทันที 10% (CAT10OFF)", fill=(225, 65, 38), font=get_font(38, bold=True))
    draw2.text((rx2, 175), "• ใช้ได้เมื่อมียอดสั่งซื้อขั้นต่ำ 300 บาทขึ้นไป\n• การันตีสุขภาพ 180 วัน + วัคซีนครบ 2 เข็ม\n• แถมฟรี Starter Kit มูลค่า 4,500 บาท", fill=(45, 55, 72), font=get_font(21))

    # CTA Button
    btn_w2, btn_h2 = 495, 68
    btn_y2 = 390
    btn_grad2 = create_horizontal_grad(btn_w2, btn_h2, (255, 90, 50), (245, 158, 11))
    btn_mask2 = Image.new("L", (btn_w2, btn_h2), 0)
    ImageDraw.Draw(btn_mask2).rounded_rectangle([0, 0, btn_w2, btn_h2], radius=34, fill=255)
    btn_layer2 = Image.new("RGBA", (btn_w2, btn_h2), (0, 0, 0, 0))
    btn_layer2.paste(btn_grad2, (0, 0), btn_mask2)
    img2.paste(btn_layer2, (rx2, btn_y2), btn_layer2)

    draw2 = ImageDraw.Draw(img2)
    draw2.text((rx2 + 85, btn_y2 + 16), "🐾 กดรับสิทธิ์ & ชมน้องแมว >", fill=(255, 255, 255), font=get_font(26, bold=True))
    draw2.rounded_rectangle([0, 0, w2-1, h2-1], outline=(255, 107, 74), width=5)

    out_p2 = os.path.join(dir_rich_msg, "rich_message_1040x520_compact.png")
    img2.save(out_p2, "PNG")
    print(f"✓ Saved: {out_p2}")

# ==============================================================================
# 2. GENERATE RICH VIDEO MESSAGE COVER TEMPLATES
# ==============================================================================
def gen_rich_video_templates():
    print("🎬 Generating Rich Video Message Covers...")
    # 2.1 Video Cover 16:9 (1920 x 1080)
    w, h = 1920, 1080
    img = Image.new("RGBA", (w, h), (20, 24, 33))
    draw = ImageDraw.Draw(img)

    # Ambient Glow
    cat_p = os.path.join(base_dir, "assets/images/cat_british.jpg")
    if os.path.exists(cat_p):
        c_bg = Image.open(cat_p).convert("RGBA").resize((w, h), Image.Resampling.LANCZOS)
        c_bg = c_bg.filter(ImageFilter.GaussianBlur(35))
        img.paste(c_bg, (0, 0))

    overlay = Image.new("RGBA", (w, h), (15, 20, 30, 160))
    img.paste(overlay, (0, 0), overlay)
    draw = ImageDraw.Draw(img)

    # Video Frame Center Box
    if os.path.exists(cat_p):
        c_main = Image.open(cat_p).convert("RGBA").resize((800, 800), Image.Resampling.LANCZOS)
        c_m_mask = Image.new("L", (800, 800), 0)
        ImageDraw.Draw(c_m_mask).rounded_rectangle([0, 0, 800, 800], radius=40, fill=255)
        c_box = Image.new("RGBA", (800, 800), (0, 0, 0, 0))
        c_box.paste(c_main, (0, 0), c_m_mask)
        ImageDraw.Draw(c_box).rounded_rectangle([0, 0, 799, 799], radius=40, outline=(245, 158, 11), width=8)
        img.paste(c_box, (140, 140), c_box)

    # Play Icon Indicator
    play_cx, play_cy = 540, 540
    draw.ellipse([play_cx - 65, play_cy - 65, play_cx + 65, play_cy + 65], fill=(255, 107, 74, 230), outline=(255, 255, 255), width=4)
    draw.polygon([(play_cx - 20, play_cy - 35), (play_cx + 35, play_cy), (play_cx - 20, play_cy + 35)], fill=(255, 255, 255))

    # Right Text Info
    tx = 1010
    draw.rounded_rectangle([tx, 140, 1780, 210], radius=18, fill=(245, 158, 11))
    draw.text((tx + 30, 155), "🎬 RICH VIDEO PREVIEW • 16:9 FULL HD", fill=(255, 255, 255), font=get_font(32, bold=True))

    draw.text((tx, 250), "ชมความน่ารักน้องแมวตัวจริง 🐾", fill=(255, 255, 255), font=get_font(52, bold=True))
    draw.text((tx, 330), "Purrfect Shop Cattery & Boutique", fill=(254, 215, 170), font=get_font(36))

    draw.text((tx, 430), "• วิดีโอเล่นอัตโนมัติ (Auto-play) ทันทีที่เปิดแชท\n• สัดส่วนมาตรฐาน 16:9 แนวนอน คมชัดระดับ Full HD\n• ความยาวแนะนำ 10-30 วินาที (ขนาดไฟล์ไม่เกิน 200 MB)\n• มีปุ่ม Call-to-Action ด้านล่างวิดีโอ กดลิงก์ไปหน้าร้านได้ทันที", fill=(226, 232, 240), font=get_font(28))

    # Button Simulation
    btn_y = 780
    draw.rounded_rectangle([tx, btn_y, 1780, btn_y + 110], radius=55, fill=(255, 107, 74), outline=(255, 255, 255), width=3)
    draw.text((tx + 180, btn_y + 28), "🐾 รับเลี้ยง & จองน้องแมวตัวนี้ >", fill=(255, 255, 255), font=get_font(38, bold=True))

    out_v1 = os.path.join(dir_rich_video, "video_cover_16x9_1920x1080.png")
    img.save(out_v1, "PNG")
    print(f"✓ Saved: {out_v1}")

    # 2.2 Video Cover 1:1 (1080 x 1080 Square)
    w_sq, h_sq = 1080, 1080
    img_sq = Image.new("RGBA", (w_sq, h_sq), (20, 24, 33))
    if os.path.exists(cat_p):
        c_sq = Image.open(cat_p).convert("RGBA").resize((w_sq, h_sq), Image.Resampling.LANCZOS)
        img_sq.paste(c_sq, (0, 0))
    ov_sq = Image.new("RGBA", (w_sq, h_sq), (15, 20, 30, 110))
    img_sq.paste(ov_sq, (0, 0), ov_sq)
    draw_sq = ImageDraw.Draw(img_sq)

    # Play Icon in Center
    draw_sq.ellipse([w_sq//2 - 75, h_sq//2 - 75, w_sq//2 + 75, h_sq//2 + 75], fill=(255, 107, 74, 230), outline=(255, 255, 255), width=5)
    draw_sq.polygon([(w_sq//2 - 25, h_sq//2 - 40), (w_sq//2 + 40, h_sq//2), (w_sq//2 - 25, h_sq//2 + 40)], fill=(255, 255, 255))

    # Top & Bottom Banners
    draw_sq.rounded_rectangle([50, 50, w_sq - 50, 120], radius=20, fill=(15, 20, 30, 220), outline=(245, 158, 11), width=3)
    draw_sq.text((80, 68), "🎬 RICH VIDEO SQUARE (1:1) • PURRFECT CATTERY", fill=(255, 255, 255), font=get_font(30, bold=True))

    draw_sq.rounded_rectangle([50, h_sq - 150, w_sq - 50, h_sq - 50], radius=30, fill=(255, 107, 74), outline=(255, 255, 255), width=3)
    draw_sq.text((220, h_sq - 120), "🐾 กดเพื่อรับเลี้ยงน้องแมวตัวนี้ >", fill=(255, 255, 255), font=get_font(36, bold=True))

    out_v2 = os.path.join(dir_rich_video, "video_cover_1x1_1080x1080.png")
    img_sq.save(out_v2, "PNG")
    print(f"✓ Saved: {out_v2}")

# ==============================================================================
# 3. GENERATE CARD MESSAGE ASSETS (1200 x 780 Ratio 1.54:1)
# ==============================================================================
def gen_card_messages():
    print("🃏 Generating Card Messages (Carousel Cards)...")
    cw, ch = 1200, 780

    card_items = [
        {
            "filename": "card_1_british_1200x780.png",
            "img_path": "assets/images/cat_british.jpg",
            "title": "น้องบริติช บลู (British Shorthair)",
            "sub": "เพศผู้ • อายุ 2.5 เดือน • ขนแน่นนุ่มฟู หน้ากลมแป้น",
            "price": "35,000 ฿",
            "tag": "🔥 ยอดนิยมอันดับ 1",
            "color": (255, 107, 74)
        },
        {
            "filename": "card_2_scottish_1200x780.png",
            "img_path": "assets/images/cat_scottish.jpg",
            "title": "น้องสกอตติช โฟลด์ (Scottish Fold)",
            "sub": "เพศเมีย • อายุ 2 เดือน • หูพับสนิท ตากลมโต ขี้อ้อน",
            "price": "32,000 ฿",
            "tag": "🧸 เลี้ยงง่าย ขี้อ้อน",
            "color": (217, 119, 6)
        },
        {
            "filename": "card_3_mainecoon_1200x780.png",
            "img_path": "assets/images/cat_mainecoon.jpg",
            "title": "น้องเมนคูน ไจแอนท์ (Maine Coon)",
            "sub": "เพศผู้ • สายเลือดแชมป์ WCF/CFA • โครงสร้างใหญ่สง่างาม",
            "price": "55,000 ฿",
            "tag": "👑 เกรดพรีเมียม ใบเพ็ดครบ",
            "color": (147, 51, 234)
        },
        {
            "filename": "card_4_welcome_deal_1200x780.png",
            "img_path": "assets/images/coupon_10pct.png",
            "title": "ดีลต้อนรับสมาชิก & Starter Kit ฟรี!",
            "sub": "รับส่วนลด 10% (CAT10OFF) + ของแถม 11 รายการ มูลค่า 4,500.-",
            "price": "ส่วนลด 10%",
            "tag": "🎁 สิทธิพิเศษสมาชิก",
            "color": (5, 150, 105)
        }
    ]

    for item in card_items:
        card = Image.new("RGBA", (cw, ch), (255, 252, 248))
        draw = ImageDraw.Draw(card)

        # Image Section (Left Half)
        im_w, im_h = 580, 700
        full_im_p = os.path.join(base_dir, item["img_path"])
        if os.path.exists(full_im_p):
            c_raw = Image.open(full_im_p).convert("RGBA").resize((im_w, im_h), Image.Resampling.LANCZOS)
            c_mask = Image.new("L", (im_w, im_h), 0)
            ImageDraw.Draw(c_mask).rounded_rectangle([0, 0, im_w, im_h], radius=28, fill=255)
            c_box = Image.new("RGBA", (im_w, im_h), (0, 0, 0, 0))
            c_box.paste(c_raw, (0, 0), c_mask)
            ImageDraw.Draw(c_box).rounded_rectangle([0, 0, im_w - 1, im_h - 1], radius=28, outline=item["color"], width=5)
            card.paste(c_box, (40, 40), c_box)

        # Right Content Section
        rx = 660
        draw.rounded_rectangle([rx, 40, 1140, 95], radius=16, fill=(255, 243, 235), outline=item["color"], width=2)
        draw.text((rx + 20, 52), item["tag"], fill=item["color"], font=get_font(24, bold=True))

        draw.text((rx, 125), item["title"], fill=(30, 41, 59), font=get_font(34, bold=True))
        draw.text((rx, 185), item["sub"], fill=(100, 116, 139), font=get_font(22))

        # Price Box
        draw.rounded_rectangle([rx, 260, 1140, 360], radius=20, fill=(255, 255, 255), outline=(226, 232, 240), width=2)
        draw.text((rx + 24, 275), "ค่าสินสอด / ราคาพิเศษ:", fill=(100, 116, 139), font=get_font(20))
        draw.text((rx + 24, 305), item["price"], fill=(225, 65, 38), font=get_font(42, bold=True))

        # Benefits
        b_y = 390
        b_list = [
            "🩺 ฉีดวัคซีนรวม + ถ่ายพยาธิครบ",
            "🔬 ตรวจแล็บปลอดโรค FeLV/FIV 100%",
            "🛡️ การันตีสุขภาพโรคร้ายแรง 180 วัน",
            "🚗 ส่งฟรีทั่วประเทศด้วยรถ Pet Taxi"
        ]
        for b in b_list:
            draw_gold_star(draw, rx + 10, b_y + 10, r=7, fill=(245, 158, 11))
            draw.text((rx + 28, b_y), b, fill=(51, 65, 85), font=get_font(21))
            b_y += 45

        # Button
        btn_w, btn_h = 480, 80
        btn_y = 660
        btn_grad = create_horizontal_grad(btn_w, btn_h, (255, 90, 50), (245, 158, 11))
        btn_mask = Image.new("L", (btn_w, btn_h), 0)
        ImageDraw.Draw(btn_mask).rounded_rectangle([0, 0, btn_w, btn_h], radius=40, fill=255)
        btn_layer = Image.new("RGBA", (btn_w, btn_h), (0, 0, 0, 0))
        btn_layer.paste(btn_grad, (0, 0), btn_mask)
        card.paste(btn_layer, (rx, btn_y), btn_layer)

        draw = ImageDraw.Draw(card)
        draw.text((rx + 110, btn_y + 20), "🐾 ดูตัวจริง & จองน้อง >", fill=(255, 255, 255), font=get_font(28, bold=True))
        draw.rounded_rectangle([0, 0, cw - 1, ch - 1], outline=(255, 107, 74), width=5)

        out_c = os.path.join(dir_card_msg, item["filename"])
        card.save(out_c, "PNG")
        print(f"✓ Saved: {out_c}")

# ==============================================================================
# 4. CREATE INSTRUCTION README FILES IN EACH FOLDER
# ==============================================================================
def gen_readmes():
    # 4.1 Rich Message Readme
    readme_rm = """================================================================================
📸 คู่มือขนาดภาพ & การตั้งค่า: ริชเมสเสจ (Rich Message)
================================================================================

📌 ข้อมูลขนาดภาพที่ถูกต้องตามมาตรฐาน LINE OA:
1. สัดส่วนจัตุรัส (Square - แนะนำที่สุด): 1040 x 1040 px
   - ไฟล์ตัวอย่าง: rich_message_1040x1040_square.png
2. สัดส่วนผืนผ้าแนวนอน (Compact Banner): 1040 x 520 px
   - ไฟล์ตัวอย่าง: rich_message_1040x520_compact.png
3. รูปแบบไฟล์: PNG หรือ JPG (ขนาดไฟล์ไม่เกิน 10 MB)

🛠️ วิธีนำไปใช้งานใน LINE OA Manager (manager.line.biz):
1. ไปที่เมนู "เครื่องมือแสดงผล (Display Tools)" -> "ริชเมสเสจ (Rich Messages)"
2. คลิก "สร้างใหม่ (Create new)"
3. เลือกเทมเพลต (เช่น 1 ช่องเต็ม หรือแบ่ง 2 ช่อง)
4. อัปโหลดรูปภาพจากโฟลเดอร์นี้
5. กำหนด Action เป็น "ลิงก์ (Link)" -> ใส่ URL:
   - หน้าร้าน: https://patches660.github.io/Purrfect-Shop/products.html
   - หน้าโปรโมชั่น: https://patches660.github.io/Purrfect-Shop/welcome_deal.html
6. บันทึกและนำไปใช้ในการบรอดแคสต์ได้ทันที!
"""
    with open(os.path.join(dir_rich_msg, "README_RICH_MESSAGE.txt"), "w", encoding="utf-8") as f:
        f.write(readme_rm)

    # 4.2 Rich Video Readme
    readme_rv = """================================================================================
🎬 คู่มือขนาดภาพ & วิดีโอ: ริชวิดีโอเมสเสจ (Rich Video Message)
================================================================================

📌 สเปกวิดีโอและภาพปกที่ถูกต้องตามมาตรฐาน LINE OA:
1. สัดส่วนวิดีโอที่รองรับ:
   - แนวนอน (Landscape 16:9): 1920 x 1080 px (ไฟล์ตัวอย่าง: video_cover_16x9_1920x1080.png)
   - จัตุรัส (Square 1:1): 1080 x 1080 px (ไฟล์ตัวอย่าง: video_cover_1x1_1080x1080.png)
   - แนวตั้ง (Vertical 9:16): 1080 x 1920 px (สไตล์ Reels / TikTok)
2. ฟอร์แมตไฟล์วิดีโอ: MP4, MOV (ขนาดไฟล์ไม่เกิน 200 MB)
3. ความยาววิดีโอที่แนะนำ: 10 - 30 วินาที

🛠️ วิธีนำไปใช้งานใน LINE OA Manager (manager.line.biz):
1. ไปที่เมนู "เครื่องมือแสดงผล (Display Tools)" -> "ริชวิดีโอเมสเสจ (Rich Video Messages)"
2. คลิก "สร้างใหม่ (Create new)"
3. อัปโหลดไฟล์วิดีโอน้องแมว
4. ตั้งค่าปุ่ม Action (Call to Action Button):
   - ข้อความบนปุ่ม: "รับเลี้ยงน้องแมว 🐾" หรือ "ดูค่าสินสอด & จอง 🛒"
   - ลิงก์ปลายทาง: https://patches660.github.io/Purrfect-Shop/products.html
5. กดบันทึกและนำไปใช้บรอดแคสต์ได้เลย!
"""
    with open(os.path.join(dir_rich_video, "README_RICH_VIDEO.txt"), "w", encoding="utf-8") as f:
        f.write(readme_rv)

    # 4.3 Card Message Readme
    readme_cm = """================================================================================
🃏 คู่มือขนาดภาพ & การตั้งค่า: การ์ดเมสเสจ (Card-based Message / Carousel)
================================================================================

📌 ข้อมูลขนาดภาพการ์ดตามมาตรฐาน LINE OA:
1. สัดส่วนภาพบนการ์ด: 1.54 : 1
   - ขนาดภาพที่แนะนำ: 1200 x 780 px (หรือ 1040 x 675 px)
   - ไฟล์ตัวอย่าง:
     • card_1_british_1200x780.png (น้องบริติช ช็อตแฮร์)
     • card_2_scottish_1200x780.png (น้องสกอตติช โฟลด์)
     • card_3_mainecoon_1200x780.png (น้องเมนคูน ไจแอนท์)
     • card_4_welcome_deal_1200x780.png (ดีลส่วนลด 10% & Starter Kit)
2. รูปแบบไฟล์: PNG หรือ JPG (ขนาดไม่เกิน 10 MB)

🛠️ วิธีนำไปใช้งานใน LINE OA Manager (manager.line.biz):
1. ไปที่เมนู "เครื่องมือแสดงผล (Display Tools)" -> "การ์ดเมสเสจ (Card-based Messages)"
2. คลิก "สร้างใหม่ (Create new)" -> เลือกประเภทการ์ด "สินค้า (Product)"
3. เพิ่มการ์ดสินค้าได้สูงสุด 9 การ์ด (+ 1 การ์ดปิดท้าย):
   - การ์ดที่ 1: อัปโหลด card_1_british_1200x780.png -> ใส่ราคา 35,000 บาท -> ลิงก์หน้าร้าน
   - การ์ดที่ 2: อัปโหลด card_2_scottish_1200x780.png -> ใส่ราคา 32,000 บาท -> ลิงก์หน้าร้าน
   - การ์ดที่ 3: อัปโหลด card_3_mainecoon_1200x780.png -> ใส่ราคา 55,000 บาท -> ลิงก์หน้าร้าน
   - การ์ดที่ 4: อัปโหลด card_4_welcome_deal_1200x780.png -> ดีลสมาชิกใหม่
4. กดบันทึกและใช้ส่งบรอดแคสต์ให้ลูกค้าปัดสไลด์เลือกดูน้องแมวได้เพลินๆ ครับ!
"""
    with open(os.path.join(dir_card_msg, "README_CARD_MESSAGE.txt"), "w", encoding="utf-8") as f:
        f.write(readme_cm)

    print("✓ Created README documentation in all 3 folders!")

if __name__ == "__main__":
    gen_rich_messages()
    gen_rich_video_templates()
    gen_card_messages()
    gen_readmes()
    print("\n🎉 All LINE Marketing Folders and Assets Created Successfully!")
