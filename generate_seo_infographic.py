import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

base_dir = os.path.dirname(os.path.abspath(__file__))
art_dir = r"C:\Users\Windows\.gemini\antigravity\brain\63a36a4c-6ead-4c37-aede-64d365cc7888"
char_img_path = os.path.join(art_dir, "purrfect_character_art_1790824776641.jpg")

# Output files
out_png_1 = os.path.join(base_dir, "seo_meta_tags_purrfect_shop.png")
out_png_2 = os.path.join(art_dir, "seo_meta_tags_purrfect_shop.png")

W, H = 1920, 1080
canvas = Image.new("RGBA", (W, H), (255, 252, 248, 255))
draw = ImageDraw.Draw(canvas)

# Fonts
font_bold_p = "C:/Windows/Fonts/LeelaUIb.ttf" if os.path.exists("C:/Windows/Fonts/LeelaUIb.ttf") else "C:/Windows/Fonts/tahomabd.ttf"
font_reg_p = "C:/Windows/Fonts/LeelawUI.ttf" if os.path.exists("C:/Windows/Fonts/LeelawUI.ttf") else "C:/Windows/Fonts/tahoma.ttf"
font_sans_bold = "C:/Windows/Fonts/segouib.ttf" if os.path.exists("C:/Windows/Fonts/segouib.ttf") else "C:/Windows/Fonts/arialbd.ttf"
font_script_p = "C:/Windows/Fonts/SegoeScript.ttf" if os.path.exists("C:/Windows/Fonts/SegoeScript.ttf") else "C:/Windows/Fonts/BRUSHSCI.TTF"

def get_font(size, bold=False, script=False, sans=False):
    if script and os.path.exists(font_script_p):
        return ImageFont.truetype(font_script_p, int(size))
    if sans and os.path.exists(font_sans_bold):
        return ImageFont.truetype(font_sans_bold, int(size))
    p = font_bold_p if bold else font_reg_p
    try:
        return ImageFont.truetype(p, int(size))
    except:
        return ImageFont.load_default()

