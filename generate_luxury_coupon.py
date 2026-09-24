import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

def create_gold_gradient(width, height, angle_deg=45):
    """Creates a rich shimmering metallic gold gradient."""
    stops = [
        (0.00, (230, 185, 55)),
        (0.20, (255, 245, 175)),
        (0.38, (195, 140, 32)),
        (0.58, (255, 250, 200)),
        (0.78, (225, 180, 55)),
        (1.00, (160, 115, 22))
    ]
    
    rad = math.radians(angle_deg)
    cos_a = math.cos(rad)
    sin_a = math.sin(rad)
    
    grad = Image.new("RGBA", (width, height))
    pixels = grad.load()
    
    max_proj = abs(width * cos_a) + abs(height * sin_a)
    
    for y in range(height):
        for x in range(width):
            proj = (x * cos_a + y * sin_a) / max_proj
            proj = max(0.0, min(1.0, (proj + 0.5) % 1.0))
            
            c1, c2 = stops[0], stops[-1]
            for i in range(len(stops) - 1):
                if stops[i][0] <= proj <= stops[i+1][0]:
                    c1 = stops[i]
                    c2 = stops[i+1]
                    break
            
            t = (proj - c1[0]) / max(0.0001, (c2[0] - c1[0]))
            r = int(c1[1][0] + t * (c2[1][0] - c1[1][0]))
            g = int(c1[1][1] + t * (c2[1][1] - c1[1][1]))
            b = int(c1[1][2] + t * (c2[1][2] - c1[1][2]))
            pixels[x, y] = (r, g, b, 255)
            
    return grad

def draw_gold_diamond(draw, cx, cy, size, fill=(245, 212, 122)):
    """Draws a crisp geometric luxury 4-point star/diamond."""
    half = size / 2.0
    pts = [
        (cx, cy - half),
        (cx + half * 0.65, cy),
        (cx, cy + half),
        (cx - half * 0.65, cy)
    ]
    draw.polygon(pts, fill=fill)

def draw_luxury_sparkle(draw, cx, cy, r=10, color=(255, 240, 160, 230)):
    """Draws a luxury shining 8-point sparkle."""
    # Vertical & horizontal long spikes
    draw.polygon([(cx, cy - r), (cx + 2, cy), (cx, cy + r), (cx - 2, cy)], fill=color)
    draw.polygon([(cx - r, cy), (cx, cy + 2), (cx + r, cy), (cx, cy - 2)], fill=color)
    # Diagonal short spikes
    d = int(r * 0.45)
    draw.polygon([(cx - d, cy - d), (cx, cy), (cx + d, cy + d), (cx, cy)], fill=color)
    draw.polygon([(cx + d, cy - d), (cx, cy), (cx - d, cy + d), (cx, cy)], fill=color)

