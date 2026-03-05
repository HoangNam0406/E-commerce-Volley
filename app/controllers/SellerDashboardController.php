<?php
/**
 * Seller Dashboard Controller
 */
class SellerDashboardController extends Controller {

    /**
     * Check seller access
     */
    private function requireSeller() {
        $this->requireRole('seller');
    }

    /**
     * Show dashboard
     */
    public function index() {
        $this->requireSeller();

        $sellerId = $this->getUserId();
        $orderModel = new Order();
        $productModel = new Product();

        // Dashboard statistics
        $totalOrders = $orderModel->count(['seller_id' => $sellerId]);
        $totalProducts = $productModel->count(['seller_id' => $sellerId, 'status' => 'active']);
        $totalRevenue = $orderModel->getSellerRevenue($sellerId);

        // Recent orders
        $recentOrders = $orderModel->getSellerOrders($sellerId, 5);

        $this->render('seller/dashboard', [
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalRevenue' => $totalRevenue,
            'recentOrders' => $recentOrders
        ]);
    }

    /**
     * Show products management
     */
    public function products() {
        $this->requireSeller();

        $productModel = new Product();
        $page = getCurrentPage();

        $totalProducts = $productModel->count(['seller_id' => $this->getUserId()]);
        $pagination = getPaginationInfo($totalProducts, ITEMS_PER_PAGE);

        $products = $productModel->getBySeller($this->getUserId(), $pagination['limit'], $pagination['offset']);

        $this->render('seller/products/index', [
            'products' => $products,
            'pagination' => $pagination
        ]);
    }

    /**
     * Show add product form
     */
    public function addProductView() {
        $this->requireSeller();

        $categoryModel = new Category();
        $categories = $categoryModel->getActive();

        $this->render('seller/products/add', ['categories' => $categories]);
    }

