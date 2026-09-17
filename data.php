<?php
// Cat Shop - Core Data & Helper Functions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------------
// 1. Data Source: 100% Cat Breeds with Curated Specific Categories
// -------------------------------------------------------------
$cats = [
    'cat_british' => [
        'id' => 'cat_british',
        'name' => 'น้องสโนว์ (British Shorthair)',
        'breed' => 'บริติช ช็อตแฮร์ (British Shorthair)',
        'description' => 'น้องแมวสายพันธุ์ผู้ดีอังกฤษ หน้ากลมแก้มป่อง ขนแน่นนุ่มเหมือนกำมะหยี่ อารมณ์ดี เรียบร้อย เลี้ยงง่าย และเป็นมิตรกับทุกคนในครอบครัว',
        'price' => 18000,
        'image' => 'cat_british.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นแน่นนุ่ม',
        'personality' => 'เรียบร้อย อ่อนโยน เข้ากับคนง่าย',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ถ่ายพยาธิแล้ว',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF รับรองสายพันธุ์',
        'categories' => ['popular', 'condo', 'beginner', 'short_hair'],
        'highlights' => ['หน้ากลมแก้มยุ้ยน่าฟัด', 'ขนแน่นไม่ค่อยร่วง', 'เหมาะกับห้องพักและคอนโด']
    ],
    'cat_persian' => [
        'id' => 'cat_persian',
        'name' => 'น้องปุยหิมะ (Persian Classic)',
        'breed' => 'เปอร์เซีย (Persian)',
        'description' => 'ราชินีแห่งแมวขนยาว หน้าหวาน ขนฟูยาวหนานุ่มสง่างาม นิสัยสุภาพ นิ่งสงบ ไม่ส่งเสียงรบกวน ชอบนอนซุกผ้าห่มและคลอเคลียเจ้าของ',
        'price' => 16500,
        'image' => 'cat_persian.jpg',
        'age' => '3 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'long',
        'hair_label' => 'ขนยาวฟูหนา',
        'personality' => 'รักสงบ นิ่งเงียบ อ่อนโยนมาก',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ตรวจสุขภาพพร้อมย้ายบ้าน',
        'pedigree' => 'มีใบเพ็ดดีกรี CFA สายเลือดแชมป์',
        'categories' => ['fluffy', 'condo', 'long_hair'],
        'highlights' => ['ขนนุ่มฟูระดับพรีเมียม', 'รักความสงบไม่ร้องกวน', 'ชอบให้อุ้มและแปรงขน']
    ],
    'cat_sphynx' => [
        'id' => 'cat_sphynx',
        'name' => 'น้องซีซาร์ (Canadian Sphynx)',
        'breed' => 'สฟิงซ์ (Sphynx)',
        'description' => 'แมวไร้ขนสุดเท่ ผิวสัมผัสนุ่มอุ่นดั่งลูกพีช ไร้ขนร่วง 100% เหมาะสำหรับผู้ที่เป็นโรคภูมิแพ้ขนสัตว์ ฉลาดมาก ขี้อ้อนติดคนเหมือนสุนัขตัวน้อย',
        'price' => 28000,
        'image' => 'cat_sphynx.jpg',
        'age' => '3 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'hairless',
        'hair_label' => 'ไร้ขน (ผิวสัมผัสพีช)',
        'personality' => 'ขี้อ้อนมาก ติดเจ้าของ เป็นมิตรสุดๆ',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ฝังไมโครชิปแล้ว',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF สายพันธุ์แท้',
        'categories' => ['low_shed'],
        'highlights' => ['ขนร่วง 0% หมดปัญหาภูมิแพ้', 'ฉลาดและตอบสนองไว', 'อ้อนเก่งระดับสิบ']
    ],
    'cat_siamese' => [
        'id' => 'cat_siamese',
        'name' => 'น้องมงคล (Siamese / วิเชียรมาศ)',
        'breed' => 'วิเชียรมาศ (Siamese Cat)',
        'description' => 'แมวมงคลไทยโบราณ แต้มสี 9 จุดคมชัด ตาสีฟ้าครามเปล่งประกาย สติปัญญาเฉลียวฉลาด ช่างพูดช่างคุย ซื่อสัตย์และรักเจ้าของสุดหัวใจ',
        'price' => 12000,
        'image' => 'cat_siamese.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นเรียบเนียน',
        'personality' => 'ช่างพูด เฉลียวฉลาด ร่าเริง ผูกพันกับเจ้าของ',
        'vaccine' => 'ฉีดวัคซีนรวม + ตรวจลิวคีเมีย/เอดส์แมวผ่าน',
        'pedigree' => 'ใบรับรองสายพันธุ์แมวไทยโบราณแท้',
        'categories' => ['beginner', 'playful', 'short_hair'],
        'highlights' => ['แมวมงคลนำโชคลาภ', 'ช่างอ้อนช่างคุย', 'สุขภาพแข็งแรงเลี้ยงง่าย']
    ],
    'cat_mainecoon' => [
        'id' => 'cat_mainecoon',
        'name' => 'น้องไททัน (Maine Coon)',
        'breed' => 'เมนคูน (Maine Coon)',
        'description' => 'ยักษ์ใหญ่ใจดี สายพันธุ์แมวที่ตัวใหญ่ที่สุดในโลก ขนแผงคอสง่างามราวสิงโต นิสัยอ่อนโยน ขี้เล่น เป็นมิตรกับเด็กและสัตว์เลี้ยงอื่นๆ',
        'price' => 35000,
        'image' => 'cat_mainecoon.jpg',
        'age' => '3.5 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'long',
        'hair_label' => 'ขนยาวสองชั้นฟูหนา',
        'personality' => 'ใจดี อ่อนโยน เข้ากับทุกคนได้ง่าย',
        'vaccine' => 'ฉีดวัคซีนครบ 3 เข็ม + ฝังไมโครชิปมาตรฐาน',
        'pedigree' => 'มีใบเพ็ดดีกรี CFA สายเลือดแชมป์นำเข้า',
        'categories' => ['fluffy', 'playful', 'long_hair'],
        'highlights' => ['ขนาดตัวใหญ่สง่างามดั่งสิงโต', 'นิสัยน่ารักใจดีดั่งสุนัข', 'รักทุกคนในบ้าน']
    ],
    'cat_ragdoll' => [
        'id' => 'cat_ragdoll',
        'name' => 'น้องคอตตอน (Ragdoll)',
        'breed' => 'แร็กดอลล์ (Ragdoll)',
        'description' => 'เจ้าหญิงดวงตาสีฟ้าคราม ตัวนุ่มปวกเปียกเหมือนตุ๊กตาผ้าเวลาอุ้ม อ่อนโยน รักความสงบ ขนนุ่มดั่งปุยฝ้าย ไม่กางเล็บ เหมาะกับครอบครัวที่มีเด็ก',
        'price' => 29000,
        'image' => 'cat_ragdoll.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'long',
        'hair_label' => 'ขนกึ่งยาวนุ่มลื่น',
        'personality' => 'ยอมคน อ่อนหวาน นุ่มนวล ไม่ดุร้าย',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ตรวจพันธุกรรมโรคหัวใจ HCM ปกติ',
        'pedigree' => 'มีใบเพ็ดดีกรี TICA รับรองสายพันธุ์',
        'categories' => ['popular', 'fluffy', 'condo', 'long_hair'],
        'highlights' => ['ตาสีฟ้าเปล่งประกายสวยงาม', 'ตัวนุ่มนิ่มอุ้มง่ายไม่ดิ้น', 'ปลอดภัยสำหรับเด็ก']
    ],
    'cat_bengal' => [
        'id' => 'cat_bengal',
        'name' => 'น้องจากัวร์ (Bengal Rosetted)',
        'breed' => 'เบงกอล (Bengal)',
        'description' => 'เสือดาวจิ๋วประจำบ้าน ลวดลายดอกกุหลาบ (Rosette) สีทองเด่นชัด กล้ามเนื้อแข็งแรง ปราดเปรียว ชื่นชอบการปีนป่ายและเล่นน้ำ',
        'price' => 26000,
        'image' => 'cat_bengal.jpg',
        'age' => '3 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นประกายกลิตเตอร์ทอง',
        'personality' => 'พลังงานสูง ปราดเปรียว รักการผจญภัย',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ถ่ายพยาธิเรียบร้อย',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF ลายโรเซ็ตต์ชัดเจน',
        'categories' => ['playful', 'short_hair'],
        'highlights' => ['ลายเสือดาวหายากสวยสง่า', 'ขนมีประกายทองวิบวับ', 'รักการเล่นน้ำและวิ่งเล่น']
    ],
    'cat_scottish' => [
        'id' => 'cat_scottish',
        'name' => 'น้องพุดดิ้ง (Scottish Fold)',
        'breed' => 'สก็อตติช โฟลด์ (Scottish Fold)',
        'description' => 'เจ้าเหมียวหูพับ หน้ากลมแป้นเหมือนนกฮูก ขี้อ้อน ชอบนั่งท่านั่งพุงพลุ้ย (Buddha Position) น่ารักน่าเอ็นดู เข้ากับทุกคนได้ง่ายดาย',
        'price' => 21000,
        'image' => 'cat_scottish.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นนุ่มหนา',
        'personality' => 'ขี้อ้อน อารมณ์ดี ชอบอยู่ใกล้ชิดเจ้าของ',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ตรวจสุขภาพกระดูกและข้อผ่าน',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF สายเลือดแท้',
        'categories' => ['popular', 'condo', 'beginner', 'short_hair'],
        'highlights' => ['หูพับแนบสนิทหน้ากลมน่ารัก', 'ชอบนั่งท่าบุดด้าตลกๆ', 'รักสงบเหมาะกับคอนโด']
    ],
    'cat_munchkin' => [
        'id' => 'cat_munchkin',
        'name' => 'น้องชอร์ตตี้ (Munchkin Short Legs)',
        'breed' => 'มันช์กิ้น ขาสั้น (Munchkin)',
        'description' => 'แมวขาสั้นเตี้ยดุ๊กดิ๊กสุดน่ารัก วิ่งดุ๊กดิ๊กไปมาเรียกเสียงหัวเราะได้ตลอดวัน กระตือรือร้น ชอบสำรวจ เป็นมิตรและปรับตัวเข้ากับสภาพแวดล้อมได้เร็ว',
        'price' => 24000,
        'image' => 'cat_munchkin.jpg',
        'age' => '2 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นนุ่มฟูนิดๆ',
        'personality' => 'ร่าเริง สดใส ตื่นตัว ชอบเล่นกับของเล่น',
        'vaccine' => 'ฉีดวัคซีนครบตามช่วงวัย + ถ่ายพยาธิเรียบร้อย',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF ขาสั้นแท้มาตรฐาน',
        'categories' => ['popular', 'playful', 'short_hair'],
        'highlights' => ['ขาสั้นน่ารักดุ๊กดิ๊กสะกดใจ', 'ปรับตัวเก่งเป็นมิตร', 'เล่นเก่งไม่เหงาแน่นอน']
    ],
    'cat_russian' => [
        'id' => 'cat_russian',
        'name' => 'น้องบลูสกาย (Russian Blue)',
        'breed' => 'รัสเซียน บลู (Russian Blue)',
        'description' => 'แมวชั้นสูงสีเทาเงินสะท้อนแสง ดวงตาสีเขียวมรกต เรียบร้อย สะอาด สุภาพ ไม่ส่งเสียงดัง ผลัดขนน้อยมาก เหมาะสำหรับคนทำงานที่ชอบความสงบ',
        'price' => 22000,
        'image' => 'cat_russian.jpg',
        'age' => '3 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นสีเทาเงินประกาย',
        'personality' => 'สุภาพ เรียบร้อย ไม่ส่งเสียงดัง รักสงบ',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ตรวจสุขภาพทั่วไปผ่านฉลุย',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF สายเลือดแท้',
        'categories' => ['condo', 'low_shed', 'short_hair'],
        'highlights' => ['ดวงตาสีเขียวมรกตสะกดสายตา', 'ขนร่วงน้อยแทบไม่ผลัดขน', 'รักความสะอาดและความเงียบสงบ']
    ],
    'cat_abyssinian' => [
        'id' => 'cat_abyssinian',
        'name' => 'น้องแอมเบอร์ (Abyssinian)',
        'breed' => 'อบิสซิเนียน (Abyssinian)',
        'description' => 'แมวอียิปต์โบราณ ขนสีส้มอบเชยไล่เฉดสวยงาม ล่ำสัน ปราดเปรียวและฉลาดหลักแหลม ชอบมีส่วนร่วมกับทุกกิจกรรมของเจ้าของ รักการกระโดดสูง',
        'price' => 19000,
        'image' => 'cat_abyssinian.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นลวดลาย Ticked Tabby',
        'personality' => 'คล่องแคล่ว เฉลียวฉลาด ร่าเริง ชอบเล่น',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + สมุดตรวจสุขภาพประจำตัว',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF รับรองสายพันธุ์',
        'categories' => ['playful', 'short_hair'],
        'highlights' => ['ขนไล่เฉดสีอบเชยสวยแปลกตา', 'ฉลาดสามารถฝึกสอนคำสั่งได้', 'ขี้เล่นสร้างรอยยิ้มได้เสมอ']
    ],
    'cat_americanshorthair' => [
        'id' => 'cat_americanshorthair',
        'name' => 'น้องการ์ฟิลด์ (American Shorthair)',
        'breed' => 'อเมริกัน ช็อตแฮร์ (American Shorthair)',
        'description' => 'แมวลายเสือสีเงินคลาสสิก ลายชัดเจน สุขภาพแข็งแรง อึด ทน ไม่ป่วยง่าย เลี้ยงง่าย อารมณ์ดี เป็นมิตรกับทั้งคนแปลกหน้าและสัตว์เลี้ยงอื่น',
        'price' => 15000,
        'image' => 'cat_americanshorthair.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นแน่นลายชัดเจน',
        'personality' => 'อารมณ์ดี เข้ากับทุกคนง่าย เลี้ยงง่ายที่สุด',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ถ่ายพยาธิเรียบร้อย',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF ลาย Classic Tabby ชัดเจน',
        'categories' => ['beginner', 'condo', 'short_hair'],
        'highlights' => ['ลายเสือคลาสสิกคมชัด', 'สุขภาพแข็งแรงเลี้ยงง่ายที่สุด', 'เป็นมิตรกับทุกคนในบ้าน']
    ],
    'cat_norwegian' => [
        'id' => 'cat_norwegian',
        'name' => 'น้องธอร์ (Norwegian Forest Cat)',
        'breed' => 'นอร์วีเจียน ฟอเรสต์ (Norwegian Forest Cat - นอร์เวย์)',
        'description' => 'แมวไวกิ้งโบราณแห่งสแกนดิเนเวีย ขนสองชั้นกันน้ำหนานุ่ม แผงคอใหญ่สง่างาม ร่างกายกำยำ ปีนป่ายเก่งและรักความสงบ',
        'price' => 32000,
        'image' => 'cat_norwegian.jpg',
        'age' => '3 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'long',
        'hair_label' => 'ขนยาวสองชั้นหนานุ่ม',
        'personality' => 'สุขุม อ่อนโยน ฉลาด รักการสำรวจ',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ฝังไมโครชิปแล้ว',
        'pedigree' => 'มีใบเพ็ดดีกรี FIFe สายพันธุ์แท้นำเข้า',
        'categories' => ['fluffy', 'playful', 'long_hair'],
        'highlights' => ['สายพันธุ์ไวกิ้งหายาก', 'ขนกันน้ำหนานุ่ม', 'นิสัยอ่อนโยนรักสงบ']
    ],
    'cat_japanese_bobtail' => [
        'id' => 'cat_japanese_bobtail',
        'name' => 'น้องซากุระ (Japanese Bobtail Mi-Ke)',
        'breed' => 'เจแปนนิส บ็อบเทล (Japanese Bobtail - ญี่ปุ่น)',
        'description' => 'ต้นกำเนิดแมวกวักนำโชค (Maneki-Neko) ของญี่ปุ่น หางกุดรูปพู่กลม ลายสามสีมงคล สดใส ช่างพูด เป็นมิตรและซื่อสัตย์',
        'price' => 23000,
        'image' => 'cat_japanese_bobtail.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นลวดลายสามสี มิเกะ',
        'personality' => 'ร่าเริง ฉลาด ช่างเจรจา ซื่อสัตย์',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ตรวจสุขภาพสมบูรณ์',
        'pedigree' => 'มีใบเพ็ดดีกรี CFA สายพันธุ์แท้',
        'categories' => ['popular', 'beginner', 'playful', 'short_hair'],
        'highlights' => ['ต้นแบบแมวกวักนำโชค', 'หางกุดพุ่มน่ารัก', 'ปรับตัวเก่งเป็นมิตร']
    ],
    'cat_siberian' => [
        'id' => 'cat_siberian',
        'name' => 'น้องวลาดิเมียร์ (Siberian Forest Cat)',
        'breed' => 'ไซบีเรียน (Siberian Cat - รัสเซีย)',
        'description' => 'แมวแห่งผืนป่าไซบีเรีย ขนสามชั้นทนหนาวจัด สารก่อภูมิแพ้ต่ำ (Hypoallergenic) กระโดดสูง ใจดี ขี้เล่น และรักทุกคนในบ้าน',
        'price' => 34000,
        'image' => 'cat_siberian.jpg',
        'age' => '3 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'long',
        'hair_label' => 'ขนยาวสามชั้น Hypoallergenic',
        'personality' => 'ใจดี รักสนุก ฉลาด ขี้อ้อน',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + สมุดประจำตัว',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF สายเลือดรัสเซียแท้',
        'categories' => ['fluffy', 'low_shed', 'playful', 'long_hair'],
        'highlights' => ['สารก่อภูมิแพ้ต่ำเหมาะคนแพ้ง่าย', 'ขนฟูสวยสง่า', 'เป็นมิตรกับสัตว์เลี้ยงอื่น']
    ],
    'cat_turkish_angora' => [
        'id' => 'cat_turkish_angora',
        'name' => 'น้องสุลต่าน (Turkish Angora)',
        'breed' => 'เตอร์กิช แองโกรา (Turkish Angora - ตุรกี)',
        'description' => 'สมบัติแห่งชาติตุรกี ขนสีขาวบริสุทธิ์พริ้วไหวดั่งผ้าไหม ดวงตาสีฟ้าและสีอำพัน (Odd-Eyes) เฉลียวฉลาด สง่างามและช่างประจบ',
        'price' => 27000,
        'image' => 'cat_turkish_angora.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'long',
        'hair_label' => 'ขนกึ่งยาวสัมผัสไหมนุ่ม',
        'personality' => 'สง่างาม ปราดเปรียว ช่างเอาใจ ฉลาด',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ตรวจการได้ยินปกติ',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF สายเลือดแท้',
        'categories' => ['fluffy', 'condo', 'long_hair'],
        'highlights' => ['ขนสีขาวประกายไหม', 'ตาสองสีทรงเสน่ห์', 'ความสง่างามระดับราชสำนัก']
    ],
    'cat_turkish_van' => [
        'id' => 'cat_turkish_van',
        'name' => 'น้องคาราเมล (Turkish Van Swimming Cat)',
        'breed' => 'เตอร์กิช แวน (Turkish Van - ตุรกี)',
        'description' => 'แมวว่ายน้ำแห่งทะเลสาบแวน ลวดลายสีเฉพาะที่หัวและหาง (Van Pattern) ขนกันน้ำ ชอบเล่นน้ำและผจญภัย ร่างกายล่ำสันแข็งแรง',
        'price' => 26000,
        'image' => 'cat_turkish_van.jpg',
        'age' => '3 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'long',
        'hair_label' => 'ขนกึ่งยาวกันน้ำพิเศษ',
        'personality' => 'รักการว่ายน้ำ พลังงานสูง ฉลาด มั่นใจ',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ถ่ายพยาธิแล้ว',
        'pedigree' => 'มีใบเพ็ดดีกรี TICA รับรองสายพันธุ์',
        'categories' => ['playful', 'long_hair'],
        'highlights' => ['ฉายาแมวชอบว่ายน้ำ', 'ลวดลายแวนคลาสสิก', 'สุขภาพแข็งแรงมาก']
    ],
    'cat_cornish_rex' => [
        'id' => 'cat_cornish_rex',
        'name' => 'น้องซิกแซก (Cornish Rex)',
        'breed' => 'คอร์นิช เร็กซ์ (Cornish Rex - อังกฤษ)',
        'description' => 'แมวขนลอนคลื่นแปลกตาจากคอร์นวอลล์ สัมผัสนุ่มเหมือนกำมะหยี่ ขนแทบไม่ร่วง รูปร่างเพรียวระหงดั่งเกรย์ฮาวด์ กระโดดเก่งและขี้อ้อน',
        'price' => 25000,
        'image' => 'cat_cornish_rex.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นหยิกเป็นลอนคลื่น',
        'personality' => 'ขี้เล่น กระตือรือร้น รักความอบอุ่น',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ตรวจสุขภาพผ่าน',
        'pedigree' => 'มีใบเพ็ดดีกรี GCCF จากอังกฤษ',
        'categories' => ['low_shed', 'playful', 'short_hair'],
        'highlights' => ['ขนลอนคลื่นไม่เหมือนใคร', 'ขนร่วงน้อยมาก', 'อบอุ่นน่ากอด']
    ],
    'cat_devon_rex' => [
        'id' => 'cat_devon_rex',
        'name' => 'น้องพิ๊กซี่ (Devon Rex)',
        'breed' => 'เดวอน เร็กซ์ (Devon Rex - อังกฤษ)',
        'description' => 'แมวเอลฟ์หูใหญ่ ตาโต ขนสั้นหยิกนุ่มเหมือนขนนก ฉลาดหลักแหลม ชอบเกาะบนไหล่และเรียนรู้ทริคใหม่ๆ ได้อย่างรวดเร็ว',
        'price' => 27000,
        'image' => 'cat_devon_rex.jpg',
        'age' => '3 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นลอนละเอียดนุ่ม',
        'personality' => 'ขี้เล่น ช่างสังเกต ชอบนั่งบนไหล่',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ถ่ายพยาธิ',
        'pedigree' => 'มีใบเพ็ดดีกรี CFA สายเลือดแท้',
        'categories' => ['popular', 'low_shed', 'playful', 'condo', 'short_hair'],
        'highlights' => ['หน้าตาเหมือนเอลฟ์น่ารัก', 'ชอบคลอเคลียบนไหล่', 'ขนร่วงน้อยดูแลง่าย']
    ],
    'cat_singapura' => [
        'id' => 'cat_singapura',
        'name' => 'น้องเปี๊ยก (Singapura)',
        'breed' => 'สิงกาปุระ (Singapura - สิงคโปร์)',
        'description' => 'สายพันธุ์แมวบ้านที่มีขนาดตัวเล็กที่สุดในโลก ตาโตสีเฮเซล ขนสีน้ำตาลเซเปียอบอุ่น น่ารักทะนุถนอม ขี้อ้อนและเข้ากับคนง่าย',
        'price' => 28000,
        'image' => 'cat_singapura.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นละเอียดสีเซเปีย',
        'personality' => 'ตัวเล็กขี้อ้อน อ่อนหวาน ช่างสังเกต',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + สมุดสุขภาพ',
        'pedigree' => 'มีใบเพ็ดดีกรี TICA รับรองสายพันธุ์จิ๋ว',
        'categories' => ['condo', 'beginner', 'short_hair'],
        'highlights' => ['สายพันธุ์แมวตัวเล็กที่สุดในโลก', 'ดวงตากลมโตบ้องแบ๊ว', 'เหมาะกับคอนโดและพื้นที่จำกัด']
    ],
    'cat_american_curl' => [
        'id' => 'cat_american_curl',
        'name' => 'น้องเคิร์ลลี่ (American Curl)',
        'breed' => 'อเมริกัน เคิร์ล (American Curl - สหรัฐอเมริกา)',
        'description' => 'แมวหูดัดม้วนไปข้างหลังอย่างมีเอกลักษณ์ นิสัยเหมือนลูกแมวตลอดชีวิต (Peter Pan of Cats) อารมณ์ดี ฉลาด เป็นมิตรกับทุกคน',
        'price' => 24000,
        'image' => 'cat_american_curl.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นนุ่มแน่น',
        'personality' => 'ขี้เล่น สดใส เข้ากับเด็กๆ ได้ดีเยี่ยม',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ตรวจกระดูกใบหู',
        'pedigree' => 'มีใบเพ็ดดีกรี CFA สายพันธุ์แท้',
        'categories' => ['beginner', 'playful', 'short_hair'],
        'highlights' => ['ใบหูดัดโค้งไปด้านหลังแปลกตา', 'นิสัยร่าเริงดั่งเด็กน้อยตลอดกาล', 'สุขภาพแข็งแรง']
    ],
    'cat_bombay' => [
        'id' => 'cat_bombay',
        'name' => 'น้องแพนเธอร์ (Bombay Cat)',
        'breed' => 'บอมเบย์ (Bombay Cat - สหรัฐอเมริกา)',
        'description' => 'เสือดำจิ๋วประจำห้องนั่งเล่น ขนสีดำขลับเงาวับดั่งผ้าซาติน ดวงตาสีทองแดงเปล่งประกาย สุภาพ ขี้อ้อน และรักความอบอุ่น',
        'price' => 21000,
        'image' => 'cat_bombay.jpg',
        'age' => '3 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นสีดำเงาวับ',
        'personality' => 'สุขุม อ่อนหวาน ติดคน รักความสงบ',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ถ่ายพยาธิ',
        'pedigree' => 'มีใบเพ็ดดีกรี TICA รับรองสายพันธุ์',
        'categories' => ['condo', 'beginner', 'short_hair'],
        'highlights' => ['ขนดำเงาดั่งแพรไหม', 'ตาสีทองแดงโดดเด่น', 'เชื่องและอ้อนเก่ง']
    ],
    'cat_burmese' => [
        'id' => 'cat_burmese',
        'name' => 'น้องโกโก้ (Burmese Cat)',
        'breed' => 'เบอร์มีส (Burmese - เมียนมา / พม่า)',
        'description' => 'แมวทองแดงโบราณ รูปร่างกลมกลืน ขนสีน้ำตาลเข้มซาติน ตาสีเหลืองทอง นิสัยขี้เล่น เชื่อมโยงจิตใจกับเจ้าของอย่างลึกซึ้ง',
        'price' => 19500,
        'image' => 'cat_burmese.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นเงางามสีน้ำตาลช็อกโกแลต',
        'personality' => 'ซื่อสัตย์ ช่างเอาใจ ขี้เล่น ผูกพันกับคน',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + สมุดประจำตัว',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF สายเลือดแท้',
        'categories' => ['beginner', 'playful', 'short_hair'],
        'highlights' => ['ขนเนียนนุ่มสีน้ำตาลหรูหรา', 'ขี้อ้อนติดเจ้าของ', 'เลี้ยงง่ายไม่จุกจิก']
    ],
    'cat_chartreux' => [
        'id' => 'cat_chartreux',
        'name' => 'น้องมอนเต้ (Chartreux)',
        'breed' => 'ชาร์ตรู (Chartreux - ฝรั่งเศส)',
        'description' => 'แมวประวัติศาสตร์ของพระคริสต์ในฝรั่งเศส ขนสีเทาเงินอมฟ้าหนานุ่ม ตาสีส้มอำพัน ยิ้มหวานตลอดเวลา เงียบสงบ ไม่ส่งเสียงรบกวน',
        'price' => 29000,
        'image' => 'cat_chartreux.jpg',
        'age' => '3 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นสองชั้นสีเทาเงินอมฟ้า',
        'personality' => 'สงบนิ่ง สุภาพ ไม่ส่งเสียงดัง ยิ้มหวาน',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ตรวจโรคทางพันธุกรรม',
        'pedigree' => 'มีใบเพ็ดดีกรี LOOF จากฝรั่งเศส',
        'categories' => ['condo', 'short_hair'],
        'highlights' => ['ฉายาแมวยิ้มแห่งฝรั่งเศส', 'เงียบสงบที่สุดไม่เคยร้องกวน', 'ขนสองชั้นนุ่มหนา']
    ],
    'cat_khao_manee' => [
        'id' => 'cat_khao_manee',
        'name' => 'น้องมณีเพชร (ขาวมณี / Khao Manee)',
        'breed' => 'ขาวมณี (Khao Manee - ประเทศไทย)',
        'description' => 'อัญมณีมีชีวิตแห่งสยาม ขนขาวปลอดดั่งหิมะ ดวงตาสองสี (ฟ้า-เหลืองอำพัน) ส่องประกายดั่งเพชร ฉลาด ขี้เล่น และนำพาโชคลาภวาสนา',
        'price' => 25000,
        'image' => 'cat_khao_manee.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นสีขาวบริสุทธิ์ 100%',
        'personality' => 'ฉลาด ร่าเริง ช่างสังเกต ขี้ประจบ',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ตรวจการได้ยินผ่านเกณฑ์',
        'pedigree' => 'ใบรับรองสายพันธุ์แมวมงคลไทยโบราณ',
        'categories' => ['popular', 'beginner', 'playful', 'short_hair'],
        'highlights' => ['ตาสองสี (Odd-Eyes) หายากทรงคุณค่า', 'แมวมงคลสยามโบราณ', 'ขาวบริสุทธิ์ไร้แต้มสี']
    ],
    'cat_korat' => [
        'id' => 'cat_korat',
        'name' => 'น้องสีเงิน (โคราช / แมวสีสวาด)',
        'breed' => 'โคราช / สีสวาด (Korat Cat - ประเทศไทย)',
        'description' => 'แมวแห่งความรักและโชคลาภ หน้าทรงรูปหัวใจ ขนสีสวาดปลายเงินประกาย (Silver-tipped) ตาสีเขียวมรกต ฉลาดและผูกพันกับเจ้าของเป็นเลิศ',
        'price' => 16000,
        'image' => 'cat_korat.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นประกายสีเงินเหลือบเมฆ',
        'personality' => 'ซื่อสัตย์ อ่อนหวาน จดจำเจ้าของแม่นยำ',
        'vaccine' => 'ฉีดวัคซีนรวม + ตรวจสุขภาพเรียบร้อย',
        'pedigree' => 'ใบรับรองแมวไทยโบราณสายพันธุ์แท้',
        'categories' => ['condo', 'beginner', 'short_hair'],
        'highlights' => ['ใบหน้าทรงหัวใจนำความรัก', 'ขนประกายสีเงินวาววับ', 'สุขภาพแข็งแรงสายพันธุ์แท้ดั้งเดิม']
    ],
    'cat_savannah' => [
        'id' => 'cat_savannah',
        'name' => 'น้องซิมบ้า (Savannah Cat F4)',
        'breed' => 'ซาวันนาห์ (Savannah - สหรัฐอเมริกา / แอฟริกา)',
        'description' => 'แมวขายาวสง่างาม ลวดลายจุดเสือป่าแอฟริกัน ร่างกายสูงเพรียว ฉลาดเทียบเท่าสุนัข เดินด้วยสายจูงได้ และชื่นชอบการเล่นน้ำ',
        'price' => 45000,
        'image' => 'cat_savannah.jpg',
        'age' => '3 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นลายจุดเสือป่า',
        'personality' => 'พลังงานสูง ฉลาดล้ำเลิศ คล่องแคล่ว รักน้ำ',
        'vaccine' => 'ฉีดวัคซีนครบ 3 เข็ม + ฝังไมโครชิปสากล',
        'pedigree' => 'มีใบเพ็ดดีกรี TICA จดทะเบียน F4 ถูกกฎหมาย',
        'categories' => ['playful', 'short_hair'],
        'highlights' => ['รูปร่างสูงเพรียวสง่าดั่งเสือชีตาห์', 'ฝึกจูงเดินเล่นได้เหมือนสุนัข', 'ระดับพรีเมียมเอกซ์คลูซีฟ']
    ],
    'cat_egyptian_mau' => [
        'id' => 'cat_egyptian_mau',
        'name' => 'น้องฟาโรห์ (Egyptian Mau)',
        'breed' => 'อียิปเชียน มัว (Egyptian Mau - อียิปต์)',
        'description' => 'แมวโบราณลายจุดธรรมชาติแท้หนึ่งเดียวจากลุ่มแม่น้ำไนล์ วิ่งเร็วที่สุดในโลก (สูงสุด 48 กม./ชม.) ตาสีกูสเบอร์รี่เขียวสดใส ซื่อสัตย์และรักเจ้าของ',
        'price' => 28000,
        'image' => 'cat_egyptian_mau.jpg',
        'age' => '3 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นลายจุดธรรมชาติคมชัด',
        'personality' => 'ว่องไว ซื่อสัตย์ อ่อนโยนกับครอบครัว',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + สมุดสุขภาพ',
        'pedigree' => 'มีใบเพ็ดดีกรี CFA สายเลือดอียิปต์แท้',
        'categories' => ['playful', 'short_hair'],
        'highlights' => ['ลายจุดธรรมชาติแท้จากอียิปต์', 'แมวบ้านที่วิ่งเร็วที่สุดในโลก', 'รักเจ้าของสุดหัวใจ']
    ],
    'cat_scottish_straight' => [
        'id' => 'cat_scottish_straight',
        'name' => 'น้องมาร์ชเมลโล่ (Scottish Straight)',
        'breed' => 'สก็อตติช สเตรท (Scottish Straight - สก็อตแลนด์)',
        'description' => 'พี่น้องหูตั้งของสก็อตติช โฟลด์ หน้ากลมแป้น แก้มยุ้ย โครงสร้างกระดูกแข็งแรงสมบูรณ์ ขี้อ้อน ใจดี เหมาะสำหรับครอบครัว',
        'price' => 17000,
        'image' => 'cat_scottish_straight.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นแน่นนุ่มฟู',
        'personality' => 'อารมณ์ดี ขี้อ้อน เรียบร้อย สบายๆ',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ถ่ายพยาธิ',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF สายพันธุ์แท้',
        'categories' => ['popular', 'condo', 'beginner', 'short_hair'],
        'highlights' => ['หน้ากลมแก้มยุ้ยน่ารัก', 'สุขภาพข้อกระดูกแข็งแรงไร้กังวล', 'เลี้ยงง่ายเป็นมิตร']
    ],
    'cat_somali' => [
        'id' => 'cat_somali',
        'name' => 'น้องฟ็อกซี่ (Somali Fox Cat)',
        'breed' => 'โซมาลี (Somali Cat - โซมาเลีย / สหรัฐอเมริกา)',
        'description' => 'จิ้งจอกน้อยแห่งโลกแมว ขนยาวพริ้วสีส้มอบเชย หางพวงฟูสง่างาม ฉลาด คล่องแคล่ว ชอบเล่นซ่อนหาและสร้างรอยยิ้ม',
        'price' => 26000,
        'image' => 'cat_somali.jpg',
        'age' => '3 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'long',
        'hair_label' => 'ขนยาวพริ้วสีอบเชยหางพวง',
        'personality' => 'ร่าเริง ฉลาด ช่างเล่น อยากรู้อยากเห็น',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ตรวจสุขภาพ',
        'pedigree' => 'มีใบเพ็ดดีกรี CFA รับรองสายพันธุ์',
        'categories' => ['fluffy', 'playful', 'long_hair'],
        'highlights' => ['หางพวงขนฟูเหมือนจิ้งจอก', 'ขนไล่สีอบเชยเงางาม', 'ฉลาดฝึกสอนได้']
    ],
    'cat_australian_mist' => [
        'id' => 'cat_australian_mist',
        'name' => 'น้องโอปอล (Australian Mist)',
        'breed' => 'ออสเตรเลียน มิสต์ (Australian Mist - ออสเตรเลีย)',
        'description' => 'แมวสายพันธุ์แรกของออสเตรเลีย ลายหมอกนุ่มนวลเหมือนคลุมด้วยไอหมอก นิสัยผ่อนคลาย รักเด็กและทนทาน เหมาะสำหรับเลี้ยงในบ้าน 100%',
        'price' => 22000,
        'image' => 'cat_australian_mist.jpg',
        'age' => '2.5 เดือน',
        'gender' => 'เมีย (Female)',
        'hair_type' => 'short',
        'hair_label' => 'ขนสั้นลวดลายหมอกนุ่มนวล',
        'personality' => 'ใจเย็น อ่อนโยน เข้ากับเด็กเล็กได้ยอดเยี่ยม',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + สมุดตรวจสุขภาพ',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF สายพันธุ์ออสเตรเลียแท้',
        'categories' => ['condo', 'beginner', 'short_hair'],
        'highlights' => ['สายพันธุ์แรกแห่งแดนจิงโจ้', 'ลายหมอกฟุ้งนุ่มตา', 'ใจเย็นเข้ากับเด็กเล็กได้ดีที่สุด']
    ],
    'cat_kurilian_bobtail' => [
        'id' => 'cat_kurilian_bobtail',
        'name' => 'น้องโมจิ (Kurilian Bobtail)',
        'breed' => 'คูริเลียน บ็อบเทล (Kurilian Bobtail - หมู่เกาะคูริล / รัสเซีย-ญี่ปุ่น)',
        'description' => 'แมวเกาะหางกระต่ายธรรมชาติ ขนหนาทนหนาว กล้ามเนื้อแข็งแรง ว่ายน้ำเก่ง ไม่กลัวน้ำ นิสัยสุขุม ซื่อสัตย์ดั่งสุนัข',
        'price' => 25000,
        'image' => 'cat_kurilian_bobtail.jpg',
        'age' => '3 เดือน',
        'gender' => 'ผู้ (Male)',
        'hair_type' => 'long',
        'hair_label' => 'ขนกึ่งยาวหนานุ่มหางพู่สั้น',
        'personality' => 'ซื่อสัตย์ สุขุม รักน้ำ ว่ายน้ำเก่ง',
        'vaccine' => 'ฉีดวัคซีนครบ 2 เข็ม + ฝังไมโครชิป',
        'pedigree' => 'มีใบเพ็ดดีกรี WCF สายเลือดเกาะคูริลแท้',
        'categories' => ['fluffy', 'playful', 'long_hair'],
        'highlights' => ['หางพู่ปอมปอมธรรมชาติ', 'ทนทานไม่ป่วยง่าย', 'นิสัยซื่อสัตย์ดั่งสุนัข']
    ]
];

