<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/data.php';

$page_title = '🎡 วงล้อลุ้นโชค Paw Lucky Spin - Purrfect Shop';
require_once __DIR__ . '/header.php';
?>

<main class="page-container" style="padding: 2.5rem 1rem 4rem; max-width: 1200px; margin: 0 auto;">
    <!-- Hero Banner -->
    <div class="wheel-hero-banner" style="text-align: center; margin-bottom: 2.5rem; background: linear-gradient(135deg, rgba(255, 107, 74, 0.12) 0%, rgba(254, 243, 199, 0.7) 100%); padding: 2.5rem 1.5rem; border-radius: 28px; border: 2px dashed rgba(255, 107, 74, 0.35); position: relative; overflow: hidden;">
        <div style="position: absolute; top: -20px; right: -20px; font-size: 6rem; opacity: 0.15; transform: rotate(15deg); pointer-events: none;">🎡</div>
        <div style="position: absolute; bottom: -20px; left: -20px; font-size: 6rem; opacity: 0.15; transform: rotate(-15deg); pointer-events: none;">🎁</div>
        
        <span style="display: inline-block; background: #FF5A5F; color: #fff; font-weight: 800; font-size: 0.85rem; padding: 0.35rem 1rem; border-radius: 999px; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(255, 90, 95, 0.35); text-transform: uppercase; letter-spacing: 0.5px;">
            ✨ DAILY LUCKY REWARDS • สิทธิพิเศษประจำวัน
        </span>
        <h1 class="wheel-main-title" style="font-size: 2.4rem; font-weight: 900; color: var(--text-main); margin-bottom: 0.6rem;">
            🎡 Paw Lucky Spin วงล้อเสี่ยงโชคทาสแมว
        </h1>
        <p class="wheel-sub-desc" style="font-size: 1.05rem; color: var(--text-muted); max-width: 680px; margin: 0 auto 1.5rem; line-height: 1.6;">
            หมุนวงล้อรับรางวัลคูปองส่วนลดสูงสุด <strong>25%</strong>, ขนมฟรี Freeze-Dried, บริการส่งฟรี VIP หรือรับแต้ม <strong>Paw Points 500 คะแนน</strong> ไปใช้ลดได้ทันที!
        </p>
        
        <div class="wheel-feature-pills" style="display: inline-flex; align-items: center; gap: 1rem; flex-wrap: wrap; justify-content: center; background: var(--bg-card); padding: 0.6rem 1.4rem; border-radius: 999px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid var(--border-color);">
            <span style="font-size: 0.9rem; font-weight: 700; color: var(--primary-coral);">🎯 หมุนฟรีวันละ 3 ครั้ง (รีเซ็ตทุกเที่ยงคืน)</span>
            <span class="pill-divider" style="color: var(--border-color);">|</span>
            <span style="font-size: 0.9rem; font-weight: 700; color: #D97706;">⭐ แจกจริงทุกรางวัล 100%</span>
            <span class="pill-divider" style="color: var(--border-color);">|</span>
            <span style="font-size: 0.9rem; font-weight: 700; color: #10B981;">🐾 ใช้ได้กับน้องแมวทุกสายพันธุ์</span>
        </div>
    </div>

    <!-- Main Game Section -->
    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 2rem; align-items: start;" class="wheel-grid-layout">
        
        <!-- Wheel Canvas Interactive Card -->
        <div class="wheel-canvas-card" style="background: var(--bg-card); border-radius: 28px; padding: 2.2rem; border: 1px solid var(--border-color); box-shadow: 0 12px 36px rgba(0,0,0,0.06); text-align: center; position: relative; overflow: hidden;">
            
            <!-- Wheel Container -->
            <div class="wheel-container-inner">
                <!-- Pointer / Stopper -->
                <div class="wheel-pointer-pin">
                    <div class="wheel-pointer-arrow"></div>
                    <div class="wheel-pointer-dot"></div>
                </div>

                <!-- Canvas Wheel -->
                <canvas id="luckyWheelCanvas" width="420" height="420"></canvas>

                <!-- Center Spin Button -->
                <button type="button" id="centerSpinBtn" onclick="spinWheel()" title="คลิกเพื่อหมุนวงล้อ">
                    <span>หมุน</span>
                    <span class="spin-sub">SPIN! 🐾</span>
                </button>
            </div>

            <!-- Action Controls -->
            <div style="margin-top: 2rem;">
                <button type="button" id="bigSpinBtn" onclick="spinWheel()" class="btn btn-primary btn-lg" style="padding: 1rem 3rem; font-size: 1.2rem; font-weight: 800; border-radius: 999px; box-shadow: 0 8px 25px rgba(255, 107, 74, 0.35);">
                    🎡 กดปุ่มหมุนวงล้อเสี่ยงโชค!
                </button>
                <p id="spinStatusText" style="font-size: 0.92rem; color: var(--text-muted); margin-top: 0.9rem;">
                    💡 วันนี้คุณมีสิทธิ์หมุนฟรี <strong>3 ครั้ง</strong> • คลิกที่ปุ่มเพื่อเริ่มเลย!
                </p>
            </div>

            <!-- Sound & Mute Toggle -->
            <div style="margin-top: 1.2rem; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted); cursor: pointer;" onclick="toggleSound()">
                <input type="checkbox" id="soundToggle" checked style="cursor: pointer;">
                <label for="soundToggle" style="cursor: pointer;">🔊 เปิดเสียงเอฟเฟกต์ (Audio Effects)</label>
            </div>
        </div>

        <!-- Right Side: Prize List & Recent Winners -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Prize Table Card -->
            <div style="background: var(--bg-card); border-radius: 24px; padding: 1.8rem; border: 1px solid var(--border-color); box-shadow: 0 8px 24px rgba(0,0,0,0.04);">
                <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 8px; color: var(--text-main);">
                    <span>🎁</span> รางวัลทั้งหมดในวงล้อ
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: rgba(255, 107, 74, 0.08); border-radius: 14px; border-left: 4px solid #FF6B4A;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.4rem;">🔥</span>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-main); display: block;">ลด 25% ต้อนรับทาสใหม่</strong>
                                <span style="font-size: 0.78rem; color: var(--text-muted);">โค้ด: PURR25NEW (สูงสุด 12,500.-)</span>
                            </div>
                        </div>
                        <span style="background: #FF5A5F; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 999px;">แจ็คพอต</span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: rgba(245, 158, 11, 0.08); border-radius: 14px; border-left: 4px solid #F59E0B;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.4rem;">📦</span>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-main); display: block;">ฟรี VIP Starter Kit</strong>
                                <span style="font-size: 0.78rem; color: var(--text-muted);">เซ็ทกระบะ+คอนโด+ของเล่น (มูลค่า 3,500.-)</span>
                            </div>
                        </div>
                        <span style="background: #F59E0B; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 999px;">สุดคุ้ม</span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: rgba(16, 185, 129, 0.08); border-radius: 14px; border-left: 4px solid #10B981;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.4rem;">🚚</span>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-main); display: block;">ฟรีค่าจัดส่ง VIP ทั่วไทย</strong>
                                <span style="font-size: 0.78rem; color: var(--text-muted);">รถควบคุมอุณหภูมิดูแลตลอดทาง (มูลค่า 1,500.-)</span>
                            </div>
                        </div>
                        <span style="background: #10B981; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 999px;">ยอดฮิต</span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: rgba(99, 102, 241, 0.08); border-radius: 14px; border-left: 4px solid #6366F1;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.4rem;">🐾</span>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-main); display: block;">รับ 500 Paw Points</strong>
                                <span style="font-size: 0.78rem; color: var(--text-muted);">ใช้แลกของพรีเมียมหรือลดค่าตัวน้องแมว</span>
                            </div>
                        </div>
                        <span style="background: #6366F1; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 999px;">คะแนน</span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: rgba(236, 72, 153, 0.08); border-radius: 14px; border-left: 4px solid #EC4899;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.4rem;">🍖</span>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-main); display: block;">ฟรี Freeze-Dried Salmon</strong>
                                <span style="font-size: 0.78rem; color: var(--text-muted);">ขนมฟรีซดรายเกรดพรีเมียม 1 ซองใหญ่</span>
                            </div>
                        </div>
                        <span style="background: #EC4899; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 999px;">ของแถม</span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: rgba(14, 165, 233, 0.08); border-radius: 14px; border-left: 4px solid #0EA5E9;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.4rem;">🎫</span>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-main); display: block;">ลด 10% ทุกออเดอร์</strong>
                                <span style="font-size: 0.78rem; color: var(--text-muted);">โค้ด: CAT10OFF ไม่มีขั้นต่ำ</span>
                            </div>
                        </div>
                        <span style="background: #0EA5E9; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 999px;">คูปอง</span>
                    </div>
                </div>
            </div>

            <!-- Recent Winners Live Feed -->
            <div style="background: var(--bg-card); border-radius: 24px; padding: 1.5rem; border: 1px solid var(--border-color); box-shadow: 0 8px 24px rgba(0,0,0,0.04);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <h4 style="font-size: 1.05rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 6px; color: var(--text-main);">
                        <span>📢</span> ผู้โชคดีล่าสุด
                    </h4>
                    <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.75rem; color: #10B981; font-weight: 700;">
                        <span style="width: 8px; height: 8px; background: #10B981; border-radius: 50%; animation: pulse 1.5s infinite;"></span> LIVE
                    </span>
                </div>

                <div id="liveWinnersFeed" style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; color: var(--text-muted); border-bottom: 1px dashed var(--border-color); padding-bottom: 6px;">
                        <span>คุณ นภัสวรรณ (089-xxx-4122)</span>
                        <strong style="color: #FF5A5F;">ลด 25% 🔥</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; color: var(--text-muted); border-bottom: 1px dashed var(--border-color); padding-bottom: 6px;">
                        <span>คุณ ภานุพงศ์ (092-xxx-8819)</span>
                        <strong style="color: #F59E0B;">Starter Kit 📦</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; color: var(--text-muted); border-bottom: 1px dashed var(--border-color); padding-bottom: 6px;">
                        <span>คุณ ชลธิชา (081-xxx-9543)</span>
                        <strong style="color: #10B981;">ส่งฟรี VIP 🚚</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; color: var(--text-muted);">
                        <span>คุณ กิตติศักดิ์ (065-xxx-1102)</span>
                        <strong style="color: #6366F1;">+500 พอยท์ 🐾</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Terms and Rules -->
    <div style="margin-top: 3rem; background: rgba(0,0,0,0.02); border-radius: 20px; padding: 1.8rem 2rem; border: 1px solid var(--border-color);">
        <h4 style="font-size: 1rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.8rem;">
            📜 กติกาและเงื่อนไขการรับรางวัล
        </h4>
        <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.88rem; color: var(--text-muted); line-height: 1.8;">
            <li>สมาชิกทั่วไปและผู้เยี่ยมชมสามารถหมุนวงล้อเสี่ยงโชคได้ฟรีวันละ <strong>3 ครั้ง</strong> ต่อ 1 อุปกรณ์/บัญชี (รีเซ็ตสิทธิ์ทุกเที่ยงคืน)</li>
            <li>โค้ดส่วนลดที่ได้รับสามารถนำไปกรอกในช่อง <strong>"โค้ดส่วนลด / Coupon Code"</strong> ที่หน้าตะกร้าสินค้า <a href="cart.php" style="color: var(--primary-coral); font-weight: 700;">cart.php</a> ได้ทันที</li>
            <li>รางวัลของแถม (Starter Kit / ขนมฟรีซดราย) จะถูกจัดส่งพร้อมกับน้องแมวเมื่อทำการยืนยันการจอง</li>
            <li>คะแนน Paw Points จะถูกบันทึกเข้ากระเป๋าบัญชีสมาชิกอัตโนมัติเมื่อเข้าสู่ระบบ</li>
        </ul>
    </div>