    /**
     * Handle add product
     */
    public function addProduct() {
        $this->requireSeller();

        if (!$this->isPost()) {
            $this->addProductView();
            return;
        }

        $name = sanitize($_POST['name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock_quantity'] ?? 0);
        $description = sanitize($_POST['description'] ?? '');

        if (empty($name) || $categoryId <= 0 || $price <= 0 || $stock < 0) {
            redirectWithMessage(APP_URL . '/seller/products/add', 'All fields are required', 'error');
        }

        $productModel = new Product();

        $productData = [
            'seller_id' => $this->getUserId(),
            'category_id' => $categoryId,
            'name' => $name,
            'price' => $price,
            'stock_quantity' => $stock,
            'description' => $description,
            'status' => 'active'
        ];

        $productModel->create($productData);
        $productId = Database::getInstance()->getConnection()->lastInsertId();

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['image']);
            if ($uploadResult['success']) {
                // Save to product_images table
                if ($productModel->addImage($productId, $uploadResult['filename'], true)) {
                    // Also save the filename on the products table
                    $productModel->update($productId, ['image' => $uploadResult['filename']]);
                }
            }
        }

        redirectWithMessage(APP_URL . '/seller/products', 'Product added successfully', 'success');
    }

    /**
     * Show edit product form
     */
    public function editProductView($productId) {
        $this->requireSeller();

        $productModel = new Product();
        $categoryModel = new Category();

        $product = $productModel->findById($productId);

        if (!$product || $product['seller_id'] != $this->getUserId()) {
            redirectWithMessage(APP_URL . '/seller/products', 'Product not found', 'error');
        }

        $categories = $categoryModel->getActive();
        $images = $productModel->getImages($productId);

        $this->render('seller/products/edit', [
            'product' => $product,
            'categories' => $categories,
            'images' => $images
        ]);
    }

    /**
     * Handle edit product
     */
    public function editProduct($productId) {
        $this->requireSeller();

        if (!$this->isPost()) {
            $this->editProductView($productId);
            return;
        }

        $productModel = new Product();
        $product = $productModel->findById($productId);

        if (!$product || $product['seller_id'] != $this->getUserId()) {
            redirectWithMessage(APP_URL . '/seller/products', 'Product not found', 'error');
        }

        $name = sanitize($_POST['name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock_quantity'] ?? 0);
        $description = sanitize($_POST['description'] ?? '');
        $discountPercentage = (float)($_POST['discount_percentage'] ?? 0);

        // Validate required fields
        if (empty($name) || $categoryId <= 0 || $price <= 0 || $stock < 0) {
            redirectWithMessage(APP_URL . '/seller/product-edit/' . $productId, 'All required fields must be valid', 'error');
        }

        // Handle datetime-local format conversion
        $discountStartDate = null;
        $discountEndDate = null;
        
        if (!empty($_POST['discount_start_date'])) {
            $discountStartDate = str_replace('T', ' ', $_POST['discount_start_date']) . ':00';
        }
        if (!empty($_POST['discount_end_date'])) {
            $discountEndDate = str_replace('T', ' ', $_POST['discount_end_date']) . ':00';
        }

        $updateData = [
            'name' => $name,
            'category_id' => $categoryId,
            'price' => $price,
            'stock_quantity' => $stock,
            'description' => $description,
            'discount_percentage' => $discountPercentage,
            'discount_start_date' => $discountStartDate,
            'discount_end_date' => $discountEndDate
        ];

        $productModel->update($productId, $updateData);

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['image']);
            if ($uploadResult['success']) {
                // Delete old images
                $productModel->deleteImages($productId);
                // Save new image to product_images table
                if ($productModel->addImage($productId, $uploadResult['filename'], true)) {
                    // Also update the products table
                    $productModel->update($productId, ['image' => $uploadResult['filename']]);
                }
            }
        }

        redirectWithMessage(APP_URL . '/seller/products', 'Product updated successfully', 'success');
    }

    /**
     * Delete product
     */
    public function deleteProduct($productId) {
        $this->requireSeller();

        $productModel = new Product();
        $product = $productModel->findById($productId);

        if (!$product || $product['seller_id'] != $this->getUserId()) {
            redirectWithMessage(APP_URL . '/seller/products', 'Product not found', 'error');
        }

        $productModel->update($productId, ['status' => 'deleted']);

        redirectWithMessage(APP_URL . '/seller/products', 'Product deleted successfully', 'success');
    }

    /**
     * Show orders
     */
    public function orders() {
        $this->requireSeller();

        $orderModel = new Order();
        $page = getCurrentPage();

        $totalOrders = $orderModel->count(['seller_id' => $this->getUserId()]);
        $pagination = getPaginationInfo($totalOrders, ITEMS_PER_PAGE);

        $orders = $orderModel->getSellerOrders($this->getUserId(), $pagination['limit'], $pagination['offset']);

        $this->render('seller/orders/index', [
            'orders' => $orders,
            'pagination' => $pagination
        ]);
    }

    /**
     * Show order detail
     */
    public function orderDetail($orderId) {
        $this->requireSeller();

        $orderModel = new Order();
        $order = $orderModel->findById($orderId);

        if (!$order || $order['seller_id'] != $this->getUserId()) {
            redirectWithMessage(APP_URL . '/seller/orders', 'Order not found', 'error');
        }

        $orderItems = $orderModel->getOrderItems($orderId);

        $this->render('seller/orders/detail', [
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }

    /**
     * Confirm order
     */
    public function confirmOrder($orderId) {
        $this->requireSeller();

        $orderModel = new Order();
        $order = $orderModel->findById($orderId);

        if (!$order || $order['seller_id'] != $this->getUserId()) {
            redirectWithMessage(APP_URL . '/seller/orders', 'Order not found', 'error');
        }

        $orderModel->updateStatus($orderId, 'confirmed');

        redirectWithMessage(APP_URL . '/seller/orders/' . $orderId, 'Order confirmed', 'success');
    }

    /**
     * Ship order
     */
    public function shipOrder($orderId) {
        $this->requireSeller();

        $orderModel = new Order();
        $order = $orderModel->findById($orderId);

        if (!$order || $order['seller_id'] != $this->getUserId()) {
            redirectWithMessage(APP_URL . '/seller/orders', 'Order not found', 'error');
        }

        $orderModel->updateStatus($orderId, 'shipped');

        redirectWithMessage(APP_URL . '/seller/orders/' . $orderId, 'Order shipped', 'success');
    }

    /**
     * Cancel order with reason
     */
    public function cancelOrder($orderId) {
        $this->requireSeller();

        if (!$this->isPost()) {
            redirectWithMessage(APP_URL . '/seller/orders', 'Invalid request', 'error');
        }

        $reason = sanitize($_POST['cancellation_reason'] ?? '');

        if (empty($reason)) {
            redirectWithMessage(APP_URL . '/seller/orders/' . $orderId, 'Cancellation reason is required', 'error');
        }

        $orderModel = new Order();
        $order = $orderModel->findById($orderId);

        if (!$order || $order['seller_id'] != $this->getUserId()) {
            redirectWithMessage(APP_URL . '/seller/orders', 'Order not found', 'error');
        }

        try {
            $orderModel->cancelOrder($orderId, $reason);
            redirectWithMessage(APP_URL . '/seller/orders', 'Order cancelled', 'success');
        } catch (Exception $e) {
            redirectWithMessage(APP_URL . '/seller/orders/' . $orderId, 'Error cancelling order', 'error');
        }
    }
}
?>
