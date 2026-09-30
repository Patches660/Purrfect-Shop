<?php
require_once __DIR__ . '/header.php';
?>

<div class="container" style="max-width: 1000px; margin: 2rem auto 5rem auto; padding: 0 1rem;">
    <!-- Hero Banner -->
    <div class="quiz-hero-banner" style="background: linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 50%, #E9D5FF 100%); border-radius: 28px; padding: 3rem 2rem; text-align: center; border: 2px solid #D8B4FE; box-shadow: 0 12px 35px rgba(168, 85, 247, 0.12); margin-bottom: 2.5rem; position: relative; overflow: hidden;">
        <div style="display: inline-flex; align-items: center; gap: 8px; background: #9333EA; color: #fff; padding: 0.45rem 1.25rem; border-radius: 999px; font-weight: 800; font-size: 0.9rem; margin-bottom: 1rem; box-shadow: 0 4px 12px rgba(147, 51, 234, 0.3);">
            🔮 AI CAT MATCHMAKER QUIZ
        </div>
        <h1 class="quiz-main-title" style="font-size: 2.5rem; font-weight: 900; color: #581C87; margin-bottom: 0.8rem;">
            ค้นหาสายพันธุ์น้องแมวในฝันที่เหมาะกับคุณ 🐾✨
        </h1>
        <p class="quiz-sub-desc" style="font-size: 1.15rem; color: #6B21A8; max-width: 760px; margin: 0 auto; line-height: 1.6;">
            ตอบคำถามไลฟ์สไตล์ง่ายๆ 5 ข้อ ระบบ AI จะคำนวณและจับคู่สายพันธุ์น้องแมวที่ตรงกับบ้าน นิสัย และเวลาของคุณที่สุด พร้อมคูปองพิเศษ!
        </p>
    </div>

    <!-- Quiz Wizard Box -->
    <div id="quiz-container" style="background: #fff; border-radius: 26px; padding: 2.5rem; border: 2px solid #E2E8F0; box-shadow: 0 10px 35px rgba(0,0,0,0.06); position: relative;">
        
        <!-- Progress Bar -->
        <div id="quiz-progress-bar-wrapper" style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem; font-size: 0.85rem; font-weight: 800; color: #6B21A8;">
                <span id="quiz-step-label">คำถามที่ 1 จาก 5</span>
                <span id="quiz-progress-percent">20%</span>
            </div>
            <div style="width: 100%; height: 10px; background: #F3E8FF; border-radius: 999px; overflow: hidden;">
                <div id="quiz-progress-bar" style="width: 20%; height: 100%; background: linear-gradient(90deg, #A855F7, #EC4899); border-radius: 999px; transition: width 0.4s ease;"></div>
            </div>
        </div>

        <!-- Question 1 -->
        <div class="quiz-step" id="step-1" style="display: block;">
            <div style="font-size: 0.9rem; font-weight: 800; color: #A855F7; margin-bottom: 0.4rem;">QUESTION 1</div>
            <h2 style="font-size: 1.5rem; font-weight: 900; color: #1E293B; margin-bottom: 1.5rem;">
                🏡 สถานที่พักอาศัยและพื้นที่ของคุณเป็นแบบไหน?
            </h2>
            <div class="quiz-options-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div class="quiz-option-card" onclick="selectAnswer(1, 'living', 'condo', this)">
                    <div class="quiz-opt-icon">🏢</div>
                    <div class="quiz-opt-title">คอนโด / หอพัก</div>
                    <div class="quiz-opt-desc">พื้นที่กะทัดรัด ต้องการแมวที่ไม่ส่งเสียงดัง ไม่กระโดดโลดโผนมาก</div>
                </div>
                <div class="quiz-option-card" onclick="selectAnswer(1, 'living', 'townhome', this)">
                    <div class="quiz-opt-icon">🏘️</div>
                    <div class="quiz-opt-title">ทาวน์โฮม / บ้านแฝด</div>
                    <div class="quiz-opt-desc">มีพื้นที่วิ่งเล่นในบ้านปานกลาง จัดมุมคอนโดแมวได้</div>
                </div>
                <div class="quiz-option-card" onclick="selectAnswer(1, 'living', 'single_house', this)">
                    <div class="quiz-opt-icon">🏡</div>
                    <div class="quiz-opt-title">บ้านเดี่ยวพร้อมสวน / พื้นที่กว้าง</div>
                    <div class="quiz-opt-desc">พื้นที่กว้างขวาง เหมาะกับแมวตัวใหญ่หรือพลังงานสูง</div>
                </div>
            </div>
        </div>

        <!-- Question 2 -->
        <div class="quiz-step" id="step-2" style="display: none;">
            <div style="font-size: 0.9rem; font-weight: 800; color: #A855F7; margin-bottom: 0.4rem;">QUESTION 2</div>
            <h2 style="font-size: 1.5rem; font-weight: 900; color: #1E293B; margin-bottom: 1.5rem;">
                ⏰ คุณมีเวลาคลุกคลีและเล่นกับน้องแมววันละประมาณกี่ชั่วโมง?
            </h2>
            <div class="quiz-options-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div class="quiz-option-card" onclick="selectAnswer(2, 'time', 'low', this)">
                    <div class="quiz-opt-icon">💼</div>
                    <div class="quiz-opt-title">1 - 2 ชั่วโมง / วัน</div>
                    <div class="quiz-opt-desc">ทำงานนอกบ้านบ่อย ต้องการแมวอินดี้ ดูแลตัวเองได้ ไม่เหงาเกินไป</div>
                </div>
                <div class="quiz-option-card" onclick="selectAnswer(2, 'time', 'medium', this)">
                    <div class="quiz-opt-icon">🛋️</div>
                    <div class="quiz-opt-title">3 - 5 ชั่วโมง / วัน</div>
                    <div class="quiz-opt-desc">กลับมาเล่นและหวีขนช่วงเย็น มีเวลาให้ความรักสม่ำเสมอ</div>
                </div>
                <div class="quiz-option-card" onclick="selectAnswer(2, 'time', 'high', this)">
                    <div class="quiz-opt-icon">💻</div>
                    <div class="quiz-opt-title">อยู่บ้านเกือบทั้งวัน / WFH</div>
                    <div class="quiz-opt-desc">มีเวลาดูแลเต็มที่ พร้อมกอด นัวเนีย และหวีขนฟูตลอดเวลา</div>
                </div>
            </div>
        </div>

        <!-- Question 3 -->
        <div class="quiz-step" id="step-3" style="display: none;">
            <div style="font-size: 0.9rem; font-weight: 800; color: #A855F7; margin-bottom: 0.4rem;">QUESTION 3</div>
            <h2 style="font-size: 1.5rem; font-weight: 900; color: #1E293B; margin-bottom: 1.5rem;">
                💖 นิสัยน้องแมวแบบไหนที่คุณตกหลุมรักมากที่สุด?
            </h2>
            <div class="quiz-options-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div class="quiz-option-card" onclick="selectAnswer(3, 'personality', 'cuddly', this)">
                    <div class="quiz-opt-icon">🧸</div>
                    <div class="quiz-opt-title">ขี้อ้อน ติดคน ยอมให้อุ้ม</div>
                    <div class="quiz-opt-desc">ชอบนอนตัก ร้องเรียกหา อุ้มแล้วตัวนิ่มละลายในอ้อมแขน</div>
                </div>
                <div class="quiz-option-card" onclick="selectAnswer(3, 'personality', 'calm', this)">
                    <div class="quiz-opt-icon">👑</div>
                    <div class="quiz-opt-title">สุขุม สง่างาม เรียบร้อย</div>
                    <div class="quiz-opt-desc">ไม่วุ่นวาย นั่งมองเงียบๆ อบอุ่น เป็นผู้ดีประจำบ้าน</div>
                </div>
                <div class="quiz-option-card" onclick="selectAnswer(3, 'personality', 'playful', this)">
                    <div class="quiz-opt-icon">⚡</div>
                    <div class="quiz-opt-title">ซน ฉลาด คึกคัก ชอบคาบของ</div>
                    <div class="quiz-opt-desc">พลังงานสูง มีความกระตือรือร้น เล่นสนุก ชวนคุยเก่ง</div>
                </div>
                <div class="quiz-option-card" onclick="selectAnswer(3, 'personality', 'gentle_giant', this)">
                    <div class="quiz-opt-icon">🦁</div>
                    <div class="quiz-opt-title">ตัวใหญ่ใจดี ขนปุกปุย</div>
                    <div class="quiz-opt-desc">ขนาดอลังการ กอดเต็มไม้เต็มมือ ซื่อสัตย์เหมือนสุนัข</div>
                </div>
            </div>
        </div>

        <!-- Question 4 -->
        <div class="quiz-step" id="step-4" style="display: none;">
            <div style="font-size: 0.9rem; font-weight: 800; color: #A855F7; margin-bottom: 0.4rem;">QUESTION 4</div>
            <h2 style="font-size: 1.5rem; font-weight: 900; color: #1E293B; margin-bottom: 1.5rem;">
                ✂️ คุณพร้อมดูแลเรื่อง "การแปรงขนและตัดแต่งขน" ระดับใด?
            </h2>
            <div class="quiz-options-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div class="quiz-option-card" onclick="selectAnswer(4, 'grooming', 'easy', this)">
                    <div class="quiz-opt-icon">🧼</div>
                    <div class="quiz-opt-title">ดูแลง่าย ขนสั้น ไม่ยุ่งยาก</div>
                    <div class="quiz-opt-desc">แปรงขนสัปดาห์ละ 1-2 ครั้ง ขนไม่พันกัน เลี้ยงสบาย</div>
                </div>
                <div class="quiz-option-card" onclick="selectAnswer(4, 'grooming', 'medium', this)">
                    <div class="quiz-opt-icon">✨</div>
                    <div class="quiz-opt-title">ขนแน่นนุ่ม แปรงได้เรื่อยๆ</div>
                    <div class="quiz-opt-desc">ขนสั้นแน่นหรือกึ่งยาว แปรงวันเว้นวันเพื่อความเงางาม</div>
                </div>
                <div class="quiz-option-card" onclick="selectAnswer(4, 'grooming', 'fluffy', this)">
                    <div class="quiz-opt-icon">👑</div>
                    <div class="quiz-opt-title">พร้อมแปรงขนยาวฟูทุกวัน</div>
                    <div class="quiz-opt-desc">ชอบแมวขนยาวดุจปุยเมฆ พร้อมหวีและพาเข้ากรูมมิ่ง</div>
                </div>
            </div>
        </div>

        <!-- Question 5 -->
        <div class="quiz-step" id="step-5" style="display: none;">
            <div style="font-size: 0.9rem; font-weight: 800; color: #A855F7; margin-bottom: 0.4rem;">QUESTION 5</div>
            <h2 style="font-size: 1.5rem; font-weight: 900; color: #1E293B; margin-bottom: 1.5rem;">
                🎯 ประสบการณ์ในการเลี้ยงดูแมวของคุณ?
            </h2>
            <div class="quiz-options-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div class="quiz-option-card" onclick="selectAnswer(5, 'exp', 'beginner', this)">
                    <div class="quiz-opt-icon">🌱</div>
                    <div class="quiz-opt-title">มือใหม่ป้ายแดง (แมวตัวแรก)</div>
                    <div class="quiz-opt-desc">ต้องการแมวที่เลี้ยงง่าย ปรับตัวเข้ากับสิ่งแวดล้อมไว สุขภาพแข็งแรง</div>
                </div>
                <div class="quiz-option-card" onclick="selectAnswer(5, 'exp', 'experienced', this)">
                    <div class="quiz-opt-icon">🐾</div>
                    <div class="quiz-opt-title">เคยเลี้ยงมาบ้างแล้ว</div>
                    <div class="quiz-opt-desc">เข้าใจพฤติกรรมแมว รู้จักการสังเกตสุขภาพและการดูแลพื้นฐาน</div>
                </div>
                <div class="quiz-option-card" onclick="selectAnswer(5, 'exp', 'expert', this)">
                    <div class="quiz-opt-icon">🏆</div>
                    <div class="quiz-opt-title">ทาสแมวตัวจริงระดับ VIP</div>
                    <div class="quiz-opt-desc">พร้อมทุ่มเทเพื่อสายพันธุ์พรีเมียม โครงสร้างสวยตามมาตรฐานสากล</div>
                </div>
            </div>
        </div>

        <!-- Calculation Loader -->
        <div id="quiz-loader" style="display: none; text-align: center; padding: 4rem 1rem;">
            <div style="font-size: 3.5rem; animation: spin 1.2s infinite linear; display: inline-block; margin-bottom: 1.5rem;">🔮</div>
            <h3 style="font-size: 1.6rem; font-weight: 900; color: #581C87; margin-bottom: 0.5rem;">AI กำลังวิเคราะห์ข้อมูลและจับคู่สายพันธุ์...</h3>
            <p style="color: #6B21A8; font-size: 1rem;">เปรียบเทียบจากสายพันธุ์แมวแท้ CFA/WCF กว่า 10 สายพันธุ์</p>
        </div>

        <!-- Result View -->
        <div id="quiz-result" style="display: none;">
            <div style="text-align: center; margin-bottom: 2.5rem;">
                <div style="display: inline-flex; align-items: center; gap: 8px; background: #DCFCE7; color: #166534; padding: 0.4rem 1.2rem; border-radius: 999px; font-weight: 800; font-size: 0.9rem; margin-bottom: 1rem;">
                    🎉 98.6% MATCH FOUND!
                </div>
                <h2 style="font-size: 2.2rem; font-weight: 900; color: #1E293B; margin-bottom: 0.4rem;">
                    น้องแมวสายพันธุ์ที่เหมาะกับคุณที่สุดคือ:
                </h2>
                <div id="result-breed-name" style="font-size: 2.6rem; font-weight: 900; background: linear-gradient(135deg, #9333EA, #EC4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 1rem;">
                    Scottish Fold (สก็อตติช โฟลด์)
                </div>
            </div>

            <!-- Match Card Showcase -->
            <div style="background: linear-gradient(135deg, #FAF5FF 0%, #FFFFFF 100%); border: 2px solid #E9D5FF; border-radius: 22px; padding: 2rem; margin-bottom: 2.5rem; display: grid; grid-template-columns: 280px 1fr; gap: 2rem; align-items: center;" class="result-showcase-grid">
                <div style="text-align: center;">
                    <img id="result-breed-img" src="assets/images/cat_scottish.jpg" alt="Match Breed" style="width: 240px; height: 240px; object-fit: cover; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.12); border: 4px solid #fff;">
                    <div id="result-badge-tag" style="margin-top: 0.8rem; display: inline-block; background: #FAF5FF; color: #9333EA; border: 1.5px solid #D8B4FE; font-size: 0.8rem; font-weight: 800; padding: 3px 12px; border-radius: 999px;">
                        🌟 หูพับ ตาโต ขี้อ้อนอันดับ 1
                    </div>
                </div>

                <div>
                    <h3 style="font-size: 1.3rem; font-weight: 900; color: #1E293B; margin-bottom: 0.8rem;">
                        ทำไมสายพันธุ์นี้ถึงลงตัวกับคุณ? 💡
                    </h3>
                    <p id="result-breed-desc" style="color: #475569; font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.2rem;">
                        สก็อตติช โฟลด์ เป็นแมวที่มีนิสัยน่ารัก อ่อนโยน เข้ากับคนง่ายมาก เสียงเบา และปรับตัวเข้ากับชีวิตในคอนโดหรือบ้านได้ดีเยี่ยม เหมาะกับผู้ที่มีเวลาเล่นช่วงเย็น และต้องการเพื่อนนอนตักที่น่ารักที่สุด
                    </p>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.8rem; margin-bottom: 1.5rem;" class="result-specs-grid">
                        <div style="background: #fff; padding: 0.75rem 1rem; border-radius: 12px; border: 1px solid #E2E8F0;">
                            <span style="font-size: 0.75rem; color: #64748B; font-weight: 700;">ระดับความขี้อ้อน:</span>
                            <div style="font-weight: 800; color: #EC4899; font-size: 0.95rem;">⭐⭐⭐⭐⭐ (สูงสุด)</div>
                        </div>
                        <div style="background: #fff; padding: 0.75rem 1rem; border-radius: 12px; border: 1px solid #E2E8F0;">
                            <span style="font-size: 0.75rem; color: #64748B; font-weight: 700;">การดูแลรักษาขน:</span>
                            <div style="font-weight: 800; color: #10B981; font-size: 0.95rem;">ง่าย (สัปดาห์ละ 2 ครั้ง)</div>
                        </div>
                        <div style="background: #fff; padding: 0.75rem 1rem; border-radius: 12px; border: 1px solid #E2E8F0;">
                            <span style="font-size: 0.75rem; color: #64748B; font-weight: 700;">ความเงียบสงบ:</span>
                            <div style="font-weight: 800; color: #3B82F6; font-size: 0.95rem;">เงียบ ไม่ส่งเสียงรบกวน</div>
                        </div>
                        <div style="background: #fff; padding: 0.75rem 1rem; border-radius: 12px; border: 1px solid #E2E8F0;">
                            <span style="font-size: 0.75rem; color: #64748B; font-weight: 700;">ช่วงสินสอดประมาณ:</span>
                            <div id="result-price-range" style="font-weight: 800; color: #EA580C; font-size: 0.95rem;">25,000 - 38,000 ฿</div>
                        </div>
                    </div>

                    <!-- Secret Quiz Voucher Reward -->
                    <div style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); border: 2px dashed #F59E0B; border-radius: 14px; padding: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.8rem;">🎁</span>
                            <div>
                                <strong style="color: #92400E; font-size: 0.95rem;">รับฟรี! โค้ดส่วนลด ฿100 จากการทำ Quiz</strong>
                                <div style="font-size: 0.78rem; color: #B45309;">ใช้เป็นส่วนลดค่าสินสอดหรือสินค้าในร้านได้ทันที</div>
                            </div>
                        </div>
                        <a href="cart.php?apply_coupon=PAW100SURVEY" class="btn btn-primary btn-sm" style="background: #D97706; border-color: #B45309; font-weight: 800; font-size: 0.8rem; padding: 0.5rem 1rem;">
                            ใช้คูปอง PAW100SURVEY ➔
                        </a>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="products.php" id="result-view-cats-btn" class="btn btn-primary" style="padding: 0.85rem 1.8rem; font-weight: 800; font-size: 1rem; border-radius: 14px;">
                    🐱 ดูน้องแมวพร้อมย้ายบ้านในร้าน ➔
                </a>
                <a href="booking.php" class="btn btn-secondary" style="padding: 0.85rem 1.8rem; font-weight: 800; font-size: 1rem; border-radius: 14px; background: #fff; color: #1E293B; border: 2px solid #CBD5E1;">
                    📅 นัดหมายดูตัวจริงที่ฟาร์ม
                </a>
                <button type="button" onclick="shareQuizResult()" class="btn btn-secondary" style="padding: 0.85rem 1.5rem; font-weight: 800; font-size: 0.95rem; border-radius: 14px; background: #06C755; color: #fff; border: none; display: inline-flex; align-items: center; gap: 6px;">
                    💬 แชร์ผลลัพธ์เข้า LINE
                </button>
                <button type="button" onclick="restartQuiz()" class="btn btn-secondary" style="padding: 0.85rem 1.2rem; font-weight: 800; font-size: 0.95rem; border-radius: 14px; background: none; color: #64748B; border: 1px dashed #CBD5E1;">
                    🔄 ทำแบบทดสอบใหม่
                </button>
            </div>
        </div>

    </div>
