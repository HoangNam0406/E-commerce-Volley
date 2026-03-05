<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <!-- Header -->
    <?php $this->component('header'); ?>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        <!-- Category Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8 flex flex-col md:flex-row items-center md:items-start gap-6">
            <?php if (!empty($category['image'])): ?>
                <div class="w-32 h-32 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                    <img src="<?php echo APP_URL; ?>/public/images/categories/<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-full h-full object-cover">
                </div>
            <?php
else: ?>
                <div class="w-32 h-32 flex-shrink-0 bg-gray-200 rounded-lg flex items-center justify-center">
                    <i class="fas fa-folder-open text-5xl text-gray-400"></i>
                </div>
            <?php
endif; ?>
            
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($category['name']); ?></h1>
                <?php if (!empty($category['description'])): ?>
                    <p class="text-gray-600 max-w-3xl"><?php echo nl2br(htmlspecialchars($category['description'])); ?></p>
                <?php
endif; ?>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Products in <?php echo htmlspecialchars($category['name']); ?></h2>
            
            <?php if (empty($products)): ?>
                <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <p class="text-xl text-gray-500">No products found in this category.</p>
                    <a href="<?php echo APP_URL; ?>/categories" class="mt-4 inline-block text-red-600 hover:text-red-700 font-medium">Browse other categories</a>
                </div>
            <?php
else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                            <a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>" class="block relative pt-[100%] bg-gray-100">
                                <?php
        $imageSrc = !empty($product['image']) ? $product['image'] : 'default.png';
?>
                                <img src="<?php echo APP_URL; ?>/public/images/<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="absolute inset-0 w-full h-full object-cover" onerror="this.src='<?php echo APP_URL; ?>/public/images/default.png'; this.onerror=null;">
                                
                                <?php if ($product['discount_percentage'] > 0): ?>
                                    <div class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 rounded text-xs font-bold">
                                        -<?php echo $product['discount_percentage']; ?>%
                                    </div>
                                <?php
        endif; ?>
                            </a>
                            <div class="p-4">
                                <h3 class="font-medium text-gray-900 mb-1 truncate" title="<?php echo htmlspecialchars($product['name']); ?>">
                                    <a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>" class="hover:text-red-600 transition"><?php echo htmlspecialchars($product['name']); ?></a>
                                </h3>
                                
                                <div class="flex items-center justify-between mt-3">
                                    <div class="flex flex-col">
                                        <?php if ($product['discount_percentage'] > 0): ?>
                                            <span class="text-red-600 font-bold"><?php echo formatCurrency(Product::getDiscountedPrice($product['price'], $product['discount_percentage'])); ?></span>
                                            <span class="text-gray-400 text-sm line-through"><?php echo formatCurrency($product['price']); ?></span>
                                        <?php
        else: ?>
                                            <span class="text-red-600 font-bold"><?php echo formatCurrency($product['price']); ?></span>
                                        <?php
        endif; ?>
                                    </div>
                                </div>
                                
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" class="w-full mt-4 bg-red-600 text-white py-2 rounded-md hover:bg-red-700 transition flex items-center justify-center gap-2">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php
    endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="mt-8 flex justify-center">
                        <nav class="flex space-x-2">
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <a href="?page=<?php echo $i; ?>" 
                                   class="px-4 py-2 border rounded-md <?php echo $pagination['current_page'] == $i ? 'bg-red-600 text-white border-red-600' : 'bg-white hover:bg-gray-100 text-gray-700'; ?>">
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
    </main>

    <!-- Footer -->
    <?php $this->component('footer'); ?>

    <!-- Scripts -->
    <script src="<?php echo APP_URL; ?>/public/js/main.js?v=<?php echo time(); ?>"></script>
    <script>
        // Simple cart addition function for category view
        function addToCart(productId) {
            window.location.href = "<?php echo APP_URL; ?>/cart-add/" + productId;
        }
    </script>
</body>
</html>
