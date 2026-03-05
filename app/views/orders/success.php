<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - E-Commerce Volley</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50">
    <?php $this->component('header'); ?>

    <main class="max-w-7xl mx-auto px-4 py-12">
        <!-- Success Message -->
        <div class="max-w-2xl mx-auto bg-white rounded-lg p-8 text-center mb-8">
            <div class="mb-6">
                <i class="fas fa-check-circle text-6xl text-green-600"></i>
            </div>
            <h1 class="text-4xl font-bold mb-4 text-gray-900">Order Placed Successfully!</h1>
            <p class="text-gray-600 text-lg mb-8">Thank you for your purchase. Your order has been placed and is being processed.</p>
            
            <div class="space-y-4 mb-8">
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="bg-gray-50 p-6 rounded-lg text-left">
                            <p class="font-semibold mb-2">Order #<?php echo $order['order_number']; ?></p>
                            <p class="text-gray-600 text-sm mb-3">Placed on <?php echo formatDate($order['created_at']); ?></p>
                            <p class="text-lg font-bold text-red-600"><?php echo formatCurrency($order['total_amount']); ?></p>
                            <p class="text-sm text-gray-600 mt-2">Status: <span class="font-semibold capitalize"><?php echo $order['status']; ?></span></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="space-x-4">
                <a href="<?php echo APP_URL; ?>/orders" class="btn btn-primary py-3 px-8 inline-block">View My Orders</a>
                <a href="<?php echo APP_URL; ?>" class="btn btn-secondary py-3 px-8 inline-block">Continue Shopping</a>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="max-w-2xl mx-auto bg-white rounded-lg p-8">
            <h2 class="text-2xl font-bold mb-6">What happens next?</h2>
            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="font-bold text-red-600">1</span>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-1">Order Confirmation</h3>
                        <p class="text-gray-600">You'll receive an order confirmation email shortly</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="font-bold text-red-600">2</span>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-1">Seller Confirmation</h3>
                        <p class="text-gray-600">The seller will review and confirm your order</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="font-bold text-red-600">3</span>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-1">Shipping</h3>
                        <p class="text-gray-600">Your items will be shipped to the address provided</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="font-bold text-red-600">4</span>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-1">Delivery</h3>
                        <p class="text-gray-600">Receive your order and leave a review</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php $this->component('footer'); ?>
    <script src="<?php echo APP_URL; ?>/public/js/main.js"></script>
</body>
</html>
