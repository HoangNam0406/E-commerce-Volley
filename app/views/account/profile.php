<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - E-Commerce Volley</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50">
    <?php $this->component('header'); ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Sidebar Menu -->
            <aside class="bg-white rounded-lg p-6 h-fit">
                <h2 class="font-bold text-lg mb-4">My Account</h2>
                <nav class="space-y-2">
                    <a href="<?php echo APP_URL; ?>/account-profile" class="block px-4 py-2 rounded hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <a href="<?php echo APP_URL; ?>/orders" class="block px-4 py-2 rounded hover:bg-gray-100">
                        <i class="fas fa-shopping-cart mr-2"></i> My Orders
                    </a>
                    <a href="<?php echo APP_URL; ?>/account-wishlist" class="block px-4 py-2 rounded hover:bg-gray-100">
                        <i class="fas fa-heart mr-2"></i> Wishlist
                    </a>
                    <a href="<?php echo APP_URL; ?>/account-wallet" class="block px-4 py-2 rounded hover:bg-gray-100">
                        <i class="fas fa-wallet mr-2"></i> Wallet
                    </a>
                    <a href="<?php echo APP_URL; ?>/change-password" class="block px-4 py-2 rounded hover:bg-gray-100">
                        <i class="fas fa-key mr-2"></i> Change Password
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="md:col-span-3 bg-white rounded-lg p-6">
                <?php if (hasFlashMessage()): 
                    $msg = getFlashMessage(); ?>
                    <div class="alert alert-<?php echo $msg['type']; ?> mb-6">
                        <?php echo $msg['message']; ?>
                    </div>
                <?php endif; ?>

                <div class="flex items-start justify-between mb-6 pb-6 border-b">
                    <div class="flex items-center gap-4">
                        <div>
                            <?php if ($user['avatar']): ?>
                                <img src="<?php echo APP_URL; ?>/public/images/<?php echo $user['avatar']; ?>" alt="avatar" class="w-24 h-24 rounded-full">
                            <?php else: ?>
                                <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-user text-3xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold"><?php echo $user['full_name'] ?? $user['username']; ?></h1>
                            <p class="text-gray-600"><?php echo $user['email']; ?></p>
                            <span class="inline-block mt-2 px-3 py-1 bg-gray-100 rounded text-sm font-semibold capitalize">
                                <?php echo $user['role']; ?>
                            </span>
                        </div>
                    </div>
                    <button onclick="document.getElementById('avatarUpload').click()" class="btn btn-secondary">Change Avatar</button>
                </div>

                <!-- Profile Form -->
                <form action="<?php echo APP_URL; ?>/account-update" method="POST" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo $user['full_name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone_number" class="form-control" value="<?php echo $user['phone_number'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"><?php echo $user['address'] ?? ''; ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary py-3 px-6">Save Changes</button>
                </form>

                <!-- Hidden Avatar Upload -->
                <input type="file" id="avatarUpload" accept="image/*" style="display: none;">
            </div>
        </div>
    </main>

    <?php $this->component('footer'); ?>
    
    <script src="<?php echo APP_URL; ?>/public/js/main.js"></script>
    <script>
        document.getElementById('avatarUpload').addEventListener('change', function() {
            const formData = new FormData();
            formData.append('avatar', this.files[0]);

            fetch('<?php echo APP_URL; ?>/account-avatar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => showMessage('Error uploading avatar', 'error'));
        });
    </script>
</body>
</html>
