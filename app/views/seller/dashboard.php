<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - E-Commerce Volley</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php $this->component('header'); ?>

    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md hidden md:block">
            <nav class="p-4 space-y-2">
                <a href="<?php echo APP_URL; ?>/seller/dashboard" class="block px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium">
                    <i class="fas fa-home w-6"></i> Dashboard
                </a>
                <a href="<?php echo APP_URL; ?>/seller/products" class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg">
                    <i class="fas fa-box w-6"></i> My Products
                </a>
                <a href="<?php echo APP_URL; ?>/seller/orders" class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg">
                    <i class="fas fa-shopping-bag w-6"></i> My Orders
                </a>
                <a href="<?php echo APP_URL; ?>/account/wallet" class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg">
                    <i class="fas fa-wallet w-6"></i> Wallet
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Seller Dashboard</h1>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Orders</p>
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo number_format($data['totalOrders']); ?></h3>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-shopping-bag text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Revenue</p>
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo formatCurrency($data['totalRevenue']); ?></h3>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-money-bill-wave text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Active Products</p>
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo number_format($data['totalProducts']); ?></h3>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-box text-yellow-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800">Recent Orders</h2>
                    <a href="<?php echo APP_URL; ?>/seller/orders" class="text-red-600 hover:text-red-800 text-sm font-medium">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-700 text-sm">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">Order #</th>
                                <th class="px-6 py-3 text-left font-medium">Customer</th>
                                <th class="px-6 py-3 text-left font-medium">Date</th>
                                <th class="px-6 py-3 text-center font-medium">Status</th>
                                <th class="px-6 py-3 text-right font-medium">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($data['recentOrders'])): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No recent orders found.</td>
                                </tr>
                            <?php
else:
    foreach ($data['recentOrders'] as $order): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <a href="<?php echo APP_URL; ?>/seller/order-detail/<?php echo $order['id']; ?>" class="text-blue-600 font-medium">
                                            #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td class="px-6 py-4 text-gray-500"><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <?php
        $statusClass = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800'
        ][$order['status']] ?? 'bg-gray-100 text-gray-800';
?>
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium">
                                        <?php echo formatCurrency($order['seller_amount']); ?>
                                    </td>
                                </tr>
                            <?php
    endforeach;
endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
