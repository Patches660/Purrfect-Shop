    <footer>
        <!-- Newsletter Subscription Ribbon -->
        <div style="background: linear-gradient(135deg, rgba(255, 117, 86, 0.12) 0%, rgba(254, 243, 199, 0.5) 100%); border-bottom: 1px solid var(--border-color); padding: 1.8rem 1rem;">
            <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <span style="font-size: 2.4rem; line-height: 1;">📬</span>
                    <div>
                        <h4 style="margin: 0 0 4px 0; font-size: 1.15rem; color: var(--text-main); font-weight: 800;">
                            สมัครรับจดหมายข่าว & ดีลลับน้องแมว Purrfect Shop 🐾
                        </h4>
                        <p style="margin: 0; font-size: 0.88rem; color: var(--text-muted);">
                            รับแจ้งเตือนน้องแมวเข้าใหม่ เกร็ดสุขภาพจากสัตวแพทย์ พร้อมรับโค้ดส่วนลด 10% (CATNEWS10) ทันทีทางอีเมล
                        </p>
                    </div>
                </div>
                <form action="subscribe.php" method="POST" style="display: flex; gap: 8px; flex-wrap: wrap; flex: 1; max-width: 460px;">
                    <input type="email" name="subscriber_email" placeholder="กรอกอีเมลของคุณ เช่น name@gmail.com" required style="flex: 1; min-width: 220px; padding: 10px 16px; border-radius: 999px; border: 1.5px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-size: 0.9rem;">
                    <button type="submit" class="btn btn-primary" style="border-radius: 999px; padding: 10px 22px; font-weight: 800; font-size: 0.9rem; white-space: nowrap; box-shadow: 0 4px 12px rgba(255, 117, 86, 0.3);">
                        ติดตามข่าวสาร ✨
                    </button>
                </form>
            </div>
        </div>

        <div class="footer-inner">
            <div class="footer-brand">
                <h3>Purrfect Shop 🐾</h3>
                <p style="font-weight: 600; color: var(--primary-coral); margin-bottom: 0.4rem;">
                    "เพราะทุกบ้านควรมีเจ้าเหมียว"
                </p>
                <p>
                    ฟาร์มและศูนย์รวมน้องแมวสายพันธุ์แท้ 100% สุขภาพดี ได้รับการดูแลจากสัตวแพทย์อย่างใกล้ชิด
                    พร้อมบริการตรวจสุขภาพ ฉีดวัคซีน ถ่ายพยาธิ และใบเพ็ดดีกรีรับรองสายพันธุ์ครบถ้วน
                </p>
                <div style="margin-top: 1rem; display: flex; gap: 0.8rem; font-size: 0.85rem; color: var(--text-muted);">
                    <span>📍 บริการจัดส่งทั่วประเทศ</span> &bull; 
                    <span>⏰ เปิดทุกวัน 09:00 - 20:00 น.</span>
                </div>
            </div>

            <div class="footer-col">
                <h4>หมวดหมู่แนะนำ</h4>
                <ul>
                    <li><a href="recommend.php?cat=popular">🔥 สายพันธุ์ยอดนิยม</a></li>
                    <li><a href="recommend.php?cat=condo">🏢 แมวเลี้ยงในคอนโด/รักสงบ</a></li>
                    <li><a href="recommend.php?cat=fluffy">🧸 แมวขนยาวนุ่มฟู</a></li>
                    <li><a href="recommend.php?cat=short_hair">✨ แมวขนสั้นดูแลง่าย</a></li>
                    <li><a href="recommend.php?cat=beginner">👶 มือใหม่เลี้ยงง่าย</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>ความปลอดภัย & สมาชิก</h4>
                <ul>
                    <li><a href="register.php">✨ สมัครสมาชิกรับส่วนลด 5%</a></li>
                    <li><a href="creator.php">👨‍💻 ข้อมูลผู้จัดทำโครงการ</a></li>
                    <li><a href="cart.php">🛒 ตะกร้าการจองน้องแมว</a></li>
                    <li><a href="recommend.php">🌟 แนะนำสายพันธุ์สำหรับคุณ</a></li>
                    <li><a href="products.php">🐱 ดูสายพันธุ์น้องแมวทั้งหมด</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <strong>Purrfect Shop</strong> - คัดสรรและส่งมอบเพื่อนที่ดีที่สุดด้วยหัวใจ 🐾</p>
            <p>
                พัฒนาโดย <strong>Purrfect Cattery Team</strong> (TH-CAT-8899 IT) &bull; Purrfect Boutique Cattery
            </p>
        </div>
    </footer>
    <script src="assets/js/main.js"></script>
</body>
</html>
