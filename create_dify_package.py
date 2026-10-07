# -*- coding: utf-8 -*-
"""
create_dify_package.py
Generates ready-to-use dataset and app configuration for Dify.ai
"""
import json
import csv
import os

root_dir = os.path.dirname(os.path.abspath(__file__))
json_file = os.path.join(root_dir, 'chatbot_knowledge_base.json')

with open(json_file, 'r', encoding='utf-8') as f:
    kb = json.load(f)

output_dir = os.path.join(root_dir, 'DIFY_AI_PACKAGE')
os.makedirs(output_dir, exist_ok=True)

# 1. Dify QA CSV (Standard format: question, answer)
csv_file = os.path.join(output_dir, '1_Dify_QA_Dataset.csv')
with open(csv_file, 'w', encoding='utf-8-sig', newline='') as f:
    writer = csv.writer(f)
    writer.writerow(['question', 'answer'])
    for item in kb:
        q = item.get('question', '').strip()
        ans = item.get('answer', '').strip()
        writer.writerow([q, ans])

print(f"[OK] Created 1_Dify_QA_Dataset.csv ({len(kb)} QA pairs)")

# 2. Dify Markdown Document
md_file = os.path.join(output_dir, '2_Dify_Knowledge_Markdown.md')
with open(md_file, 'w', encoding='utf-8') as f:
    f.write('# 🐾 ฐานข้อมูลความรู้ธุรกิจ Purrfect Shop สำหรับ AI Chatbot (Dify.ai)\n\n')
    current_cat = ''
    for item in kb:
        cat = item.get('category', 'ทั่วไป')
        if cat != current_cat:
            current_cat = cat
            f.write(f'\n## 📂 หมวดหมู่: {current_cat}\n\n')
        qid = item.get('id', '')
        q = item.get('question', '')
        ans = item.get('answer', '')
        tags = ', '.join(item.get('tags', []))
        f.write(f'### [{qid}] {q}\n')
        f.write(f'**คำตอบ:** {ans}\n')
        if tags:
            f.write(f'**แท็กที่เกี่ยวข้อง:** {tags}\n\n')

print("[OK] Created 2_Dify_Knowledge_Markdown.md")

# 3. System Prompt
prompt_file = os.path.join(output_dir, '3_Dify_System_Prompt.txt')
system_prompt = """# บทบาทและหน้าที่ (Role & Persona)
คุณคือ "Purrfect Bot 🐾" ผู้ช่วยอัจฉริยะและที่ปรึกษาประจำร้าน "Purrfect Shop" (ฟาร์มแมวสายพันธุ์แท้ 100% มาตรฐานสากล CFA/WCF/TICA และบูติกอุปกรณ์สัตว์เลี้ยงพรีเมียม)

# บุคลิกภาพและน้ำเสียง (Tone of Voice)
- อบอุ่น สุภาพ เป็นกันเอง รักสัตว์ และมีความรู้ลึกซึ้งเรื่องสายพันธุ์และการดูแลแมว
- ลงท้ายด้วย "ครับ/ค่ะ" อย่างนุ่มนวล และใช้อีโมจิน่ารักที่เกี่ยวกับแมว เช่น 🐾 🐱 ✨ 💖 🩺 🎁

# กฎเหล็กในการตอบคำถาม (Crucial Rules)
1. ให้ตอบคำถามโดยอ้างอิงข้อมูลจาก Knowledge Base (บริบทที่ระบบส่งให้) เป็นหลักเสมอ ห้ามกุข้อมูลราคาหรือการันตีขึ้นมาเอง
2. ข้อมูลร้านที่สำคัญ:
   - จุดเด่น: สายพันธุ์แท้ 100% มีใบเพ็ดดีกรี, การันตีสุขภาพ 180 วัน (ครอบคลุม FPV, FeLV, FIV, FIP), ตรวจแล็บ ฉีดวัคซีนครบ 2 เข็ม ฝังไมโครชิป
   - การจัดส่ง: ส่งฟรีทั่วไทยด้วยรถ Pet Taxi ปรับอากาศควบคุมอุณหภูมิ 25°C
   - ของแถม: ฟรี Starter Kit 11 รายการ มูลค่า 4,500 บาท ทุกออเดอร์
   - โปรโมชั่น: โค้ด CAT10OFF (ลด 10%), NEWMEMBER5 (ลด 5% สมาชิกใหม่), PURRFECT10 (ลด 10% เมื่อยอด 25,000฿)
   - เวลาทำการ: หน้าร้านเปิดทุกวัน 09:00 - 19:00 น. (สุขุมวิท 71 ใกล้ BTS พระโขนง) แชทออนไลน์ 24 ชม.
   - ช่องทางติดต่อ: LINE OA: @purrfectshop, โทร: 02-888-9999 หรือ 095-888-7777
3. หากลูกค้าถามคำถามที่ไม่มีใน Knowledge Base ให้ตอบอย่างสุภาพว่ากำลังประสานงานเจ้าหน้าที่แอดมินเข้ามาดูแลเพิ่มเติม พร้อมแนะนำช่องทางโทรหรือ LINE OA
"""
with open(prompt_file, 'w', encoding='utf-8') as f:
    f.write(system_prompt.strip())

print("[OK] Created 3_Dify_System_Prompt.txt")

