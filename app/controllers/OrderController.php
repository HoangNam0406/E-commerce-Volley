<?php
/**
 * Order Controller
 */
class OrderController extends Controller {

    /**
     * Show checkout page
     */
    public function checkout() {
        $this->requireAuth();

        $cartModel = new Cart();
        $cartItems = $cartModel->getCustomerCart($this->getUserId());

        if (empty($cartItems)) {
            redirectWithMessage(APP_URL . '/cart', 'Your cart is empty', 'error');
        }

        $cartTotal = $cartModel->getCartTotal($this->getUserId());
        $userModel = new User();
        $user = $userModel->findById($this->getUserId());

        $this->render('orders/checkout', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'user' => $user
        ]);
    }

    /**
     * Place order
     */
    public function place() {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect(APP_URL . '/orders/checkout');
        }

        $shippingName = sanitize($_POST['shipping_name'] ?? '');
        $shippingPhone = sanitize($_POST['shipping_phone'] ?? '');
        $shippingAddress = sanitize($_POST['shipping_address'] ?? '');
        $paymentMethod = sanitize($_POST['payment_method'] ?? 'cod');
        $selectedCartIds = isset($_POST['selected_cart_ids']) ? json_decode($_POST['selected_cart_ids'], true) : [];

        // Validation
        if (empty($shippingName) || empty($shippingPhone) || empty($shippingAddress)) {
            redirectWithMessage(APP_URL . '/orders/checkout', 'All shipping fields are required', 'error');
        }

        if (!in_array($paymentMethod, ['cod', 'vnpay'])) {
            redirectWithMessage(APP_URL . '/orders/checkout', 'Invalid payment method', 'error');
        }

        $cartModel = new Cart();
        $productModel = new Product();
        $orderModel = new Order();

        $cartItems = $cartModel->getCustomerCart($this->getUserId());

        if (empty($cartItems)) {
            redirectWithMessage(APP_URL . '/cart', 'Your cart is empty', 'error');
        }

        // Filter cart items by selected cart IDs if provided
        if (!empty($selectedCartIds)) {
            $cartItems = array_filter($cartItems, function($item) use ($selectedCartIds) {
                return in_array($item['cart_id'], $selectedCartIds);
            });
            $cartItems = array_values($cartItems); // Re-index array
        }

        if (empty($cartItems)) {
            redirectWithMessage(APP_URL . '/cart', 'Please select items to checkout', 'error');
        }

        try {
            // Group items by seller
            $itemsBySeller = [];
            foreach ($cartItems as $item) {
                if (!isset($itemsBySeller[$item['seller_id']])) {
                    $itemsBySeller[$item['seller_id']] = [];
                }
                $itemsBySeller[$item['seller_id']][] = $item;
            }

            // Create order for each seller
            $orderIds = [];
            foreach ($itemsBySeller as $sellerId => $items) {
                $totalAmount = 0;
                $orderItems = [];

                foreach ($items as $item) {
                    $itemTotal = $item['current_price'] * $item['quantity'];
                    $totalAmount += $itemTotal;

                    $orderItems[] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['current_price'],
                        'discount_percentage' => $item['discount_percentage'] ?? 0
                    ];
                }

                // Calculate amounts
                $amounts = calculateOrderAmounts($totalAmount);

                $orderData = [
                    'order_number' => $orderModel->generateOrderNumber(),
                    'customer_id' => $this->getUserId(),
                    'seller_id' => $sellerId,
                    'total_amount' => $totalAmount,
                    'discount_amount' => $amounts['discount'] ?? 0,
                    'platform_fee' => $amounts['platform_fee'],
                    'transaction_fee' => $amounts['transaction_fee'],
                    'seller_amount' => $amounts['seller_amount'],
                    'status' => 'pending',
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentMethod === 'cod' ? 'unpaid' : 'pending',
                    'shipping_address' => $shippingAddress,
                    'shipping_phone' => $shippingPhone,
                    'shipping_name' => $shippingName
                ];

                $orderId = $orderModel->createOrderWithItems($orderData, $orderItems);
                $orderIds[] = $orderId;
            }

            // Remove only selected items from cart
            if (!empty($selectedCartIds)) {
                foreach ($selectedCartIds as $cartId) {
                    $cartModel->removeFromCart($cartId, $this->getUserId());
                }
            } else {
                // Clear all cart if no selection (for backward compatibility)
                $cartModel->clearCart($this->getUserId());
            }

            // Redirect based on payment method
            if ($paymentMethod === 'vnpay') {
                // Redirect to VNPay payment
                $_SESSION['order_ids'] = $orderIds;
                $this->redirect(APP_URL . '/payment/vnpay');
            } else {
                // Redirect to success page for COD
                $_SESSION['order_ids'] = $orderIds;
                $this->redirect(APP_URL . '/orders/success');
            }
        } catch (Exception $e) {
            redirectWithMessage(APP_URL . '/orders/checkout', 'Error creating order: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Order success
     */
    public function success() {
        $this->requireAuth();

        $orderIds = $_SESSION['order_ids'] ?? [];
        unset($_SESSION['order_ids']);

        $orderModel = new Order();
        $orders = [];

        foreach ($orderIds as $orderId) {
            $order = $orderModel->findById($orderId);
            if ($order) {
                $order['items'] = $orderModel->getOrderItems($orderId);
                $orders[] = $order;
            }
        }

        $this->render('orders/success', [
            'orders' => $orders
        ]);
    }

    /**
     * Show orders list
     */
    public function index() {
        $this->requireAuth();

        $orderModel = new Order();
        $page = getCurrentPage();

        if ($this->hasRole('seller')) {
            $totalOrders = $orderModel->count(['seller_id' => $this->getUserId()]);
            $pagination = getPaginationInfo($totalOrders, ITEMS_PER_PAGE);
            $orders = $orderModel->getSellerOrders($this->getUserId(), $pagination['limit'], $pagination['offset']);
        } else {
            $totalOrders = $orderModel->count(['customer_id' => $this->getUserId()]);
            $pagination = getPaginationInfo($totalOrders, ITEMS_PER_PAGE);
            $orders = $orderModel->getCustomerOrders($this->getUserId(), $pagination['limit'], $pagination['offset']);
        }

        $this->render('orders/index', [
            'orders' => $orders,
            'pagination' => $pagination
        ]);
    }

    /**
     * Show order detail
     */
    public function detail($orderId) {
        $this->requireAuth();

        $orderModel = new Order();
        $order = $orderModel->findById($orderId);

        if (!$order) {
            http_response_code(404);
            die('Order not found');
        }

        // Check authorization
        if ($this->hasRole('customer') && $order['customer_id'] != $this->getUserId()) {
            http_response_code(403);
            die('Access denied');
        }

        if ($this->hasRole('seller') && $order['seller_id'] != $this->getUserId()) {
            http_response_code(403);
            die('Access denied');
        }

        $orderItems = $orderModel->getOrderItems($orderId);

        $this->render('orders/detail', [
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }

    /**
     * Cancel order (Customer)
     */
    public function cancel($orderId) {
        $this->requireAuth();

        $orderModel = new Order();
        $order = $orderModel->findById($orderId);

        if (!$order || $order['customer_id'] != $this->getUserId()) {
            http_response_code(403);
            die('Access denied');
        }

        if ($order['status'] !== 'pending') {
            redirectWithMessage(APP_URL . '/orders/' . $orderId, 'Cannot cancel this order', 'error');
        }

        try {
            $orderModel->cancelOrder($orderId, 'Cancelled by customer');
            redirectWithMessage(APP_URL . '/orders', 'Order cancelled successfully', 'success');
        } catch (Exception $e) {
            redirectWithMessage(APP_URL . '/orders/' . $orderId, 'Error cancelling order', 'error');
        }
    }
}
?>