</main>

<!-- Prize Winner Modal -->
<div id="prizeModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); backdrop-filter: blur(6px); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
    <div class="winner-modal-card" style="background: var(--bg-card); border-radius: 32px; max-width: 480px; width: 100%; padding: 2.5rem 2rem; text-align: center; border: 2px solid #FFD700; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative; animation: popIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275); max-height: 90vh; overflow-y: auto;">
        
        <div style="font-size: 4.5rem; margin-bottom: 0.5rem;" id="modalPrizeIcon">🎉</div>
        <span style="display: inline-block; background: #FFD700; color: #92400E; font-weight: 900; font-size: 0.85rem; padding: 0.35rem 1.2rem; border-radius: 999px; margin-bottom: 0.8rem;">
            ✨ CONGRATULATIONS! ยินดีด้วยค่ะ ✨
        </span>
        <h2 style="font-size: 1.8rem; font-weight: 900; color: var(--text-main); margin-bottom: 0.5rem;" id="modalPrizeTitle">
            คุณได้รับส่วนลด 25%!
        </h2>
        <p style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 1.5rem;" id="modalPrizeDesc">
            ใช้เป็นส่วนลดสำหรับค่าตัวน้องแมวทุกสายพันธุ์ในร้าน
        </p>

        <!-- Coupon Code Box -->
        <div id="modalCouponBox" style="background: rgba(255, 107, 74, 0.1); border: 2px dashed #FF6B4A; border-radius: 16px; padding: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
            <div style="text-align: left; min-width: 140px;">
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; display: block;">รหัสคูปองของคุณ</span>
                <strong id="modalCouponCode" style="font-size: 1.4rem; letter-spacing: 2px; color: #FF334B; font-family: monospace;">PURR25NEW</strong>
            </div>
            <button type="button" onclick="copyModalCoupon()" class="btn btn-primary btn-sm" id="modalCopyBtn" style="border-radius: 10px; padding: 0.6rem 1.1rem; font-weight: 800;">
                คัดลอก 📋
            </button>
        </div>

        <!-- Buttons -->
        <div style="display: flex; flex-direction: column; gap: 0.8rem;">
            <a href="cart.php" id="modalApplyCartBtn" class="btn btn-primary" style="padding: 0.85rem; font-weight: 800; font-size: 1rem; border-radius: 14px; text-decoration: none;">
                🛒 นำโค้ดไปใช้ที่ตะกร้าเลย
            </a>
            <a href="profile.php?tab=coupons" id="modalProfileCouponsBtn" class="btn btn-secondary" style="padding: 0.75rem; font-weight: 800; font-size: 0.95rem; border-radius: 14px; text-decoration: none; border-color: var(--primary-coral); color: var(--primary-coral);">
                🎁 ดูคูปองทั้งหมดในโปรไฟล์ของฉัน
            </a>
            <button type="button" onclick="closePrizeModal()" class="btn btn-secondary" style="padding: 0.65rem; font-weight: 700; font-size: 0.9rem; border-radius: 14px;">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>
