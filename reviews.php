<?php
require_once __DIR__ . '/header.php';

$reviews_file = __DIR__ . '/data_reviews.json';
$reviews = file_exists($reviews_file) ? json_decode(file_get_contents($reviews_file), true) : [];

?>

<div class="container" style="max-width: 1200px; margin: 2rem auto 5rem auto; padding: 0 1rem;">
    <!-- Top Hero Banner -->
    <div class="reviews-hero-banner" style="background: linear-gradient(135deg, #FFF1F2 0%, #FFE4E6 50%, #FECDD3 100%); border-radius: 28px; padding: 3rem 2rem; text-align: center; border: 2px solid #FDA4AF; box-shadow: 0 12px 35px rgba(244, 63, 94, 0.12); margin-bottom: 3rem; position: relative; overflow: hidden;">
        <div style="display: inline-flex; align-items: center; gap: 8px; background: #E11D48; color: #fff; padding: 0.45rem 1.25rem; border-radius: 999px; font-weight: 800; font-size: 0.9rem; margin-bottom: 1rem; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3);">
            ⭐ 100% VERIFIED HAPPY ADOPTERS
        </div>
        <h1 class="reviews-main-title" style="font-size: 2.6rem; font-weight: 900; color: #881337; margin-bottom: 0.8rem; line-height: 1.25;">
            รีวิวความประทับใจจากครอบครัวทาสแมว 🐾💖
        </h1>
        <p class="reviews-sub-desc" style="font-size: 1.15rem; color: #4C0519; max-width: 780px; margin: 0 auto 2rem auto; line-height: 1.6;">
            ส่งมอบน้องแมวสายพันธุ์แท้สุขภาพสมบูรณ์สู่บ้านใหม่อย่างอบอุ่นกว่า 1,200+ ครอบครัวทั่วประเทศไทย พร้อมบริการดูแลดั่งคนในครอบครัว
        </p>

        <!-- Stats Bar -->
        <div class="reviews-stats-bar" style="display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap;">
            <div class="reviews-stat-card" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px); padding: 1rem 1.8rem; border-radius: 20px; border: 1.5px solid #FDA4AF; min-width: 160px; flex: 1; max-width: 240px;">
                <div style="font-size: 2.2rem; font-weight: 900; color: #E11D48;">5.0 / 5.0</div>
                <div style="color: #F59E0B; font-size: 1.2rem; margin: 2px 0;">⭐⭐⭐⭐⭐</div>
                <div style="font-size: 0.85rem; color: #881337; font-weight: 700;">คะแนนความพึงพอใจเฉลี่ย</div>
            </div>
            <div class="reviews-stat-card" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px); padding: 1rem 1.8rem; border-radius: 20px; border: 1.5px solid #FDA4AF; min-width: 160px; flex: 1; max-width: 240px;">
                <div style="font-size: 2.2rem; font-weight: 900; color: #E11D48;">1,200+</div>
                <div style="color: #059669; font-size: 0.95rem; font-weight: 800; margin: 5px 0;">🏡 ส่งมอบทั่วไทย</div>
                <div style="font-size: 0.85rem; color: #881337; font-weight: 700;">น้องแมวย้ายบ้านสำเร็จ</div>
            </div>
            <div class="reviews-stat-card" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px); padding: 1rem 1.8rem; border-radius: 20px; border: 1.5px solid #FDA4AF; min-width: 160px; flex: 1; max-width: 240px;">
                <div style="font-size: 2.2rem; font-weight: 900; color: #E11D48;">180 วัน</div>
                <div style="color: #2563EB; font-size: 0.95rem; font-weight: 800; margin: 5px 0;">🩺 การันตีสุขภาพ</div>
                <div style="font-size: 0.85rem; color: #881337; font-weight: 700;">ทีมสัตวแพทย์ดูแล 24 ชม.</div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div style="display: flex; gap: 0.6rem; overflow-x: auto; padding-bottom: 1rem; margin-bottom: 2rem; justify-content: center; flex-wrap: wrap;">
        <button class="filter-btn active" onclick="filterReviews('all', this)" style="padding: 0.5rem 1.25rem; border-radius: 999px; font-weight: 700; border: 2px solid var(--primary-coral); background: var(--primary-coral); color: #fff; cursor: pointer; transition: all 0.2s;">
            🌟 ทั้งหมด (<?php echo count($reviews); ?>)
        </button>
        <button class="filter-btn" onclick="filterReviews('cat_british', this)" style="padding: 0.5rem 1.25rem; border-radius: 999px; font-weight: 700; border: 2px solid #E2E8F0; background: #fff; color: #475569; cursor: pointer; transition: all 0.2s;">
            🐱 บริติช ช็อตแฮร์
        </button>
        <button class="filter-btn" onclick="filterReviews('cat_persian', this)" style="padding: 0.5rem 1.25rem; border-radius: 999px; font-weight: 700; border: 2px solid #E2E8F0; background: #fff; color: #475569; cursor: pointer; transition: all 0.2s;">
            👑 เปอร์เซีย
        </button>
        <button class="filter-btn" onclick="filterReviews('cat_ragdoll', this)" style="padding: 0.5rem 1.25rem; border-radius: 999px; font-weight: 700; border: 2px solid #E2E8F0; background: #fff; color: #475569; cursor: pointer; transition: all 0.2s;">
            💎 แร็กดอลล์
        </button>
        <button class="filter-btn" onclick="filterReviews('cat_mainecoon', this)" style="padding: 0.5rem 1.25rem; border-radius: 999px; font-weight: 700; border: 2px solid #E2E8F0; background: #fff; color: #475569; cursor: pointer; transition: all 0.2s;">
            🦁 เมนคูน
        </button>
        <button class="filter-btn" onclick="filterReviews('cat_munchkin', this)" style="padding: 0.5rem 1.25rem; border-radius: 999px; font-weight: 700; border: 2px solid #E2E8F0; background: #fff; color: #475569; cursor: pointer; transition: all 0.2s;">
            🐾 มันช์กิ้น ขาสั้น
        </button>
        <button class="filter-btn" onclick="filterReviews('cat_bengal', this)" style="padding: 0.5rem 1.25rem; border-radius: 999px; font-weight: 700; border: 2px solid #E2E8F0; background: #fff; color: #475569; cursor: pointer; transition: all 0.2s;">
            🐆 เบงกอล
        </button>
    </div>

    <!-- Reviews Grid -->
    <div id="reviews-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
        <?php foreach ($reviews as $rev): ?>
            <div class="review-card" data-breed="<?php echo htmlspecialchars($rev['breed_id'] ?? 'all'); ?>" style="background: #fff; border-radius: 24px; padding: 1.8rem; border: 2px solid #F1F5F9; box-shadow: 0 8px 24px rgba(0,0,0,0.06); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                <div>
                    <!-- Card Top Header -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.2rem;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #FFEDD5 0%, #FED7AA 100%); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; border: 2px solid var(--primary-coral);">
                                🐱
                            </div>
                            <div>
                                <div style="font-weight: 900; color: #1E293B; font-size: 1.05rem;"><?php echo htmlspecialchars($rev['author']); ?></div>
                                <div style="font-size: 0.8rem; color: #64748B;">📍 <?php echo htmlspecialchars($rev['location']); ?></div>
                            </div>
                        </div>
                        <span style="background: #DCFCE7; color: #166534; font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.65rem; border-radius: 999px; display: inline-flex; align-items: center; gap: 3px;">
                            ✓ ผู้รับเลี้ยงจริง
                        </span>
                    </div>

                    <!-- Cat Photo Showcase (Before & After) -->
                    <div style="position: relative; border-radius: 18px; overflow: hidden; margin-bottom: 1.2rem; border: 2px solid #E2E8F0; aspect-ratio: 16/10;">
                        <img src="<?php echo htmlspecialchars($rev['image_at_home'] ?? 'assets/images/cat_british.jpg'); ?>" alt="<?php echo htmlspecialchars($rev['breed_name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; bottom: 10px; left: 10px; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px); color: #fff; padding: 0.3rem 0.8rem; border-radius: 999px; font-size: 0.8rem; font-weight: 800;">
                            🐾 <?php echo htmlspecialchars($rev['breed_name']); ?>
                        </div>
                        <div style="position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 255, 0.9); color: #B45309; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 800; border: 1px solid #FDE68A;">
                            ⏱️ <?php echo htmlspecialchars($rev['adoption_duration'] ?? 'รับน้องไปแล้ว'); ?>
                        </div>
                    </div>

                    <!-- Stars & Rating -->
                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 0.8rem;">
                        <div style="color: #F59E0B; font-size: 1.15rem; letter-spacing: 2px;">
                            <?php echo str_repeat('★', intval($rev['rating'])) . str_repeat('☆', 5 - intval($rev['rating'])); ?>
                        </div>
                        <span style="font-size: 0.85rem; font-weight: 800; color: #B45309;"><?php echo number_format($rev['rating'], 1); ?>/5.0</span>
                    </div>

                    <!-- Comment Body -->
                    <p style="color: #334155; font-size: 0.95rem; line-height: 1.65; margin-bottom: 1.5rem;">
                        "<?php echo htmlspecialchars($rev['comment']); ?>"
                    </p>
                </div>

                <!-- Footer / Date & Like Button -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1.5px solid #F1F5F9;">
                    <span style="font-size: 0.8rem; color: #94A3B8;">📅 <?php echo htmlspecialchars($rev['date']); ?></span>
                    <button onclick="toggleReviewLike(this)" style="background: #FFF1F2; border: 1px solid #FECDD3; color: #E11D48; padding: 0.35rem 0.85rem; border-radius: 999px; font-weight: 800; font-size: 0.82rem; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s;">
                        ❤️ ถูกใจ (<span class="like-cnt"><?php echo intval($rev['likes'] ?? 10); ?></span>)
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function filterReviews(breed, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.style.background = '#fff';
        b.style.color = '#475569';
        b.style.borderColor = '#E2E8F0';
    });
    btn.style.background = 'var(--primary-coral)';
    btn.style.color = '#fff';
    btn.style.borderColor = 'var(--primary-coral)';

    const cards = document.querySelectorAll('.review-card');
    cards.forEach(card => {
        if (breed === 'all' || card.getAttribute('data-breed') === breed) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function toggleReviewLike(btn) {
    const span = btn.querySelector('.like-cnt');
    let cnt = parseInt(span.innerText);
    if (!btn.classList.contains('liked')) {
        btn.classList.add('liked');
        btn.style.background = '#E11D48';
        btn.style.color = '#fff';
        span.innerText = cnt + 1;
    } else {
        btn.classList.remove('liked');
        btn.style.background = '#FFF1F2';
        btn.style.color = '#E11D48';
        span.innerText = cnt - 1;
    }
}
</script>

<style>
@media (max-width: 768px) {
    .reviews-hero-banner {
        padding: 2rem 1.2rem !important;
        border-radius: 20px !important;
    }
    .reviews-main-title {
        font-size: 1.8rem !important;
    }
    .reviews-sub-desc {
        font-size: 1rem !important;
    }
    .reviews-stat-card {
        min-width: 130px !important;
        padding: 0.8rem 1rem !important;
    }
    #reviews-grid {
        grid-template-columns: 1fr !important;
        gap: 1.2rem !important;
    }
}

@media (max-width: 480px) {
    .reviews-stat-card {
        max-width: 100% !important;
        width: 100% !important;
    }
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