def draw_paw(d, cx, cy, size, fill=(244, 63, 94, 255)):
    pad_w = int(size * 0.5)
    pad_h = int(size * 0.4)
    d.ellipse([cx - pad_w//2, cy - pad_h//2, cx + pad_w//2, cy + pad_h//2], fill=fill)
    toe_r = int(size * 0.16)
    d.ellipse([cx - int(size * 0.3) - toe_r, cy - int(size * 0.3) - toe_r, cx - int(size * 0.3) + toe_r, cy - int(size * 0.3) + toe_r], fill=fill)
    d.ellipse([cx - int(size * 0.1) - toe_r, cy - int(size * 0.42) - toe_r, cx - int(size * 0.1) + toe_r, cy - int(size * 0.42) + toe_r], fill=fill)
    d.ellipse([cx + int(size * 0.12) - toe_r, cy - int(size * 0.42) - toe_r, cx + int(size * 0.12) + toe_r, cy - int(size * 0.42) + toe_r], fill=fill)
    d.ellipse([cx + int(size * 0.32) - toe_r, cy - int(size * 0.3) - toe_r, cx + int(size * 0.32) + toe_r, cy - int(size * 0.3) + toe_r], fill=fill)

def draw_sparkle(d, cx, cy, r=14, fill=(251, 146, 60)):
    d.polygon([(cx, cy - r), (cx + 2, cy), (cx, cy + r), (cx - 2, cy)], fill=fill)
    d.polygon([(cx - r, cy), (cx, cy + 2), (cx + r, cy), (cx, cy - 2)], fill=fill)
    offset = int(r * 0.45)
    d.polygon([(cx - offset, cy - offset), (cx, cy), (cx + offset, cy + offset), (cx, cy)], fill=fill)
    d.polygon([(cx + offset, cy - offset), (cx, cy), (cx - offset, cy + offset), (cx, cy)], fill=fill)

def create_horizontal_grad(w, h, c1, c2):
    img = Image.new("RGBA", (w, h), (255, 255, 255, 255))
    d = ImageDraw.Draw(img)
    for x in range(w):
        t = x / max(1, w - 1)
        r = int(c1[0] + t * (c2[0] - c1[0]))
        g = int(c1[1] + t * (c2[1] - c1[1]))
        b = int(c1[2] + t * (c2[2] - c1[2]))
        d.line([(x, 0), (x, h)], fill=(r, g, b, 255))
    return img

def create_vertical_grad(w, h, c1, c2):
    img = Image.new("RGBA", (w, h), (255, 255, 255, 255))
    d = ImageDraw.Draw(img)
    for y in range(h):
        t = y / max(1, h - 1)
        r = int(c1[0] + t * (c2[0] - c1[0]))
        g = int(c1[1] + t * (c2[1] - c1[1]))
        b = int(c1[2] + t * (c2[2] - c1[2]))
        d.line([(0, y), (w, y)], fill=(r, g, b, 255))
    return img

# -------------------------------------------------------------
# 1. Warm Cream & Pastel Pink Background
# -------------------------------------------------------------
# Soft Cream Ivory to Warm Milk-Pink gradient
bg_grad = create_vertical_grad(W, H, (255, 253, 250), (254, 242, 242))
canvas.paste(bg_grad, (0, 0))

# Ambient soft pink & cream bokeh
bokeh_layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
b_draw = ImageDraw.Draw(bokeh_layer)
b_draw.ellipse([50, -50, 480, 360], fill=(254, 205, 211, 60))
b_draw.ellipse([W - 650, 150, W + 100, 850], fill=(253, 230, 138, 45))
b_draw.ellipse([150, 600, 680, 1100], fill=(251, 113, 133, 35))
bokeh_layer = bokeh_layer.filter(ImageFilter.GaussianBlur(55))
canvas.paste(bokeh_layer, (0, 0), bokeh_layer)

# Background cute decorative paw doodles
d_draw = ImageDraw.Draw(canvas)
draw_paw(d_draw, 140, 210, 28, fill=(254, 205, 211, 160))
draw_paw(d_draw, 1780, 260, 24, fill=(253, 230, 138, 180))
draw_sparkle(d_draw, 70, 480, r=16, fill=(251, 146, 60, 160))
draw_sparkle(d_draw, 1840, 640, r=18, fill=(244, 63, 94, 150))
draw_sparkle(d_draw, 1760, 890, r=14, fill=(251, 191, 36, 170))

# -------------------------------------------------------------
# 2. Top-Right Character Showcase (Pink Cream Frame)
# -------------------------------------------------------------
if os.path.exists(char_img_path):
    char_raw = Image.open(char_img_path).convert("RGBA")
    sc_w, sc_h = 680, 210
    orig_w, orig_h = char_raw.size
    crop_box = (int(orig_w * 0.16), int(orig_h * 0.20), int(orig_w * 0.86), int(orig_h * 0.80))
    char_cropped = char_raw.crop(crop_box).resize((sc_w, sc_h), Image.Resampling.LANCZOS)
    
    # Rounded mask with smooth corners & soft left fade
    c_mask = Image.new("L", (sc_w, sc_h), 255)
    m_draw = ImageDraw.Draw(c_mask)
    m_draw.rounded_rectangle([0, 0, sc_w, sc_h], radius=24, fill=255)
    
    for x in range(80):
        alpha = int(255 * (x / 80))
        for y in range(sc_h):
            orig_a = c_mask.getpixel((x, y))
            c_mask.putpixel((x, y), min(orig_a, alpha))
            
    cx_pos = W - sc_w - 70
    cy_pos = 28
    
    # Soft Pink & Cream Shadow
    sh_box = Image.new("RGBA", (sc_w + 30, sc_h + 30), (0, 0, 0, 0))
    sh_draw = ImageDraw.Draw(sh_box)
    sh_draw.rounded_rectangle([15, 15, sc_w + 15, sc_h + 15], radius=24, fill=(244, 63, 94, 45))
    sh_box = sh_box.filter(ImageFilter.GaussianBlur(14))
    canvas.paste(sh_box, (cx_pos - 15, cy_pos - 15), sh_box)
    
    # Paste character art
    canvas.paste(char_cropped, (cx_pos, cy_pos), c_mask)
    
    # Cream-Pink framed border
    framed_outline = Image.new("RGBA", (sc_w, sc_h), (0, 0, 0, 0))
    ImageDraw.Draw(framed_outline).rounded_rectangle([0, 0, sc_w - 1, sc_h - 1], radius=24, outline=(255, 228, 230, 240), width=4)
    canvas.paste(framed_outline, (cx_pos, cy_pos), framed_outline)
    
    # Cute sticker badge on top of character frame
    st_draw = ImageDraw.Draw(canvas)
    st_draw.rounded_rectangle([cx_pos + sc_w - 240, cy_pos + 12, cx_pos + sc_w - 18, cy_pos + 48], radius=14, fill=(255, 255, 255, 245), outline=(244, 63, 94), width=2)
    draw_paw(st_draw, cx_pos + sc_w - 222, cy_pos + 30, 15, fill=(244, 63, 94))
    st_draw.text((cx_pos + sc_w - 204, cy_pos + 19), "Purrfect Squad", font=get_font(15, bold=True, sans=True), fill=(190, 18, 60))
    draw_sparkle(st_draw, cx_pos + sc_w - 36, cy_pos + 30, r=8, fill=(251, 146, 60))

# -------------------------------------------------------------
# 3. Top Header Branding (Rose Pink & Warm Cream)
# -------------------------------------------------------------
draw = ImageDraw.Draw(canvas)

# Top Brand Category Tag
draw.rounded_rectangle([80, 32, 345, 68], radius=14, fill=(255, 241, 242), outline=(244, 63, 94), width=2)
draw_paw(draw, 102, 50, 16, fill=(244, 63, 94))
draw.text((126, 40), "OFFICIAL CATTERY & BOUTIQUE", font=get_font(13, bold=True, sans=True), fill=(190, 18, 60))

# Brand Logo: PURRFECT SHOP
draw.text((80, 78), "PURRFECT", font=get_font(52, bold=True, sans=True), fill=(136, 19, 55))  # Deep Velvet Wine
draw.text((375, 78), "SHOP", font=get_font(52, bold=True, sans=True), fill=(244, 63, 94))     # Rose Pink
draw_sparkle(draw, 545, 90, r=14, fill=(251, 146, 60))

# Subtitle
draw.text((82, 142), "ศูนย์รวมน้องแมวสายพันธุ์แท้ 100% • ตรวจสุขภาพครบ • การันตี 180 วัน", font=get_font(18, bold=True), fill=(159, 18, 57))

# Vertical separator
draw.line([(620, 50), (620, 155)], fill=(254, 205, 211), width=3)

# Slogan with warm pink-cream styling
draw.text((650, 85), "เพราะทุกบ้านควรมีเจ้าเหมียว", font=get_font(34, bold=True), fill=(136, 19, 55))
draw_paw(draw, 1080, 108, 28, fill=(244, 63, 94))

# -------------------------------------------------------------
# 4. Three Thematic Pink & Cream Cards
# -------------------------------------------------------------

# Theme Configurations:
# Card 1: Rose Pink & Strawberry Cream
# Card 2: Velvet Blossom & Cream Vanilla
# Card 3: Pastel Coral Pink & Sweet Peach Cream

cards_config = [
    {
        'y': 255,
        'h': 175,
        'theme_grad': ((244, 63, 94), (251, 113, 133)),     # Vivid Rose Pink to Sweet Coral Pink
        'badge_border': (254, 205, 211),
        'bg_card': (255, 255, 255),
        'card_outline': (254, 205, 211),
        'card_glow': (244, 63, 94, 30),
        'icon_type': 'code',
        'tag_title_lines': ['Meta Tag', 'Title'],
        'pill_bg': (255, 241, 242),
        'pill_txt': (190, 18, 60),
        'pill_border': (244, 63, 94),
        'pill_name': 'Title Tag',
        'content_lines': [
            'Purrfect Shop | ฟาร์มแมวสายพันธุ์แท้ การันตีสุขภาพ 180 วัน พร้อมใบเพ็ดดีกรี'
        ],
        'is_bold': True
    },
    {
        'y': 455,
        'h': 215,
        'theme_grad': ((219, 39, 119), (244, 114, 182)),    # Deep Berry Blossom to Soft Pastel Pink
        'badge_border': (251, 207, 232),
        'bg_card': (255, 255, 255),
        'card_outline': (251, 207, 232),
        'card_glow': (219, 39, 119, 28),
        'icon_type': 'doc',
        'tag_title_lines': ['Meta Tag', 'Description'],
        'pill_bg': (253, 242, 248),
        'pill_txt': (157, 23, 77),
        'pill_border': (219, 39, 119),
        'pill_name': 'Meta Description',
        'content_lines': [
            'Purrfect Shop ศูนย์รวมและฟาร์มเพาะพันธุ์น้องแมวเกรดพรีเมียม สายพันธุ์แท้ 100% อาทิ บริติช, สกอตติช, เมนคูน, แร็กดอลล์',
            'และเปอร์เซีย ตรวจสุขภาพ วัคซีนครบ พร้อมใบเพ็ดดีกรีสากล และบริการส่งฟรีทั่วประเทศด้วย Pet Taxi ปรับอากาศ'
        ],
        'is_bold': False
    },
    {
        'y': 695,
        'h': 250,
        'theme_grad': ((249, 115, 22), (251, 146, 60)),     # Warm Peach-Cream to Coral Sunrise
        'badge_border': (254, 215, 170),
        'bg_card': (255, 255, 255),
        'card_outline': (254, 215, 170),
        'card_glow': (249, 115, 22, 25),
        'icon_type': 'search',
        'tag_title_lines': ['Meta Tag', 'Keywords'],
        'pill_bg': (255, 247, 237),
        'pill_txt': (194, 65, 12),
        'pill_border': (249, 115, 22),
        'pill_name': 'Keywords (15 คำ)',
        'content_lines': [
            'ซื้อแมว  •  ฟาร์มแมว  •  ขายแมวสายพันธุ์แท้  •  แมวบริติชช็อตแฮร์  •  แมวสกอตติชโฟลด์  •  แมวเมนคูน',
            'แมวแร็กดอลล์  •  แมวเปอร์เซีย  •  แมวมันช์กิ้นขาสั้น  •  แมวมีใบเพ็ด  •  ใบเพ็ดดีกรีแมว CFA TICA',
            'รับเลี้ยงแมวพันธุ์แท้  •  ฟาร์มแมวรับประกันสุขภาพ  •  อุปกรณ์เลี้ยงแมว  •  ส่งแมวข้ามจังหวัด Pet Taxi'
        ],
        'is_bold': False
    }
]

def render_stylish_card(cfg):
    card_x = 80
    card_w = 1760
    badge_w = 220
    y = cfg['y']
    h = cfg['h']
    
    # 1. Outer Soft Pink / Peach Glow Shadow
    c_shadow = Image.new("RGBA", (card_w + 30, h + 30), (0, 0, 0, 0))
    cs_draw = ImageDraw.Draw(c_shadow)
    cs_draw.rounded_rectangle([15, 15, card_w + 15, h + 15], radius=24, fill=cfg['card_glow'])
    c_shadow = c_shadow.filter(ImageFilter.GaussianBlur(14))
    canvas.paste(c_shadow, (card_x - 15, y - 15), c_shadow)
    
    # 2. Card Background & Colored Border (Cream-white)
    draw.rounded_rectangle([card_x, y, card_x + card_w, y + h], radius=22, fill=cfg['bg_card'], outline=cfg['card_outline'], width=2)
    
    # 3. Left Gradient Badge
    badge_img = create_vertical_grad(badge_w, h, cfg['theme_grad'][0], cfg['theme_grad'][1])
    badge_mask = Image.new("L", (badge_w, h), 0)
    bm_draw = ImageDraw.Draw(badge_mask)
    bm_draw.rounded_rectangle([0, 0, badge_w * 2, h], radius=22, fill=255)
    canvas.paste(badge_img, (card_x, y), badge_mask)
    
    # Badge inner shine & right separator line
    b_edge = Image.new("RGBA", (4, h), (255, 255, 255, 180))
    canvas.paste(b_edge, (card_x + badge_w - 4, y), b_edge)
    
    # Badge Icon
    ic_cx = card_x + badge_w // 2
    ic_cy = y + 50
    
    # Icon background white circle/capsule
    draw.ellipse([ic_cx - 26, ic_cy - 26, ic_cx + 26, ic_cy + 26], fill=(255, 255, 255, 240))
    
    icon_col = cfg['theme_grad'][0]
    if cfg['icon_type'] == "code":
        draw.text((ic_cx - 15, ic_cy - 12), "</>", font=get_font(18, bold=True, sans=True), fill=icon_col)
    elif cfg['icon_type'] == "doc":
        draw.rounded_rectangle([ic_cx - 12, ic_cy - 14, ic_cx + 12, ic_cy + 14], radius=4, outline=icon_col, width=2)
        draw.line([(ic_cx - 7, ic_cy - 6), (ic_cx + 7, ic_cy - 6)], fill=icon_col, width=2)
        draw.line([(ic_cx - 7, ic_cy), (ic_cx + 7, ic_cy)], fill=icon_col, width=2)
        draw.line([(ic_cx - 7, ic_cy + 6), (ic_cx + 3, ic_cy + 6)], fill=icon_col, width=2)
    elif cfg['icon_type'] == "search":
        draw.ellipse([ic_cx - 12, ic_cy - 12, ic_cx + 4, ic_cy + 4], outline=icon_col, width=3)
        draw.line([(ic_cx + 3, ic_cy + 3), (ic_cx + 12, ic_cy + 12)], fill=icon_col, width=3)
        
    # Badge Titles
    ty = ic_cy + 35
    for line in cfg['tag_title_lines']:
        t_font = get_font(21, bold=True)
        tb = draw.textbbox((0, 0), line, font=t_font)
        tw = tb[2] - tb[0]
        # Text shadow
        draw.text((ic_cx - tw // 2 + 1, ty + 1), line, font=t_font, fill=(0, 0, 0, 50))
        draw.text((ic_cx - tw // 2, ty), line, font=t_font, fill=(255, 255, 255))
        ty += 28

    # 4. Right Content Container
    rc_x = card_x + badge_w + 35
    
    # Themed Pill Badge
    pill_font = get_font(17, bold=True)
    pb = draw.textbbox((0, 0), cfg['pill_name'], font=pill_font)
    pw = pb[2] - pb[0] + 36
    draw.rounded_rectangle([rc_x, y + 24, rc_x + pw, y + 62], radius=14, fill=cfg['pill_bg'], outline=cfg['pill_border'], width=1)
    draw.text((rc_x + 18, y + 29), cfg['pill_name'], font=pill_font, fill=cfg['pill_txt'])
    
    # Paw icon next to pill
    draw_paw(draw, rc_x + pw + 25, y + 43, 16, fill=cfg['theme_grad'][0])
    
    # Content Text Lines
    content_y = y + 78
    for line in cfg['content_lines']:
        c_font = get_font(24, bold=cfg['is_bold'])
        draw.text((rc_x, content_y), line, font=c_font, fill=(30, 41, 59) if cfg['is_bold'] else (71, 85, 105))
        content_y += 38

for cfg in cards_config:
    render_stylish_card(cfg)

# -------------------------------------------------------------
# 5. Pink & Vanilla Cream Wave Footer
# -------------------------------------------------------------
footer_h = 120
footer_img = Image.new("RGBA", (W, footer_h), (0, 0, 0, 0))

# Gradient from Rose Pink to Warm Peach Cream
foot_grad = create_horizontal_grad(W, footer_h, (244, 63, 94), (251, 146, 60))
f_mask = Image.new("L", (W, footer_h), 0)
fm_draw = ImageDraw.Draw(f_mask)

f_pts = [(0, 32)]
for x in range(0, W + 1, 20):
    y_wave = int(30 + 16 * math.sin((x / W) * math.pi * 1.5))
    f_pts.append((x, y_wave))
f_pts.append((W, footer_h))
f_pts.append((0, footer_h))

fm_draw.polygon(f_pts, fill=255)
footer_img.paste(foot_grad, (0, 0), f_mask)
canvas.paste(footer_img, (0, H - footer_h), footer_img)

# Footer Content
draw = ImageDraw.Draw(canvas)

# Left Branding
draw_paw(draw, 95, H - 46, 24, fill=(255, 255, 255, 240))
draw.text((125, H - 58), "PURRFECT SHOP", font=get_font(20, bold=True, sans=True), fill=(255, 255, 255))
draw.line([(295, H - 56), (295, H - 36)], fill=(255, 255, 255, 180), width=2)
draw.text((315, H - 58), "เพราะทุกบ้านควรมีเจ้าเหมียว", font=get_font(19, bold=True), fill=(255, 247, 237))
draw_paw(draw, 545, H - 46, 16, fill=(255, 255, 255, 220))

# Right Script Slogan with golden sparkle
draw_sparkle(draw, W - 680, H - 46, r=12, fill=(254, 240, 138))
script_font = get_font(33, script=True)
draw.text((W - 655, H - 66), "Every Home Deserves A Purrfect Cat", font=script_font, fill=(255, 255, 255))

# Save output
canvas_rgb = canvas.convert("RGB")
canvas_rgb.save(out_png_1, quality=96)
canvas_rgb.save(out_png_2, quality=96)
Copy_p = os.path.join(base_dir, "assets", "images", "seo_meta_tags_purrfect_shop.png")
canvas_rgb.save(Copy_p, quality=96)

print(f"[OK] Generated Pink & Cream Infographic at {out_png_1}")
print(f"[OK] Generated Pink & Cream Infographic at {Copy_p}")
