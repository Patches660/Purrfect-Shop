<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/data.php';

// Detect active page name
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat Cyber Shop - แมวและโปรแกรมตามสั่ง</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="index.php" class="logo">
                <img src="assets/images/logo.png" alt="Cat Cyber Shop Logo" class="logo-img">
                <span class="logo-text">CAT <span>CYBER</span> SHOP</span>
            </a>
            <nav>
                <ul class="nav-menu">
                    <li>
                        <a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
                    </li>
                    <li>
                        <a href="products.php" class="nav-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>">Products</a>
                    </li>
                    <li>
                        <a href="creator.php" class="nav-link <?php echo $current_page == 'creator.php' ? 'active' : ''; ?>">Creator</a>
                    </li>
                    <li class="cart-badge-container">
                        <a href="cart.php" class="nav-link <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>">
                            🛒 Cart
                            <?php 
                            $count = getCartCount();
                            if ($count > 0): 
                            ?>
                                <span class="cart-badge" id="cart-badge-val"><?php echo $count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
