<?php
/**
 * Application Entry Point
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// ensure request uses the correct host/port as defined by APP_URL
$parsed = parse_url(APP_URL);
$expectedHost = $parsed['host'] ?? '';
$expectedPort = $parsed['port'] ?? ($_SERVER['SERVER_PORT'] ?? 80);

// if the script is ever executed on a port other than the one configured, show a message
if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != $expectedPort) {
    // it's possible the user started a built-in PHP server on a different port
    echo '<h1>Incorrect port</h1>';
    echo '<p>Your server is running on port ' . htmlspecialchars($_SERVER['SERVER_PORT']) . ", but APP_URL is configured for port $expectedPort.</p>";
    echo '<p>Update <code>config/config.php</code> <strong>APP_URL</strong> or access the correct port.</p>';
    exit;
}

if (strpos($_SERVER['HTTP_HOST'], $expectedHost) === false) {
    // redirect to canonical URL
    $redirect = APP_URL . ($_SERVER['REQUEST_URI'] ?? '/');
    header('Location: ' . $redirect, true, 301);
    exit;
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include autoloader, helpers, and security guard
require_once __DIR__ . '/core/Autoloader.php';
require_once __DIR__ . '/helpers/Helper.php';
require_once __DIR__ . '/core/SecurityMiddleware.php';

// Initialize database connection
$database = Database::getInstance();

// Check for malicious requests (DDoS protection)
SecurityMiddleware::check();

// Route handler
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// if the raw URI contains an unencoded space, redirect to encoded version
if (strpos($requestUri, ' ') !== false) {
    $redirect = str_replace(' ', '%20', $requestUri);
    header('Location: ' . $redirect, true, 301);
    exit;
}

// ensure index.php exists before continuing (prevents Apache 404 before PHP)
if (!file_exists(__DIR__ . '/index.php')) {
    http_response_code(500);
    echo '<h1>Server misconfiguration</h1><p>index.php not found.</p>';
    exit;
}

// strip application base path whether encoded or not
$requestUri = str_replace('/Projects/E-commerce%20Volley', '', $requestUri);
$requestUri = str_replace('/Projects/E-commerce Volley', '', $requestUri);

// remove explicit index.php if present
$requestUri = str_replace('index.php', '', $requestUri);
$requestUri = trim($requestUri, '/');

// Default route
if (empty($requestUri)) {
    $requestUri = 'home';
}

// Parse route
$routeParts = explode('/', $requestUri);
$action = $routeParts[0] ?? 'home';
$method = $routeParts[1] ?? 'index';
$params = array_slice($routeParts, 2);

// Route mapping
$routes = [
    'home' => 'HomeController@index',
    'contact' => 'HomeController@contact',
    'contact-submit' => 'HomeController@submitContact',

    'categories' => 'CategoryController@index',
    'category' => 'CategoryController@view',

    'products' => 'ProductController@index',
    'product' => 'ProductController@detail',
    'search' => 'ProductController@search',

    'cart' => 'CartController@index',
    'cart-add' => 'CartController@add',
    'cart-update' => 'CartController@updateQuantity',
    'cart-remove' => 'CartController@remove',
    'cart-clear' => 'CartController@clear',
    'cart-count' => 'CartController@getCount',

    'orders' => 'OrderController@index',
    'order-detail' => 'OrderController@detail',
    'order-checkout' => 'OrderController@checkout',
    'order-place' => 'OrderController@place',
    'order-success' => 'OrderController@success',
    'order-cancel' => 'OrderController@cancel',

    'payment' => 'PaymentController@vnpay',
    'payment-vnpay-return' => 'PaymentController@vnpayReturn',

    'account' => 'AccountController@profile',
    'account-profile' => 'AccountController@profile',
    'account-update' => 'AccountController@updateProfile',
    'account-avatar' => 'AccountController@updateAvatar',
    'account-wallet' => 'AccountController@wallet',
    'account-wishlist' => 'AccountController@wishlist',
    'account-add-wishlist' => 'AccountController@addToWishlist',
    'account-remove-wishlist' => 'AccountController@removeFromWishlist',

    'auth' => 'AuthController@loginView',
    'login' => 'AuthController@login',
    'register' => 'AuthController@register',
    'logout' => 'AuthController@logout',
    'change-password' => 'AuthController@changePassword',

    'seller' => 'SellerDashboardController@index',
    'seller-dashboard' => 'SellerDashboardController@index',
    'seller-products' => 'SellerDashboardController@products',
    'seller-product-add' => 'SellerDashboardController@addProductView',
    'seller-product-create' => 'SellerDashboardController@addProduct',
    'seller-product-edit' => 'SellerDashboardController@editProductView',
    'seller-product-update' => 'SellerDashboardController@editProduct',
    'seller-product-delete' => 'SellerDashboardController@deleteProduct',
    'seller-orders' => 'SellerDashboardController@orders',
    'seller-order-detail' => 'SellerDashboardController@orderDetail',
    'seller-order-confirm' => 'SellerDashboardController@confirmOrder',
    'seller-order-ship' => 'SellerDashboardController@shipOrder',
    'seller-order-cancel' => 'SellerDashboardController@cancelOrder',

    'admin' => 'AdminDashboardController@index',
    'admin-dashboard' => 'AdminDashboardController@index',
    'admin-users' => 'AdminDashboardController@users',
    'admin-user-delete' => 'AdminDashboardController@deleteUser',
    'admin-categories' => 'AdminDashboardController@categories',
    'admin-category-add' => 'AdminDashboardController@addCategory',
    'admin-category-edit' => 'AdminDashboardController@editCategory',
    'admin-category-delete' => 'AdminDashboardController@deleteCategory',
    'admin-banners' => 'AdminDashboardController@banners',
    'admin-banner-add' => 'AdminDashboardController@addBanner',
    'admin-banner-edit' => 'AdminDashboardController@editBanner',
    'admin-banner-delete' => 'AdminDashboardController@deleteBanner',
];

// Get route
try {
    $routeKey = null;
    $route = null;

    // Try combining first two parts first
    if (!empty($method) && $method !== 'index') {
        $routeKey = $action . '-' . $method;
        $route = $routes[$routeKey] ?? null;
        if ($route) {
            $params = array_slice($routeParts, 2);
        }
    }

    // If not found, try exact route
    if (!$route) {
        $routeKey = $action;
        $route = $routes[$routeKey] ?? null;
        if ($route) {
            $params = array_slice($routeParts, 1);
        }
    }

    if (!$route) {
        // Send 404 header but show friendly message or forward to controller
        http_response_code(404);
        echo '<h1>404 Not Found</h1><p>Route not found: ' . htmlspecialchars($requestUri) . '</p>';
        exit;
    }

    // Parse controller and method
    [$controllerName, $methodName] = explode('@', $route);

    // Instantiate controller
    $controller = new $controllerName();

    // Call method with parameters
    if (!empty($params)) {
        call_user_func_array([$controller, $methodName], $params);
    }
    else {
        $controller->$methodName();
    }
}
catch (Exception $e) {
    handleException($e);
}
?>
