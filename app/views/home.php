<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Volley - Online Shopping</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php $this->component('header'); ?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Flash Messages -->
        <?php if (hasFlashMessage()):
    $msg = getFlashMessage(); ?>
            <div class="alert alert-<?php echo $msg['type']; ?> mb-6">
                <?php echo $msg['message']; ?>
            </div>
        <?php
endif; ?>

        <!-- Content -->
        <?php
// Banner Section
if (!empty($banners)):
?>
            <div class="mb-12">
                <div class="relative h-[500px] bg-gray-200 rounded-lg overflow-hidden">
                    <?php foreach ($banners as $key => $banner): ?>
                        <div class="banner-slide absolute inset-0 <?php echo $key === 0 ? 'active' : ''; ?>" style="display: <?php echo $key === 0 ? 'block' : 'none'; ?>;">
                            <img src="<?php echo APP_URL; ?>/public/images/<?php echo $banner['image_path']; ?>" alt="<?php echo $banner['title']; ?>" class="w-full h-full object-cover">
                            <?php if ($banner['link']): ?>
                                <a href="<?php echo $banner['link']; ?>" class="absolute inset-0"></a>
                            <?php
        endif; ?>
                        </div>
                    <?php
    endforeach; ?>
                </div>
            </div>
        <?php
endif; ?>

        <!-- Categories Section -->
        <?php if (!empty($categories)): ?>
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-6">Categories</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <?php foreach ($categories as $category): ?>
                        <a href="<?php echo APP_URL; ?>/category/<?php echo $category['id']; ?>" class="bg-white p-4 rounded-lg shadow text-center hover:shadow-lg transition">
                            <i class="fas fa-th text-2xl text-red-600 mb-2"></i>
                            <p class="font-semibold text-sm"><?php echo $category['name']; ?></p>
                        </a>
                    <?php
    endforeach; ?>
                </div>
            </div>
        <?php
endif; ?>

        <!-- Bestsellers Section -->
        <?php if (!empty($bestsellers)): ?>
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-6">Bestsellers</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($bestsellers as $product): ?>
                        <div class="product-card bg-white rounded-lg overflow-hidden">
                            <a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>">
                                <?php $imageSrc = !empty($product['image']) ? $product['image'] : 'default.png'; ?>
                                <img src="<?php echo APP_URL; ?>/public/images/<?php echo $imageSrc; ?>" alt="<?php echo $product['name']; ?>" class="product-image" onerror="this.src='<?php echo APP_URL; ?>/public/images/default.png';">
                            </a>
                            <div class="p-4">
                                <h3 class="product-title"><a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a></h3>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="product-price"><?php echo formatCurrency($product['price']); ?></span>
                                    <?php if ($product['discount_percentage'] > 0): ?>
                                        <span class="product-discount">-<?php echo $product['discount_percentage']; ?>%</span>
                                    <?php
        endif; ?>
                                </div>
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" class="w-full btn btn-primary btn-sm">Add to Cart</button>
                            </div> 
                        </div>
                    <?php
    endforeach; ?>
                </div>
            </div>
        <?php
endif; ?>

        <!-- On Sale Section -->
        <?php if (!empty($onSale)): ?>
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-6">Limited Offers</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($onSale as $product): ?>
                        <div class="product-card bg-white rounded-lg overflow-hidden relative">
                            <?php if ($product['discount_percentage'] > 0): ?>
                                <div class="absolute top-2 right-2 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    -<?php echo $product['discount_percentage']; ?>%
                                </div>
                            <?php
        endif; ?>
                            <a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>">
                                <?php $imageSrc = !empty($product['image']) ? $product['image'] : 'default.png'; ?>
                                <img src="<?php echo APP_URL; ?>/public/images/<?php echo $imageSrc; ?>" alt="<?php echo $product['name']; ?>" class="product-image" onerror="this.src='<?php echo APP_URL; ?>/public/images/default.png';">
                            </a>
                            <div class="p-4">
                                <h3 class="product-title"><a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a></h3>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="product-price"><?php echo formatCurrency(Product::getDiscountedPrice($product['price'], $product['discount_percentage'])); ?></span>
                                    <del class="text-gray-500 text-sm"><?php echo formatCurrency($product['price']); ?></del>
                                </div>
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" class="w-full btn btn-primary btn-sm">Add to Cart</button>
                            </div>
                        </div>
                    <?php
    endforeach; ?>
                </div>
            </div>
        <?php
endif; ?>

        <!-- All Products Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6">All Products</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card bg-white rounded-lg overflow-hidden">
                            <a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>">
                                <?php $imageSrc = !empty($product['image']) ? $product['image'] : 'default.png'; ?>
                                <img src="<?php echo APP_URL; ?>/public/images/<?php echo $imageSrc; ?>" alt="<?php echo $product['name']; ?>" class="product-image" onerror="this.src='<?php echo APP_URL; ?>/public/images/default.png';">
                            </a>
                            <div class="p-4">
                                <h3 class="product-title"><a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a></h3>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="product-price"><?php echo formatCurrency($product['price']); ?></span>
                                </div>
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" class="w-full btn btn-primary btn-sm">Add to Cart</button>
                            </div>
                        </div>
                    <?php
    endforeach; ?>
                <?php
else: ?>
                    <p class="col-span-full text-center text-gray-500">No products available</p>
                <?php
endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php
    endfor; ?>
                </div>
            <?php
endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php $this->component('footer'); ?>

    <!-- Scripts -->
    <script src="<?php echo APP_URL; ?>/public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
