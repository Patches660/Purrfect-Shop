import os
from PIL import Image, ImageDraw, ImageFilter

def make_framed_rich_menu():
    src_path = r"C:\Users\Windows\.gemini\antigravity\brain\63a36a4c-6ead-4c37-aede-64d365cc7888\purrfect_orange_cat_richmenu_1790219815331.jpg"
    if not os.path.exists(src_path):
        src_path = "assets/images/line_rich_menu_art.jpg"
    
    src = Image.open(src_path).convert("RGBA")
    sw, sh = src.size

    # Target Dimensions (2500 x 1686)
    W = 2500
    H = 1686

    # Canvas Background: Warm Cream / Pastel Linen
    canvas = Image.new("RGBA", (W, H), (253, 248, 244, 255))

    # Margin & Gap Parameters
    pad_outer_x = 36  # Outer margin left/right
    pad_outer_y = 32  # Outer margin top/bottom
    gap_y = 28        # Gap between Top row and Bottom row
    gap_x = 28        # Gap between bottom 3 columns
    corner_radius = 42 # Rounded corner radius
    border_width = 10  # Bold thick border stroke width

    # Row Heights
    avail_h = H - (pad_outer_y * 2) - gap_y
    top_h = int(avail_h * 0.51)
    bot_h = avail_h - top_h

    top_w = W - (pad_outer_x * 2)

    # Bottom Column Widths
    avail_w = W - (pad_outer_x * 2) - (gap_x * 2)
    col_w = int(avail_w / 3.0)

    # Precise Cropping Coordinates without border bleeding
    # 1. Top Section (Block 1)
    crop_top = src.crop((0, 0, sw, 420))
    # 2. Bottom Left (Block 2)
    crop_bot_l = src.crop((0, 444, 420, sh))
    # 3. Bottom Center (Block 3)
    crop_bot_c = src.crop((422, 444, 842, sh))
    # 4. Bottom Right (Block 4)
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
            "border_color": (13, 148, 136, 255), # Deep Dark Teal / Turquoise
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

        # Resize cropped image to fit target card box
        resized_img = item["img"].resize((card_w, card_h), Image.Resampling.LANCZOS)

        # Create rounded mask
        mask = Image.new("L", (card_w, card_h), 0)
        m_draw = ImageDraw.Draw(mask)
        m_draw.rounded_rectangle([0, 0, card_w, card_h], radius=corner_radius, fill=255)

        # Create card container
        card_layer = Image.new("RGBA", (card_w, card_h), (0, 0, 0, 0))
        card_layer.paste(resized_img, (0, 0), mask)

        # Draw Bold Dark Border Outline
        b_draw = ImageDraw.Draw(card_layer)
        # Inner border
        b_draw.rounded_rectangle([0, 0, card_w - 1, card_h - 1], radius=corner_radius, outline=item["border_color"], width=border_width)

        # Drop Shadow
        shadow_margin = 16
        shadow_layer = Image.new("RGBA", (card_w + shadow_margin * 2, card_h + shadow_margin * 2), (0, 0, 0, 0))
        s_draw = ImageDraw.Draw(shadow_layer)
        s_draw.rounded_rectangle([shadow_margin, shadow_margin + 6, shadow_margin + card_w, shadow_margin + card_h + 6], 
                                 radius=corner_radius, fill=item["shadow_color"])
        shadow_layer = shadow_layer.filter(ImageFilter.GaussianBlur(10))

        # Paste Shadow then Card
        canvas.paste(shadow_layer, (x1 - shadow_margin, y1 - shadow_margin), shadow_layer)
        canvas.paste(card_layer, (x1, y1), card_layer)

    # Convert to RGB for saving
    final_img = canvas.convert("RGB")

    # Save Ultra HD 2500x1686
    final_img.save("line_rich_menu_2500x1686.png", "PNG", quality=95)
    final_img.save("assets/images/line_rich_menu_2500x1686.png", "PNG", quality=95)
    final_img.save("docs/line_rich_menu_2500x1686.png", "PNG", quality=95)
    final_img.save("GITHUB_PAGES_EXPORT/line_rich_menu_2500x1686.png", "PNG", quality=95)
    final_img.save("INFINITYFREE_HTDOCS_UPLOAD/line_rich_menu_2500x1686.png", "PNG", quality=95)

    # Save Compact 800x540
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

    print("Successfully generated framed Rich Menu images with margins and dark borders!")

if __name__ == "__main__":
    make_framed_rich_menu()
