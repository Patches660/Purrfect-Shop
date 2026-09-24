import os
from PIL import Image, ImageDraw, ImageFont

def create_rich_menu(width=2500, height=1686, output_path="line_rich_menu_2500x1686.png"):
    # Base Canvas
    img = Image.new("RGBA", (width, height), (255, 255, 255, 255))
    draw = ImageDraw.Draw(img)

    sf = width / 2500.0

    # Fonts
    font_bold_path = "C:/Windows/Fonts/LeelaUIb.ttf" if os.path.exists("C:/Windows/Fonts/LeelaUIb.ttf") else "C:/Windows/Fonts/tahomabd.ttf"
    font_reg_path = "C:/Windows/Fonts/LeelawUI.ttf" if os.path.exists("C:/Windows/Fonts/LeelawUI.ttf") else "C:/Windows/Fonts/tahoma.ttf"

    def get_font(size, bold=False):
        p = font_bold_path if bold else font_reg_path
        try:
            return ImageFont.truetype(p, int(size * sf))
        except:
            return ImageFont.load_default()

    font_huge = get_font(76, bold=True)
    font_title = get_font(58, bold=True)
    font_block_title = get_font(46, bold=True)
    font_sub_bold = get_font(34, bold=True)
    font_sub = get_font(30, bold=False)
    font_badge = get_font(26, bold=True)
    font_tag = get_font(30, bold=True)

    # Color Palette
    c_coral_dark = (220, 70, 45)
    c_coral_main = (255, 107, 74)
    c_coral_light = (255, 138, 110)
    c_coral_soft = (255, 243, 238)
    
    c_gold_main = (235, 140, 0)
    c_gold_soft = (254, 247, 230)
    
    c_blue_main = (2, 132, 199)
    c_blue_soft = (240, 249, 255)
    
    c_text_dark = (30, 41, 59)
    c_text_muted = (100, 116, 139)
    c_white = (255, 255, 255)

    half_h = int(height * 0.5)

    # ----------------------------------------------------
    # BLOCK A: TOP HALF (0, 0, width, half_h) -> ลิ้งไปหน้าร้าน
    # ----------------------------------------------------
    # Warm background gradient
    for y in range(0, half_h):
        ratio = y / float(half_h)
        r = int(255)
        g = int(107 + (120 - 107) * ratio)
        b = int(74 + (90 - 74) * ratio)
        draw.line([(0, y), (width, y)], fill=(r, g, b, 255))

    # Inner White Container Card with soft border
    card_margin = int(28 * sf)
    draw.rounded_rectangle([card_margin, card_margin, width - card_margin, half_h - card_margin], 
                           radius=int(32 * sf), fill=(255, 255, 255, 245), outline=(255, 225, 215, 255), width=int(3 * sf))

    # Cat Photo on Left of Block A
    cat_img_path = "assets/images/cat_british.jpg"
    if os.path.exists(cat_img_path):
        try:
            cat_raw = Image.open(cat_img_path).convert("RGBA")
            cat_size = int(680 * sf)
            cat_raw = cat_raw.resize((cat_size, cat_size), Image.Resampling.LANCZOS)
            
            mask = Image.new("L", (cat_size, cat_size), 0)
            m_draw = ImageDraw.Draw(mask)
            m_draw.rounded_rectangle([0, 0, cat_size, cat_size], radius=int(48 * sf), fill=255)
            
            cat_box = Image.new("RGBA", (cat_size, cat_size), (0, 0, 0, 0))
            cat_box.paste(cat_raw, (0, 0), mask)

            # Coral Frame
            b_draw = ImageDraw.Draw(cat_box)
            b_draw.rounded_rectangle([0, 0, cat_size - 1, cat_size - 1], radius=int(48 * sf), outline=c_coral_main, width=int(8 * sf))

            cat_x = int(65 * sf)
            cat_y = int((half_h - cat_size) / 2)
            img.paste(cat_box, (cat_x, cat_y), cat_box)
        except Exception as e:
            print("Error cat:", e)

    # Right Side Content for Block A
    text_x = int(800 * sf)
    tag_y = int(95 * sf)
    
    # Pill Tag: PURRFECT SHOP CATTERY & BOUTIQUE
    tag_w = int(720 * sf)
    tag_h = int(58 * sf)
    draw.rounded_rectangle([text_x, tag_y, text_x + tag_w, tag_y + tag_h], radius=int(29 * sf), fill=c_coral_soft, outline=c_coral_main, width=int(2 * sf))
    draw.text((text_x + int(28 * sf), tag_y + int(10 * sf)), "PURRFECT SHOP • CATTERY & BOUTIQUE", fill=c_coral_dark, font=font_tag)

    # Main Headline
    draw.text((text_x, tag_y + int(85 * sf)), "ยินดีต้อนรับสู่ Purrfect Shop", fill=c_coral_dark, font=font_huge)
    draw.text((text_x, tag_y + int(190 * sf)), "ฟาร์มเพาะพันธุ์น้องแมวสายพันธุ์แท้ 32 สายพันธุ์ พร้อมใบเพ็ดดีกรี & การันตี 180 วัน", fill=c_text_dark, font=font_sub)

    # CTA Button
    btn_w = int(720 * sf)
    btn_h = int(110 * sf)
    btn_y = tag_y + int(270 * sf)
    draw.rounded_rectangle([text_x, btn_y, text_x + btn_w, btn_y + btn_h], radius=int(28 * sf), fill=c_coral_main)
    draw.text((text_x + int(45 * sf), btn_y + int(22 * sf)), "A. เข้าชมหน้าร้านออนไลน์ทั้งหมด  >", fill=c_white, font=font_block_title)


    # ----------------------------------------------------
    # BOTTOM HALF: 3 EQUAL BLOCKS (B, C, D)
    # ----------------------------------------------------
    col_w = width / 3.0

    blocks = [
        {
            "id": "B",
            "idx": 0,
            "title": "B. สมัครสมาชิก",
            "desc1": "รับส่วนลดทันที 5% ทุกตัว",
            "desc2": "ฟรี Welcome Starter Kit 2,500 บ.",
            "btn": "สมัครสมาชิกรับสิทธิ์ >",
            "badge": "HOT DEAL",
            "bg": (255, 248, 245),
            "accent": c_coral_main
        },
        {
            "id": "C",
            "idx": 1,
            "title": "C. คูปองส่วนลด",
            "desc1": "รวมโค้ด & สิทธิพิเศษสมาชิก",
            "desc2": "สะสม Paw Points แลกของรางวัล",
            "btn": "กดรับคูปอง & ดีล >",
            "badge": "SPECIAL",
            "bg": c_gold_soft,
            "accent": c_gold_main
        },
        {
            "id": "D",
            "idx": 2,
            "title": "D. สินค้า / น้องแมว",
            "desc1": "แคตตาล็อกน้องแมว 32 สายพันธุ์",
            "desc2": "ตรวจสุขภาพ & ฉีดวัคซีนครบ",
            "btn": "เลือกชมน้องแมว >",
            "badge": "CATALOG",
            "bg": c_blue_soft,
            "accent": c_blue_main
        }
    ]

    for b in blocks:
        bx1 = int(b["idx"] * col_w)
        bx2 = int((b["idx"] + 1) * col_w)
        by1 = half_h
        by2 = height

        # Background
        draw.rectangle([bx1, by1, bx2, by2], fill=b["bg"])

        # Inner Card
        m = int(22 * sf)
        cx1 = bx1 + m
        cx2 = bx2 - m
        cy1 = by1 + m
        cy2 = by2 - m

        draw.rounded_rectangle([cx1, cy1, cx2, cy2], radius=int(30 * sf), fill=c_white, outline=(230, 230, 230), width=int(2 * sf))

        # Top Badge
        bdg_w = int(170 * sf)
        bdg_h = int(48 * sf)
        draw.rounded_rectangle([cx2 - bdg_w - int(20 * sf), cy1 + int(24 * sf), cx2 - int(20 * sf), cy1 + int(24 * sf) + bdg_h],
                               radius=int(24 * sf), fill=b["accent"])
        draw.text((cx2 - bdg_w + int(14 * sf), cy1 + int(30 * sf)), b["badge"], fill=c_white, font=font_badge)

        # Content
        px = cx1 + int(35 * sf)
        py = cy1 + int(60 * sf)

        draw.text((px, py), b["title"], fill=b["accent"], font=font_title)
        draw.text((px, py + int(105 * sf)), b["desc1"], fill=c_text_dark, font=font_sub_bold)
        draw.text((px, py + int(170 * sf)), b["desc2"], fill=c_text_muted, font=font_sub)

        # Block Button
        btn_box_w = cx2 - cx1 - int(70 * sf)
        btn_box_h = int(98 * sf)
        btn_box_y = cy2 - btn_box_h - int(45 * sf)

        draw.rounded_rectangle([px, btn_box_y, px + btn_box_w, btn_box_y + btn_box_h], radius=int(24 * sf), fill=b["accent"])
        draw.text((px + int(45 * sf), btn_box_y + int(22 * sf)), b["btn"], fill=c_white, font=font_block_title)

    # Dividing Grid Lines
    grid_color = (210, 210, 210)
    draw.line([(0, half_h), (width, half_h)], fill=grid_color, width=int(4 * sf))
    draw.line([(int(col_w), half_h), (int(col_w), height)], fill=grid_color, width=int(4 * sf))
    draw.line([(int(col_w * 2), half_h), (int(col_w * 2), height)], fill=grid_color, width=int(4 * sf))

    # Outer Border
    draw.rectangle([0, 0, width - 1, height - 1], outline=(190, 190, 190), width=int(4 * sf))

    # Save
    img.save(output_path, "PNG", quality=95)
    print(f"Saved: {output_path}")

    # 800x540
    img_800 = img.resize((800, 540), Image.Resampling.LANCZOS)
    img_800.save("line_rich_menu_800x540.png", "PNG", quality=95)
    print("Saved: line_rich_menu_800x540.png")

    # Assets copies
    img.save("assets/images/line_rich_menu_2500x1686.png", "PNG", quality=95)
    img_800.save("assets/images/line_rich_menu_800x540.png", "PNG", quality=95)

if __name__ == "__main__":
    create_rich_menu(2500, 1686, "line_rich_menu_2500x1686.png")
