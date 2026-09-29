"""
generate_line_reward_cards.py
Generates ultra-luxury, high-resolution 1920x960 px LINE Reward Card cover images
with custom vector paw stamps, crowns, gifts, and crisp typography.
"""

import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter, ImageEnhance

DIR_CURRENT = os.path.dirname(os.path.abspath(__file__))
DIR_ASSETS = os.path.join(DIR_CURRENT, 'assets', 'images')
DIR_LINE_ASSETS = os.path.join(DIR_CURRENT, 'LINE_ASSETS', '5_REWARD_CARD')

os.makedirs(DIR_LINE_ASSETS, exist_ok=True)

WIDTH = 1920
HEIGHT = 960

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

def draw_paw_print(draw, cx, cy, radius, color):
    main_w = int(radius * 1.1)
    main_h = int(radius * 0.9)
    draw.ellipse((cx - main_w, cy - main_h//2 + int(radius*0.2), cx + main_w, cy + main_h//2 + int(radius*0.9)), fill=color)
    
    toe_r = int(radius * 0.38)
    angles = [-60, -20, 20, 60]
    distances = [radius * 1.05, radius * 1.25, radius * 1.25, radius * 1.05]
    
    for angle, dist in zip(angles, distances):
        rad = math.radians(angle - 90)
        tx = cx + int(dist * math.cos(rad))
        ty = cy + int(dist * math.sin(rad))
        draw.ellipse((tx - toe_r, ty - toe_r, tx + toe_r, ty + toe_r), fill=color)

def draw_crown(draw, cx, cy, size, color):
    w = size
    h = int(size * 0.7)
    points = [
        (cx - w//2, cy + h//2),
        (cx - w//2, cy - h//4),
        (cx - w//3, cy),
        (cx, cy - h//2),
        (cx + w//3, cy),
        (cx + w//2, cy - h//4),
        (cx + w//2, cy + h//2),
    ]
    draw.polygon(points, fill=color)
    jewel_r = max(2, int(size * 0.08))
    draw.ellipse((cx - w//2 - jewel_r, cy - h//4 - jewel_r*2, cx - w//2 + jewel_r, cy - h//4), fill=color)
    draw.ellipse((cx - jewel_r, cy - h//2 - jewel_r*2, cx + jewel_r, cy - h//2), fill=color)
    draw.ellipse((cx + w//2 - jewel_r, cy - h//4 - jewel_r*2, cx + w//2 + jewel_r, cy - h//4), fill=color)

def draw_gift_box(draw, cx, cy, size, color, ribbon_color=(255, 255, 255)):
    w = size
    h = size
    box_top = cy - h//2 + int(size * 0.25)
    box_bottom = cy + h//2
    draw.rounded_rectangle((cx - w//2, box_top, cx + w//2, box_bottom), radius=8, fill=color)
    lid_top = cy - h//2 + int(size * 0.1)
    draw.rounded_rectangle((cx - w//2 - 6, lid_top, cx + w//2 + 6, box_top + 4), radius=6, fill=color)
    ribbon_w = max(4, int(size * 0.16))
    draw.rectangle((cx - ribbon_w//2, lid_top, cx + ribbon_w//2, box_bottom), fill=ribbon_color)
    draw.rectangle((cx - w//2, box_top + int(h*0.3), cx + w//2, box_top + int(h*0.3) + ribbon_w), fill=ribbon_color)
    bow_r = int(size * 0.2)
    draw.ellipse((cx - bow_r - 2, lid_top - bow_r//2, cx - 2, lid_top + bow_r//2), outline=ribbon_color, width=4)
    draw.ellipse((cx + 2, lid_top - bow_r//2, cx + bow_r + 2, lid_top + bow_r//2), outline=ribbon_color, width=4)

def draw_trophy(draw, cx, cy, size, color):
    w = int(size * 0.8)
    h = size
    cup_top = cy - h//2
    cup_bottom = cy
    draw.pieslice((cx - w//2, cup_top - w//4, cx + w//2, cup_bottom + w//4), start=0, end=180, fill=color)
    draw.rectangle((cx - w//2, cup_top, cx + w//2, cup_top + w//3), fill=color)
    draw.arc((cx - w//2 - int(w*0.3), cup_top, cx - w//2 + int(w*0.1), cup_top + int(h*0.4)), start=90, end=270, fill=color, width=6)
    draw.arc((cx + w//2 - int(w*0.1), cup_top, cx + w//2 + int(w*0.3), cup_top + int(h*0.4)), start=270, end=90, fill=color, width=6)
    draw.rectangle((cx - 6, cup_bottom, cx + 6, cy + int(h*0.3)), fill=color)
    draw.rounded_rectangle((cx - w//3, cy + int(h*0.3), cx + w//3, cy + h//2), radius=4, fill=color)

def draw_star(draw, cx, cy, size, color):
    points = []
    outer_r = size
    inner_r = size * 0.45
    for i in range(10):
        angle = math.radians(i * 36 - 90)
        r = outer_r if i % 2 == 0 else inner_r
        points.append((cx + int(r * math.cos(angle)), cy + int(r * math.sin(angle))))
    draw.polygon(points, fill=color)

def create_circular_cat_thumbnail(image_name, diameter, border_color=(255, 215, 0), border_width=10):
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

def build_luxury_gold_card():
    base = Image.new("RGBA", (WIDTH, HEIGHT), (14, 18, 30, 255))
    draw = ImageDraw.Draw(base)
    
    # Glows
    for r in range(800, 0, -30):
        alpha = int(45 * (1 - r / 800))
        draw.ellipse((WIDTH//2 - r, HEIGHT//2 - r, WIDTH//2 + r, HEIGHT//2 + r), fill=(212, 175, 55, alpha))
        
    for r in range(550, 0, -25):
        alpha = int(35 * (1 - r / 550))
        draw.ellipse((WIDTH - 250 - r, -150 - r, WIDTH - 250 + r, -150 + r), fill=(255, 107, 107, alpha))
        
    margin = 35
    card_rect = (margin, margin, WIDTH - margin, HEIGHT - margin)
    
    inner_card = Image.new("RGBA", (WIDTH, HEIGHT), (0,0,0,0))
    ic_draw = ImageDraw.Draw(inner_card)
    ic_draw.rounded_rectangle(card_rect, radius=42, fill=(22, 28, 44, 248), outline=(212, 175, 55, 255), width=6)
    
    inner_margin = margin + 14
    ic_draw.rounded_rectangle((inner_margin, inner_margin, WIDTH - inner_margin, HEIGHT - inner_margin), 
                             radius=32, outline=(255, 223, 128, 140), width=2)
    
    base = Image.alpha_composite(base, inner_card)
    draw = ImageDraw.Draw(base)
    
    # VIP Badge Top-Left
    pill_rect = (margin + 45, margin + 45, margin + 400, margin + 105)
    draw.rounded_rectangle(pill_rect, radius=30, fill=(212, 175, 55, 255))
    draw_crown(draw, margin + 85, margin + 75, size=30, color=(14, 18, 30))
    draw.text((margin + 120, margin + 58), "ROYAL CATTERY VIP", font=get_thai_font(24, bold=True), fill=(14, 18, 30, 255))
    
    # Title & Subtitle
    draw.text((margin + 45, margin + 125), "PURRFECT REWARD CLUB", font=get_thai_font(52, bold=True), fill=(255, 223, 128, 255))
    draw.text((margin + 48, margin + 195), "Paw Points Loyalty Card • บัตรสะสมแต้มพรีเมียมสำหรับครอบครัวทาสแมว", font=get_thai_font(25, bold=True), fill=(226, 232, 240, 255))
    
    # Smart Chip Top Right
    chip_x = WIDTH - margin - 220
    chip_y = margin + 50
    draw.rounded_rectangle((chip_x, chip_y, chip_x + 130, chip_y + 90), radius=16, fill=(230, 185, 75, 255), outline=(255, 255, 255, 200), width=3)
    draw.line([(chip_x + 65, chip_y), (chip_x + 65, chip_y + 90)], fill=(150, 110, 25, 255), width=3)
    draw.line([(chip_x, chip_y + 45), (chip_x + 130, chip_y + 45)], fill=(150, 110, 25, 255), width=3)
    draw.rounded_rectangle((chip_x + 35, chip_y + 25, chip_x + 95, chip_y + 65), radius=8, outline=(150, 110, 25, 255), width=2)
    
    draw.text((chip_x - 240, chip_y + 25), "CARD NO. PURR-8899", font=get_thai_font(24, bold=True), fill=(212, 175, 55, 255))
    
    # Cat Portraits Composite (Right side)
    cat3 = create_circular_cat_thumbnail('cat_persian.jpg', diameter=240, border_color=(255, 223, 128, 255), border_width=8)
    cat2 = create_circular_cat_thumbnail('cat_ragdoll.jpg', diameter=260, border_color=(255, 107, 107, 255), border_width=8)
    cat1 = create_circular_cat_thumbnail('cat_british.jpg', diameter=360, border_color=(255, 215, 0, 255), border_width=12)
    
    base.paste(cat3, (WIDTH - margin - 540, HEIGHT//2 - 130), mask=cat3)
    base.paste(cat2, (WIDTH - margin - 280, HEIGHT//2 - 70), mask=cat2)
    base.paste(cat1, (WIDTH - margin - 460, HEIGHT//2 - 190), mask=cat1)
    
    draw = ImageDraw.Draw(base)
    
    # Milestone Track Header
    track_y = 525
    track_start_x = margin + 50
    slot_width = 175
    slot_gap = 25
    
    draw_star(draw, track_start_x + 15, track_y - 65, size=18, color=(255, 215, 0))
    draw.text((track_start_x + 45, track_y - 82), "สะสมครบ 5 แต้ม แลกรับเซ็ตของขวัญทันที!", font=get_thai_font(34, bold=True), fill=(255, 255, 255, 255))
    draw.text((track_start_x + 5, track_y - 38), "ช้อปสินค้าครบทุก 500 บาท หรือรับเลี้ยงน้องแมว 1 ตัว = รับ 1 แต้ม (เปิดบัตรวันนี้ แจกฟรี 1 แต้มทันที)", font=get_thai_font(23, bold=False), fill=(203, 213, 225, 255))
    
    stamps = [
        {"num": "1", "label": "ฟรีแรกเข้า", "active": True, "reward": False},
        {"num": "2", "label": "ช้อป 500.-", "active": False, "reward": False},
        {"num": "3", "label": "ลด 10%", "active": False, "reward": True},
        {"num": "4", "label": "ช้อป 500.-", "active": False, "reward": False},
        {"num": "5", "label": "ฟรี 350.-", "active": False, "reward": True, "is_final": True},
    ]
    
    for i, s in enumerate(stamps):
        sx = track_start_x + i * (slot_width + slot_gap)
        sy = track_y
        sh = 230
        
        if s.get("is_final", False):
            fw = slot_width + 45
            draw.rounded_rectangle((sx, sy, sx + fw, sy + sh), radius=24, 
                                   fill=(212, 175, 55, 250), outline=(255, 255, 255, 255), width=4)
            draw_trophy(draw, sx + fw//2, sy + 45, size=46, color=(14, 18, 30))
            draw.text((sx + 24, sy + 76), "รางวัลใหญ่", font=get_thai_font(24, bold=True), fill=(14, 18, 30, 255))
            draw.text((sx + 15, sy + 110), "STARTER KIT", font=get_thai_font(26, bold=True), fill=(14, 18, 30, 255))
            draw.text((sx + 20, sy + 146), "+ ขนมแมวเลีย", font=get_thai_font(22, bold=True), fill=(14, 18, 30, 255))
            draw.text((sx + 20, sy + 182), "มูลค่า 350 บาท", font=get_thai_font(24, bold=True), fill=(180, 25, 25, 255))
        else:
            bg_fill = (32, 42, 65, 240) if not s["active"] else (255, 107, 107, 255)
            outline_col = (212, 175, 55, 200) if not s["reward"] else (255, 215, 0, 255)
            
            draw.rounded_rectangle((sx, sy, sx + slot_width, sy + sh), radius=22, 
                                   fill=bg_fill, outline=outline_col, width=3 if not s["reward"] else 4)
            
            if s["reward"]:
                draw_gift_box(draw, sx + slot_width//2, sy + 48, size=42, color=(255, 215, 0), ribbon_color=(255, 255, 255))
            else:
                paw_col = (255, 255, 255) if s["active"] else (212, 175, 55)
                draw_paw_print(draw, sx + slot_width//2, sy + 48, radius=20, color=paw_col)
                
            num_col = (255, 255, 255) if s["active"] else (255, 223, 128)
            draw.text((sx + 35, sy + 95), f"แต้มที่ {s['num']}", font=get_thai_font(28, bold=True), fill=num_col)
            draw.text((sx + 25, sy + 160), s["label"], font=get_thai_font(22, bold=True), fill=(241, 245, 249, 255))
            
    draw.text((margin + 50, HEIGHT - margin - 50), "WCF & CFA CERTIFIED CATTERY • รับประกันสุขภาพและสายพันธุ์แท้ 100% พร้อมบริการดูแลตลอดชีพ", font=get_thai_font(22, bold=True), fill=(148, 163, 184, 255))
    
    return base

def build_rose_boutique_card():
    base = Image.new("RGBA", (WIDTH, HEIGHT), (36, 18, 28, 255))
    draw = ImageDraw.Draw(base)
    
    for r in range(750, 0, -30):
        alpha = int(45 * (1 - r / 750))
        draw.ellipse((WIDTH//2 - r, HEIGHT//2 - r, WIDTH//2 + r, HEIGHT//2 + r), fill=(255, 140, 160, alpha))
        
    for r in range(500, 0, -25):
        alpha = int(35 * (1 - r / 500))
        draw.ellipse((200 - r, HEIGHT - 100 - r, 200 + r, HEIGHT - 100 + r), fill=(255, 200, 120, alpha))
        
    margin = 35
    card_rect = (margin, margin, WIDTH - margin, HEIGHT - margin)
    
    inner_card = Image.new("RGBA", (WIDTH, HEIGHT), (0,0,0,0))
    ic_draw = ImageDraw.Draw(inner_card)
    ic_draw.rounded_rectangle(card_rect, radius=42, fill=(48, 24, 38, 248), outline=(255, 180, 190, 255), width=6)
    
    inner_margin = margin + 14
    ic_draw.rounded_rectangle((inner_margin, inner_margin, WIDTH - inner_margin, HEIGHT - inner_margin), 
                             radius=32, outline=(255, 220, 180, 140), width=2)
    
    base = Image.alpha_composite(base, inner_card)
    draw = ImageDraw.Draw(base)
    
    # VIP Badge Top-Left
    pill_rect = (margin + 45, margin + 45, margin + 420, margin + 105)
    draw.rounded_rectangle(pill_rect, radius=30, fill=(255, 107, 129, 255))
    draw_crown(draw, margin + 85, margin + 75, size=30, color=(255, 255, 255))
    draw.text((margin + 120, margin + 58), "VIP BOUTIQUE MEMBER", font=get_thai_font(24, bold=True), fill=(255, 255, 255, 255))
    
    # Title & Subtitle
    draw.text((margin + 45, margin + 125), "PURRFECT BOUTIQUE CARD", font=get_thai_font(52, bold=True), fill=(255, 215, 225, 255))
    draw.text((margin + 48, margin + 195), "Paw Points Loyalty Card • สะสมพอยท์แลกของขวัญและบริการกรูมมิ่งฟรี", font=get_thai_font(25, bold=True), fill=(248, 250, 252, 255))
    
    # Smart Chip Top Right
    chip_x = WIDTH - margin - 220
    chip_y = margin + 50
    draw.rounded_rectangle((chip_x, chip_y, chip_x + 130, chip_y + 90), radius=16, fill=(255, 180, 190, 255), outline=(255, 255, 255, 200), width=3)
    draw.line([(chip_x + 65, chip_y), (chip_x + 65, chip_y + 90)], fill=(160, 80, 100, 255), width=3)
    draw.line([(chip_x, chip_y + 45), (chip_x + 130, chip_y + 45)], fill=(160, 80, 100, 255), width=3)
    draw.rounded_rectangle((chip_x + 35, chip_y + 25, chip_x + 95, chip_y + 65), radius=8, outline=(160, 80, 100, 255), width=2)
    
    draw.text((chip_x - 240, chip_y + 25), "CARD NO. PURR-8899", font=get_thai_font(24, bold=True), fill=(255, 180, 190, 255))
    
    # Cat Portraits
    cat3 = create_circular_cat_thumbnail('cat_scottish.jpg', diameter=240, border_color=(255, 180, 190, 255), border_width=8)
    cat2 = create_circular_cat_thumbnail('cat_munchkin.jpg', diameter=260, border_color=(255, 215, 0, 255), border_width=8)
    cat1 = create_circular_cat_thumbnail('cat_persian.jpg', diameter=360, border_color=(255, 140, 160, 255), border_width=12)
    
    base.paste(cat3, (WIDTH - margin - 540, HEIGHT//2 - 130), mask=cat3)
    base.paste(cat2, (WIDTH - margin - 280, HEIGHT//2 - 70), mask=cat2)
    base.paste(cat1, (WIDTH - margin - 460, HEIGHT//2 - 190), mask=cat1)
    
    draw = ImageDraw.Draw(base)
    
    # Milestone Track
    track_y = 525
    track_start_x = margin + 50
    slot_width = 175
    slot_gap = 25
    
    draw_star(draw, track_start_x + 15, track_y - 65, size=18, color=(255, 215, 0))
    draw.text((track_start_x + 45, track_y - 82), "สะสมครบ 5 แต้ม รับฟรี Starter Kit & ขนมแมวพรีเมียม!", font=get_thai_font(34, bold=True), fill=(255, 255, 255, 255))
    draw.text((track_start_x + 5, track_y - 38), "รับ 1 แต้มทุกยอดซื้อ 500 บาท หรือรับเลี้ยงน้องแมว 1 ตัว • แจกฟรี 1 แต้มแรกเข้าทันที", font=get_thai_font(23, bold=False), fill=(255, 225, 235, 255))
    
    stamps = [
        {"num": "1", "label": "ฟรีแรกเข้า", "active": True, "reward": False},
        {"num": "2", "label": "ช้อป 500.-", "active": False, "reward": False},
        {"num": "3", "label": "ลด 10%", "active": False, "reward": True},
        {"num": "4", "label": "ช้อป 500.-", "active": False, "reward": False},
        {"num": "5", "label": "ฟรี 350.-", "active": False, "reward": True, "is_final": True},
    ]
    
    for i, s in enumerate(stamps):
        sx = track_start_x + i * (slot_width + slot_gap)
        sy = track_y
        sh = 230
        
        if s.get("is_final", False):
            fw = slot_width + 45
            draw.rounded_rectangle((sx, sy, sx + fw, sy + sh), radius=24, 
                                   fill=(255, 107, 129, 250), outline=(255, 255, 255, 255), width=4)
            draw_trophy(draw, sx + fw//2, sy + 45, size=46, color=(255, 255, 255))
            draw.text((sx + 24, sy + 76), "รางวัลใหญ่", font=get_thai_font(24, bold=True), fill=(255, 255, 255, 255))
            draw.text((sx + 15, sy + 110), "STARTER KIT", font=get_thai_font(26, bold=True), fill=(255, 255, 255, 255))
            draw.text((sx + 20, sy + 146), "+ ขนมแมวเลีย", font=get_thai_font(22, bold=True), fill=(255, 255, 255, 255))
            draw.text((sx + 20, sy + 182), "มูลค่า 350 บาท", font=get_thai_font(24, bold=True), fill=(255, 240, 100, 255))
        else:
            bg_fill = (65, 32, 46, 240) if not s["active"] else (255, 130, 150, 255)
            outline_col = (255, 180, 190, 200) if not s["reward"] else (255, 215, 0, 255)
            
            draw.rounded_rectangle((sx, sy, sx + slot_width, sy + sh), radius=22, 
                                   fill=bg_fill, outline=outline_col, width=3 if not s["reward"] else 4)
            
            if s["reward"]:
                draw_gift_box(draw, sx + slot_width//2, sy + 48, size=42, color=(255, 215, 0), ribbon_color=(255, 255, 255))
            else:
                paw_col = (255, 255, 255) if s["active"] else (255, 180, 190)
                draw_paw_print(draw, sx + slot_width//2, sy + 48, radius=20, color=paw_col)
                
            num_col = (255, 255, 255) if s["active"] else (255, 215, 225)
            draw.text((sx + 35, sy + 95), f"แต้มที่ {s['num']}", font=get_thai_font(28, bold=True), fill=num_col)
            draw.text((sx + 25, sy + 160), s["label"], font=get_thai_font(22, bold=True), fill=(255, 235, 240, 255))
            
    draw.text((margin + 50, HEIGHT - margin - 50), "PURRFECT SHOP CATTERY • บริการด้วยหัวใจระดับพรีเมียม สุขภาพแข็งแรง 100%", font=get_thai_font(22, bold=True), fill=(210, 160, 175, 255))
    
    return base

# Generate and save
img_gold = build_luxury_gold_card()
img_gold_path1 = os.path.join(DIR_LINE_ASSETS, 'line_reward_card_gold_1920x960.png')
img_gold_path2 = os.path.join(DIR_ASSETS, 'line_reward_card_gold_1920x960.png')
img_gold.save(img_gold_path1, 'PNG')
img_gold.save(img_gold_path2, 'PNG')
print(f"[OK] Created: {img_gold_path1}")

img_rose = build_rose_boutique_card()
img_rose_path1 = os.path.join(DIR_LINE_ASSETS, 'line_reward_card_rose_1920x960.png')
img_rose_path2 = os.path.join(DIR_ASSETS, 'line_reward_card_rose_1920x960.png')
img_rose.save(img_rose_path1, 'PNG')
img_rose.save(img_rose_path2, 'PNG')
print(f"[OK] Created: {img_rose_path1}")
