<?php
/**
 * Category Controller
 */
class CategoryController extends Controller {

    /**
     * Show categories page
     */
    public function index() {
        $categoryModel = new Category();
        
        $page = getCurrentPage();
        $totalCategories = $categoryModel->count(['status' => 'active']);
        $pagination = getPaginationInfo($totalCategories, ITEMS_PER_PAGE);
        
        $categories = $categoryModel->getActive($pagination['limit'], $pagination['offset']);

        $this->render('categories/index', [
            'categories' => $categories,
            'pagination' => $pagination
        ]);
    }

    /**
     * Show category products
     */
    public function view($categoryId) {
        $categoryModel = new Category();
        $productModel = new Product();

        $category = $categoryModel->findById($categoryId);
        if (!$category) {
            http_response_code(404);
            die('Category not found');
        }

        $page = getCurrentPage();
        $totalProducts = $productModel->count([
            'category_id' => $categoryId,
            'status' => 'active'
        ]);
        $pagination = getPaginationInfo($totalProducts, ITEMS_PER_PAGE);

        $products = $productModel->getByCategory($categoryId, $pagination['limit'], $pagination['offset']);

        $this->render('categories/view', [
            'category' => $category,
            'products' => $products,
            'pagination' => $pagination
        ]);
    }
}
?>