def create_luxury_coupon(width=1200, height=600, output_path="coupon_10pct.png"):
    img = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    sf = width / 1200.0

    # Fonts
    font_bold_path = "C:/Windows/Fonts/LeelaUIb.ttf" if os.path.exists("C:/Windows/Fonts/LeelaUIb.ttf") else "C:/Windows/Fonts/tahomabd.ttf"
    font_reg_path = "C:/Windows/Fonts/LeelawUI.ttf" if os.path.exists("C:/Windows/Fonts/LeelawUI.ttf") else "C:/Windows/Fonts/tahoma.ttf"

    def get_font(size, bold=False):
        p = font_bold_path if bold else font_reg_path
        try:
            return ImageFont.truetype(p, int(size * sf))
        except:
            return ImageFont.load_default()

    font_huge_discount = get_font(76, bold=True)
    font_title = get_font(30, bold=True)
    font_brand = get_font(23, bold=True)
    font_subtitle = get_font(18, bold=False)
    font_term = get_font(24, bold=True)
    font_code_title = get_font(19, bold=True)
    font_code = get_font(38, bold=True)
    font_badge = get_font(18, bold=True)
    font_serial = get_font(15, bold=True)
    font_footer = get_font(16, bold=False)

    # Color Palette - Luxury Black & Gold
    c_gold_light = (255, 240, 160)
    c_gold_main = (230, 185, 75)
    c_gold_dark = (185, 138, 30)
    c_card_dark = (14, 19, 28)        # Deep Obsidian Navy
    c_card_inner = (21, 28, 41)       # Elegant Dark Slate
    c_text_gold = (245, 215, 130)
    c_text_white = (255, 255, 255)
    c_text_muted = (156, 163, 175)

    # Dimensions
    pad_x = int(32 * sf)
    pad_y = int(28 * sf)
    tx1, ty1 = pad_x, pad_y
    tx2, ty2 = width - pad_x, height - pad_y
    tw, th = tx2 - tx1, ty2 - ty1
    radius = int(26 * sf)

    split_x = tx1 + int(tw * 0.71)
    cutout_radius = int(22 * sf)

    # 1. Luxury Gold Glow Drop Shadow
    shadow = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    s_draw = ImageDraw.Draw(shadow)
    # Warm gold ambient glow
    s_draw.rounded_rectangle([tx1 - 4, ty1 + 4, tx2 + 4, ty2 + 12], radius=radius + 4, fill=(212, 175, 55, 80))
    # Deep shadow
    s_draw.rounded_rectangle([tx1, ty1 + 8, tx2, ty2 + 18], radius=radius, fill=(0, 0, 0, 185))
    shadow = shadow.filter(ImageFilter.GaussianBlur(16))
    img.paste(shadow, (0, 0), shadow)

    # 2. Ticket Base
    ticket_base = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    t_draw = ImageDraw.Draw(ticket_base)
    
    # Outer dark body
    t_draw.rounded_rectangle([tx1, ty1, tx2, ty2], radius=radius, fill=c_card_dark)
    
    # Inner subtle luxurious panels
    t_draw.rounded_rectangle([tx1 + 6, ty1 + 6, split_x - 6, ty2 - 6], radius=radius - 3, fill=c_card_inner)
    t_draw.rounded_rectangle([split_x + 6, ty1 + 6, tx2 - 6, ty2 - 6], radius=radius - 3, fill=(18, 24, 36))

    # Semicircle Cutouts
    t_draw.ellipse([split_x - cutout_radius, ty1 - cutout_radius, split_x + cutout_radius, ty1 + cutout_radius], fill=(0, 0, 0, 0))
    t_draw.ellipse([split_x - cutout_radius, ty2 - cutout_radius, split_x + cutout_radius, ty2 + cutout_radius], fill=(0, 0, 0, 0))

    img.paste(ticket_base, (0, 0), ticket_base)

    # 3. Gold Metallic Borders & Filigree Lines
    gold_grad = create_gold_gradient(width, height, angle_deg=35)
    
    # Border mask
    border_mask = Image.new("L", (width, height), 0)
    bm_draw = ImageDraw.Draw(border_mask)
    
    # Outer gold border
    bm_draw.rounded_rectangle([tx1, ty1, tx2, ty2], radius=radius, outline=255, width=int(4 * sf))
    # Inner delicate framing line
    bm_draw.rounded_rectangle([tx1 + int(12 * sf), ty1 + int(12 * sf), split_x - int(14 * sf), ty2 - int(12 * sf)], radius=radius - 8, outline=180, width=int(1.5 * sf))
    bm_draw.rounded_rectangle([split_x + int(14 * sf), ty1 + int(12 * sf), tx2 - int(12 * sf), ty2 - int(12 * sf)], radius=radius - 8, outline=180, width=int(1.5 * sf))

    # Cutout border rings
    bm_draw.arc([split_x - cutout_radius, ty1 - cutout_radius, split_x + cutout_radius, ty1 + cutout_radius], start=0, end=180, fill=255, width=int(4 * sf))
    bm_draw.arc([split_x - cutout_radius, ty2 - cutout_radius, split_x + cutout_radius, ty2 + cutout_radius], start=180, end=360, fill=255, width=int(4 * sf))

    # Apply gold to borders
    gold_border_layer = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    gold_border_layer.paste(gold_grad, (0, 0), border_mask)
    img.paste(gold_border_layer, (0, 0), gold_border_layer)

    # 4. Dashed Gold Perforation Line
    draw = ImageDraw.Draw(img)
    dash_h = int(10 * sf)
    gap_h = int(8 * sf)
    cur_y = ty1 + cutout_radius + int(12 * sf)
    end_y = ty2 - cutout_radius - int(12 * sf)
    while cur_y < end_y:
        draw.line([(split_x, cur_y), (split_x, min(cur_y + dash_h, end_y))], fill=c_gold_dark, width=int(2.5 * sf))
        cur_y += dash_h + gap_h

    # Decorative gold sparkles
    draw_luxury_sparkle(draw, split_x - int(38 * sf), ty1 + int(24 * sf), r=int(10 * sf))
    draw_luxury_sparkle(draw, tx2 - int(36 * sf), ty1 + int(36 * sf), r=int(10 * sf))
    draw_luxury_sparkle(draw, tx1 + int(36 * sf), ty2 - int(36 * sf), r=int(9 * sf))

    # ----------------------------------------------------
    # LEFT CONTENT (VOUCHER DETAILS)
    # ----------------------------------------------------
    content_x = tx1 + int(36 * sf)
    
    # Top Luxury Branding Bar
    brand_y = ty1 + int(28 * sf)
    draw.text((content_x, brand_y), "PURRFECT BOUTIQUE", fill=c_gold_light, font=font_brand)
    draw.text((content_x + int(265 * sf), brand_y + int(3 * sf)), "•  EXCLUSIVE VIP PRIVILEGE", fill=c_text_muted, font=font_subtitle)

    # VIP Badge Top Right of Left Section
    badge_w = int(160 * sf)
    badge_h = int(36 * sf)
    badge_x = split_x - badge_w - int(55 * sf)
    badge_y = ty1 + int(26 * sf)
    
    draw.rounded_rectangle([badge_x, badge_y, badge_x + badge_w, badge_y + badge_h], radius=int(18 * sf), fill=(35, 43, 62), outline=c_gold_main, width=int(1.5 * sf))
    draw_gold_diamond(draw, badge_x + int(18 * sf), badge_y + int(18 * sf), size=int(14 * sf), fill=c_gold_light)
    draw.text((badge_x + int(32 * sf), badge_y + int(6 * sf)), "VIP MEMBER", fill=c_gold_light, font=font_badge)

    # Thin separator line
    sep_y = brand_y + int(38 * sf)
    draw.line([(content_x, sep_y), (split_x - int(28 * sf), sep_y)], fill=(55, 65, 81), width=int(1 * sf))

    # Main Headline
    title_y = sep_y + int(18 * sf)
    draw.text((content_x, title_y), "เอกสิทธิ์ส่วนลดพิเศษสำหรับท่าน", fill=c_text_white, font=font_title)

    # ----------------------------------------------------
    # Gold Foil Text for "ลดทันที 10%"
    # ----------------------------------------------------
    disc_y = title_y + int(46 * sf)
    
    # Text Mask for Gold Foil Effect
    text_mask = Image.new("L", (width, height), 0)
    tm_draw = ImageDraw.Draw(text_mask)
    tm_draw.text((content_x, disc_y), "ลดทันที 10%", fill=255, font=font_huge_discount)
    
    # Paste gold gradient onto discount text
    gold_text_layer = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    gold_text_layer.paste(gold_grad, (0, 0), text_mask)
    img.paste(gold_text_layer, (0, 0), gold_text_layer)
    
    # Glowing Tag: 10% OFF
    tag_x = content_x + int(460 * sf)
    tag_y = disc_y + int(22 * sf)
    tag_w = int(140 * sf)
    tag_h = int(50 * sf)
    
    # Tag gold border
    draw.rounded_rectangle([tag_x, tag_y, tag_x + tag_w, tag_y + tag_h], radius=int(12 * sf), fill=(50, 40, 18), outline=c_gold_light, width=int(2 * sf))
    draw.text((tag_x + int(18 * sf), tag_y + int(8 * sf)), "10% OFF", fill=c_gold_light, font=font_term)

    # Luxury Conditions Card Box
    terms_box_y = disc_y + int(114 * sf)
    terms_box_w = split_x - content_x - int(28 * sf)
    terms_box_h = int(178 * sf)

    # Dark luxury container for terms
    draw.rounded_rectangle([content_x, terms_box_y, content_x + terms_box_w, terms_box_y + terms_box_h], 
                           radius=int(14 * sf), fill=(14, 18, 27), outline=(51, 65, 85), width=int(1.5 * sf))
    
    # Terms Title
    draw_gold_diamond(draw, content_x + int(24 * sf), terms_box_y + int(25 * sf), size=int(12 * sf), fill=c_gold_main)
    draw.text((content_x + int(38 * sf), terms_box_y + int(13 * sf)), "เงื่อนไขและสิทธิพิเศษการใช้คูปอง :", fill=c_text_gold, font=font_term)

    # 3 Requirements
    t1_y = terms_box_y + int(54 * sf)
    t2_y = terms_box_y + int(94 * sf)
    t3_y = terms_box_y + int(134 * sf)

    # Requirement 1: ใช้ได้ 1 ครั้ง
    draw_gold_diamond(draw, content_x + int(24 * sf), t1_y + int(13 * sf), size=int(10 * sf), fill=c_gold_main)
    draw.text((content_x + int(42 * sf), t1_y), "1. ใช้ได้ 1 ครั้ง", fill=c_text_white, font=font_term)
    draw.text((content_x + int(205 * sf), t1_y + int(2 * sf)), "(จำกัด 1 สิทธิ์ต่อบัญชีสมาชิก VIP)", fill=c_text_muted, font=font_badge)

    # Requirement 2: ใช้ได้ 1 เดือน
    draw_gold_diamond(draw, content_x + int(24 * sf), t2_y + int(13 * sf), size=int(10 * sf), fill=c_gold_main)
    draw.text((content_x + int(42 * sf), t2_y), "2. ใช้งานได้ภายใน 1 เดือน", fill=c_text_white, font=font_term)
    draw.text((content_x + int(328 * sf), t2_y + int(2 * sf)), "(นับจากวันที่ได้รับสิทธิ์)", fill=c_text_muted, font=font_badge)

    # Requirement 3: ขั้นต่ำ 300 บาท
    draw_gold_diamond(draw, content_x + int(24 * sf), t3_y + int(13 * sf), size=int(10 * sf), fill=c_gold_main)
    draw.text((content_x + int(42 * sf), t3_y), "3. ใช้ได้เมื่อมียอดสั่งซื้อขั้นต่ำ 300 บาทขึ้นไป", fill=c_gold_light, font=font_term)

    # Subtle Footer Note
    footer_y = terms_box_y + terms_box_h + int(10 * sf)
    draw.text((content_x + int(6 * sf), footer_y), "• สิทธิพิเศษเฉพาะสมาชิก Purrfect Shop • ไม่สามารถแลกเปลี่ยนหรือทอนเป็นเงินสดได้", fill=(100, 116, 139), font=font_footer)

    # ----------------------------------------------------
    # RIGHT SECTION (TICKET STUB)
    # ----------------------------------------------------
    right_center_x = split_x + int((tx2 - split_x) / 2)

    # Luxury Cat Avatar with Gold Ring Frame
    cat_path = "assets/images/cat_british.jpg"
    cat_size = int(112 * sf)
    cat_top_y = ty1 + int(26 * sf)
    
    if os.path.exists(cat_path):
        try:
            c_raw = Image.open(cat_path).convert("RGBA")
            c_raw = c_raw.resize((cat_size, cat_size), Image.Resampling.LANCZOS)
            
            c_mask = Image.new("L", (cat_size, cat_size), 0)
            cm_draw = ImageDraw.Draw(c_mask)
            cm_draw.ellipse([0, 0, cat_size, cat_size], fill=255)
            
            c_circ = Image.new("RGBA", (cat_size, cat_size), (0, 0, 0, 0))
            c_circ.paste(c_raw, (0, 0), c_mask)
            
            cat_pos_x = right_center_x - int(cat_size / 2)
            img.paste(c_circ, (cat_pos_x, cat_top_y), c_circ)
            
            # Double gold ring around cat
            draw.ellipse([cat_pos_x - 3, cat_top_y - 3, cat_pos_x + cat_size + 3, cat_top_y + cat_size + 3], outline=c_gold_main, width=int(3 * sf))
            draw.ellipse([cat_pos_x - 7, cat_top_y - 7, cat_pos_x + cat_size + 7, cat_top_y + cat_size + 7], outline=c_gold_dark, width=int(1.5 * sf))
        except Exception as e:
            print("Cat photo error:", e)

    # Code Label
    code_lbl_y = cat_top_y + cat_size + int(14 * sf)
    draw.text((right_center_x - int(58 * sf), code_lbl_y), "PROMO CODE", fill=c_text_muted, font=font_code_title)

    # Luxury Promo Code Box (CAT10OFF)
    code_box_w = int(240 * sf)
    code_box_h = int(62 * sf)
    code_box_x = right_center_x - int(code_box_w / 2)
    code_box_y = code_lbl_y + int(26 * sf)

    # Code background
    draw.rounded_rectangle([code_box_x, code_box_y, code_box_x + code_box_w, code_box_y + code_box_h], 
                           radius=int(12 * sf), fill=(30, 38, 54), outline=c_gold_main, width=int(2.5 * sf))
    
    # Gold foil text on CAT10OFF
    code_mask = Image.new("L", (width, height), 0)
    cm_d = ImageDraw.Draw(code_mask)
    cm_d.text((code_box_x + int(22 * sf), code_box_y + int(8 * sf)), "CAT10OFF", fill=255, font=font_code)
    
    gold_code_layer = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    gold_code_layer.paste(gold_grad, (0, 0), code_mask)
    img.paste(gold_code_layer, (0, 0), gold_code_layer)

    # Serial Number & Gold Barcode
    serial_y = code_box_y + int(68 * sf)
    draw.text((right_center_x - int(65 * sf), serial_y), "NO. VIP-8892-CAT10", fill=(110, 125, 150), font=font_serial)

    bar_y = serial_y + int(20 * sf)
    bar_w = int(220 * sf)
    bar_x1 = right_center_x - int(bar_w / 2)
    
    bar_pattern = [3, 2, 4, 1, 2, 3, 1, 4, 2, 1, 3, 2, 4, 1, 2, 3, 2, 1, 4, 2, 3, 1]
    cur_bx = bar_x1
    for bw in bar_pattern:
        scaled_bw = max(1, int(bw * 2 * sf))
        draw.rectangle([cur_bx, bar_y, cur_bx + scaled_bw, bar_y + int(30 * sf)], fill=c_gold_dark)
        cur_bx += scaled_bw + int(4 * sf)

    # Gold CTA Button
    btn_w = int(240 * sf)
    btn_h = int(52 * sf)
    btn_x = right_center_x - int(btn_w / 2)
    btn_y = ty2 - btn_h - int(18 * sf)

    # Gold foil gradient button
    btn_mask = Image.new("L", (width, height), 0)
    btn_m_draw = ImageDraw.Draw(btn_mask)
    btn_m_draw.rounded_rectangle([btn_x, btn_y, btn_x + btn_w, btn_y + btn_h], radius=int(26 * sf), fill=255)
    
    btn_gold = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    btn_gold.paste(gold_grad, (0, 0), btn_mask)
    img.paste(btn_gold, (0, 0), btn_gold)

    # Button text with dark contrast
    draw = ImageDraw.Draw(img)
    draw_gold_diamond(draw, btn_x + int(26 * sf), btn_y + int(26 * sf), size=int(12 * sf), fill=(30, 20, 0))
    draw.text((btn_x + int(40 * sf), btn_y + int(11 * sf)), "คัดลอกโค้ดส่วนลด", fill=(30, 20, 0), font=font_badge)
    draw_gold_diamond(draw, btn_x + btn_w - int(26 * sf), btn_y + int(26 * sf), size=int(12 * sf), fill=(30, 20, 0))

    # Save
    img.save(output_path, "PNG")
    img.save("assets/images/coupon_10pct.png", "PNG")
    img.save("docs/assets/images/coupon_10pct.png", "PNG")
    img.save("GITHUB_PAGES_EXPORT/assets/images/coupon_10pct.png", "PNG")
    img.save("INFINITYFREE_HTDOCS_UPLOAD/assets/images/coupon_10pct.png", "PNG")
    
    artifact_path = "C:/Users/Windows/.gemini/antigravity/brain/63a36a4c-6ead-4c37-aede-64d365cc7888/coupon_10pct.png"
    img.save(artifact_path, "PNG")

    print(f"Luxury coupon saved: {output_path}")

if __name__ == "__main__":
    create_luxury_coupon(1200, 600, "coupon_10pct.png")
