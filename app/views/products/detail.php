<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - E-Commerce Volley</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50">
    <?php $this->component('header'); ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <?php if (hasFlashMessage()):
    $msg = getFlashMessage(); ?>
            <div class="alert alert-<?php echo $msg['type']; ?> mb-6">
                <?php echo $msg['message']; ?>
            </div>
        <?php
endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <!-- Product Images -->
            <div>
                <div class="bg-white rounded-lg p-4 mb-4">
                    <?php if (!empty($images)): ?>
                        <img id="mainImage" src="<?php echo APP_URL; ?>/public/images/<?php echo $images[0]['image_path']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-96 object-cover rounded-lg" onerror="this.src='<?php echo APP_URL; ?>/public/images/default.png';">
                    <?php
else: ?>
                        <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                    <?php
endif; ?>
                </div>

                <!-- Thumbnails -->
                <?php if (!empty($images)): ?>
                    <div class="grid grid-cols-4 gap-2">
                        <?php foreach ($images as $image): ?>
                            <img src="<?php echo APP_URL; ?>/public/images/<?php echo $image['image_path']; ?>" alt="thumb" class="cursor-pointer border rounded-lg hover:border-red-600" onclick="changeImage('<?php echo APP_URL; ?>/public/images/<?php echo $image['image_path']; ?>')" onerror="this.src='<?php echo APP_URL; ?>/public/images/default.png';">
                        <?php
    endforeach; ?>
                    </div>
                <?php
endif; ?>
            </div>

            <!-- Product Details -->
            <div class="bg-white rounded-lg p-6">
                <h1 class="text-3xl font-bold mb-4"><?php echo $product['name']; ?></h1>

                <!-- Rating -->
                <div class="flex items-center gap-2 mb-6">
                    <div class="flex text-yellow-400">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <i class="<?php echo $i < floor($averageRating) ? 'fas' : 'far'; ?> fa-star"></i>
                        <?php
endfor; ?>
                    </div>
                    <span class="text-gray-600"><?php echo $averageRating; ?> / 5 (<?php echo count($reviews); ?> reviews)</span>
                </div>

                <!-- Price -->
                <div class="mb-6 pb-6 border-b">
                    <?php if ($product['discount_percentage'] > 0 && strtotime($product['discount_start_date']) <= time() && strtotime($product['discount_end_date']) >= time()): ?>
                        <div class="flex items-center gap-4">
                            <?php $discountedPrice = $product['price'] - ($product['price'] * $product['discount_percentage'] / 100); ?>
                            <span class="text-3xl font-bold text-red-600"><?php echo formatCurrency($discountedPrice); ?></span>
                            <del class="text-xl text-gray-500"><?php echo formatCurrency($product['price']); ?></del>
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">-<?php echo $product['discount_percentage']; ?>%</span>
                        </div>
                    <?php
else: ?>
                        <span class="text-3xl font-bold text-red-600"><?php echo formatCurrency($product['price']); ?></span>
                    <?php
endif; ?>
                </div>

                <!-- Stock & Seller -->
                <div class="mb-6 pb-6 border-b">
                    <p class="mb-2"><strong>In Stock:</strong> <span class="<?php echo $product['stock_quantity'] > 0 ? 'text-green-600' : 'text-red-600'; ?>"><?php echo $product['stock_quantity']; ?> units</span></p>
                    <?php if ($seller): ?>
                        <p><strong>Seller:</strong> <a href="#" class="text-red-600"><?php echo $seller['full_name']; ?></a></p>
                    <?php
endif; ?>
                </div>

                <!-- Quantity & Add to Cart -->
                <div class="mb-6 pb-6 border-b">
                    <label class="block font-semibold mb-2">Quantity:</label>
                    <div class="flex items-center gap-4">
                        <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" class="form-control w-20">
                        <?php if ($product['stock_quantity'] > 0): ?>
                            <button onclick="addToCart(<?php echo $product['id']; ?>, parseInt(document.getElementById('quantity').value))" class="btn btn-primary px-6 py-3 flex-1">Add to Cart</button>
                        <?php
