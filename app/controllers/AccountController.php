<?php
/**
 * Account Controller
 */
class AccountController extends Controller
{

    /**
     * Show account profile
     */
    public function profile()
    {
        $this->requireAuth();

        $userModel = new User();
        $user = $userModel->findById($this->getUserId());

        $this->render('account/profile', ['user' => $user]);
    }

    /**
     * Update profile
     */
    public function updateProfile()
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect(APP_URL . '/account/profile');
        }

        $fullName = sanitize($_POST['full_name'] ?? '');
        $phoneNumber = sanitize($_POST['phone_number'] ?? '');
        $address = sanitize($_POST['address'] ?? '');

        $userModel = new User();
        $userModel->updateProfile($this->getUserId(), [
            'full_name' => $fullName,
            'phone_number' => $phoneNumber,
            'address' => $address
        ]);

        // Update session
        $_SESSION['full_name'] = $fullName;

        redirectWithMessage(APP_URL . '/account/profile', 'Profile updated successfully', 'success');
    }

    /**
     * Update avatar
     */
    public function updateAvatar()
    {
        $this->requireAuth();

        if (!isset($_FILES['avatar'])) {
            $this->jsonResponse(['success' => false, 'message' => 'No file uploaded'], 400);
        }

        $uploadResult = uploadFile($_FILES['avatar']);

        if (!$uploadResult['success']) {
            $this->jsonResponse(['success' => false, 'message' => $uploadResult['error']], 400);
        }

        $userModel = new User();
        $userModel->update($this->getUserId(), [
            'avatar' => $uploadResult['filename']
        ]);

        $_SESSION['avatar'] = $uploadResult['filename'];

        $this->jsonResponse([
            'success' => true,
            'message' => 'Avatar updated',
            'filename' => $uploadResult['filename']
        ]);
    }

    /**
     * Show wallet
     */
    public function wallet()
    {
        $this->requireAuth();

        $walletModel = new Wallet();
        $wallet = $walletModel->getByUserId($this->getUserId());

        if (!$wallet) {
            // Create an empty wallet object to prevent array offset errors in the view
            $wallet = ['id' => null, 'balance' => 0];
            $totalTransactions = 0;
            $transactions = []; // Initialize transactions as empty
        }
        else {
            $totalTransactions = count($walletModel->getTransactions($wallet['id']));
        }

        $page = getCurrentPage();
        $pagination = getPaginationInfo($totalTransactions, ITEMS_PER_PAGE);

        // Only fetch transactions if a wallet ID exists
        if ($wallet['id']) {
            $transactions = $walletModel->getTransactions(
                $wallet['id'],
                $pagination['limit'],
                $pagination['offset']
            );
        }
        else {
            $transactions = []; // Ensure transactions is defined even if no wallet ID
        }

        $this->render('account/wallet', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'pagination' => $pagination
        ]);
    }

    /**
     * Show wishlist
     */
    public function wishlist()
    {
        $this->requireAuth();

        $wishlistModel = new Wishlist();

        $page = getCurrentPage();
        $wishlistItems = $wishlistModel->getCustomerWishlist($this->getUserId());
        $totalItems = count($wishlistItems);
        $pagination = getPaginationInfo($totalItems, ITEMS_PER_PAGE);

        // Paginate results
        $wishlistItems = array_slice(
            $wishlistItems,
            $pagination['offset'],
            $pagination['limit']
        );

        $this->render('account/wishlist', [
            'wishlistItems' => $wishlistItems,
            'pagination' => $pagination,
            'totalItems' => $totalItems
        ]);
    }

    /**
     * Add to wishlist (AJAX)
     */
    public function addToWishlist()
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $productId = (int)($_POST['product_id'] ?? 0);

        if ($productId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid product'], 400);
        }

        $productModel = new Product();
        $product = $productModel->findById($productId);

        if (!$product) {
            $this->jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
        }

        $wishlistModel = new Wishlist();
        $wishlistModel->addToWishlist($this->getUserId(), $productId);

        $this->jsonResponse([
            'success' => true,
            'message' => 'Added to wishlist'
        ]);
    }

    /**
     * Remove from wishlist (AJAX)
     */
    public function removeFromWishlist()
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $productId = (int)($_POST['product_id'] ?? 0);

        if ($productId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid product'], 400);
        }

        $wishlistModel = new Wishlist();
        $wishlistModel->removeFromWishlist($this->getUserId(), $productId);

        $this->jsonResponse([
            'success' => true,
            'message' => 'Removed from wishlist'
        ]);
    }
}
?>
