"""
generate_line_reward_vouchers.py
Ultra-luxury LINE OA Reward Voucher & Coupon Generator with mathematical group centering,
sparkling gold ambient effects, and ornamental designs.
"""

import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter, ImageEnhance

DIR_CURRENT = os.path.dirname(os.path.abspath(__file__))
DIR_ASSETS = os.path.join(DIR_CURRENT, 'assets', 'images')
DIR_LINE_ASSETS = os.path.join(DIR_CURRENT, 'LINE_ASSETS', '5_REWARD_CARD')

os.makedirs(DIR_LINE_ASSETS, exist_ok=True)

def get_thai_font(size, bold=False):
    paths = [
        "C:\\Windows\\Fonts\\LeelaUIb.ttf" if bold else "C:\\Windows\\Fonts\\LeelawUI.ttf",
        "C:\\Windows\\Fonts\\segoeuib.ttf" if bold else "C:\\Windows\\Fonts\\segoeui.ttf",
        "C:\\Windows\\Fonts\\tahomabd.ttf" if bold else "C:\\Windows\\Fonts\\tahoma.ttf",
        "C:\\Windows\\Fonts\\arialbd.ttf" if bold else "C:\\Windows\\Fonts\\arial.ttf"
    ]
    for p in paths:
        if os.path.exists(p):
            try:
                return ImageFont.truetype(p, size)
            except Exception:
                pass
    return ImageFont.load_default()

def draw_centered_text(draw, text, y, font, fill, width):
    bbox = font.getbbox(text)
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    x = (width - tw) // 2 - bbox[0]
    draw.text((x, y), text, font=font, fill=fill)
    return tw, th

def draw_sparkle(draw, cx, cy, radius, color):
    draw.line([(cx - radius, cy), (cx + radius, cy)], fill=color, width=2)
    draw.line([(cx, cy - radius), (cx, cy + radius)], fill=color, width=2)
    sub_r = radius * 0.45
    draw.line([(cx - sub_r, cy - sub_r), (cx + sub_r, cy + sub_r)], fill=color, width=1)
    draw.line([(cx - sub_r, cy + sub_r), (cx + sub_r, cy - sub_r)], fill=color, width=1)