// -------------------------------------------------------------
// 2. Curated Recommendation Categories (Distinct Subsets)
// -------------------------------------------------------------
$recommendation_categories = [
    'popular' => [
        'name' => '🔥 สายพันธุ์ยอดฮิต',
        'title' => '🔥 สายพันธุ์ยอดนิยมอันดับ 1',
        'desc' => 'น้องแมวสายพันธุ์ท็อปฮิตที่ได้รับความนิยมสูงสุด นิสัยน่ารัก เข้ากับทุกคนง่าย'
    ],
    'condo' => [
        'name' => '🏢 เหมาะเลี้ยงในคอนโด',
        'title' => '🏢 แมวรักสงบ เหมาะสำหรับคอนโดและพื้นที่จำกัด',
        'desc' => 'ไม่ส่งเสียงรบกวนเพื่อนบ้าน ไม่กระโดดรื้อของ รักความเงียบสงบและเป็นระเบียบ'
    ],
    'fluffy' => [
        'name' => '🧸 ขนยาว นุ่มฟูน่ากอด',
        'title' => '🧸 น้องแมวขนฟู ปุยเมฆน่ากอด',
        'desc' => 'ขนนุ่มหนาสง่างามดั่งเจ้าหญิงเจ้าชาย เหมาะสำหรับคนที่หลงใหลในความนุ่มฟู'
    ],
    'short_hair' => [
        'name' => '✨ ขนสั้น ดูแลง่าย',
        'title' => '✨ น้องแมวขนสั้น ดูแลง่าย สบายใจ',
        'desc' => 'หมดกังวลเรื่องการหวีขน ขนแน่นนุ่มไม่พันกัน อาบน้ำแห้งไว เหมาะกับวิถีชีวิตคนยุคใหม่'
    ],
    'playful' => [
        'name' => '⚡ ร่าเริง ขี้เล่น ซุกซน',
        'title' => '⚡ น้องแมวสายร่าเริง สดใส พลังงานเต็มเปี่ยม',
        'desc' => 'ชอบเล่นของเล่น ช่างสำรวจ ปราดเปรียว เติมเต็มเสียงหัวเราะและความมีชีวิตชีวาให้บ้าน'
    ],
    'beginner' => [
        'name' => '👶 มือใหม่เลี้ยงง่าย',
        'title' => '👶 น้องแมวสำหรับมือใหม่หัดเลี้ยงแมวตัวแรก',
        'desc' => 'สุขภาพแข็งแรง ทนทาน เลี้ยงง่าย อารมณ์ดี ไม่จุกจิก ปรับตัวเข้ากับครอบครัวได้รวดเร็ว'
    ],
    'low_shed' => [
        'name' => '🌿 ขนร่วงน้อย / ภูมิแพ้',
        'title' => '🌿 แมวขนร่วงน้อย ตอบโจทย์คนมีอาการภูมิแพ้',
        'desc' => 'ขนร่วงน้อยมากหรือไร้ขน ดูแลความสะอาดง่าย หมดกังวลเรื่องฝุ่นและภูมิแพ้ขนสัตว์'
    ]
];

