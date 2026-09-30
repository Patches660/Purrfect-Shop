<?php
// policies.php - Official Store Policies & Terms of Service
require_once __DIR__ . '/header.php';

$active_tab = trim($_GET['tab'] ?? 'privacy');
if (!in_array($active_tab, ['privacy', 'terms', 'shipping', 'pet_welfare', 'cookies'])) {
    $active_tab = 'privacy';
}
?>

<div class="container" style="max-width: 1080px; margin: 2rem auto 5rem auto; padding: 0 1rem;">
    <!-- Breadcrumb & Header Hero -->
    <div style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); border-radius: 28px; padding: 3rem 2rem; color: #FFFFFF; text-align: center; box-shadow: var(--shadow-md); margin-bottom: 2.5rem; position: relative; overflow: hidden;">
        <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 107, 74, 0.2); color: #FF8E72; border: 1.5px solid rgba(255, 107, 74, 0.4); padding: 0.45rem 1.25rem; border-radius: 999px; font-weight: 800; font-size: 0.88rem; margin-bottom: 1rem;">
            📜 OFFICIAL POLICIES & TERMS
        </div>
        <h1 style="font-size: 2.4rem; font-weight: 900; margin: 0 0 0.8rem 0; color: #FFFFFF;">
            นโยบายและข้อกำหนดการให้บริการ 🐾
        </h1>
        <p style="font-size: 1.05rem; color: #94A3B8; max-width: 720px; margin: 0 auto; line-height: 1.6;">
            ความโปร่งใส สวัสดิภาพของน้องแมว และความปลอดภัยของข้อมูลลูกค้าคือหัวใจสำคัญสูงสุดของ Purrfect Shop
        </p>
    </div>

    <!-- Navigation Tabs Grid -->
    <div class="policy-nav-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px; margin-bottom: 2.5rem;">
        <a href="?tab=privacy" class="policy-tab-btn <?php echo $active_tab === 'privacy' ? 'active' : ''; ?>" data-target-tab="privacy" onclick="switchPolicyTab('privacy', event)">
            <span class="policy-tab-icon">🔒</span>
            <div class="policy-tab-text-wrap">
                <strong class="policy-tab-title">Privacy Policy</strong>
                <small>นโยบายความเป็นส่วนตัว (PDPA)</small>
            </div>
        </a>

        <a href="?tab=terms" class="policy-tab-btn <?php echo $active_tab === 'terms' ? 'active' : ''; ?>" data-target-tab="terms" onclick="switchPolicyTab('terms', event)">
            <span class="policy-tab-icon">📝</span>
            <div class="policy-tab-text-wrap">
                <strong class="policy-tab-title">Terms of Service</strong>
                <small>ข้อกำหนด & เงื่อนไขบริการ</small>
            </div>
        </a>

        <a href="?tab=shipping" class="policy-tab-btn <?php echo $active_tab === 'shipping' ? 'active' : ''; ?>" data-target-tab="shipping" onclick="switchPolicyTab('shipping', event)">
            <span class="policy-tab-icon">🚐</span>
            <div class="policy-tab-text-wrap">
                <strong class="policy-tab-title">Shipping & Returns Policy</strong>
                <small>การจัดส่ง & คืนเงิน</small>
            </div>
        </a>

        <a href="?tab=pet_welfare" class="policy-tab-btn <?php echo $active_tab === 'pet_welfare' ? 'active' : ''; ?>" data-target-tab="pet_welfare" onclick="switchPolicyTab('pet_welfare', event)">
            <span class="policy-tab-icon">🐾</span>
            <div class="policy-tab-text-wrap">
                <strong class="policy-tab-title">Pet Adoption Policy</strong>
                <small>สวัสดิภาพ & ประกัน 180 วัน</small>
            </div>
        </a>

        <a href="?tab=cookies" class="policy-tab-btn <?php echo $active_tab === 'cookies' ? 'active' : ''; ?>" data-target-tab="cookies" onclick="switchPolicyTab('cookies', event)">
            <span class="policy-tab-icon">🍪</span>
            <div class="policy-tab-text-wrap">
                <strong class="policy-tab-title">Cookie Policy</strong>
                <small>นโยบายการใช้งานคุกกี้</small>
            </div>
        </a>
    </div>

    <!-- Policy Content Container -->
    <div id="policy-main-viewport" style="background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 24px; padding: 2.5rem; box-shadow: var(--shadow-sm); min-height: 480px;">
        
        <!-- 1. Privacy Policy (Privacy Policy) -->
        <div id="policy-sec-privacy" class="policy-panel <?php echo $active_tab === 'privacy' ? 'active' : ''; ?>" data-policy-id="privacy" style="<?php echo $active_tab === 'privacy' ? 'display: block !important;' : 'display: none;'; ?>">
            <div style="display: flex; align-items: center; gap: 12px; border-bottom: 2px solid var(--border-color); padding-bottom: 1.2rem; margin-bottom: 1.8rem;">
                <span style="font-size: 2.2rem;">🔒</span>
                <div>
                    <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">
                        นโยบายความเป็นส่วนตัว (Privacy Policy & PDPA)
                    </h2>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">
                        มีผลบังคับใช้ตั้งแต่วันที่ 1 มกราคม 2026 • สอดคล้องตาม พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562
                    </span>
                </div>
            </div>

            <div class="policy-body-content">
                <h3>1. ข้อมูลที่เราเก็บรวบรวม</h3>
                <p>Purrfect Shop ("เรา") ให้ความสำคัญสูงสุดต่อการรักษาความลับและความปลอดภัยของข้อมูลส่วนบุคคลของท่าน โดยเราจะจัดเก็บข้อมูลที่จำเป็นต่อการให้บริการรับเลี้ยงน้องแมวและการติดต่อเท่านั้น ได้แก่:</p>
                <ul>
                    <li><strong>ข้อมูลระบุตัวตน:</strong> ชื่อ-นามสกุล, ชื่อบัญชีผู้ใช้, รหัสสมาชิก</li>
                    <li><strong>ข้อมูลการติดต่อ:</strong> เบอร์โทรศัพท์มือถือ, อีเมล, LINE ID</li>
                    <li><strong>ข้อมูลสถานที่จัดส่ง:</strong> ที่อยู่จัดส่งน้องแมว, พิกัด GPS (Latitude, Longitude) เพื่อการนำทางของรถตู้ Pet Taxi ปรับอากาศ</li>
                    <li><strong>ประวัติการทำรายการ:</strong> ข้อมูลคำสั่งซื้อ, หมายเลข Tracking พัสดุ, ข้อมูลการจองคิวนัดหมายเยี่ยมฟาร์ม</li>
                </ul>

                <h3>2. วัตถุประสงค์ในการประมวลผลข้อมูล</h3>
                <p>เราใช้ข้อมูลส่วนบุคคลของท่านเพื่อวัตถุประสงค์ดังต่อไปนี้:</p>
                <ul>
                    <li>เพื่อดำเนินการส่งมอบน้องแมวสายพันธุ์แท้ถึงบ้านของท่านอย่างปลอดภัยและตรงเวลา</li>
                    <li>เพื่อออกเอกสารสัญญารับเลี้ยง, ใบเสร็จรับเงิน, และใบรับรองสายพันธุ์ (Pedigree Certificate)</li>
                    <li>เพื่อจัดทำสมุดวัคซีนดิจิทัลและส่งการแจ้งเตือนรอบนัดหมายฉีดวัคซีนตามช่วงวัยของน้องแมว</li>
                    <li>เพื่อให้บริการช่วยเหลือ ปรึกษาสุขภาพสัตว์เลี้ยงผ่านช่องทางแชท และสิทธิประโยชน์สมาชิก Paw Points</li>
                </ul>

                <h3>3. มาตรการรักษาความปลอดภัยของข้อมูล</h3>
                <p>ข้อมูลทั้งหมดจะถูกเข้ารหัสผ่านระบบรักษาความปลอดภัย <strong>SSL/TLS 256-bit Encryption</strong> และจัดเก็บในฐานข้อมูลที่มีการควบคุมการเข้าถึงอย่างเข้มงวด เราจะไม่จำหน่าย จ่าย แจก หรือเปิดเผยข้อมูลส่วนบุคคลของท่านให้แก่บุคคลภายนอกโดยเด็ดขาด ยกเว้นกรณีที่ได้รับความยินยอมจากท่านหรือเป็นไปตามคำสั่งทางกฎหมาย</p>

                <h3>4. สิทธิของเจ้าของข้อมูลส่วนบุคคล (Your Rights)</h3>
                <p>ท่านมีสิทธิในการขอเข้าถึง, ขอรับสำเนา, ขอแก้ไขข้อมูลให้ถูกต้องเป็นปัจจุบัน, ขอระงับการใช้ หรือขอลบข้อมูลส่วนบุคคลของท่านได้ตลอดเวลาผ่านหน้า <a href="profile.php" style="color: var(--primary-coral); font-weight: 700;">โปรไฟล์ของฉัน</a> หรือติดต่อเจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคลที่ <code style="background: #F1F5F9; padding: 2px 8px; border-radius: 6px;">dpo@purrfectshop.com</code></p>
            </div>
        </div>

        <!-- 2. Terms of Service (Terms of Service) -->
        <div id="policy-sec-terms" class="policy-panel <?php echo $active_tab === 'terms' ? 'active' : ''; ?>" data-policy-id="terms" style="<?php echo $active_tab === 'terms' ? 'display: block !important;' : 'display: none;'; ?>">
            <div style="display: flex; align-items: center; gap: 12px; border-bottom: 2px solid var(--border-color); padding-bottom: 1.2rem; margin-bottom: 1.8rem;">
                <span style="font-size: 2.2rem;">📝</span>
                <div>
                    <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">
                        ข้อกำหนดและเงื่อนไขการให้บริการ (Terms of Service)
                    </h2>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">
                        ข้อตกลงและเงื่อนไขในการสั่งจองและรับเลี้ยงน้องแมวผ่านระบบ Purrfect Shop
                    </span>
                </div>
            </div>

            <div class="policy-body-content">
                <h3>1. การสั่งจองและการชำระเงิน</h3>
                <ul>
                    <li>การสั่งจองน้องแมวจะถือว่าสมบูรณ์เมื่อท่านได้ดำเนินการยืนยันคำสั่งซื้อและชำระเงินมัดจำหรือเต็มจำนวนผ่านช่องทางที่ร้านค้ารองรับ</li>
                    <li>ระบบจะออกรหัสคำสั่งจอง (Order ID) และรหัสติดตามพัสดุ (Tracking Code) ให้ทันทีหลังการชำระเงินสำเร็จ</li>
                    <li>สำหรับผู้ที่เลือกช่องทาง "ชำระเงินเมื่อส่งมอบ (Cash on Handover)" ท่านจะต้องยืนยันตัวตนและเบอร์โทรศัพท์กับเจ้าหน้าที่ก่อนจัดส่ง</li>
                </ul>

                <h3>2. การโอนกรรมสิทธิ์และการส่งมอบ</h3>
                <p>กรรมสิทธิ์ในตัวน้องแมวจะโอนไปยังผู้รับอุปการะเมื่อมีการส่งมอบและลงนามในเอกสารตรวจรับมอบน้องแมวหน้าบ้าน พร้อมส่งมอบสมุดสุขภาพและใบเพ็ดดีกรีตัวจริง</p>

                <h3>3. หน้าที่และความรับผิดชอบของผู้รับเลี้ยง</h3>
                <ul>
                    <li>ผู้รับเลี้ยงต้องมีสถานที่เลี้ยงดูที่ปลอดภัย ถูกสุขลักษณะ และไม่อนุญาตให้นำน้องแมวไปใช้งานในทางทารุณกรรมสัตว์</li>
                    <li>ผู้รับเลี้ยงตกลงที่จะพาน้องแมวไปฉีดวัคซีนกระตุ้นและตรวจสุขภาพตามกำหนดการในสมุดวัคซีนอย่างสม่ำเสมอ</li>
                </ul>
            </div>
        </div>

        <!-- 3. Shipping & Returns Policy (Shipping & Returns Policy) -->
        <div id="policy-sec-shipping" class="policy-panel <?php echo $active_tab === 'shipping' ? 'active' : ''; ?>" data-policy-id="shipping" style="<?php echo $active_tab === 'shipping' ? 'display: block !important;' : 'display: none;'; ?>">
            <div style="display: flex; align-items: center; gap: 12px; border-bottom: 2px solid var(--border-color); padding-bottom: 1.2rem; margin-bottom: 1.8rem;">
                <span style="font-size: 2.2rem;">🚐</span>
                <div>
                    <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">
                        นโยบายการจัดส่งและการคืนเงิน (Shipping & Returns Policy)
                    </h2>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">
                        มาตรฐานการเดินทางด้วยรถตู้ปรับอากาศสำหรับสัตว์เลี้ยง & เงื่อนไขความคุ้มครอง
                    </span>
                </div>
            </div>

            <div class="policy-body-content">
                <h3>1. มาตรฐานการจัดส่งสัตว์เลี้ยง (Pet Transport Protocol)</h3>
                <ul>
                    <li><strong>รถตู้ปรับอากาศควบคุมอุณหภูมิ (Pet Taxi Express):</strong> ขนส่งด้วยรถตู้ควบคุมความเย็น 24-25°C มีพี่เลี้ยงดูแลตลอดเส้นทาง ป้อนน้ำ พักผ่อน และไม่ขังรวมกับสัตว์เลี้ยงอื่น</li>
                    <li><strong>เครื่องบินภายในประเทศ (Pet Cargo):</strong> จัดส่งสำหรับพื้นที่ต่างจังหวัดระยะไกล ผ่านสายการบินมาตรฐานสากลพร้อมกรงเดินทางตามมาตรฐาน IATA</li>
                    <li><strong>การติดตามสถานะสด:</strong> ลูกค้าสามารถตรวจสอบสถานะ 4 ขั้นตอนได้ตลอดเวลาผ่านหน้าระบบติดตาม Live Timeline Tracking</li>
                </ul>

                <h3>2. การยกเลิกและการคืนเงิน (Refund Policy)</h3>
                <ul>
                    <li><strong>กรณีตรวจสุขภาพไม่ผ่านก่อนเดินทาง:</strong> หากสัตวแพทย์ตรวจพบอาการป่วยหรือความผิดปกติก่อนวันส่งมอบ ทางฟาร์มจะคืนเงินมัดจำ/ค่าตัวเต็มจำนวน 100% ทันที หรือให้สิทธิ์เลือกลูกแมวคอกใหม่ตามความต้องการของลูกค้า</li>
                    <li><strong>การยกเลิกโดยผู้ซื้อ:</strong> สามารถขอยกเลิกได้ล่วงหน้าอย่างน้อย 48 ชั่วโมงก่อนกำหนดการเดินทาง โดยได้รับเงินคืน 80% (หักค่าธรรมเนียมตรวจสุขภาพและวัคซีนที่ดำเนินการไปแล้ว)</li>
                </ul>
            </div>
        </div>

        <!-- 4. Pet Welfare & Health Guarantee Policy (Pet Adoption Policy) -->
        <div id="policy-sec-pet_welfare" class="policy-panel <?php echo $active_tab === 'pet_welfare' ? 'active' : ''; ?>" data-policy-id="pet_welfare" style="<?php echo $active_tab === 'pet_welfare' ? 'display: block !important;' : 'display: none;'; ?>">
            <div style="display: flex; align-items: center; gap: 12px; border-bottom: 2px solid var(--border-color); padding-bottom: 1.2rem; margin-bottom: 1.8rem;">
                <span style="font-size: 2.2rem;">🩺</span>
                <div>
                    <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">
                        นโยบายสวัสดิภาพสัตว์เลี้ยง & การรับประกันสุขภาพ 180 วัน (Pet Adoption Policy)
                    </h2>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">
                        มาตรฐานการเพาะพันธุ์อย่างมีจริยธรรม (Ethical Breeding) และการคุ้มครองสุขภาพตลอดชีพ
                    </span>
                </div>
            </div>

            <div class="policy-body-content">
                <h3>1. การเพาะพันธุ์อย่างมีจริยธรรม (Ethical Breeding Standard)</h3>
                <p>Purrfect Shop ปฏิบัติตามมาตรฐานสมาคมแมวสากล (WCF, CFA, TICA) อย่างเคร่งครัด:</p>
                <ul>
                    <li><strong>ปลอดการผสมเลือดชิด (No Inbreeding 100%):</strong> แม่พันธุ์ได้รับการพักฟื้นอย่างน้อย 8-12 เดือนต่อหนึ่งครอก เพื่อให้ลูกแมวได้รับสารอาหารและสุขภาพที่แข็งแรงสมบูรณ์ที่สุด</li>
                    <li><strong>ตรวจคัดกรองโรคพันธุกรรม:</strong> พ่อแม่พันธุ์ทุกตัวผ่านการตรวจ DNA และ Echocardiogram ปลอดโรคหัวใจหนา (HCM) และถุงน้ำในไต (PKD)</li>
                </ul>

                <h3>2. ความคุ้มครองการรับประกันสุขภาพ 180 วัน</h3>
                <ul>
                    <li><strong>คุ้มครองโรคติดต่อร้ายแรง 30 วันแรก:</strong> หากน้องแมวมีอาการป่วยจากโรคไข้หัดแมว (FPV) หรือลิวคีเมีย (FeLV) ภายใน 30 วันหลังจากรับมอบ ทางฟาร์มรับผิดชอบค่ารักษาพยาบาล หรือเปลี่ยนน้องแมวตัวใหม่ หรือคืนเงินเต็มจำนวน</li>
                    <li><strong>คุ้มครองโรคพันธุกรรม 180 วัน:</strong> ครอบคลุมโรคทางพันธุกรรมที่มีผลต่อการดำรงชีวิต เช่น โรคหัวใจ HCM, PKD</li>
                </ul>

                <h3>3. สิทธิพิเศษหลังการขายตลอดชีพ (Lifetime Support)</h3>
                <p>ผู้รับเลี้ยงน้องแมวจาก Purrfect Shop จะได้รับคำปรึกษาด้านพฤติกรรม อาหารการกิน และสุขภาพจากทีมสัตวแพทย์และผู้เชี่ยวชาญฟรีตลอดอายุขัยของน้องแมวผ่านระบบ Live Chat และ LINE Official Account</p>
            </div>
        </div>

        <!-- 5. Cookie Policy (Cookie Policy - Informational Legal Display) -->
        <div id="policy-sec-cookies" class="policy-panel <?php echo $active_tab === 'cookies' ? 'active' : ''; ?>" data-policy-id="cookies" style="<?php echo $active_tab === 'cookies' ? 'display: block !important;' : 'display: none;'; ?>">
            <div style="display: flex; align-items: center; gap: 12px; border-bottom: 2px solid var(--border-color); padding-bottom: 1.2rem; margin-bottom: 1.8rem;">
                <span style="font-size: 2.2rem;">🍪</span>
                <div>
                    <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">
                        นโยบายการใช้คุกกี้ (Cookie Policy)
                    </h2>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">
                        ข้อมูลเกี่ยวกับประเภทและวัตถุประสงค์ของการใช้งานคุกกี้และเทคโนโลยีการจัดเก็บข้อมูลบน Purrfect Shop
                    </span>
                </div>
            </div>

            <div class="policy-body-content">
                <p>
                    เว็บไซต์ <strong>Purrfect Shop</strong> ("เรา") ใช้คุกกี้ (Cookies) และเทคโนโลยีการจัดเก็บข้อมูลฝั่งผู้ใช้ (เช่น LocalStorage และ SessionStorage) เพื่อเพิ่มประสิทธิภาพและความปลอดภัยในการทำงานของเว็บไซต์ ช่วยให้ท่านสามารถเลือกชม สั่งจอง และติดตามสถานะน้องแมวได้อย่างราบรื่น
                </p>

                <!-- Information Cards for 4 Cookie Categories (Informational Display Only) -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; margin: 1.8rem 0;">
                    
                    <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: 16px; padding: 1.25rem; box-shadow: var(--shadow-sm);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <span style="font-size: 1.6rem;">🔒</span>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-main); display: block;">1. คุกกี้จำเป็นอย่างยิ่ง</strong>
                                <span style="font-size: 0.72rem; background: #ECFDF5; color: #065F46; padding: 2px 8px; border-radius: 999px; font-weight: 800;">Strictly Necessary</span>
                            </div>
                        </div>
                        <p style="font-size: 0.83rem; color: var(--text-muted); margin: 0; line-height: 1.55;">
                            จำเป็นต่อการทำงานพื้นฐาน เช่น ระบบตะกร้าสินค้า (Cart), ระบบตรวจสอบเซสชันเข้าสู่ระบบสมาชิก และความปลอดภัยของแบบฟอร์ม
                        </p>
                    </div>

                    <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: 16px; padding: 1.25rem; box-shadow: var(--shadow-sm);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <span style="font-size: 1.6rem;">🎨</span>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-main); display: block;">2. คุกกี้บันทึกการตั้งค่า</strong>
                                <span style="font-size: 0.72rem; background: #EFF6FF; color: #1E40AF; padding: 2px 8px; border-radius: 999px; font-weight: 800;">Preferences</span>
                            </div>
                        </div>
                        <p style="font-size: 0.83rem; color: var(--text-muted); margin: 0; line-height: 1.55;">
                            จดจำการตั้งค่าส่วนบุคคลของท่าน เช่น ธีมสีเว็บไซต์ (Theme Switcher), ประวัติการค้นหา และพิกัดแผนที่เพื่อความสะดวก
                        </p>
                    </div>

                    <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: 16px; padding: 1.25rem; box-shadow: var(--shadow-sm);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <span style="font-size: 1.6rem;">📊</span>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-main); display: block;">3. คุกกี้เพื่อการวิเคราะห์</strong>
                                <span style="font-size: 0.72rem; background: #FDF4FF; color: #86198F; padding: 2px 8px; border-radius: 999px; font-weight: 800;">Analytics</span>
                            </div>
                        </div>
                        <p style="font-size: 0.83rem; color: var(--text-muted); margin: 0; line-height: 1.55;">
                            ประเมินสถิติการเข้าชม หน้าสายพันธุ์ยอดนิยม และประสิทธิภาพความเร็วในการโหลด เพื่อพัฒนาการให้บริการให้ดียิ่งขึ้น
                        </p>
                    </div>

                    <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: 16px; padding: 1.25rem; box-shadow: var(--shadow-sm);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <span style="font-size: 1.6rem;">🎡</span>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-main); display: block;">4. คุกกี้สิทธิพิเศษ & กิจกรรม</strong>
                                <span style="font-size: 0.72rem; background: #FFF7ED; color: #9A3412; padding: 2px 8px; border-radius: 999px; font-weight: 800;">Marketing & Rewards</span>
                            </div>
                        </div>
                        <p style="font-size: 0.83rem; color: var(--text-muted); margin: 0; line-height: 1.55;">
                            บันทึกโค้ดส่วนลดและรางวัลที่ได้รับจากวงล้อเสี่ยงโชค Paw Spin, โค้ดโปรโมชัน LINE OA และคำแนะนำจากบอท AI
                        </p>
                    </div>

                </div>

                <h3>1. รายละเอียดคุกกี้ที่ใช้งานบนระบบ Purrfect Shop</h3>
                <div style="overflow-x: auto; margin-bottom: 1.8rem;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem; background: var(--bg-card); border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color);">
                        <thead>
                            <tr style="background: var(--bg-card-subtle); color: var(--text-main); text-align: left;">
                                <th style="padding: 12px 16px; border-bottom: 1.5px solid var(--border-color);">ชื่อคุกกี้ / คีย์จัดเก็บ</th>
                                <th style="padding: 12px 16px; border-bottom: 1.5px solid var(--border-color);">ประเภท</th>
                                <th style="padding: 12px 16px; border-bottom: 1.5px solid var(--border-color);">วัตถุประสงค์การใช้งาน</th>
                                <th style="padding: 12px 16px; border-bottom: 1.5px solid var(--border-color);">ระยะเวลาจัดเก็บ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 10px 16px; font-family: monospace; font-weight: 700; color: var(--primary-coral);">cat_shop_cart</td>
                                <td style="padding: 10px 16px;">จำเป็น</td>
                                <td style="padding: 10px 16px;">จดจำรายการน้องแมวที่เลือกไว้ในตะกร้าสินค้า</td>
                                <td style="padding: 10px 16px;">30 วัน</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 10px 16px; font-family: monospace; font-weight: 700; color: var(--primary-coral);">PHPSESSID</td>
                                <td style="padding: 10px 16px;">จำเป็น</td>
                                <td style="padding: 10px 16px;">รักษาความปลอดภัยเซสชันการเข้าสู่ระบบสมาชิก</td>
                                <td style="padding: 10px 16px;">สิ้นสุดเมื่อปิดเบราว์เซอร์ (Session)</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 10px 16px; font-family: monospace; font-weight: 700; color: var(--primary-coral);">purrfect_theme</td>
                                <td style="padding: 10px 16px;">การตั้งค่า</td>
                                <td style="padding: 10px 16px;">จดจำธีมสีเว็บไซต์ที่ท่านเลือก (อบอุ่น, มินต์, ดาร์กโหมด ฯลฯ)</td>
                                <td style="padding: 10px 16px;">365 วัน</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 10px 16px; font-family: monospace; font-weight: 700; color: var(--primary-coral);">cat_shop_orders</td>
                                <td style="padding: 10px 16px;">จำเป็น</td>
                                <td style="padding: 10px 16px;">จัดเก็บประวัติคำสั่งจองและรหัส Tracking สำหรับเปิดดูในโปรไฟล์</td>
                                <td style="padding: 10px 16px;">180 วัน</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 10px 16px; font-family: monospace; font-weight: 700; color: var(--primary-coral);">cat_shop_my_rewards</td>
                                <td style="padding: 10px 16px;">การตลาด/กิจกรรม</td>
                                <td style="padding: 10px 16px;">บันทึกโค้ดส่วนลดที่ได้รับจากวงล้อเสี่ยงโชค Paw Spin</td>
                                <td style="padding: 10px 16px;">60 วัน</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 16px; font-family: monospace; font-weight: 700; color: var(--primary-coral);">_ga, _gid</td>
                                <td style="padding: 10px 16px;">การวิเคราะห์</td>
                                <td style="padding: 10px 16px;">Google Analytics เพื่อประเมินพฤติกรรมการเข้าชมหน้าสินค้า</td>
                                <td style="padding: 10px 16px;">2 ปี / 24 ชั่วโมง</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>2. วิธีการลบหรือปิดการทำงานของคุกกี้บนเบราว์เซอร์ของคุณ</h3>
                <p>ท่านสามารถควบคุมหรือลบคุกกี้ที่มีอยู่แล้วในเครื่องของท่านได้ตลอดเวลาผ่านเมนูการตั้งค่าของแต่ละเว็บเบราว์เซอร์:</p>
                <ul>
                    <li><strong>Google Chrome:</strong> เมนู (⋮) ➔ การตั้งค่า ➔ ความเป็นส่วนตัวและความปลอดภัย ➔ ล้างข้อมูลการท่องเว็บ (คุกกี้และข้อมูลอื่นของไซต์)</li>
                    <li><strong>Apple Safari:</strong> เมนู Preferences ➔ Privacy ➔ Manage Website Data ➔ Remove All</li>
                    <li><strong>Microsoft Edge:</strong> เมนู (...) ➔ Settings ➔ Cookies and site permissions ➔ Manage and delete cookies</li>
                    <li><strong>Mozilla Firefox:</strong> เมนู (≡) ➔ Settings ➔ Privacy & Security ➔ Cookies and Site Data ➔ Clear Data</li>
                </ul>

                <h3>3. การติดต่อสอบถาม</h3>
                <p>หากท่านมีข้อสงสัยเกี่ยวกับนโยบายคุกกี้หรือการคุ้มครองข้อมูล สามารถติดต่อทีมงานได้ตลอด 24 ชั่วโมงผ่านทาง Live Chat หรืออีเมล <code style="background: #F1F5F9; padding: 2px 8px; border-radius: 6px;">privacy@purrfectshop.com</code></p>
            </div>
        </div>

    </div>
