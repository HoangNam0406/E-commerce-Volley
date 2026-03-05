<?php
/**
 * Authentication Controller
 */
class AuthController extends Controller
{

    /**
     * Show login page
     */
    public function loginView()
    {
        if ($this->isAuthenticated()) {
            $this->redirect(APP_URL);
        }
        $this->render('auth/login');
    }

    /**
     * Handle login
     */
    public function login()
    {
        if (!$this->isPost()) {
            $this->loginView();
            return;
        }

        // verify CSRF token if provided
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->render('auth/login', [
                'error' => 'Invalid request. Please try again.'
            ]);
            return;
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->render('auth/login', [
                'error' => 'Email and password are required'
            ]);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !$userModel->verifyPassword($password, $user['password'])) {
            $this->render('auth/login', [
                'error' => 'Invalid email or password'
            ]);
            return;
        }

        if ($user['status'] !== 'active') {
            $this->render('auth/login', [
                'error' => 'Your account is inactive'
            ]);
            return;
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['avatar'] = $user['avatar'];

        // Redirect to intended page or home
        $redirect = $_SESSION['redirect_after_login'] ?? APP_URL;
        unset($_SESSION['redirect_after_login']);

        $this->redirect($redirect);
    }

    /**
     * Show register page
     */
    public function registerView()
    {
        if ($this->isAuthenticated()) {
            $this->redirect(APP_URL);
        }
        $this->render('auth/register');
    }

    /**
     * Handle registration
     */
    public function register()
    {
        if (!$this->isPost()) {
            $this->registerView();
            return;
        }

        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $role = sanitize($_POST['role'] ?? 'customer');
        $full_name = sanitize($_POST['full_name'] ?? '');

        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $this->render('auth/register', [
                'error' => 'Username, email, and password are required'
            ]);
            return;
        }

        if ($password !== $password_confirm) {
            $this->render('auth/register', [
                'error' => 'Passwords do not match'
            ]);
            return;
        }

        if (strlen($password) < 6) {
            $this->render('auth/register', [
                'error' => 'Password must be at least 6 characters'
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/register', [
                'error' => 'Invalid email address'
            ]);
            return;
        }

        if (!in_array($role, ['customer', 'seller'])) {
            $this->render('auth/register', [
                'error' => 'Invalid role'
            ]);
            return;
        }

        $userModel = new User();

        // Check if user already exists
        if ($userModel->findByEmail($email)) {
            $this->render('auth/register', [
                'error' => 'Email already registered'
            ]);
            return;
        }

        if ($userModel->findByUsername($username)) {
            $this->render('auth/register', [
                'error' => 'Username already taken'
            ]);
            return;
        }

        // Create user
        try {
            $userModel->register([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'full_name' => $full_name,
                'role' => $role,
                'status' => 'active'
            ]);

            redirectWithMessage(
                APP_URL . '/auth/login',
                'Account created successfully. Please log in.',
                'success'
            );
        }
        catch (Exception $e) {
            $this->render('auth/register', [
                'error' => 'An error occurred during registration'
            ]);
        }
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        // clear session and destroy
        session_unset();
        session_destroy();
        session_start();

        // after logging out, send the user to the login page with a notice
        redirectWithMessage(
            APP_URL . '/auth/login',
            'You have been logged out.',
            'success'
        );
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->render('auth/change-password');
            return;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $newPasswordConfirm = $_POST['new_password_confirm'] ?? '';

        $userModel = new User();
        $user = $userModel->findById($this->getUserId());

        if (!$userModel->verifyPassword($currentPassword, $user['password'])) {
            $this->render('auth/change-password', [
                'error' => 'Current password is incorrect'
            ]);
            return;
        }

        if (strlen($newPassword) < 6) {
            $this->render('auth/change-password', [
                'error' => 'New password must be at least 6 characters'
            ]);
            return;
        }

        if ($newPassword !== $newPasswordConfirm) {
            $this->render('auth/change-password', [
                'error' => 'New passwords do not match'
            ]);
            return;
        }

        $userModel->changePassword($this->getUserId(), $newPassword);

        redirectWithMessage(
            APP_URL . '/account/profile',
            'Password changed successfully',
            'success'
        );
    }
}
?>