else: ?>
                            <button disabled class="btn btn-secondary px-6 py-3 flex-1 opacity-50">Out of Stock</button>
                        <?php
endif; ?>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button onclick="<?php echo $isInWishlist ? "removeFromWishlist(" . $product['id'] . ")" : "addToWishlist(" . $product['id'] . ")"; ?>" class="btn btn-secondary px-4 py-3">
                                <i class="fas fa-heart <?php echo $isInWishlist ? 'text-red-600' : ''; ?>"></i>
                            </button>
                        <?php
else: ?>
                            <button onclick="alert('Please login first')" class="btn btn-secondary px-4 py-3">
                                <i class="fas fa-heart"></i>
                            </button>
                        <?php
endif; ?>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <h3 class="font-bold text-lg mb-2">Description</h3>
                    <p class="text-gray-700"><?php echo nl2br($product['description']); ?></p>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-white rounded-lg p-6 mb-12">
            <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>

            <?php if (!empty($reviews)): ?>
                <div class="space-y-6">
                    <?php foreach ($reviews as $review): ?>
                        <div class="border-b pb-6 last:border-b-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <?php if ($review['avatar']): ?>
                                        <img src="<?php echo APP_URL; ?>/public/images/<?php echo $review['avatar']; ?>" alt="<?php echo $review['full_name']; ?>" class="w-10 h-10 rounded-full">
                                    <?php
        endif; ?>
                                    <div>
                                        <p class="font-semibold"><?php echo $review['full_name']; ?></p>
                                        <p class="text-sm text-gray-500"><?php echo formatDate($review['created_at'], 'M d, Y'); ?></p>
                                    </div>
                                </div>
                                <div class="flex text-yellow-400">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="<?php echo $i < intval($review['rating']) ? 'fas' : 'far'; ?> fa-star"></i>
                                    <?php
        endfor; ?>
                                </div>
                            </div>
                            <p class="text-gray-700"><?php echo $review['comment']; ?></p>
                        </div>
                    <?php
    endforeach; ?>
                </div>
            <?php
else: ?>
                <p class="text-gray-500 text-center">No reviews yet</p>
            <?php
endif; ?>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-6">Related Products</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($relatedProducts as $relatedProduct): ?>
                        <div class="product-card bg-white rounded-lg overflow-hidden">
                            <?php
        $imagePath = !empty($relatedProduct['image_path']) ? $relatedProduct['image_path'] : (!empty($relatedProduct['image']) ? $relatedProduct['image'] : 'default.png');
?>
                            <a href="<?php echo APP_URL; ?>/product/<?php echo $relatedProduct['id']; ?>">
                                <img src="<?php echo APP_URL; ?>/public/images/<?php echo $imagePath; ?>" alt="<?php echo $relatedProduct['name']; ?>" class="product-image" onerror="this.src='<?php echo APP_URL; ?>/public/images/default.png';">
                            </a>
                            <div class="p-4">
                                <h3 class="product-title"><a href="<?php echo APP_URL; ?>/product/<?php echo $relatedProduct['id']; ?>"><?php echo $relatedProduct['name']; ?></a></h3>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="product-price"><?php echo formatCurrency($relatedProduct['price']); ?></span>
                                </div>
                                <button onclick="addToCart(<?php echo $relatedProduct['id']; ?>)" class="w-full btn btn-primary btn-sm">Add to Cart</button>
                            </div>
                        </div>
                    <?php
    endforeach; ?>
                </div>
            </div>
        <?php
endif; ?>
    </main>

    <?php $this->component('footer'); ?>
    
    <script src="<?php echo APP_URL; ?>/public/js/main.js"></script>
    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
        }
    </script>
</body>
</html>