</div>

<style>
@keyframes popIn {
    0% { transform: scale(0.7); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes pulse {
    0% { transform: scale(0.95); opacity: 0.7; }
    50% { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.7; }
}

.wheel-container-inner {
    position: relative;
    width: 100%;
    max-width: 400px;
    margin: 15px auto 10px;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    box-sizing: border-box;
}

#luckyWheelCanvas {
    display: block;
    width: 100% !important;
    max-width: 380px !important;
    height: auto !important;
    aspect-ratio: 1 / 1;
    border-radius: 50%;
    box-shadow: 0 10px 32px rgba(255, 107, 74, 0.22);
    border: 7px solid #FFF8F0;
    box-sizing: border-box;
    transition: transform 0.2s ease;
}

.wheel-pointer-pin {
    position: absolute;
    top: -8px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 25;
    filter: drop-shadow(0 4px 6px rgba(0,0,0,0.28));
    pointer-events: none;
}

.wheel-pointer-arrow {
    width: 0;
    height: 0;
    border-left: 15px solid transparent;
    border-right: 15px solid transparent;
    border-top: 34px solid #FF334B;
}

.wheel-pointer-dot {
    width: 12px;
    height: 12px;
    background: #FFD700;
    border-radius: 50%;
    margin: -32px auto 0;
    box-shadow: 0 0 6px rgba(255,215,0,0.9);
}

#centerSpinBtn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 82px;
    height: 82px;
    border-radius: 50%;
    background: linear-gradient(135deg, #FF6B4A 0%, #FF334B 100%);
    color: #fff;
    border: 4.5px solid #FFF8F0;
    font-size: 1.05rem;
    font-weight: 900;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(255, 51, 75, 0.45);
    transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1px;
    user-select: none;
}
#centerSpinBtn .spin-sub {
    font-size: 0.65rem;
    opacity: 0.92;
    font-weight: 700;
}
#centerSpinBtn:hover {
    transform: translate(-50%, -50%) scale(1.06);
}
#centerSpinBtn:active {
    transform: translate(-50%, -50%) scale(0.92);
}

