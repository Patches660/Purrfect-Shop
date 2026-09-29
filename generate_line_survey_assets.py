"""
generate_line_survey_assets.py
Generates 780x510 px Luxury LINE Official Account Survey & Questionnaire Cover/Logo Images.
Includes mathematical centering, custom vector icons, ambient glows, and typography with Tahoma font.
"""

import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

DIR_CURRENT = os.path.dirname(os.path.abspath(__file__))
DIR_ASSETS = os.path.join(DIR_CURRENT, 'assets', 'images')
DIR_LINE_ASSETS = os.path.join(DIR_CURRENT, 'LINE_ASSETS', '6_SURVEY')

os.makedirs(DIR_LINE_ASSETS, exist_ok=True)

def get_thai_font(size, bold=False):
    paths = [
        "C:\\Windows\\Fonts\\tahomabd.ttf" if bold else "C:\\Windows\\Fonts\\tahoma.ttf",
        "C:\\Windows\\Fonts\\LeelaUIb.ttf" if bold else "C:\\Windows\\Fonts\\LeelawUI.ttf",
        "C:\\Windows\\Fonts\\segoeuib.ttf" if bold else "C:\\Windows\\Fonts\\segoeui.ttf",
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

def draw_star(draw, cx, cy, r_outer, r_inner, color):
    points = []
    for i in range(10):
        angle = i * math.pi / 5 - math.pi / 2
        r = r_outer if i % 2 == 0 else r_inner
        x = cx + r * math.cos(angle)
        y = cy + r * math.sin(angle)
        points.append((x, y))
    draw.polygon(points, fill=color)

def draw_survey_doc(draw, cx, cy, size, color, fill_color=(255, 255, 255)):
    w = int(size * 0.8)
    h = size
    # Sheet
    draw.rounded_rectangle((cx - w//2, cy - h//2, cx + w//2, cy + h//2), radius=3, fill=fill_color, outline=color, width=2)
    # Clip at top
    clip_w = int(w * 0.5)
    clip_h = max(3, int(h * 0.2))
    draw.rounded_rectangle((cx - clip_w//2, cy - h//2 - 2, cx + clip_w//2, cy - h//2 + clip_h), radius=2, fill=color)
    # 2 neat horizontal lines
    line_y1 = cy - 2
    line_y2 = cy + 4
    draw.line([(cx - w//2 + 4, line_y1), (cx + w//2 - 4, line_y1)], fill=color, width=1)
    draw.line([(cx - w//2 + 4, line_y2), (cx + w//2 - 4, line_y2)], fill=color, width=1)

def draw_gift_box(draw, cx, cy, size, color, ribbon_color=(255, 255, 255)):
    w = size
    h = size
    box_top = cy - h//2 + int(size * 0.25)
    box_bottom = cy + h//2
    draw.rounded_rectangle((cx - w//2, box_top, cx + w//2, box_bottom), radius=4, fill=color)
    lid_top = cy - h//2 + int(size * 0.1)
    draw.rounded_rectangle((cx - w//2 - 2, lid_top, cx + w//2 + 2, box_top + 2), radius=3, fill=color)
    ribbon_w = max(2, int(size * 0.16))
    draw.rectangle((cx - ribbon_w//2, lid_top, cx + ribbon_w//2, box_bottom), fill=ribbon_color)
    draw.rectangle((cx - w//2, box_top + int(h*0.3), cx + w//2, box_top + int(h*0.3) + ribbon_w), fill=ribbon_color)
    bow_r = int(size * 0.18)
    draw.ellipse((cx - bow_r - 2, lid_top - bow_r//2, cx - 2, lid_top + bow_r//2), outline=ribbon_color, width=2)
    draw.ellipse((cx + 2, lid_top - bow_r//2, cx + bow_r + 2, lid_top + bow_r//2), outline=ribbon_color, width=2)

def create_circular_cat_thumbnail(image_name, diameter, border_color=(255, 215, 0), border_width=6):
    cat_path = os.path.join(DIR_ASSETS, image_name)
    if not os.path.exists(cat_path):
        cat_path = os.path.join(DIR_ASSETS, 'cat_scottish.jpg')
    
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
# 1. MAIN SURVEY COVER (780x510 px - Royal Obsidian & Champagne Gold)
# =========================================================================
def build_survey_cover_vip_780x510():
    w, h = 780, 510
    base = Image.new("RGBA", (w, h), (14, 20, 32, 255))
    draw = ImageDraw.Draw(base)
    
    # Ambient Radial Lighting
    for r in range(480, 0, -20):
        alpha = int(45 * (1 - r / 480))
        draw.ellipse((w//2 - r, h//2 - r, w//2 + r, h//2 + r), fill=(212, 175, 55, alpha))
        
    for r in range(240, 0, -15):
        alpha = int(35 * (1 - r / 240))
        draw.ellipse((w//2 - r, 200 - r, w//2 + r, 200 + r), fill=(59, 130, 246, alpha))
        
    margin = 18
    card_rect = (margin, margin, w - margin, h - margin)
    inner_card = Image.new("RGBA", (w, h), (0,0,0,0))
    ic_draw = ImageDraw.Draw(inner_card)
    ic_draw.rounded_rectangle(card_rect, radius=24, fill=(20, 28, 45, 250), outline=(212, 175, 55, 255), width=5)
    
    inner_margin = margin + 8
    ic_draw.rounded_rectangle((inner_margin, inner_margin, w - inner_margin, h - inner_margin), 
                             radius=18, outline=(255, 223, 128, 120), width=2)
    
    base = Image.alpha_composite(base, inner_card)
    draw = ImageDraw.Draw(base)
    
    # Sparkles
    sparkle_color = (255, 223, 128, 220)
    draw_sparkle(draw, 45, 45, radius=12, color=sparkle_color)
    draw_sparkle(draw, w - 45, 45, radius=12, color=sparkle_color)
    draw_sparkle(draw, 45, h - 45, radius=10, color=sparkle_color)
    draw_sparkle(draw, w - 45, h - 45, radius=10, color=sparkle_color)
    
    # 1. Top Pill Badge (Group Centered)
    font_badge = get_thai_font(16, bold=True)
    badge_text = "PURRFECT FEEDBACK & SURVEY 2026"
    bbox_b = font_badge.getbbox(badge_text)
    tb_w = bbox_b[2] - bbox_b[0]
    tb_h = bbox_b[3] - bbox_b[1]
    
    icon_sz = 18
    gap_b = 10
    total_b_content = icon_sz + gap_b + tb_w
    pill_w = total_b_content + 40
    pill_h = 36
    pill_y = margin + 18
    pill_x = (w - pill_w) // 2
    
    draw.rounded_rectangle((pill_x, pill_y, pill_x + pill_w, pill_y + pill_h), radius=18, fill=(212, 175, 55, 255))
    content_start_x = pill_x + (pill_w - total_b_content) // 2
    draw_survey_doc(draw, content_start_x + icon_sz//2, pill_y + pill_h//2, size=icon_sz, color=(14, 20, 32))
    draw.text((content_start_x + icon_sz + gap_b - bbox_b[0], pill_y + (pill_h - tb_h)//2 - bbox_b[1]), 
              badge_text, font=font_badge, fill=(14, 20, 32, 255))
    
    # 2. Left Side: Cat Avatar + Stars
    cat_diameter = 160
    cat_thumb = create_circular_cat_thumbnail('cat_scottish.jpg', diameter=cat_diameter, border_color=(255, 215, 0, 255), border_width=7)
    cat_x = margin + 45
    cat_y = margin + 78
    base.paste(cat_thumb, (cat_x, cat_y), mask=cat_thumb)
    
    draw = ImageDraw.Draw(base)
    draw_sparkle(draw, cat_x - 10, cat_y + 35, radius=12, color=(255, 215, 0, 255))
    draw_sparkle(draw, cat_x + cat_diameter + 10, cat_y + 35, radius=12, color=(255, 215, 0, 255))
    
    # 5 Gold Stars
    star_y = cat_y + cat_diameter + 18
    star_center_x = cat_x + cat_diameter // 2
    for idx, sx in enumerate([-40, -20, 0, 20, 40]):
        draw_star(draw, star_center_x + sx, star_y, r_outer=9, r_inner=4, color=(255, 215, 0, 255))
        
    # 3. Right Side: Content
    text_left = cat_x + cat_diameter + 45
    
    draw.text((text_left, margin + 72), "แบบสำรวจความคิดเห็น", font=get_thai_font(32, bold=True), fill=(255, 223, 128, 255))
    draw.text((text_left, margin + 118), "ร่วมพัฒนาบริการสำหรับทาสแมว", font=get_thai_font(22, bold=True), fill=(255, 255, 255, 255))
    draw.text((text_left, margin + 158), "แชร์ความประทับใจและความต้องการ เพื่อการบริการที่ดีที่สุด", font=get_thai_font(15, bold=False), fill=(203, 213, 225, 255))
    draw.text((text_left, margin + 186), "• ใช้เวลาตอบเพียง 1-2 นาที (5 คำถามง่ายๆ)", font=get_thai_font(14, bold=False), fill=(148, 163, 184, 255))
    
    # 4. Incentive Gift Badge (Group Centered inside button)
    font_btn = get_thai_font(21, bold=True)
    btn_text = "ตอบเสร็จรับฟรี คูปองส่วนลด 100.-"
    bbox_btn = font_btn.getbbox(btn_text)
    tbtn_w = bbox_btn[2] - bbox_btn[0]
    tbtn_h = bbox_btn[3] - bbox_btn[1]
    
    btn_icon_sz = 24
    btn_gap = 12
    total_btn_c = btn_icon_sz + btn_gap + tbtn_w
    btn_w = total_btn_c + 48
    btn_h = 50
    btn_x = text_left
    btn_y = margin + 230
    
    draw.rounded_rectangle((btn_x, btn_y, btn_x + btn_w, btn_y + btn_h), radius=25, fill=(220, 38, 38, 255), outline=(255, 223, 128, 255), width=2)
    btn_cstart = btn_x + (btn_w - total_btn_c) // 2
    draw_gift_box(draw, btn_cstart + btn_icon_sz//2, btn_y + btn_h//2, size=btn_icon_sz, color=(255, 255, 255), ribbon_color=(220, 38, 38))
    draw.text((btn_cstart + btn_icon_sz + btn_gap - bbox_btn[0], btn_y + (btn_h - tbtn_h)//2 - bbox_btn[1]), 
              btn_text, font=font_btn, fill=(255, 255, 255, 255))
    
    # 5. Bottom Info Bar (Centered)
    box_margin = margin + 16
    box_y = h - margin - 85
    box_h = 70
    draw.rounded_rectangle((box_margin, box_y, w - box_margin, box_y + box_h), radius=16, fill=(28, 38, 60, 230), outline=(212, 175, 55, 160), width=2)
    
    draw_centered_text(draw, "• สิทธิพิเศษเฉพาะเพื่อน LINE OA: คูปองใช้ได้ทันทีไม่มีขั้นต่ำ •", box_y + 12, get_thai_font(16, bold=True), fill=(255, 223, 128, 255), width=w)
    draw_centered_text(draw, "Purrfect Shop Cattery & Boutique • ศูนย์รวมแมวสายพันธุ์แท้ & สินค้าพรีเมียม", box_y + 40, get_thai_font(14, bold=False), fill=(160, 174, 192, 255), width=w)
    
    return base

# =========================================================================
# 2. ADOPTION SURVEY COVER (780x510 px - Royal Emerald & Gold Theme)
# =========================================================================
def build_survey_cover_adoption_780x510():
    w, h = 780, 510
    base = Image.new("RGBA", (w, h), (12, 30, 22, 255))
    draw = ImageDraw.Draw(base)
    
    for r in range(480, 0, -20):
        alpha = int(45 * (1 - r / 480))
        draw.ellipse((w//2 - r, h//2 - r, w//2 + r, h//2 + r), fill=(212, 175, 55, alpha))
        
    for r in range(240, 0, -15):
        alpha = int(40 * (1 - r / 240))
        draw.ellipse((w//2 - r, 200 - r, w//2 + r, 200 + r), fill=(34, 197, 94, alpha))
        
    margin = 18
    card_rect = (margin, margin, w - margin, h - margin)
    inner_card = Image.new("RGBA", (w, h), (0,0,0,0))
    ic_draw = ImageDraw.Draw(inner_card)
    ic_draw.rounded_rectangle(card_rect, radius=24, fill=(16, 42, 32, 250), outline=(212, 175, 55, 255), width=5)
    
    inner_margin = margin + 8
    ic_draw.rounded_rectangle((inner_margin, inner_margin, w - inner_margin, h - inner_margin), 
                             radius=18, outline=(255, 223, 128, 120), width=2)
    
    base = Image.alpha_composite(base, inner_card)
    draw = ImageDraw.Draw(base)
    
    # Sparkles
    sparkle_color = (255, 223, 128, 220)
    draw_sparkle(draw, 45, 45, radius=12, color=sparkle_color)
    draw_sparkle(draw, w - 45, 45, radius=12, color=sparkle_color)
    draw_sparkle(draw, 45, h - 45, radius=10, color=sparkle_color)
    draw_sparkle(draw, w - 45, h - 45, radius=10, color=sparkle_color)
    
    # Top Badge
    font_badge = get_thai_font(16, bold=True)
    badge_text = "CAT ADOPTION PREFERENCE SURVEY"
    bbox_b = font_badge.getbbox(badge_text)
    tb_w = bbox_b[2] - bbox_b[0]
    tb_h = bbox_b[3] - bbox_b[1]
    
    icon_sz = 18
    gap_b = 10
    total_b_content = icon_sz + gap_b + tb_w
    pill_w = total_b_content + 40
    pill_h = 36
    pill_y = margin + 18
    pill_x = (w - pill_w) // 2
    
    draw.rounded_rectangle((pill_x, pill_y, pill_x + pill_w, pill_y + pill_h), radius=18, fill=(212, 175, 55, 255))
    content_start_x = pill_x + (pill_w - total_b_content) // 2
    draw_survey_doc(draw, content_start_x + icon_sz//2, pill_y + pill_h//2, size=icon_sz, color=(12, 30, 22))
    draw.text((content_start_x + icon_sz + gap_b - bbox_b[0], pill_y + (pill_h - tb_h)//2 - bbox_b[1]), 
              badge_text, font=font_badge, fill=(12, 30, 22, 255))
    
    # Left Side Cat Avatar (Khao Manee)
    cat_diameter = 160
    cat_thumb = create_circular_cat_thumbnail('cat_khao_manee.jpg', diameter=cat_diameter, border_color=(255, 215, 0, 255), border_width=7)
    cat_x = margin + 45
    cat_y = margin + 78
    base.paste(cat_thumb, (cat_x, cat_y), mask=cat_thumb)
    
    draw = ImageDraw.Draw(base)
    draw_sparkle(draw, cat_x - 10, cat_y + 35, radius=12, color=(255, 215, 0, 255))
    draw_sparkle(draw, cat_x + cat_diameter + 10, cat_y + 35, radius=12, color=(255, 215, 0, 255))
    
    # Stars
    star_y = cat_y + cat_diameter + 18
    star_center_x = cat_x + cat_diameter // 2
    for idx, sx in enumerate([-40, -20, 0, 20, 40]):
        draw_star(draw, star_center_x + sx, star_y, r_outer=9, r_inner=4, color=(255, 215, 0, 255))
        
    text_left = cat_x + cat_diameter + 45
    draw.text((text_left, margin + 72), "แบบสอบถามแมวในฝัน", font=get_thai_font(32, bold=True), fill=(255, 223, 128, 255))
    draw.text((text_left, margin + 118), "คุณกำลังมองหาน้องแมวสายพันธุ์ใด?", font=get_thai_font(22, bold=True), fill=(255, 255, 255, 255))
    draw.text((text_left, margin + 158), "บอกสายพันธุ์ นิสัย และงบประมาณที่คุณต้องการ", font=get_thai_font(15, bold=False), fill=(203, 213, 225, 255))
    draw.text((text_left, margin + 186), "• เราจะจัดหาน้องแมวสายพันธุ์แท้ตรงใจพร้อมใบเพ็ดฯ", font=get_thai_font(14, bold=False), fill=(167, 243, 208, 255))
    
    # Incentive Button
    font_btn = get_thai_font(21, bold=True)
    btn_text = "รับสิทธิ์จองคิวก่อนใคร + สิทธิพิเศษ VIP"
    bbox_btn = font_btn.getbbox(btn_text)
    tbtn_w = bbox_btn[2] - bbox_btn[0]
    tbtn_h = bbox_btn[3] - bbox_btn[1]
    
    btn_icon_sz = 24
    btn_gap = 12
    total_btn_c = btn_icon_sz + btn_gap + tbtn_w
    btn_w = total_btn_c + 48
    btn_h = 50
    btn_x = text_left
    btn_y = margin + 230
    
    draw.rounded_rectangle((btn_x, btn_y, btn_x + btn_w, btn_y + btn_h), radius=25, fill=(212, 175, 55, 255), outline=(255, 255, 255, 255), width=2)
    btn_cstart = btn_x + (btn_w - total_btn_c) // 2
    draw_gift_box(draw, btn_cstart + btn_icon_sz//2, btn_y + btn_h//2, size=btn_icon_sz, color=(12, 30, 22), ribbon_color=(212, 175, 55))
    draw.text((btn_cstart + btn_icon_sz + btn_gap - bbox_btn[0], btn_y + (btn_h - tbtn_h)//2 - bbox_btn[1]), 
              btn_text, font=font_btn, fill=(12, 30, 22, 255))
    
    # Bottom Info
    box_margin = margin + 16
    box_y = h - margin - 85
    box_h = 70
    draw.rounded_rectangle((box_margin, box_y, w - box_margin, box_y + box_h), radius=16, fill=(22, 54, 40, 230), outline=(212, 175, 55, 160), width=2)
    
    draw_centered_text(draw, "• แมวทุกตัวมีใบรับรองสายพันธุ์ (CFA / WCF) • วัคซีนครบ • ตรวจสุขภาพ 100% •", box_y + 12, get_thai_font(16, bold=True), fill=(255, 223, 128, 255), width=w)
    draw_centered_text(draw, "Purrfect Shop Cattery & Boutique • บริการจัดส่งทั่วประเทศอย่างปลอดภัย", box_y + 40, get_thai_font(14, bold=False), fill=(167, 243, 208, 255), width=w)
    
    return base

# =========================================================================
# 3. SQUARE LOGO / ICON BADGE FOR SURVEY (780x510 px - Centered Masterpiece)
# =========================================================================
def build_survey_cover_centered_logo_780x510():
    w, h = 780, 510
    base = Image.new("RGBA", (w, h), (18, 14, 28, 255))
    draw = ImageDraw.Draw(base)
    
    for r in range(480, 0, -20):
        alpha = int(45 * (1 - r / 480))
        draw.ellipse((w//2 - r, h//2 - r, w//2 + r, h//2 + r), fill=(212, 175, 55, alpha))
        
    for r in range(260, 0, -15):
        alpha = int(35 * (1 - r / 260))
        draw.ellipse((w//2 - r, h//2 - r, w//2 + r, h//2 + r), fill=(168, 85, 247, alpha))
        
    margin = 18
    card_rect = (margin, margin, w - margin, h - margin)
    inner_card = Image.new("RGBA", (w, h), (0,0,0,0))
    ic_draw = ImageDraw.Draw(inner_card)
    ic_draw.rounded_rectangle(card_rect, radius=24, fill=(26, 20, 42, 250), outline=(212, 175, 55, 255), width=5)
    
    inner_margin = margin + 8
    ic_draw.rounded_rectangle((inner_margin, inner_margin, w - inner_margin, h - inner_margin), 
                             radius=18, outline=(255, 223, 128, 120), width=2)
    
    base = Image.alpha_composite(base, inner_card)
    draw = ImageDraw.Draw(base)
    
    # Sparkles
    sparkle_color = (255, 223, 128, 220)
    draw_sparkle(draw, 45, 45, radius=12, color=sparkle_color)
    draw_sparkle(draw, w - 45, 45, radius=12, color=sparkle_color)
    draw_sparkle(draw, 45, h - 45, radius=10, color=sparkle_color)
    draw_sparkle(draw, w - 45, h - 45, radius=10, color=sparkle_color)
    
    # Centered VIP Pill
    font_badge = get_thai_font(16, bold=True)
    badge_text = "OFFICIAL QUESTIONNAIRE & RESEARCH"
    bbox_b = font_badge.getbbox(badge_text)
    tb_w = bbox_b[2] - bbox_b[0]
    tb_h = bbox_b[3] - bbox_b[1]
    
    icon_sz = 18
    gap_b = 10
    total_b_content = icon_sz + gap_b + tb_w
    pill_w = total_b_content + 40
    pill_h = 36
    pill_y = margin + 20
    pill_x = (w - pill_w) // 2
    
    draw.rounded_rectangle((pill_x, pill_y, pill_x + pill_w, pill_y + pill_h), radius=18, fill=(212, 175, 55, 255))
    content_start_x = pill_x + (pill_w - total_b_content) // 2
    draw_survey_doc(draw, content_start_x + icon_sz//2, pill_y + pill_h//2, size=icon_sz, color=(18, 14, 28))
    draw.text((content_start_x + icon_sz + gap_b - bbox_b[0], pill_y + (pill_h - tb_h)//2 - bbox_b[1]), 
              badge_text, font=font_badge, fill=(18, 14, 28, 255))
    
    # Center Cat Avatar with cool glasses
    cat_diameter = 160
    cat_thumb = create_circular_cat_thumbnail('cat_americanshorthair.jpg', diameter=cat_diameter, border_color=(255, 215, 0, 255), border_width=7)
    cat_x = (w - cat_diameter) // 2
    cat_y = margin + 68
    base.paste(cat_thumb, (cat_x, cat_y), mask=cat_thumb)
    
    draw = ImageDraw.Draw(base)
    draw_sparkle(draw, cat_x - 22, cat_y + 40, radius=14, color=(255, 215, 0, 255))
    draw_sparkle(draw, cat_x + cat_diameter + 22, cat_y + 40, radius=14, color=(255, 215, 0, 255))
    
    # Center Title
    draw_centered_text(draw, "PURRFECT SURVEY", margin + 242, get_thai_font(34, bold=True), fill=(255, 223, 128, 255), width=w)
    draw_centered_text(draw, "แบบสอบถาม & แบบสำรวจความพึงพอใจลูกค้า", margin + 288, get_thai_font(21, bold=True), fill=(255, 255, 255, 255), width=w)
    
    # Center Button Badge with Gift box icon
    font_btn = get_thai_font(20, bold=True)
    btn_text = "ร่วมตอบแบบสอบถาม รับคูปองส่วนลดพิเศษ"
    bbox_btn = font_btn.getbbox(btn_text)
    tbtn_w = bbox_btn[2] - bbox_btn[0]
    tbtn_h = bbox_btn[3] - bbox_btn[1]
    
    btn_icon_sz = 22
    btn_gap = 12
    total_btn_c = btn_icon_sz + btn_gap + tbtn_w
    btn_w = total_btn_c + 48
    btn_h = 48
    btn_x = (w - btn_w) // 2
    btn_y = margin + 332
    
    draw.rounded_rectangle((btn_x, btn_y, btn_x + btn_w, btn_y + btn_h), radius=24, fill=(220, 38, 38, 255), outline=(255, 223, 128, 255), width=2)
    btn_cstart = btn_x + (btn_w - total_btn_c) // 2
    draw_gift_box(draw, btn_cstart + btn_icon_sz//2, btn_y + btn_h//2, size=btn_icon_sz, color=(255, 255, 255), ribbon_color=(220, 38, 38))
    draw.text((btn_cstart + btn_icon_sz + btn_gap - bbox_btn[0], btn_y + (btn_h - tbtn_h)//2 - bbox_btn[1]), 
              btn_text, font=font_btn, fill=(255, 255, 255, 255))
    
    # Bottom Note
    box_margin = margin + 16
    box_y = h - margin - 75
    box_h = 60
    draw.rounded_rectangle((box_margin, box_y, w - box_margin, box_y + box_h), radius=14, fill=(38, 28, 58, 230), outline=(212, 175, 55, 150), width=2)
    
    draw_centered_text(draw, "• ทุกความคิดเห็นของคุณมีคุณค่า เพื่อพัฒนาบริการที่ดีที่สุดสำหรับทาสแมว •", box_y + 12, get_thai_font(15, bold=True), fill=(255, 223, 128, 255), width=w)
    draw_centered_text(draw, "Purrfect Shop Cattery & Boutique • Official LINE OA", box_y + 34, get_thai_font(13, bold=False), fill=(196, 181, 253, 255), width=w)
    
    return base

# =========================================================================
# 4. BOUTIQUE ROSE GOLD SURVEY COVER (780x510 px - Velvet Rose & Peach Theme)
# =========================================================================
def build_survey_cover_boutique_rose_780x510():
    w, h = 780, 510
    base = Image.new("RGBA", (w, h), (42, 20, 32, 255))
    draw = ImageDraw.Draw(base)
    
    for r in range(480, 0, -20):
        alpha = int(45 * (1 - r / 480))
        draw.ellipse((w//2 - r, h//2 - r, w//2 + r, h//2 + r), fill=(255, 180, 190, alpha))
        
    for r in range(240, 0, -15):
        alpha = int(40 * (1 - r / 240))
        draw.ellipse((w//2 - r, 200 - r, w//2 + r, 200 + r), fill=(244, 114, 182, alpha))
        
    margin = 18
    card_rect = (margin, margin, w - margin, h - margin)
    inner_card = Image.new("RGBA", (w, h), (0,0,0,0))
    ic_draw = ImageDraw.Draw(inner_card)
    ic_draw.rounded_rectangle(card_rect, radius=24, fill=(54, 26, 42, 250), outline=(255, 180, 190, 255), width=5)
    
    inner_margin = margin + 8
    ic_draw.rounded_rectangle((inner_margin, inner_margin, w - inner_margin, h - inner_margin), 
                             radius=18, outline=(255, 220, 180, 120), width=2)
    
    base = Image.alpha_composite(base, inner_card)
    draw = ImageDraw.Draw(base)
    
    # Sparkles
    sparkle_color = (255, 220, 180, 220)
    draw_sparkle(draw, 45, 45, radius=12, color=sparkle_color)
    draw_sparkle(draw, w - 45, 45, radius=12, color=sparkle_color)
    draw_sparkle(draw, 45, h - 45, radius=10, color=sparkle_color)
    draw_sparkle(draw, w - 45, h - 45, radius=10, color=sparkle_color)
    
    # Top Badge
    font_badge = get_thai_font(16, bold=True)
    badge_text = "PET CARE & SHOPPING SURVEY"
    bbox_b = font_badge.getbbox(badge_text)
    tb_w = bbox_b[2] - bbox_b[0]
    tb_h = bbox_b[3] - bbox_b[1]
    
    icon_sz = 18
    gap_b = 10
    total_b_content = icon_sz + gap_b + tb_w
    pill_w = total_b_content + 40
    pill_h = 36
    pill_y = margin + 18
    pill_x = (w - pill_w) // 2
    
    draw.rounded_rectangle((pill_x, pill_y, pill_x + pill_w, pill_y + pill_h), radius=18, fill=(255, 107, 129, 255))
    content_start_x = pill_x + (pill_w - total_b_content) // 2
    draw_survey_doc(draw, content_start_x + icon_sz//2, pill_y + pill_h//2, size=icon_sz, color=(255, 255, 255))
    draw.text((content_start_x + icon_sz + gap_b - bbox_b[0], pill_y + (pill_h - tb_h)//2 - bbox_b[1]), 
              badge_text, font=font_badge, fill=(255, 255, 255, 255))
    
    # Left Side Cat Avatar (Ragdoll)
    cat_diameter = 160
    cat_thumb = create_circular_cat_thumbnail('cat_ragdoll.jpg', diameter=cat_diameter, border_color=(255, 180, 190, 255), border_width=7)
    cat_x = margin + 45
    cat_y = margin + 78
    base.paste(cat_thumb, (cat_x, cat_y), mask=cat_thumb)
    
    draw = ImageDraw.Draw(base)
    draw_sparkle(draw, cat_x - 10, cat_y + 35, radius=12, color=(255, 180, 190, 255))
    draw_sparkle(draw, cat_x + cat_diameter + 10, cat_y + 35, radius=12, color=(255, 180, 190, 255))
    
    # Stars
    star_y = cat_y + cat_diameter + 18
    star_center_x = cat_x + cat_diameter // 2
    for idx, sx in enumerate([-40, -20, 0, 20, 40]):
        draw_star(draw, star_center_x + sx, star_y, r_outer=9, r_inner=4, color=(255, 215, 0, 255))
        
    text_left = cat_x + cat_diameter + 45
    draw.text((text_left, margin + 72), "สำรวจสินค้าที่ทาสแมวชื่นชอบ", font=get_thai_font(32, bold=True), fill=(255, 215, 225, 255))
    draw.text((text_left, margin + 118), "สินค้า & บริการที่คุณอยากให้มีในร้าน", font=get_thai_font(22, bold=True), fill=(255, 255, 255, 255))
    draw.text((text_left, margin + 158), "ร่วมโหวตอาหาร ขนม ของเล่น หรืออุปกรณ์ที่คุณอยากได้", font=get_thai_font(15, bold=False), fill=(244, 215, 225, 255))
    draw.text((text_left, margin + 186), "• ตอบแบบสอบถามง่ายๆ เพื่อให้ร้านจัดเตรียมโปรโดนใจ", font=get_thai_font(14, bold=False), fill=(251, 191, 212, 255))
    
    # Incentive Button
    font_btn = get_thai_font(21, bold=True)
    btn_text = "รับแต้มสะสม + สิทธิ์ลุ้นรับขนมฟรี"
    bbox_btn = font_btn.getbbox(btn_text)
    tbtn_w = bbox_btn[2] - bbox_btn[0]
    tbtn_h = bbox_btn[3] - bbox_btn[1]
    
    btn_icon_sz = 24
    btn_gap = 12
    total_btn_c = btn_icon_sz + btn_gap + tbtn_w
    btn_w = total_btn_c + 48
    btn_h = 50
    btn_x = text_left
    btn_y = margin + 230
    
    draw.rounded_rectangle((btn_x, btn_y, btn_x + btn_w, btn_y + btn_h), radius=25, fill=(212, 175, 55, 255), outline=(255, 255, 255, 255), width=2)
    btn_cstart = btn_x + (btn_w - total_btn_c) // 2
    draw_gift_box(draw, btn_cstart + btn_icon_sz//2, btn_y + btn_h//2, size=btn_icon_sz, color=(42, 20, 32), ribbon_color=(212, 175, 55))
    draw.text((btn_cstart + btn_icon_sz + btn_gap - bbox_btn[0], btn_y + (btn_h - tbtn_h)//2 - bbox_btn[1]), 
              btn_text, font=font_btn, fill=(42, 20, 32, 255))
    
    # Bottom Info
    box_margin = margin + 16
    box_y = h - margin - 85
    box_h = 70
    draw.rounded_rectangle((box_margin, box_y, w - box_margin, box_y + box_h), radius=16, fill=(72, 36, 54, 230), outline=(255, 180, 190, 160), width=2)
    
    draw_centered_text(draw, "• Purrfect Shop คัดสรรเฉพาะสิ่งที่ดีที่สุดสำหรับน้องแมว •", box_y + 12, get_thai_font(16, bold=True), fill=(255, 215, 225, 255), width=w)
    draw_centered_text(draw, "อาหารเกรด Holistic • ทรายแมว Organic • ของเล่นนำเข้า", box_y + 40, get_thai_font(14, bold=False), fill=(244, 190, 210, 255), width=w)
    
    return base

# Generate all 4 images
img1 = build_survey_cover_vip_780x510()
p1 = os.path.join(DIR_LINE_ASSETS, 'line_survey_header_feedback_780x510.png')
p1_assets = os.path.join(DIR_ASSETS, 'line_survey_header_feedback_780x510.png')
img1.save(p1, 'PNG')
img1.save(p1_assets, 'PNG')
print(f"[OK] Created: {p1}")

img2 = build_survey_cover_adoption_780x510()
p2 = os.path.join(DIR_LINE_ASSETS, 'line_survey_header_adoption_780x510.png')
p2_assets = os.path.join(DIR_ASSETS, 'line_survey_header_adoption_780x510.png')
img2.save(p2, 'PNG')
img2.save(p2_assets, 'PNG')
print(f"[OK] Created: {p2}")

img3 = build_survey_cover_centered_logo_780x510()
p3 = os.path.join(DIR_LINE_ASSETS, 'line_survey_header_centered_logo_780x510.png')
p3_assets = os.path.join(DIR_ASSETS, 'line_survey_header_centered_logo_780x510.png')
img3.save(p3, 'PNG')
img3.save(p3_assets, 'PNG')
print(f"[OK] Created: {p3}")

img4 = build_survey_cover_boutique_rose_780x510()
p4 = os.path.join(DIR_LINE_ASSETS, 'line_survey_header_boutique_rose_780x510.png')
p4_assets = os.path.join(DIR_ASSETS, 'line_survey_header_boutique_rose_780x510.png')
img4.save(p4, 'PNG')
img4.save(p4_assets, 'PNG')
print(f"[OK] Created: {p4}")

# Default main cover
img1.save(os.path.join(DIR_LINE_ASSETS, 'line_survey_cover_780x510.png'), 'PNG')
img1.save(os.path.join(DIR_ASSETS, 'line_survey_cover_780x510.png'), 'PNG')
print(f"[OK] Created: {os.path.join(DIR_LINE_ASSETS, 'line_survey_cover_780x510.png')}")