// Helper: Filter cats by recommendation category
function getCatsByCategory($category_key = 'popular') {
    global $cats;
    if ($category_key === 'all' || empty($category_key)) {
        return $cats;
    }
    
    $filtered = [];
    foreach ($cats as $id => $cat) {
        if (in_array($category_key, $cat['categories'])) {
            $filtered[$id] = $cat;
        }
    }
    return $filtered;
}

// -------------------------------------------------------------
// 3. User & Member Management System (JSON-based)
// -------------------------------------------------------------
define('USERS_FILE', __DIR__ . '/data_users.json');
define('NEWSLETTERS_FILE', __DIR__ . '/data_newsletters.json');
define('MAIL_OUTBOX_FILE', __DIR__ . '/data_mail_outbox.json');
define('SMTP_CONFIG_FILE', __DIR__ . '/data_smtp.json');
require_once __DIR__ . '/smtp_mailer.php';
define('EMAILJS_CONFIG_FILE', __DIR__ . '/data_emailjs.json');
require_once __DIR__ . '/emailjs_mailer.php';


function getUsers() {
    if (!file_exists(USERS_FILE)) {
        $default = [
            [
                'id' => 'u_' . uniqid(),
                'fullname' => 'ผู้ทดสอบระบบ Cat Shop',
                'username' => 'catlover',
                'email' => 'catlover@example.com',
                'phone' => '0891234567',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'role' => 'customer',
                'consent_email' => true,
                'consent_phone' => true,
                'consent_terms' => true,
                'registered_at' => date('Y-m-d H:i:s')
            ]
        ];
        file_put_contents(USERS_FILE, json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $default;
    }
    
    $content = file_get_contents(USERS_FILE);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function findUserByEmailOrUsername($identifier) {
    $users = getUsers();
    $id_clean = trim(strtolower($identifier));
    foreach ($users as $user) {
        if (strtolower($user['email']) === $id_clean || strtolower($user['username']) === $id_clean) {
            return $user;
        }
    }
    return null;
}

function registerUser($fullname, $username, $email, $phone, $password, $consent_email, $consent_phone, $consent_terms) {
    $users = getUsers();
    
    foreach ($users as $u) {
        if (strtolower($u['email']) === strtolower(trim($email))) {
            return ['success' => false, 'message' => 'อีเมลนี้ถูกใช้งานในระบบแล้ว กรุณาใช้อีเมลอื่น'];
        }
        if (strtolower($u['username']) === strtolower(trim($username))) {
            return ['success' => false, 'message' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว กรุณาเลือกชื่ออื่น'];
        }
    }
    
    $newUser = [
        'id' => 'u_' . uniqid(),
        'fullname' => trim($fullname),
        'username' => trim($username),
        'email' => trim($email),
        'phone' => trim($phone),
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'customer',
        'avatar' => 'assets/images/logo.png',
        'consent_email' => (bool)$consent_email,
        'consent_phone' => (bool)$consent_phone,
        'consent_terms' => (bool)$consent_terms,
        'registered_at' => date('Y-m-d H:i:s')
    ];
    
    $users[] = $newUser;
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    $_SESSION['user'] = [
        'id' => $newUser['id'],
        'fullname' => $newUser['fullname'],
        'username' => $newUser['username'],
        'email' => $newUser['email'],
        'phone' => $newUser['phone'],
        'role' => $newUser['role'],
        'avatar' => $newUser['avatar'],
        'consent_email' => $newUser['consent_email'],
        'consent_phone' => $newUser['consent_phone']
    ];
    
    // Automatically send welcome email into user's inbox
    sendWelcomeEmailToUser($newUser['id'], $newUser['fullname'], $newUser['email']);
    
    return ['success' => true, 'user' => $_SESSION['user']];
}

function loginUser($identifier, $password) {
    $user = findUserByEmailOrUsername($identifier);
    if (!$user) {
        return ['success' => false, 'message' => 'ไม่พบบัญชีผู้ใช้นี้ในระบบ'];
    }
    
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'รหัสผ่านไม่ถูกต้อง'];
    }
    
    $_SESSION['user'] = [
        'id' => $user['id'],
        'fullname' => $user['fullname'],
        'username' => $user['username'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'role' => $user['role'] ?? 'customer',
        'avatar' => $user['avatar'] ?? 'assets/images/logo.png',
        'bank_name' => $user['bank_name'] ?? 'ธนาคารกสิกรไทย (KBANK)',
        'bank_account' => $user['bank_account'] ?? '',
        'bank_account_name' => $user['bank_account_name'] ?? '',
        'delivery_address' => $user['delivery_address'] ?? '',
        'consent_email' => $user['consent_email'] ?? true,
        'consent_phone' => $user['consent_phone'] ?? true
    ];
    
    return ['success' => true, 'user' => $_SESSION['user']];
}

function isUserLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['username']);
}

