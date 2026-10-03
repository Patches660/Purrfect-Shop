import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

base_dir = os.path.dirname(os.path.abspath(__file__))
line_dir = os.path.join(base_dir, "LINE_ASSETS")
src_card_dir = os.path.join(line_dir, "3_CARD_MESSAGE")
assets_img_dir = os.path.join(base_dir, "assets", "images")

# Target folders
out_folder_1 = os.path.join(line_dir, "3_CARD_MESSAGE_RATIO_1.11_1")
out_folder_2 = os.path.join(src_card_dir, "RATIO_1.11_1")
os.makedirs(out_folder_1, exist_ok=True)
os.makedirs(out_folder_2, exist_ok=True)

TARGET_W = 1110
TARGET_H = 1000  # 1110 / 1000 = 1.11 : 1

# Fonts
font_bold_path = "C:/Windows/Fonts/LeelaUIb.ttf" if os.path.exists("C:/Windows/Fonts/LeelaUIb.ttf") else "C:/Windows/Fonts/tahomabd.ttf"
font_reg_path = "C:/Windows/Fonts/LeelawUI.ttf" if os.path.exists("C:/Windows/Fonts/LeelawUI.ttf") else "C:/Windows/Fonts/tahoma.ttf"

def get_font(size, bold=False):
    p = font_bold_path if bold else font_reg_path
    try:
        return ImageFont.truetype(p, int(size))
    except:
        return ImageFont.load_default()

def create_vertical_grad(w, h, c1, c2):
    img = Image.new("RGBA", (w, h), (255, 255, 255, 255))
    draw = ImageDraw.Draw(img)
    for y in range(h):
        t = y / max(1, h - 1)
        r = int(c1[0] + t * (c2[0] - c1[0]))
        g = int(c1[1] + t * (c2[1] - c1[1]))
        b = int(c1[2] + t * (c2[2] - c1[2]))
        draw.line([(0, y), (w, y)], fill=(r, g, b, 255))
    return img