@media (max-width: 900px) {
    .wheel-grid-layout {
        grid-template-columns: 1fr !important;
        gap: 1.5rem !important;
    }
}

@media (max-width: 640px) {
    .wheel-hero-banner {
        padding: 1.5rem 0.9rem !important;
        border-radius: 20px !important;
        margin-bottom: 1.5rem !important;
    }
    .wheel-main-title {
        font-size: 1.6rem !important;
    }
    .wheel-sub-desc {
        font-size: 0.88rem !important;
        margin-bottom: 1rem !important;
    }
    .wheel-feature-pills {
        flex-direction: column !important;
        gap: 0.35rem !important;
        border-radius: 16px !important;
        padding: 0.75rem 0.9rem !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .wheel-feature-pills .pill-divider {
        display: none !important;
    }
    .wheel-canvas-card {
        padding: 1.4rem 0.6rem !important;
        border-radius: 22px !important;
    }
    .wheel-container-inner {
        max-width: 100% !important;
        margin: 12px auto 8px !important;
    }
    #luckyWheelCanvas {
        max-width: min(78vw, 300px) !important;
        border-width: 5px !important;
    }
    .wheel-pointer-arrow {
        border-left-width: 12px !important;
        border-right-width: 12px !important;
        border-top-width: 28px !important;
    }
    .wheel-pointer-dot {
        width: 10px !important;
        height: 10px !important;
        margin-top: -26px !important;
    }
    #centerSpinBtn {
        width: 66px !important;
        height: 66px !important;
        font-size: 0.88rem !important;
        border-width: 3.5px !important;
    }
    #centerSpinBtn .spin-sub {
        font-size: 0.58rem !important;
    }
    #bigSpinBtn {
        width: 100% !important;
        padding: 0.85rem 1.2rem !important;
        font-size: 1rem !important;
    }
    .winner-modal-card {
        padding: 1.6rem 1rem !important;
        border-radius: 22px !important;
    }
    #modalPrizeIcon {
        font-size: 3.2rem !important;
    }
    #modalPrizeTitle {
        font-size: 1.35rem !important;
    }
}

