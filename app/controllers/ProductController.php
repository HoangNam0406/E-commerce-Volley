<?php
/**
 * Product Controller
 */
class ProductController extends Controller {

    /**
     * Show all products
     */
    public function index() {
        $productModel = new Product();
        $categoryModel = new Category();

        $page = getCurrentPage();
        $totalProducts = $productModel->countActive();
        $pagination = getPaginationInfo($totalProducts, ITEMS_PER_PAGE);

        $products = $productModel->getActive($pagination['limit'], $pagination['offset']);
        $categories = $categoryModel->getActive();

        $this->render('products/index', [
            'products' => $products,
            'categories' => $categories,
            'pagination' => $pagination
        ]);
    }

    /**
     * Show product detail
     */
    public function detail($productId) {
        $productModel = new Product();
        $reviewModel = new Review();

        $product = $productModel->findById($productId);
        if (!$product || $product['status'] !== 'active') {
            http_response_code(404);
            die('Product not found');
        }

        $images = $productModel->getImages($productId);
        $seller = $productModel->getSeller($productId);
        $reviews = $reviewModel->getProductReviews($productId);
        $averageRating = $reviewModel->getAverageRating($productId);

        // Get related products (same category)
        $relatedProducts = $productModel->getByCategory($product['category_id'], 5);

        $isInWishlist = false;
        if ($this->isAuthenticated()) {
            $wishlistModel = new Wishlist();
            $isInWishlist = $wishlistModel->isInWishlist($this->getUserId(), $productId) !== false;
        }

        $this->render('products/detail', [
            'product' => $product,
            'images' => $images,
            'seller' => $seller,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'relatedProducts' => $relatedProducts,
            'isInWishlist' => $isInWishlist
        ]);
    }

    /**
     * Search products
     */
    public function search() {
        $keyword = sanitize($_GET['q'] ?? '');
        $productModel = new Product();
        $categoryModel = new Category();

        if (empty($keyword)) {
            $this->redirect(APP_URL . '/products');
        }

        $page = getCurrentPage();
        $pagination = getPaginationInfo(PHP_INT_MAX, ITEMS_PER_PAGE);
        $searchResults = $productModel->search($keyword, $pagination['limit'], $pagination['offset']);
        $totalProducts = count($searchResults);
        $pagination = getPaginationInfo($totalProducts, ITEMS_PER_PAGE);

        // Get paginated results
        $products = $searchResults;
        $categories = $categoryModel->getActive();

        $this->render('products/search', [
            'keyword' => $keyword,
            'products' => $products,
            'categories' => $categories,
            'pagination' => $pagination,
            'totalResults' => $totalProducts
        ]);
    }
}
?>
