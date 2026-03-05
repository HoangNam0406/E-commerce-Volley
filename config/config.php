<?php
/**
 * Application Configuration
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce_volley');

// Application Configuration
define('APP_NAME', 'E-Commerce Volley');
// When using PHP built-in server, ensure this URL matches the port you start it on
// e.g. `php -S localhost:3000 -t public` would require port 3000 below.
define('APP_URL', 'http://localhost:3000');
define('APP_DEBUG', true);
define('APP_ENV', 'development');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('SESSION_NAME', 'ecommerce_session');

// VNPay Configuration
define('VNPAY_TMNCODE', '2QXUJ63D');
define('VNPAY_HASHSECRET', 'SFWIIENIVXML3TJQVFEGZPBMHUJHAKSA');
define('VNPAY_URL', 'https://sandbox.vnpayment.vn/payapi/vnpay_api.php');
define('VNPAY_RETURN_URL', APP_URL . '/payment/vnpay-return');

// Admin Fees
define('ADMIN_PLATFORM_FEE', 0.05); // 5% platform fee
define('VNPAY_TRANSACTION_FEE', 0.015); // 1.5% transaction fee

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/../public/images/');
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Pagination
define('ITEMS_PER_PAGE', 12);

// Language Configuration
define('DEFAULT_LANGUAGE', 'en');
define('SUPPORTED_LANGUAGES', ['en', 'vi']);

// Currency Configuration
define('CURRENCY', 'USD');
define('EXCHANGE_RATE', 24000); // 1 USD = 24,000 VND (Tỷ giá hiện tại)
?>