@media (max-width: 380px) {
    .wheel-canvas-card {
        padding: 1rem 0.35rem !important;
    }
    #luckyWheelCanvas {
        max-width: min(74vw, 250px) !important;
        border-width: 4px !important;
    }
    #centerSpinBtn {
        width: 56px !important;
        height: 56px !important;
        font-size: 0.78rem !important;
        border-width: 3px !important;
    }
    #centerSpinBtn .spin-sub {
        display: none !important;
    }
}
</style>

<script>
// --- Lucky Wheel Configuration & Daily Spin Limit ---
const MAX_DAILY_SPINS = 3;

function getTodayKey() {
    const d = new Date();
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function getDailySpinCount() {
    const today = getTodayKey();
    const savedDate = localStorage.getItem('purrfect_spin_date');
    if (savedDate !== today) {
        localStorage.setItem('purrfect_spin_date', today);
        localStorage.setItem('purrfect_spin_count_today', '0');
        return 0;
    }
    const count = parseInt(localStorage.getItem('purrfect_spin_count_today') || '0', 10);
    return isNaN(count) ? 0 : count;
}

function incrementDailySpinCount() {
    const count = getDailySpinCount();
    const newCount = count + 1;
    localStorage.setItem('purrfect_spin_count_today', newCount.toString());
    updateSpinQuotaUI();
    return newCount;
}

function updateSpinQuotaUI() {
    const count = getDailySpinCount();
    const remaining = Math.max(0, MAX_DAILY_SPINS - count);
    const statusText = document.getElementById('spinStatusText');
    const bigBtn = document.getElementById('bigSpinBtn');
    const centerBtn = document.getElementById('centerSpinBtn');

    if (remaining > 0) {
        if (statusText) {
            statusText.innerHTML = `💡 สิทธิ์หมุนฟรีวันนี้: เหลืออีก <strong style="color: #FF334B; font-size: 1.1rem;">${remaining} / ${MAX_DAILY_SPINS}</strong> ครั้ง • คลิกที่ปุ่มเพื่อลุ้นรางวัล! 🐾`;
        }
        if (bigBtn && !isSpinning) {
            bigBtn.disabled = false;
            bigBtn.style.opacity = '1';
            bigBtn.innerHTML = `🎡 กดปุ่มหมุนวงล้อเสี่ยงโชค! (เหลือ ${remaining} ครั้ง)`;
        }
        if (centerBtn && !isSpinning) {
            centerBtn.disabled = false;
            centerBtn.style.opacity = '1';
        }
    } else {
        if (statusText) {
            statusText.innerHTML = `⚠️ <strong style="color: #DC2626;">คุณใช้สิทธิ์หมุนฟรีครบ ${MAX_DAILY_SPINS} ครั้งสำหรับวันนี้แล้ว!</strong><br><span style="font-size: 0.85rem; color: var(--text-muted);">ระบบจะรีเซ็ตสิทธิ์ให้ใหม่เวลา 00:00 น. พรุ่งนี้ค่ะ 🐾</span>`;
        }
        if (bigBtn && !isSpinning) {
            bigBtn.disabled = true;
            bigBtn.style.opacity = '0.55';
            bigBtn.style.cursor = 'not-allowed';
            bigBtn.innerHTML = `🔒 ใช้สิทธิ์ครบ 3 ครั้งแล้ววันนี้ (รีเซ็ตพรุ่งนี้)`;
        }
        if (centerBtn && !isSpinning) {
            centerBtn.disabled = true;
            centerBtn.style.opacity = '0.6';
            centerBtn.style.cursor = 'not-allowed';
        }
    }
}

const prizes = [
    { title: "ลด 25%", desc: "โค้ดส่วนลด 25% สำหรับน้องแมวทุกตัว (PURR25NEW)", code: "PURR25NEW", icon: "🔥", color: "#FF6B4A", textCol: "#FFFFFF" },
    { title: "Starter Kit", desc: "รับฟรี VIP Starter Kit เซ็ตพร้อมเลี้ยง 3,500.- (STARTERVIP)", code: "STARTERVIP", icon: "📦", color: "#FFA726", textCol: "#FFFFFF" },
    { title: "ส่งฟรี VIP", desc: "ฟรีบริการจัดส่งรถแอร์ทั่วประเทศ 1,500.- (FREESHIPCAT)", code: "FREESHIPCAT", icon: "🚚", color: "#26A69A", textCol: "#FFFFFF" },
    { title: "500 พอยท์", desc: "รับคะแนนสะสม Paw Points 500 แต้มเข้ากระเป๋า", code: "PAW500PTS", icon: "🐾", color: "#5C6BC0", textCol: "#FFFFFF" },
    { title: "ฟรีซดราย", desc: "รับฟรี Freeze-Dried Salmon แซลมอนฟรีซดราย 1 ซอง (SALMONFREE)", code: "SALMONFREE", icon: "🍖", color: "#EC407A", textCol: "#FFFFFF" },
    { title: "ลด 10%", desc: "โค้ดส่วนลด 10% ไม่มีขั้นต่ำ (CAT10OFF)", code: "CAT10OFF", icon: "🎫", color: "#42A5F5", textCol: "#FFFFFF" }
];

const numSegments = prizes.length;
const arc = Math.PI * 2 / numSegments;
let startAngle = 0;
let isSpinning = false;
let currentRotation = 0;

const canvas = document.getElementById('luckyWheelCanvas');
const ctx = canvas.getContext('2d');

function drawWheel() {
    const outsideRadius = 200;
    const textRadius = 140;
    const insideRadius = 55;

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;

    for (let i = 0; i < numSegments; i++) {
        const angle = startAngle + i * arc;
        ctx.fillStyle = prizes[i].color;

        ctx.beginPath();
        ctx.arc(centerX, centerY, outsideRadius, angle, angle + arc, false);
        ctx.arc(centerX, centerY, insideRadius, angle + arc, angle, true);
        ctx.fill();

        // Border line between slices
        ctx.strokeStyle = "#FFFFFF";
        ctx.lineWidth = 3;
        ctx.stroke();

        // Draw Text and Icon
        ctx.save();
        ctx.fillStyle = prizes[i].textCol;
        ctx.font = "bold 15px 'Segoe UI', Tahoma, sans-serif";
        ctx.translate(
            centerX + Math.cos(angle + arc / 2) * textRadius,
            centerY + Math.sin(angle + arc / 2) * textRadius
        );
        ctx.rotate(angle + arc / 2 + Math.PI / 2);
        
        const label = prizes[i].icon + " " + prizes[i].title;
        ctx.fillText(label, -ctx.measureText(label).width / 2, 0);
        ctx.restore();
    }

    // Outer decorative dots
    for (let j = 0; j < 24; j++) {
        const dotAngle = (Math.PI * 2 / 24) * j;
        const dotX = centerX + Math.cos(dotAngle) * 203;
        const dotY = centerY + Math.sin(dotAngle) * 203;
        ctx.beginPath();
        ctx.arc(dotX, dotY, 4, 0, Math.PI * 2);
        ctx.fillStyle = j % 2 === 0 ? "#FFD700" : "#FFFFFF";
        ctx.fill();
    }
}

// Web Audio API Sound Synthesizer (No external mp3 required)
let audioCtx = null;
function playBeep(freq, duration) {
    const soundToggle = document.getElementById('soundToggle');
    if (!soundToggle || !soundToggle.checked) return;
    try {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + duration);
        osc.start();
        osc.stop(audioCtx.currentTime + duration);
    } catch(e) {}
}

