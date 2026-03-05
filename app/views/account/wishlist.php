<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - E-Commerce Volley</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php $this->component('header'); ?>

    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar for Account -->
            <aside class="w-full md:w-64 bg-white rounded-lg shadow-sm p-4 hidden md:block">
                <nav class="space-y-2">
                    <a href="<?php echo APP_URL; ?>/account/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Profile</a>
                    <a href="<?php echo APP_URL; ?>/orders" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">My Orders</a>
                    <a href="<?php echo APP_URL; ?>/account/wishlist" class="block px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium">Wishlist</a>
                    <a href="<?php echo APP_URL; ?>/account/wallet" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">My Wallet</a>
                </nav>
            </aside>

            <!-- Wishlist Content -->
            <div class="flex-1">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">My Wishlist (<?php echo $data['totalItems']; ?>)</h1>
                </div>

                <?php if (empty($data['wishlistItems'])): ?>
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <i class="fas fa-heart text-6xl text-gray-200 mb-4"></i>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Your wishlist is empty</h2>
                        <p class="text-gray-500 mb-6">Explore our products and find your favorites!</p>
                        <a href="<?php echo APP_URL; ?>/products" class="btn btn-primary px-8">Browse Products</a>
                    </div>
                <?php
else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($data['wishlistItems'] as $product): ?>
                            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden group">
                                <div class="relative aspect-w-4 aspect-h-3">
                                    <a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>">
                                        <?php if (!empty($product['image'])): ?>
                                            <?php $imageSrc = !empty($product['image']) ? $product['image'] : 'default.png'; ?>
                                            <img src="<?php echo APP_URL; ?>/public/images/<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover" onerror="this.src='<?php echo APP_URL; ?>/public/images/default.png';">
                                        <?php
        else: ?>
                                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-image text-4xl text-gray-400"></i>
                                            </div>
                                        <?php
        endif; ?>
                                    </a>
                                    
                                    <!-- Remove from wishlist button -->
                                    <button class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-red-500 hover:bg-red-50" onclick="removeFromWishlist(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    <?php if ($product['discount_percentage'] > 0): ?>
                                        <div class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                                            -<?php echo $product['discount_percentage']; ?>%
                                        </div>
                                    <?php
        endif; ?>
                                </div>
                                <div class="p-4">
                                    <h3 class="text-lg font-bold text-gray-800 mb-2 truncate">
                                        <a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>" class="hover:text-red-600">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </a>
                                    </h3>
                                    
                                    <div class="mt-2 flex items-center justify-between">
                                        <div class="price-container">
                                            <?php if ($product['discount_percentage'] > 0): ?>
                                                <span class="text-sm text-gray-500 line-through"><?php echo formatCurrency($product['price']); ?></span>
                                                <span class="text-lg font-bold text-red-600 ml-2">
                                                    <?php echo formatCurrency($product['price'] * (1 - $product['discount_percentage'] / 100)); ?>
                                                </span>
                                            <?php
        else: ?>
                                                <span class="text-lg font-bold text-gray-800"><?php echo formatCurrency($product['price']); ?></span>
                                            <?php
        endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 flex gap-2">
                                        <?php if ($product['stock_quantity'] > 0): ?>
                                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="w-full bg-black text-white py-2 rounded hover:bg-gray-800 transition">
                                                Add to Cart
                                            </button>
                                        <?php
        else: ?>
                                            <button disabled class="w-full bg-gray-300 text-gray-600 py-2 rounded cursor-not-allowed">
                                                Out of Stock
                                            </button>
                                        <?php
        endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php
    endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['pagination']['total_pages'] > 1): ?>
                        <div class="mt-8 flex justify-center">
                            <nav class="flex gap-2">
                                <?php for ($i = 1; $i <= $data['pagination']['total_pages']; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>" class="px-4 py-2 rounded border <?php echo $i === $data['pagination']['current_page'] ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php
        endfor; ?>
                            </nav>
                        </div>
                    <?php
    endif; ?>
                <?php
endif; ?>
            </div>
        </div>
    </main>

    <?php $this->component('footer'); ?>
    <script src="<?php echo APP_URL; ?>/public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
