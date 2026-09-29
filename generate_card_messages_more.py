import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

base_dir = os.path.dirname(os.path.abspath(__file__))
line_dir = os.path.join(base_dir, "LINE_ASSETS")
dir_card_msg = os.path.join(line_dir, "3_CARD_MESSAGE")
assets_img_dir = os.path.join(base_dir, "assets", "images")

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

def draw_checkmark(draw, cx, cy, size=14, color=(16, 185, 129)):
    draw.line([(cx - size//2, cy), (cx - size//6, cy + size//2), (cx + size//2, cy - size//2)], fill=color, width=3)

def render_single_card(item, output_filename):
    cw, ch = 1200, 780
    card = Image.new("RGBA", (cw, ch), (255, 253, 250))
    draw = ImageDraw.Draw(card)

    # Ambient subtle background gradient on right
    bg_grad = create_vertical_grad(cw, ch, item['bg_c1'], item['bg_c2'])
    card.paste(bg_grad, (0, 0))

    # Outer border of the whole card
    draw.rounded_rectangle([0, 0, cw - 1, ch - 1], radius=32, outline=item['color'], width=4)

    # 1. Left Photo Section (x=40, y=40, w=560, h=700)
    im_x, im_y, im_w, im_h = 40, 40, 560, 700
    
    # Shadow behind image box
    shadow = Image.new("RGBA", (im_w + 20, im_h + 20), (0, 0, 0, 0))
    s_draw = ImageDraw.Draw(shadow)
    s_draw.rounded_rectangle([10, 10, im_w + 10, im_h + 10], radius=28, fill=(0, 0, 0, 50))
    shadow = shadow.filter(ImageFilter.GaussianBlur(10))
    card.paste(shadow, (im_x - 10, im_y - 10), shadow)

    # Cat Photo Box
    full_im_p = os.path.join(assets_img_dir, item["cat_image"])
    if os.path.exists(full_im_p):
        cat_img = Image.open(full_im_p).convert("RGBA")
        src_w, src_h = cat_img.size
        ratio = max(im_w / src_w, im_h / src_h)
        new_w = int(src_w * ratio)
        new_h = int(src_h * ratio)
        cat_img = cat_img.resize((new_w, new_h), Image.Resampling.LANCZOS)
        
        left = (new_w - im_w) // 2
        top = (new_h - im_h) // 2
        cat_cropped = cat_img.crop((left, top, left + im_w, top + im_h))

        c_mask = Image.new("L", (im_w, im_h), 0)
        ImageDraw.Draw(c_mask).rounded_rectangle([0, 0, im_w, im_h], radius=26, fill=255)
        
        card.paste(cat_cropped, (im_x, im_y), c_mask)

        # Image Frame Border
        draw.rounded_rectangle([im_x, im_y, im_x + im_w, im_y + im_h], radius=26, outline=item["color"], width=4)

    # Overlaid Tag Badge on top of Image (Top-Left of photo)
    tag_font = get_font(21, True)
    tag_bbox = draw.textbbox((0, 0), item['tag_text'], font=tag_font)
    tag_w = tag_bbox[2] - tag_bbox[0] + 45
    draw.rounded_rectangle([im_x + 20, im_y + 20, im_x + 20 + tag_w, im_y + 68], radius=16, fill=(0, 0, 0, 205), outline=item['color'], width=2)
    draw_sparkle(draw, im_x + 38, im_y + 44, 9, item['color'])
    draw.text((im_x + 54, im_y + 30), item['tag_text'], font=tag_font, fill=(255, 255, 255))

    # Overlaid Age/Gender Badge on bottom of photo
    info_font = get_font(19, True)
    info_bbox = draw.textbbox((0, 0), item['photo_info'], font=info_font)
    info_w = info_bbox[2] - info_bbox[0] + 45
    draw.rounded_rectangle([im_x + 20, im_y + im_h - 70, im_x + 20 + info_w, im_y + im_h - 22], radius=16, fill=(0, 0, 0, 195))
    draw_paw(draw, im_x + 38, im_y + im_h - 46, 18, (255, 220, 100))
    draw.text((im_x + 54, im_y + im_h - 60), item['photo_info'], font=info_font, fill=(255, 235, 140))

    # 2. Right Side: Card Information & Marketing Details (rx = 640)
    rx = 640

    # Brand Title Pill
    bp_font = get_font(15, True)
    bp_text = "PURRFECT CAT SHOP  |  PREMIUM CAROUSEL"
    bp_bbox = draw.textbbox((0, 0), bp_text, font=bp_font)
    bp_w = bp_bbox[2] - bp_bbox[0] + 36
    draw.rounded_rectangle([rx, 45, rx + bp_w, 75], radius=12, fill=item['badge_bg'], outline=item['badge_border'], width=2)
    draw.text((rx + 18, 49), bp_text, font=bp_font, fill=item['badge_txt'])

    # Main Card Title
    draw.text((rx, 90), item['title'], font=get_font(34, True), fill=(15, 23, 42))

    # Breed Description
    draw.text((rx, 145), item['description'], font=get_font(21, False), fill=(71, 85, 105))

    # 3 Bullet Badges (Highlights)
    bullet_y = 205
    for idx, (b_title, b_desc) in enumerate(item['highlights']):
        by = bullet_y + (idx * 88)
        draw.rounded_rectangle([rx, by, rx + 500, by + 76], radius=16, fill=(255, 255, 255), outline=item['bullet_border'], width=2)
        # Bullet number circle
        draw.ellipse([rx + 14, by + 16, rx + 56, by + 58], fill=item['color'])
        draw.text((rx + 27, by + 22), str(idx + 1), font=get_font(21, True), fill=(255, 255, 255))
        # Bullet texts
        draw.text((rx + 70, by + 12), b_title, font=get_font(20, True), fill=(15, 23, 42))
        draw.text((rx + 70, by + 40), b_desc, font=get_font(17, False), fill=(100, 116, 139))

    # Bottom Area: Pricing & Action Button (y=490 to 720)
    bot_y = 490
    draw.rounded_rectangle([rx, bot_y, rx + 500, 720], radius=22, fill=(255, 255, 255), outline=item['bullet_border'], width=2)

    # Price Info
    draw.text((rx + 25, bot_y + 18), "ค่าสินสอดพิเศษ:", font=get_font(20, False), fill=(100, 116, 139))
    
    # Original Price Strike
    draw.text((rx + 25, bot_y + 50), item['price_orig'], font=get_font(22, True), fill=(160, 160, 160))
    p_bbox = draw.textbbox((rx + 25, bot_y + 50), item['price_orig'], font=get_font(22, True))
    draw.line([(p_bbox[0] - 2, (p_bbox[1] + p_bbox[3]) // 2), (p_bbox[2] + 4, (p_bbox[1] + p_bbox[3]) // 2)], fill=(220, 38, 38), width=2)

    # Special Price Big
    draw.text((rx + 130, bot_y + 40), item['price_special'], font=get_font(46, True), fill=item['color'])
    draw.text((rx + 330, bot_y + 60), "บาท", font=get_font(22, True), fill=(15, 23, 42))

    # Guarantee line
    draw_checkmark(draw, rx + 35, bot_y + 115, 12, (16, 185, 129))
    draw.text((rx + 50, bot_y + 104), item['guarantee'], font=get_font(17, False), fill=(71, 85, 105))

    # Big CTA Button
    btn_y = bot_y + 140
    btn_w = 460
    btn_h = 70
    draw.rounded_rectangle([rx + 20, btn_y, rx + 20 + btn_w, btn_y + btn_h], radius=18, fill=item['color'])
    draw.text((rx + 120, btn_y + 18), item['btn_text'], font=get_font(24, True), fill=(255, 255, 255))
    draw_paw(draw, rx + 20 + btn_w - 35, btn_y + 35, 24, (255, 255, 255, 120))

    # Save
    final_path = os.path.join(dir_card_msg, output_filename)
    card.convert("RGB").save(final_path, quality=95)
    print(f"✓ Saved 1200x780 Card: {final_path}")

# ==============================================================================
# CONFIGURATIONS FOR 6 NEW CARD MESSAGES
# ==============================================================================

card_items_new = [
    # 5. PERSIAN (น้องปุยหิมะ - เปอร์เซีย)
    {
        'output_filename': 'card_5_persian_1200x780.png',
        'cat_image': 'cat_persian.jpg',
        'tag_text': 'ราชินีขนฟู CFA',
        'photo_info': 'เพศเมีย | อายุ 3 เดือน',
        'color': (225, 29, 72),        # Rose Pink
        'bg_c1': (255, 243, 247),
        'bg_c2': (254, 235, 242),
        'badge_bg': (255, 228, 230),
        'badge_border': (251, 113, 133),
        'badge_txt': (159, 18, 57),
        'title': 'น้องปุยหิมะ (Persian Classic)',
        'description': 'ราชินีแห่งแมวขนยาว หน้าหวาน นิสัยสุภาพ นิ่งสงบ อารมณ์ดี',
        'highlights': [
            ('ขนนุ่มฟูหนาสองชั้น', 'ขนนุ่มฟูระดับพรีเมียม สวยสง่าดั่งปุยหิมะ'),
            ('รักความสงบ เลี้ยงง่าย', 'ไม่ส่งเสียงรบกวน เหมาะกับคอนโดและบ้าน'),
            ('วัคซีนครบ + เพ็ด CFA', 'ตรวจสุขภาพพร้อมย้ายบ้าน รับประกัน 14 วัน')
        ],
        'bullet_border': (254, 205, 211),
        'price_orig': '22,000.-',
        'price_special': '16,500',
        'guarantee': 'ฟรี! Starter Kit 2,500.- | ตรวจสุขภาพครบ',
        'btn_text': 'จองน้องเปอร์เซีย >>'
    },

    # 6. RAGDOLL (น้องคอตตอน - แร็กดอลล์)
    {
        'output_filename': 'card_6_ragdoll_1200x780.png',
        'cat_image': 'cat_ragdoll.jpg',
        'tag_text': 'ตาสีฟ้าคราม TICA',
        'photo_info': 'เพศเมีย | อายุ 2.5 เดือน',
        'color': (2, 132, 199),        # Sky Blue
        'bg_c1': (240, 249, 255),
        'bg_c2': (224, 242, 254),
        'badge_bg': (224, 242, 254),
        'badge_border': (125, 211, 252),
        'badge_txt': (7, 89, 133),
        'title': 'น้องคอตตอน (Ragdoll Princess)',
        'description': 'เจ้าหญิงตาสีฟ้าคราม ตัวนุ่มปวกเปียกเหมือนตุ๊กตาผ้า ไม่กางเล็บ',
        'highlights': [
            ('ตาสีฟ้าครามประกายเพชร', 'สายพันธุ์แท้นำเข้า มาร์คกิ้งคมชัดสมบูรณ์แบบ'),
            ('ขี้อ้อน ปลอดภัยกับเด็ก', 'นุ่มนิ่มอุ้มง่าย อ่อนโยน เข้ากับทุกคนได้ดี'),
            ('ตรวจยีนหัวใจ HCM ผ่าน', 'ใบเพ็ด TICA แท้ + วัคซีนครบ 2 เข็ม')
        ],
        'bullet_border': (186, 230, 253),
        'price_orig': '35,000.-',
        'price_special': '29,000',
        'guarantee': 'ฟรี! กระเป๋าแคปซูลอวกาศ + Starter Kit',
        'btn_text': 'จองน้องแร็กดอลล์ >>'
    },

    # 7. MUNCHKIN (น้องชอร์ตตี้ - มันช์กิ้น ขาสั้น)
    {
        'output_filename': 'card_7_munchkin_1200x780.png',
        'cat_image': 'cat_munchkin.jpg',
        'tag_text': 'ขาสั้นเตี้ยดุ๊กดิ๊ก',
        'photo_info': 'เพศผู้ | อายุ 2 เดือน',
        'color': (234, 88, 12),       # Sunset Orange
        'bg_c1': (255, 247, 237),
        'bg_c2': (254, 243, 199),
        'badge_bg': (254, 243, 199),
        'badge_border': (253, 230, 138),
        'badge_txt': (154, 52, 18),
        'title': 'น้องชอร์ตตี้ (Munchkin Legs)',
        'description': 'เจ้าเหมียวขาสั้นเตี้ยดุ๊กดิ๊ก วิ่งน่ารักสดใส ขี้เล่นอารมณ์ดี',
        'highlights': [
            ('ขาสั้นเตี้ยแท้ WCF', 'ขาสั้นน่ารักดุ๊กดิ๊ก สุขภาพแข็งแรง คล่องแคล่ว'),
            ('ปรับตัวเก่ง เป็นมิตร', 'ชอบเล่นของเล่น เข้ากับคนง่าย ไม่ซนกวนใจ'),
            ('ตรวจสุขภาพข้อต่อครบ', 'ตรวจข้อต่อและกระดูกผ่าน + ฉีดวัคซีนแล้ว')
        ],
        'bullet_border': (254, 215, 170),
        'price_orig': '29,000.-',
        'price_special': '24,000',
        'guarantee': 'ฟรี! คอนโดแมวมินิ + ของเล่นดุ๊กดิ๊ก',
        'btn_text': 'จองน้องมันช์กิ้น >>'
    },

    # 8. BENGAL (น้องจากัวร์ - เบงกอล เสือดาวจิ๋ว)
    {
        'output_filename': 'card_8_bengal_1200x780.png',
        'cat_image': 'cat_bengal.jpg',
        'tag_text': 'ลายเสือดาว Grade A',
        'photo_info': 'เพศผู้ | อายุ 3 เดือน',
        'color': (217, 119, 6),        # Amber Gold
        'bg_c1': (254, 243, 199),
        'bg_c2': (253, 230, 138),
        'badge_bg': (254, 240, 138),
        'badge_border': (250, 204, 21),
        'badge_txt': (113, 63, 18),
        'title': 'น้องจากัวร์ (Bengal Rosetted)',
        'description': 'เสือดาวจิ๋ว ลายกุหลาบทองคำ ขนประกายกลิตเตอร์ ฉลาด ปราดเปรียว',
        'highlights': [
            ('ลาย Rosette ชัดเจน', 'ลายดอกกุหลาบคมชัด ขนสะท้อนแสง Glittering'),
            ('กล้ามเนื้อแกร่ง ฉลาด', 'รักการเล่นน้ำ สามารถฝึกใส่สายจูงเดินเล่นได้'),
            ('เพ็ด WCF + ไมโครชิป', 'สายเลือดแชมป์นำเข้า ฝังไมโครชิปมาตรฐาน')
        ],
        'bullet_border': (253, 230, 138),
        'price_orig': '32,000.-',
        'price_special': '26,000',
        'guarantee': 'ฟรี! สายจูงพรีเมียม + น้ำพุแมวสแตนเลส',
        'btn_text': 'จองน้องเบงกอล >>'
    },

    # 9. SPHYNX (น้องซีซาร์ - สฟิงซ์ ไร้ขน)
    {
        'output_filename': 'card_9_sphynx_1200x780.png',
        'cat_image': 'cat_sphynx.jpg',
        'tag_text': 'ภูมิแพ้ 0% ไร้ขน',
        'photo_info': 'เพศผู้ | อายุ 3 เดือน',
        'color': (13, 148, 136),       # Teal Cyan
        'bg_c1': (240, 253, 250),
        'bg_c2': (204, 251, 241),
        'badge_bg': (204, 251, 241),
        'badge_border': (94, 234, 212),
        'badge_txt': (17, 94, 89),
        'title': 'น้องซีซาร์ (Canadian Sphynx)',
        'description': 'แมวไร้ขน ผิวสัมผัสนุ่มอุ่นดั่งลูกพีช ฉลาด ขี้อ้อนติดคนมาก',
        'highlights': [
            ('ขนร่วง 0% ไร้ภูมิแพ้', 'เหมาะสำหรับคนเป็นภูมิแพ้ขนสัตว์ เลี้ยงสบายใจ'),
            ('ขี้อ้อนติดคนเหมือนสุนัข', 'ชอบนอนซุกผ้าห่ม ฉลาด ตอบสนองคำสั่งไว'),
            ('เพ็ด WCF + ฝังชิปครบ', 'วัคซีนครบ 2 เข็ม + สมุดตรวจสุขภาพประจำตัว')
        ],
        'bullet_border': (153, 246, 228),
        'price_orig': '34,000.-',
        'price_special': '28,000',
        'guarantee': 'ฟรี! เสื้อกันหนาวสฟิงซ์ + เซ็ตสกินแคร์แมว',
        'btn_text': 'จองน้องสฟิงซ์ >>'
    },

    # 10. SIAMESE (น้องมงคล - วิเชียรมาศ แมวมงคล)
    {
        'output_filename': 'card_10_siamese_1200x780.png',
        'cat_image': 'cat_siamese.jpg',
        'tag_text': 'แมวมงคลไทยนำโชค',
        'photo_info': 'เพศเมีย | อายุ 2.5 เดือน',
        'color': (124, 58, 237),       # Royal Purple
        'bg_c1': (245, 243, 255),
        'bg_c2': (237, 233, 254),
        'badge_bg': (237, 233, 254),
        'badge_border': (196, 181, 253),
        'badge_txt': (91, 33, 182),
        'title': 'น้องมงคล (Siamese / วิเชียรมาศ)',
        'description': 'แมวมงคลไทยโบราณ แต้มสี 9 จุดคมชัด ตาสีฟ้าคราม ช่างเจรจา',
        'highlights': [
            ('แมวมงคลนำโชคลาภ', 'แต้มสี 9 จุดครบถ้วนตามตำราแมวไทยโบราณ'),
            ('ช่างพูด ช่างอ้อน ซื่อสัตย์', 'สติปัญญาเฉลียวฉลาด ผูกพันกับเจ้าของมาก'),
            ('สุขภาพแข็งแรง เลี้ยงง่าย', 'ตรวจลิวคีเมีย/เอดส์แมวผ่าน + วัคซีนครบ')
        ],
        'bullet_border': (221, 214, 254),
        'price_orig': '15,000.-',
        'price_special': '12,000',
        'guarantee': 'ฟรี! ปลอกคอกระดิ่งทอง + อาหารมงคล',
        'btn_text': 'จองน้องวิเชียรมาศ >>'
    }
]

def main():
    print("🃏 Generating 6 New Card Messages (1200x780 px)...")
    for item in card_items_new:
        render_single_card(item, item['output_filename'])

    # Write updated README_CARD_MESSAGE.txt
    readme_path = os.path.join(dir_card_msg, "README_CARD_MESSAGE.txt")
    with open(readme_path, "w", encoding="utf-8") as f:
        f.write("""================================================================================
🃏 รวมการ์ดเมสเสจ (Card-based Message Carousel) - Purrfect Shop
================================================================================

📌 สเปกขนาดรูปภาพการ์ดตามมาตรฐาน LINE Official Account:
- สัดส่วนภาพบนการ์ด: 1.54 : 1
- ขนาดภาพที่แนะนำ: 1200 x 780 px (หรือ 1040 x 675 px)
- ฟอร์แมต: PNG หรือ JPG (ขนาดไฟล์ไม่เกิน 10 MB)
- จำนวนการ์ดต่อชุด: สร้างได้สูงสุด 9 การ์ดสินค้า + 1 การ์ดปิดท้าย (End Card)

--------------------------------------------------------------------------------
📋 ตารางข้อมูลการ์ดสินค้า 10 ใบ พร้อมข้อความแท็ก (Tag Texts) สำหรับใส่ใน LINE OA:
--------------------------------------------------------------------------------

[การ์ดที่ 1]
- รูปภาพ: card_1_british_1200x780.png
- ข้อความแท็ก (Tag): ยอดนิยมอันดับ 1
- หัวเรื่อง (Title): น้องบริติช บลู (British Shorthair)
- คำอธิบาย (Description): ขนแน่นนุ่มฟู หน้ากลมแป้น อารมณ์ดี เรียบร้อย
- ราคา (Price): 35,000 บาท
- ลิงก์ Action: https://patches660.github.io/Purrfect-Shop/products.html?breed=british

[การ์ดที่ 2]
- รูปภาพ: card_2_scottish_1200x780.png
- ข้อความแท็ก (Tag): เลี้ยงง่าย ขี้อ้อน
- หัวเรื่อง (Title): น้องสกอตติช โฟลด์ (Scottish Fold)
- คำอธิบาย (Description): หูพับสนิท ตากลมโต ขี้อ้อน ชอบนั่งพุงพลุ้ย
- ราคา (Price): 32,000 บาท
- ลิงก์ Action: https://patches660.github.io/Purrfect-Shop/products.html?breed=scottish

[การ์ดที่ 3]
- รูปภาพ: card_3_mainecoon_1200x780.png
- ข้อความแท็ก (Tag): เกรดพรีเมียม ใบเพ็ดครบ
- หัวเรื่อง (Title): น้องเมนคูน ไจแอนท์ (Maine Coon)
- คำอธิบาย (Description): ยักษ์ใหญ่ใจดี สายเลือดแชมป์ โครงสร้างใหญ่สง่างาม
- ราคา (Price): 55,000 บาท
- ลิงก์ Action: https://patches660.github.io/Purrfect-Shop/products.html?breed=mainecoon

[การ์ดที่ 4]
- รูปภาพ: card_4_welcome_deal_1200x780.png
- ข้อความแท็ก (Tag): สิทธิพิเศษสมาชิก
- หัวเรื่อง (Title): ดีลต้อนรับสมาชิกใหม่
- คำอธิบาย (Description): รับส่วนลดพิเศษ 10-25% พร้อม Starter Kit ฟรี
- ราคา (Price): ลดสูงสุด 25%
- ลิงก์ Action: https://patches660.github.io/Purrfect-Shop/welcome_deal.html

[การ์ดที่ 5]
- รูปภาพ: card_5_persian_1200x780.png
- ข้อความแท็ก (Tag): ราชินีขนฟู CFA
- หัวเรื่อง (Title): น้องปุยหิมะ (Persian Classic)
- คำอธิบาย (Description): ราชินีแห่งแมวขนยาว หน้าหวาน นิสัยสุภาพ นิ่งสงบ อารมณ์ดี
- ราคา (Price): 16,500 บาท
- ลิงก์ Action: https://patches660.github.io/Purrfect-Shop/products.html?breed=persian

[การ์ดที่ 6]
- รูปภาพ: card_6_ragdoll_1200x780.png
- ข้อความแท็ก (Tag): ตาสีฟ้าคราม TICA
- หัวเรื่อง (Title): น้องคอตตอน (Ragdoll Princess)
- คำอธิบาย (Description): เจ้าหญิงตาสีฟ้า ตัวนุ่มดั่งตุ๊กตาผ้า ไม่กางเล็บ ปลอดภัยกับเด็ก
- ราคา (Price): 29,000 บาท
- ลิงก์ Action: https://patches660.github.io/Purrfect-Shop/products.html?breed=ragdoll

[การ์ดที่ 7]
- รูปภาพ: card_7_munchkin_1200x780.png
- ข้อความแท็ก (Tag): ขาสั้นเตี้ยดุ๊กดิ๊ก
- หัวเรื่อง (Title): น้องชอร์ตตี้ (Munchkin Legs)
- คำอธิบาย (Description): ขาสั้นเตี้ยดุ๊กดิ๊ก วิ่งน่ารักสดใส ขี้เล่นอารมณ์ดีตลอดวัน
- ราคา (Price): 24,000 บาท
- ลิงก์ Action: https://patches660.github.io/Purrfect-Shop/products.html?breed=munchkin

[การ์ดที่ 8]
- รูปภาพ: card_8_bengal_1200x780.png
- ข้อความแท็ก (Tag): ลายเสือดาว Grade A
- หัวเรื่อง (Title): น้องจากัวร์ (Bengal Rosetted)
- คำอธิบาย (Description): ลายกุหลาบทองคำ ขนประกายกลิตเตอร์ ฉลาด ปราดเปรียว
- ราคา (Price): 26,000 บาท
- ลิงก์ Action: https://patches660.github.io/Purrfect-Shop/products.html?breed=bengal

[การ์ดที่ 9]
- รูปภาพ: card_9_sphynx_1200x780.png
- ข้อความแท็ก (Tag): ภูมิแพ้ 0% ไร้ขน
- หัวเรื่อง (Title): น้องซีซาร์ (Canadian Sphynx)
- คำอธิบาย (Description): แมวไร้ขน ผิวนุ่มอุ่นดั่งลูกพีช ฉลาด ขี้อ้อนติดคน หมดห่วงภูมิแพ้
- ราคา (Price): 28,000 บาท
- ลิงก์ Action: https://patches660.github.io/Purrfect-Shop/products.html?breed=sphynx

[การ์ดที่ 10]
- รูปภาพ: card_10_siamese_1200x780.png
- ข้อความแท็ก (Tag): แมวมงคลไทยนำโชค
- หัวเรื่อง (Title): น้องมงคล (Siamese / วิเชียรมาศ)
- คำอธิบาย (Description): แต้มสี 9 จุดคมชัด ตาสีฟ้าคราม ช่างพูด เฉลียวฉลาด นำโชคลาภ
- ราคา (Price): 12,000 บาท
- ลิงก์ Action: https://patches660.github.io/Purrfect-Shop/products.html?breed=siamese

--------------------------------------------------------------------------------
🛠️ วิธีนำไปตั้งค่าใน LINE Official Account Manager (manager.line.biz):
--------------------------------------------------------------------------------
1. เข้าไปที่เมนู "เครื่องมือแสดงผล (Display Tools)" -> "การ์ดเมสเสจ (Card-based Messages)"
2. คลิก "สร้างใหม่ (Create new)" -> เลือกประเภทการ์ด "สินค้า (Product)"
3. เลือกรูปภาพ อัปโหลดไฟล์ภาพจากโฟลเดอร์นี้
4. กรอกข้อความแท็ก, หัวเรื่อง, คำอธิบาย, ราคา ตามข้อมูลด้านบน
5. กำหนดปุ่ม Action เช่น "ดูรูป & จองน้องแมว" -> ใส่ URL หน้าร้าน
6. กดบันทึกและนำไปใช้บรอดแคสต์ให้ลูกค้าเลื่อนสไลด์ดูน้องแมวได้ทันที!
""")
    print(f"✓ Updated README: {readme_path}")
    print("🎉 All 6 New Card Messages Created Successfully!")

if __name__ == "__main__":
    main()