function playWinFanfare() {
    playBeep(523.25, 0.15); // C5
    setTimeout(() => playBeep(659.25, 0.15), 150); // E5
    setTimeout(() => playBeep(783.99, 0.25), 300); // G5
    setTimeout(() => playBeep(1046.50, 0.45), 450); // C6
}

function toggleSound() {
    const soundToggle = document.getElementById('soundToggle');
    soundToggle.checked = !soundToggle.checked;
}

function spinWheel() {
    if (isSpinning) return;
    
    // Check daily quota
    const count = getDailySpinCount();
    if (count >= MAX_DAILY_SPINS) {
        alert("⚠️ คุณใช้สิทธิ์หมุนฟรีครบ 3 ครั้งสำหรับวันนี้แล้วค่ะ! กรุณากลับมาหมุนใหม่ในวันพรุ่งนี้นะคะ 🐾");
        updateSpinQuotaUI();
        return;
    }

    isSpinning = true;

    const centerBtn = document.getElementById('centerSpinBtn');
    const bigBtn = document.getElementById('bigSpinBtn');
    centerBtn.style.transform = 'translate(-50%, -50%) scale(0.9)';
    bigBtn.disabled = true;
    bigBtn.style.opacity = '0.6';

    // Increment daily spin count
    incrementDailySpinCount();

    // Random prize target (0 to 5)
    const winningIndex = Math.floor(Math.random() * numSegments);
    
    // Calculate final angle so pointer (top center: 270 deg or 3*PI/2) points to winningIndex
    const extraRounds = 5 + Math.floor(Math.random() * 3); // 5 to 7 full spins
    const segmentCenter = winningIndex * arc + arc / 2;
    const targetOffset = (Math.PI * 1.5) - segmentCenter;
    const totalRotation = (extraRounds * Math.PI * 2) + targetOffset - (startAngle % (Math.PI * 2));
    
    const duration = 4500; // 4.5 seconds
    const startTime = performance.now();
    const initialAngle = startAngle;
    let lastTickAngle = startAngle;

    function animate(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Ease out cubic
        const ease = 1 - Math.pow(1 - progress, 3);
        startAngle = initialAngle + (totalRotation * ease);
        drawWheel();

        // Tick sound on segment pass
        if (Math.abs(startAngle - lastTickAngle) >= arc) {
            playBeep(400, 0.04);
            lastTickAngle = startAngle;
        }

        if (progress < 1) {
            requestAnimationFrame(animate);
        } else {
            isSpinning = false;
            centerBtn.style.transform = 'translate(-50%, -50%) scale(1)';
            updateSpinQuotaUI();
            showWinningPrize(prizes[winningIndex]);
        }
    }

    requestAnimationFrame(animate);
}

