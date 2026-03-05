<?php
/**
 * Home Controller
 */
class HomeController extends Controller {

    /**
     * Show home page
     */
    public function index() {
        $bannerModel = new Banner();
        $categoryModel = new Category();
        $productModel = new Product();

        $banners = $bannerModel->getActive();
        $categories = $categoryModel->getActive();
        $bestsellers = $productModel->getBestsellers(8);
        $onSale = $productModel->getOnSale(8);

        // Get paginated all products
        $page = getCurrentPage();
        $totalProducts = $productModel->countActive();
        $pagination = getPaginationInfo($totalProducts, ITEMS_PER_PAGE);
        $products = $productModel->getActive($pagination['limit'], $pagination['offset']);

        $this->render('home', [
            'banners' => $banners,
            'categories' => $categories,
            'bestsellers' => $bestsellers,
            'onSale' => $onSale,
            'products' => $products,
            'pagination' => $pagination
        ]);
    }

    /**
     * Show contact page
     */
    public function contact() {
        $this->render('contact');
    }

    /**
     * Handle contact form
     */
    public function submitContact() {
        if (!$this->isPost()) {
            $this->contact();
            return;
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message = sanitize($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            redirectWithMessage(APP_URL . '/contact', 'All fields are required', 'error');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectWithMessage(APP_URL . '/contact', 'Invalid email address', 'error');
        }

        // TODO: Send email
        // For now, just redirect with success message

        redirectWithMessage(APP_URL . '/contact', 'Message sent successfully', 'success');
    }
}
?>
