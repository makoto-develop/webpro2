<?php
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    define('DB_URL', 'mysql');
    define('DB_USER', 'user1');
    define('DB_PASS', 'user1password');
    define('DB_NAME', 'webshop');
} else {
    define('DB_URL', 'mysql403.phy.lolipop.lan');
    define('DB_USER', 'LAA1710026');
    define('DB_PASS', 'rootpassword');
    define('DB_NAME', 'LAA1710026-webshop');
}
?>