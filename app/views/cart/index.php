<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - E-Commerce Volley</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50">
    <?php $this->component('header'); ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>

        <?php if (hasFlashMessage()): 
            $msg = getFlashMessage(); ?>
            <div class="alert alert-<?php echo $msg['type']; ?> mb-6">
                <?php echo $msg['message']; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($cartItems)): ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-4 mb-4 p-4 bg-white rounded-lg border-b">
                        <input type="checkbox" id="selectAllCart" class="w-5 h-5 rounded cursor-pointer" onchange="toggleSelectAll(this)">
                        <label for="selectAllCart" class="cursor-pointer font-semibold">Select All</label>
                    </div>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item bg-white rounded-lg flex items-center gap-4 p-4 mb-4">
                            <input type="checkbox" class="cart-item-checkbox w-5 h-5 rounded cursor-pointer" data-cart-id="<?php echo $item['cart_id']; ?>" data-price="<?php echo $item['current_price']; ?>" data-quantity="<?php echo $item['quantity']; ?>" onchange="updateSelectedTotal()">
                            
                            <img src="<?php echo APP_URL; ?>/public/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="cart-item-image w-24 h-24 object-cover rounded">
                            
                            <div class="cart-item-details flex-1">
                                <h3 class="font-bold text-lg"><a href="<?php echo APP_URL; ?>/product/<?php echo $item['product_id']; ?>"><?php echo $item['name']; ?></a></h3>
                                <p class="text-gray-600 text-sm mb-2">Seller: <?php echo $item['seller_id']; ?></p>
                                <div class="flex items-center gap-4">
                                    <span class="font-bold text-red-600 text-lg"><?php echo formatCurrency($item['current_price']); ?></span>
                                    <div class="flex items-center gap-2">
                                        <button onclick="updateCartQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] - 1; ?>)" class="btn btn-sm bg-gray-200">-</button>
                                        <span class="px-4"><?php echo $item['quantity']; ?></span>
                                        <button onclick="updateCartQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] + 1; ?>)" class="btn btn-sm bg-gray-200">+</button>
                                    </div>
                                    <button onclick="removeFromCart(<?php echo $item['cart_id']; ?>)" class="btn btn-danger btn-sm">Remove</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Cart Summary -->
                <div class="bg-white rounded-lg p-6 h-fit sticky top-20">
                    <h3 class="font-bold text-lg mb-4">Order Summary</h3>
                    <div class="space-y-3 mb-6 border-b pb-4">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span id="selectedSubtotal"><?php echo formatCurrency(0); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total:</span>
                            <span class="text-red-600" id="selectedTotal"><?php echo formatCurrency(0); ?></span>
                        </div>
                        <div class="text-sm text-gray-500">
                            <span id="selectedCount">0</span> item(s) selected
                        </div>
                    </div>
                    <button onclick="proceedToCheckout()" class="w-full btn btn-primary py-3 text-center block mb-3">Proceed to Checkout</button>
                    <a href="<?php echo APP_URL; ?>" class="w-full btn btn-secondary py-3 text-center block">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg p-8 text-center">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-bold mb-4">Your cart is empty</h2>
                <p class="text-gray-600 mb-6">Add some products to your cart to get started!</p>
                <a href="<?php echo APP_URL; ?>/products" class="btn btn-primary py-3 inline-block">Browse Products</a>
            </div>
        <?php endif; ?>
    </main>

    <?php $this->component('footer'); ?>
    <script src="<?php echo APP_URL; ?>/public/js/main.js"></script>
</body>
</html>