function showWinningPrize(prize) {
    playWinFanfare();
    
    document.getElementById('modalPrizeIcon').textContent = prize.icon;
    document.getElementById('modalPrizeTitle').textContent = "คุณได้รับ " + prize.title + "!";
    document.getElementById('modalPrizeDesc').textContent = prize.desc;
    document.getElementById('modalCouponCode').textContent = prize.code;
    
    const isStatic = window.location.pathname.endsWith('.html') || !window.location.pathname.includes('.php');
    const applyCartBtn = document.getElementById('modalApplyCartBtn');
    if (applyCartBtn) {
        applyCartBtn.href = isStatic 
            ? "cart.html?apply_coupon=" + encodeURIComponent(prize.code)
            : "cart.php?apply_coupon=" + encodeURIComponent(prize.code);
    }

    const profileCouponsBtn = document.getElementById('modalProfileCouponsBtn');
    if (profileCouponsBtn) {
        profileCouponsBtn.href = isStatic ? "profile.html?tab=coupons" : "profile.php?tab=coupons";
    }

    const modal = document.getElementById('prizeModal');
    modal.style.display = 'flex';

    // Store in localStorage
    try {
        localStorage.setItem('purrfect_last_lucky_spin', JSON.stringify({
            prize: prize.title,
            code: prize.code,
            date: new Date().toISOString()
        }));

        // Append to my rewards list in localStorage
        let myRewards = [];
        try {
            myRewards = JSON.parse(localStorage.getItem('cat_shop_my_rewards') || '[]');
        } catch(err) { myRewards = []; }

        myRewards.unshift({
            id: 'rew_' + Date.now(),
            title: prize.title,
            desc: prize.desc,
            icon: prize.icon,
            code: prize.code,
            date: new Date().toISOString(),
            date_formatted: new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
        });
        localStorage.setItem('cat_shop_my_rewards', JSON.stringify(myRewards));

        // If prize is points, credit points to logged in user
        if (prize.code === 'PAW500PTS' || prize.title.includes('Paw Points')) {
            let u = JSON.parse(localStorage.getItem('cat_shop_logged_user') || 'null');
            if (u) {
                u.paw_points = (u.paw_points || 150) + 500;
                localStorage.setItem('cat_shop_logged_user', JSON.stringify(u));
            }
        }
    } catch(e) {}
}

function closePrizeModal() {
    document.getElementById('prizeModal').style.display = 'none';
}

function copyModalCoupon() {
    const code = document.getElementById('modalCouponCode').textContent;
    navigator.clipboard.writeText(code).then(() => {
        const btn = document.getElementById('modalCopyBtn');
        btn.textContent = "คัดลอกแล้ว! ✓";
        btn.style.background = "#10B981";
        setTimeout(() => {
            btn.textContent = "คัดลอก 📋";
            btn.style.background = "";
        }, 2000);
    });
}

// Initial draw and quota check on page load
drawWheel();
updateSpinQuotaUI();
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
