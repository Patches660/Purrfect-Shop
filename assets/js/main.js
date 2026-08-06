// Main JavaScript for Cat Cyber Shop

// Calculate custom scope prices dynamically
function calculatePrice() {
    const baseSelect = document.getElementById('base_scale');
    if (!baseSelect) return; // Only run on products page

    // Base pricing options
    const selectedOption = baseSelect.options[baseSelect.selectedIndex];
    const basePrice = parseInt(selectedOption.getAttribute('data-price')) || 0;
    const baseName = selectedOption.text.split(' (')[0];

    // Addon checkboxes
    const optAuth = document.getElementById('opt_auth');
    const optDb = document.getElementById('opt_db');
    const optPay = document.getElementById('opt_pay');
    const optExpress = document.getElementById('opt_express');
    const userReq = document.getElementById('user_req').value.trim();

    let total = basePrice;
    let detailsList = [baseName];

    // Check auth
    if (optAuth && optAuth.checked) {
        total += parseInt(optAuth.value);
        document.getElementById('row-auth').style.display = 'flex';
        detailsList.push('ระบบสิทธิ์การล็อกอิน');
    } else if (document.getElementById('row-auth')) {
        document.getElementById('row-auth').style.display = 'none';
    }

    // Check db
    if (optDb && optDb.checked) {
        total += parseInt(optDb.value);
        document.getElementById('row-db').style.display = 'flex';
        detailsList.push('ฐานข้อมูล MySQL');
    } else if (document.getElementById('row-db')) {
        document.getElementById('row-db').style.display = 'none';
    }

    // Check payment gateway
    if (optPay && optPay.checked) {
        total += parseInt(optPay.value);
        document.getElementById('row-pay').style.display = 'flex';
        detailsList.push('เกตเวย์ชำระเงิน');
    } else if (document.getElementById('row-pay')) {
        document.getElementById('row-pay').style.display = 'none';
    }

    // Check express delivery
    if (optExpress && optExpress.checked) {
        total += parseInt(optExpress.value);
        document.getElementById('row-express').style.display = 'flex';
        detailsList.push('ส่งด่วนพิเศษ');
    } else if (document.getElementById('row-express')) {
        document.getElementById('row-express').style.display = 'none';
    }

    // Update labels
    document.getElementById('lbl-base').innerText = basePrice.toLocaleString() + ' ฿';
    document.getElementById('lbl-total').innerText = total.toLocaleString() + ' ฿';

    // Set form hidden input fields
    document.getElementById('form-custom-price').value = total;

    // Build the description note
    let customNote = `ขอบเขต ${baseName} (เพิ่ม: ` + detailsList.slice(1).join(', ');
    if (detailsList.length === 1) {
        customNote += 'ไม่มีเพิ่มเติม';
    }
    customNote += ')';

    if (userReq) {
        customNote += ` - ความต้องการผู้ใช้: "${userReq}"`;
    }

    document.getElementById('form-custom-notes').value = customNote;
}

// Initial calculation run on load if elements exist
window.addEventListener('DOMContentLoaded', () => {
    calculatePrice();
});
