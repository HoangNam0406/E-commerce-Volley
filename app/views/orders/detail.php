<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Detail</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php $this->component('header'); ?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <a href="<?php echo APP_URL; ?>/account/orders" class="text-gray-500 hover:text-red-600 mr-4">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h1>
            </div>
            <div>
                <?php
$statusClass = [
    'pending' => 'bg-yellow-100 text-yellow-800',
    'confirmed' => 'bg-blue-100 text-blue-800',
    'shipped' => 'bg-indigo-100 text-indigo-800',
    'delivered' => 'bg-green-100 text-green-800',
    'cancelled' => 'bg-red-100 text-red-800'
][$order['status']] ?? 'bg-gray-100 text-gray-800';
?>
                <span class="px-3 py-1 text-sm font-medium rounded-full <?php echo $statusClass; ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </div>
        </div>

        <?php if (hasFlashMessage()):
    $msg = getFlashMessage(); ?>
            <div class="p-4 mb-6 rounded-lg <?php echo $msg['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $msg['message']; ?>
            </div>
        <?php
endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Items & Shipping Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h2 class="text-xl font-bold text-gray-800">Order Items</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php foreach ($orderItems as $item): ?>
                                <div class="flex justify-between items-center border-b pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded flex-shrink-0 flex items-center justify-center mr-4">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <p class="text-sm text-gray-500">
                                                <?php echo formatCurrency($item['price']); ?> x <?php echo $item['quantity']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="font-bold text-gray-800">
                                        <?php echo formatCurrency($item['price'] * $item['quantity']); ?>
                                    </div>
                                </div>
                            <?php
endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h2 class="text-xl font-bold text-gray-800">Shipping Details</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Name</p>
                            <p class="font-medium"><?php echo htmlspecialchars($order['shipping_name']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Phone</p>
                            <p class="font-medium"><?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 mb-1">Shipping Address</p>
                            <p class="font-medium"><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Order Date</p>
                            <p class="font-medium"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Payment Method</p>
                            <p class="font-medium"><?php echo ucfirst($order['payment_method']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Panel & Summary -->
            <div class="space-y-6">
                <!-- Actions -->
                <?php if ($order['status'] === 'pending'): ?>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b">
                            <h2 class="text-xl font-bold text-gray-800">Actions</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <button type="button" onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition">
                                <i class="fas fa-times-circle mr-2"></i> Cancel Order
                            </button>
                        </div>
                    </div>

                    <!-- Cancel Modal -->
                    <div id="cancelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3 text-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Order</h3>
                                <div class="mt-2 text-left">
                                    <form action="<?php echo APP_URL; ?>/order-cancel/<?php echo $order['id']; ?>" method="POST">
                                        <label class="block text-sm text-gray-700 mb-2">Reason for cancellation (optional)</label>
                                        <textarea name="cancellation_reason" class="w-full px-3 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500" rows="3"></textarea>
                                        <div class="items-center px-4 py-3 mt-4 flex gap-2 justify-end">
                                            <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-200">
                                                Back
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700">
                                                Cancel Order
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
endif; ?>

                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h2 class="text-xl font-bold text-gray-800">Order Summary</h2>
                    </div>
                    <div class="p-6">
                        <?php 
                        $subtotal = $order['total_amount'] + $order['discount_amount'] + $order['platform_fee'] + $order['transaction_fee'];
                        ?>
                        <div class="flex justify-between mb-3 text-gray-600">
                            <span>Subtotal:</span>
                            <span><?php echo formatCurrency($subtotal); ?></span>
                        </div>
                        
                        <?php if ($order['discount_amount'] > 0): ?>
                        <div class="flex justify-between mb-3 text-red-500">
                            <span>Discount:</span>
                            <span>-<?php echo formatCurrency($order['discount_amount']); ?></span>
                        </div>
                        <?php
endif; ?>

                        <div class="flex justify-between mb-3 text-gray-500 border-t pt-3">
                            <span>Platform Fee (5%):</span>
                            <span>-<?php echo formatCurrency($order['platform_fee']); ?></span>
                        </div>
                        
                        <?php if ($order['transaction_fee'] > 0): ?>
                        <div class="flex justify-between mb-3 text-gray-500">
                            <span>Transaction Fee (1.5%):</span>
                            <span>-<?php echo formatCurrency($order['transaction_fee']); ?></span>
                        </div>
                        <?php
endif; ?>

                        <div class="flex justify-between mt-4 pt-4 border-t border-gray-200">
                            <span class="font-bold text-lg">Total Amount:</span>
                            <span class="font-bold text-lg text-red-600"><?php echo formatCurrency($order['total_amount']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php $this->component('footer'); ?>
</body>
</html>
