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
    <title>คู่มือฐานข้อมูลความรู้สำหรับ LINE AI Chatbot - Purrfect Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 14mm 12mm 14mm;
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
            font-size: 13px;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .page {
            page-break-after: always;
            page-break-inside: avoid;
            height: 270mm;
            max-height: 270mm;
            position: relative;
            padding-bottom: 25px;
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
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .page-header .brand-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #e11d48;
            letter-spacing: 0.5px;
        }
        .page-header .doc-tag {
            font-size: 11px;
            color: #64748b;
            background: #fff1f2;
            padding: 2px 8px;
            border-radius: 6px;
            border: 1px solid #ffe4e6;
        }
        .page-footer {
            position: absolute;
            bottom: 2px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 5px;
            font-size: 10px;
            color: #94a3b8;
        }

        /* Section Headings */
        .section-header {
            background: linear-gradient(135deg, #fff1f2 0%, #fff7ed 100%);
            border: 1.5px solid #fecdd3;
            border-left: 6px solid #e11d48;
            border-radius: 10px;
            padding: 8px 12px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-badge {
            background: #e11d48;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #881337;
        }
        .section-subtitle {
            font-size: 11.5px;
            color: #64748b;
            margin-left: auto;
        }

        /* Cards & Grids */
        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .card-pink {
            border-color: #fecdd3;
            background: #fffafa;
        }
        .card-cream {
            border-color: #fed7aa;
            background: #fffdfa;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
        }

        /* Tables */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 8px;
            font-size: 11.5px;
        }
        table.data-table th {
            background: #f8fafc;
            color: #1e293b;
            font-weight: 600;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
        }
        table.data-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        table.data-table.pink-head th {
            background: #fff1f2;
            color: #9f1239;
            border-color: #fecdd3;
        }

        /* Badges & Tags */
        .badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 5px;
            margin-right: 3px;
        }
        .badge-rose { background: #ffe4e6; color: #be123c; border: 1px solid #fecdd3; }
        .badge-amber { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-teal { background: #ccfbf1; color: #0f766e; border: 1px solid #99f6e4; }
        .badge-purple { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }

        /* Lists */
        ul.bullet-list {
            padding-left: 16px;
            margin-top: 3px;
        }
        ul.bullet-list li {
            margin-bottom: 3px;
        }
        
        /* Bot Prompt Boxes */
        .bot-prompt-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-left: 4px solid #16a34a;
            border-radius: 8px;
            padding: 7px 10px;
            margin-top: 6px;
            font-size: 11.5px;
            color: #166534;
        }

        /* Cover Page Styling */
        .cover-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 255mm;
            text-align: center;
            background: radial-gradient(circle at 50% 30%, #fff1f2 0%, #fffdfa 60%, #ffffff 100%);
            border: 2px solid #fecdd3;
            border-radius: 18px;
            padding: 30px 24px;
        }
        .cover-badge {
            background: #e11d48;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 16px;
            border-radius: 20px;
            margin-bottom: 16px;
            letter-spacing: 1px;
        }
        .cover-title {
            font-size: 32px;
            font-weight: 700;
            color: #881337;
            line-height: 1.25;
            margin-bottom: 10px;
        }
        .cover-subtitle {
            font-size: 15px;
            color: #475569;
            margin-bottom: 24px;
            max-width: 580px;
        }
        .cover-toc-box {
            background: #ffffff;
            border: 1px solid #fed7aa;
            border-radius: 12px;
            padding: 18px 22px;
            text-align: left;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 4px 15px rgba(225, 29, 72, 0.05);
        }
        .cover-toc-box h3 {
            font-size: 14px;
            color: #9a3412;
            margin-bottom: 10px;
            border-bottom: 1.5px solid #ffedd5;
            padding-bottom: 4px;
        }
        .toc-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px 14px;
            font-size: 11px;
            color: #334155;
        }
        .cover-meta {
            margin-top: 25px;
            font-size: 11.5px;
            color: #94a3b8;
        }

        /* FAQ Styling */
        .faq-item {
            margin-bottom: 6px;
            border: 1px solid #f1f5f9;
            border-radius: 6px;
            overflow: hidden;
        }
        .faq-q {
            background: #fff1f2;
            color: #9f1239;
            font-weight: 600;
            padding: 4px 8px;
            font-size: 11.5px;
            border-bottom: 1px solid #ffe4e6;
        }
        .faq-a {
            background: #ffffff;
            color: #334155;
            padding: 4px 8px;
            font-size: 11px;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <!-- =================================================================== -->
    <!-- PAGE 1: COVER & TABLE OF CONTENTS -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="cover-container">
            <div class="cover-badge">KNOWLEDGE BASE FOR AI CHATBOT</div>
            <h1 class="cover-title">คู่มือฐานข้อมูลความรู้ธุรกิจ<br>สำหรับ LINE AI Chatbot</h1>
            <p class="cover-subtitle">Purrfect Shop Cattery & Boutique • ศูนย์รวมน้องแมวสายพันธุ์แท้ 100% พร้อมบริการและอุปกรณ์สัตว์เลี้ยงครบวงจร</p>
            
            <div class="cover-toc-box">
                <h3>📚 สารบัญ 14 หมวดหมู่ข้อมูลมาตรฐาน</h3>
                <div class="toc-grid">
                    <div><strong>1.</strong> ข้อมูลทั่วไปของร้าน/ธุรกิจ</div>
                    <div><strong>8.</strong> นโยบายและช่องทางชำระเงิน</div>
                    <div><strong>2.</strong> ข้อมูลสินค้าและบริการ</div>
                    <div><strong>9.</strong> ระบบการจัดส่ง & Pet Taxi</div>
                    <div><strong>3.</strong> หมวดหมู่สินค้าและบริการ</div>
                    <div><strong>10.</strong> การรับประกัน 180 วัน & คืนสินค้า</div>
                    <div><strong>4.</strong> คู่มือเลือกสินค้าให้เหมาะกับลูกค้า</div>
                    <div><strong>11.</strong> FAQ คำถามที่พบบ่อย (20 ข้อ)</div>
                    <div><strong>5.</strong> ข้อมูลเปรียบเทียบสินค้า/สายพันธุ์</div>
                    <div><strong>12.</strong> คำถามสำหรับระบบคัดกรองแนะนำ</div>
                    <div><strong>6.</strong> โปรโมชั่น คูปอง และสิทธิพิเศษ</div>
                    <div><strong>13.</strong> กฎตรรกะตัดสินใจ AI Recommendation</div>
                    <div><strong>7.</strong> ขั้นตอนการสั่งซื้อและจองแมว</div>
                    <div><strong>14.</strong> ข้อมูลติดต่อ & บริการหลังการขาย</div>
                </div>
            </div>

            <div class="cover-meta">
                <p>จัดทำโดย: <strong>ทีมพัฒนาระบบ AI Chatbot Purrfect Shop</strong> | เอกสารเวอร์ชัน 2.5 (อัปเดต 2026)</p>
                <p>สำหรับใช้เป็น System Prompt Context & Document RAG Knowledge Retrieval</p>
            </div>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 2: TOPIC 1 - GENERAL BUSINESS INFORMATION -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 1: ข้อมูลทั่วไป</span>
        </div>

        <div class="section-header">
            <span class="section-badge">01</span>
            <h2 class="section-title">ข้อมูลทั่วไปของร้านและธุรกิจ (General Business Info)</h2>
            <span class="section-subtitle">พื้นฐานองค์กรและช่องทางบริการ</span>
        </div>

        <div class="card card-pink">
            <h3 style="color:#be123c; font-size:14px; margin-bottom:4px;">🏪 ข้อมูลองค์กรและอัตลักษณ์แบรนด์</h3>
            <table class="data-table pink-head">
                <tr>
                    <th style="width:28%;">หัวข้อ</th>
                    <th>รายละเอียดสำหรับ AI ใช้ตอบคำถาม</th>
                </tr>
                <tr>
                    <td><strong>ชื่อร้านค้า (Brand)</strong></td>
                    <td><strong>Purrfect Shop Cattery & Boutique</strong> (เพอร์เฟกต์ ช็อป แคทเทอรี่ & บูติก)</td>
                </tr>
                <tr>
                    <td><strong>สโลแกน (Slogan)</strong></td>
                    <td><em>"เพราะทุกบ้านควรมีเจ้าเหมียว"</em> (Every Home Deserves A Purrfect Cat 🐾)</td>
                </tr>
                <tr>
                    <td><strong>ประเภทธุรกิจ</strong></td>
                    <td>ฟาร์มเพาะพันธุ์แมวสายพันธุ์แท้ 100% มาตรฐานสากล, บูติกจำหน่ายอุปกรณ์ ของเล่น อาหารพรีเมียม และบริการจัดส่งสัตว์เลี้ยงทั่วประเทศ</td>
                </tr>
                <tr>
                    <td><strong>จุดเด่นของร้าน (USP)</strong></td>
                    <td>
                        <ul class="bullet-list">
                            <li>สายพันธุ์แท้ 100% มีใบเพ็ดดีกรีรับรองมาตรฐานสากล (CFA / TICA / WCF)</li>
                            <li><strong>การันตีคุ้มครองสุขภาพยาวนานที่สุดถึง 180 วัน</strong> (โรค FIP, FeLV, FIV, Panleukopenia)</li>
                            <li>ตรวจสุขภาพละเอียด ฉีดวัคซีนครบ 2 เข็ม ฝังไมโครชิป และถ่ายพยาธิก่อนย้ายบ้าน</li>
                            <li>ส่งฟรีทั่วประเทศด้วยรถ Pet Taxi ควบคุมอุณหภูมิ มีพี่เลี้ยงดูแลตลอดเส้นทาง</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td><strong>เวลาทำการ (Hours)</strong></td>
                    <td>
                        • หน้าร้าน/ฟาร์ม: <strong>ทุกวัน 09:00 – 19:00 น.</strong> (นัดหมายเข้าชมล่วงหน้า)<br>
                        • ช่องทางออนไลน์ & แชท: <strong>เปิดบริการ 24 ชั่วโมง</strong> (AI Chatbot ตอบทันที)
                    </td>
                </tr>
                <tr>
                    <td><strong>ที่อยู่หน้าร้าน</strong></td>
                    <td>เลขที่ 88/9 อาคารเพอร์เฟกต์ ทาวเวอร์ ถนนสุขุมวิท 71 แขวงพระโขนงเหนือ เขตวัฒนา กรุงเทพมหานคร 10110</td>
                </tr>
                <tr>
                    <td><strong>ช่องทางติดต่อหลัก</strong></td>
                    <td>
                        • LINE Official Account: <strong>@purrfectshop</strong><br>
                        • เบอร์โทรศัพท์: <strong>02-888-9999</strong> หรือ <strong>095-888-7777</strong><br>
                        • อีเมล: <strong>contact@purrfectshop.com</strong> | Facebook: <strong>PurrfectShopOfficial</strong>
                    </td>
                </tr>
                <tr>
                    <td><strong>ช่องทางสั่งซื้อ</strong></td>
                    <td>เว็บไซต์ทางการ <code>https://patches660.github.io/Purrfect-Shop/</code>, LINE Shop, และ LINE OA Chat</td>
                </tr>
            </table>
        </div>

        <div class="bot-prompt-box">
            <strong>🤖 แนวทางการตอบของ Bot (Prompt Instruction):</strong> เมื่อลูกค้าทักทาย ให้ตอบรับอย่างสุภาพ เป็นกันเอง ใช้อิโมจิน้องแมว 🐾 แนะนำตัวว่าคือ "น้องพูร์รี่ (Purry) AI ผู้ช่วยดูแลจาก Purrfect Shop" และแจ้งเวลาทำการพร้อมช่องทางบริการได้ทันที
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 2 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 3: TOPIC 2 - PRODUCTS & SERVICES -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 2: สินค้าและบริการ</span>
        </div>

        <div class="section-header">
            <span class="section-badge">02</span>
            <h2 class="section-title">ข้อมูลสินค้าและบริการ (Products & Services Catalog)</h2>
            <span class="section-subtitle">รายละเอียดสายพันธุ์และอุปกรณ์พรีเมียม</span>
        </div>

        <div class="card">
            <h3 style="color:#881337; font-size:13.5px; margin-bottom:3px;">🐱 สายพันธุ์น้องแมวยอดนิยมในฟาร์ม</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>สายพันธุ์</th>
                        <th>ช่วงราคา</th>
                        <th>ลักษณะเด่น & นิสัย</th>
                        <th>ใบรับรอง</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>British Shorthair (บลู/ไลแลค)</strong></td>
                        <td>32,000 – 42,000 ฿</td>
                        <td>หน้ากลมแป้น ขนแน่นกำมะหยี่ นิ่ง สุภาพ ไม่กวนใจ เหมาะกับคอนโด</td>
                        <td>WCF / CFA</td>
                        <td><span class="badge badge-teal">พร้อมย้ายบ้าน</span></td>
                    </tr>
                    <tr>
                        <td><strong>Scottish Fold (หูพับ/หูตั้ง)</strong></td>
                        <td>28,000 – 38,000 ฿</td>
                        <td>ตากลมโต หูพับสนิท ขี้อ้อน ชอบนอนหงายพุง ติดคนมาก</td>
                        <td>WCF</td>
                        <td><span class="badge badge-teal">พร้อมย้ายบ้าน</span></td>
                    </tr>
                    <tr>
                        <td><strong>Maine Coon (ยักษ์ใหญ่ใจดี)</strong></td>
                        <td>48,000 – 65,000 ฿</td>
                        <td>โครงสร้างใหญ่สง่างาม ขนแผงคอฟู ฉลาด ชอบเล่นน้ำ เดินสายจูงได้</td>
                        <td>CFA / TICA</td>
                        <td><span class="badge badge-amber">จองล่วงหน้า</span></td>
                    </tr>
                    <tr>
                        <td><strong>Ragdoll (เจ้าหญิงตาสีฟ้า)</strong></td>
                        <td>29,000 – 39,000 ฿</td>
                        <td>ตัวนุ่มปวกเปียก ตาสีฟ้าคราม อ่อนโยน ปลอดภัยกับเด็กเล็ก 100%</td>
                        <td>TICA</td>
                        <td><span class="badge badge-teal">พร้อมย้ายบ้าน</span></td>
                    </tr>
                    <tr>
                        <td><strong>Persian Classic (ปุยหิมะ)</strong></td>
                        <td>16,500 – 24,000 ฿</td>
                        <td>ราชินีขนฟู หน้าหวาน นิสัยสุภาพ นิ่งสงบ รักความเงียบสงบ</td>
                        <td>CFA</td>
                        <td><span class="badge badge-teal">พร้อมย้ายบ้าน</span></td>
                    </tr>
                    <tr>
                        <td><strong>Munchkin (ขาสั้นดุ๊กดิ๊ก)</strong></td>
                        <td>24,000 – 32,000 ฿</td>
                        <td>ขาสั้นเตี้ยวิ่งดุ๊กดิ๊ก ซุกซน ร่าเริง สุขภาพข้อต่อแข็งแรง</td>
                        <td>WCF</td>
                        <td><span class="badge badge-teal">พร้อมย้ายบ้าน</span></td>
                    </tr>
                    <tr>
                        <td><strong>Bengal (เสือดาวจิ๋ว)</strong></td>
                        <td>26,000 – 35,000 ฿</td>
                        <td>ลายดอกกุหลาบ Rosette ชัดเจน กล้ามเนื้อสวย ปราดเปรียว รักน้ำ</td>
                        <td>WCF</td>
                        <td><span class="badge badge-teal">พร้อมย้ายบ้าน</span></td>
                    </tr>
                    <tr>
                        <td><strong>Siamese / วิเชียรมาศ</strong></td>
                        <td>12,000 – 16,000 ฿</td>
                        <td>แมวมงคลไทย แต้มสี 9 จุด ฉลาด ช่างพูด ผูกพันกับเจ้าของสูง</td>
                        <td>ใบเพ็ดไทยแท้</td>
                        <td><span class="badge badge-teal">พร้อมย้ายบ้าน</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card card-cream">
            <h3 style="color:#9a3412; font-size:13.5px; margin-bottom:3px;">🎁 แพ็กเกจ Starter Kit และอุปกรณ์เสริม</h3>
            <div class="grid-3" style="font-size:11px;">
                <div style="background:#ffffff; padding:6px; border-radius:6px; border:1px solid #fed7aa;">
                    <strong>Starter Kit Basic (1,890 ฿)</strong><br>
                    กระบะทราย + ทรายเต้าหู้ 2 ถุง + ชามอาหารคู่ + ของเล่น 3 ชิ้น
                </div>
                <div style="background:#ffffff; padding:6px; border-radius:6px; border:1px solid #fed7aa;">
                    <strong>Starter Kit Deluxe (4,500 ฿)</strong><br>
                    กระเป๋าแคปซูล + คอนโดมินิ + อาหารพรีเมียม 2 กก. + น้ำพุไร้สาย
                </div>
                <div style="background:#ffffff; padding:6px; border-radius:6px; border:1px solid #fed7aa;">
                    <strong>VIP Health Care Box (3,200 ฿)</strong><br>
                    แชมพูออร์แกนิก + หวีสแตนเลส + วิตามินบำรุงขน + ยาหยอดเห็บ
                </div>
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 3 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 4: TOPIC 3 - PRODUCT CATEGORIES -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 3: หมวดหมู่สินค้า</span>
        </div>

        <div class="section-header">
            <span class="section-badge">03</span>
            <h2 class="section-title">หมวดหมู่สินค้าและบริการ (Product Categories)</h2>
            <span class="section-subtitle">การแบ่งกลุ่มและแท็กสำหรับค้นหา</span>
        </div>

        <div class="grid-2">
            <div class="card card-pink">
                <h3 style="color:#be123c; font-size:13.5px; margin-bottom:6px;">🔥 1. สินค้าขายดี (Best Sellers)</h3>
                <ul class="bullet-list" style="font-size:11.5px;">
                    <li><strong>British Shorthair (Blue):</strong> ครองแชมป์ยอดจองอันดับ 1 หน้ากลม เลี้ยงง่าย</li>
                    <li><strong>Starter Kit 11 รายการ (Deluxe):</strong> ของแถมและชุดเริ่มต้นที่ลูกค้าสั่งพร้อมแมวมากที่สุด</li>
                    <li><strong>Scottish Fold หูพับ:</strong> ยอดนิยมสำหรับสาวๆ และผู้ที่ชอบแมวขี้อ้อน</li>
                    <li><strong>ทรายแมวเต้าหู้พรีเมียม 6L:</strong> ไร้ฝุ่น 99.9% ทิ้งชักโครกได้</li>
                </ul>
            </div>
            <div class="card card-cream">
                <h3 style="color:#9a3412; font-size:13.5px; margin-bottom:6px;">✨ 2. สินค้าเข้าใหม่ (New Arrivals)</h3>
                <ul class="bullet-list" style="font-size:11.5px;">
                    <li><strong>ลูกแมวครอกใหม่เดือนนี้:</strong> แร็กดอลล์และเมนคูน สายเลือดแชมป์นำเข้า</li>
                    <li><strong>น้ำพุแมวอัจฉริยะระบบกรอง 5 ชั้น:</strong> ไร้สาย แบตเตอรี่ใช้ได้ 60 วัน</li>
                    <li><strong>คอนโดแมวไม้แท้มินิมอล 1.6m:</strong> แข็งแรง รองรับแมวตัวใหญ่</li>
                    <li><strong>ขนมแมวเลียสูตรบำรุงไต & ขนสวย:</strong> นำเข้าจากประเทศญี่ปุ่น</li>
                </ul>
            </div>
        </div>

        <div class="grid-2">
            <div class="card" style="border-color:#bbf7d0; background:#f0fdf4;">
                <h3 style="color:#166534; font-size:13.5px; margin-bottom:6px;">⭐ 3. สินค้าแนะนำประจำสัปดาห์ (Featured)</h3>
                <ul class="bullet-list" style="font-size:11.5px;">
                    <li><strong>น้องซีซาร์ (Canadian Sphynx):</strong> แมวไร้ขน เหมาะกับคนเป็นภูมิแพ้ 0%</li>
                    <li><strong>Welcome Deal 10% Off:</strong> คูปองสำหรับสมาชิกใหม่ รหัส <code>CAT10OFF</code></li>
                    <li><strong>แพ็กเกจตรวจสุขภาพก่อนส่งมอบ:</strong> ตรวจ PCR ครบ 12 โรค</li>
                </ul>
            </div>
            <div class="card" style="border-color:#e9d5ff; background:#faf5ff;">
                <h3 style="color:#6b21a8; font-size:13.5px; margin-bottom:6px;">🏷️ 4. สินค้าแบ่งตามประเภท (By Category)</h3>
                <ul class="bullet-list" style="font-size:11.5px;">
                    <li><strong>หมวดน้องแมว:</strong> ขนสั้น, ขนยาว, แมวไซส์ยักษ์, แมวขาสั้น, แมวมงคล</li>
                    <li><strong>หมวดอาหาร:</strong> Holistic Grain-free, Freeze Dried, นมแพะแท้ 100%</li>
                    <li><strong>หมวดสุขอนามัย:</strong> ทรายแมว, ห้องน้ำอัตโนมัติ, สเปรย์ดับกลิ่นชีวภาพ</li>
                    <li><strong>หมวดบริการ:</strong> จองแมวออนไลน์, Pet Taxi, นัดพบแพทย์ผ่านฟาร์ม</li>
                </ul>
            </div>
        </div>

        <div class="bot-prompt-box">
            <strong>🤖 Logic แชทบอท:</strong> หากลูกค้าพิมพ์ถามหา "มีอะไรแนะนำบ้าง" หรือ "แมวพันธุ์ไหนคนเลี้ยงเยอะสุด" ให้ดึงข้อมูลจากกลุ่ม Best Sellers (British Shorthair / Scottish Fold) และแจ้งโปรโมชั่น Starter Kit ฟรีทันที
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 4 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 5: TOPIC 4 - CUSTOMER MATCHING GUIDE -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 4: การเลือกให้เหมาะกับลูกค้า</span>
        </div>

        <div class="section-header">
            <span class="section-badge">04</span>
            <h2 class="section-title">การเลือกสินค้าและน้องแมวให้เหมาะกับลูกค้า (Matching Guide)</h2>
            <span class="section-subtitle">เกณฑ์การวิเคราะห์และคัดกรองความต้องการ</span>
        </div>

        <div class="card card-pink">
            <h3 style="color:#881337; font-size:14px; margin-bottom:4px;">🎯 เกณฑ์การเลือกน้องแมวตาม Lifestyle และข้อจำกัดของลูกค้า</h3>
            <table class="data-table">
                <thead>
                    <tr style="background:#fff1f2; color:#9f1239;">
                        <th>กลุ่มลูกค้า / ลักษณะที่อยู่อาศัย</th>
                        <th>สายพันธุ์ที่แนะนำ</th>
                        <th>เหตุผลประกอบการตัดสินใจ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>1. อาศัยอยู่คอนโด / ห้องพัก</strong><br><span class="badge badge-rose">พื้นที่จำกัด ไม่ส่งเสียงดัง</span></td>
                        <td><strong>British Shorthair</strong><br><strong>Persian Classic</strong></td>
                        <td>นิสัยเงียบสงบ ไม่ส่งเสียงร้องโวยวาย ใช้พื้นที่น้อย รักความสงบและนอนเก่ง</td>
                    </tr>
                    <tr>
                        <td><strong>2. มีเด็กเล็ก หรือ คนสูงวัยในบ้าน</strong><br><span class="badge badge-purple">ความปลอดภัย อ่อนโยน</span></td>
                        <td><strong>Ragdoll</strong><br><strong>Scottish Fold</strong></td>
                        <td>ตัวนุ่มปวกเปียก ไม่กางเล็บ ใจดีกับเด็กมาก ขี้อ้อน และทนต่อการกอดอุ้ม</td>
                    </tr>
                    <tr>
                        <td><strong>3. เป็นโรคภูมิแพ้ขนสัตว์</strong><br><span class="badge badge-amber">Allergy-Friendly</span></td>
                        <td><strong>Sphynx (ไร้ขน)</strong><br><strong>Devon Rex / Cornish</strong></td>
                        <td>ไม่มีขนร่วง ขนสั้นหยิกติดผิว ไม่สะสมไรฝุ่นและโปรตีน Fel d 1 ในอากาศ</td>
                    </tr>
                    <tr>
                        <td><strong>4. เจ้าของทำงานนอกบ้าน (ไม่มีเวลา)</strong><br><span class="badge badge-teal">Independent Cat</span></td>
                        <td><strong>British Shorthair</strong><br><strong>American Shorthair</strong></td>
                        <td>พึ่งพาตัวเองได้ดี ไม่เครียดง่ายเมื่ออยู่ลำพัง วางอาหารและน้ำไว้ก็อยู่ได้สบาย</td>
                    </tr>
                    <tr>
                        <td><strong>5. ชอบทำกิจกรรม ชอบแมวพลังเยอะ</strong><br><span class="badge badge-amber">Active & Playful</span></td>
                        <td><strong>Bengal</strong><br><strong>Maine Coon</strong></td>
                        <td>ฉลาด ฝึกเดินสายจูงได้ ชอบเล่นน้ำ ชวนคุยเก่งและมีพลังในการสำรวจสูง</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card card-cream">
            <h3 style="color:#9a3412; font-size:13.5px; margin-bottom:3px;">💰 การแนะนำตามงบประมาณ (Budget Matching)</h3>
            <div class="grid-3" style="font-size:11px;">
                <div style="padding:6px; border-left:3px solid #10b981; background:#ffffff;">
                    <strong>งบประหยัด (12,000 - 20,000 ฿)</strong><br>
                    • วิเชียรมาศ (Siamese)<br>
                    • เปอร์เซียคลาสสิก (Persian)<br>
                    • Starter Kit Basic ฟรี
                </div>
                <div style="padding:6px; border-left:3px solid #3b82f6; background:#ffffff;">
                    <strong>งบมาตรฐาน (25,000 - 38,000 ฿)</strong><br>
                    • British Shorthair<br>
                    • Scottish Fold / Munchkin<br>
                    • Ragdoll Princess
                </div>
                <div style="padding:6px; border-left:3px solid #8b5cf6; background:#ffffff;">
                    <strong>งบพรีเมียม (40,000 - 65,000+ ฿)</strong><br>
                    • Maine Coon Giant WCF<br>
                    • Bengal Rosette Show Grade<br>
                    • Canadian Sphynx Import
                </div>
            </div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 5 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 6: TOPIC 5 - COMPARISON MATRIX -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 5: ตารางเปรียบเทียบ</span>
        </div>

        <div class="section-header">
            <span class="section-badge">05</span>
            <h2 class="section-title">ข้อมูลเปรียบเทียบสินค้าและสายพันธุ์ (Comparison Matrix)</h2>
            <span class="section-subtitle">ตารางเปรียบเทียบคุณสมบัติสำหรับตัดสินใจ</span>
        </div>

        <div class="card">
            <h3 style="color:#881337; font-size:13.5px; margin-bottom:4px;">📊 ตารางเปรียบเทียบคุณสมบัติสายพันธุ์แมว 6 ยอดนิยม</h3>
            <table class="data-table" style="font-size:11px;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th>สายพันธุ์</th>
                        <th>ระดับการร่วงของขน</th>
                        <th>ความขี้อ้อน</th>
                        <th>ความแอคทีฟ</th>
                        <th>การดูแลขน</th>
                        <th>ระดับเสียงร้อง</th>
                        <th>เหมาะกับมือใหม่</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>British Shorthair</strong></td>
                        <td>ปานกลาง</td>
                        <td>⭐⭐⭐⭐</td>
                        <td>⭐⭐</td>
                        <td>ง่าย (สัปดาห์ละ 1 ครั้ง)</td>
                        <td>เงียบมาก 🔇</td>
                        <td>⭐⭐⭐⭐⭐ (ดีเยี่ยม)</td>
                    </tr>
                    <tr>
                        <td><strong>Scottish Fold</strong></td>
                        <td>ปานกลาง</td>
                        <td>⭐⭐⭐⭐⭐</td>
                        <td>⭐⭐⭐</td>
                        <td>ง่าย (สัปดาห์ละ 1-2 ครั้ง)</td>
                        <td>เบามาก</td>
                        <td>⭐⭐⭐⭐⭐ (ดีเยี่ยม)</td>
                    </tr>
                    <tr>
                        <td><strong>Maine Coon</strong></td>
                        <td>ค่อนข้างมาก</td>
                        <td>⭐⭐⭐⭐</td>
                        <td>⭐⭐⭐⭐</td>
                        <td>ปานกลาง (หวีทุก 2 วัน)</td>
                        <td>เสียงเบาจิ๋ว</td>
                        <td>⭐⭐⭐⭐ (ดีมาก)</td>
                    </tr>
                    <tr>
                        <td><strong>Ragdoll</strong></td>
                        <td>ปานกลาง</td>
                        <td>⭐⭐⭐⭐⭐</td>
                        <td>⭐⭐</td>
                        <td>ปานกลาง (ขนนุ่มไม่พันกัน)</td>
                        <td>เงียบมาก</td>
                        <td>⭐⭐⭐⭐⭐ (ดีเยี่ยม)</td>
                    </tr>
                    <tr>
                        <td><strong>Sphynx</strong></td>
                        <td><strong>0% (ไม่มีขน)</strong></td>
                        <td>⭐⭐⭐⭐⭐</td>
                        <td>⭐⭐⭐⭐</td>
                        <td>เช็ดตัวสัปดาห์ละ 1 ครั้ง</td>
                        <td>ปานกลาง</td>
                        <td>⭐⭐⭐⭐ (ดีมาก)</td>
                    </tr>
                    <tr>
                        <td><strong>Bengal</strong></td>
                        <td>น้อยมาก</td>
                        <td>⭐⭐⭐</td>
                        <td>⭐⭐⭐⭐⭐</td>
                        <td>ง่ายมาก (แทบไม่ต้องหวี)</td>
                        <td>ช่างคุย 🗣️</td>
                        <td>⭐⭐⭐ (ชอบเล่น)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card card-pink">
            <h3 style="color:#be123c; font-size:13.5px; margin-bottom:4px;">📋 ตารางเปรียบเทียบแพ็กเกจการรับเลี้ยง (Adoption Tiers)</h3>
            <table class="data-table pink-head" style="font-size:11px;">
                <thead>
                    <tr>
                        <th>สิทธิประโยชน์ในแพ็กเกจ</th>
                        <th>Standard Tier</th>
                        <th>Premium Tier (แนะนำ)</th>
                        <th>VIP Show Tier</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>ใบเพ็ดดีกรีรับรองสายพันธุ์</td>
                        <td>ใบเพ็ดสมาคมไทย</td>
                        <td>ใบเพ็ด WCF / CFA สากล</td>
                        <td>ใบเพ็ด CFA/TICA บันทึก 4 ชั่วอายุ</td>
                    </tr>
                    <tr>
                        <td>ระยะเวลาการันตีสุขภาพ</td>
                        <td>30 วัน</td>
                        <td><strong>180 วันเต็ม</strong></td>
                        <td><strong>365 วัน (1 ปีเต็ม)</strong></td>
                    </tr>
                    <tr>
                        <td>การตรวจสุขภาพและแล็บ</td>
                        <td>วัคซีน 2 เข็ม + ถ่ายพยาธิ</td>
                        <td>วัคซีน + ฝังชิป + ตรวจ FeLV/FIV</td>
                        <td>Full Lab PCR 12 โรค + เอกซเรย์ข้อ</td>
                    </tr>
                    <tr>
                        <td>ชุดของแถม Starter Kit</td>
                        <td>Basic Kit (1,890฿)</td>
                        <td><strong>Deluxe Kit 11 รายการ (4,500฿)</strong></td>
                        <td>Luxury Travel & Condo (8,500฿)</td>
                    </tr>
                    <tr>
                        <td>บริการจัดส่ง Pet Taxi</td>
                        <td>ฟรีในกรุงเทพฯ</td>
                        <td><strong>ส่งฟรีทั่วประเทศไทย</strong></td>
                        <td>ส่งฟรีทั่วประเทศ + พี่เลี้ยงส่งถึงบ้าน</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 6 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 7: TOPIC 6 - PROMOTIONS & PRIVILEGES -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 6: โปรโมชั่น & สิทธิพิเศษ</span>
        </div>

        <div class="section-header">
            <span class="section-badge">06</span>
            <h2 class="section-title">โปรโมชั่น คูปอง และสิทธิพิเศษ (Promotions & Privileges)</h2>
            <span class="section-subtitle">แคมเปญส่งเสริมการขายและโค้ดส่วนลด</span>
        </div>

        <div class="grid-2">
            <div class="card card-pink">
                <h3 style="color:#be123c; font-size:13.5px; margin-bottom:4px;">🎁 1. คูปองส่วนลดหลัก (Active Coupons)</h3>
                <table class="data-table pink-head" style="font-size:11px;">
                    <tr>
                        <th>รหัสโค้ด</th>
                        <th>สิทธิประโยชน์</th>
                        <th>เงื่อนไข</th>
                    </tr>
                    <tr>
                        <td><code>CAT10OFF</code></td>
                        <td>ลดทันที 10%</td>
                        <td>สมาชิกใหม่ ช้อปขั้นต่ำ 300.-</td>
                    </tr>
                    <tr>
                        <td><code>ADOPT25</code></td>
                        <td>ลด 25% ค่าสินสอด</td>
                        <td>สำหรับน้องแมวตัวที่ 2 หรือจองคู่</td>
                    </tr>
                    <tr>
                        <td><code>FOOD25OFF</code></td>
                        <td>ลด 25% อาหาร/ทราย</td>
                        <td>เมื่อสั่งซื้ออาหาร 2 ถุงขึ้นไป</td>
                    </tr>
                    <tr>
                        <td><code>FREESHIP</code></td>
                        <td>ส่งฟรี Pet Taxi</td>
                        <td>เมื่อมียอดจองน้องแมวทุกสายพันธุ์</td>
                    </tr>
                </table>
            </div>

            <div class="card card-cream">
                <h3 style="color:#9a3412; font-size:13.5px; margin-bottom:4px;">🎉 2. โปรโมชั่นประจำเดือน (Monthly Deals)</h3>
                <ul class="bullet-list" style="font-size:11.5px;">
                    <li><strong>โปรโมชั่น "Grand Opening Cattery":</strong>
                        <br>รับฟรี Starter Kit มูลค่า 4,500 บาททันทีเมื่อจองน้องแมวทุกสายพันธุ์ในเดือนนี้</li>
                    <li><strong>โปรผ่อนชำระ 0% นานสูงสุด 10 เดือน:</strong>
                        <br>ผ่านบัตรเครดิต KBank, SCB, KTC, BBL และ Krungsri</li>
                    <li><strong>วงล้อลุ้นโชค Lucky Wheel:</strong>
                        <br>หมุนฟรีวันละ 1 ครั้ง ลุ้นรับส่วนลดสูงสุด 1,000 บาท หรือขนมแมวเลียฟรี</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <h3 style="color:#881337; font-size:13.5px; margin-bottom:4px;">📜 3. เงื่อนไขและข้อกำหนดโปรโมชั่น (Promotion Terms)</h3>
            <ul class="bullet-list" style="font-size:11px; color:#475569;">
                <li>คูปองส่วนลดไม่สามารถแลกเปลี่ยนเป็นเงินสดหรือโอนสิทธิ์ให้ผู้อื่นได้</li>
                <li>โค้ด <code>CAT10OFF</code> ใช้ได้ 1 ครั้ง ต่อ 1 บัญชีผู้ใช้งาน</li>
                <li>ของแถม Starter Kit จะถูกจัดส่งไปพร้อมกับน้องแมวในวันส่งมอบ</li>
                <li>โปรโมชั่นผ่อนชำระ 0% ยอดขั้นต่ำ 10,000 บาทขึ้นไป</li>
            </ul>
        </div>

        <div class="bot-prompt-box">
            <strong>🤖 Bot Trigger Rule:</strong> หากลูกค้าถามว่า "มีโปรโมชั่นอะไรบ้าง" หรือ "มีส่วนลดไหม" ให้แจ้งโค้ด <code>CAT10OFF</code> ลด 10% พร้อมแจ้งสิทธิ์รับ Starter Kit 11 รายการฟรี และแนบลิงก์หน้าโปรโมชั่น <code>/welcome_deal.html</code>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 7 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 8: TOPIC 7 - ORDERING PROCESS -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 7: ขั้นตอนการสั่งซื้อ</span>
        </div>

        <div class="section-header">
            <span class="section-badge">07</span>
            <h2 class="section-title">ขั้นตอนการสั่งซื้อและระบบการจอง (Ordering & Booking Flow)</h2>
            <span class="section-subtitle">แนวทางการแนะนำลูกค้าทีละขั้นตอน</span>
        </div>

        <div class="card card-pink">
            <h3 style="color:#be123c; font-size:13.5px; margin-bottom:6px;">🛒 ขั้นตอนการสั่งซื้อสินค้าและจองน้องแมว (5 ขั้นตอนง่ายๆ)</h3>
            
            <div style="display:flex; flex-direction:column; gap:6px; font-size:11.5px;">
                <div style="display:flex; gap:8px; align-items:flex-start; background:#ffffff; padding:6px 10px; border-radius:6px; border:1px solid #fecdd3;">
                    <span style="background:#e11d48; color:#fff; font-weight:700; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:10.5px;">1</span>
                    <div><strong>เลือกน้องแมวหรือสินค้าที่ต้องการ:</strong> เข้าชมผ่านเว็บไซต์ หรือให้ AI แนะนำสายพันธุ์ที่ตรงกับความต้องการ พร้อมดูรูปและวิดีโอตัวจริง</div>
                </div>

                <div style="display:flex; gap:8px; align-items:flex-start; background:#ffffff; padding:6px 10px; border-radius:6px; border:1px solid #fecdd3;">
                    <span style="background:#e11d48; color:#fff; font-weight:700; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:10.5px;">2</span>
                    <div><strong>กดเพิ่มลงตะกร้า หรือ กดจอง (Book Now):</strong> กรอกข้อมูลผู้รับเลี้ยง ชื่อ ที่อยู่จัดส่ง และเลือกวันเวลาที่สะดวกรับน้องแมว</div>
                </div>

                <div style="display:flex; gap:8px; align-items:flex-start; background:#ffffff; padding:6px 10px; border-radius:6px; border:1px solid #fecdd3;">
                    <span style="background:#e11d48; color:#fff; font-weight:700; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:10.5px;">3</span>
                    <div><strong>ใส่โค้ดส่วนลด & ตรวจสอบยอด:</strong> ใส่โค้ดโปรโมชั่น เช่น <code>CAT10OFF</code> เพื่อรับส่วนลดและของแถม Starter Kit</div>
                </div>

                <div style="display:flex; gap:8px; align-items:flex-start; background:#ffffff; padding:6px 10px; border-radius:6px; border:1px solid #fecdd3;">
                    <span style="background:#e11d48; color:#fff; font-weight:700; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:10.5px;">4</span>
                    <div><strong>ชำระเงินมัดจำ หรือ ชำระเต็มจำนวน:</strong> จ่ายผ่าน PromptPay QR Code, โอนธนาคาร หรือตัดบัตรเครดิต 0%</div>
                </div>

                <div style="display:flex; gap:8px; align-items:flex-start; background:#ffffff; padding:6px 10px; border-radius:6px; border:1px solid #fecdd3;">
                    <span style="background:#e11d48; color:#fff; font-weight:700; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:10.5px;">5</span>
                    <div><strong>รับใบยืนยันคำสั่งซื้อ & ติดตามสถานะ:</strong> ระบบจะออก Order Confirmation & ใบจองพร้อมเลข Tracking ติดตามรถ Pet Taxi แบบ Real-time</div>
                </div>
            </div>
        </div>

        <div class="card card-cream">
            <h3 style="color:#9a3412; font-size:13.5px; margin-bottom:3px;">📋 นโยบายเงินมัดจำในการจองน้องแมว</h3>
            <p style="font-size:11.5px; color:#475569;">
                • ยอดเงินมัดจำเริ่มต้นเพียง <strong>5,000 บาท</strong> ต่อตัว เพื่อล็อคน้องแมวไว้ให้ลูกค้า<br>
                • ยอดเงินมัดจำจะนำไปหักเต็มจำนวนในวันรับส่งมอบน้องแมว<br>
                • ทางฟาร์มจะตรวจสุขภาพซ้ำก่อนส่งมอบ หากตรวจพบปัญหาสุขภาพ ยินดีคืนเงินมัดจำเต็มจำนวน 100%
            </p>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 8 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 9: TOPICS 8 & 9 - PAYMENT & SHIPPING -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 8 & 9: ชำระเงิน & จัดส่ง</span>
        </div>

        <div class="section-header">
            <span class="section-badge">08-09</span>
            <h2 class="section-title">นโยบายการชำระเงิน และ ระบบการจัดส่ง (Payment & Shipping)</h2>
            <span class="section-subtitle">ความปลอดภัยและสวัสดิภาพการขนส่งสัตว์เลี้ยง</span>
        </div>

        <div class="grid-2">
            <div class="card card-pink">
                <h3 style="color:#be123c; font-size:13.5px; margin-bottom:4px;">💳 หมวดที่ 8: ช่องทางการชำระเงิน</h3>
                <ul class="bullet-list" style="font-size:11.5px;">
                    <li><strong>Thai PromptPay QR Code:</strong> สแกนจ่ายได้ทุกธนาคาร ยืนยันยอดอัตโนมัติภายใน 5 วินาที</li>
                    <li><strong>โอนเงินผ่านบัญชีธนาคาร (Bank Transfer):</strong>
                        <br>ธนาคารกสิกรไทย (KBANK)
                        <br>เลขที่บัญชี: <code>888-2-55555-8</code>
                        <br>ชื่อบัญชี: บจก. เพอร์เฟกต์ แคท ช็อป
                    </li>
                    <li><strong>บัตรเครดิต / เดบิต:</strong> Visa, Mastercard, JCB (ระบบความปลอดภัย 3D-Secure)</li>
                    <li><strong>ผ่อนชำระ 0%:</strong> นาน 3, 6, 10 เดือน สำหรับยอด 10,000 ฿ ขึ้นไป</li>
                    <li><strong>เก็บเงินปลายทาง (COD):</strong> เฉพาะสินค้าอุปกรณ์และอาหาร (น้องแมวต้องชำระมัดจำก่อน)</li>
                </ul>
            </div>

            <div class="card card-cream">
                <h3 style="color:#9a3412; font-size:13.5px; margin-bottom:4px;">🚐 หมวดที่ 9: ระบบการจัดส่งสัตว์เลี้ยง (Pet Taxi)</h3>
                <ul class="bullet-list" style="font-size:11.5px;">
                    <li><strong>รถ Pet Taxi ปรับอากาศ VIP:</strong>
                        <br>ควบคุมอุณหภูมิ 24-25°C ตลอดเส้นทาง มีกล้องวงจรปิดเช็คสภาพน้องแมวได้</li>
                    <li><strong>พี่เลี้ยงดูแลตลอดทาง:</strong>
                        <br>ให้น้ำ อาหาร และคอยดูแลไม่ให้น้องแมวตื่นตกใจ</li>
                    <li><strong>ระยะเวลาจัดส่ง:</strong>
                        <br>• กรุงเทพฯ และปริมณฑล: ส่งภายในวันเดียวกัน (Same-Day)
                        <br>• ต่างจังหวัดทั่วประเทศ: 1 – 2 วันทำการ
                    </li>
                    <li><strong>เงื่อนไขส่งฟรี:</strong>
                        <br><strong>ส่งฟรีทั่วประเทศไทย</strong> เมื่อจองน้องแมวทุกสายพันธุ์</li>
                    <li><strong>การติดตามสถานะ:</strong>
                        <br>ตรวจสอบ Real-time GPS ได้ที่หน้า <code>/tracking.html</code></li>
                </ul>
            </div>
        </div>

        <div class="card" style="border-color:#bbf7d0; background:#f0fdf4;">
            <h3 style="color:#166534; font-size:13px; margin-bottom:3px;">📦 การจัดส่งสินค้าและอุปกรณ์ทั่วไป</h3>
            <p style="font-size:11.5px; color:#14532d;">
                ขนส่งผ่าน Flash Express, Kerry Express, และ J&T Express • สั่งซื้อก่อน 14:00 น. จัดส่งออกในวันเดียวกัน • ยอดช้อปอุปกรณ์ครบ 500 บาท ส่งฟรีพัสดุด่วนทั่วประเทศ
            </p>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 9 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 10: TOPIC 10 - WARRANTY & RETURNS -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 10: การันตี & เคลมสินค้า</span>
        </div>

        <div class="section-header">
            <span class="section-badge">10</span>
            <h2 class="section-title">การการันตีสุขภาพ 180 วัน & การคืนสินค้า (Warranty & Claims)</h2>
            <span class="section-subtitle">นโยบายสวัสดิภาพสัตว์และการคุ้มครองผู้บริโภค</span>
        </div>

        <div class="card card-pink">
            <h3 style="color:#881337; font-size:14px; margin-bottom:4px;">🛡️ นโยบายการันตีคุ้มครองสุขภาพน้องแมว 180 วันเต็ม</h3>
            <table class="data-table pink-head">
                <tr>
                    <th style="width:30%;">ความคุ้มครอง</th>
                    <th>เงื่อนไขและรายละเอียดการเคลม</th>
                </tr>
                <tr>
                    <td><strong>โรคติดต่อร้ายแรง 4 โรคหลัก</strong></td>
                    <td>คุ้มครองโรคไข้หัดแมว (FPV), ลิวคีเมีย (FeLV), เอดส์แมว (FIV), และเยื่อบุช่องท้องอักเสบ (FIP) <strong>นาน 180 วัน</strong> นับจากวันรับมอบ</td>
                </tr>
                <tr>
                    <td><strong>แนวทางการชดเชย</strong></td>
                    <td>หากตรวจพบโรคตามใบรับรองแพทย์จาก รพ.สัตว์ มาตรฐาน ทางฟาร์มยินดี:
                        <ul class="bullet-list" style="margin-top:2px;">
                            <li><strong>ตัวเลือกที่ 1:</strong> ดูแลค่ารักษาพยาบาลให้ทั้งหมด หรือ</li>
                            <li><strong>ตัวเลือกที่ 2:</strong> เปลี่ยนน้องแมวตัวใหม่ในเกรดเดียวกันให้ทันที หรือ</li>
                            <li><strong>ตัวเลือกที่ 3:</strong> คืนเงินเต็มจำนวน 100%</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td><strong>โรคทางพันธุกรรม (Genetic)</strong></td>
                    <td>คุ้มครองโรคหัวใจโต (HCM) และโรคถุงน้ำในไต (PKD) ตลอดอายุขัย 1 ปีแรก</td>
                </tr>
                <tr>
                    <td><strong>เอกสารประกอบการเคลม</strong></td>
                    <td>ใบรับรองแพทย์จากสัตวแพทย์ที่มีใบประกอบวิชาชีพ + สมุดวัคซีนประจำตัวน้องแมว</td>
                </tr>
            </table>
        </div>

        <div class="card card-cream">
            <h3 style="color:#9a3412; font-size:13.5px; margin-bottom:4px;">📦 นโยบายการเปลี่ยน/คืนสินค้าอุปกรณ์และของใช้</h3>
            <ul class="bullet-list" style="font-size:11.5px;">
                <li><strong>ระยะเวลาที่แจ้งได้:</strong> ภายใน <strong>7 วัน</strong> หลังจากได้รับพัสดุ</li>
                <li><strong>กรณีสินค้าชำรุดเสียหาย / ใช้งานไม่ได้:</strong> ส่งสินค้าชิ้นใหม่ให้ทันทีโดยไม่มีค่าใช้จ่ายเพิ่มเติม</li>
                <li><strong>กรณีร้านส่งสินค้าผิดรุ่น/สี/ขนาด:</strong> ทางร้านจัดส่งตัวที่ถูกต้องให้ทันที พร้อมรับผิดชอบค่าส่งกลับทั้งหมด</li>
                <li><strong>เงื่อนไขสินค้า:</strong> สินค้าต้องอยู่ในสภาพเดิม ยังไม่ผ่านการใช้งานโดยสัตว์เลี้ยง และมีบรรจุภัณฑ์ครบถ้วน</li>
            </ul>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 10 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 11: TOPIC 11 - FAQ PART 1 (Q1 - Q10) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 11: FAQ ตอนที่ 1</span>
        </div>

        <div class="section-header" style="margin-bottom:8px; padding:6px 10px;">
            <span class="section-badge">11</span>
            <h2 class="section-title">คำถามที่พบบ่อยสำหรับ AI Chatbot (FAQ Part 1)</h2>
            <span class="section-subtitle">คำถาม-คำตอบหมวดทั่วไป สินค้า และการจัดส่ง (ข้อ 1 - 10)</span>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q1: ร้าน Purrfect Shop มีหน้าร้านไหม ตั้งอยู่ที่ไหน และเปิดกี่โมง?</div>
            <div class="faq-a">A: มีหน้าร้านและฟาร์มครับ ตั้งอยู่ที่ 88/9 อาคารเพอร์เฟกต์ ทาวเวอร์ ถ.สุขุมวิท 71 วัฒนา กทม. เปิดบริการทุกวัน 09:00 – 19:00 น. นัดหมายเข้าชมล่วงหน้าได้ครับ 🐾</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q2: น้องแมวที่ฟาร์มเป็นพันธุ์แท้ไหม มีใบเพ็ดดีกรีรับรองหรือไม่?</div>
            <div class="faq-a">A: สายพันธุ์แท้ 100% ทุกตัวครับ มีใบเพ็ดดีกรีจากสมาคมสากล (CFA / TICA / WCF) พร้อมฝังไมโครชิปและสมุดวัคซีนประจำตัวครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q3: มีบริการจัดส่งต่างจังหวัดไหม คิดค่าส่งเท่าไร?</div>
            <div class="faq-a">A: มีบริการส่งฟรีทั่วประเทศไทยครับ! 🚐 จัดส่งด้วยรถ Pet Taxi VIP ปรับอากาศควบคุมอุณหภูมิ 25°C มีพี่เลี้ยงดูแลตลอดการเดินทาง มั่นใจได้ 100% ครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q4: ถ้าอยู่คอนโด แนะนำน้องแมวสายพันธุ์ไหนดีที่สุด?</div>
            <div class="faq-a">A: แนะนำ <strong>British Shorthair</strong> หรือ <strong>Persian</strong> ครับ เพราะนิสัยนิ่ง เงียบสงบ ไม่ส่งเสียงร้องโวยวาย ไม่ต้องการพื้นที่วิ่งเล่นเยอะ และปรับตัวเข้ากับคอนโดได้ดีเยี่ยมครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q5: เป็นโรคภูมิแพ้ขนแมว แต่อยากเลี้ยงแมว มีพันธุ์ไหนแนะนำไหม?</div>
            <div class="faq-a">A: แนะนำน้อง <strong>Canadian Sphynx (สฟิงซ์ไร้ขน)</strong> ครับ ขนร่วง 0% ผิวสัมผัสอุ่นนุ่ม ไร้ปัญหาขนฟุ้งกระจาย และไม่สะสมไรฝุ่น ปลอดภัยสำหรับคนเป็นภูมิแพ้ครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q6: ชำระเงินอย่างไร มีบริการผ่อนชำระไหม?</div>
            <div class="faq-a">A: รองรับ PromptPay QR Code, โอนธนาคาร, บัตรเครดิต และมีบริการ <strong>ผ่อนชำระ 0% นานสูงสุด 10 เดือน</strong> ผ่านบัตรเครดิตชั้นนำครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q7: ค่ามัดจำจองน้องแมวเท่าไร และถ้ายกเลิกได้เงินคืนไหม?</div>
            <div class="faq-a">A: มัดจำเริ่มต้นเพียง 5,000 บาทครับ หากก่อนส่งมอบตรวจพบปัญหาสุขภาพ ทางฟาร์มยินดีคืนเงินมัดจำเต็มจำนวน 100% ทันทีครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q8: สมาชิกใหม่มีโปรโมชั่นส่วนลดอะไรบ้าง?</div>
            <div class="faq-a">A: สมาชิกใหม่สามารถใช้โค้ด <code>CAT10OFF</code> รับส่วนลดทันที 10% พร้อมรับฟรี Starter Kit 11 รายการ มูลค่า 4,500 บาทในเดือนนี้ครับ 🎉</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q9: น้องแมวอายุเท่าไรถึงจะพร้อมย้ายบ้าน?</div>
            <div class="faq-a">A: น้องแมวจะพร้อมย้ายบ้านที่อายุประมาณ <strong>2.5 – 3 เดือน</strong> ขึ้นไป หลังจากได้รับวัคซีนครบ 2 เข็ม ตรวจแล็บผ่าน และทานอาหารเม็ดแข็งได้สมบูรณ์แล้วครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q10: สินค้าอุปกรณ์ทั่วไป ใช้เวลาจัดส่งกี่วัน?</div>
            <div class="faq-a">A: กทม.และปริมณฑล 1-2 วันทำการ, ต่างจังหวัด 2-3 วันทำการ ผ่านขนส่งด่วน Flash/Kerry ครับ</div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 11 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 12: TOPIC 11 - FAQ PART 2 (Q11 - Q20) -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 11: FAQ ตอนที่ 2</span>
        </div>

        <div class="section-header" style="margin-bottom:8px; padding:6px 10px;">
            <span class="section-badge">11</span>
            <h2 class="section-title">คำถามที่พบบ่อยสำหรับ AI Chatbot (FAQ Part 2)</h2>
            <span class="section-subtitle">คำถาม-คำตอบหมวดการันตี สุขภาพ และบริการหลังการขาย (ข้อ 11 - 20)</span>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q11: การันตีสุขภาพ 180 วัน ครอบคลุมโรคอะไรบ้าง?</div>
            <div class="faq-a">A: ครอบคลุม 4 โรคติดต่อร้ายแรง ได้แก่ ไข้หัดแมว (FPV), ลิวคีเมีย (FeLV), เอดส์แมว (FIV), และเยื่อบุช่องท้องอักเสบ (FIP) หากพบโรคยินดีรักษาฟรี เปลี่ยนตัวใหม่ หรือคืนเงิน 100% ครับ 🛡️</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q12: ได้รับอุปกรณ์อะไรบ้างในชุด Starter Kit 11 รายการ?</div>
            <div class="faq-a">A: ประกอบด้วย กระเป๋าเดินทางแคปซูล, ชามอาหารคู่สแตนเลส, กระบะทราย, ทรายเต้าหู้ 2 ถุง, อาหารพรีเมียม 2 กก., ขนมแมวเลีย, ที่ลับเล็บ, ของเล่น 3 ชิ้น, และแชมพูอาบน้ำแมวครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q13: มือใหม่ไม่เคยเลี้ยงแมวมาก่อนเลย เลี้ยงยากไหม มีคนคอยให้คำปรึกษาไหม?</div>
            <div class="faq-a">A: ไม่ยากเลยครับ! ทางฟาร์มมีทีมผู้เชี่ยวชาญและสัตวแพทย์คอยให้คำปรึกษาผ่าน LINE ตลอด 24 ชม. พร้อมคู่มือการเลี้ยงและระบบแจ้งเตือนฉีดวัคซีนอัตโนมัติครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q14: สามารถขอดูรูปหรือวิดีโอน้องแมวตัวจริงเพิ่มเติมได้ทางไหน?</div>
            <div class="faq-a">A: แจ้งรหัสหรือสายพันธุ์ที่สนใจกับแอดมินในแชทนี้ได้เลยครับ แอดมินจะส่งคลิปวิดีโอน้องแมวปัจจุบัน หรือนัดหมาย Video Call ชมสดๆ ได้ทันทีครับ 📹</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q15: ในบ้านมีเด็กเล็ก แนะนำน้องแมวพันธุ์ไหนที่ปลอดภัย ไม่ข่วน?</div>
            <div class="faq-a">A: แนะนำ <strong>Ragdoll (แร็กดอลล์)</strong> ครับ ได้รับฉายาว่า "Puppy Cat" นิสัยอ่อนโยนมาก ตัวนิ่ม ไม่กางเล็บ ชอบให้อุ้มและเข้ากับเด็กๆ ได้อย่างปลอดภัย 100% ครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q16: ถ้าสินค้าอุปกรณ์ชำรุดเสียหายตอนได้รับพัสดุ เคลมอย่างไร?</div>
            <div class="faq-a">A: ถ่ายภาพหรือวิดีโอตอนเปิดกล่องส่งมาที่ LINE @purrfectshop ภายใน 7 วัน ทางร้านจะส่งสินค้าชิ้นใหม่ให้ทันทีโดยไม่มีค่าใช้จ่ายครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q17: ตรวจสอบสถานะการจัดส่งน้องแมวและพัสดุได้ที่ไหน?</div>
            <div class="faq-a">A: ลูกค้าสามารถนำเลข Order ID เข้าไปตรวจสอบพิกัด GPS สดของรถ Pet Taxi ได้ที่หน้าเว็บ <code>/tracking.html</code> ได้ตลอด 24 ชั่วโมงครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q18: น้องแมวเมนคูน ตัวใหญ่แค่ไหน และโตเต็มที่กี่กิโลกรัม?</div>
            <div class="faq-a">A: เมนคูนเป็นแมวบ้านที่ใหญ่ที่สุดในโลก เพศผู้โตเต็มที่หนักได้ถึง 8 – 12 กก. ลำตัวยาวได้ถึง 1 เมตร สง่างามและใจดีมากครับ 🦁</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q19: มีบริการรับฝากเลี้ยงน้องแมวเมื่อเจ้าของไปเที่ยวไหม?</div>
            <div class="faq-a">A: มีบริการ Purrfect Cat Hotel & Spa สำหรับลูกค้ารับเลี้ยงน้องแมวจากฟาร์มเรา ได้รับส่วนลดค่าฝากเลี้ยง 20% ตลอดชีพครับ</div>
        </div>

        <div class="faq-item">
            <div class="faq-q">Q20: ติดต่อเจ้าหน้าที่มนุษย์ (Human Agent) ได้อย่างไร?</div>
            <div class="faq-a">A: พิมพ์คำว่า <strong>"ติดต่อเจ้าหน้าที่"</strong> หรือ <strong>"ขอคุยกับแอดมิน"</strong> ในแชท ระบบจะโอนสายให้แอดมินผู้เชี่ยวชาญดูแลทันทีครับ 👩‍💼</div>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 12 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 13: TOPICS 12 & 13 - AI RECOMMENDATION & RULES -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 12 & 13: ระบบแนะนำ & กฎตรรกะ AI</span>
        </div>

        <div class="section-header">
            <span class="section-badge">12-13</span>
            <h2 class="section-title">ระบบคัดกรอง และ กฎในการแนะนำสินค้า (AI Rules & Decision Tree)</h2>
            <span class="section-subtitle">คำถามคัดกรองและตรรกะ If-Else สำหรับ Chatbot</span>
        </div>

        <div class="card card-pink">
            <h3 style="color:#be123c; font-size:13.5px; margin-bottom:4px;">📋 หมวดที่ 12: ชุดคำถามคัดกรองความต้องการ (Interactive Screening Flow)</h3>
            <p style="font-size:11px; color:#64748b; margin-bottom:6px;">เมื่อลูกค้าต้องการคำแนะนำ ให้ AI ถามคำถาม 4 ข้อง่ายๆ เพื่อประมวลผล:</p>
            <div class="grid-2" style="font-size:11px;">
                <div style="background:#ffffff; padding:6px 8px; border-radius:6px; border:1px solid #fecdd3;">
                    <strong>ข้อ 1: สถานที่อยู่อาศัย</strong><br>
                    • A) คอนโด / หอพัก (พื้นที่จำกัด)<br>
                    • B) บ้านเดี่ยว / ทาวน์โฮม (มีพื้นที่)
                </div>
                <div style="background:#ffffff; padding:6px 8px; border-radius:6px; border:1px solid #fecdd3;">
                    <strong>ข้อ 2: ประสบการณ์การเลี้ยง</strong><br>
                    • A) มือใหม่ ไม่เคยเลี้ยงมาก่อน<br>
                    • B) เคยเลี้ยงแมว / มีประสบการณ์
                </div>
                <div style="background:#ffffff; padding:6px 8px; border-radius:6px; border:1px solid #fecdd3;">
                    <strong>ข้อ 3: นิสัยแมวที่ชอบ</strong><br>
                    • A) นิ่ง สงบ ไม่กวนใจ นอนเก่ง<br>
                    • B) ขี้อ้อน ติดคน นุ่มนิ่ม<br>
                    • C) ซน ร่าเริง ชอบเล่นพลังเยอะ
                </div>
                <div style="background:#ffffff; padding:6px 8px; border-radius:6px; border:1px solid #fecdd3;">
                    <strong>ข้อ 4: งบประมาณที่ตั้งไว้</strong><br>
                    • A) ไม่เกิน 20,000 ฿<br>
                    • B) 20,000 – 40,000 ฿<br>
                    • C) 40,000 ฿ ขึ้นไป (เกรดประกวด)
                </div>
            </div>
        </div>

        <div class="card card-cream">
            <h3 style="color:#9a3412; font-size:13.5px; margin-bottom:4px;">⚙️ หมวดที่ 13: กฎในการแนะนำ (If-Else Recommendation Decision Rules)</h3>
            <table class="data-table" style="font-size:11px;">
                <thead>
                    <tr style="background:#fff7ed; color:#9a3412;">
                        <th>เงื่อนไขความต้องการของลูกค้า (Conditions)</th>
                        <th>สายพันธุ์ & แพ็กเกจที่ระบบต้องแนะนำ (Outputs)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>IF:</strong> คอนโด + มือใหม่ + ชอบความสงบ</td>
                        <td>➡️ <strong>แนะนำ British Shorthair</strong> (ยอดนิยม #1 เลี้ยงง่าย ไม่ร้องกวน)</td>
                    </tr>
                    <tr>
                        <td><strong>IF:</strong> มีเด็กเล็ก + ชอบแมวขี้อ้อนมาก</td>
                        <td>➡️ <strong>แนะนำ Ragdoll</strong> หรือ <strong>Scottish Fold</strong> (อ่อนโยน ไม่กางเล็บ)</td>
                    </tr>
                    <tr>
                        <td><strong>IF:</strong> มีประวัติภูมิแพ้ขนสัตว์</td>
                        <td>➡️ <strong>แนะนำ Canadian Sphynx</strong> (ไร้ขน 0% ไร้ภูมิแพ้)</td>
                    </tr>
                    <tr>
                        <td><strong>IF:</strong> ชอบแมวตัวใหญ่ อลังการ ฉลาด ชอบกิจกรรม</td>
                        <td>➡️ <strong>แนะนำ Maine Coon Giant</strong> (ยักษ์ใหญ่ใจดี สายเลือดแชมป์)</td>
                    </tr>
                    <tr>
                        <td><strong>IF:</strong> งบประหยัด ไม่เกิน 15,000 - 20,000 ฿</td>
                        <td>➡️ <strong>แนะนำ วิเชียรมาศ (Siamese)</strong> หรือ <strong>Persian Classic</strong></td>
                    </tr>
                    <tr>
                        <td><strong>IF:</strong> ลูกค้าถามหาของขวัญ / ซื้อให้น้องแมว</td>
                        <td>➡️ <strong>แนะนำ Deluxe Starter Kit</strong> หรือ <strong>น้ำพุแมวไร้สาย</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 13 จาก 14</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- PAGE 14: TOPIC 14 - CONTACT & AFTER-SALES -->
    <!-- =================================================================== -->
    <div class="page">
        <div class="page-header">
            <span class="brand-title">🐾 PURRFECT SHOP • AI CHATBOT KNOWLEDGE BASE</span>
            <span class="doc-tag">หมวดที่ 14: ติดต่อ & บริการหลังการขาย</span>
        </div>

        <div class="section-header">
            <span class="section-badge">14</span>
            <h2 class="section-title">ข้อมูลติดต่อและบริการหลังการขาย (Contact & After-Sales Service)</h2>
            <span class="section-subtitle">การดูแลระยะยาวและช่องทางประสานงานทีมงาน</span>
        </div>

        <div class="card card-pink">
            <h3 style="color:#be123c; font-size:14px; margin-bottom:6px;">🤝 บริการหลังการขายแบบครบวงจร (After-Sales Care)</h3>
            <div class="grid-2" style="font-size:11.5px;">
                <div>
                    <h4 style="color:#881337; margin-bottom:3px;">🩺 1. ทีมสัตวแพทย์ที่ปรึกษาฟรีตลอดชีพ</h4>
                    <p style="color:#475569;">ปรึกษาปัญหาสุขภาพ พฤติกรรม และโภชนาการของน้องแมวผ่าน LINE OA ได้ตลอด 24 ชั่วโมง โดยมีสัตวแพทย์ประจำฟาร์มคอยตอบ</p>
                </div>
                <div>
                    <h4 style="color:#881337; margin-bottom:3px;">💉 2. ระบบแจ้งเตือนวัคซีนอัจฉริยะ</h4>
                    <p style="color:#475569;">ระบบ Vaccine Reminder อัตโนมัติ แจ้งเตือนนัดฉีดวัคซีนประจำปีและถ่ายพยาธิผ่าน LINE และหน้าเว็บ <code>/vaccine_reminder.html</code></p>
                </div>
                <div>
                    <h4 style="color:#881337; margin-bottom:3px;">📜 3. การจัดส่งเอกสารใบเพ็ดดีกรีฉบับจริง</h4>
                    <p style="color:#475569;">ใบเพ็ดสากลฉบับจริง (CFA/WCF/TICA) จะถูกจัดส่งผ่าน EMS ติดตามได้ถึงบ้านภายใน 14-30 วันหลังจากย้ายบ้าน</p>
                </div>
                <div>
                    <h4 style="color:#881337; margin-bottom:3px;">🏨 4. สิทธิพิเศษโรงแรมและสปาแมว</h4>
                    <p style="color:#475569;">รับสิทธิ์ส่วนลด 20% สำหรับบริการตัดขน สปา และฝากเลี้ยงที่ Purrfect Cat Hotel ตลอดอายุขัยของน้องแมว</p>
                </div>
            </div>
        </div>

        <div class="card card-cream">
            <h3 style="color:#9a3412; font-size:13.5px; margin-bottom:4px;">📞 สรุปข้อมูลช่องทางการติดต่อประสานงาน</h3>
            <table class="data-table" style="font-size:11.5px;">
                <tr>
                    <td style="width:25%;"><strong>LINE Official Account</strong></td>
                    <td><strong>@purrfectshop</strong> (มี @ นำหน้า) — ให้บริการตอบแชทตลอด 24 ชม.</td>
                </tr>
                <tr>
                    <td><strong>สายด่วนผู้เชี่ยวชาญ</strong></td>
                    <td><strong>02-888-9999</strong> หรือ <strong>095-888-7777</strong> (เวลา 08:30 - 20:00 น.)</td>
                </tr>
                <tr>
                    <td><strong>Facebook Fanpage</strong></td>
                    <td><strong>Purrfect Shop Cattery & Boutique</strong> (facebook.com/purrfectshop)</td>
                </tr>
                <tr>
                    <td><strong>เว็บไซต์หลัก</strong></td>
                    <td><code>https://patches660.github.io/Purrfect-Shop/</code></td>
                </tr>
                <tr>
                    <td><strong>ที่ตั้งฟาร์ม & โชว์รูม</strong></td>
                    <td>88/9 อาคารเพอร์เฟกต์ ทาวเวอร์ ถ.สุขุมวิท 71 วัฒนา กทม. 10110</td>
                </tr>
            </table>
        </div>

        <div class="bot-prompt-box">
            <strong>🤖 Human Handoff Rule:</strong> หากลูกค้าต้องการคุยกับมนุษย์ แชทบอทจะส่งข้อความ: <em>"น้องพูร์รี่ได้ส่งเรื่องให้พี่แอดมินผู้เชี่ยวชาญแล้วค่ะ เจ้าหน้าที่จะติดต่อกลับภายใน 5 นาที หรือโทรสายด่วน 02-888-9999 ได้เลยนะคะ 🐾"</em>
        </div>

        <div class="page-footer">
            <span>Purrfect Shop Knowledge Base</span>
            <span>หน้า 14 จาก 14</span>
        </div>
    </div>

</body>
</html>
"""

with open(html_path, "w", encoding="utf-8") as f:
    f.write(html_content)

print(f"[OK] Generated Knowledge Base HTML at: {html_path}")

# Convert HTML to PDF using Edge/Chrome
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
        print(f"[OK] PDF successfully generated! File: {pdf_path} ({size_kb:.1f} KB)")
        
        art_pdf = os.path.join(r"C:\Users\Windows\.gemini\antigravity\brain\63a36a4c-6ead-4c37-aede-64d365cc7888", "Purrfect_Shop_Chatbot_Knowledge_Base.pdf")
        shutil.copy2(pdf_path, art_pdf)
        print(f"[OK] Copied to artifacts directory: {art_pdf}")
