<?php
// Logout handler
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['user']);
header("Location: index.php?msg=logged_out");
exit;
