<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <!-- Header -->
    <?php $this->component('header'); ?>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar / Categories -->
            <aside class="w-full md:w-1/4">
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="font-bold text-lg mb-4 border-b pb-2">Categories</h3>
                    <ul class="space-y-2">
                        <li><a href="<?php echo APP_URL; ?>/products" class="text-gray-600 hover:text-red-600 transition">All Products</a></li>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a href="<?php echo APP_URL; ?>/category/<?php echo $category['id']; ?>" class="text-gray-600 hover:text-red-600 transition">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </a>
                                </li>
                            <?php
    endforeach; ?>
                        <?php
endif; ?>
                    </ul>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="w-full md:w-3/4">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold mb-2">Search Results</h1>
                    <p class="text-gray-600">
                        Found <strong><?php echo $totalResults; ?></strong> result(s) for "<strong><?php echo htmlspecialchars($keyword); ?></strong>"
                    </p>
                </div>

                <?php if (!empty($products)): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden flex flex-col h-full">
                                <a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>" class="block relative aspect-square">
                                    <?php if ($product['discount_percentage'] > 0): ?>
                                        <div class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 rounded text-xs font-bold z-10">
                                            -<?php echo $product['discount_percentage']; ?>%
                                        </div>
                                    <?php
        endif; ?>
                                    <?php
        $imageSrc = !empty($product['image']) ? $product['image'] : 'default.png';
?>
                                    <img src="<?php echo APP_URL; ?>/public/images/<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover" onerror="this.src='<?php echo APP_URL; ?>/public/images/default.png';">
                                </a>
                                <div class="p-4 flex flex-col flex-grow">
                                    <h3 class="font-semibold text-gray-800 mb-2 truncate">
                                        <a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                    </h3>
                                    <div class="mt-auto">
                                        <div class="flex items-center gap-2 mb-3">
                                            <?php if ($product['discount_percentage'] > 0): ?>
                                                <span class="text-lg font-bold text-red-600"><?php echo formatCurrency(Product::getDiscountedPrice($product['price'], $product['discount_percentage'])); ?></span>
                                                <del class="text-sm text-gray-400"><?php echo formatCurrency($product['price']); ?></del>
                                            <?php
        else: ?>
                                                <span class="text-lg font-bold text-red-600"><?php echo formatCurrency($product['price']); ?></span>
                                            <?php
        endif; ?>
                                        </div>
                                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php
    endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
                        <div class="flex justify-center mt-8 gap-2">
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <a href="?q=<?php echo urlencode($keyword); ?>&page=<?php echo $i; ?>" class="px-4 py-2 border rounded <?php echo $i === $pagination['current_page'] ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-600 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php
        endfor; ?>
                        </div>
                    <?php
    endif; ?>
                <?php
else: ?>
                    <div class="bg-white p-8 rounded-lg text-center">
                        <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                        <h2 class="text-xl font-semibold text-gray-700 mb-2">No products found</h2>
                        <p class="text-gray-500 mb-4">We couldn't find any products matching "<strong><?php echo htmlspecialchars($keyword); ?></strong>"</p>
                        <a href="<?php echo APP_URL; ?>/products" class="inline-block bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                            View All Products
                        </a>
                    </div>
                <?php
endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php $this->component('footer'); ?>

    <script src="<?php echo APP_URL; ?>/public/js/main.js"></script>
</body>
</html>
