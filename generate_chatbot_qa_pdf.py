import os
import subprocess
import shutil

base_dir = os.path.dirname(os.path.abspath(__file__))
html_path = os.path.join(base_dir, "chatbot_knowledge_base.html")
pdf_path = os.path.join(base_dir, "Purrfect_Shop_Chatbot_Knowledge_Base.pdf")

html_content = """<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ฐานข้อมูลความรู้ถาม-ตอบสำหรับเทรน LINE AI Chatbot - Purrfect Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 11mm 13mm 11mm 13mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Prompt', 'Sarabun', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            font-size: 12.5px;
            line-height: 1.48;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .page {
            page-break-after: always;
            page-break-inside: avoid;
            height: 273mm;
            max-height: 273mm;
            position: relative;
            padding-bottom: 22px;
            overflow: hidden;
        }
        .page:last-child {
            page-break-after: avoid;
        }
        
        /* Header & Footer on each page */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #fecdd3;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .page-header .brand-title {
            font-size: 12px;
            font-weight: 700;
            color: #e11d48;
            letter-spacing: 0.5px;
        }
        .page-header .doc-tag {
            font-size: 10.5px;
            color: #9f1239;
            background: #fff1f2;
            padding: 2px 8px;
            border-radius: 6px;
            border: 1px solid #fecdd3;
            font-weight: 600;
        }
        .page-footer {
            position: absolute;
            bottom: 0px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 4px;
            font-size: 9.5px;
            color: #94a3b8;
        }

        /* Section Headings */
        .section-header {
            background: linear-gradient(135deg, #fff1f2 0%, #fff7ed 100%);
            border: 1.5px solid #fecdd3;
            border-left: 5px solid #e11d48;
            border-radius: 8px;
            padding: 6px 12px;
            margin-bottom: 9px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-badge {
            background: #e11d48;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 5px;
        }
        .section-title {
            font-size: 14.5px;
            font-weight: 700;
            color: #881337;
        }
        .section-subtitle {
            font-size: 11px;
            color: #64748b;
            margin-left: auto;
        }

        /* QA Block Styling */
        .qa-card {
            background: #ffffff;
            border: 1px solid #fecdd3;
            border-radius: 8px;
            margin-bottom: 7.5px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(225, 29, 72, 0.03);
        }
        .qa-q {
            background: #fff1f2;
            color: #9f1239;
            font-weight: 600;
            padding: 5px 10px;
            font-size: 12px;
            border-bottom: 1px solid #ffe4e6;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .qa-q .q-tag {
            background: #e11d48;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 4px;
        }
        .qa-a {
            background: #ffffff;
            color: #334155;
            padding: 6px 10px;
            font-size: 11.5px;
            line-height: 1.45;
        }
        .qa-a strong {
            color: #0f172a;
        }
        .qa-a .highlight {
            color: #be123c;
            font-weight: 600;
        }
        .qa-a ul {
            padding-left: 16px;
            margin-top: 3px;
        }
        .qa-a ul li {
            margin-bottom: 2px;
        }

        /* Key-Value Tag */
        .kv-tag {
            display: inline-block;
            background: #fef2f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
            font-size: 10.5px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 4px;
            margin-right: 4px;
        }
        .bot-tip {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10.5px;
            margin-top: 3px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <!-- =================================================================== -->
    <!-- PAGE 1: TOPIC 1 - GENERAL BUSINESS INFO (Q1 - Q6) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 1: ข้อมูลทั่วไปของร้าน</span>
        </div>

        <div class="section-header">
            <span class="section-badge">01</span>
            <h2 class="section-title">ข้อมูลทั่วไปของร้านและธุรกิจ (General Business Information)</h2>
            <span class="section-subtitle">ถาม-ตอบข้อมูลพื้นฐานองค์กรและช่องทางติดต่อ</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q1</span> ร้านชื่ออะไร และดำเนินธุรกิจประเภทไหน?</div>
            <div class="qa-a">
                <strong>A:</strong> ร้านของเราชื่อ <strong>Purrfect Shop (เพอร์เฟกต์ ช็อป)</strong> ดำเนินธุรกิจเป็นฟาร์มเพาะพันธุ์น้องแมวสายพันธุ์แท้ 100% ตามมาตรฐานสากล พร้อมทั้งเป็นบูติกจำหน่ายอาหาร อุปกรณ์ ของใช้สัตว์เลี้ยงเกรดพรีเมียม และบริการจัดส่งสัตว์เลี้ยงปลอดภัยทั่วประเทศไทยครับ 🐾
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q2</span> สโลแกนและแนวคิดหลักของร้าน Purrfect Shop คืออะไร?</div>
            <div class="qa-a">
                <strong>A:</strong> สโลแกนหลักของร้านเราคือ <em>"เพราะทุกบ้านควรมีเจ้าเหมียว"</em> (Every Home Deserves A Purrfect Cat) โดยเรามุ่งเน้นการส่งมอบน้องแมวที่มีสุขภาพแข็งแรง เลี้ยงง่าย อารมณ์ดี พร้อมการดูแลอย่างจริงใจและเป็นกันเองตลอดอายุขัยครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q3</span> จุดเด่นที่ทำให้ร้าน Purrfect Shop แตกต่างจากฟาร์มแมวทั่วไปมีอะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> จุดเด่นสำคัญ 4 ประการของร้านเรา ได้แก่:
                <ul>
                    <li><span class="highlight">สายพันธุ์แท้ 100%:</span> มีใบเพ็ดดีกรีรับรองมาตรฐานสากลจากสมาคมระดับโลก (CFA / TICA / WCF)</li>
                    <li><span class="highlight">การันตีสุขภาพยาวนานที่สุดถึง 180 วัน:</span> คุ้มครองโรคติดต่อร้ายแรง (FPV, FeLV, FIV, FIP)</li>
                    <li><span class="highlight">ตรวจสุขภาพครบวงจร:</span> ฉีดวัคซีนครบ 2 เข็ม ฝังไมโครชิป และถ่ายพยาธิเรียบร้อยก่อนส่งมอบ</li>
                    <li><span class="highlight">ส่งฟรีทั่วไทยด้วย Pet Taxi ปรับอากาศ:</span> รถควบคุมอุณหภูมิ 25°C มีพี่เลี้ยงดูแลตลอดทาง</li>
                </ul>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q4</span> หน้าร้านและฟาร์มเปิดทำการเวลาใด และติดต่อได้ช่องทางไหนบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> หน้าร้านเปิดบริการ <strong>ทุกวัน เวลา 09:00 – 19:00 น.</strong> (นัดหมายเข้าชมล่วงหน้า) ส่วนช่องทางแชทออนไลน์เปิดให้บริการ <strong>ตลอด 24 ชั่วโมง</strong> ครับ โดยติดต่อได้ทาง:
                <ul>
                    <li><strong>LINE Official Account:</strong> @purrfectshop (มี @ นำหน้า)</li>
                    <li><strong>เบอร์โทรศัพท์สายด่วน:</strong> 02-888-9999 หรือ 095-888-7777</li>
                    <li><strong>Facebook Page:</strong> Purrfect Shop (facebook.com/purrfectshop)</li>
                    <li><strong>อีเมล:</strong> contact@purrfectshop.com</li>
                </ul>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q5</span> ที่อยู่หน้าร้านของ Purrfect Shop ตั้งอยู่ที่ไหน?</div>
            <div class="qa-a">
                <strong>A:</strong> ตั้งอยู่ที่ <strong>เลขที่ 88/9 อาคารเพอร์เฟกต์ ทาวเวอร์ ถนนสุขุมวิท 71 แขวงพระโขนงเหนือ เขตวัฒนา กรุงเทพมหานคร 10110</strong> (มีที่จอดรถสะดวกสบาย หรือเดินทางด้วย BTS พระโขนง) ครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q6</span> สามารถสั่งซื้อสินค้าและจองน้องแมวผ่านช่องทางใดได้บ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> สั่งซื้อได้ผ่านเว็บไซต์หลัก <code>https://patches660.github.io/Purrfect-Shop/</code>, ผ่านระบบ LINE Shop และสามารถทักแชทสั่งจองกับแอดมินหรือบอทใน LINE OA @purrfectshop ได้โดยตรงตลอด 24 ชั่วโมงครับ
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 1 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 2: TOPIC 2 - PRODUCTS & SERVICES CATALOG (Q7 - Q12) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 2: สินค้าและบริการ (ตอนที่ 1)</span>
        </div>

        <div class="section-header">
            <span class="section-badge">02</span>
            <h2 class="section-title">ข้อมูลสินค้าและบริการ (Products & Services - Part 1)</h2>
            <span class="section-subtitle">ถาม-ตอบรายละเอียดสายพันธุ์แมวยอดนิยม</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q7</span> ข้อมูลและราคาน้องแมวพันธุ์ British Shorthair (บริติช ช็อตแฮร์) เป็นอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> <span class="kv-tag">ราคา: 32,000 – 42,000 บาท</span> <span class="kv-tag">ใบรับรอง: WCF / CFA</span><br>
                • <strong>ลักษณะเด่น:</strong> หัวกลมโต หน้ากลมแป้น แก้มยุ้ย ขนสั้นแน่นนุ่มดั่งกำมะหยี่ โครงสร้าง Cobby ล่ำสมบูรณ์<br>
                • <strong>นิสัย:</strong> นิ่ง สุภาพ อารมณ์ดี ไม่ส่งเสียงร้องโวยวาย เลี้ยงง่ายมาก เหมาะอย่างยิ่งกับผู้ที่อยู่คอนโดหรือทำงานนอกบ้าน<br>
                • <strong>สถานะ:</strong> มีลูกแมวพร้อมย้ายบ้านและเปิดจองต่อเนื่องครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q8</span> ข้อมูลและราคาน้องแมวพันธุ์ Scottish Fold (สกอตติช โฟลด์) เป็นอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> <span class="kv-tag">ราคา: 28,000 – 38,000 บาท</span> <span class="kv-tag">ใบรับรอง: WCF</span><br>
                • <strong>ลักษณะเด่น:</strong> หูพับสนิทแนบกะโหลก ตากลมโตเหมือนนกฮูก หน้าตาน่ารักน่าเอ็นดู มีทั้งขนสั้นและขนยาว<br>
                • <strong>นิสัย:</strong> ขี้อ้อนสุดๆ ชอบนอนหงายพุง ติดเจ้าของมาก ชอบให้ลูบตัว อ่อนโยนและเป็นมิตรกับทุกคน<br>
                • <strong>สถานะ:</strong> พร้อมย้ายบ้านหลังตรวจพันธุกรรมกระดูกข้อต่อเรียบร้อย 100% ครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q9</span> ข้อมูลและราคาน้องแมวพันธุ์ Maine Coon (เมนคูน ยักษ์ใหญ่ใจดี) เป็นอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> <span class="kv-tag">ราคา: 48,000 – 65,000 บาท</span> <span class="kv-tag">ใบรับรอง: CFA / TICA</span><br>
                • <strong>ลักษณะเด่น:</strong> แมวบ้านสายพันธุ์ใหญ่ที่สุดในโลก โครงสร้างกระดูกใหญ่ ขนแผงคอหนาฟู หางเป็นพวงสง่างาม<br>
                • <strong>นิสัย:</strong> ฉลาดมาก อ่อนโยน รักน้ำ ชอบเล่นของเล่น สามารถฝึกใส่สายจูงเดินเล่นได้เหมือนสุนัข<br>
                • <strong>สถานะ:</strong> เปิดรับจองล่วงหน้าสำหรับลูกแมวเกรด Show & Breed ครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q10</span> ข้อมูลและราคาน้องแมวพันธุ์ Ragdoll (แร็กดอลล์ เจ้าหญิงตาสีฟ้า) เป็นอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> <span class="kv-tag">ราคา: 29,000 – 39,000 บาท</span> <span class="kv-tag">ใบรับรอง: TICA</span><br>
                • <strong>ลักษณะเด่น:</strong> ตาสีฟ้าครามประกายเพชร ขนยาวนุ่มฟู มาร์คกิ้งคมชัด ตัวนิ่มปวกเปียกเวลาอุ้มเหมือนตุ๊กตาผ้า<br>
                • <strong>นิสัย:</strong> อ่อนหวาน ใจดี ไม่กางเล็บ ปลอดภัยสำหรับบ้านที่มีเด็กเล็กและผู้สูงอายุ 100%<br>
                • <strong>สถานะ:</strong> มีพร้อมย้ายบ้าน ตรวจยีนหัวใจ HCM ผ่านสมบูรณ์ครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q11</span> ข้อมูลและราคาน้องแมวพันธุ์ Persian Classic (เปอร์เซีย คลาสสิก) เป็นอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> <span class="kv-tag">ราคา: 16,500 – 24,000 บาท</span> <span class="kv-tag">ใบรับรอง: CFA</span><br>
                • <strong>ลักษณะเด่น:</strong> ราชินีขนยาว ขนหนาสองชั้นนุ่มฟู หน้าหวาน ตากลมโต สวยหรูสง่างาม<br>
                • <strong>นิสัย:</strong> รักความสงบ นิ่ง เรียบร้อย ไม่ชอบความวุ่นวาย ชอบนอนพักผ่อนบนเบาะนุ่มๆ<br>
                • <strong>สถานะ:</strong> พร้อมย้ายบ้าน ตรวจสุขภาพและฉีดวัคซีนครบถ้วนครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q12</span> น้องแมวทุกตัวก่อนย้ายบ้านได้รับการดูแลและตรวจสุขภาพอะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> น้องแมวทุกตัวจะได้รับการ:
                <ul>
                    <li>ฉีดวัคซีนรวมป้องกันไข้หัดและหวัดแมวครบ 2 เข็ม</li>
                    <li>ตรวจสุขภาพทั่วไป ช่องปาก ดวงตา หู ข้อต่อ และตรวจแล็บปลอดโรค FeLV/FIV</li>
                    <li>ถ่ายพยาธิ ป้องกันเห็บหมัด และฝังไมโครชิปสากล 15 หลัก พร้อมสมุดวัคซีนประจำตัวครับ</li>
                </ul>
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 2 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 3: TOPIC 2 (CONT) & TOPIC 3 - CATEGORIES (Q13 - Q18) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 2 (ต่อ) & หมวดที่ 3: หมวดหมู่สินค้า</span>
        </div>

        <div class="section-header">
            <span class="section-badge">02-03</span>
            <h2 class="section-title">สายพันธุ์เพิ่มเติม & การจัดหมวดหมู่สินค้า (Categories)</h2>
            <span class="section-subtitle">ถาม-ตอบสายพันธุ์อื่นๆ และกลุ่มสินค้าในร้าน</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q13</span> ข้อมูลน้องแมวพันธุ์ Munchkin (มันช์กิ้น ขาสั้น) และ Bengal (เบงกอล เสือดาวจิ๋ว)?</div>
            <div class="qa-a">
                <strong>A:</strong>
                <ul>
                    <li><strong>Munchkin (24,000 – 32,000 ฿):</strong> แมวขาสั้นเตี้ยวิ่งดุ๊กดิ๊ก น่ารัก ร่าเริง ซุกซน แข็งแรง คล่องแคล่ว ข้อต่อผ่านการตรวจสุขภาพ</li>
                    <li><strong>Bengal (26,000 – 35,000 ฿):</strong> ลายกุหลาบ Rosette สวยคมชัดเหมือนเสือดาวจิ๋ว กล้ามเนื้อแน่น ฉลาด ปราดเปรียว ชอบเล่นน้ำและใส่สายจูงได้</li>
                </ul>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q14</span> ข้อมูลน้องแมวพันธุ์ Siamese (วิเชียรมาศ) และ Canadian Sphynx (สฟิงซ์)?</div>
            <div class="qa-a">
                <strong>A:</strong>
                <ul>
                    <li><strong>Siamese / วิเชียรมาศ (12,000 – 16,000 ฿):</strong> แมวมงคลไทย แต้มสี 9 จุด ตาสีฟ้าคราม ช่างพูด ช่างเจรจา ฉลาด ซื่อสัตย์และผูกพันกับเจ้าของมาก</li>
                    <li><strong>Canadian Sphynx (28,000 – 38,000 ฿):</strong> แมวไร้ขน ผิวนุ่มอุ่นดั่งลูกพีช ขี้อ้อนติดคนมาก ขนร่วง 0% ปลอดภัยสำหรับคนเป็นโรคภูมิแพ้ขนสัตว์</li>
                </ul>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q15</span> แพ็กเกจ Starter Kit มีอะไรบ้าง และราคาเท่าไร?</div>
            <div class="qa-a">
                <strong>A:</strong> ทางร้านมีชุดอุปกรณ์เริ่มต้น 3 รูปแบบ:
                <ul>
                    <li><strong>Starter Kit Basic (1,890 ฿):</strong> กระบะทราย + ทรายเต้าหู้ 2 ถุง + ชามอาหารคู่ + ของเล่น 3 ชิ้น</li>
                    <li><strong>Starter Kit Deluxe (4,500 ฿):</strong> กระเป๋าแคปซูลอวกาศ + คอนโดมินิ + อาหารเกรด Holistic 2 กก. + น้ำพุไร้สาย + ขนมแมวเลีย (แจกฟรีสำหรับโปรเปิดร้าน!)</li>
                    <li><strong>VIP Health Care Box (3,200 ฿):</strong> แชมพูออร์แกนิก + หวีสแตนเลส + วิตามินบำรุงขน + ยาหยอดเห็บหมัด</li>
                </ul>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q16</span> สินค้ากลุ่มขายดี (Best Sellers) ของร้าน Purrfect Shop มีอะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> สินค้าขายดีอันดับ 1 คือ <strong>น้องแมว British Shorthair (Blue)</strong> ตามด้วย <strong>Scottish Fold หูพับ</strong>, <strong>ชุด Starter Kit Deluxe 11 รายการ</strong>, และ <strong>ทรายแมวเต้าหู้พรีเมียมไร้ฝุ่น 99.9%</strong> ครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q17</span> สินค้ากลุ่มเข้าใหม่ (New Arrivals) ในช่วงนี้มีอะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> ได้แก่ ลูกแมวแร็กดอลล์และเมนคูนครอกใหม่สายเลือดแชมป์, น้ำพุแมวไร้สายระบบกรอง 5 ชั้น (แบตเตอรี่ 60 วัน), คอนโดแมวไม้แท้มินิมอล 1.6 เมตร, และขนมแมวเลียสูตรบำรุงไตนำเข้าจากญี่ปุ่นครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q18</span> ร้าน Purrfect Shop มีการจัดแบ่งหมวดหมู่สินค้าตามประเภทอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> แบ่งออกเป็น 4 หมวดหมู่หลัก ได้แก่:
                <br>1) <strong>หมวดน้องแมว:</strong> ขนสั้น, ขนยาว, ไซส์ยักษ์, ขาสั้น, แมวมงคลไทย
                <br>2) <strong>หมวดอาหารและโภชนาการ:</strong> Holistic Grain-Free, Freeze Dried, นมแพะแท้
                <br>3) <strong>หมวดสุขอนามัยและของใช้:</strong> ทรายแมวเต้าหู้, กระบะทราย, ห้องน้ำอัตโนมัติ, สเปรย์ดับกลิ่น
                <br>4) <strong>หมวดบริการ:</strong> จองแมวออนไลน์, Pet Taxi, ฝากเลี้ยงโรงแรมแมว
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 3 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 4: TOPIC 4 - MATCHING GUIDE (Q19 - Q24) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 4: การเลือกให้เหมาะกับลูกค้า</span>
        </div>

        <div class="section-header">
            <span class="section-badge">04</span>
            <h2 class="section-title">การเลือกสินค้าและน้องแมวให้เหมาะกับลูกค้า (Matching Guide)</h2>
            <span class="section-subtitle">ถาม-ตอบการจับคู่สายพันธุ์ตาม Lifestyle และงบประมาณ</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q19</span> ถ้าลูกค้าพักอาศัยอยู่คอนโดหรือหอพัก ควรแนะนำน้องแมวสายพันธุ์ไหน?</div>
            <div class="qa-a">
                <strong>A:</strong> แนะนำสายพันธุ์ <strong>British Shorthair</strong> หรือ <strong>Persian Classic</strong> ครับ เพราะทั้งสองสายพันธุ์นี้นิสัยนิ่ง สุภาพมาก ไม่ส่งเสียงร้องรบกวนเพื่อนบ้าน ไม่ต้องการพื้นที่วิ่งเล่นกว้างขวาง และสามารถนอนพักผ่อนอย่างมีความสุขในพื้นที่คอนโดได้เป็นอย่างดีครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q20</span> ถ้าในบ้านมีเด็กเล็กหรือผู้สูงอายุ ควรแนะนำน้องแมวสายพันธุ์ใด?</div>
            <div class="qa-a">
                <strong>A:</strong> แนะนำ <strong>Ragdoll (แร็กดอลล์)</strong> หรือ <strong>Scottish Fold (สกอตติช โฟลด์)</strong> ครับ เนื่องจากเป็นแมวที่มีความอดทนสูง ใจดี อ่อนโยนเป็นพิเศษ ไม่กางเล็บเวลาถูกอุ้มหรือกอด และมีความเป็นมิตรกับเด็กๆ สูงมาก ปลอดภัย 100% ครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q21</span> ถ้าลูกค้าหรือคนในบ้านเป็นโรคภูมิแพ้ขนแมว แนะนำสายพันธุ์ไหนได้บ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> แนะนำน้อง <strong>Canadian Sphynx (สฟิงซ์ไร้ขน)</strong> หรือ <strong>Devon Rex</strong> ครับ เพราะเป็นแมวที่ไม่มีขนหลุดร่วงในอากาศ ทำให้ไม่สะสมไรฝุ่นและโปรตีน Fel d 1 ในอากาศ ช่วยลดอาการระคายเคืองและภูมิแพ้ได้เกือบ 100% ครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q22</span> เจ้าของต้องทำงานนอกบ้านทั้งวัน ไม่ค่อยมีเวลาเล่นด้วย แนะนำพันธุ์ไหน?</div>
            <div class="qa-a">
                <strong>A:</strong> แนะนำ <strong>British Shorthair</strong> หรือ <strong>American Shorthair</strong> ครับ เพราะเป็นแมวที่รักอิสระ สามารถพึ่งพาและดูแลตัวเองได้ดี ไม่เกิดภาวะซึมเศร้าหรือเครียดง่ายเมื่อต้องอยู่ตัวเดียว เพียงเตรียมอาหาร น้ำ และของเล่นไว้ก็อยู่ได้อย่างสบายใจครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q23</span> ลูกค้าที่ชอบแมวพลังเยอะ ฉลาด ชอบทำกิจกรรมร่วมกับเจ้าของ เหมาะกับพันธุ์ไหน?</div>
            <div class="qa-a">
                <strong>A:</strong> แนะนำ <strong>Bengal (เบงกอล)</strong> หรือ <strong>Maine Coon (เมนคูน)</strong> ครับ เพราะมีความเฉลียวฉลาดสูง ร่าเริง ชอบเล่นน้ำ สามารถฝึกคำสั่งและฝึกใส่สายจูงพาเดินเล่นนอกบ้านได้เหมือนสุนัขครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q24</span> การแนะนำน้องแมวตามระดับงบประมาณของลูกค้า มีแนวทางอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong>
                <ul>
                    <li><span class="highlight">งบประหยัด (12,000 – 20,000 ฿):</span> แนะนำ วิเชียรมาศ (Siamese), เปอร์เซียคลาสสิก (Persian)</li>
                    <li><span class="highlight">งบมาตรฐานยอดนิยม (25,000 – 38,000 ฿):</span> แนะนำ British Shorthair, Scottish Fold, Munchkin ขาสั้น, Ragdoll</li>
                    <li><span class="highlight">งบพรีเมียม (40,000 – 65,000+ ฿):</span> แนะนำ Maine Coon ไจแอนท์, Bengal ลาย Rosette เกรดประกวด, Sphynx นำเข้า</li>
                </ul>
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 4 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 5: TOPIC 5 - COMPARISON MATRIX (Q25 - Q30) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 5: ข้อมูลเปรียบเทียบ</span>
        </div>

        <div class="section-header">
            <span class="section-badge">05</span>
            <h2 class="section-title">ข้อมูลเปรียบเทียบสินค้าและสายพันธุ์ (Comparison & Differences)</h2>
            <span class="section-subtitle">ถาม-ตอบข้อแตกต่างของสายพันธุ์และแพ็กเกจรับเลี้ยง</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q25</span> น้องแมว British Shorthair กับ Scottish Fold ต่างกันอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> 
                <br>• <strong>British Shorthair:</strong> หูตั้ง หน้ากลมแป้น ตัวแน่นตัน นิสัยค่อนข้างนิ่ง สุภาพ รักความสงบ ขนสั้นแน่นหนา
                <br>• <strong>Scottish Fold:</strong> โดดเด่นด้วยหูพับสนิท ตากลมโตเหมือนลูกนกฮูก นิสัยจะขี้อ้อน ติดคนมากกว่า และชอบนอนหงายพุงเล่นกับเจ้าของครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q26</span> น้องแมว Maine Coon กับ Ragdoll แตกต่างกันอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong>
                <br>• <strong>Maine Coon:</strong> ตัวใหญ่ที่สุด หน้าคมโครงสร้างสง่างาม หางพวง แผงคอใหญ่ นิสัยกระฉับกระเฉง ชอบสำรวจ ชอบน้ำ
                <br>• <strong>Ragdoll:</strong> ขนาดตัวปานกลางค่อนข้างใหญ่ ตาสีฟ้าคราม ขนนุ่มฟูสีอ่อน นิสัยนุ่มนิ่ม ปวกเปียก ไม่ชอบปีนป่ายสูง รักการถูกอุ้มกอด
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q27</span> เปรียบเทียบระดับการดูแลขนของแต่ละสายพันธุ์ (ขนร่วง/การหวีขน)?</div>
            <div class="qa-a">
                <strong>A:</strong>
                <ul>
                    <li><strong>ดูแลขนง่ายมาก (แทบไม่ต้องหวี):</strong> Sphynx (ไม่มีขนเลย), Bengal, Siamese</li>
                    <li><strong>ดูแลขนง่าย (หวีสัปดาห์ละ 1-2 ครั้ง):</strong> British Shorthair, Scottish Fold (Short), Munchkin (Short)</li>
                    <li><strong>ดูแลขนปานกลาง-สม่ำเสมอ (หวีสัปดาห์ละ 3-4 ครั้ง):</strong> Ragdoll, Maine Coon, Persian Classic</li>
                </ul>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q28</span> แพ็กเกจ Standard Tier กับ Premium Tier ของทางฟาร์มต่างกันอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong>
                <br>• <strong>Standard Tier:</strong> ใบเพ็ดดีกรีสมาคมไทย, การันตีสุขภาพ 30 วัน, วัคซีน 2 เข็ม, Basic Kit (1,890฿), ส่งฟรีเฉพาะ กทม.
                <br>• <strong>Premium Tier (แนะนำ):</strong> ใบเพ็ดดีกรี WCF/CFA สากล, <span class="highlight">การันตีสุขภาพยาวนาน 180 วันเต็ม</span>, ตรวจแล็บ FeLV/FIV + ฝังชิป, Deluxe Kit 11 รายการ (4,500฿), และ <span class="highlight">ส่งฟรี Pet Taxi ทั่วประเทศไทย</span> ครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q29</span> เปรียบเทียบทรายแมวเต้าหู้ของร้าน กับทรายแมวภูเขาไฟทั่วไป?</div>
            <div class="qa-a">
                <strong>A:</strong> ทรายเต้าหู้ของ Purrfect Shop ทำจากวัตถุดิบธรรมชาติ 100% <strong>ไร้ฝุ่น 99.9%</strong> ไม่ติดเท้าแมว สามารถตักทิ้งลงชักโครกได้โดยไม่อุดตัน ดับกลิ่นได้ดีเยี่ยม ปลอดภัยต่อระบบทางเดินหายใจของลูกแมวมากกว่าทรายภูเขาไฟทั่วไปครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q30</span> แมวเพศผู้กับเพศเมีย นิสัยและการดูแลแตกต่างกันอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> โดยทั่วไปเพศผู้จะมีโครงสร้างกระดูกและแก้มที่ใหญ่กว่า นิสัยมักจะขี้อ้อน ติดคน และติดเล่นมากกว่า ส่วนเพศเมียจะตัวกะทัดรัดกว่า รักความสะอาด เรียบร้อย และหวงพื้นที่มากกว่าเล็กน้อย ทั้งนี้หลังทำหมันแล้วนิสัยทั้งสองเพศจะอ่อนโยนและนิ่งขึ้นเหมือนกันครับ
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 5 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 6: TOPIC 6 - PROMOTIONS & PRIVILEGES (Q31 - Q36) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 6: โปรโมชั่น & สิทธิพิเศษ</span>
        </div>

        <div class="section-header">
            <span class="section-badge">06</span>
            <h2 class="section-title">โปรโมชั่น คูปอง และสิทธิพิเศษ (Promotions & Vouchers)</h2>
            <span class="section-subtitle">ถาม-ตอบแคมเปญส่งเสริมการขายและโค้ดส่วนลด</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q31</span> ตอนนี้มีโปรโมชั่นต้อนรับสมาชิกใหม่อะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> สมาชิกใหม่รับสิทธิ์ 2 ต่อทันที:
                <br>1) ใช้โค้ด <code>CAT10OFF</code> รับส่วนลดทันที <strong>10%</strong> เมื่อช้อปขั้นต่ำ 300 บาท
                <br>2) รับฟรีชุด <strong>Starter Kit Deluxe 11 รายการ มูลค่า 4,500 บาท</strong> ทันทีเมื่อจองน้องแมวทุกสายพันธุ์ในเดือนนี้ครับ 🎉
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q32</span> โค้ดส่วนลดอื่นๆ ที่สามารถใช้งานได้มีอะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> 
                <ul>
                    <li><code>ADOPT25</code> : ลด 25% ค่าสินสอดสำหรับน้องแมวตัวที่ 2 หรือกรณีรับเลี้ยงเป็นคู่</li>
                    <li><code>FOOD25OFF</code> : ลด 25% หมวดอาหารและทรายแมว เมื่อสั่งซื้อ 2 ถุงขึ้นไป</li>
                    <li><code>FREESHIP</code> : รับสิทธิ์บริการจัดส่งรถ Pet Taxi ปรับอากาศฟรีทั่วประเทศไทย</li>
                </ul>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q33</span> มีโปรโมชั่นผ่อนชำระ 0% หรือไม่ และมีเงื่อนไขอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> มีครับ! ทางร้านมีโปรโมชั่น <strong>ผ่อนชำระ 0% นาน 3, 6, และ 10 เดือน</strong> ผ่านบัตรเครดิตธนาคารกสิกรไทย (KBank), ไทยพาณิชย์ (SCB), เคทีซี (KTC), กรุงเทพ (BBL) และกรุงศรี (Krungsri) สำหรับยอดชำระ 10,000 บาทขึ้นไปครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q34</span> กิจกรรมวงล้อนำโชค Lucky Wheel คืออะไร และเล่นอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> เป็นมินิเกมบนเว็บไซต์และ LINE OA ให้สมาชิกลุ้นรับรางวัลฟรีวันละ 1 ครั้ง ของรางวัลประกอบด้วย คูปองส่วนลด 100 - 1,000 บาท, ขนมแมวเลียฟรี, และสิทธิ์อัปเกรด Starter Kit ฟรีครับ เข้าเล่นได้ที่หน้า <code>/lucky_wheel.html</code>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q35</span> เงื่อนไขการใช้คูปองส่วนลดมีข้อจำกัดอะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong>
                <ul>
                    <li>โค้ดส่วนลด 1 โค้ด สามารถใช้ได้ 1 ครั้ง ต่อ 1 บัญชีผู้ใช้งาน</li>
                    <li>ไม่สามารถนำคูปองมาแลกเปลี่ยนหรือทอนเป็นเงินสดได้</li>
                    <li>ของแถม Starter Kit จะจัดส่งไปพร้อมกับน้องแมวในวันส่งมอบตัวจริงครับ</li>
                </ul>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q36</span> เมื่อลูกค้าถามหาโปรโมชั่นในแชท บอทควรตอบอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> บอทจะแจ้งโค้ด <code>CAT10OFF</code> ลด 10% พร้อมแจ้งสิทธิพิเศษของแถม Starter Kit Deluxe 11 รายการ และแนบลิงก์หน้า <code>/welcome_deal.html</code> ให้ลูกค้ากดรับสิทธิ์ได้ทันทีครับ
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 6 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 7: TOPIC 7 - ORDERING PROCESS (Q37 - Q42) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 7: ขั้นตอนการสั่งซื้อ</span>
        </div>

        <div class="section-header">
            <span class="section-badge">07</span>
            <h2 class="section-title">ขั้นตอนการสั่งซื้อและระบบการจอง (Ordering & Booking Process)</h2>
            <span class="section-subtitle">ถาม-ตอบขั้นตอนการสั่งซื้อ จอง และมัดจำ</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q37</span> ขั้นตอนการสั่งจองน้องแมวผ่านระบบออนไลน์มีกี่ขั้นตอน อะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> มี 5 ขั้นตอนง่ายๆ ดังนี้ครับ:
                <br><strong>1. เลือกน้องแมว:</strong> ดูรูป วิดีโอ และรายละเอียดสายพันธุ์ที่ต้องการ
                <br><strong>2. กดจอง (Book Now):</strong> กรอกชื่อ-เบอร์โทรผู้รับเลี้ยง และเลือกวันที่สะดวกรับน้องแมว
                <br><strong>3. ใส่โค้ดส่วนลด:</strong> กรอกโค้ด เช่น <code>CAT10OFF</code> เพื่อรับสิทธิ์โปรโมชั่น
                <br><strong>4. ชำระเงินมัดจำ:</strong> จ่ายผ่าน QR Code หรือโอนเงิน 5,000 บาทเพื่อล็อคน้องแมว
                <br><strong>5. รับใบยืนยันคำสั่งซื้อ:</strong> รับเอกสารยืนยันและลิงก์ติดตามสถานะแบบเรียลไทม์
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q38</span> ยอดเงินมัดจำในการจองน้องแมวคิดเท่าไร?</div>
            <div class="qa-a">
                <strong>A:</strong> เงินมัดจำเริ่มต้นเพียง <strong>5,000 บาท ต่อตัว</strong> ครับ เพื่อให้ฟาร์มล็อคน้องแมวไว้ให้ลูกค้าท่านนั้น และยอดมัดจำนี้จะถูกนำไปหักลบออกจากราคาสินสอดเต็มจำนวนในวันส่งมอบน้องแมวครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q39</span> ถ้ายกเลิกการจอง จะได้เงินมัดจำคืนหรือไม่?</div>
            <div class="qa-a">
                <strong>A:</strong> หากก่อนวันส่งมอบ ทางฟาร์มตรวจพบปัญหาสุขภาพของน้องแมว หรือเหตุขัดข้องจากทางฟาร์ม ทางร้านยินดี <strong>คืนเงินมัดจำเต็มจำนวน 100% ทันที</strong> ครับ แต่หากลูกค้ายกเลิกด้วยเหตุผลส่วนตัว สามารถขอเลื่อนกำหนดรับหรือเปลี่ยนตัวใหม่ได้ภายใน 6 เดือนครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q40</span> สั่งซื้อสินค้าอุปกรณ์และอาหารแมว ทำอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> สามารถเลือกสินค้าใส่ตะกร้าผ่านหน้าเว็บ <code>/products.html</code> ระบุที่อยู่จัดส่ง และเลือกชำระเงินผ่าน PromptPay, โอนเงิน หรือเก็บเงินปลายทาง (COD) ได้ทันทีครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q41</span> จะทราบได้อย่างไรว่าคำสั่งซื้อหรือการจองสำเร็จเรียบร้อยแล้ว?</div>
            <div class="qa-a">
                <strong>A:</strong> ทันทีที่ชำระเงิน ระบบจะแสดงหน้า Order Confirmation พร้อมส่งข้อความแจ้งเตือนสรุปรายละเอียดและใบเสร็จไปยัง LINE และอีเมลของลูกค้าอัตโนมัติครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q42</span> ลูกค้าสามารถขอนัดดูน้องแมวตัวจริงก่อนตัดสินใจจองได้หรือไม่?</div>
            <div class="qa-a">
                <strong>A:</strong> ได้แน่นอนครับ! สามารถนัดหมายเข้าชมน้องแมวที่หน้าร้านล่วงหน้า 1 วัน หรือแจ้งแอดมินเพื่อขอนัด <strong>Video Call ชมความน่ารักแบบสดๆ</strong> ผ่าน LINE ได้ทันทีครับ 📹
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 7 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 8: TOPICS 8 & 9 - PAYMENT & SHIPPING (Q43 - Q48) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 8 & 9: ชำระเงิน & จัดส่ง</span>
        </div>

        <div class="section-header">
            <span class="section-badge">08-09</span>
            <h2 class="section-title">นโยบายการชำระเงิน และ ระบบการจัดส่ง (Payment & Logistics)</h2>
            <span class="section-subtitle">ถาม-ตอบช่องทางการเงินและความปลอดภัยการขนส่ง</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q43</span> ทางร้านรองรับช่องทางการชำระเงินใดบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> รองรับ 4 ช่องทางหลัก:
                <ul>
                    <li><strong>Thai PromptPay QR Code:</strong> สแกนจ่ายได้ทุกแอปธนาคาร ยืนยันยอดทันที</li>
                    <li><strong>โอนผ่านบัญชีธนาคาร:</strong> ธนาคารกสิกรไทย (KBANK) เลขที่บัญชี <code>888-2-55555-8</code> ชื่อบัญชี บจก. เพอร์เฟกต์ ช็อป</li>
                    <li><strong>บัตรเครดิต/เดบิต:</strong> Visa, Mastercard, JCB พร้อมระบบความปลอดภัย 3D Secure</li>
                    <li><strong>ผ่อนชำระ 0%:</strong> นานสูงสุด 10 เดือน สำหรับยอด 10,000 บาทขึ้นไป</li>
                </ul>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q44</span> มีบริการเก็บเงินปลายทาง (Cash on Delivery - COD) หรือไม่?</div>
            <div class="qa-a">
                <strong>A:</strong> สำหรับสินค้าอุปกรณ์ ของเล่น และอาหาร มีบริการเก็บเงินปลายทางครับ แต่สำหรับน้องแมวจะต้องชำระเงินมัดจำ 5,000 บาทล่วงหน้าเพื่อยืนยันการจอง และชำระส่วนที่เหลือในวันรับมอบครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q45</span> บริการส่งน้องแมวด้วย Pet Taxi มีมาตรฐานความปลอดภัยอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> รถ Pet Taxi ของเราเป็นรถตู้ VIP ปรับอากาศควบคุมอุณหภูมิที่ 24 – 25°C ตลอดเส้นทาง มีกล้องวงจรปิดภายในรถ และมีพี่เลี้ยงผู้เชี่ยวชาญคอยดูแล ให้น้ำ ให้อาหาร และเช็คความผ่อนคลายของน้องแมวตลอดการเดินทางครับ 🚐
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q46</span> การจัดส่งน้องแมวคิดค่าบริการเท่าไร และมีเงื่อนไขส่งฟรีไหม?</div>
            <div class="qa-a">
                <strong>A:</strong> <strong>ส่งฟรีทั่วประเทศไทย 100%</strong> สำหรับการรับเลี้ยงน้องแมวทุกสายพันธุ์จากฟาร์ม Purrfect Shop ไม่มีค่าจัดส่งเพิ่มเติมครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q47</span> ระยะเวลาในการจัดส่งน้องแมวและพัสดุอุปกรณ์ใช้เวลากี่วัน?</div>
            <div class="qa-a">
                <strong>A:</strong>
                <br>• <strong>น้องแมว (Pet Taxi):</strong> กทม.และปริมณฑลจัดส่งภายในวันที่นัดหมาย (Same-Day) / ต่างจังหวัด 1-2 วันทำการตามเวลานัดหมาย
                <br>• <strong>สินค้าและอุปกรณ์ (Flash/Kerry):</strong> กทม. 1-2 วันทำการ / ต่างจังหวัด 2-3 วันทำการ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q48</span> ลูกค้าสามารถติดตามสถานะการจัดส่งแบบ Real-time ได้ทางไหน?</div>
            <div class="qa-a">
                <strong>A:</strong> นำเลข Order ID หรือ Booking Code ไปกรอกตรวจสอบพิกัด GPS สดของรถ Pet Taxi และสถานะพัสดุได้ที่หน้าเว็บ <code>/tracking.html</code> ตลอด 24 ชั่วโมงครับ
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 8 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 9: TOPIC 10 - WARRANTY & CLAIMS (Q49 - Q54) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 10: การันตีสุขภาพ & เคลมสินค้า</span>
        </div>

        <div class="section-header">
            <span class="section-badge">10</span>
            <h2 class="section-title">การการันตีสุขภาพ 180 วัน และ การเคลมสินค้า (Warranty & Claims)</h2>
            <span class="section-subtitle">ถาม-ตอบนโยบายการคุ้มครองและแนวทางชดเชย</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q49</span> การันตีคุ้มครองสุขภาพน้องแมว 180 วัน ครอบคลุมโรคอะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> ครอบคลุม 4 โรคติดต่อร้ายแรงในแมว ได้แก่:
                <br>1) <strong>ไข้หัดแมว (FPV - Feline Panleukopenia)</strong>
                <br>2) <strong>โรคลิวคีเมียในแมว (FeLV - Feline Leukemia Virus)</strong>
                <br>3) <strong>โรคเอดส์แมว (FIV - Feline Immunodeficiency Virus)</strong>
                <br>4) <strong>โรคเยื่อบุช่องท้องอักเสบติดต่อ (FIP - Feline Infectious Peritonitis)</strong>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q50</span> หากตรวจพบว่าน้องแมวมีอาการป่วยในระยะประกัน มีแนวทางชดเชยอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> หากมีใบรับรองแพทย์ยืนยันจากโรงพยาบาลสัตว์มาตรฐาน ทางฟาร์มยินดีให้ลูกค้าเลือก 3 ทางเลือก:
                <br>• <strong>ทางเลือกที่ 1:</strong> ทางฟาร์มรับผิดชอบดูแลค่ารักษาพยาบาลให้ทั้งหมดจนหายขาด
                <br>• <strong>ทางเลือกที่ 2:</strong> เปลี่ยนน้องแมวตัวใหม่ในเกรดและสายพันธุ์เดียวกันให้ทันที
                <br>• <strong>ทางเลือกที่ 3:</strong> คืนเงินเต็มจำนวน 100% โดยไม่มีข้อโต้แย้งครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q51</span> มีการรับประกันโรคทางพันธุกรรม (Genetic Diseases) หรือไม่?</div>
            <div class="qa-a">
                <strong>A:</strong> มีครับ! ทางฟาร์มรับประกันโรคทางพันธุกรรม เช่น โรคกล้ามเนื้อหัวใจหนาตัวผิดปกติ (HCM) และโรคถุงน้ำในไต (PKD) ตลอดอายุขัย 1 ปีแรกครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q52</span> เอกสารที่ต้องใช้ในการยื่นเรื่องเคลมประกันสุขภาพน้องแมวมีอะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> ใช้เพียง 2 อย่าง ได้แก่: 1) ใบรับรองแพทย์และผลตรวจแล็บจากสัตวแพทย์ผู้มีใบประกอบวิชาชีพ และ 2) สมุดวัคซีนประจำตัวน้องแมวที่ออกโดยฟาร์ม Purrfect Shop ครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q53</span> นโยบายการคืนหรือเปลี่ยนสินค้าประเภทอุปกรณ์และของใช้เป็นอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> สามารถแจ้งเปลี่ยนหรือคืนได้ภายใน <strong>7 วัน</strong> หลังจากได้รับพัสดุ ในกรณีสินค้าชำรุด ใช้งานไม่ได้ หรือทางร้านจัดส่งผิดรุ่น โดยสินค้าต้องยังไม่ผ่านการใช้งานจริงและบรรจุภัณฑ์อยู่ในสภาพเดิมครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q54</span> หากพบว่าพัสดุอุปกรณ์เสียหายตอนแกะกล่อง ต้องทำอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> ให้ถ่ายภาพหรือคลิปวิดีโอขณะเปิดกล่องพัสดุ แล้วส่งมาที่ LINE @purrfectshop ทางร้านจะส่งสินค้าชิ้นใหม่ไปให้ทันทีโดยลูกค้าไม่ต้องเสียค่าใช้จ่ายใดๆ เพิ่มเติมครับ
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 9 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 10: TOPIC 11 - FAQ PART 1 (Q55 - Q60) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 11: คำถามที่พบบ่อย (ชุดที่ 1)</span>
        </div>

        <div class="section-header">
            <span class="section-badge">11</span>
            <h2 class="section-title">คำถามที่พบบ่อยสำหรับ AI Chatbot (FAQ Dataset - Set 1)</h2>
            <span class="section-subtitle">ถาม-ตอบข้อสงสัยยอดนิยมของลูกค้าทั่วไป</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q55</span> น้องแมวพร้อมย้ายบ้านเมื่ออายุเท่าไร?</div>
            <div class="qa-a">
                <strong>A:</strong> น้องแมวจะพร้อมส่งมอบย้ายบ้านที่อายุประมาณ <strong>2.5 – 3 เดือนขึ้นไป</strong> หลังจากได้รับวัคซีนครบ 2 เข็ม ตรวจแล็บผ่าน ทานอาหารเม็ดแข็งได้ดี และใช้กระบะทรายเป็นเรียบร้อยแล้วครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q56</span> มือใหม่ที่ไม่เคยเลี้ยงแมวมาก่อนเลย จะเลี้ยงยากไหม?</div>
            <div class="qa-a">
                <strong>A:</strong> ไม่ยากเลยครับ! ทางฟาร์มจะมอบคู่มือการดูแลลูกแมวอย่างละเอียด มีชุด Starter Kit พร้อมใช้ และมีทีมสัตวแพทย์กับแอดมินคอยให้คำแนะนำผ่าน LINE ตลอด 24 ชั่วโมงครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q57</span> น้องแมวมีใบเพ็ดดีกรีของสมาคมอะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> มีใบเพ็ดดีกรีรับรองมาตรฐานสากลจาก <strong>CFA (สหรัฐอเมริกา)</strong>, <strong>TICA (สากล)</strong>, และ <strong>WCF (เยอรมนี)</strong> ขึ้นอยู่กับสายพันธุ์และเกรดของน้องแมวครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q58</span> เอกสารใบเพ็ดดีกรีตัวจริงจะได้รับตอนไหน?</div>
            <div class="qa-a">
                <strong>A:</strong> ใบเพ็ดดีกรีฉบับจริงจากสมาคมต่างประเทศจะจัดส่งตามไปให้ทาง EMS ภายใน 14 – 30 วันหลังจากส่งมอบน้องแมวเรียบร้อยแล้วครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q59</span> น้องแมวที่ฟาร์มฝึกขับถ่ายในกระบะทรายหรือยัง?</div>
            <div class="qa-a">
                <strong>A:</strong> น้องแมวทุกตัวได้รับการฝึกฝนให้ขับถ่ายในกระบะทรายเต้าหู้อย่างถูกต้องและเป็นระเบียบ 100% ตั้งแต่ก่อนย้ายบ้านครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q60</span> ต้องเตรียมอุปกรณ์อะไรไว้ที่บ้านก่อนน้องแมวเดินทางไปถึงบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> หากรับสิทธิ์ Starter Kit ฟรีกับทางร้านแล้ว แทบไม่ต้องซื้อเพิ่มเลยครับ! เพียงเตรียมพื้นที่เงียบสงบในบ้าน ชามน้ำสะอาด และมุมวางกระบะทรายไว้ก็พร้อมต้อนรับน้องแมวเข้าบ้านได้ทันทีครับ
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 10 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 11: TOPIC 11 - FAQ PART 2 (Q61 - Q66) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 11: คำถามที่พบบ่อย (ชุดที่ 2)</span>
        </div>

        <div class="section-header">
            <span class="section-badge">11</span>
            <h2 class="section-title">คำถามที่พบบ่อยสำหรับ AI Chatbot (FAQ Dataset - Set 2)</h2>
            <span class="section-subtitle">ถาม-ตอบการดูแลสุขภาพและบริการเสริมพิเศษ</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q61</span> น้องแมวพันธุ์ Maine Coon โตเต็มที่จะมีขนาดตัวและน้ำหนักเท่าไร?</div>
            <div class="qa-a">
                <strong>A:</strong> เพศผู้โตเต็มวัยจะมีน้ำหนักประมาณ <strong>8 – 12 กิโลกรัม</strong> ลำตัวยาวได้ถึง 1 เมตร ส่วนเพศเมียจะหนักประมาณ 5 – 7 กิโลกรัม โดยจะเจริญเติบโตเต็มที่เมื่ออายุครบ 3 – 4 ปีครับ 🦁
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q62</span> ทางร้านมีระบบช่วยเตือนนัดฉีดวัคซีนประจำปีหรือไม่?</div>
            <div class="qa-a">
                <strong>A:</strong> มีครับ! ทางร้านมีระบบ <strong>Vaccine Reminder อัจฉริยะ</strong> แจ้งเตือนนัดฉีดวัคซีนและถ่ายพยาธิล่วงหน้าผ่าน LINE และตรวจสอบตารางได้ที่หน้าเว็บ <code>/vaccine_reminder.html</code>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q63</span> หากเจ้าของต้องเดินทางไปต่างจังหวัด มีบริการรับฝากเลี้ยงไหม?</div>
            <div class="qa-a">
                <strong>A:</strong> มีบริการ <strong>Purrfect Cat Hotel & Spa</strong> โรงแรมแมวระดับพรีเมียมห้องแอร์ส่วนตัว โดยลูกค้ารับเลี้ยงจากร้านเราจะได้รับสิทธิ์ส่วนลด 20% ตลอดชีพครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q64</span> มีบริการให้คำปรึกษาด้านพฤติกรรมแมว (เช่น แมวเครียด ปรับตัวไม่ทัน) หรือไม่?</div>
            <div class="qa-a">
                <strong>A:</strong> มีทีมผู้เชี่ยวชาญด้านพฤติกรรมสัตว์เลี้ยงคอยให้คำแนะนำฟรีตลอดชีพผ่าน LINE OA สามารถสอบถามเทคนิคการปรับตัว การเข้ากับแมวเดิมในบ้าน หรือการปรับอาหารได้ตลอดเวลาครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q65</span> ถ้าต้องการทำประกันสุขภาพสัตว์เลี้ยง ทางร้านมีแนะนำไหม?</div>
            <div class="qa-a">
                <strong>A:</strong> ทางร้านมีพันธมิตรประกันภัยสัตว์เลี้ยงชั้นนำ มอบสิทธิ์คุ้มครองค่ารักษาพยาบาลอุบัติเหตุและเจ็บป่วยต่อเนื่องหลังครบกำหนด 180 วันในราคาพิเศษสำหรับลูกค้า Purrfect Shop ครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q66</span> ลูกค้าสามารถส่งรีวิวรูปน้องแมวหลังรับเลี้ยงได้ทางช่องทางไหน?</div>
            <div class="qa-a">
                <strong>A:</strong> ส่งรูปและรีวิวมาที่แชท LINE @purrfectshop หรือโพสต์บนหน้า <code>/reviews.html</code> เพื่อรับคูปองส่วนลดซื้ออาหารและทรายแมว 200 บาทฟรีได้ทุกเดือนครับ
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 11 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 12: TOPICS 12 & 13 - AI RECOMMENDATION RULES (Q67 - Q72) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 12 & 13: ระบบแนะนำ & กฎตรรกะ AI</span>
        </div>

        <div class="section-header">
            <span class="section-badge">12-13</span>
            <h2 class="section-title">ระบบคัดกรอง และ กฎในการแนะนำ (AI Decision Tree & Logic Rules)</h2>
            <span class="section-subtitle">ถาม-ตอบชุดคำถามคัดกรองและตรรกะ If-Else</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q67</span> แชทบอทมีชุดคำถามคัดกรองเพื่อหาสายพันธุ์แมวที่ตรงใจลูกค้าอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> แชทบอทจะใช้คำถามคัดกรอง 4 ข้อง่ายๆ:
                <br>1) <strong>สถานที่อยู่อาศัย:</strong> คอนโด/หอพัก หรือ บ้านเดี่ยว/มีบริเวณ
                <br>2) <strong>ประสบการณ์:</strong> มือใหม่ไม่เคยเลี้ยง หรือ เคยเลี้ยงแมวมาก่อน
                <br>3) <strong>ลักษณะนิสัยที่ชอบ:</strong> นิ่งสงบ / ขี้อ้อนติดคน / ซุกซนร่าเริง
                <br>4) <strong>งบประมาณ:</strong> ไม่เกิน 20,000 ฿ / 20,000 – 40,000 ฿ / 40,000 ฿ ขึ้นไป
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q68</span> กฎตรรกะ: ถ้าลูกค้าอยู่คอนโด + เป็นมือใหม่ + ชอบแมวไม่ร้องกวน?</div>
            <div class="qa-a">
                <strong>A:</strong> <span class="highlight">IF (คอนโด AND มือใหม่ AND ชอบความสงบ)</span> ➡️ <strong>แนะนำ British Shorthair</strong> เพราะเป็นสายพันธุ์ยอดนิยมอันดับ 1 ดูแลขนง่าย ไม่ส่งเสียงร้องรบกวน ปรับตัวเข้ากับคอนโดได้ดีเยี่ยม พร้อมแนะนำ Starter Kit Deluxe ฟรีครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q69</span> กฎตรรกะ: ถ้าลูกค้ามีเด็กเล็กในบ้าน + ต้องการแมวขี้อ้อน ไม่ดุร้าย?</div>
            <div class="qa-a">
                <strong>A:</strong> <span class="highlight">IF (มีเด็กเล็ก AND ต้องการแมวใจดี)</span> ➡️ <strong>แนะนำ Ragdoll (แร็กดอลล์) หรือ Scottish Fold</strong> เพราะนิสัยอ่อนโยนมาก ตัวนิ่ม ไม่กางเล็บ อดทนสูง และปลอดภัยกับเด็กเล็ก 100% ครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q70</span> กฎตรรกะ: ถ้าลูกค้ามีประวัติเป็นโรคภูมิแพ้ขนแมว?</div>
            <div class="qa-a">
                <strong>A:</strong> <span class="highlight">IF (มีอาการภูมิแพ้ขนสัตว์)</span> ➡️ <strong>แนะนำ Canadian Sphynx (สฟิงซ์ไร้ขน)</strong> เพราะขนร่วง 0% ไม่กักเก็บไรฝุ่นในบ้าน และไม่ก่อให้เกิดการแพ้โปรตีนในขนแมวครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q71</span> กฎตรรกะ: ถ้าลูกค้างบประมาณจำกัดไม่เกิน 15,000 – 20,000 บาท?</div>
            <div class="qa-a">
                <strong>A:</strong> <span class="highlight">IF (งบประมาณ &lt;= 20,000 บาท)</span> ➡️ <strong>แนะนำ แมววิเชียรมาศ (Siamese) หรือ Persian Classic</strong> พร้อมแจ้งโปรโมชั่นผ่อนชำระ 0% นาน 10 เดือน เดือนละพันกว่าบาท เพื่อช่วยให้ตัดสินใจง่ายขึ้นครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q72</span> กฎตรรกะ: ถ้าลูกค้าถามหาของขวัญ หรือของใช้สำหรับน้องแมวตัวใหม่?</div>
            <div class="qa-a">
                <strong>A:</strong> <span class="highlight">IF (ต้องการของใช้เริ่มต้น / ของขวัญ)</span> ➡️ <strong>แนะนำ Deluxe Starter Kit 11 รายการ หรือ น้ำพุแมวไร้สายอัจฉริยะ</strong> เพราะเป็นเซ็ตยอดนิยมที่มีประโยชน์และคุ้มค่าที่สุดครับ
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 12 จาก 13</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 13: TOPIC 14 - CONTACT & AFTER-SALES (Q73 - Q78) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE (Q&A FORMAT)</span>
            <span class="doc-tag">หมวดที่ 14: ข้อมูลติดต่อ & บริการหลังการขาย</span>
        </div>

        <div class="section-header">
            <span class="section-badge">14</span>
            <h2 class="section-title">ข้อมูลติดต่อและบริการหลังการขาย (Contact & Support Protocol)</h2>
            <span class="section-subtitle">ถาม-ตอบการดูแลลูกค้าและระบบส่งต่อเจ้าหน้าที่มนุษย์</span>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q73</span> ร้าน Purrfect Shop มีบริการหลังการขายแบบครบวงจรอะไรบ้าง?</div>
            <div class="qa-a">
                <strong>A:</strong> มีบริการ 4 ด้านหลัก:
                <br>1) <strong>ทีมสัตวแพทย์ที่ปรึกษาฟรีตลอดชีพ</strong> ผ่าน LINE ตลอด 24 ชม.
                <br>2) <strong>ระบบ Vaccine Reminder</strong> แจ้งเตือนฉีดวัคซีนและถ่ายพยาธิอัตโนมัติ
                <br>3) <strong>จัดส่งใบเพ็ดดีกรีสากลตัวจริง</strong> ถึงบ้านด้วยไปรษณีย์ด่วนพิเศษ EMS
                <br>4) <strong>ส่วนลด 20% โรงแรมและสปาแมว</strong> Purrfect Cat Hotel ตลอดอายุขัย
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q74</span> หากลูกค้าต้องการคุยกับเจ้าหน้าที่ที่เป็นมนุษย์ (Human Agent) บอทต้องทำอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> เมื่อลูกค้าพิมพ์คำว่า <em>"ติดต่อเจ้าหน้าที่", "ขอคุยกับแอดมิน", "โทรหาคน"</em> หรือมีเคสซับซ้อน แชทบอทจะตอบทันทีว่า:
                <div class="bot-tip">"น้องพูร์รี่ได้ประสานงานส่งเรื่องให้พี่แอดมินผู้เชี่ยวชาญเรียบร้อยแล้วค่ะ เจ้าหน้าที่จะติดต่อกลับในแชทนี้ภายใน 5 นาที หรือสามารถโทรสายด่วน 02-888-9999 ได้เลยนะคะ 🐾"</div>
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q75</span> สรุปเบอร์โทรศัพท์และเวลาทำการของฝ่ายบริการลูกค้า?</div>
            <div class="qa-a">
                <strong>A:</strong> สายด่วนผู้เชี่ยวชาญ: <strong>02-888-9999</strong> หรือ <strong>095-888-7777</strong> ให้บริการทุกวัน เวลา 08:30 – 20:00 น. ส่วนช่องทางแชท LINE OA @purrfectshop ให้บริการตลอด 24 ชั่วโมงครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q76</span> หากลูกค้ารับน้องแมวไปแล้วพบปัญหาการปรับตัวใน 3 วันแรก ควรแนะนำอย่างไร?</div>
            <div class="qa-a">
                <strong>A:</strong> แนะนำให้ลูกค้านำน้องแมวไว้ในห้องที่เงียบสงบก่อน วางอาหาร น้ำ และกระบะทรายไว้ใกล้ๆ อย่าเพิ่งอุ้มหรือบังคับเล่น ให้น้องคุ้นเคยกับกลิ่นและสถานที่ประมาณ 1-2 วัน และทักแชทให้สัตวแพทย์ของร้านประเมินอาการได้ตลอดเวลาครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q77</span> หากต้องการเปลี่ยนแปลงวันรับส่งมอบน้องแมว ต้องแจ้งล่วงหน้ากี่วัน?</div>
            <div class="qa-a">
                <strong>A:</strong> สามารถแจ้งเปลี่ยนแปลงวันเวลารับมอบกับแอดมินผ่าน LINE OA ล่วงหน้าอย่างน้อย <strong>2 วัน</strong> เพื่อให้ทางฟาร์มและทีมรถ Pet Taxi จัดตารางการเดินทางได้อย่างราบรื่นครับ
            </div>
        </div>

        <div class="qa-card">
            <div class="qa-q"><span class="q-tag">Q78</span> สรุปช่องทางดิจิทัลและเว็บไซต์ทางการทั้งหมดของ Purrfect Shop?</div>
            <div class="qa-a">
                <strong>A:</strong>
                <ul>
                    <li><strong>เว็บไซต์หลัก:</strong> <code>https://patches660.github.io/Purrfect-Shop/</code></li>
                    <li><strong>LINE Official:</strong> @purrfectshop</li>
                    <li><strong>Facebook:</strong> Purrfect Shop (facebook.com/purrfectshop)</li>
                    <li><strong>ระบบติดตาม Pet Taxi:</strong> <code>https://patches660.github.io/Purrfect-Shop/tracking.html</code></li>
                </ul>
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base (Q&A Training Dataset)</span>
            <span>หน้า 13 จาก 13</span>
        </div>
    </div>

</body>
</html>
"""

with open(html_path, "w", encoding="utf-8") as f:
    f.write(html_content)

print("[OK] Generated Q&A Dataset HTML (13 Pages)")

# Generate PDF via Edge/Chrome
edge_p = r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
chrome_p = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
browser_exe = edge_p if os.path.exists(edge_p) else (chrome_p if os.path.exists(chrome_p) else None)

if browser_exe:
    cmd = [
        browser_exe,
        "--headless",
        "--disable-gpu",
        "--no-pdf-header-footer",
        f"--print-to-pdf={pdf_path}",
        html_path
    ]
    res = subprocess.run(cmd, capture_output=True, text=True)
    if os.path.exists(pdf_path):
        size_kb = os.path.getsize(pdf_path) / 1024
        print(f"[OK] PDF successfully regenerated in Q&A Format! File: {pdf_path} ({size_kb:.1f} KB)")
        
        art_pdf = os.path.join(r"C:\Users\Windows\.gemini\antigravity\brain\63a36a4c-6ead-4c37-aede-64d365cc7888", "Purrfect_Shop_Chatbot_Knowledge_Base.pdf")
        shutil.copy2(pdf_path, art_pdf)
        print(f"[OK] Copied to artifacts directory: {art_pdf}")