def draw_trophy(draw, cx, cy, size, color):
    w = int(size * 0.8)
    h = size
    cup_top = cy - h//2
    cup_bottom = cy
    draw.pieslice((cx - w//2, cup_top - w//4, cx + w//2, cup_bottom + w//4), start=0, end=180, fill=color)
    draw.rectangle((cx - w//2, cup_top, cx + w//2, cup_top + w//3), fill=color)
    draw.arc((cx - w//2 - int(w*0.3), cup_top, cx - w//2 + int(w*0.1), cup_top + int(h*0.4)), start=90, end=270, fill=color, width=max(2, int(size*0.08)))
    draw.arc((cx + w//2 - int(w*0.1), cup_top, cx + w//2 + int(w*0.3), cup_top + int(h*0.4)), start=270, end=90, fill=color, width=max(2, int(size*0.08)))
    draw.rectangle((cx - max(2, int(w*0.06)), cup_bottom, cx + max(2, int(w*0.06)), cy + int(h*0.3)), fill=color)
    draw.rounded_rectangle((cx - w//3, cy + int(h*0.3), cx + w//3, cy + h//2), radius=4, fill=color)

def draw_gift_box(draw, cx, cy, size, color, ribbon_color=(255, 255, 255)):
    w = size
    h = size
    box_top = cy - h//2 + int(size * 0.25)
    box_bottom = cy + h//2
    draw.rounded_rectangle((cx - w//2, box_top, cx + w//2, box_bottom), radius=5, fill=color)
    lid_top = cy - h//2 + int(size * 0.1)
    draw.rounded_rectangle((cx - w//2 - 3, lid_top, cx + w//2 + 3, box_top + 3), radius=3, fill=color)
    ribbon_w = max(3, int(size * 0.16))
    draw.rectangle((cx - ribbon_w//2, lid_top, cx + ribbon_w//2, box_bottom), fill=ribbon_color)
    draw.rectangle((cx - w//2, box_top + int(h*0.3), cx + w//2, box_top + int(h*0.3) + ribbon_w), fill=ribbon_color)
    bow_r = int(size * 0.2)
    draw.ellipse((cx - bow_r - 2, lid_top - bow_r//2, cx - 2, lid_top + bow_r//2), outline=ribbon_color, width=2)
    draw.ellipse((cx + 2, lid_top - bow_r//2, cx + bow_r + 2, lid_top + bow_r//2), outline=ribbon_color, width=2)

def create_circular_cat_thumbnail(image_name, diameter, border_color=(255, 215, 0), border_width=8):
    cat_path = os.path.join(DIR_ASSETS, image_name)
    if not os.path.exists(cat_path):
        cat_path = os.path.join(DIR_ASSETS, 'cat_british.jpg')
    
    cat_img = Image.open(cat_path).convert("RGBA")
    w, h = cat_img.size
    min_dim = min(w, h)
    left = (w - min_dim) // 2
    top = int(h * 0.05) if h > min_dim else 0
    cat_img = cat_img.crop((left, top, left + min_dim, top + min_dim))
    cat_img = cat_img.resize((diameter, diameter), Image.Resampling.LANCZOS)
    
    mask = Image.new("L", (diameter, diameter), 0)
    dmask = ImageDraw.Draw(mask)
    dmask.ellipse((0, 0, diameter, diameter), fill=255)
    
    output = Image.new("RGBA", (diameter, diameter), (0,0,0,0))
    output.paste(cat_img, (0, 0), mask=mask)
    
    border_img = Image.new("RGBA", (diameter, diameter), (0,0,0,0))
    dborder = ImageDraw.Draw(border_img)
    dborder.ellipse((border_width//2, border_width//2, diameter - border_width//2, diameter - border_width//2), 
                    outline=border_color, width=border_width)
    
    output = Image.alpha_composite(output, border_img)
    return output

# =========================================================================
# 1. MAJOR REWARD 45% OFF (640x640 px - Perfectly Centered & Grouped)
# =========================================================================
def build_major_45pct_coupon_640():
    w, h = 640, 640
    base = Image.new("RGBA", (w, h), (14, 18, 30, 255))
    draw = ImageDraw.Draw(base)
    
    for r in range(420, 0, -18):
        alpha = int(50 * (1 - r / 420))
        draw.ellipse((w//2 - r, h//2 - r, w//2 + r, h//2 + r), fill=(212, 175, 55, alpha))
        
    for r in range(260, 0, -15):
        alpha = int(40 * (1 - r / 260))
        draw.ellipse((w//2 - r, 310 - r, w//2 + r, 310 + r), fill=(220, 38, 38, alpha))
        
    margin = 20
    card_rect = (margin, margin, w - margin, h - margin)
    inner_card = Image.new("RGBA", (w, h), (0,0,0,0))
    ic_draw = ImageDraw.Draw(inner_card)
    ic_draw.rounded_rectangle(card_rect, radius=28, fill=(20, 26, 42, 250), outline=(212, 175, 55, 255), width=5)
    
    inner_margin = margin + 10
    ic_draw.rounded_rectangle((inner_margin, inner_margin, w - inner_margin, h - inner_margin), 
                             radius=20, outline=(255, 223, 128, 140), width=2)
    
    base = Image.alpha_composite(base, inner_card)
    draw = ImageDraw.Draw(base)
    
    # Corner Sparkles
    sparkle_color = (255, 223, 128, 200)
    draw_sparkle(draw, 50, 50, radius=12, color=sparkle_color)
    draw_sparkle(draw, w - 50, 50, radius=12, color=sparkle_color)
    draw_sparkle(draw, 50, h - 50, radius=10, color=sparkle_color)
    draw_sparkle(draw, w - 50, h - 50, radius=10, color=sparkle_color)
    
    # 1. Top VIP Pill (Group Centering Icon + Text)
    font_badge = get_thai_font(17, bold=True)
    badge_text = "รางวัลใหญ่ • 5 PAW POINTS"
    bbox_b = font_badge.getbbox(badge_text)
    tb_w = bbox_b[2] - bbox_b[0]
    tb_h = bbox_b[3] - bbox_b[1]
    
    icon_sz = 22
    gap_b = 10
    total_b_content = icon_sz + gap_b + tb_w
    pill_w = total_b_content + 48
    pill_h = 40
    pill_y = margin + 20
    pill_x = (w - pill_w) // 2
    
    draw.rounded_rectangle((pill_x, pill_y, pill_x + pill_w, pill_y + pill_h), radius=20, fill=(212, 175, 55, 255))
    content_start_x = pill_x + (pill_w - total_b_content) // 2
    draw_trophy(draw, content_start_x + icon_sz//2, pill_y + pill_h//2, size=icon_sz, color=(14, 18, 30))
    draw.text((content_start_x + icon_sz + gap_b - bbox_b[0], pill_y + (pill_h - tb_h)//2 - bbox_b[1]), 
              badge_text, font=font_badge, fill=(14, 18, 30, 255))
    
    # 2. Main Title (Centered)
    font_title = get_thai_font(42, bold=True)
    draw_centered_text(draw, "GRAND REWARD", margin + 70, font_title, fill=(255, 223, 128, 255), width=w)
    
    # 3. Subtitle (Centered)
    font_sub = get_thai_font(21, bold=True)
    draw_centered_text(draw, "ส่วนลดพิเศษ 45% ทันที!", margin + 120, font_sub, fill=(255, 255, 255, 255), width=w)
    
    # 4. Circular Cat Portrait Frame (Centered)
    cat_diameter = 190
    cat_thumb = create_circular_cat_thumbnail('cat_americanshorthair.jpg', diameter=cat_diameter, border_color=(255, 215, 0, 255), border_width=8)
    cat_x = (w - cat_diameter) // 2
    cat_y = margin + 158
    base.paste(cat_thumb, (cat_x, cat_y), mask=cat_thumb)
    
    draw = ImageDraw.Draw(base)
    draw_sparkle(draw, cat_x - 18, cat_y + 45, radius=14, color=(255, 215, 0, 255))
    draw_sparkle(draw, cat_x + cat_diameter + 18, cat_y + 45, radius=14, color=(255, 215, 0, 255))
    
    # 5. Big Red & Gold Discount Badge (Group Centered Icon + Text)
    font_val = get_thai_font(27, bold=True)
    val_text = "ลดทันที 45% ทุกยอด"
    bbox_v = font_val.getbbox(val_text)
    tv_w = bbox_v[2] - bbox_v[0]
    tv_h = bbox_v[3] - bbox_v[1]
    
    icon_v_sz = 26
    gap_v = 12
    total_v_content = icon_v_sz + gap_v + tv_w
    badge_w = total_v_content + 56
    badge_h = 56
    badge_y = margin + 368
    badge_x = (w - badge_w) // 2
    
    draw.rounded_rectangle((badge_x, badge_y, badge_x + badge_w, badge_y + badge_h), radius=28, fill=(220, 38, 38, 255), outline=(255, 223, 128, 255), width=3)
    v_content_start_x = badge_x + (badge_w - total_v_content) // 2
    draw_gift_box(draw, v_content_start_x + icon_v_sz//2, badge_y + badge_h//2, size=icon_v_sz, color=(255, 255, 255), ribbon_color=(220, 38, 38))
    draw.text((v_content_start_x + icon_v_sz + gap_v - bbox_v[0], badge_y + (badge_h - tv_h)//2 - bbox_v[1]), 
              val_text, font=font_val, fill=(255, 255, 255, 255))
    
    # 6. Bottom Information & Conditions Box (Centered Text)
    box_margin = margin + 20
    box_y = margin + 444
    box_h = h - margin - 18 - box_y
    draw.rounded_rectangle((box_margin, box_y, w - box_margin, box_y + box_h), radius=18, fill=(28, 36, 58, 230), outline=(212, 175, 55, 180), width=2)
    
    font_c1 = get_thai_font(18, bold=True)
    font_c2 = get_thai_font(15, bold=False)
    font_c3 = get_thai_font(14, bold=False)
    
    draw_centered_text(draw, "สิทธิพิเศษ: แลกรับสิทธิ์เมื่อสะสมครบ 5 แต้ม", box_y + 16, font_c1, fill=(255, 223, 128, 255), width=w)
    draw_centered_text(draw, "ใช้ลด 45% ค่าสินสอดรับเลี้ยงน้องแมว หรือช้อปสินค้าทั้งร้าน", box_y + 48, font_c2, fill=(226, 232, 240, 255), width=w)
    draw_centered_text(draw, "Purrfect Shop Cattery & Boutique • รหัสคูปอง: PAW45GRAND", box_y + 78, font_c3, fill=(148, 163, 184, 255), width=w)
    
    return base

# =========================================================================
# 2. MAJOR REWARD 45% OFF (1200x780 px - Card Message)
# =========================================================================
def build_major_45pct_card_1200():
    w, h = 1200, 780
    base = Image.new("RGBA", (w, h), (14, 18, 30, 255))
    draw = ImageDraw.Draw(base)
    
    for r in range(600, 0, -25):
        alpha = int(45 * (1 - r / 600))
        draw.ellipse((w//2 - r, h//2 - r, w//2 + r, h//2 + r), fill=(212, 175, 55, alpha))
        
    margin = 30
    card_rect = (margin, margin, w - margin, h - margin)
    inner_card = Image.new("RGBA", (w, h), (0,0,0,0))
    ic_draw = ImageDraw.Draw(inner_card)
    ic_draw.rounded_rectangle(card_rect, radius=36, fill=(22, 28, 44, 250), outline=(212, 175, 55, 255), width=6)
    
    inner_margin = margin + 12
    ic_draw.rounded_rectangle((inner_margin, inner_margin, w - inner_margin, h - inner_margin), 
                             radius=26, outline=(255, 223, 128, 120), width=2)
    
    base = Image.alpha_composite(base, inner_card)
    draw = ImageDraw.Draw(base)
    
    # VIP Badge
    pill_rect = (margin + 40, margin + 40, margin + 350, margin + 95)
    draw.rounded_rectangle(pill_rect, radius=26, fill=(212, 175, 55, 255))
    draw_trophy(draw, margin + 75, margin + 68, size=28, color=(14, 18, 30))
    draw.text((margin + 105, margin + 52), "รางวัลหลัก • 5 แต้มสะสม", font=get_thai_font(20, bold=True), fill=(14, 18, 30, 255))
    
    draw.text((margin + 40, margin + 120), "GRAND REWARD", font=get_thai_font(54, bold=True), fill=(255, 223, 128, 255))
    draw.text((margin + 40, margin + 185), "DISCOUNT 45% OFF", font=get_thai_font(54, bold=True), fill=(255, 255, 255, 255))
    draw.text((margin + 45, margin + 260), "ลดพิเศษ 45% ค่าสินสอดรับเลี้ยงน้องแมว หรือสินค้าทั้งร้าน", font=get_thai_font(24, bold=True), fill=(203, 213, 225, 255))
    
    # Value Badge (Icon + Text Group Centered inside badge)
    font_val = get_thai_font(28, bold=True)
    val_text = "ลดทันที 45% ทุกยอด"
    bbox_v = font_val.getbbox(val_text)
    tv_w = bbox_v[2] - bbox_v[0]
    tv_h = bbox_v[3] - bbox_v[1]
    
    icon_sz = 32
    gap = 14
    tot = icon_sz + gap + tv_w
    bw = tot + 64
    bh = 70
    by = margin + 320
    bx = margin + 40
    
    draw.rounded_rectangle((bx, by, bx + bw, by + bh), radius=22, fill=(220, 38, 38, 255), outline=(255, 255, 255, 255), width=3)
    cstart = bx + (bw - tot) // 2
    draw_gift_box(draw, cstart + icon_sz//2, by + bh//2, size=icon_sz, color=(255, 255, 255), ribbon_color=(220, 38, 38))
    draw.text((cstart + icon_sz + gap - bbox_v[0], by + (bh - tv_h)//2 - bbox_v[1]), val_text, font=font_val, fill=(255, 255, 255, 255))
    
    det_rect = (margin + 40, margin + 430, w - margin - 40, h - margin - 35)
    draw.rounded_rectangle(det_rect, radius=20, fill=(32, 42, 65, 240), outline=(212, 175, 55, 180), width=2)
    
    draw.text((margin + 75, margin + 455), "สิทธิ์พิเศษ: แลกรับได้ทันทีเมื่อสะสม Paw Points ครบ 5 แต้ม", font=get_thai_font(22, bold=True), fill=(255, 223, 128, 255))
    draw.text((margin + 75, margin + 495), "ใช้เป็นส่วนลด 45% ในการรับเลี้ยงน้องแมวทุกสายพันธุ์ หรือซื้อสินค้าในร้าน • แจ้งโค้ด PAW45GRAND กับแอดมิน", font=get_thai_font(18, bold=False), fill=(226, 232, 240, 255))
    draw.text((margin + 75, margin + 535), "Purrfect Shop Cattery & Boutique • สิทธิพิเศษสมาชิก VIP สูงสุด", font=get_thai_font(16, bold=False), fill=(148, 163, 184, 255))
    
    cat1 = create_circular_cat_thumbnail('cat_americanshorthair.jpg', diameter=310, border_color=(255, 215, 0, 255), border_width=10)
    cat2 = create_circular_cat_thumbnail('cat_scottish.jpg', diameter=210, border_color=(255, 107, 107, 255), border_width=8)
    
    base.paste(cat2, (w - margin - 460, margin + 110), mask=cat2)
    base.paste(cat1, (w - margin - 330, margin + 60), mask=cat1)
    
    return base

# =========================================================================
# 3. MAJOR REWARD STARTER KIT (640x640 px - Royal Emerald & Gold Theme)
# =========================================================================
def build_major_reward_coupon_640():
    w, h = 640, 640
    # Deep Emerald Velvet Base
    base = Image.new("RGBA", (w, h), (12, 30, 22, 255))
    draw = ImageDraw.Draw(base)
    
    # Emerald & Gold Radial Glow
    for r in range(420, 0, -18):
        alpha = int(50 * (1 - r / 420))
        draw.ellipse((w//2 - r, h//2 - r, w//2 + r, h//2 + r), fill=(212, 175, 55, alpha))
        
    for r in range(260, 0, -15):
        alpha = int(45 * (1 - r / 260))
        draw.ellipse((w//2 - r, 310 - r, w//2 + r, 310 + r), fill=(34, 197, 94, alpha))
        
    margin = 20
    card_rect = (margin, margin, w - margin, h - margin)
    inner_card = Image.new("RGBA", (w, h), (0,0,0,0))
    ic_draw = ImageDraw.Draw(inner_card)
    ic_draw.rounded_rectangle(card_rect, radius=28, fill=(16, 40, 30, 250), outline=(212, 175, 55, 255), width=5)
    
    inner_margin = margin + 10
    ic_draw.rounded_rectangle((inner_margin, inner_margin, w - inner_margin, h - inner_margin), 
                             radius=20, outline=(255, 223, 128, 140), width=2)
    
    base = Image.alpha_composite(base, inner_card)
    draw = ImageDraw.Draw(base)
    
    # Corner Sparkles
    sparkle_color = (255, 223, 128, 200)
    draw_sparkle(draw, 50, 50, radius=12, color=sparkle_color)
    draw_sparkle(draw, w - 50, 50, radius=12, color=sparkle_color)
    draw_sparkle(draw, 50, h - 50, radius=10, color=sparkle_color)
    draw_sparkle(draw, w - 50, h - 50, radius=10, color=sparkle_color)
    
    # Pill
    font_badge = get_thai_font(17, bold=True)
    badge_text = "รางวัลหลัก • 5 PAW POINTS"
    bbox_b = font_badge.getbbox(badge_text)
    tb_w = bbox_b[2] - bbox_b[0]
    tb_h = bbox_b[3] - bbox_b[1]
    
    icon_sz = 22
    gap_b = 10
    total_b_content = icon_sz + gap_b + tb_w
    pill_w = total_b_content + 48
    pill_h = 40
    pill_y = margin + 20
    pill_x = (w - pill_w) // 2
    
    draw.rounded_rectangle((pill_x, pill_y, pill_x + pill_w, pill_y + pill_h), radius=20, fill=(212, 175, 55, 255))
    content_start_x = pill_x + (pill_w - total_b_content) // 2
    draw_trophy(draw, content_start_x + icon_sz//2, pill_y + pill_h//2, size=icon_sz, color=(12, 30, 22))
    draw.text((content_start_x + icon_sz + gap_b - bbox_b[0], pill_y + (pill_h - tb_h)//2 - bbox_b[1]), 
              badge_text, font=font_badge, fill=(12, 30, 22, 255))
    
    draw_centered_text(draw, "FREE STARTER KIT", margin + 70, get_thai_font(40, bold=True), fill=(255, 223, 128, 255), width=w)
    draw_centered_text(draw, "+ ขนมแมวเลียพรีเมียม 1 เซ็ต", margin + 120, get_thai_font(21, bold=True), fill=(241, 245, 249, 255), width=w)
    
    cat_diameter = 190
    cat_thumb = create_circular_cat_thumbnail('cat_british.jpg', diameter=cat_diameter, border_color=(255, 215, 0, 255), border_width=8)
    cat_x = (w - cat_diameter) // 2
    cat_y = margin + 158
    base.paste(cat_thumb, (cat_x, cat_y), mask=cat_thumb)
    
    draw = ImageDraw.Draw(base)
    draw_sparkle(draw, cat_x - 18, cat_y + 45, radius=14, color=(255, 215, 0, 255))
    draw_sparkle(draw, cat_x + cat_diameter + 18, cat_y + 45, radius=14, color=(255, 215, 0, 255))
    
    # Value Badge (Group Centered)
    font_val = get_thai_font(27, bold=True)
    val_text = "มูลค่าฟรี 350 บาท"
    bbox_v = font_val.getbbox(val_text)
    tv_w = bbox_v[2] - bbox_v[0]
    tv_h = bbox_v[3] - bbox_v[1]
    
    icon_v_sz = 26
    gap_v = 12
    total_v_content = icon_v_sz + gap_v + tv_w
    badge_w = total_v_content + 56
    badge_h = 56
    badge_y = margin + 368
    badge_x = (w - badge_w) // 2
    
    draw.rounded_rectangle((badge_x, badge_y, badge_x + badge_w, badge_y + badge_h), radius=28, fill=(212, 175, 55, 255), outline=(255, 255, 255, 255), width=3)
    v_content_start_x = badge_x + (badge_w - total_v_content) // 2
    draw_gift_box(draw, v_content_start_x + icon_v_sz//2, badge_y + badge_h//2, size=icon_v_sz, color=(12, 30, 22), ribbon_color=(212, 175, 55))
    draw.text((v_content_start_x + icon_v_sz + gap_v - bbox_v[0], badge_y + (badge_h - tv_h)//2 - bbox_v[1]), 
              val_text, font=font_val, fill=(12, 30, 22, 255))
    
    box_margin = margin + 20
    box_y = margin + 444
    box_h = h - margin - 18 - box_y
    draw.rounded_rectangle((box_margin, box_y, w - box_margin, box_y + box_h), radius=18, fill=(12, 32, 24, 230), outline=(212, 175, 55, 180), width=2)
    
    draw_centered_text(draw, "สิทธิพิเศษ: แลกรับสิทธิ์เมื่อสะสมครบ 5 แต้ม", box_y + 16, get_thai_font(18, bold=True), fill=(255, 223, 128, 255), width=w)
    draw_centered_text(draw, "แสดงคูปองนี้กับแอดมินเพื่อรับชุดของขวัญ หรือใช้ลด 350.-", box_y + 48, get_thai_font(15, bold=False), fill=(226, 232, 240, 255), width=w)
    draw_centered_text(draw, "Purrfect Shop Cattery & Boutique • ใช้ได้ถึง 31 ธ.ค. 2026", box_y + 78, get_thai_font(14, bold=False), fill=(148, 163, 184, 255), width=w)
    
    return base

# =========================================================================
# 4. MINOR REWARD 10% (640x640 px - Group Centered)
# =========================================================================
def build_minor_reward_coupon_640():
    w, h = 640, 640
    base = Image.new("RGBA", (w, h), (36, 18, 28, 255))
    draw = ImageDraw.Draw(base)
    
    for r in range(400, 0, -20):
        alpha = int(45 * (1 - r / 400))
        draw.ellipse((w//2 - r, h//2 - r, w//2 + r, h//2 + r), fill=(255, 140, 160, alpha))
        
    margin = 20
    card_rect = (margin, margin, w - margin, h - margin)
    inner_card = Image.new("RGBA", (w, h), (0,0,0,0))
    ic_draw = ImageDraw.Draw(inner_card)
    ic_draw.rounded_rectangle(card_rect, radius=28, fill=(48, 24, 38, 250), outline=(255, 180, 190, 255), width=5)
    
    inner_margin = margin + 10
    ic_draw.rounded_rectangle((inner_margin, inner_margin, w - inner_margin, h - inner_margin), 
                             radius=20, outline=(255, 220, 180, 120), width=2)
    
    base = Image.alpha_composite(base, inner_card)
    draw = ImageDraw.Draw(base)
    
    # Pill
    font_badge = get_thai_font(17, bold=True)
    badge_text = "รางวัลเสริม • 3 PAW POINTS"
    bbox_b = font_badge.getbbox(badge_text)
    tb_w = bbox_b[2] - bbox_b[0]
    tb_h = bbox_b[3] - bbox_b[1]
    
    icon_sz = 22
    gap_b = 10
    total_b_content = icon_sz + gap_b + tb_w
    pill_w = total_b_content + 48
    pill_h = 40
    pill_y = margin + 20
    pill_x = (w - pill_w) // 2
    
    draw.rounded_rectangle((pill_x, pill_y, pill_x + pill_w, pill_y + pill_h), radius=20, fill=(255, 107, 129, 255))
    content_start_x = pill_x + (pill_w - total_b_content) // 2
    draw_gift_box(draw, content_start_x + icon_sz//2, pill_y + pill_h//2, size=icon_sz, color=(255, 255, 255), ribbon_color=(255, 107, 129))
    draw.text((content_start_x + icon_sz + gap_b - bbox_b[0], pill_y + (pill_h - tb_h)//2 - bbox_b[1]), 
              badge_text, font=font_badge, fill=(255, 255, 255, 255))
    
    draw_centered_text(draw, "DISCOUNT 10% OFF", margin + 70, get_thai_font(40, bold=True), fill=(255, 215, 225, 255), width=w)
    draw_centered_text(draw, "ส่วนลดค่าอาหาร ทรายแมว & อุปกรณ์", margin + 120, get_thai_font(21, bold=True), fill=(248, 250, 252, 255), width=w)
    
    cat_diameter = 190
    cat_thumb = create_circular_cat_thumbnail('cat_ragdoll.jpg', diameter=cat_diameter, border_color=(255, 180, 190, 255), border_width=8)
    cat_x = (w - cat_diameter) // 2
    cat_y = margin + 158
    base.paste(cat_thumb, (cat_x, cat_y), mask=cat_thumb)
    
    draw = ImageDraw.Draw(base)
    
    # Value Badge (Group Centered)
    font_val = get_thai_font(27, bold=True)
    val_text = "ลดทันที 10% ทุกยอด"
    bbox_v = font_val.getbbox(val_text)
    tv_w = bbox_v[2] - bbox_v[0]
    tv_h = bbox_v[3] - bbox_v[1]
    
    icon_v_sz = 26
    gap_v = 12
    total_v_content = icon_v_sz + gap_v + tv_w
    badge_w = total_v_content + 56
    badge_h = 56
    badge_y = margin + 368
    badge_x = (w - badge_w) // 2
    
    draw.rounded_rectangle((badge_x, badge_y, badge_x + badge_w, badge_y + badge_h), radius=28, fill=(212, 175, 55, 255), outline=(255, 255, 255, 255), width=3)
    v_content_start_x = badge_x + (badge_w - total_v_content) // 2
    draw_gift_box(draw, v_content_start_x + icon_v_sz//2, badge_y + badge_h//2, size=icon_v_sz, color=(14, 18, 30), ribbon_color=(212, 175, 55))
    draw.text((v_content_start_x + icon_v_sz + gap_v - bbox_v[0], badge_y + (badge_h - tv_h)//2 - bbox_v[1]), 
              val_text, font=font_val, fill=(14, 18, 30, 255))
    
    box_margin = margin + 20
    box_y = margin + 444
    box_h = h - margin - 18 - box_y
    draw.rounded_rectangle((box_margin, box_y, w - box_margin, box_y + box_h), radius=18, fill=(65, 32, 46, 230), outline=(255, 180, 190, 180), width=2)
    
    draw_centered_text(draw, "สิทธิพิเศษ: แลกรับสิทธิ์เมื่อสะสมครบ 3 แต้ม", box_y + 16, get_thai_font(18, bold=True), fill=(255, 215, 225, 255), width=w)
    draw_centered_text(draw, "ใช้เป็นส่วนลดซื้ออาหาร ขนม ทรายแมว หรือบริการกรูมมิ่ง", box_y + 48, get_thai_font(15, bold=False), fill=(235, 215, 225, 255), width=w)
    draw_centered_text(draw, "Purrfect Shop Cattery & Boutique • โค้ด: PAW10REWARD", box_y + 78, get_thai_font(14, bold=False), fill=(203, 160, 175, 255), width=w)
    
    return base

# Generate and Save all Images
img_major_45_640 = build_major_45pct_coupon_640()
p1 = os.path.join(DIR_LINE_ASSETS, 'reward_voucher_major_discount45_640x640.png')
p1_assets = os.path.join(DIR_ASSETS, 'reward_voucher_major_discount45_640x640.png')
img_major_45_640.save(p1, 'PNG')
img_major_45_640.save(p1_assets, 'PNG')
print(f"[OK] Created: {p1}")

img_major_45_1200 = build_major_45pct_card_1200()
p2 = os.path.join(DIR_LINE_ASSETS, 'reward_voucher_major_discount45_1200x780.png')
p2_assets = os.path.join(DIR_ASSETS, 'reward_voucher_major_discount45_1200x780.png')
img_major_45_1200.save(p2, 'PNG')
img_major_45_1200.save(p2_assets, 'PNG')
print(f"[OK] Created: {p2}")

img_major_640 = build_major_reward_coupon_640()
p3 = os.path.join(DIR_LINE_ASSETS, 'reward_voucher_major_starter_kit_640x640.png')
p3_assets = os.path.join(DIR_ASSETS, 'reward_voucher_major_starter_kit_640x640.png')
img_major_640.save(p3, 'PNG')
img_major_640.save(p3_assets, 'PNG')
print(f"[OK] Created: {p3}")

img_minor_640 = build_minor_reward_coupon_640()
p4 = os.path.join(DIR_LINE_ASSETS, 'reward_voucher_minor_discount10_640x640.png')
p4_assets = os.path.join(DIR_ASSETS, 'reward_voucher_minor_discount10_640x640.png')
img_minor_640.save(p4, 'PNG')
img_minor_640.save(p4_assets, 'PNG')
print(f"[OK] Created: {p4}")
