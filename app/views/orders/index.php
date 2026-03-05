<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?php echo APP_NAME; ?></title>
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
                    <?php if ($_SESSION['role'] === 'customer'): ?>
                        <a href="<?php echo APP_URL; ?>/orders" class="block px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium">My Orders</a>
                        <a href="<?php echo APP_URL; ?>/account/wishlist" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Wishlist</a>
                    <?php
elseif ($_SESSION['role'] === 'seller'): ?>
                        <a href="<?php echo APP_URL; ?>/seller/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Seller Dashboard</a>
                        <a href="<?php echo APP_URL; ?>/orders" class="block px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium">My Orders</a>
                    <?php
endif; ?>
                    <a href="<?php echo APP_URL; ?>/account/wallet" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">My Wallet</a>
                </nav>
            </aside>

            <!-- Orders Content -->
            <div class="flex-1">
                <h1 class="text-2xl font-bold mb-6 text-gray-800">My Orders</h1>

                <!-- Orders List -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <?php if (empty($orders)): ?>
                        <div class="p-8 text-center text-gray-500">
                            <i class="fas fa-box-open text-4xl mb-4 text-gray-300"></i>
                            <p>You have not placed any orders yet.</p>
                            <a href="<?php echo APP_URL; ?>/products" class="mt-4 inline-block text-red-600 font-medium hover:text-red-700">Start Shopping</a>
                        </div>
                    <?php
else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead class="bg-gray-50 text-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-medium">Order ID</th>
                                        <th class="px-6 py-3 text-left font-medium">Date</th>
                                        <th class="px-6 py-3 text-left font-medium">Total</th>
                                        <th class="px-6 py-3 text-left font-medium">Status</th>
                                        <th class="px-6 py-3 text-center font-medium">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($orders as $order): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 font-medium text-gray-900">
                                                #<?php echo $order['id']; ?>
                                            </td>
                                            <td class="px-6 py-4 text-gray-500">
                                                <?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?>
                                            </td>
                                            <td class="px-6 py-4 text-gray-700 font-bold">
                                                <?php echo formatCurrency($order['total_amount']); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php
        $statusClasses = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800'
        ];
        $class = $statusClasses[$order['status']] ?? 'bg-gray-100 text-gray-800';
?>
                                                <span class="px-2 py-1 text-xs rounded-full <?php echo $class; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <a href="<?php echo APP_URL; ?>/order-detail/<?php echo $order['id']; ?>" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php
    endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($pagination['total_pages'] > 1): ?>
                            <div class="px-6 py-4 flex justify-center border-t">
                                <nav class="flex gap-2">
                                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>" class="px-4 py-2 rounded border <?php echo $i === $pagination['current_page'] ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>">
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
        </div>
    </main>

    <?php $this->component('footer'); ?>
    <script src="<?php echo APP_URL; ?>/public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