function isAdmin() {
    if (!isUserLoggedIn()) return false;
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function getCurrentUser() {
    if (!isUserLoggedIn()) return null;
    $user = $_SESSION['user'];
    if (empty($user['avatar'])) $user['avatar'] = 'assets/images/logo.png';
    return $user;
}

function updateUserProfile($userId, $data) {
    $users = getUsers();
    $found = false;

    foreach ($users as &$user) {
        if ($user['id'] === $userId || (isset($user['username']) && $user['username'] === $userId)) {
            $found = true;
            if (isset($data['fullname'])) $user['fullname'] = trim($data['fullname']);
            if (isset($data['phone'])) $user['phone'] = trim($data['phone']);
            if (isset($data['email'])) $user['email'] = trim($data['email']);
            if (isset($data['avatar']) && !empty($data['avatar'])) $user['avatar'] = trim($data['avatar']);
            if (isset($data['bank_name'])) $user['bank_name'] = trim($data['bank_name']);
            if (isset($data['bank_account'])) $user['bank_account'] = trim($data['bank_account']);
            if (isset($data['bank_account_name'])) $user['bank_account_name'] = trim($data['bank_account_name']);
            if (isset($data['delivery_address'])) $user['delivery_address'] = trim($data['delivery_address']);

            if (!empty($data['new_password'])) {
                $user['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
            }

            // Sync session
            $_SESSION['user']['fullname'] = $user['fullname'];
            $_SESSION['user']['phone'] = $user['phone'];
            $_SESSION['user']['email'] = $user['email'];
            $_SESSION['user']['avatar'] = $user['avatar'] ?? 'assets/images/logo.png';
            $_SESSION['user']['bank_name'] = $user['bank_name'] ?? '';
            $_SESSION['user']['bank_account'] = $user['bank_account'] ?? '';
            $_SESSION['user']['bank_account_name'] = $user['bank_account_name'] ?? '';
            $_SESSION['user']['delivery_address'] = $user['delivery_address'] ?? '';
            break;
        }
    }

    if ($found) {
        file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return ['success' => true, 'message' => 'บันทึกการตั้งค่าโปรไฟล์เรียบร้อยแล้ว!'];
    }

    return ['success' => false, 'message' => 'ไม่พบข้อมูลผู้ใช้ในระบบ'];
}

// -------------------------------------------------------------
// 3.1 Order & Adoption History Persistence (JSON-based)
// -------------------------------------------------------------
define('ORDERS_FILE', __DIR__ . '/data_orders.json');

function getOrders() {
    if (!file_exists(ORDERS_FILE)) {
        // Initial sample orders so users immediately see order history
        $initial = [
            [
                'order_id' => 'PFC-260901-7A2B',
                'user_id' => 'u_6a97f8b172436',
                'username' => 'catlover',
                'customer_name' => 'ผู้ทดสอบระบบ Cat Shop',
                'customer_phone' => '0891234567',
                'customer_email' => 'catlover@example.com',
                'delivery_address' => '123/45 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110',
                'delivery_date' => '2026-09-05',
                'delivery_timeslot' => 'ช่วงบ่าย (13:00 - 17:00 น.)',
                'items' => [
                    [
                        'id' => 'cat_british',
                        'name' => 'น้องมิลค์กี้ (British Shorthair)',
                        'breed' => 'บริติช ช็อตแฮร์ (British Shorthair)',
                        'price' => 25000,
                        'image' => 'cat_british.jpg',
                        'gender' => 'ผู้ (Male)',
                        'age' => '2.5 เดือน',
                        'qty' => 1
                    ]
                ],
                'cat_count' => 1,
                'subtotal' => 25000,
                'discount' => 1250,
                'vat' => 1662.50,
                'total' => 25412.50,
                'payment_channel' => '🏦 โอนเงินผ่านบัญชีธนาคาร',
                'payment_details' => 'ธนาคารกสิกรไทย (KBANK) • เลขบัญชี 098-7-65432-1',
                'status' => 'จัดส่งน้องแมวสำเร็จเรียบร้อยแล้ว (Delivered)',
                'created_at' => '2026-09-01 14:30:00'
            ],
            [
                'order_id' => 'PFC-260902-8F9C',
                'user_id' => 'u_6a97ff2d0b8b8',
                'username' => 'Meow',
                'customer_name' => 'Meow',
                'customer_phone' => '0634169812',
                'customer_email' => 'Meow@gmail.com',
                'delivery_address' => '88/9 คอนโดไอดีโอ ถนนพหลโยธิน แขวงจตุจักร เขตจตุจักร กรุงเทพฯ 10900',
                'delivery_date' => '2026-09-06',
                'delivery_timeslot' => 'ช่วงเช้า (09:00 - 12:00 น.)',
                'items' => [
                    [
                        'id' => 'cat_ragdoll',
                        'name' => 'น้องสโนว์ (Ragdoll Blue Bicolor)',
                        'breed' => 'แร็กดอลล์ (Ragdoll)',
                        'price' => 29000,
                        'image' => 'cat_ragdoll.jpg',
                        'gender' => 'เมีย (Female)',
                        'age' => '3 เดือน',
                        'qty' => 1
                    ],
                    [
                        'id' => 'cat_scottishfold',
                        'name' => 'น้องลูน่า (Scottish Fold)',
                        'breed' => 'สก็อตติช โฟลด์ (Scottish Fold)',
                        'price' => 18000,
                        'image' => 'cat_scottishfold.jpg',
                        'gender' => 'เมีย (Female)',
                        'age' => '2 เดือน',
                        'qty' => 1
                    ]
                ],
                'cat_count' => 2,
                'subtotal' => 47000,
                'discount' => 2350,
                'vat' => 3125.50,
                'total' => 47775.50,
                'payment_channel' => '📱 สแกนพร้อมเพย์ (PromptPay QR)',
                'payment_details' => 'PromptPay 089-123-4567 • บจก. เพอร์เฟกต์ แคท ช็อป',
                'status' => 'ชำระเงินแล้ว / กำลังเตรียมส่งมอบ (Paid & Preparing)',
                'created_at' => '2026-09-02 16:45:00'
            ]
        ];
        file_put_contents(ORDERS_FILE, json_encode($initial, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $initial;
    }

    $content = file_get_contents(ORDERS_FILE);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function saveOrder($orderData) {
    $orders = getOrders();
    array_unshift($orders, $orderData);
    file_put_contents(ORDERS_FILE, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return true;
}

function getUserOrders($userIdOrEmail) {
    $orders = getOrders();
    $matched = [];
    $id_clean = trim(strtolower($userIdOrEmail));
    foreach ($orders as $order) {
        $orderUser = strtolower($order['user_id'] ?? '');
        $orderEmail = strtolower($order['customer_email'] ?? '');
        $orderUsername = strtolower($order['username'] ?? '');
        if ($orderUser === $id_clean || $orderEmail === $id_clean || $orderUsername === $id_clean) {
            $matched[] = $order;
        }
    }
    return $matched;
}

function getOrderById($orderId) {
    $orders = getOrders();
    $id_clean = trim(strtoupper($orderId));
    foreach ($orders as $order) {
        if (trim(strtoupper($order['order_id'] ?? '')) === $id_clean) {
            return $order;
        }
    }
    return null;
}

function updateOrderStatus($orderId, $newStatus) {
    $orders = getOrders();
    $found = false;
    $id_clean = trim(strtoupper($orderId));
    foreach ($orders as &$order) {
        if (trim(strtoupper($order['order_id'] ?? '')) === $id_clean) {
            $order['status'] = $newStatus;
            $order['updated_at'] = date('Y-m-d H:i:s');
            $found = true;
            break;
        }
    }
    if ($found) {
        file_put_contents(ORDERS_FILE, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    return false;
}

function deleteOrder($orderId) {
    $orders = getOrders();
    $newOrders = [];
    $found = false;
    $id_clean = trim(strtoupper($orderId));
    foreach ($orders as $order) {
        if (trim(strtoupper($order['order_id'] ?? '')) === $id_clean) {
            $found = true;
            continue;
        }
        $newOrders[] = $order;
    }
    if ($found) {
        file_put_contents(ORDERS_FILE, json_encode($newOrders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    return false;
}

// User & Role Management by Admin
function updateUserRole($userId, $newRole) {
    $users = getUsers();
    $found = false;
    foreach ($users as &$user) {
        if ($user['id'] === $userId || (isset($user['username']) && $user['username'] === $userId)) {
            $user['role'] = $newRole;
            $found = true;
            if (isset($_SESSION['user']) && ($_SESSION['user']['id'] === $userId || $_SESSION['user']['username'] === $userId)) {
                $_SESSION['user']['role'] = $newRole;
            }
            break;
        }
    }
    if ($found) {
        file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    return false;
}

function deleteUser($userId) {
    $users = getUsers();
    $newUsers = [];
    $found = false;
    foreach ($users as $user) {
        if ($user['id'] === $userId || (isset($user['username']) && $user['username'] === $userId)) {
            if (($user['username'] ?? '') === 'admin') {
                return ['success' => false, 'message' => 'ไม่สามารถลบบัญชีผู้ดูแลระบบหลัก (admin) ได้'];
            }
            $found = true;
            continue;
        }
        $newUsers[] = $user;
    }
    if ($found) {
        file_put_contents(USERS_FILE, json_encode($newUsers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return ['success' => true, 'message' => 'ลบบัญชีผู้ใช้เรียบร้อยแล้ว'];
    }
    return ['success' => false, 'message' => 'ไม่พบบัญชีผู้ใช้'];
}

function addUserByAdmin($data) {
    $users = getUsers();
    $email = trim($data['email'] ?? '');
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '123456';
    $role = $data['role'] ?? 'customer';
    
    foreach ($users as $u) {
        if (strtolower($u['email']) === strtolower($email)) {
            return ['success' => false, 'message' => 'อีเมลนี้ถูกใช้งานแล้ว'];
        }
        if (strtolower($u['username']) === strtolower($username)) {
            return ['success' => false, 'message' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว'];
        }
    }
    
    $newUser = [
        'id' => 'u_' . uniqid(),
        'fullname' => trim($data['fullname'] ?? $username),
        'username' => $username,
        'email' => $email,
        'phone' => trim($data['phone'] ?? ''),
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role,
        'consent_email' => true,
        'consent_phone' => true,
        'consent_terms' => true,
        'registered_at' => date('Y-m-d H:i:s'),
        'avatar' => 'assets/images/logo.png'
    ];
    $users[] = $newUser;
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return ['success' => true, 'user' => $newUser];
}

// -------------------------------------------------------------
// Newsletter & Marketing Messages System
// -------------------------------------------------------------
function getMailOutbox() {
    if (!file_exists(MAIL_OUTBOX_FILE)) {
        return [];
    }
    $content = file_get_contents(MAIL_OUTBOX_FILE);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function getSmtpConfig() {
    $defaults = [
        'enabled' => false,
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => '',
        'password' => '',
        'from_email' => 'purrfect.cattery.shop@gmail.com',
        'from_name' => 'Purrfect Cattery & Boutique 🐾',
        'last_tested' => null,
        'last_status' => 'ยังไม่ได้ทดสอบเชื่อมต่อ'
    ];
    if (!file_exists(SMTP_CONFIG_FILE)) {
        return $defaults;
    }
    $content = file_get_contents(SMTP_CONFIG_FILE);
    $data = json_decode($content, true);
    if (!is_array($data)) {
        return $defaults;
    }
    return array_merge($defaults, $data);
}

function saveSmtpConfig($newConfig) {
    $current = getSmtpConfig();
    $merged = array_merge($current, $newConfig);
    file_put_contents(SMTP_CONFIG_FILE, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return $merged;
}

function getEmailJsConfig() {
    $defaults = [
        'enabled' => false,
        'service_id' => '',
        'template_id' => 'template_xt7cq3g',
        'template_welcome' => 'template_xt7cq3g',
        'template_subscribe' => 'template_y8wnrlt',
        'public_key' => '',
        'private_key' => '',
        'from_name' => 'Purrfect Cattery & Boutique 🐾',
        'from_email' => 'purrfect.cattery.shop@gmail.com',
        'last_tested' => null,
        'last_status' => 'ยังไม่ได้ทดสอบเชื่อมต่อ (ปิดโหมดส่งจริง EmailJS)'
    ];
    if (!file_exists(EMAILJS_CONFIG_FILE)) {
        return $defaults;
    }
    $content = file_get_contents(EMAILJS_CONFIG_FILE);
    $data = json_decode($content, true);
    if (!is_array($data)) {
        return $defaults;
    }
    return array_merge($defaults, $data);
}

function saveEmailJsConfig($newConfig) {
    $current = getEmailJsConfig();
    $merged = array_merge($current, $newConfig);
    file_put_contents(EMAILJS_CONFIG_FILE, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return $merged;
}

function getActiveMailDriver() {
    $emailjs = getEmailJsConfig();
    if (!empty($emailjs['enabled']) && !empty($emailjs['service_id']) && !empty($emailjs['template_id']) && !empty($emailjs['public_key'])) {
        return 'emailjs';
    }
    $smtp = getSmtpConfig();
    if (!empty($smtp['enabled']) && !empty($smtp['username']) && !empty($smtp['password'])) {
        return 'smtp';
    }
    return 'outbox';
}

function sendActualEmail($toEmail, $toName, $subject, $htmlBody, $voucherCode = 'WELCOME15', $userId = '', $senderInfo = [], $templateType = 'welcome') {
    $smtpConfig = getSmtpConfig();
    $emailJsConfig = getEmailJsConfig();
    $activeDriver = getActiveMailDriver();

    $senderName = !empty($senderInfo['name']) ? trim($senderInfo['name']) : ($smtpConfig['from_name'] ?? 'Purrfect Cattery & Boutique 🐾');
    $senderEmail = !empty($senderInfo['email']) ? trim($senderInfo['email']) : ($smtpConfig['from_email'] ?? 'purrfect.cattery.shop@gmail.com');
    $senderAvatar = !empty($senderInfo['avatar']) ? trim($senderInfo['avatar']) : 'assets/images/logo.png';
    $senderUserId = !empty($senderInfo['id']) ? trim($senderInfo['id']) : '';

    if (!empty($senderName)) {
        $smtpConfig['from_name'] = $senderName;
        $emailJsConfig['from_name'] = $senderName;
    }
    if (!empty($senderEmail) && filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
        $smtpConfig['from_email'] = $senderEmail;
        $emailJsConfig['from_email'] = $senderEmail;
    }

    // Determine the active EmailJS Template ID based on templateType:
    // - 'welcome' => template_xt7cq3g (สมัครสมาชิก)
    // - 'subscribe' / 'newsletter' => template_y8wnrlt (รับข่าวสาร)
    // - 'order' => template บัญชีที่ 2 (ยืนยันคำสั่งซื้อ) — ต้องกรอก credentials ใน data_emailjs.json
    $currentEmailJsConfig = $emailJsConfig;
    if ($templateType === 'subscribe' || $templateType === 'newsletter') {
        $currentEmailJsConfig['template_id'] = !empty($emailJsConfig['template_subscribe']) ? $emailJsConfig['template_subscribe'] : 'template_y8wnrlt';
    } elseif ($templateType === 'order') {
        // Order Confirmation: ใช้ EmailJS Account ที่ 2 (Gmail บัญชีอื่น)
        if (!empty($emailJsConfig['order_enabled']) && $emailJsConfig['order_enabled'] &&
            !empty($emailJsConfig['order_service_id']) && !empty($emailJsConfig['order_template_id']) && !empty($emailJsConfig['order_public_key'])) {
            $currentEmailJsConfig['service_id']  = $emailJsConfig['order_service_id'];
            $currentEmailJsConfig['template_id'] = $emailJsConfig['order_template_id'];
            $currentEmailJsConfig['public_key']  = $emailJsConfig['order_public_key'];
            if (!empty($emailJsConfig['order_from_name']))  $currentEmailJsConfig['from_name']  = $emailJsConfig['order_from_name'];
            if (!empty($emailJsConfig['order_from_email'])) $currentEmailJsConfig['from_email'] = $emailJsConfig['order_from_email'];
        } else {
            // Fallback: ยังไม่ได้ตั้งค่า account ที่ 2 — ใช้ account หลัก + template_welcome ไปก่อน
            $currentEmailJsConfig['template_id'] = !empty($emailJsConfig['template_welcome']) ? $emailJsConfig['template_welcome'] : ($emailJsConfig['template_id'] ?? 'template_xt7cq3g');
        }
    } else {
        $currentEmailJsConfig['template_id'] = !empty($emailJsConfig['template_welcome']) ? $emailJsConfig['template_welcome'] : ($emailJsConfig['template_id'] ?? 'template_xt7cq3g');
    }


    $liveResult = null;
    $deliveryStatus = 'LOCAL_LOGGED';
    $deliveryMode = 'LOCAL_OUTBOX';
    $serverNotice = '';

    // 1. Dispatch according to active driver
    if ($activeDriver === 'emailjs') {
        $liveResult = EmailJsMailer::send($toEmail, $toName, $subject, $htmlBody, $currentEmailJsConfig, [
            'voucher_code' => $voucherCode,
            'user_id' => $userId,
            'from_name' => $senderName,
            'sender_name' => $senderName,
            'sender_email' => $senderEmail,
            'sender_avatar' => $senderAvatar,
            'template_type' => $templateType
        ]);
        $deliveryMode = 'LIVE_EMAILJS';
        if ($liveResult['success']) {
            $deliveryStatus = 'DELIVERED_VIA_EMAILJS';
            $serverNotice = $liveResult['message'] ?? '200 OK Accepted by EmailJS';
        } else {
            $deliveryStatus = 'EMAILJS_FAILED';
            $serverNotice = $liveResult['message'] ?? 'EmailJS Delivery Failed';
        }
    } elseif ($activeDriver === 'smtp') {
        $liveResult = SmtpMailer::send($toEmail, $toName, $subject, $htmlBody, $smtpConfig);
        $deliveryMode = 'LIVE_SMTP';
        if ($liveResult['success']) {
            $deliveryStatus = 'DELIVERED_VIA_SMTP';
            $serverNotice = $liveResult['response'] ?? '250 OK Message accepted';
        } else {
            $deliveryStatus = 'SMTP_FAILED';
            $serverNotice = $liveResult['message'] ?? 'SMTP Delivery Failed';
        }
    } else {
        // Fallback: Local Outbox Mode
        $deliveryMode = 'LOCAL_OUTBOX';
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$senderName} <{$senderEmail}>\r\n";
        $headers .= "Reply-To: {$senderEmail}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $mailSent = @mail($toEmail, $subject, $htmlBody, $headers);
        $deliveryStatus = $mailSent ? 'DISPATCHED_TO_MTA' : 'LOCAL_LOGGED';
        $serverNotice = $mailSent ? 'Dispatched via local MTA' : 'โหมดจำลองในระบบ (ยังไม่เปิดใช้งานส่งจริง - ไม่พบการเชื่อมต่อ SMTP หรือ EmailJS)';
    }

    // 2. Record in Outbox Log
    $outbox = getMailOutbox();
    $logEntry = [
        'id' => 'mail_' . uniqid(),
        'to_email' => trim($toEmail),
        'to_name' => trim($toName),
        'sender_id' => $senderUserId,
        'sender_name' => $senderName,
        'sender_email' => $senderEmail,
        'sender_avatar' => $senderAvatar,
        'subject' => trim($subject),
        'voucher_code' => trim($voucherCode),
        'preview' => mb_strimwidth(strip_tags($htmlBody), 0, 180, '...'),
        'status' => (strpos($deliveryStatus, 'DELIVERED') !== false || $deliveryStatus === 'DISPATCHED_TO_MTA') ? 'SENT_SUCCESS' : ($deliveryStatus === 'SMTP_FAILED' || $deliveryStatus === 'EMAILJS_FAILED' ? 'FAILED' : 'SENT_SUCCESS'),
        'delivery_mode' => $deliveryMode,
        'delivery_status' => $deliveryStatus,
        'server_notice' => $serverNotice,
        'sent_at' => date('Y-m-d H:i:s'),
        'user_id' => $userId
    ];
    array_unshift($outbox, $logEntry);
    file_put_contents(MAIL_OUTBOX_FILE, json_encode($outbox, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // 3. Also register into customer's portal inbox so the user can view it in profile.php
    $newsletters = getNewsletters();
    $inboxMessage = [
        'id' => 'wc_' . uniqid(),
        'type' => 'welcome',
        'user_id' => $userId,
        'target_email' => trim($toEmail),
        'target_name' => trim($toName),
        'title' => trim($subject),
        'message' => 'ขอต้อนรับคุณ ' . $toName . ' สู่คอมมูนิตี้คนรักแมวสายพันธุ์แท้! สิทธิพิเศษต้อนรับสมาชิกใหม่ ลดทันที 15% (โค้ด: ' . $voucherCode . ') สำหรับการรับเลี้ยงน้องแมวตัวแรก พร้อมตรวจสุขภาพและจัดส่งรถตู้แอร์ปรับอุณหภูมิฟรีถึงหน้าบ้าน',
        'voucher_code' => trim($voucherCode),
        'sender_id' => $senderUserId,
        'sender_name' => $senderName,
        'sender_email' => $senderEmail,
        'sender_avatar' => $senderAvatar,
        'sent_at' => date('Y-m-d H:i:s')
    ];
    array_unshift($newsletters, $inboxMessage);
    file_put_contents(NEWSLETTERS_FILE, json_encode($newsletters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return [
        'log' => $logEntry,
        'live_result' => $liveResult,
        'smtp_result' => $liveResult
    ];
}


function getNewsletters() {
    if (!file_exists(NEWSLETTERS_FILE)) {
        return [];
    }
    $content = file_get_contents(NEWSLETTERS_FILE);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function sendNewsletterCampaign($title, $message, $selected_item_ids = [], $target_audience = 'all', $sent_by = 'admin') {
    $newsletters = getNewsletters();
    $newCampaign = [
        'id' => 'nl_' . uniqid(),
        'type' => 'broadcast',
        'title' => trim($title),
        'message' => trim($message),
        'items' => is_array($selected_item_ids) ? $selected_item_ids : [],
        'target_audience' => $target_audience,
        'sent_by' => $sent_by,
        'sent_at' => date('Y-m-d H:i:s')
    ];
    array_unshift($newsletters, $newCampaign);
    file_put_contents(NEWSLETTERS_FILE, json_encode($newsletters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return $newCampaign;
}

function sendWelcomeEmailToUser($userId, $fullname, $email) {
    $voucher_code = 'WELCOME15';
    require_once __DIR__ . '/email_member_welcome.php';
    $member_id = strtoupper(substr(md5($email), 0, 8));
    $rendered_email = renderMemberWelcomeEmail($fullname, $email, $voucher_code, $member_id);

    $subject = '🎉 ยินดีต้อนรับสู่ครอบครัว Purrfect Shop! รับโค้ดลด 15% (โค้ด: ' . $voucher_code . ')';

    // Dispatch email (will send via Live SMTP/EmailJS if active, or log to Outbox & Inbox)
    $res = sendActualEmail($email, $fullname, $subject, $rendered_email, $voucher_code, $userId, [], 'welcome');
    return $res['log'] ?? [];
}

function sendNewsletterSubscribeEmail($toEmail, $toName = '') {
    $voucher_code = 'CATNEWS10';
    $recipient_name = !empty($toName) ? $toName : 'คนรักน้องแมว';
    $recipient_email = trim($toEmail);
    
    require_once __DIR__ . '/email_newsletter_subscribe.php';
    $rendered_email = renderNewsletterSubscribeEmail($recipient_name, $recipient_email, $voucher_code);

    $subject = "📬 [ยืนยันการรับข่าวสาร] ยินดีต้อนรับสู่ Purrfect Cat Club + โค้ดส่วนลด 10% ({$voucher_code})";

    $res = sendActualEmail($recipient_email, $recipient_name, $subject, $rendered_email, $voucher_code, '', [], 'subscribe');
    return $res['log'] ?? [];
}

/**
 * sendOrderConfirmationEmail — ส่งอีเมลยืนยันคำสั่งซื้อหลัง checkout สำเร็จ
 * ใช้ EmailJS Account ที่ 2 (order_service_id / order_template_id / order_public_key)
 *
 * หากส่งไม่สำเร็จ จะบันทึกลง data_email_queue.json เพื่อ retry ภายหลัง
 * (checkout จะเสร็จทันที ไม่ค้างรอ EmailJS)
 *
 * @param array $orderData  Array จาก $newOrder ใน cart.php
 * @return array  log result
 */
function sendOrderConfirmationEmail(array $orderData): array {
    $customerName    = $orderData['customer_name']    ?? 'ลูกค้า';
    $customerEmail   = trim($orderData['customer_email'] ?? '');
    $orderId         = $orderData['order_id']         ?? 'N/A';
    $items           = $orderData['items']            ?? [];
    $subtotal        = (float) ($orderData['subtotal']  ?? 0);
    $discount        = (float) ($orderData['discount']  ?? 0);
    $vat             = (float) ($orderData['vat']       ?? 0);
    $total           = (float) ($orderData['total']     ?? 0);
    $paymentChannel  = $orderData['payment_channel']  ?? '';
    $deliveryDate    = $orderData['delivery_date']    ?? '';
    $deliveryAddress = $orderData['delivery_address'] ?? '';
    $deliverySlot    = $orderData['delivery_timeslot'] ?? '';

    if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        return ['status' => 'SKIPPED', 'reason' => 'ไม่มีอีเมลลูกค้า หรือ email ไม่ถูกต้อง'];
    }

    // บันทึก queue ก่อน — checkout ไม่ค้างแม้ EmailJS จะช้า
    $queueFile = __DIR__ . '/data_email_queue.json';
    $queue = [];
    if (file_exists($queueFile)) {
        $queue = json_decode(file_get_contents($queueFile), true) ?: [];
    }
    $queueEntry = [
        'id'           => 'q_' . uniqid(),
        'type'         => 'order',
        'order_id'     => $orderId,
        'to_email'     => $customerEmail,
        'to_name'      => $customerName,
        'order_data'   => $orderData,
        'queued_at'    => date('Y-m-d H:i:s'),
        'status'       => 'pending',
        'attempts'     => 0,
        'last_error'   => ''
    ];
    $queue[] = $queueEntry;
    file_put_contents($queueFile, json_encode($queue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    // ลองส่งทันที (ถ้าส่งสำเร็จ อัพเดต status เป็น sent)
    require_once __DIR__ . '/email_order_confirmation.php';
    $rendered = renderOrderConfirmationEmail(
        $customerName, $customerEmail, $orderId,
        $items, $subtotal, $discount, $vat, $total,
        $paymentChannel, $deliveryDate, $deliveryAddress, $deliverySlot
    );
    $subject = "✅ [ยืนยันคำสั่งซื้อ #{$orderId}] ขอบคุณที่ซื้อสินค้ากับ Purrfect Shop! 🐾";

    $res = sendActualEmail($customerEmail, $customerName, $subject, $rendered, '', '', [], 'order');
    $log = $res['log'] ?? [];

    // อัพเดต queue status ตามผลลัพธ์
    $sent = ($log['status'] ?? '') === 'SENT_SUCCESS';
    $queue_updated = json_decode(file_get_contents($queueFile), true) ?: [];
    foreach ($queue_updated as &$entry) {
        if ($entry['id'] === $queueEntry['id']) {
            $entry['status']     = $sent ? 'sent' : 'failed';
            $entry['attempts']   = 1;
            $entry['last_error'] = $sent ? '' : ($log['server_notice'] ?? 'unknown error');
            $entry['sent_at']    = $sent ? date('Y-m-d H:i:s') : '';
            break;
        }
    }
    unset($entry);
    file_put_contents($queueFile, json_encode($queue_updated, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    return $log;
}

/**
 * retryPendingOrderEmails — ลองส่งอีเมลที่ค้างใน queue ใหม่อีกครั้ง
 * เรียกจาก admin หรือ welcome_sales_mockup
 */
function retryPendingOrderEmails(): array {
    $queueFile = __DIR__ . '/data_email_queue.json';
    if (!file_exists($queueFile)) return ['retried' => 0, 'success' => 0];

    $queue = json_decode(file_get_contents($queueFile), true) ?: [];
    $retried = 0; $success = 0;

    require_once __DIR__ . '/email_order_confirmation.php';

    foreach ($queue as &$entry) {
        if (($entry['status'] ?? '') !== 'pending' && ($entry['status'] ?? '') !== 'failed') continue;
        if (($entry['attempts'] ?? 0) >= 5) continue; // หยุด retry หลัง 5 ครั้ง

        $od = $entry['order_data'] ?? [];
        $rendered = renderOrderConfirmationEmail(
            $od['customer_name']    ?? '',
            $od['customer_email']   ?? $entry['to_email'],
            $od['order_id']         ?? $entry['order_id'],
            $od['items']            ?? [],
            (float)($od['subtotal'] ?? 0),
            (float)($od['discount'] ?? 0),
            (float)($od['vat']      ?? 0),
            (float)($od['total']    ?? 0),
            $od['payment_channel']  ?? '',
            $od['delivery_date']    ?? '',
            $od['delivery_address'] ?? '',
            $od['delivery_timeslot']?? ''
        );
        $subject = "✅ [ยืนยันคำสั่งซื้อ #{$entry['order_id']}] ขอบคุณที่ซื้อสินค้ากับ Purrfect Shop! 🐾";
        $res = sendActualEmail($entry['to_email'], $entry['to_name'], $subject, $rendered, '', '', [], 'order');
        $log = $res['log'] ?? [];
        $sent = ($log['status'] ?? '') === 'SENT_SUCCESS';

        $entry['attempts']   = ($entry['attempts'] ?? 0) + 1;
        $entry['status']     = $sent ? 'sent' : 'failed';
        $entry['last_error'] = $sent ? '' : ($log['server_notice'] ?? '');
        if ($sent) $entry['sent_at'] = date('Y-m-d H:i:s');

        $retried++;
        if ($sent) $success++;
    }
    unset($entry);

    file_put_contents($queueFile, json_encode($queue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    return ['retried' => $retried, 'success' => $success];
}


function getCustomerMessages($userEmail, $userId = '') {
    $newsletters = getNewsletters();
    $matched = [];
    $e_clean = strtolower(trim($userEmail));
    $u_clean = strtolower(trim($userId));

    foreach ($newsletters as $item) {
        if ($item['type'] === 'broadcast') {
            $matched[] = $item;
        } elseif ($item['type'] === 'welcome') {
            $targetEmail = strtolower($item['target_email'] ?? '');
            $targetId = strtolower($item['user_id'] ?? '');
            if ($targetEmail === $e_clean || ($u_clean && $targetId === $u_clean)) {
                $matched[] = $item;
            }
        }
    }
    return $matched;
}

// -------------------------------------------------------------
// 4. Cart Helper Functions & Starter Bundles
// -------------------------------------------------------------
$starter_bundles = [
    'bundle_starter' => [
        'id' => 'bundle_starter',
        'name' => 'ชุดทาสแมวมือใหม่ (Newborn Starter Kit)',
        'breed' => 'แพ็กเกจของใช้ทาสแมวครบเซ็ต',
        'description' => 'คอนโดแมว 3 ชั้นบุนวมนุ่ม + กระบะทรายอัตโนมัติ + ชามน้ำพุกรองไอออน + อาหารโฮลิสติก 5kg + ขนมแมวเลีย 1 โหล',
        'price' => 2490,
        'image' => 'logo.png',
        'age' => 'ครบชุดพร้อมใช้',
        'gender' => 'ของใช้พรีเมียม',
        'vaccine' => 'รับประกันอุปกรณ์ 1 ปี',
        'pedigree' => 'Food-Grade ปลอดภัย 100%',
        'original_price' => 4990,
        'badge' => '🔥 ขายดีอันดับ 1'
    ],
    'bundle_spa' => [
        'id' => 'bundle_spa',
        'name' => 'ชุดสปา & สุขภาพพรีเมียม (Royal Spa & Wellness Kit)',
        'breed' => 'แพ็กเกจบริการและสุขภาพสัตว์เลี้ยง',
        'description' => 'บัตรสปา กรูมมิ่ง อาบน้ำตัดขน 3 ครั้ง + ประกันสุขภาพ 1 ปี + ชุดวิตามินบำรุงขน + แปรงนวดสแตนเลส',
        'price' => 3490,
        'image' => 'logo.png',
        'age' => 'ความคุ้มครอง 1 ปี',
        'gender' => 'บริการ & สุขภาพ',
        'vaccine' => 'รวมประกันอุบัติเหตุ 10,000 บาท',
        'pedigree' => 'รพ.สัตว์ชั้นนำร่วมรายการ',
        'original_price' => 6500,
        'badge' => '⭐ แนะนำสำหรับสมาชิก'
    ],
    'bundle_vip' => [
        'id' => 'bundle_vip',
        'name' => 'แพ็กเกจพร้อมอยู่ All-Inclusive (Ultimate VIP Pack)',
        'breed' => 'แพ็กเกจระดับพรีเมียมครบวงจร',
        'description' => 'รวมเซ็ต 1+2 พร้อมกรงเดินทางขึ้นเครื่องบินมาตรฐาน IATA + ปลอกคอ GPS ติดตามตำแหน่ง + เครื่องให้อาหาร Wi-Fi',
        'price' => 5990,
        'image' => 'logo.png',
        'age' => 'บริการระดับ VIP',
        'gender' => 'พรีเมียม ออล-อิน-วัน',
        'vaccine' => 'รับประกันอุปกรณ์ 2 ปี',
        'pedigree' => 'เกรดส่งออกระดับสากล',
        'original_price' => 11500,
        'badge' => '👑 คุ้มค่าที่สุด'
    ]
];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function addToCart($cat_id) {
    global $cats, $starter_bundles;
    $item = $cats[$cat_id] ?? ($starter_bundles[$cat_id] ?? null);
    if (!$item) {
        return false;
    }
    
    if (isset($_SESSION['cart'][$cat_id])) {
        $_SESSION['cart'][$cat_id]['qty']++;
    } else {
        $_SESSION['cart'][$cat_id] = [
            'id' => $cat_id,
            'name' => $item['name'],
            'breed' => $item['breed'],
            'price' => $item['price'],
            'image' => $item['image'],
            'age' => $item['age'] ?? 'ครบชุด',
            'gender' => $item['gender'] ?? 'ของใช้พรีเมียม',
            'vaccine' => $item['vaccine'] ?? 'รับประกันคุณภาพ',
            'pedigree' => $item['pedigree'] ?? 'มาตรฐานรับรอง',
            'qty' => 1
        ];
    }
    return true;
}

function getCartCount() {
    $count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['qty'];
        }
    }
    return $count;
}

function getCartSubtotal() {
    $subtotal = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }
    }
    return $subtotal;
}

function getMemberDiscount($subtotal) {
    if (isUserLoggedIn() && $subtotal > 0) {
        return round($subtotal * 0.05);
    }
    return 0;
}

function getCartTotal() {
    $subtotal = getCartSubtotal();
    $discount = getMemberDiscount($subtotal);
    return max(0, $subtotal - $discount);
}
?>