# 4. Dify DSL YAML App Export
dsl_content = """app:
  description: 'AI Chatbot ให้คำปรึกษาสายพันธุ์แมว ราคา โปรโมชั่น และการดูแลสำหรับร้าน Purrfect Shop'
  icon: '🐱'
  icon_background: '#FFE4E6'
  mode: chat
  name: 'Purrfect Shop AI Advisor 🐾'
kind: app
model_config:
  model:
    completion_params:
      temperature: 0.3
      top_p: 0.85
    mode: chat
    name: gpt-4o-mini
    provider: openai
  opening_statement: 'สวัสดีครับยินดีต้อนรับสู่ Purrfect Shop! 🐾 ผมคือ Purrfect Bot ยินดีให้คำปรึกษาเรื่องสายพันธุ์น้องแมว ราคา การดูแล และโปรโมชั่นพิเศษ สอบถามได้เลยครับ!'
  pre_prompt: |
    คุณคือ "Purrfect Bot 🐾" ผู้ช่วยอัจฉริยะและที่ปรึกษาประจำร้าน "Purrfect Shop" (ฟาร์มแมวสายพันธุ์แท้ 100% มาตรฐานสากล CFA/WCF/TICA และบูติกอุปกรณ์สัตว์เลี้ยงพรีเมียม)
    
    หน้าที่: ตอบคำถามลูกค้าเกี่ยวกับสายพันธุ์น้องแมว, ราคา, การจัดส่ง Pet Taxi, การรับประกันสุขภาพ 180 วัน, และโปรโมชั่นต่างๆ โดยอ้างอิงจาก Knowledge Base เป็นหลัก
    น้ำเสียง: อบอุ่น สุภาพ เป็นกันเอง ลงท้ายครับ/ค่ะ และใช้อีโมจิน่ารัก 🐾🐱✨
  retriever_resource:
    enabled: true
  suggested_questions:
  - '🐱 แนะนำน้องแมวยอดนิยม เลี้ยงง่าย สำหรับมือใหม่หน่อยครับ'
  - '🏢 มีแมวพันธุ์ไหนเหมาะกับเลี้ยงในคอนโด รักความสงบ บ้าง?'
  - '🤧 เป็นภูมิแพ้ แนะนำน้องแมวขนไม่ร่วงหน่อยครับ'
  - '🎁 ตอนนี้มีโปรโมชั่นหรือโค้ดส่วนลดอะไรบ้าง?'
  - '🚗 การจัดส่งน้องแมวต่างจังหวัดคิดค่าส่งอย่างไร?'
  user_input_form: []
version: 0.1.5
"""

dsl_file = os.path.join(output_dir, '4_Purrfect_Shop_Dify_App_DSL.yml')
with open(dsl_file, 'w', encoding='utf-8') as f:
    f.write(dsl_content)

print("[OK] Created 4_Purrfect_Shop_Dify_App_DSL.yml")

# 5. Readme Guide
guide_file = os.path.join(output_dir, 'README_DIFY_GUIDE.md')
guide_text = """# 🐾 คู่มือการนำไฟล์ไปใช้งานบน Dify.ai แบบ 1-Click

โฟลเดอร์นี้รวบรวมไฟล์ที่จัดโครงสร้างให้เข้ากับระบบของ **Dify.ai** โดยเฉพาะ:

---

## 📁 รายการไฟล์ในโฟลเดอร์นี้:

1. **`1_Dify_QA_Dataset.csv`** ➔ ⭐ **(แนะนำที่สุด)**
   - นำเข้าเป็น **Q&A Segment** (ถาม-ตอบ 1:1) ทั้ง 78 ข้อ
   - ช่วยให้บอทค้นหาคำตอบได้แม่นยำ 100% โดยข้อความไม่ถูกตัดทอน

2. **`2_Dify_Knowledge_Markdown.md`** ➔
   - เอกสาร Markdown จัดหมวดหมู่ 14 หมวด สำหรับ Import แบบ Document / General Indexing

3. **`3_Dify_System_Prompt.txt`** ➔
   - ข้อความ System Instructions สำหรับคัดลอกไปวางในหน้า Chatbot Studio

4. **`4_Purrfect_Shop_Dify_App_DSL.yml`** ➔ ⭐ **(1-Click Import)**
   - ไฟล์ Import App สำเร็จรูปใน Dify นำเข้าแล้วได้ Bot พร้อมตั้งค่าข้อความต้อนรับ + คำถามแนะนำทันที

---

## 🚀 วิธีตั้งค่าใน Dify.ai (ใช้เวลา 2 นาที):

### ขั้นตอนที่ 1: สร้าง Knowledge Base ใน Dify
1. เข้าไปที่ **Dify.ai** ➔ เลือกเมนู **Knowledge** (ฐานความรู้)
2. กดปุ่ม **Create Knowledge** ➔ เลือก **Import from file**
3. ลากไฟล์ **`1_Dify_QA_Dataset.csv`** เข้าไป
4. ในหน้าตั้งค่า Indexing:
   - เลือกรูปแบบเป็น **QA Mode** (หรือ High Quality)
   - กด **Save & Process** (รอประมวลผลประมาณ 10-20 วินาที)

### ขั้นตอนที่ 2: สร้าง App Chatbot (วิธีนำเข้าอัตโนมัติ)
1. ไปที่เมนู **Studio** ➔ กดปุ่ม **Import DSL** (หรือ Create from Blank ➔ Chatbot)
2. เลือกไฟล์ **`4_Purrfect_Shop_Dify_App_DSL.yml`**
3. ในหน้าตั้งค่าบอท ตรงหัวข้อ **Context** ➔ กด `+ Add` แล้วเลือก Knowledge Base ที่สร้างไว้ในขั้นตอนที่ 1
4. กด **Publish** ➔ บอทพร้อมใช้งานและเชื่อมต่อ LINE OA ได้ทันที! 🐾
"""
with open(guide_file, 'w', encoding='utf-8') as f:
    f.write(guide_text.strip())

print("[OK] Created README_DIFY_GUIDE.md")
