# 🐾 คู่มือการนำไฟล์ไปใช้งานบน Dify.ai แบบ 1-Click

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