<?php
/**
 * Admin Dashboard Controller
 */
class AdminDashboardController extends Controller
{

    /**
     * Check admin access
     */
    private function requireAdmin()
    {
        $this->requireRole('admin');
    }

    /**
     * Show dashboard
     */
    public function index()
    {
        $this->requireAdmin();

        $userModel = new User();
        $orderModel = new Order();
        $productModel = new Product();

        $totalUsers = $userModel->count();
        $totalCustomers = $userModel->countCustomers();
        $totalSellers = $userModel->countSellers();
        $totalOrders = $orderModel->count();
        $totalProducts = $productModel->countActive();
        $totalRevenue = $orderModel->getTotalRevenue();

        $securityLogModel = new SecurityLog();
        $totalAnalyzed = $securityLogModel->getTotalAnalyzed();
        $totalBlocked = $securityLogModel->getTotalBlocked();
        $recentBlocks = $securityLogModel->getRecentBlocks(5);

        $this->render('admin/dashboard', [
            'totalUsers' => $totalUsers,
            'totalCustomers' => $totalCustomers,
            'totalSellers' => $totalSellers,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalRevenue' => $totalRevenue,
            'totalAnalyzed' => $totalAnalyzed,
            'totalBlocked' => $totalBlocked,
            'recentBlocks' => $recentBlocks
        ]);
    }

    /**
     * Show users management
     */
    public function users()
    {
        $this->requireAdmin();

        $userModel = new User();
        $page = getCurrentPage();

        $totalUsers = $userModel->count();
        $pagination = getPaginationInfo($totalUsers, ITEMS_PER_PAGE);

        $users = $userModel->getAll($pagination['limit'], $pagination['offset']);

        $this->render('admin/users/index', [
            'users' => $users,
            'pagination' => $pagination
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser($userId)
    {
        $this->requireAdmin();

        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user) {
            redirectWithMessage(APP_URL . '/admin/users', 'User not found', 'error');
        }

        // Prevent admin from deleting themselves
        if ($user['id'] === $_SESSION['user_id']) {
            redirectWithMessage(APP_URL . '/admin/users', 'You cannot delete your own account', 'error');
        }

        $userModel->delete($userId);

        redirectWithMessage(APP_URL . '/admin/users', 'User deleted successfully', 'success');
    }

    /**
     * Show categories management
     */
    public function categories()
    {
        $this->requireAdmin();

        $categoryModel = new Category();
        $page = getCurrentPage();

        $totalCategories = $categoryModel->count();
        $pagination = getPaginationInfo($totalCategories, ITEMS_PER_PAGE);

        $categories = $categoryModel->getAll($pagination['limit'], $pagination['offset']);

        $this->render('admin/categories/index', [
            'categories' => $categories,
            'pagination' => $pagination
        ]);
    }

    /**
     * Add category
     */
    public function addCategory()
    {
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->render('admin/categories/add');
            return;
        }

        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        if (empty($name)) {
            redirectWithMessage(APP_URL . '/admin/categories/add', 'Category name is required', 'error');
        }

        $categoryModel = new Category();
        $categoryModel->create([
            'name' => $name,
            'description' => $description,
            'slug' => $slug,
            'status' => 'active'
        ]);

        redirectWithMessage(APP_URL . '/admin/categories', 'Category added successfully', 'success');
    }

    /**
     * Edit category
     */
    public function editCategory($categoryId)
    {
        $this->requireAdmin();

        $categoryModel = new Category();
        $category = $categoryModel->findById($categoryId);

        if (!$category) {
            redirectWithMessage(APP_URL . '/admin/categories', 'Category not found', 'error');
        }

        if (!$this->isPost()) {
            $this->render('admin/categories/edit', ['category' => $category]);
            return;
        }

        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');

        if (empty($name)) {
            redirectWithMessage(APP_URL . '/admin/categories/edit/' . $categoryId, 'Category name is required', 'error');
        }

        $categoryModel->update($categoryId, [
            'name' => $name,
            'description' => $description
        ]);

        redirectWithMessage(APP_URL . '/admin/categories', 'Category updated successfully', 'success');
    }

    /**
     * Delete category
     */
    public function deleteCategory($categoryId)
    {
        $this->requireAdmin();

        $categoryModel = new Category();
        $categoryModel->delete($categoryId);

        redirectWithMessage(APP_URL . '/admin/categories', 'Category deleted successfully', 'success');
    }

    /**
     * Show banners management
     */
    public function banners()
    {
        $this->requireAdmin();

        $bannerModel = new Banner();
        $page = getCurrentPage();

        $totalBanners = $bannerModel->count();
        $pagination = getPaginationInfo($totalBanners, ITEMS_PER_PAGE);

        $banners = $bannerModel->getAll($pagination['limit'], $pagination['offset']);

        $this->render('admin/banners/index', [
            'banners' => $banners,
            'pagination' => $pagination
        ]);
    }

    /**
     * Add banner
     */
    public function addBanner()
    {
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->render('admin/banners/add');
            return;
        }

        $title = sanitize($_POST['title'] ?? '');
        $link = sanitize($_POST['link'] ?? '');
        $position = (int)($_POST['position'] ?? 0);

        if (!isset($_FILES['image']) || $_FILES['image']['error'] == UPLOAD_ERR_NO_FILE) {
            redirectWithMessage(APP_URL . '/admin/banner-add', 'Banner image is required', 'error');
        }

        $uploadResult = uploadFile($_FILES['image']);

        if (!$uploadResult['success']) {
            redirectWithMessage(APP_URL . '/admin/banner-add', $uploadResult['error'], 'error');
        }

        $bannerModel = new Banner();
        $bannerModel->create([
            'title' => $title,
            'image_path' => $uploadResult['filename'],
            'link' => $link,
            'position' => $position,
            'is_active' => true
        ]);

        redirectWithMessage(APP_URL . '/admin/banners', 'Banner added successfully', 'success');
    }

    /**
     * Edit banner
     */
    public function editBanner($bannerId)
    {
        $this->requireAdmin();

        $bannerModel = new Banner();
        $banner = $bannerModel->findById($bannerId);

        if (!$banner) {
            redirectWithMessage(APP_URL . '/admin/banners', 'Banner not found', 'error');
        }

        if (!$this->isPost()) {
            $this->render('admin/banners/edit', ['banner' => $banner]);
            return;
        }

        $title = sanitize($_POST['title'] ?? '');
        $link = sanitize($_POST['link'] ?? '');
        $position = (int)($_POST['position'] ?? 0);

        $updateData = [
            'title' => $title,
            'link' => $link,
            'position' => $position
        ];

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $uploadResult = uploadFile($_FILES['image']);
            if ($uploadResult['success']) {
                $updateData['image_path'] = $uploadResult['filename'];
            }
        }

        $bannerModel->update($bannerId, $updateData);

        redirectWithMessage(APP_URL . '/admin/banners', 'Banner updated successfully', 'success');
    }

    /**
     * Delete banner
     */
    public function deleteBanner($bannerId)
    {
        $this->requireAdmin();

        $bannerModel = new Banner();
        $bannerModel->delete($bannerId);

        redirectWithMessage(APP_URL . '/admin/banners', 'Banner deleted successfully', 'success');
    }
}
?>
