import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

def create_horizontal_gradient(width, height, color_start, color_end):
    """Creates a smooth horizontal 2-color gradient."""
    grad = Image.new("RGBA", (width, height))
    draw = ImageDraw.Draw(grad)
    for x in range(width):
        t = x / max(1, width - 1)
        r = int(color_start[0] + t * (color_end[0] - color_start[0]))
        g = int(color_start[1] + t * (color_end[1] - color_start[1]))
        b = int(color_start[2] + t * (color_end[2] - color_start[2]))
        a = int(color_start[3] + t * (color_end[3] - color_start[3])) if len(color_start) > 3 and len(color_end) > 3 else 255
        draw.line([(x, 0), (x, height)], fill=(r, g, b, a))
    return grad

def draw_paw(draw, cx, cy, size, fill=(255, 107, 74, 80)):
    """Draws a cute decorative paw print."""
    pad_w = int(size * 0.5)
    pad_h = int(size * 0.4)
    # Main pad
    draw.ellipse([cx - pad_w//2, cy - pad_h//2, cx + pad_w//2, cy + pad_h//2], fill=fill)
    # 4 Toes
    toe_r = int(size * 0.16)
    draw.ellipse([cx - int(size * 0.3) - toe_r, cy - int(size * 0.3) - toe_r, cx - int(size * 0.3) + toe_r, cy - int(size * 0.3) + toe_r], fill=fill)
    draw.ellipse([cx - int(size * 0.1) - toe_r, cy - int(size * 0.42) - toe_r, cx - int(size * 0.1) + toe_r, cy - int(size * 0.42) + toe_r], fill=fill)
    draw.ellipse([cx + int(size * 0.12) - toe_r, cy - int(size * 0.42) - toe_r, cx + int(size * 0.12) + toe_r, cy - int(size * 0.42) + toe_r], fill=fill)
    draw.ellipse([cx + int(size * 0.32) - toe_r, cy - int(size * 0.3) - toe_r, cx + int(size * 0.32) + toe_r, cy - int(size * 0.3) + toe_r], fill=fill)

def draw_gold_star(draw, cx, cy, r=10, fill=(245, 158, 11)):
    """Draws a four-pointed luxury star."""
    draw.polygon([(cx, cy - r), (cx + 2, cy), (cx, cy + r), (cx - 2, cy)], fill=fill)
    draw.polygon([(cx - r, cy), (cx, cy + 2), (cx + r, cy), (cx, cy - 2)], fill=fill)
    d = int(r * 0.45)
    draw.polygon([(cx - d, cy - d), (cx, cy), (cx + d, cy + d), (cx, cy)], fill=fill)
    draw.polygon([(cx + d, cy - d), (cx, cy), (cx - d, cy + d), (cx, cy)], fill=fill)

def draw_gold_diamond(draw, cx, cy, size, fill=(245, 158, 11)):
    half = size / 2.0
    pts = [(cx, cy - half), (cx + half * 0.65, cy), (cx, cy + half), (cx - half * 0.65, cy)]
    draw.polygon(pts, fill=fill)

def create_themed_luxury_coupon(width=1200, height=600, output_path="coupon_10pct.png"):
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
    font_subtitle = get_font(17, bold=False)
    font_term_title = get_font(22, bold=True)
    font_term_bold = get_font(22, bold=True)
    font_term_desc = get_font(18, bold=False)
    font_code_title = get_font(18, bold=True)
    font_code = get_font(36, bold=True)
    font_badge = get_font(18, bold=True)
    font_serial = get_font(14, bold=True)
    font_footer = get_font(15, bold=False)

    # Color Palette - Shop Signature Themed Palette
    c_coral_dark = (225, 65, 38)
    c_coral_main = (255, 107, 74)
    c_coral_light = (255, 237, 230)
    c_gold_main = (245, 158, 11)
    c_gold_dark = (195, 120, 10)
    c_gold_light = (254, 243, 199)
    c_mint_dark = (5, 150, 105)
    c_mint_light = (209, 250, 229)
    c_rose_dark = (225, 29, 72)
    c_rose_light = (255, 228, 230)
    c_text_dark = (30, 41, 59)
    c_text_muted = (100, 116, 139)
    c_bg_ticket = (255, 253, 250)

    # Dimensions
    pad_x = int(28 * sf)
    pad_y = int(24 * sf)
    tx1, ty1 = pad_x, pad_y
    tx2, ty2 = width - pad_x, height - pad_y
    tw, th = tx2 - tx1, ty2 - ty1
    radius = int(26 * sf)

    split_x = tx1 + int(tw * 0.71)
    cutout_radius = int(22 * sf)

    # 1. Multi-layer Drop Shadow with Warm Ambient Glow
    shadow = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    s_draw = ImageDraw.Draw(shadow)
    s_draw.rounded_rectangle([tx1 - 6, ty1 + 4, tx2 + 6, ty2 + 14], radius=radius + 6, fill=(255, 107, 74, 50))
    s_draw.rounded_rectangle([tx1 - 2, ty1 + 6, tx2 + 2, ty2 + 16], radius=radius + 2, fill=(245, 158, 11, 40))
    s_draw.rounded_rectangle([tx1, ty1 + 8, tx2, ty2 + 18], radius=radius, fill=(50, 20, 10, 75))
    shadow = shadow.filter(ImageFilter.GaussianBlur(14))
    img.paste(shadow, (0, 0), shadow)

    # 2. Main Ticket Body
    ticket_layer = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    t_draw = ImageDraw.Draw(ticket_layer)
    t_draw.rounded_rectangle([tx1, ty1, tx2, ty2], radius=radius, fill=c_bg_ticket)

    # Left Top Header Banner (Coral-to-Orange Gradient)
    header_h = int(90 * sf)
    header_grad = create_horizontal_gradient(split_x - tx1, header_h, (255, 90, 50, 255), (255, 130, 80, 255))
    
    h_mask = Image.new("L", (split_x - tx1, header_h), 0)
    hm_draw = ImageDraw.Draw(h_mask)
    hm_draw.rounded_rectangle([0, 0, split_x - tx1, header_h], radius=radius, fill=255)
    hm_draw.rectangle([0, radius, split_x - tx1, header_h], fill=255)
    
    h_layer = Image.new("RGBA", (split_x - tx1, header_h), (0, 0, 0, 0))
    h_layer.paste(header_grad, (0, 0), h_mask)
    ticket_layer.paste(h_layer, (tx1, ty1), h_layer)

    # Right Section Background (Warm Cream Amber Panel)
    r_panel_w = tx2 - split_x
    r_panel_h = ty2 - ty1
    r_panel = Image.new("RGBA", (r_panel_w, r_panel_h), (0, 0, 0, 0))
    rp_draw = ImageDraw.Draw(r_panel)
    rp_draw.rounded_rectangle([0, 0, r_panel_w, r_panel_h], radius=radius, fill=(255, 248, 235, 255))
    rp_draw.rectangle([0, 0, radius, r_panel_h], fill=(255, 248, 235, 255))
    ticket_layer.paste(r_panel, (split_x, ty1), r_panel)

    # Semicircle Cutouts
    t_draw.ellipse([split_x - cutout_radius, ty1 - cutout_radius, split_x + cutout_radius, ty1 + cutout_radius], fill=(0, 0, 0, 0))
    t_draw.ellipse([split_x - cutout_radius, ty2 - cutout_radius, split_x + cutout_radius, ty2 + cutout_radius], fill=(0, 0, 0, 0))

    # Outer Border (Gold + Coral Outline)
    t_draw.rounded_rectangle([tx1, ty1, tx2, ty2], radius=radius, outline=c_coral_main, width=int(4 * sf))
    t_draw.rounded_rectangle([tx1 + 4, ty1 + 4, tx2 - 4, ty2 - 4], radius=radius - 2, outline=(245, 158, 11, 90), width=int(1.5 * sf))

    # Cutout Border Arcs
    t_draw.arc([split_x - cutout_radius, ty1 - cutout_radius, split_x + cutout_radius, ty1 + cutout_radius], start=0, end=180, fill=c_coral_main, width=int(4 * sf))
    t_draw.arc([split_x - cutout_radius, ty2 - cutout_radius, split_x + cutout_radius, ty2 + cutout_radius], start=180, end=360, fill=c_coral_main, width=int(4 * sf))

    # Dashed Perforation Line with Golden Stitches
    dash_h = int(10 * sf)
    gap_h = int(8 * sf)
    cur_y = ty1 + cutout_radius + int(10 * sf)
    end_y = ty2 - cutout_radius - int(10 * sf)
    while cur_y < end_y:
        t_draw.line([(split_x, cur_y), (split_x, min(cur_y + dash_h, end_y))], fill=(217, 119, 6, 180), width=int(3 * sf))
        cur_y += dash_h + gap_h

    # Subtle Background Watermark Paws in non-obtrusive corners
    draw_paw(t_draw, tx1 + int(70 * sf), ty2 - int(70 * sf), size=int(55 * sf), fill=(255, 107, 74, 14))
    draw_paw(t_draw, split_x - int(60 * sf), ty1 + int(140 * sf), size=int(48 * sf), fill=(245, 158, 11, 14))
    draw_paw(t_draw, tx2 - int(50 * sf), ty1 + int(80 * sf), size=int(45 * sf), fill=(255, 107, 74, 15))

    img.paste(ticket_layer, (0, 0), ticket_layer)

    draw = ImageDraw.Draw(img)

    # ----------------------------------------------------
    # LEFT HEADER BRANDING
    # ----------------------------------------------------
    brand_x = tx1 + int(36 * sf)
    brand_y = ty1 + int(22 * sf)
    
    # Paw icon on header
    draw_paw(draw, brand_x + int(12 * sf), brand_y + int(16 * sf), size=int(26 * sf), fill=(255, 255, 255, 230))
    draw.text((brand_x + int(32 * sf), brand_y), "PURRFECT BOUTIQUE & CATTERY", fill=(255, 255, 255), font=font_brand)
    draw.text((brand_x + int(34 * sf), brand_y + int(32 * sf)), "EXCLUSIVE PRIVILEGE VOUCHER • บัตรเอกสิทธิ์ส่วนลดพิเศษ", fill=(255, 235, 225), font=font_subtitle)

    # VIP Badge in Header Top-Right
    badge_w = int(165 * sf)
    badge_h = int(38 * sf)
    badge_x = split_x - badge_w - int(28 * sf)
    badge_y = ty1 + int(24 * sf)
    draw.rounded_rectangle([badge_x, badge_y, badge_x + badge_w, badge_y + badge_h], radius=int(19 * sf), fill=c_gold_main, outline=(255, 255, 255), width=int(2 * sf))
    draw_gold_diamond(draw, badge_x + int(18 * sf), badge_y + int(19 * sf), size=int(14 * sf), fill=(255, 255, 255))
    draw.text((badge_x + int(32 * sf), badge_y + int(6 * sf)), "VIP MEMBER", fill=(255, 255, 255), font=font_badge)

    # ----------------------------------------------------
    # MAIN DISCOUNT TITLE AREA
    # ----------------------------------------------------
    content_y = ty1 + header_h + int(14 * sf)
    draw.text((brand_x, content_y), "คูปองส่วนลดพิเศษสำหรับสมาชิกคนสำคัญ", fill=c_text_muted, font=font_title)

    disc_y = content_y + int(46 * sf)
    # 3D shadow for discount text
    draw.text((brand_x + 2, disc_y + 2), "ลดทันที 10%", fill=(255, 205, 185), font=font_huge_discount)
    draw.text((brand_x, disc_y), "ลดทันที 10%", fill=c_coral_dark, font=font_huge_discount)

    # 10% OFF Badge with Ribbon Notch & Gold Border
    tag_x = brand_x + int(445 * sf)
    tag_y = disc_y + int(16 * sf)
    tag_w = int(145 * sf)
    tag_h = int(50 * sf)
    draw.rounded_rectangle([tag_x, tag_y, tag_x + tag_w, tag_y + tag_h], radius=int(14 * sf), fill=(255, 237, 230), outline=c_coral_main, width=int(2.5 * sf))
    draw_gold_star(draw, tag_x + int(18 * sf), tag_y + int(25 * sf), r=int(8 * sf), fill=c_coral_main)
    draw.text((tag_x + int(30 * sf), tag_y + int(8 * sf)), "10% OFF", fill=c_coral_dark, font=font_term_bold)

    # ----------------------------------------------------
    # 3 USER CONDITIONS (ENHANCED RICH TILES)
    # ----------------------------------------------------
    terms_y = disc_y + int(108 * sf)
    terms_box_w = split_x - brand_x - int(28 * sf)
    terms_box_h = int(184 * sf)

    # Main Card Box with clean white surface and soft warm border
    draw.rounded_rectangle([brand_x, terms_y, brand_x + terms_box_w, terms_y + terms_box_h], 
                           radius=int(16 * sf), fill=(255, 255, 255), outline=(235, 215, 205), width=int(2 * sf))

    # Box Header
    draw_gold_star(draw, brand_x + int(24 * sf), terms_y + int(23 * sf), r=int(9 * sf), fill=c_gold_main)
    draw.text((brand_x + int(40 * sf), terms_y + int(11 * sf)), "เงื่อนไขและสิทธิพิเศษการใช้คูปอง :", fill=c_text_dark, font=font_term_title)

    # 3 Condition Row Cards
    row_w = terms_box_w - int(32 * sf)
    row_h = int(39 * sf)
    r_x = brand_x + int(16 * sf)

    # Row 1: ใช้ได้ 1 ครั้ง (Mint Palette)
    r1_y = terms_y + int(46 * sf)
    draw.rounded_rectangle([r_x, r1_y, r_x + row_w, r1_y + row_h], radius=int(10 * sf), fill=c_mint_light)
    draw.ellipse([r_x + int(12 * sf), r1_y + int(11 * sf), r_x + int(28 * sf), r1_y + int(27 * sf)], fill=c_mint_dark)
    draw_gold_diamond(draw, r_x + int(20 * sf), r1_y + int(19 * sf), size=int(8 * sf), fill=(255, 255, 255))
    draw.text((r_x + int(36 * sf), r1_y + int(5 * sf)), "1. ใช้ได้ 1 ครั้ง", fill=c_mint_dark, font=font_term_bold)
    draw.text((r_x + int(195 * sf), r1_y + int(8 * sf)), "(จำกัด 1 สิทธิ์ต่อบัญชีผู้ใช้งาน)", fill=c_text_dark, font=font_term_desc)

    # Row 2: ใช้ได้ 1 เดือน (Amber Gold Palette)
    r2_y = terms_y + int(91 * sf)
    draw.rounded_rectangle([r_x, r2_y, r_x + row_w, r2_y + row_h], radius=int(10 * sf), fill=c_gold_light)
    draw.ellipse([r_x + int(12 * sf), r2_y + int(11 * sf), r_x + int(28 * sf), r2_y + int(27 * sf)], fill=c_gold_main)
    draw_gold_diamond(draw, r_x + int(20 * sf), r2_y + int(19 * sf), size=int(8 * sf), fill=(255, 255, 255))
    draw.text((r_x + int(36 * sf), r2_y + int(5 * sf)), "2. ใช้งานได้ภายใน 1 เดือน", fill=c_gold_dark, font=font_term_bold)
    draw.text((r_x + int(305 * sf), r2_y + int(8 * sf)), "(นับจากวันที่ได้รับคูปอง)", fill=c_text_dark, font=font_term_desc)

    # Row 3: ขั้นต่ำ 300 บาท (Coral/Rose Palette)
    r3_y = terms_y + int(136 * sf)
    draw.rounded_rectangle([r_x, r3_y, r_x + row_w, r3_y + row_h], radius=int(10 * sf), fill=c_rose_light)
    draw.ellipse([r_x + int(12 * sf), r3_y + int(11 * sf), r_x + int(28 * sf), r3_y + int(27 * sf)], fill=c_coral_dark)
    draw_gold_diamond(draw, r_x + int(20 * sf), r3_y + int(19 * sf), size=int(8 * sf), fill=(255, 255, 255))
    draw.text((r_x + int(36 * sf), r3_y + int(5 * sf)), "3. ยอดสั่งซื้อขั้นต่ำ 300 บาทขึ้นไป", fill=c_coral_dark, font=font_term_bold)
    draw.text((r_x + int(375 * sf), r3_y + int(8 * sf)), "(ใช้ได้กับสินค้าทุกหมวด)", fill=c_text_muted, font=font_term_desc)

    # Bottom Note
    draw.text((brand_x + int(8 * sf), ty2 - int(20 * sf)), "* สิทธิพิเศษเฉพาะสมาชิก Purrfect Shop เท่านั้น • ไม่สามารถแลกเปลี่ยนหรือทอนเป็นเงินสดได้", fill=c_text_muted, font=font_footer)

    # ----------------------------------------------------
    # RIGHT SECTION (STUB & PROMO CODE)
    # ----------------------------------------------------
    right_center_x = split_x + int((tx2 - split_x) / 2)

    # Luxury Cat Portrait with Golden Crown Ring Frame
    cat_path = "assets/images/cat_british.jpg"
    cat_size = int(104 * sf)
    cat_top_y = ty1 + int(20 * sf)

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
            
            # Double Golden Frame around Cat
            draw.ellipse([cat_pos_x - 3, cat_top_y - 3, cat_pos_x + cat_size + 3, cat_top_y + cat_size + 3], outline=c_gold_main, width=int(3 * sf))
            draw.ellipse([cat_pos_x - 6, cat_top_y - 6, cat_pos_x + cat_size + 6, cat_top_y + cat_size + 6], outline=(255, 107, 74, 120), width=int(1.5 * sf))
            
            # Gold Star above cat
            draw_gold_star(draw, right_center_x, cat_top_y - int(8 * sf), r=int(9 * sf), fill=c_gold_main)
        except Exception as e:
            print("Cat photo error:", e)

    # Promo Code Header
    code_lbl_y = cat_top_y + cat_size + int(12 * sf)
    draw.text((right_center_x - int(62 * sf), code_lbl_y), "รหัสคูปองส่วนลด", fill=c_text_muted, font=font_code_title)

    # Promo Code Card (CAT10OFF) with Dashed Border
    code_box_w = int(240 * sf)
    code_box_h = int(58 * sf)
    code_box_x = right_center_x - int(code_box_w / 2)
    code_box_y = code_lbl_y + int(24 * sf)

    # Code container with white surface and gold border
    draw.rounded_rectangle([code_box_x, code_box_y, code_box_x + code_box_w, code_box_y + code_box_h], 
                           radius=int(14 * sf), fill=(255, 255, 255), outline=c_gold_main, width=int(2.5 * sf))
    
    # Inner dashed line
    c_dash_w = code_box_w - int(10 * sf)
    c_dash_h = code_box_h - int(10 * sf)
    draw.rounded_rectangle([code_box_x + int(5 * sf), code_box_y + int(5 * sf), code_box_x + int(5 * sf) + c_dash_w, code_box_y + int(5 * sf) + c_dash_h],
                           radius=int(10 * sf), outline=(254, 215, 170), width=int(1.5 * sf))
    
    draw.text((code_box_x + int(24 * sf), code_box_y + int(7 * sf)), "CAT10OFF", fill=c_gold_dark, font=font_code)

    # Serial Number & Realistic Barcode
    serial_y = code_box_y + int(64 * sf)
    draw.text((right_center_x - int(72 * sf), serial_y), "NO. PURR-2026-VIP10", fill=c_text_muted, font=font_serial)

    bar_y = serial_y + int(18 * sf)
    bar_w = int(220 * sf)
    bar_x1 = right_center_x - int(bar_w / 2)
    
    bar_pattern = [3, 2, 4, 1, 2, 3, 1, 4, 2, 1, 3, 2, 4, 1, 2, 3, 2, 1, 4, 2, 3, 1]
    cur_bx = bar_x1
    for bw in bar_pattern:
        scaled_bw = max(1, int(bw * 2 * sf))
        draw.rectangle([cur_bx, bar_y, cur_bx + scaled_bw, bar_y + int(28 * sf)], fill=c_text_dark)
        cur_bx += scaled_bw + int(4 * sf)

    # CTA Button in Coral-to-Gold Theme
    btn_w = int(240 * sf)
    btn_h = int(50 * sf)
    btn_x = right_center_x - int(btn_w / 2)
    btn_y = bar_y + int(42 * sf)

    # Coral gradient button
    btn_grad = create_horizontal_gradient(btn_w, btn_h, (255, 90, 50, 255), (245, 158, 11, 255))
    btn_mask = Image.new("L", (btn_w, btn_h), 0)
    bm_d = ImageDraw.Draw(btn_mask)
    bm_d.rounded_rectangle([0, 0, btn_w, btn_h], radius=int(25 * sf), fill=255)
    
    btn_layer = Image.new("RGBA", (btn_w, btn_h), (0, 0, 0, 0))
    btn_layer.paste(btn_grad, (0, 0), btn_mask)
    img.paste(btn_layer, (btn_x, btn_y), btn_layer)

    draw = ImageDraw.Draw(img)
    # Button text with paw icons
    draw_paw(draw, btn_x + int(28 * sf), btn_y + int(25 * sf), size=int(18 * sf), fill=(255, 255, 255))
    draw.text((btn_x + int(48 * sf), btn_y + int(10 * sf)), "คัดลอกโค้ดส่วนลด", fill=(255, 255, 255), font=font_badge)
    draw_paw(draw, btn_x + btn_w - int(28 * sf), btn_y + int(25 * sf), size=int(18 * sf), fill=(255, 255, 255))

    # Save to all required targets
    img.save(output_path, "PNG")
    img.save("assets/images/coupon_10pct.png", "PNG")
    img.save("docs/assets/images/coupon_10pct.png", "PNG")
    img.save("GITHUB_PAGES_EXPORT/assets/images/coupon_10pct.png", "PNG")
    img.save("INFINITYFREE_HTDOCS_UPLOAD/assets/images/coupon_10pct.png", "PNG")
    
    artifact_path = "C:/Users/Windows/.gemini/antigravity/brain/63a36a4c-6ead-4c37-aede-64d365cc7888/coupon_10pct.png"
    img.save(artifact_path, "PNG")

    print(f"Themed luxury coupon saved: {output_path}")

if __name__ == "__main__":
    create_themed_luxury_coupon(1200, 600, "coupon_10pct.png")
