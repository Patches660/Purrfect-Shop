<?php
require_once __DIR__ . '/header.php';

$pedigrees_file = __DIR__ . '/data_pedigrees.json';
$pedigrees = file_exists($pedigrees_file) ? json_decode(file_get_contents($pedigrees_file), true) : [];

$query = trim($_GET['chip'] ?? '900215001234567');
$selected_pedigree = $pedigrees[$query] ?? $pedigrees['900215001234567'] ?? null;
?>

<div class="container" style="max-width: 1150px; margin: 2rem auto 5rem auto; padding: 0 1rem;">
    <!-- Hero Banner -->
    <div class="pedigree-hero-banner" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 50%, #BBF7D0 100%); border-radius: 28px; padding: 3rem 2rem; text-align: center; border: 2px solid #86EFAC; box-shadow: 0 12px 35px rgba(16, 185, 129, 0.12); margin-bottom: 3rem;">
        <div style="display: inline-flex; align-items: center; gap: 8px; background: #059669; color: #fff; padding: 0.45rem 1.25rem; border-radius: 999px; font-weight: 800; font-size: 0.9rem; margin-bottom: 1rem; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);">
            🩺 VERIFIED DIGITAL PASSPORT & PEDIGREE
        </div>
        <h1 class="pedigree-main-title" style="font-size: 2.5rem; font-weight: 900; color: #064E3B; margin-bottom: 0.8rem;">
            ตรวจสอบใบเพ็ดดีกรี & สมุดวัคซีนดิจิทัล 🏆📜
        </h1>
        <p class="pedigree-sub-desc" style="font-size: 1.15rem; color: #065F46; max-width: 760px; margin: 0 auto 2rem auto; line-height: 1.6;">
            ระบบตรวจสอบประวัติสายพันธุ์แท้ (WCF / CFA / TICA) ผลตรวจพันธุกรรมโรคหัวใจ ไต และประวัติการฉีดวัคซีนผ่านหมายเลขไมโครชิปมาตรฐานสากล
        </p>

        <!-- Search Bar -->
        <form method="GET" action="pedigree.php" id="pedigree-search-form" onsubmit="handlePedigreeSearch(event)" style="max-width: 650px; margin: 0 auto; display: flex; gap: 0.8rem; background: #fff; padding: 0.6rem; border-radius: 999px; border: 2.5px solid #059669; box-shadow: 0 8px 25px rgba(0,0,0,0.08);" class="pedigree-search-form">
            <input type="text" name="chip" id="pedChipInput" value="<?php echo htmlspecialchars($query); ?>" placeholder="กรอกเลขไมโครชิป 15 หลัก..." style="flex: 1; min-width: 0; border: none; outline: none; padding: 0.6rem 1.4rem; font-size: 1.05rem; font-weight: 700; border-radius: 999px;">
            <button type="submit" class="btn btn-primary" style="background: #059669; border-color: #059669; border-radius: 999px; padding: 0.75rem 1.8rem; font-weight: 800; font-size: 1rem; cursor: pointer; white-space: nowrap;">
                🔍 ตรวจสอบ
            </button>
        </form>

        <!-- Quick Select Buttons (All 10 Breeds) -->
        <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap; align-items: center;" id="quickChipContainer">
            <span style="font-size: 0.85rem; font-weight: 800; color: #064E3B; width: 100%; margin-bottom: 4px;">เลือกดูไมโครชิปน้องแมวแต่ละสายพันธุ์:</span>
            <button type="button" onclick="selectChipClient('900215001234567')" class="chip-select-btn" data-chip="900215001234567" style="background: #fff; border: 1.5px solid #86EFAC; color: #065F46; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.82rem; font-weight: 800; cursor: pointer;">
                🐱 บริติช (น้องสโนว์)
            </button>
            <button type="button" onclick="selectChipClient('900215003344556')" class="chip-select-btn" data-chip="900215003344556" style="background: #fff; border: 1.5px solid #86EFAC; color: #065F46; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.82rem; font-weight: 800; cursor: pointer;">
                👑 เปอร์เซีย (น้องปุยหิมะ)
            </button>
            <button type="button" onclick="selectChipClient('900215009988776')" class="chip-select-btn" data-chip="900215009988776" style="background: #fff; border: 1.5px solid #86EFAC; color: #065F46; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.82rem; font-weight: 800; cursor: pointer;">
                👽 สฟิงซ์ (น้องซีซาร์)
            </button>
            <button type="button" onclick="selectChipClient('900215007654321')" class="chip-select-btn" data-chip="900215007654321" style="background: #fff; border: 1.5px solid #86EFAC; color: #065F46; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.82rem; font-weight: 800; cursor: pointer;">
                💎 แร็กดอลล์ (น้องคอตตอน)
            </button>
            <button type="button" onclick="selectChipClient('900215008899112')" class="chip-select-btn" data-chip="900215008899112" style="background: #fff; border: 1.5px solid #86EFAC; color: #065F46; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.82rem; font-weight: 800; cursor: pointer;">
                🦁 เมนคูน (น้องไททัน)
            </button>
            <button type="button" onclick="selectChipClient('900215006677889')" class="chip-select-btn" data-chip="900215006677889" style="background: #fff; border: 1.5px solid #86EFAC; color: #065F46; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.82rem; font-weight: 800; cursor: pointer;">
                🐆 เบงกอล (น้องจากัวร์)
            </button>
            <button type="button" onclick="selectChipClient('900215007788990')" class="chip-select-btn" data-chip="900215007788990" style="background: #fff; border: 1.5px solid #86EFAC; color: #065F46; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.82rem; font-weight: 800; cursor: pointer;">
                🎀 สก็อตติช (น้องพุดดิ้ง)
            </button>
            <button type="button" onclick="selectChipClient('900215005566778')" class="chip-select-btn" data-chip="900215005566778" style="background: #fff; border: 1.5px solid #86EFAC; color: #065F46; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.82rem; font-weight: 800; cursor: pointer;">
                🐾 มันช์กิ้น (น้องชอร์ตตี้)
            </button>
            <button type="button" onclick="selectChipClient('900215004455667')" class="chip-select-btn" data-chip="900215004455667" style="background: #fff; border: 1.5px solid #86EFAC; color: #065F46; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.82rem; font-weight: 800; cursor: pointer;">
                👑 วิเชียรมาศ (น้องมงคล)
            </button>
            <button type="button" onclick="selectChipClient('900215009900112')" class="chip-select-btn" data-chip="900215009900112" style="background: #fff; border: 1.5px solid #86EFAC; color: #065F46; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.82rem; font-weight: 800; cursor: pointer;">
                🌌 รัสเซียนบลู (น้องบลูสกาย)
            </button>
        </div>
    </div>

    <?php if ($selected_pedigree): ?>
        <!-- Certificate of Pedigree (Official Certificate Template Layout) -->
        <div id="certificate-print-area" class="pedigree-certificate-card" style="background: #FFFDF9; border-radius: 32px; border: 6px double #D97706; padding: 3rem; box-shadow: 0 20px 60px rgba(0,0,0,0.12); position: relative; overflow: hidden; margin-bottom: 3rem;">
            <!-- Gold Corner Embellishments -->
            <div class="corner-embellishment" style="position: absolute; top: 15px; left: 15px; font-size: 2rem; color: #D97706; opacity: 0.6;">⚜️</div>
            <div class="corner-embellishment" style="position: absolute; top: 15px; right: 15px; font-size: 2rem; color: #D97706; opacity: 0.6;">⚜️</div>
            <div class="corner-embellishment" style="position: absolute; bottom: 15px; left: 15px; font-size: 2rem; color: #D97706; opacity: 0.6;">⚜️</div>
            <div class="corner-embellishment" style="position: absolute; bottom: 15px; right: 15px; font-size: 2rem; color: #D97706; opacity: 0.6;">⚜️</div>

            <!-- Certificate Header -->
            <div class="pedigree-cert-header" style="text-align: center; border-bottom: 2px solid #FDE68A; padding-bottom: 2rem; margin-bottom: 2.5rem;">
                <div style="display: inline-block; background: #FEF3C7; border: 1.5px solid #F59E0B; color: #92400E; padding: 0.4rem 1.2rem; border-radius: 999px; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.8rem;">
                    OFFICIAL CERTIFIED PEDIGREE & HEALTH PASSPORT
                </div>
                <h2 class="pedigree-cert-title" style="font-size: 2.2rem; font-weight: 900; color: #78350F; letter-spacing: 1px; margin-bottom: 0.3rem;">
                    ใบรับรองสายพันธุ์ & สมุดสุขภาพสากล
                </h2>
                <div style="font-size: 1.1rem; color: #B45309; font-weight: 700;" id="pedRegistry">
                    ออกโดย: <?php echo htmlspecialchars($selected_pedigree['registry']); ?>
                </div>
                <div style="font-size: 0.9rem; color: #78350F; margin-top: 4px;">
                    เลขทะเบียนสากล: <strong id="pedRegNo"><?php echo htmlspecialchars($selected_pedigree['registration_no']); ?></strong> | ฟาร์ม: <span id="pedCattery"><?php echo htmlspecialchars($selected_pedigree['cattery']); ?></span>
                </div>
            </div>

            <!-- Cat Profile & Photo Section -->
            <div style="display: grid; grid-template-columns: 280px 1fr; gap: 2.5rem; align-items: center; margin-bottom: 3rem;" class="pedigree-profile-grid">
                <div class="pedigree-img-wrap" style="border-radius: 22px; overflow: hidden; border: 4px solid #D97706; box-shadow: 0 10px 25px rgba(217, 119, 6, 0.25); aspect-ratio: 1/1; background: #fff; max-width: 280px; margin: 0 auto; width: 100%;">
                    <img id="pedImage" src="<?php echo htmlspecialchars($selected_pedigree['image']); ?>" alt="Cat Photo" style="width: 100%; height: 100%; object-fit: cover;">
                </div>

                <div>
                    <div style="display: inline-block; background: #ECFDF5; color: #065F46; border: 1px solid #10B981; padding: 0.25rem 0.75rem; border-radius: 8px; font-weight: 800; font-size: 0.8rem; margin-bottom: 0.5rem;">
                        ✓ ยืนยันตัวตนด้วยไมโครชิปสากล
                    </div>
                    <h3 id="pedNameThEn" style="font-size: 2rem; font-weight: 900; color: #1E293B; margin-bottom: 0.2rem;">
                        <?php echo htmlspecialchars($selected_pedigree['name_th']); ?> (<?php echo htmlspecialchars($selected_pedigree['name_en']); ?>)
                    </h3>
                    <div id="pedBreedTh" style="font-size: 1.15rem; color: #D97706; font-weight: 800; margin-bottom: 1.2rem;">
                        <?php echo htmlspecialchars($selected_pedigree['breed_th']); ?>
                    </div>

                    <div class="pedigree-specs-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; background: #FFFBEB; padding: 1.2rem; border-radius: 18px; border: 1.5px solid #FDE68A;">
                        <div>
                            <div style="font-size: 0.75rem; color: #92400E; font-weight: 700;">หมายเลขไมโครชิป</div>
                            <div id="pedMicrochip" style="font-size: 0.95rem; font-weight: 900; color: #1E293B; word-break: break-all;"><?php echo htmlspecialchars($selected_pedigree['microchip']); ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #92400E; font-weight: 700;">เพศ / วันเกิด</div>
                            <div id="pedGenderDob" style="font-size: 0.95rem; font-weight: 800; color: #1E293B;"><?php echo htmlspecialchars($selected_pedigree['gender']); ?> • <?php echo htmlspecialchars($selected_pedigree['dob']); ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #92400E; font-weight: 700;">สีขน / ดวงตา</div>
                            <div id="pedColor" style="font-size: 0.95rem; font-weight: 800; color: #1E293B;"><?php echo htmlspecialchars($selected_pedigree['color']); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pedigree Tree (Ancestry) -->
            <div style="margin-bottom: 3rem;">
                <h4 style="font-size: 1.3rem; font-weight: 900; color: #78350F; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 8px;">
                    🌳 แผนผังสายเลือด 3 ชั่วอายุคน (Pedigree Ancestry Tree)
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;" class="pedigree-tree-grid">
                    <!-- Sire (Father) -->
                    <div style="background: #EFF6FF; border: 2px solid #93C5FD; border-radius: 18px; padding: 1.4rem;">
                        <div style="font-size: 0.8rem; font-weight: 800; color: #1E40AF; text-transform: uppercase;">👑 พ่อพันธุ์ (Sire)</div>
                        <div id="pedSireName" style="font-size: 1.1rem; font-weight: 900; color: #1E293B; margin: 4px 0;"><?php echo htmlspecialchars($selected_pedigree['sire']['name']); ?></div>
                        <div id="pedSireTitle" style="font-size: 0.85rem; color: #2563EB; font-weight: 700;">🏆 <?php echo htmlspecialchars($selected_pedigree['sire']['title']); ?></div>
                        <div id="pedSireRegNo" style="font-size: 0.8rem; color: #64748B;">Reg: <?php echo htmlspecialchars($selected_pedigree['sire']['reg_no']); ?></div>
                    </div>

                    <!-- Dam (Mother) -->
                    <div style="background: #FFF1F2; border: 2px solid #FDA4AF; border-radius: 18px; padding: 1.4rem;">
                        <div style="font-size: 0.8rem; font-weight: 800; color: #9F1239; text-transform: uppercase;">🌸 แม่พันธุ์ (Dam)</div>
                        <div id="pedDamName" style="font-size: 1.1rem; font-weight: 900; color: #1E293B; margin: 4px 0;"><?php echo htmlspecialchars($selected_pedigree['dam']['name']); ?></div>
                        <div id="pedDamTitle" style="font-size: 0.85rem; color: #E11D48; font-weight: 700;">🏆 <?php echo htmlspecialchars($selected_pedigree['dam']['title']); ?></div>
                        <div id="pedDamRegNo" style="font-size: 0.8rem; color: #64748B;">Reg: <?php echo htmlspecialchars($selected_pedigree['dam']['reg_no']); ?></div>
                    </div>
                </div>
            </div>

            <!-- Health & Genetic Screening Results -->
            <div style="margin-bottom: 3rem;">
                <h4 style="font-size: 1.3rem; font-weight: 900; color: #065F46; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 8px;">
                    🧬 ผลตรวจพันธุกรรม & โรคประจำสายพันธุ์ (Genetic Health Screening)
                </h4>
                <div style="background: #fff; border-radius: 18px; border: 1.5px solid #E2E8F0; overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 480px;">
                        <thead>
                            <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                                <th style="padding: 0.9rem 1.2rem; font-size: 0.85rem; color: #475569;">รายการตรวจคัดกรองโรค</th>
                                <th style="padding: 0.9rem 1.2rem; font-size: 0.85rem; color: #475569;">ผลการตรวจ (Result)</th>
                                <th style="padding: 0.9rem 1.2rem; font-size: 0.85rem; color: #475569; text-align: center;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody id="pedGeneticTableBody">
                            <?php foreach ($selected_pedigree['genetic_tests'] as $gtest): ?>
                                <tr style="border-bottom: 1px solid #F1F5F9;">
                                    <td style="padding: 0.9rem 1.2rem; font-weight: 800; color: #1E293B; font-size: 0.95rem;"><?php echo htmlspecialchars($gtest['test']); ?></td>
                                    <td style="padding: 0.9rem 1.2rem; color: #059669; font-weight: 800; font-size: 0.95rem;"><?php echo htmlspecialchars($gtest['result']); ?></td>
                                    <td style="padding: 0.9rem 1.2rem; text-align: center;">
                                        <span style="background: #DCFCE7; color: #166534; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 800;">✓ ผ่าน 100%</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Vaccination Records -->
            <div style="margin-bottom: 3rem;">
                <h4 style="font-size: 1.3rem; font-weight: 900; color: #1E40AF; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 8px;">
                    💉 ประวัติการฉีดวัคซีน & ถ่ายพยาธิ (Vaccination Passport)
                </h4>
                <div style="display: grid; gap: 1rem;" id="pedVaccineList">
                    <?php foreach ($selected_pedigree['vaccinations'] as $v): ?>
                        <div style="background: #F0F9FF; border-radius: 14px; padding: 1.1rem 1.4rem; border: 1.5px solid #BAE6FD; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <div style="font-weight: 900; color: #0369A1; font-size: 1rem;"><?php echo htmlspecialchars($v['dose']); ?></div>
                                <div style="font-size: 0.85rem; color: #64748B;">โดย: <?php echo htmlspecialchars($v['vet']); ?> • <?php echo htmlspecialchars($v['clinic']); ?></div>
                            </div>
                            <div style="background: #fff; border: 1px solid #7DD3FC; padding: 0.35rem 0.85rem; border-radius: 10px; font-weight: 800; color: #0284C7; font-size: 0.85rem;">
                                📅 <?php echo htmlspecialchars($v['date']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Vet Seal & Signatures -->
            <div class="pedigree-vet-signature-box" style="display: flex; justify-content: space-between; align-items: flex-end; padding-top: 2rem; border-top: 2px dashed #D97706; flex-wrap: wrap; gap: 2rem;">
                <div>
                    <div style="font-size: 0.85rem; color: #92400E; font-weight: 700;">วันที่ออกใบรับรอง: <span id="pedIssueDate"><?php echo htmlspecialchars($selected_pedigree['issue_date']); ?></span></div>
                    <div style="font-size: 0.8rem; color: #64748B;">ตราประทับดิจิทัลของโรงพยาบาลสัตว์และฟาร์มมาตรฐาน WCF/TICA</div>
                </div>

                <div class="pedigree-vet-sign-text" style="text-align: right;">
                    <div style="font-family: cursive, sans-serif; font-size: 1.6rem; color: #059669; font-weight: bold; margin-bottom: 2px;">
                        Nichakarn P., D.V.M.
                    </div>
                    <div id="pedVetSignature" style="font-weight: 900; color: #1E293B; font-size: 0.95rem;"><?php echo htmlspecialchars($selected_pedigree['vet_signature']); ?></div>
                    <div style="font-size: 0.8rem; color: #059669; font-weight: 800;">✓ สัตวแพทย์ประจำฟาร์มรับรองความสมบูรณ์ 100%</div>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 2rem;">
            <button onclick="window.print()" class="btn btn-primary" style="background: #D97706; border-color: #D97706; padding: 0.85rem 2.2rem; font-size: 1.05rem; border-radius: 999px; font-weight: 800; box-shadow: 0 6px 20px rgba(217, 119, 6, 0.35); cursor: pointer; max-width: 100%;">
                🖨️ พิมพ์ใบรับรองสายพันธุ์ (Print Official Pedigree)
            </button>
        </div>
    <?php endif; ?>
</div>

<style>
@media (max-width: 768px) {
    .pedigree-hero-banner {
        padding: 2rem 1.2rem !important;
        border-radius: 20px !important;
    }
    .pedigree-main-title {
        font-size: 1.8rem !important;
    }
    .pedigree-sub-desc {
        font-size: 1rem !important;
    }
    .pedigree-certificate-card {
        padding: 1.5rem !important;
        border-radius: 20px !important;
        border-width: 4px !important;
    }
    .pedigree-cert-title {
        font-size: 1.6rem !important;
    }
    .pedigree-profile-grid {
        grid-template-columns: 1fr !important;
        text-align: center;
        gap: 1.5rem !important;
    }
    .pedigree-specs-grid {
        grid-template-columns: 1fr !important;
        text-align: left;
    }
    .pedigree-tree-grid {
        grid-template-columns: 1fr !important;
    }
    .corner-embellishment {
        font-size: 1.2rem !important;
    }
    .pedigree-vet-signature-box {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
    .pedigree-vet-sign-text {
        text-align: left !important;
    }
}

@media (max-width: 500px) {
    .pedigree-search-form {
        flex-direction: column !important;
        border-radius: 20px !important;
        padding: 0.8rem !important;
    }
    .pedigree-search-form input {
        width: 100% !important;
        text-align: center;
    }
    .pedigree-search-form button {
        width: 100% !important;
    }
}

.chip-select-btn:hover, .chip-select-btn.active {
    background: #059669 !important;
    color: #fff !important;
    border-color: #059669 !important;
}
</style>

<!-- Client-side Dynamic Pedigree Lookup Script (Supports Static HTML & PHP Query) -->
<script>
const allPedigrees = <?php echo json_encode($pedigrees, JSON_UNESCAPED_UNICODE); ?>;

function renderPedigreeClient(chip) {
    if (!chip || !allPedigrees[chip]) return false;
    const p = allPedigrees[chip];

    // Header & Registry
    const reg = document.getElementById('pedRegistry');
    if (reg) reg.textContent = 'ออกโดย: ' + p.registry;

    const regNo = document.getElementById('pedRegNo');
    if (regNo) regNo.textContent = p.registration_no;

    const cattery = document.getElementById('pedCattery');
    if (cattery) cattery.textContent = p.cattery;

    // Image
    const img = document.getElementById('pedImage');
    if (img) {
        img.src = p.image;
        img.alt = p.name_th;
    }

    // Cat Details
    const nameThEn = document.getElementById('pedNameThEn');
    if (nameThEn) nameThEn.textContent = `${p.name_th} (${p.name_en})`;

    const breedTh = document.getElementById('pedBreedTh');
    if (breedTh) breedTh.textContent = p.breed_th;

    const microchip = document.getElementById('pedMicrochip');
    if (microchip) microchip.textContent = p.microchip;

    const genderDob = document.getElementById('pedGenderDob');
    if (genderDob) genderDob.textContent = `${p.gender} • ${p.dob}`;

    const color = document.getElementById('pedColor');
    if (color) color.textContent = p.color;

    // Sire & Dam
    const sireName = document.getElementById('pedSireName');
    if (sireName && p.sire) sireName.textContent = p.sire.name;

    const sireTitle = document.getElementById('pedSireTitle');
    if (sireTitle && p.sire) sireTitle.textContent = '🏆 ' + p.sire.title;

    const sireRegNo = document.getElementById('pedSireRegNo');
    if (sireRegNo && p.sire) sireRegNo.textContent = 'Reg: ' + p.sire.reg_no;

    const damName = document.getElementById('pedDamName');
    if (damName && p.dam) damName.textContent = p.dam.name;

    const damTitle = document.getElementById('pedDamTitle');
    if (damTitle && p.dam) damTitle.textContent = '🏆 ' + p.dam.title;

    const damRegNo = document.getElementById('pedDamRegNo');
    if (damRegNo && p.dam) damRegNo.textContent = 'Reg: ' + p.dam.reg_no;

    // Genetic Tests Table
    const tableBody = document.getElementById('pedGeneticTableBody');
    if (tableBody && p.genetic_tests) {
        tableBody.innerHTML = p.genetic_tests.map(g => `
            <tr style="border-bottom: 1px solid #F1F5F9;">
                <td style="padding: 0.9rem 1.2rem; font-weight: 800; color: #1E293B; font-size: 0.95rem;">${g.test}</td>
                <td style="padding: 0.9rem 1.2rem; color: #059669; font-weight: 800; font-size: 0.95rem;">${g.result}</td>
                <td style="padding: 0.9rem 1.2rem; text-align: center;">
                    <span style="background: #DCFCE7; color: #166534; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 800;">✓ ผ่าน 100%</span>
                </td>
            </tr>
        `).join('');
    }

    // Vaccinations List
    const vacList = document.getElementById('pedVaccineList');
    if (vacList && p.vaccinations) {
        vacList.innerHTML = p.vaccinations.map(v => `
            <div style="background: #F0F9FF; border-radius: 14px; padding: 1.1rem 1.4rem; border: 1.5px solid #BAE6FD; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="font-weight: 900; color: #0369A1; font-size: 1rem;">${v.dose}</div>
                    <div style="font-size: 0.85rem; color: #64748B;">โดย: ${v.vet} • ${v.clinic}</div>
                </div>
                <div style="background: #fff; border: 1px solid #7DD3FC; padding: 0.35rem 0.85rem; border-radius: 10px; font-weight: 800; color: #0284C7; font-size: 0.85rem;">
                    📅 ${v.date}
                </div>
            </div>
        `).join('');
    }

    // Date & Signature
    const issueDate = document.getElementById('pedIssueDate');
    if (issueDate) issueDate.textContent = p.issue_date;

    const vetSig = document.getElementById('pedVetSignature');
    if (vetSig) vetSig.textContent = p.vet_signature;

    // Search input sync
    const input = document.getElementById('pedChipInput');
    if (input) input.value = chip;

    // Highlight button
    document.querySelectorAll('.chip-select-btn').forEach(btn => {
        if (btn.getAttribute('data-chip') === chip) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    return true;
}

function selectChipClient(chip) {
    if (renderPedigreeClient(chip)) {
        try {
            const newUrl = window.location.pathname + '?chip=' + encodeURIComponent(chip);
            window.history.pushState({ chip: chip }, '', newUrl);
        } catch(e) {}
    }
}

function handlePedigreeSearch(e) {
    const input = document.getElementById('pedChipInput');
    const chip = input ? input.value.trim() : '';
    if (chip && allPedigrees[chip]) {
        if (e) e.preventDefault();
        selectChipClient(chip);
    }
}

// Auto-run on load from URL parameters (?chip=...)
document.addEventListener('DOMContentLoaded', () => {
    try {
        const params = new URLSearchParams(window.location.search);
        const chip = params.get('chip');
        if (chip && allPedigrees[chip]) {
            renderPedigreeClient(chip);
        }
    } catch(e) {}
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
