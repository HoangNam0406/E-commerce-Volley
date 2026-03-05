<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title>Login - E-Commerce Volley</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50">
    <?php $this->component('header'); ?>

    <main class="min-h-screen flex items-center justify-center py-12 px-4">
        <?php $error = $error ?? ''; ?>
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold text-center mb-8 text-gray-900">Login</h1>

                <?php if (hasFlashMessage()):
    $msg = getFlashMessage(); ?>
                    <div class="alert alert-<?php echo $msg['type']; ?> mb-6">
                        <?php echo $msg['message']; ?>
                    </div>
                <?php
endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error mb-6">
                        <?php echo $error; ?>
                    </div>
                <?php
endif; ?>

                <form action="<?php echo APP_URL; ?>/login" method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="w-full btn btn-primary py-3">Login</button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Don't have an account? 
                        <a href="<?php echo APP_URL; ?>/register" class="text-red-600 font-semibold hover:text-red-700">Register here</a>
                    </p>
                </div>

                <!-- Demo Account Info -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-700 font-semibold mb-2">Demo Account:</p>
                    <p class="text-sm text-blue-600">Email: admin@gmail.com</p>
                    <p class="text-sm text-blue-600">Password: admin123</p>
                </div>
            </div>
        </div>
    </main>

    <?php $this->component('footer'); ?>
    <script src="<?php echo APP_URL; ?>/public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