</div>

<style>
.policy-nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    gap: 12px;
    margin-bottom: 2.5rem;
}
.policy-tab-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 1rem 1.1rem;
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 18px;
    text-decoration: none;
    color: var(--text-main);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    min-width: 0;
    overflow: hidden;
}
.policy-tab-btn:hover {
    border-color: var(--primary-coral);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 107, 74, 0.15);
}
.policy-tab-btn.active {
    border-color: var(--primary-coral);
    background: #FFF7F3;
    box-shadow: 0 6px 20px rgba(255, 107, 74, 0.2);
}
.policy-tab-icon {
    font-size: 1.8rem;
    line-height: 1;
    flex-shrink: 0;
}
.policy-tab-text-wrap {
    min-width: 0;
    flex: 1;
    overflow: hidden;
}
.policy-tab-title {
    display: block;
    font-size: 0.92rem;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 2px;
    word-break: break-word;
    overflow-wrap: anywhere;
    line-height: 1.25;
}
.policy-tab-btn small {
    display: block;
    font-size: 0.76rem;
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.policy-tab-btn.active .policy-tab-title {
    color: var(--primary-coral);
}

.policy-panel {
    display: none;
}
.policy-panel.active,
.policy-panel[data-active="true"] {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.policy-body-content {
    font-size: 0.95rem;
    color: var(--text-secondary);
    line-height: 1.75;
}
.policy-body-content h3 {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 1.6rem 0 0.6rem 0;
}
.policy-body-content p {
    margin: 0 0 1rem 0;
}
.policy-body-content ul {
    margin: 0 0 1.2rem 1.5rem;
    padding: 0;
}
.policy-body-content li {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .policy-nav-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
function switchPolicyTab(tabId, e) {
    if (e) e.preventDefault();
    if (!tabId) tabId = 'privacy';
    if (tabId === 'cookie') tabId = 'cookies';
    if (tabId === 'pet') tabId = 'pet_welfare';
    
    // Update active tab buttons
    document.querySelectorAll('.policy-tab-btn').forEach(btn => {
        btn.classList.remove('active');
        const target = btn.getAttribute('data-target-tab') || (btn.getAttribute('href') || '').split('tab=')[1];
        if (target === tabId) {
            btn.classList.add('active');
        }
    });

    // Update panels
    document.querySelectorAll('.policy-panel').forEach(panel => {
        panel.classList.remove('active');
        panel.removeAttribute('data-active');
        panel.style.cssText = 'display: none !important;';
    });

    const targetPanel = document.getElementById('policy-sec-' + tabId) 
                     || document.querySelector(`.policy-panel[data-policy-id="${tabId}"]`)
                     || document.getElementById('panel-' + tabId);

    if (targetPanel) {
        targetPanel.classList.add('active');
        targetPanel.setAttribute('data-active', 'true');
        targetPanel.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important;';
    }

    // Update URL query string smoothly without reload
    try {
        const newUrl = window.location.pathname + '?tab=' + tabId;
        window.history.replaceState({ path: newUrl }, '', newUrl);
    } catch(err) {}
}

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    let tabParam = urlParams.get('tab');
    if (tabParam) {
        if (tabParam === 'cookie') tabParam = 'cookies';
        if (tabParam === 'pet') tabParam = 'pet_welfare';
        switchPolicyTab(tabParam, null);
    } else {
        switchPolicyTab('privacy', null);
    }

    // Self-healing observer against adblocker cosmetic hiding rules
    setInterval(() => {
        const activeBtn = document.querySelector('.policy-tab-btn.active');
        if (activeBtn) {
            const tabId = activeBtn.getAttribute('data-target-tab') || 'privacy';
            const activePanel = document.getElementById('policy-sec-' + tabId) 
                             || document.querySelector(`.policy-panel[data-policy-id="${tabId}"]`)
                             || document.getElementById('panel-' + tabId);

            if (activePanel) {
                const computed = window.getComputedStyle(activePanel);
                if (computed.display === 'none' || computed.visibility === 'hidden' || computed.opacity === '0') {
                    activePanel.classList.add('active');
                    activePanel.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important;';
                }
            }
        }
    }, 400);
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
