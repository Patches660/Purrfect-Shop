import os
from PIL import Image, ImageDraw, ImageFont, ImageFilter

def make_custom_cat_rich_menu():
    # Paths
    user_cat_path = r"C:\Users\Windows\.gemini\antigravity\brain\63a36a4c-6ead-4c37-aede-64d365cc7888\.user_uploaded\media_1790220181249.jpg"
    base_art_path = r"C:\Users\Windows\.gemini\antigravity\brain\63a36a4c-6ead-4c37-aede-64d365cc7888\purrfect_orange_cat_richmenu_1790219815331.jpg"

    src = Image.open(base_art_path).convert("RGBA")
    sw, sh = src.size

    user_cat = Image.open(user_cat_path).convert("RGBA")

    # Dimensions
    W = 2500
    H = 1686

    # Canvas Background: Warm Cream / Pastel Linen
    canvas = Image.new("RGBA", (W, H), (253, 248, 244, 255))

    pad_outer_x = 36
    pad_outer_y = 32
    gap_y = 28
    gap_x = 28
    corner_radius = 42
    border_width = 10

    avail_h = H - (pad_outer_y * 2) - gap_y
    top_h = int(avail_h * 0.51)
    bot_h = avail_h - top_h

    top_w = W - (pad_outer_x * 2)

    avail_w = W - (pad_outer_x * 2) - (gap_x * 2)
    col_w = int(avail_w / 3.0)

    # Fonts
    font_bold_path = "C:/Windows/Fonts/LeelaUIb.ttf" if os.path.exists("C:/Windows/Fonts/LeelaUIb.ttf") else "C:/Windows/Fonts/tahomabd.ttf"
    font_reg_path = "C:/Windows/Fonts/LeelawUI.ttf" if os.path.exists("C:/Windows/Fonts/LeelawUI.ttf") else "C:/Windows/Fonts/tahoma.ttf"

    def get_font(size, bold=False):
        p = font_bold_path if bold else font_reg_path
        try:
            return ImageFont.truetype(p, int(size))
        except:
            return ImageFont.load_default()

    font_brand = get_font(108, bold=True)
    font_sub_brand = get_font(88, bold=True)
    font_btn = get_font(52, bold=True)

    # 1. Clean Custom Top Section (Block 1)
    crop_top = Image.new("RGBA", (top_w, top_h), (255, 255, 255, 255))
    top_draw = ImageDraw.Draw(crop_top)

    # Gradient background for Top Card (Coral / Peach to Sunset Gold)
    for x in range(top_w):
        ratio = x / float(top_w)
        r = int(255 - (255 - 255) * ratio)
        g = int(120 + (180 - 120) * ratio)
        b = int(95 + (130 - 95) * ratio)
        top_draw.line([(x, 0), (x, top_h)], fill=(r, g, b, 255))

    # Golden decorative arc on right
    gold_arc = Image.new("RGBA", (top_w, top_h), (0, 0, 0, 0))
    ga_draw = ImageDraw.Draw(gold_arc)
    ga_draw.ellipse([int(top_w * 0.65), int(-top_h * 0.4), int(top_w * 1.35), int(top_h * 1.4)], fill=(255, 240, 215, 75))
    crop_top.paste(gold_arc, (0, 0), gold_arc)

    # Composite User's Purple Cat Avatar
    cat_badge_size = int(top_h * 0.84)
    user_cat_resized = user_cat.resize((cat_badge_size, cat_badge_size), Image.Resampling.LANCZOS)

    cat_mask = Image.new("L", (cat_badge_size, cat_badge_size), 0)
    cm_draw = ImageDraw.Draw(cat_mask)
    cm_draw.rounded_rectangle([0, 0, cat_badge_size, cat_badge_size], radius=int(cat_badge_size * 0.28), fill=255)

    cat_badge = Image.new("RGBA", (cat_badge_size, cat_badge_size), (0, 0, 0, 0))
    cat_badge.paste(user_cat_resized, (0, 0), cat_mask)

    cb_draw = ImageDraw.Draw(cat_badge)
    cb_draw.rounded_rectangle([0, 0, cat_badge_size - 1, cat_badge_size - 1], 
                             radius=int(cat_badge_size * 0.28), 
                             outline=(255, 255, 255, 255), 
                             width=14)
    cb_draw.rounded_rectangle([14, 14, cat_badge_size - 15, cat_badge_size - 15], 
                             radius=int(cat_badge_size * 0.26), 
                             outline=(217, 72, 38, 255), 
                             width=7)

    badge_shadow = Image.new("RGBA", (cat_badge_size + 30, cat_badge_size + 30), (0, 0, 0, 0))
    bs_draw = ImageDraw.Draw(badge_shadow)
    bs_draw.rounded_rectangle([15, 20, 15 + cat_badge_size, 20 + cat_badge_size], 
                             radius=int(cat_badge_size * 0.28), 
                             fill=(100, 20, 10, 70))
    badge_shadow = badge_shadow.filter(ImageFilter.GaussianBlur(12))

    cat_pos_x = int(top_w * 0.05)
    cat_pos_y = int((top_h - cat_badge_size) / 2)

    crop_top.paste(badge_shadow, (cat_pos_x - 15, cat_pos_y - 15), badge_shadow)
    crop_top.paste(cat_badge, (cat_pos_x, cat_pos_y), cat_badge)

    # Top Card Typography
    text_start_x = cat_pos_x + cat_badge_size + int(70 * (W / 2500.0))
    top_draw.text((text_start_x + 3, int(top_h * 0.14) + 3), "Purrfect Shop", fill=(120, 20, 10, 180), font=font_brand)
    top_draw.text((text_start_x, int(top_h * 0.14)), "Purrfect Shop", fill=(255, 255, 255, 255), font=font_brand)

    top_draw.text((text_start_x + 3, int(top_h * 0.40) + 3), "เข้าสู่หน้าร้าน", fill=(120, 20, 10, 180), font=font_sub_brand)
    top_draw.text((text_start_x, int(top_h * 0.40)), "เข้าสู่หน้าร้าน", fill=(255, 255, 255, 255), font=font_sub_brand)

    # CTA Button: [ สำรวจสินค้า 🐾 ]
    btn_w = int(580 * (W / 2500.0))
    btn_h = int(112 * (W / 2500.0))
    btn_x = text_start_x
    btn_y = int(top_h * 0.68)

    top_draw.rounded_rectangle([btn_x, btn_y, btn_x + btn_w, btn_y + btn_h], radius=int(btn_h / 2), fill=(255, 255, 255, 255), outline=(217, 72, 38, 255), width=6)
    top_draw.text((btn_x + int(60 * (W / 2500.0)), btn_y + int(22 * (W / 2500.0))), "สำรวจสินค้า 🐾", fill=(217, 72, 38, 255), font=font_btn)

    # 2. Bottom Left
    crop_bot_l = src.crop((0, 444, 420, sh))
    # 3. Bottom Center
    crop_bot_c = src.crop((422, 444, 842, sh))
    # 4. Bottom Right
    crop_bot_r = src.crop((844, 444, sw, sh))

    cards_info = [
        {
            "img": crop_top,
            "target_box": (pad_outer_x, pad_outer_y, pad_outer_x + top_w, pad_outer_y + top_h),
            "border_color": (217, 72, 38, 255),  # Deep Dark Coral
            "shadow_color": (217, 72, 38, 70)
        },
        {
            "img": crop_bot_l,
            "target_box": (pad_outer_x, pad_outer_y + top_h + gap_y, pad_outer_x + col_w, pad_outer_y + top_h + gap_y + bot_h),
            "border_color": (13, 148, 136, 255), # Deep Dark Teal
            "shadow_color": (13, 148, 136, 70)
        },
        {
            "img": crop_bot_c,
            "target_box": (pad_outer_x + col_w + gap_x, pad_outer_y + top_h + gap_y, pad_outer_x + (col_w * 2) + gap_x, pad_outer_y + top_h + gap_y + bot_h),
            "border_color": (217, 119, 6, 255),  # Deep Golden Amber
            "shadow_color": (217, 119, 6, 70)
        },
        {
            "img": crop_bot_r,
            "target_box": (pad_outer_x + (col_w * 2) + (gap_x * 2), pad_outer_y + top_h + gap_y, W - pad_outer_x, pad_outer_y + top_h + gap_y + bot_h),
            "border_color": (225, 29, 72, 255),  # Deep Rich Rose Red
            "shadow_color": (225, 29, 72, 70)
        }
    ]

    for item in cards_info:
        x1, y1, x2, y2 = item["target_box"]
        card_w = x2 - x1
        card_h = y2 - y1

        resized_img = item["img"].resize((card_w, card_h), Image.Resampling.LANCZOS)

        mask = Image.new("L", (card_w, card_h), 0)
        m_draw = ImageDraw.Draw(mask)
        m_draw.rounded_rectangle([0, 0, card_w, card_h], radius=corner_radius, fill=255)

        card_layer = Image.new("RGBA", (card_w, card_h), (0, 0, 0, 0))
        card_layer.paste(resized_img, (0, 0), mask)

        b_draw = ImageDraw.Draw(card_layer)
        b_draw.rounded_rectangle([0, 0, card_w - 1, card_h - 1], radius=corner_radius, outline=item["border_color"], width=border_width)

        shadow_margin = 16
        shadow_layer = Image.new("RGBA", (card_w + shadow_margin * 2, card_h + shadow_margin * 2), (0, 0, 0, 0))
        s_draw = ImageDraw.Draw(shadow_layer)
        s_draw.rounded_rectangle([shadow_margin, shadow_margin + 6, shadow_margin + card_w, shadow_margin + card_h + 6], 
                                 radius=corner_radius, fill=item["shadow_color"])
        shadow_layer = shadow_layer.filter(ImageFilter.GaussianBlur(10))

        canvas.paste(shadow_layer, (x1 - shadow_margin, y1 - shadow_margin), shadow_layer)
        canvas.paste(card_layer, (x1, y1), card_layer)

    final_img = canvas.convert("RGB")

    # Save 2500x1686
    final_img.save("line_rich_menu_2500x1686.png", "PNG", quality=95)
    final_img.save("assets/images/line_rich_menu_2500x1686.png", "PNG", quality=95)
    final_img.save("docs/line_rich_menu_2500x1686.png", "PNG", quality=95)
    final_img.save("GITHUB_PAGES_EXPORT/line_rich_menu_2500x1686.png", "PNG", quality=95)
    final_img.save("INFINITYFREE_HTDOCS_UPLOAD/line_rich_menu_2500x1686.png", "PNG", quality=95)

    # Save 800x540
    img_800 = final_img.resize((800, 540), Image.Resampling.LANCZOS)
    img_800.save("line_rich_menu_800x540.png", "PNG", quality=95)
    img_800.save("assets/images/line_rich_menu_800x540.png", "PNG", quality=95)
    img_800.save("docs/line_rich_menu_800x540.png", "PNG", quality=95)
    img_800.save("GITHUB_PAGES_EXPORT/line_rich_menu_800x540.png", "PNG", quality=95)
    img_800.save("INFINITYFREE_HTDOCS_UPLOAD/line_rich_menu_800x540.png", "PNG", quality=95)

    # Save JPG
    final_img.save("line_rich_menu_art.jpg", "JPEG", quality=95)
    final_img.save("assets/images/line_rich_menu_art.jpg", "JPEG", quality=95)
    final_img.save("docs/line_rich_menu_art.jpg", "JPEG", quality=95)
    final_img.save("GITHUB_PAGES_EXPORT/line_rich_menu_art.jpg", "JPEG", quality=95)
    final_img.save("INFINITYFREE_HTDOCS_UPLOAD/line_rich_menu_art.jpg", "JPEG", quality=95)

    print("Successfully rendered custom cat Rich Menu with zero artifact!")

if __name__ == "__main__":
    make_custom_cat_rich_menu()
