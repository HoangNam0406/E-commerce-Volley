<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - E-Commerce Volley</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50">
    <?php $this->component('header'); ?>

    <main class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold text-center mb-8 text-gray-900">Create Account</h1>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error mb-6">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo APP_URL; ?>/register" method="POST" class="space-y-4">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirm" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Register As</label>
                        <select name="role" class="form-control" required>
                            <option value="customer">Customer</option>
                            <option value="seller">Seller</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full btn btn-primary py-3">Create Account</button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Already have an account? 
                        <a href="<?php echo APP_URL; ?>/login" class="text-red-600 font-semibold hover:text-red-700">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <?php $this->component('footer'); ?>
    <script src="<?php echo APP_URL; ?>/public/js/main.js"></script>
</body>
</html>
