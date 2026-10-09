<?php
// Comprehensive Logout Handler
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['user']);
unset($_SESSION['cart']);
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ออกจากระบบ...</title>
</head>
<body style="background: #0B0F19; color: #FFF; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif;">
    <div style="text-align: center;">
        <span style="font-size: 3rem;">🚪</span>
        <h3>กำลังออกจากระบบ...</h3>
    </div>
    <script>
    try {
        localStorage.removeItem('cat_shop_logged_user');
        localStorage.removeItem('cat_shop_cart');
    } catch(e) {}
    window.location.replace('index.php?msg=logged_out');
    </script>
</body>
</html>
