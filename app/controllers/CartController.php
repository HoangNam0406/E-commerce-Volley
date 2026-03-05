<?php
/**
 * Cart Controller
 */
class CartController extends Controller
{

    /**
     * Show cart page
     */
    public function index()
    {
        $this->requireAuth();

        $cartModel = new Cart();
        $cartItems = $cartModel->getCustomerCart($this->getUserId());
        $cartTotal = $cartModel->getCartTotal($this->getUserId());

        $this->render('cart/index', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal
        ]);
    }

    /**
     * Add to cart (AJAX)
     */
    public function add()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Please login to use cart']);
        }

        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $productId = sanitize($_POST['product_id'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (empty($productId) || $quantity <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid product or quantity'], 400);
        }

        $productModel = new Product();
        $product = $productModel->findById($productId);

        if (!$product || $product['status'] !== 'active') {
            $this->jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
        }

        if ($product['stock_quantity'] < $quantity) {
            $this->jsonResponse(['success' => false, 'message' => 'Insufficient stock'], 400);
        }

        $cartModel = new Cart();
        $cartModel->addToCart($this->getUserId(), $productId, $quantity);

        $cartCount = $cartModel->countItems($this->getUserId());

        $this->jsonResponse([
            'success' => true,
            'message' => 'Product added to cart',
            'cartCount' => $cartCount
        ]);
    }

    /**
     * Update cart quantity (AJAX)
     */
    public function updateQuantity()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Please login to use cart']);
        }

        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $cartId = (int)($_POST['cart_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($cartId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid cart item'], 400);
        }

        $cartModel = new Cart();
        $cartModel->updateQuantity($cartId, $this->getUserId(), $quantity);

        $cartTotal = $cartModel->getCartTotal($this->getUserId());

        $this->jsonResponse([
            'success' => true,
            'message' => 'Quantity updated',
            'cartTotal' => $cartTotal
        ]);
    }

    /**
     * Remove from cart (AJAX)
     */
    public function remove()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Please login to use cart']);
        }

        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $cartId = (int)($_POST['cart_id'] ?? 0);

        if ($cartId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid cart item'], 400);
        }

        $cartModel = new Cart();
        $cartModel->removeFromCart($cartId, $this->getUserId());

        $cartCount = $cartModel->countItems($this->getUserId());
        $cartTotal = $cartModel->getCartTotal($this->getUserId());

        $this->jsonResponse([
            'success' => true,
            'message' => 'Item removed from cart',
            'cartCount' => $cartCount,
            'cartTotal' => $cartTotal
        ]);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        $this->requireAuth();

        $cartModel = new Cart();
        $cartModel->clearCart($this->getUserId());

        redirectWithMessage(APP_URL . '/cart', 'Cart cleared', 'success');
    }

    /**
     * Get cart count (AJAX)
     */
    public function getCount()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['count' => 0]);
        }

        $cartModel = new Cart();
        $count = $cartModel->countItems($this->getUserId());

        $this->jsonResponse(['count' => $count]);
    }
}
?>
