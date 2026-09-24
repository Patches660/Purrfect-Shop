import os
from PIL import Image, ImageDraw, ImageFont, ImageFilter

def create_discount_coupon(width=1200, height=600, output_path="coupon_10pct.png"):
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

    font_huge_discount = get_font(74, bold=True)
    font_title = get_font(36, bold=True)
    font_brand = get_font(24, bold=True)
    font_term = get_font(24, bold=True)
    font_code_title = get_font(22, bold=True)
    font_code = get_font(36, bold=True)
    font_badge = get_font(20, bold=True)

    # Color Palette
    c_coral_dark = (220, 68, 42)
    c_coral_main = (255, 107, 74)
    c_gold_dark = (217, 119, 6)
    c_gold_main = (245, 158, 11)
    c_mint_dark = (5, 150, 105)
    c_text_dark = (30, 41, 59)
    c_text_muted = (100, 116, 139)
    c_white = (255, 255, 255)
    c_ticket_bg = (255, 252, 248)

    # Ticket Dimensions
    pad_x = int(30 * sf)
    pad_y = int(30 * sf)
    tx1 = pad_x
    ty1 = pad_y
    tx2 = width - pad_x
    ty2 = height - pad_y
    tw = tx2 - tx1
    th = ty2 - ty1
    radius = int(28 * sf)

    split_x = tx1 + int(tw * 0.72)
    cutout_radius = int(24 * sf)

    # Drop Shadow
    shadow = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    s_draw = ImageDraw.Draw(shadow)
    s_draw.rounded_rectangle([tx1, ty1 + 8, tx2, ty2 + 8], radius=radius, fill=(235, 100, 60, 55))
    shadow = shadow.filter(ImageFilter.GaussianBlur(14))
    img.paste(shadow, (0, 0), shadow)

    # Ticket Base Layer
    ticket_layer = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    t_draw = ImageDraw.Draw(ticket_layer)
    t_draw.rounded_rectangle([tx1, ty1, tx2, ty2], radius=radius, fill=c_ticket_bg, outline=c_coral_main, width=int(5 * sf))

    # Header Banner on Left
    left_banner_h = int(96 * sf)
    t_draw.rounded_rectangle([tx1, ty1, split_x, ty1 + left_banner_h], radius=radius, fill=(255, 107, 74, 255))
    t_draw.rectangle([tx1, ty1 + radius, split_x, ty1 + left_banner_h], fill=(255, 107, 74, 255))

    # Right Section Background
    t_draw.rounded_rectangle([split_x, ty1, tx2, ty2], radius=radius, fill=(254, 247, 230, 255))
    t_draw.rectangle([split_x, ty1, split_x + radius, ty2], fill=(254, 247, 230, 255))

    # Semicircle Cutouts
    t_draw.ellipse([split_x - cutout_radius, ty1 - cutout_radius, split_x + cutout_radius, ty1 + cutout_radius], fill=(0, 0, 0, 0))
    t_draw.ellipse([split_x - cutout_radius, ty2 - cutout_radius, split_x + cutout_radius, ty2 + cutout_radius], fill=(0, 0, 0, 0))

    # Cutout Border Arcs
    t_draw.arc([split_x - cutout_radius, ty1 - cutout_radius, split_x + cutout_radius, ty1 + cutout_radius], start=0, end=180, fill=c_coral_main, width=int(5 * sf))
    t_draw.arc([split_x - cutout_radius, ty2 - cutout_radius, split_x + cutout_radius, ty2 + cutout_radius], start=180, end=360, fill=c_coral_main, width=int(5 * sf))

    # Dashed Perforation Line
    dash_h = int(12 * sf)
    gap_h = int(10 * sf)
    cur_y = ty1 + cutout_radius + int(10 * sf)
    end_y = ty2 - cutout_radius - int(10 * sf)
    while cur_y < end_y:
        t_draw.line([(split_x, cur_y), (split_x, min(cur_y + dash_h, end_y))], fill=(215, 195, 185), width=int(3 * sf))
        cur_y += dash_h + gap_h

    img.paste(ticket_layer, (0, 0), ticket_layer)

    # ----------------------------------------------------
    # CONTENT
    # ----------------------------------------------------
    c_draw = ImageDraw.Draw(img)

    # Left Header Branding
    brand_x = tx1 + int(36 * sf)
    brand_y = ty1 + int(24 * sf)
    c_draw.text((brand_x, brand_y), "PURRFECT SHOP • DISCOUNT VOUCHER", fill=c_white, font=font_brand)

    # Gold Seal Badge
    badge_w = int(150 * sf)
    badge_h = int(40 * sf)
    badge_x = split_x - badge_w - int(24 * sf)
    badge_y = ty1 + int(26 * sf)
    c_draw.rounded_rectangle([badge_x, badge_y, badge_x + badge_w, badge_y + badge_h], radius=int(20 * sf), fill=c_gold_main)
    c_draw.text((badge_x + int(16 * sf), badge_y + int(7 * sf)), "SPECIAL 10%", fill=c_white, font=font_badge)

    # Headline
    content_y = ty1 + left_banner_h + int(24 * sf)
    c_draw.text((brand_x, content_y), "คูปองส่วนลดพิเศษ", fill=c_text_muted, font=font_title)
    
    disc_y = content_y + int(52 * sf)
    c_draw.text((brand_x, disc_y), "ลดทันที 10%", fill=c_coral_dark, font=font_huge_discount)
    
    # 10% OFF Badge
    tag_off_x = brand_x + int(440 * sf)
    tag_off_y = disc_y + int(22 * sf)
    c_draw.rounded_rectangle([tag_off_x, tag_off_y, tag_off_x + int(140 * sf), tag_off_y + int(48 * sf)], radius=int(12 * sf), fill=(255, 237, 230), outline=c_coral_main, width=int(2 * sf))
    c_draw.text((tag_off_x + int(15 * sf), tag_off_y + int(9 * sf)), "10% OFF", fill=c_coral_dark, font=font_term)

    # Terms & Conditions Box (The 3 User Requirements)
    terms_y = disc_y + int(112 * sf)
    terms_box_w = split_x - brand_x - int(36 * sf)
    terms_box_h = int(164 * sf)

    c_draw.rounded_rectangle([brand_x, terms_y, brand_x + terms_box_w, terms_y + terms_box_h], radius=int(16 * sf), fill=(248, 250, 252), outline=(226, 232, 240), width=int(2 * sf))
    c_draw.text((brand_x + int(20 * sf), terms_y + int(12 * sf)), "เงื่อนไขและข้อกำหนดการใช้คูปอง:", fill=c_text_dark, font=font_term)

    # 3 Conditions
    t_item1_y = terms_y + int(50 * sf)
    t_item2_y = terms_y + int(86 * sf)
    t_item3_y = terms_y + int(122 * sf)

    # Condition 1: ใช้ได้ 1 ครั้ง
    c_draw.ellipse([brand_x + int(20 * sf), t_item1_y + int(4 * sf), brand_x + int(36 * sf), t_item1_y + int(20 * sf)], fill=c_mint_dark)
    c_draw.text((brand_x + int(48 * sf), t_item1_y - int(3 * sf)), "1. ใช้ได้ 1 ครั้ง (จำกัด 1 สิทธิ์ต่อบัญชีผู้ใช้งาน)", fill=c_text_dark, font=font_term)

    # Condition 2: ใช้ได้ 1 เดือน
    c_draw.ellipse([brand_x + int(20 * sf), t_item2_y + int(4 * sf), brand_x + int(36 * sf), t_item2_y + int(20 * sf)], fill=c_gold_dark)
    c_draw.text((brand_x + int(48 * sf), t_item2_y - int(3 * sf)), "2. ใช้ได้ภายใน 1 เดือน (นับจากวันที่ได้รับคูปอง)", fill=c_text_dark, font=font_term)

    # Condition 3: ใช้ได้เมื่อขั้นต่ำ 300 บาท
    c_draw.ellipse([brand_x + int(20 * sf), t_item3_y + int(4 * sf), brand_x + int(36 * sf), t_item3_y + int(20 * sf)], fill=c_coral_dark)
    c_draw.text((brand_x + int(48 * sf), t_item3_y - int(3 * sf)), "3. ใช้ได้เมื่อมียอดสั่งซื้อขั้นต่ำ 300 บาท", fill=c_coral_dark, font=font_term)

    # --- Right Section ---
    right_center_x = split_x + int((tx2 - split_x) / 2)
    
    # Cat Image Thumbnail
    cat_path = "assets/images/cat_british.jpg"
    if os.path.exists(cat_path):
        try:
            c_raw = Image.open(cat_path).convert("RGBA")
            cs = int(112 * sf)
            c_raw = c_raw.resize((cs, cs), Image.Resampling.LANCZOS)
            c_mask = Image.new("L", (cs, cs), 0)
            cm_draw = ImageDraw.Draw(c_mask)
            cm_draw.rounded_rectangle([0, 0, cs, cs], radius=int(24 * sf), fill=255)
            c_box = Image.new("RGBA", (cs, cs), (0, 0, 0, 0))
            c_box.paste(c_raw, (0, 0), c_mask)
            cb_draw = ImageDraw.Draw(c_box)
            cb_draw.rounded_rectangle([0, 0, cs - 1, cs - 1], radius=int(24 * sf), outline=c_gold_main, width=int(4 * sf))
            img.paste(c_box, (right_center_x - int(cs / 2), ty1 + int(35 * sf)), c_box)
        except Exception as e:
            print("Cat error:", e)

    # Promo Code Header
    code_hdr_y = ty1 + int(165 * sf)
    c_draw.text((right_center_x - int(65 * sf), code_hdr_y), "รหัสคูปองส่วนลด", fill=c_text_muted, font=font_code_title)

    # Promo Code Box: CAT10OFF
    code_box_w = int(240 * sf)
    code_box_h = int(66 * sf)
    code_box_x = right_center_x - int(code_box_w / 2)
    code_box_y = code_hdr_y + int(36 * sf)

    c_draw.rounded_rectangle([code_box_x, code_box_y, code_box_x + code_box_w, code_box_y + code_box_h], 
                             radius=int(12 * sf), fill=c_white, outline=c_gold_main, width=int(3 * sf))
    
    c_draw.text((code_box_x + int(24 * sf), code_box_y + int(12 * sf)), "CAT10OFF", fill=c_gold_dark, font=font_code)

    # Barcode Mockup
    bar_y = code_box_y + int(88 * sf)
    bar_w = int(220 * sf)
    bar_x1 = right_center_x - int(bar_w / 2)
    
    bar_pattern = [3, 2, 4, 1, 2, 3, 1, 4, 2, 1, 3, 2, 4, 1, 2, 3, 2, 1, 4, 2, 3, 1]
    cur_bx = bar_x1
    for bw in bar_pattern:
        scaled_bw = max(1, int(bw * 2 * sf))
        c_draw.rectangle([cur_bx, bar_y, cur_bx + scaled_bw, bar_y + int(40 * sf)], fill=c_text_dark)
        cur_bx += scaled_bw + int(4 * sf)

    # CTA Button
    btn_r_w = int(240 * sf)
    btn_r_h = int(60 * sf)
    btn_r_x = right_center_x - int(btn_r_w / 2)
    btn_r_y = ty2 - btn_r_h - int(25 * sf)

    c_draw.rounded_rectangle([btn_r_x, btn_r_y, btn_r_x + btn_r_w, btn_r_y + btn_r_h], radius=int(30 * sf), fill=c_coral_main)
    c_draw.text((btn_r_x + int(45 * sf), btn_r_y + int(13 * sf)), "คัดลอกรหัสโค้ด >", fill=c_white, font=font_term)

    # Save
    img.save(output_path, "PNG", quality=95)
    img.save("assets/images/coupon_10pct.png", "PNG", quality=95)
    img.save("docs/assets/images/coupon_10pct.png", "PNG", quality=95)
    img.save("GITHUB_PAGES_EXPORT/assets/images/coupon_10pct.png", "PNG", quality=95)
    img.save("INFINITYFREE_HTDOCS_UPLOAD/assets/images/coupon_10pct.png", "PNG", quality=95)
    print(f"Saved: {output_path}")

if __name__ == "__main__":
    create_discount_coupon(1200, 600, "coupon_10pct.png")
