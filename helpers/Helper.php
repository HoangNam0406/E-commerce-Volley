<?php
/**
 * Helper Functions
 */

/**
 * Sanitize input
 */
function sanitize($input)
{
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Convert VND to USD
 */
function convertToUSD($vndAmount)
{
    return $vndAmount / EXCHANGE_RATE;
}

/**
 * Format currency
 */
function formatCurrency($amount, $currency = null)
{
    $currency = $currency ?? CURRENCY;
    
    // Nếu là USD, chuyển đổi từ VND sang USD
    if ($currency === 'USD') {
        $amount = convertToUSD($amount);
        return '$' . number_format($amount, 2, '.', ',');
    }
    
    return number_format($amount, 0, ',', '.') . ' ' . $currency;
}

/**
 * Format date
 */
function formatDate($date, $format = 'Y-m-d H:i:s')
{
    return date($format, strtotime($date));
}

/**
 * Get translated text
 */
function __($key, $lang = null)
{
    $lang = $lang ?? $_SESSION['language'] ?? DEFAULT_LANGUAGE;
    // Simple translation implementation
    $translations = [
        'en' => require __DIR__ . '/../resources/lang/en.php',
        'vi' => require __DIR__ . '/../resources/lang/vi.php'
    ];

    return $translations[$lang][$key] ?? $key;
}

/**
 * Redirect with message
 */
function redirectWithMessage($url, $message, $type = 'success')
{
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header('Location: ' . $url);
    exit;
}

/**
 * Get flash message
 */
function getFlashMessage()
{
    $message = $_SESSION['message'] ?? null;
    $type = $_SESSION['message_type'] ?? 'info';

    unset($_SESSION['message'], $_SESSION['message_type']);

    return ['message' => $message, 'type' => $type];
}

/**
 * Check if user has message
 */
function hasFlashMessage()
{
    return isset($_SESSION['message']);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Upload file
 */
function uploadFile($file, $directory = UPLOAD_DIR)
{
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload failed'];
    }

    // Check file type
    $filename = $file['name'];
    $fileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($fileType, ALLOWED_FILE_TYPES)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }

    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'File too large'];
    }

    // Create directory if not exists
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    // Generate unique filename
    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $directory . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath
        ];
    }

    return ['success' => false, 'error' => 'Failed to save file'];
}

/**
 * Calculate order amounts
 */
function calculateOrderAmounts($totalAmount, $discountAmount = 0)
{
    $subtotal = $totalAmount - $discountAmount;
    $platformFee = $subtotal * ADMIN_PLATFORM_FEE;
    $transactionFee = $subtotal * VNPAY_TRANSACTION_FEE;
    $sellerAmount = $subtotal - $platformFee - $transactionFee;

    return [
        'subtotal' => $subtotal,
        'discount' => $discountAmount,
        'platform_fee' => $platformFee,
        'transaction_fee' => $transactionFee,
        'seller_amount' => $sellerAmount,
        'total' => $totalAmount
    ];
}

/**
 * Check if product is in stock
 */
function isInStock($quantity, $stockQuantity)
{
    return $quantity > 0 && $quantity <= $stockQuantity;
}

/**
 * Sanitize filename
 */
function sanitizeFilename($filename)
{
    $filename = basename($filename);
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return $filename;
}

/**
 * Get current page number
 */
function getCurrentPage()
{
    return isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
}

/**
 * Get pagination info
 */
function getPaginationInfo($totalItems, $itemsPerPage)
{
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = getCurrentPage();

    if ($currentPage > $totalPages) {
        $currentPage = max(1, $totalPages);
    }

    $offset = ($currentPage - 1) * $itemsPerPage;

    return [
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'limit' => $itemsPerPage,
        'total_items' => $totalItems
    ];
}

/**
 * Dice exception handling
 */
function handleException($exception)
{
    if (APP_DEBUG) {
        echo "<pre>";
        echo $exception->getMessage() . "\n";
        echo $exception->getTraceAsString();
        echo "</pre>";
    }
    else {
        die('An error occurred. Please try again later.');
    }
}
?>
