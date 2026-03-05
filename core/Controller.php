<?php
/**
 * Base Controller Class
 */
abstract class Controller {
    protected $view;

    public function __construct() {
        $this->view = new View();
    }

    /**
     * Render view with data
     */
    protected function render($viewName, $data = []) {
        $this->view->render($viewName, $data);
    }

    /**
     * Redirect to URL
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Check user role
     */
    protected function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    /**
     * Check any role from array
     */
    protected function hasAnyRole($roles) {
        return isset($_SESSION['role']) && in_array($_SESSION['role'], $roles);
    }

    /**
     * Get current user ID
     */
    protected function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user role
     */
    protected function getUserRole() {
        return $_SESSION['role'] ?? null;
    }

    /**
     * Require authentication
     */
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect(APP_URL . '/auth/login');
        }
    }

    /**
     * Require specific role
     */
    protected function requireRole($role) {
        $this->requireAuth();
        if (!$this->hasRole($role)) {
            http_response_code(403);
            die('Access Denied');
        }
    }

    /**
     * Require any role from array
     */
    protected function requireAnyRole($roles) {
        $this->requireAuth();
        if (!$this->hasAnyRole($roles)) {
            http_response_code(403);
            die('Access Denied');
        }
    }

    /**
     * Return JSON response
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get POST data
     */
    protected function getPostData() {
        return $_POST;
    }

    /**
     * Get GET data
     */
    protected function getQueryData() {
        return $_GET;
    }

    /**
     * Validate request method
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function isPut() {
        return $_SERVER['REQUEST_METHOD'] === 'PUT';
    }

    protected function isDelete() {
        return $_SERVER['REQUEST_METHOD'] === 'DELETE';
    }
}
?>