</div>

<style>
.quiz-options-grid {
    margin-top: 1rem;
}
.quiz-option-card {
    background: #FAFAFA;
    border: 2px solid #E2E8F0;
    border-radius: 18px;
    padding: 1.5rem 1.2rem;
    cursor: pointer;
    transition: all 0.25s ease;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.quiz-option-card:hover {
    border-color: #A855F7;
    background: #FAF5FF;
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(168, 85, 247, 0.12);
}
.quiz-opt-icon {
    font-size: 2.5rem;
    margin-bottom: 0.6rem;
}
.quiz-opt-title {
    font-weight: 900;
    color: #1E293B;
    font-size: 1.05rem;
    margin-bottom: 0.4rem;
}
.quiz-opt-desc {
    font-size: 0.8rem;
    color: #64748B;
    line-height: 1.5;
}
@media (max-width: 768px) {
    .result-showcase-grid {
        grid-template-columns: 1fr !important;
        text-align: center;
    }
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
let currentQuizStep = 1;
const totalSteps = 5;
const userAnswers = {};

const breedDatabase = {
    scottish: {
        name: 'Scottish Fold (สก็อตติช โฟลด์)',
        tag: '🌟 หูพับ ตาโต ขี้อ้อนอันดับ 1',
        img: 'assets/images/cat_scottish.jpg',
        desc: 'สก็อตติช โฟลด์ เป็นแมวที่มีนิสัยน่ารัก อ่อนโยน เข้ากับคนง่ายมาก เสียงเบา และปรับตัวเข้ากับชีวิตในคอนโดหรือบ้านได้ดีเยี่ยม เหมาะกับผู้ที่มีเวลาเล่นช่วงเย็น และต้องการเพื่อนนอนตักที่น่ารักที่สุด',
        cuddle: '⭐⭐⭐⭐⭐ (สูงสุด)',
        groom: 'ง่าย (สัปดาห์ละ 2 ครั้ง)',
        quiet: 'เงียบสงบ ไม่ส่งเสียงรบกวน',
        price: '25,000 - 38,000 ฿',
        link: 'products.php?breed=scottish'
    },
    british: {
        name: 'British Shorthair (บริติช ช็อตแฮร์)',
        tag: '👑 สุขุม สง่างาม หน้ากลมแก้มแน่น',
        img: 'assets/images/cat_bengal.jpg',
        desc: 'บริติช ช็อตแฮร์ ขึ้นชื่อเรื่องความสุขุม เรียบร้อย ไม่วุ่นวาย อยู่คนเดียวเก่ง ขนแน่นนุ่มเหมือนพรมกำมะหยี่ เหมาะสำหรับคนทำงานหรือมือใหม่ที่ต้องการแมวที่ไม่เรียกร้องความสนใจตลอดเวลาแต่รักเจ้าของมาก',
        cuddle: '⭐⭐⭐⭐ (กำลังพอดี)',
        groom: 'ง่ายมาก (ขนสั้นแน่น)',
        quiet: 'เงียบมาก สุภาพเป็นผู้ดี',
        price: '28,000 - 45,000 ฿',
        link: 'products.php?breed=british'
    },
    ragdoll: {
        name: 'Ragdoll (แร็กดอลล์)',
        tag: '🧸 ตุ๊กตาผ้าตาสีฟ้า ขนฟูนุ่มละมุน',
        img: 'assets/images/cat_ragdoll.jpg',
        desc: 'แร็กดอลล์ คือนิยามของแมวใจดี อุ้มแล้วตัวนิ่มละลาย ตาสีฟ้าดั่งไพลิน ขนยาวนุ่มลื่น อ่อนหวานและเชื่องมาก เหมาะกับผู้ที่ชอบกอด ชอบหวีขน และอยู่บ้านบ่อยๆ',
        cuddle: '⭐⭐⭐⭐⭐ (กอดได้ทั้งวัน)',
        groom: 'ปานกลาง (ควรแปรงทุกวัน)',
        quiet: 'เสียงหวาน นุ่มนวล',
        price: '35,000 - 55,000 ฿',
        link: 'products.php?breed=ragdoll'
    },
    persian: {
        name: 'Persian (เปอร์เซียแท้)',
        tag: '👸 เจ้าหญิงขนยาว หรูหรา อ่อนหวาน',
        img: 'assets/images/cat_persian.jpg',
        desc: 'เปอร์เซีย เป็นแมวสายพันธุ์โบราณที่สง่างาม เรียบร้อย นิ่งสงบ ชอบการดูแลเอาใจใส่ เหมาะกับผู้ที่รักความประณีต มีเวลาหวีขนสวยๆ ทุกวัน',
        cuddle: '⭐⭐⭐⭐ (รักสงบ นั่งเคียงข้าง)',
        groom: 'ดูแลพิเศษ (แปรงขนทุกวัน)',
        quiet: 'นิ่งสงบที่สุด',
        price: '20,000 - 35,000 ฿',
        link: 'products.php?breed=persian'
    },
    bengal: {
        name: 'Bengal (เบงกอล ลายเสือดาว)',
        tag: '🐆 ปราดเปรียว ฉลาด พลังงานสูง',
        img: 'assets/images/cat_americanshorthair.jpg',
        desc: 'เบงกอล มีลวดลายเสือดาวอันโดดเด่น ฉลาด ว่องไว ชอบเล่นของเล่น ชอบปีนป่าย และเป็นแมวที่ชอบเล่นน้ำ เหมาะกับเจ้าของที่มีพื้นที่และชอบทำกิจกรรมสนุกสนานร่วมกับแมว',
        cuddle: '⭐⭐⭐⭐ (เล่นสนุก ติดตามเจ้านาย)',
        groom: 'ง่ายมาก (ขนสั้นเงางาม)',
        quiet: 'ชวนคุยเก่ง ร่าเริง',
        price: '30,000 - 50,000 ฿',
        link: 'products.php?breed=bengal'
    },
    khao_manee: {
        name: 'Khao Manee (ขาวมณี ตาสองสี)',
        tag: '💎 แมวมงคล ตาสองสี อัญมณีมีชีวิต',
        img: 'assets/images/cat_khao_manee.jpg',
        desc: 'ขาวมณี แมวโบราณหายาก ขนขาวบริสุทธิ์ ตาสองสีทรงเสน่ห์ ฉลาด ปราดเปรียว สุขภาพแข็งแรง เลี้ยงง่าย และรักเจ้าของมาก',
        cuddle: '⭐⭐⭐⭐⭐ (ขี้เล่น ขี้อ้อน)',
        groom: 'ง่ายมาก',
        quiet: 'ขี้เล่น พูดคุยสดใส',
        price: '22,000 - 38,000 ฿',
        link: 'products.php?breed=khao_manee'
    }
};

function selectAnswer(step, category, value, element) {
    userAnswers[category] = value;
    
    // Animate selection
    element.style.borderColor = '#9333EA';
    element.style.background = '#F3E8FF';
    
    setTimeout(() => {
        if (step < totalSteps) {
            goToStep(step + 1);
        } else {
            calculateResult();
        }
    }, 280);
}

function goToStep(step) {
    document.querySelectorAll('.quiz-step').forEach(el => el.style.display = 'none');
    const nextStepEl = document.getElementById(`step-${step}`);
    if (nextStepEl) {
        nextStepEl.style.display = 'block';
    }
    
    currentQuizStep = step;
    const percent = Math.round((step / totalSteps) * 100);
    document.getElementById('quiz-step-label').textContent = `คำถามที่ ${step} จาก ${totalSteps}`;
    document.getElementById('quiz-progress-percent').textContent = `${percent}%`;
    document.getElementById('quiz-progress-bar').style.width = `${percent}%`;
}

function calculateResult() {
    document.querySelectorAll('.quiz-step').forEach(el => el.style.display = 'none');
    document.getElementById('quiz-progress-bar-wrapper').style.display = 'none';
    document.getElementById('quiz-loader').style.display = 'block';

    setTimeout(() => {
        document.getElementById('quiz-loader').style.display = 'none';
        
        // AI Breed Matching Logic
        let matchedKey = 'scottish';
        const { living, time, personality, grooming } = userAnswers;

        if (personality === 'cuddly' || living === 'condo') {
            if (grooming === 'fluffy' || time === 'high') {
                matchedKey = 'ragdoll';
            } else {
                matchedKey = 'scottish';
            }
        } else if (personality === 'calm') {
            if (grooming === 'fluffy') {
                matchedKey = 'persian';
            } else {
                matchedKey = 'british';
            }
        } else if (personality === 'playful' || living === 'single_house') {
            matchedKey = 'bengal';
        } else if (personality === 'gentle_giant') {
            matchedKey = 'ragdoll';
        } else {
            matchedKey = 'khao_manee';
        }

        const data = breedDatabase[matchedKey] || breedDatabase.scottish;

        document.getElementById('result-breed-name').textContent = data.name;
        document.getElementById('result-badge-tag').textContent = data.tag;
        document.getElementById('result-breed-img').src = data.img;
        document.getElementById('result-breed-desc').textContent = data.desc;
        document.getElementById('result-price-range').textContent = data.price;

        document.getElementById('quiz-result').style.display = 'block';
    }, 1200);
}

function restartQuiz() {
    userAnswers.living = null;
    userAnswers.time = null;
    userAnswers.personality = null;
    userAnswers.grooming = null;
    userAnswers.exp = null;

    document.getElementById('quiz-result').style.display = 'none';
    document.getElementById('quiz-progress-bar-wrapper').style.display = 'block';
    document.querySelectorAll('.quiz-option-card').forEach(c => {
        c.style.borderColor = '#E2E8F0';
        c.style.background = '#FAFAFA';
    });
    goToStep(1);
}

function shareQuizResult() {
    const breedName = document.getElementById('result-breed-name').textContent;
    const shareText = `🔮 ฉันได้ทำแบบทดสอบ AI Cat Matchmaker จาก Purrfect Shop แล้วผลลัพธ์คือ: ${breedName}! 🐾 ลองมาค้นหาน้องแมวในฝันของคุณดูสิ: ${window.location.href}`;
    const lineUrl = `https://line.me/R/msg/text/?${encodeURIComponent(shareText)}`;
    window.open(lineUrl, '_blank');
}
</script>

<?php
require_once __DIR__ . '/footer.php';
?>