def draw_paw(draw, cx, cy, size, fill=(255, 107, 74, 180)):
    pad_w = int(size * 0.5)
    pad_h = int(size * 0.4)
    draw.ellipse([cx - pad_w//2, cy - pad_h//2, cx + pad_w//2, cy + pad_h//2], fill=fill)
    toe_r = int(size * 0.16)
    draw.ellipse([cx - int(size * 0.3) - toe_r, cy - int(size * 0.3) - toe_r, cx - int(size * 0.3) + toe_r, cy - int(size * 0.3) + toe_r], fill=fill)
    draw.ellipse([cx - int(size * 0.1) - toe_r, cy - int(size * 0.42) - toe_r, cx - int(size * 0.1) + toe_r, cy - int(size * 0.42) + toe_r], fill=fill)
    draw.ellipse([cx + int(size * 0.12) - toe_r, cy - int(size * 0.42) - toe_r, cx + int(size * 0.12) + toe_r, cy - int(size * 0.42) + toe_r], fill=fill)
    draw.ellipse([cx + int(size * 0.32) - toe_r, cy - int(size * 0.3) - toe_r, cx + int(size * 0.32) + toe_r, cy - int(size * 0.3) + toe_r], fill=fill)

def draw_sparkle(draw, cx, cy, r=12, fill=(245, 158, 11)):
    draw.polygon([(cx, cy - r), (cx + 2, cy), (cx, cy + r), (cx - 2, cy)], fill=fill)
    draw.polygon([(cx - r, cy), (cx, cy + 2), (cx + r, cy), (cx, cy - 2)], fill=fill)
    d = int(r * 0.45)
    draw.polygon([(cx - d, cy - d), (cx, cy), (cx + d, cy + d), (cx, cy)], fill=fill)
    draw.polygon([(cx + d, cy - d), (cx, cy), (cx - d, cy + d), (cx, cy)], fill=fill)

def draw_checkmark(draw, cx, cy, size=14, color=(16, 185, 129)):
    draw.line([(cx - size//2, cy), (cx - size//6, cy + size//2), (cx + size//2, cy - size//2)], fill=color, width=3)

# All 10 Cards configuration
all_card_items = [
    # 1. BRITISH SHORTHAIR
    {
        'output_filename': 'card_1_british_1110x1000_ratio1.11.png',
        'cat_image': 'cat_british.jpg',
        'tag_text': 'ยอดนิยมอันดับ 1',
        'photo_info': 'เพศผู้ | อายุ 2.5 เดือน',
        'color': (255, 107, 74),        # Coral Orange
        'bg_c1': (255, 247, 245),
        'bg_c2': (255, 237, 230),
        'badge_bg': (255, 237, 230),
        'badge_border': (255, 107, 74),
        'badge_txt': (225, 65, 38),
        'title': 'น้องบริติช บลู (British Shorthair)',
        'description': 'เจ้าก้อนกลมขนแน่นนุ่มฟู หน้ากลมแป้น อารมณ์ดี นิสัยสุภาพ เรียบร้อย',
        'highlights': [
            ('สายพันธุ์แท้ 100%', 'โครงสร้างสมบูรณ์ ขนแน่นสั้นนุ่มดั่งกำมะหยี่'),
            ('ฉลาด เลี้ยงง่าย อารมณ์ดี', 'เป็นมิตร ไม่ส่งเสียงรบกวน เหมาะกับครอบครัว'),
            ('ตรวจสุขภาพ + วัคซีนครบ', 'ใบเพ็ดดีกรีสมาคมสากล พร้อมรับประกันสุขภาพ')
        ],
        'bullet_border': (254, 205, 190),
        'price_orig': '42,000.-',
        'price_special': '35,000',
        'unit': 'บาท',
        'guarantee': 'ฟรี! Starter Kit 11 รายการ มูลค่า 4,500.-',
        'btn_text': 'จองน้องบริติช >>'
    },

    # 2. SCOTTISH FOLD
    {
        'output_filename': 'card_2_scottish_1110x1000_ratio1.11.png',
        'cat_image': 'cat_scottish.jpg',
        'tag_text': 'เลี้ยงง่าย ขี้อ้อน',
        'photo_info': 'เพศเมีย | อายุ 2 เดือน',
        'color': (217, 119, 6),         # Amber Gold
        'bg_c1': (255, 251, 235),
        'bg_c2': (254, 243, 199),
        'badge_bg': (254, 243, 199),
        'badge_border': (245, 158, 11),
        'badge_txt': (180, 83, 9),
        'title': 'น้องสกอตติช โฟลด์ (Scottish Fold)',
        'description': 'หูพับสนิท ตากลมโต ขี้อ้อน ชอบนั่งพุงพลุ้ย ติดคนและเป็นมิตร',
        'highlights': [
            ('หูพับสนิท ตากลมโต', 'หน้ากลมแป้น น่ารักน่าเอ็นดู ขนนุ่มละเอียด'),
            ('นิสัยขี้อ้อน ติดเจ้าของ', 'ชอบนอนซุกตัก ไม่ดื้อ ไม่กวนใจ เลี้ยงง่ายมาก'),
            ('ตรวจพันธุกรรมกระดูกผ่าน', 'พ่อแม่ตรวจโรคพันธุกรรมครบ ปลอดภัย 100%')
        ],
        'bullet_border': (253, 230, 138),
        'price_orig': '38,000.-',
        'price_special': '32,000',
        'unit': 'บาท',
        'guarantee': 'ฟรี! อาหารพรีเมียม 2 กก. + ตรวจสุขภาพครบ',
        'btn_text': 'จองน้องสกอตติช >>'
    },

    # 3. MAINE COON
    {
        'output_filename': 'card_3_mainecoon_1110x1000_ratio1.11.png',
        'cat_image': 'cat_mainecoon.jpg',
        'tag_text': 'เกรดพรีเมียม ใบเพ็ดครบ',
        'photo_info': 'เพศผู้ | อายุ 3 เดือน',
        'color': (147, 51, 234),        # Royal Purple
        'bg_c1': (250, 245, 255),
        'bg_c2': (243, 232, 255),
        'badge_bg': (243, 232, 255),
        'badge_border': (192, 132, 252),
        'badge_txt': (126, 34, 206),
        'title': 'น้องเมนคูน ไจแอนท์ (Maine Coon)',
        'description': 'ยักษ์ใหญ่ใจดี สายเลือดแชมป์ WCF/CFA โครงสร้างใหญ่สง่างาม',
        'highlights': [
            ('ยักษ์ใหญ่ใจดี สวยสง่า', 'โครงสร้างกระดูกใหญ่ ขนแผงคอหนาฟู หางเป็นพวง'),
            ('ฉลาด ติดคนเหมือนสุนัข', 'รักน้ำ ฝึกเดินสายจูงได้ นิสัยอ่อนโยนมาก'),
            ('ตรวจหัวใจ HCM ลบ 100%', 'ใบเพ็ด WCF/CFA นำเข้า ตรวจโรคพันธุกรรมครบ')
        ],
        'bullet_border': (233, 213, 255),
        'price_orig': '65,000.-',
        'price_special': '55,000',
        'unit': 'บาท',
        'guarantee': 'ฟรี! คอนโดแมวไซส์ใหญ่ + ประกันสุขภาพ 180 วัน',
        'btn_text': 'จองน้องเมนคูน >>'
    },

    # 4. WELCOME DEAL
    {
        'output_filename': 'card_4_welcome_deal_1110x1000_ratio1.11.png',
        'cat_image': 'coupon_10pct.png',
        'tag_text': 'สิทธิพิเศษสมาชิก',
        'photo_info': 'รหัส: CAT10OFF',
        'color': (5, 150, 105),         # Emerald Green
        'bg_c1': (236, 253, 245),
        'bg_c2': (209, 250, 229),
        'badge_bg': (209, 250, 229),
        'badge_border': (52, 211, 153),
        'badge_txt': (4, 120, 87),
        'title': 'ดีลต้อนรับสมาชิก & Starter Kit',
        'description': 'โปรโมชันพิเศษต้อนรับทาสแมวมือใหม่ แจกส่วนลดและของแถมจัดเต็ม',
        'highlights': [
            ('คูปองลด 10% ทันที', 'ใช้ได้กับสินค้าและน้องแมวทุกตัวในร้าน'),
            ('Starter Kit ฟรี 11 รายการ', 'กระเป๋า, ชามอาหาร, ทรายแมว, ของเล่น มูลค่า 4,500.-'),
            ('ส่งฟรีทั่วประเทศด้วย Pet Taxi', 'รถควบคุมอุณหภูมิ ดูแลโดยผู้เชี่ยวชาญตลอดทาง')
        ],
        'bullet_border': (167, 243, 208),
        'price_orig': 'มูลค่า 4,500.-',
        'price_special': 'ลด 10%',
        'unit': 'ฟรีของแถม',
        'guarantee': 'กดรับสิทธิ์ได้ทันที ไม่มีเงื่อนไขผูกมัด',
        'btn_text': 'กดรับคูปอง 10% >>'
    },

    # 5. PERSIAN
    {
        'output_filename': 'card_5_persian_1110x1000_ratio1.11.png',
        'cat_image': 'cat_persian.jpg',
        'tag_text': 'ราชินีขนฟู CFA',
        'photo_info': 'เพศเมีย | อายุ 3 เดือน',
        'color': (225, 29, 72),         # Rose Pink
        'bg_c1': (255, 243, 247),
        'bg_c2': (254, 235, 242),
        'badge_bg': (255, 228, 230),
        'badge_border': (251, 113, 133),
        'badge_txt': (159, 18, 57),
        'title': 'น้องปุยหิมะ (Persian Classic)',
        'description': 'ราชินีแห่งแมวขนยาว หน้าหวาน นิสัยสุภาพ นิ่งสงบ อารมณ์ดี',
        'highlights': [
            ('ขนนุ่มฟูหนาสองชั้น', 'ขนนุ่มฟูระดับพรีเมียม สวยสง่าดั่งปุยหิมะ'),
            ('รักความสงบ เลี้ยงง่าย', 'ไม่ส่งเสียงรบกวน เหมาะกับคอนโดและบ้าน'),
            ('วัคซีนครบ + เพ็ด CFA', 'ตรวจสุขภาพพร้อมย้ายบ้าน รับประกัน 14 วัน')
        ],
        'bullet_border': (254, 205, 211),
        'price_orig': '22,000.-',
        'price_special': '16,500',
        'unit': 'บาท',
        'guarantee': 'ฟรี! Starter Kit 2,500.- | ตรวจสุขภาพครบ',
        'btn_text': 'จองน้องเปอร์เซีย >>'
    },

    # 6. RAGDOLL
    {
        'output_filename': 'card_6_ragdoll_1110x1000_ratio1.11.png',
        'cat_image': 'cat_ragdoll.jpg',
        'tag_text': 'ตาสีฟ้าคราม TICA',
        'photo_info': 'เพศเมีย | อายุ 2.5 เดือน',
        'color': (2, 132, 199),         # Sky Blue
        'bg_c1': (240, 249, 255),
        'bg_c2': (224, 242, 254),
        'badge_bg': (224, 242, 254),
        'badge_border': (125, 211, 252),
        'badge_txt': (7, 89, 133),
        'title': 'น้องคอตตอน (Ragdoll Princess)',
        'description': 'เจ้าหญิงตาสีฟ้าคราม ตัวนุ่มปวกเปียกเหมือนตุ๊กตาผ้า ไม่กางเล็บ',
        'highlights': [
            ('ตาสีฟ้าครามประกายเพชร', 'สายพันธุ์แท้นำเข้า มาร์คกิ้งคมชัดสมบูรณ์แบบ'),
            ('ขี้อ้อน ปลอดภัยกับเด็ก', 'นุ่มนิ่มอุ้มง่าย อ่อนโยน เข้ากับทุกคนได้ดี'),
            ('ตรวจยีนหัวใจ HCM ผ่าน', 'ใบเพ็ด TICA แท้ + วัคซีนครบ 2 เข็ม')
        ],
        'bullet_border': (186, 230, 253),
        'price_orig': '35,000.-',
        'price_special': '29,000',
        'unit': 'บาท',
        'guarantee': 'ฟรี! กระเป๋าแคปซูลอวกาศ + Starter Kit',
        'btn_text': 'จองน้องแร็กดอลล์ >>'
    },

    # 7. MUNCHKIN
    {
        'output_filename': 'card_7_munchkin_1110x1000_ratio1.11.png',
        'cat_image': 'cat_munchkin.jpg',
        'tag_text': 'ขาสั้นเตี้ยดุ๊กดิ๊ก',
        'photo_info': 'เพศผู้ | อายุ 2 เดือน',
        'color': (234, 88, 12),        # Sunset Orange
        'bg_c1': (255, 247, 237),
        'bg_c2': (254, 243, 199),
        'badge_bg': (254, 243, 199),
        'badge_border': (253, 230, 138),
        'badge_txt': (154, 52, 18),
        'title': 'น้องชอร์ตตี้ (Munchkin Legs)',
        'description': 'เจ้าเหมียวขาสั้นเตี้ยดุ๊กดิ๊ก วิ่งน่ารักสดใส ขี้เล่นอารมณ์ดี',
        'highlights': [
            ('ขาสั้นเตี้ยแท้ WCF', 'ขาสั้นน่ารักดุ๊กดิ๊ก สุขภาพแข็งแรง คล่องแคล่ว'),
            ('ปรับตัวเก่ง เป็นมิตร', 'ชอบเล่นของเล่น เข้ากับคนง่าย ไม่ซนกวนใจ'),
            ('ตรวจสุขภาพข้อต่อครบ', 'ตรวจข้อต่อและกระดูกผ่าน + ฉีดวัคซีนแล้ว')
        ],
        'bullet_border': (254, 215, 170),
        'price_orig': '29,000.-',
        'price_special': '24,000',
        'unit': 'บาท',
        'guarantee': 'ฟรี! คอนโดแมวมินิ + ของเล่นดุ๊กดิ๊ก',
        'btn_text': 'จองน้องมันช์กิ้น >>'
    },

    # 8. BENGAL
    {
        'output_filename': 'card_8_bengal_1110x1000_ratio1.11.png',
        'cat_image': 'cat_bengal.jpg',
        'tag_text': 'ลายเสือดาว Grade A',
        'photo_info': 'เพศผู้ | อายุ 3 เดือน',
        'color': (217, 119, 6),         # Amber Gold
        'bg_c1': (254, 243, 199),
        'bg_c2': (253, 230, 138),
        'badge_bg': (254, 240, 138),
        'badge_border': (250, 204, 21),
        'badge_txt': (113, 63, 18),
        'title': 'น้องจากัวร์ (Bengal Rosetted)',
        'description': 'เสือดาวจิ๋ว ลายกุหลาบทองคำ ขนประกายกลิตเตอร์ ฉลาด ปราดเปรียว',
        'highlights': [
            ('ลาย Rosette ชัดเจน', 'ลายดอกกุหลาบคมชัด ขนสะท้อนแสง Glittering'),
            ('กล้ามเนื้อแกร่ง ฉลาด', 'รักการเล่นน้ำ สามารถฝึกใส่สายจูงเดินเล่นได้'),
            ('เพ็ด WCF + ไมโครชิป', 'สายเลือดแชมป์นำเข้า ฝังไมโครชิปมาตรฐาน')
        ],
        'bullet_border': (253, 230, 138),
        'price_orig': '32,000.-',
        'price_special': '26,000',
        'unit': 'บาท',
        'guarantee': 'ฟรี! สายจูงพรีเมียม + น้ำพุแมวสแตนเลส',
        'btn_text': 'จองน้องเบงกอล >>'
    },

    # 9. SPHYNX
    {
        'output_filename': 'card_9_sphynx_1110x1000_ratio1.11.png',
        'cat_image': 'cat_sphynx.jpg',
        'tag_text': 'ภูมิแพ้ 0% ไร้ขน',
        'photo_info': 'เพศผู้ | อายุ 3 เดือน',
        'color': (13, 148, 136),        # Teal Cyan
        'bg_c1': (240, 253, 250),
        'bg_c2': (204, 251, 241),
        'badge_bg': (204, 251, 241),
        'badge_border': (94, 234, 212),
        'badge_txt': (17, 94, 89),
        'title': 'น้องซีซาร์ (Canadian Sphynx)',
        'description': 'แมวไร้ขน ผิวสัมผัสนุ่มอุ่นดั่งลูกพีช ฉลาด ขี้อ้อนติดคนมาก',
        'highlights': [
            ('ขนร่วง 0% ไร้ภูมิแพ้', 'เหมาะสำหรับคนเป็นภูมิแพ้ขนสัตว์ เลี้ยงสบายใจ'),
            ('ขี้อ้อนติดคนเหมือนสุนัข', 'ชอบนอนซุกผ้าห่ม ฉลาด ตอบสนองคำสั่งไว'),
            ('เพ็ด WCF + ฝังชิปครบ', 'วัคซีนครบ 2 เข็ม + สมุดตรวจสุขภาพประจำตัว')
        ],
        'bullet_border': (153, 246, 228),
        'price_orig': '34,000.-',
        'price_special': '28,000',
        'unit': 'บาท',
        'guarantee': 'ฟรี! เสื้อกันหนาวสฟิงซ์ + เซ็ตสกินแคร์แมว',
        'btn_text': 'จองน้องสฟิงซ์ >>'
    },

    # 10. SIAMESE
    {
        'output_filename': 'card_10_siamese_1110x1000_ratio1.11.png',
        'cat_image': 'cat_siamese.jpg',
        'tag_text': 'แมวมงคลไทยนำโชค',
        'photo_info': 'เพศเมีย | อายุ 2.5 เดือน',
        'color': (124, 58, 237),        # Royal Purple
        'bg_c1': (245, 243, 255),
        'bg_c2': (237, 233, 254),
        'badge_bg': (237, 233, 254),
        'badge_border': (196, 181, 253),
        'badge_txt': (91, 33, 182),
        'title': 'น้องมงคล (Siamese / วิเชียรมาศ)',
        'description': 'แมวมงคลไทยโบราณ แต้มสี 9 จุดคมชัด ตาสีฟ้าคราม ช่างเจรจา',
        'highlights': [
            ('แมวมงคลนำโชคลาภ', 'แต้มสี 9 จุดครบถ้วนตามตำราแมวไทยโบราณ'),
            ('ช่างพูด ช่างอ้อน ซื่อสัตย์', 'สติปัญญาเฉลียวฉลาด ผูกพันกับเจ้าของมาก'),
            ('สุขภาพแข็งแรง เลี้ยงง่าย', 'ตรวจลิวคีเมีย/เอดส์แมวผ่าน + วัคซีนครบ')
        ],
        'bullet_border': (221, 214, 254),
        'price_orig': '15,000.-',
        'price_special': '12,000',
        'unit': 'บาท',
        'guarantee': 'ฟรี! ปลอกคอกระดิ่งทอง + อาหารมงคล',
        'btn_text': 'จองน้องวิเชียรมาศ >>'
    }
]

def render_card_1_11(item):
    cw, ch = TARGET_W, TARGET_H  # 1110 x 1000
    card = Image.new("RGBA", (cw, ch), (255, 253, 250))
    draw = ImageDraw.Draw(card)

    # Ambient subtle background gradient
    bg_grad = create_vertical_grad(cw, ch, item['bg_c1'], item['bg_c2'])
    card.paste(bg_grad, (0, 0))

    # Outer border of the whole card
    draw.rounded_rectangle([0, 0, cw - 1, ch - 1], radius=32, outline=item['color'], width=4)

    # 1. Left Photo Section (x=45, y=45, w=490, h=910)
    im_x, im_y, im_w, im_h = 45, 45, 490, 910

    # Shadow behind image box
    shadow = Image.new("RGBA", (im_w + 20, im_h + 20), (0, 0, 0, 0))
    s_draw = ImageDraw.Draw(shadow)
    s_draw.rounded_rectangle([10, 10, im_w + 10, im_h + 10], radius=28, fill=(0, 0, 0, 45))
    shadow = shadow.filter(ImageFilter.GaussianBlur(12))
    card.paste(shadow, (im_x - 10, im_y - 10), shadow)

    # Cat Photo Box
    full_im_p = os.path.join(assets_img_dir, item["cat_image"])
    if os.path.exists(full_im_p):
        cat_img = Image.open(full_im_p).convert("RGBA")
        src_w, src_h = cat_img.size
        ratio = max(im_w / src_w, im_h / src_h)
        new_w = int(src_w * ratio)
        new_h = int(src_h * ratio)
        cat_img = cat_img.resize((new_w, new_h), Image.Resampling.LANCZOS)
        
        left = (new_w - im_w) // 2
        top = (new_h - im_h) // 2
        cat_cropped = cat_img.crop((left, top, left + im_w, top + im_h))
        
        c_mask = Image.new("L", (im_w, im_h), 0)
        ImageDraw.Draw(c_mask).rounded_rectangle([0, 0, im_w, im_h], radius=26, fill=255)
        
        card.paste(cat_cropped, (im_x, im_y), c_mask)

        # Image Frame Border
        draw.rounded_rectangle([im_x, im_y, im_x + im_w, im_y + im_h], radius=26, outline=item["color"], width=4)

    # Overlaid Tag Badge on top of Image (Top-Left of photo)
    tag_font = get_font(21, True)
    tag_bbox = draw.textbbox((0, 0), item['tag_text'], font=tag_font)
    tag_w = tag_bbox[2] - tag_bbox[0] + 48
    draw.rounded_rectangle([im_x + 20, im_y + 22, im_x + 20 + tag_w, im_y + 70], radius=16, fill=(0, 0, 0, 205), outline=item['color'], width=2)
    draw_sparkle(draw, im_x + 38, im_y + 46, 9, item['color'])
    draw.text((im_x + 54, im_y + 32), item['tag_text'], font=tag_font, fill=(255, 255, 255))

    # Overlaid Age/Gender Badge on bottom of photo
    info_font = get_font(19, True)
    info_bbox = draw.textbbox((0, 0), item['photo_info'], font=info_font)
    info_w = info_bbox[2] - info_bbox[0] + 48
    draw.rounded_rectangle([im_x + 20, im_y + im_h - 72, im_x + 20 + info_w, im_y + im_h - 24], radius=16, fill=(0, 0, 0, 195))
    draw_paw(draw, im_x + 38, im_y + im_h - 48, 18, (255, 220, 100))
    draw.text((im_x + 54, im_y + im_h - 62), item['photo_info'], font=info_font, fill=(255, 235, 140))

    # 2. Right Side: Card Information & Marketing Details (rx = 570, rw = 495)
    rx = 570
    rw = 495

    # Brand Title Pill
    bp_font = get_font(15, True)
    bp_text = "PURRFECT CAT SHOP  |  PREMIUM CAROUSEL"
    bp_bbox = draw.textbbox((0, 0), bp_text, font=bp_font)
    bp_w = bp_bbox[2] - bp_bbox[0] + 36
    draw.rounded_rectangle([rx, 45, rx + bp_w, 77], radius=12, fill=item['badge_bg'], outline=item['badge_border'], width=2)
    draw.text((rx + 18, 50), bp_text, font=bp_font, fill=item['badge_txt'])

    # Main Card Title
    draw.text((rx, 92), item['title'], font=get_font(31, True), fill=(15, 23, 42))

    # Breed Description
    draw.text((rx, 146), item['description'], font=get_font(19, False), fill=(71, 85, 105))

    # 3 Bullet Badges (Highlights)
    bullet_y = 205
    for idx, (b_title, b_desc) in enumerate(item['highlights']):
        by = bullet_y + (idx * 115)
        draw.rounded_rectangle([rx, by, rx + rw, by + 98], radius=18, fill=(255, 255, 255), outline=item['bullet_border'], width=2)
        # Bullet number circle
        draw.ellipse([rx + 16, by + 22, rx + 66, by + 72], fill=item['color'])
        draw.text((rx + 33, by + 30), str(idx + 1), font=get_font(24, True), fill=(255, 255, 255))
        # Bullet texts
        draw.text((rx + 82, by + 16), b_title, font=get_font(21, True), fill=(15, 23, 42))
        draw.text((rx + 82, by + 48), b_desc, font=get_font(17, False), fill=(100, 116, 139))

    # Bottom Area: Pricing & Action Button (y=570 to 955)
    bot_y = 570
    draw.rounded_rectangle([rx, bot_y, rx + rw, bot_y + 385], radius=24, fill=(255, 255, 255), outline=item['bullet_border'], width=2)

    # Price Info
    draw.text((rx + 25, bot_y + 22), "ค่าสินสอดพิเศษ:", font=get_font(20, False), fill=(100, 116, 139))
    
    # Original Price Strike
    draw.text((rx + 25, bot_y + 56), item['price_orig'], font=get_font(22, True), fill=(160, 160, 160))
    p_bbox = draw.textbbox((rx + 25, bot_y + 56), item['price_orig'], font=get_font(22, True))
    draw.line([(p_bbox[0] - 2, (p_bbox[1] + p_bbox[3]) // 2), (p_bbox[2] + 4, (p_bbox[1] + p_bbox[3]) // 2)], fill=(220, 38, 38), width=2)

    # Special Price Big
    draw.text((rx + 140, bot_y + 44), item['price_special'], font=get_font(48, True), fill=item['color'])
    draw.text((rx + 355, bot_y + 66), item.get('unit', 'บาท'), font=get_font(22, True), fill=(15, 23, 42))

    # Guarantee line
    draw_checkmark(draw, rx + 35, bot_y + 130, 13, (16, 185, 129))
    draw.text((rx + 52, bot_y + 118), item['guarantee'], font=get_font(18, False), fill=(71, 85, 105))

    # CTA Button (Styled to match the card theme color)
    btn_y = bot_y + 165
    btn_w = rw - 40
    btn_h = 75
    draw.rounded_rectangle([rx + 20, btn_y, rx + 20 + btn_w, btn_y + btn_h], radius=18, fill=item['color'])
    draw.text((rx + 115, btn_y + 20), item['btn_text'], font=get_font(25, True), fill=(255, 255, 255))
    draw_paw(draw, rx + 20 + btn_w - 40, btn_y + 37, 26, (255, 255, 255, 140))

    # Save to both target directories
    out_p1 = os.path.join(out_folder_1, item['output_filename'])
    out_p2 = os.path.join(out_folder_2, item['output_filename'])

    final_rgb = card.convert("RGB")
    final_rgb.save(out_p1, quality=96)
    final_rgb.save(out_p2, quality=96)
    print(f"[OK] Saved 1.11:1 Card ({TARGET_W}x{TARGET_H}): {item['output_filename']}")

def main():
    print("=" * 65)
    print("GENERATING CLEAN LINE OA CARD MESSAGES (RATIO 1.11 : 1)")
    print(f"Output 1: {out_folder_1}")
    print(f"Output 2: {out_folder_2}")
    print(f"Target Resolution: {TARGET_W} x {TARGET_H} px (Exact 1.11 : 1)")
    print("=" * 65)

    for item in all_card_items:
        render_card_1_11(item)

    print("\n[DONE] All 10 Card Messages generated successfully without ratio badges or outer red banner!")

if __name__ == "__main__":
    main()
