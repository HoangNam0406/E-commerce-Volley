<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Commerce Volley</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php $this->component('header'); ?>

    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md hidden md:block">
            <nav class="p-4 space-y-2">
                <a href="<?php echo APP_URL; ?>/admin/dashboard" class="block px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium">
                    <i class="fas fa-home w-6"></i> Dashboard
                </a>
                <a href="<?php echo APP_URL; ?>/admin/users" class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg">
                    <i class="fas fa-users w-6"></i> Users
                </a>
                <a href="<?php echo APP_URL; ?>/admin/categories" class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg">
                    <i class="fas fa-list w-6"></i> Categories
                </a>
                <a href="<?php echo APP_URL; ?>/admin/banners" class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg">
                    <i class="fas fa-images w-6"></i> Banners
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Admin Dashboard</h1>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Users</p>
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo number_format($data['totalUsers']); ?></h3>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-users text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Orders</p>
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo number_format($data['totalOrders']); ?></h3>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-shopping-bag text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Revenue</p>
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo formatCurrency($data['totalRevenue']); ?></h3>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-money-bill-wave text-purple-500 text-xl"></i>
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
                
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-pink-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Customers</p>
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo number_format($data['totalCustomers']); ?></h3>
                        </div>
                        <div class="p-3 bg-pink-100 rounded-full">
                            <i class="fas fa-user text-pink-500 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-teal-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Sellers</p>
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo number_format($data['totalSellers']); ?></h3>
                        </div>
                        <div class="p-3 bg-teal-100 rounded-full">
                            <i class="fas fa-store text-teal-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- More Dashboard Content here like charts if needed -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                <div class="flex gap-4">
                    <a href="<?php echo APP_URL; ?>/admin/users" class="btn btn-outline">Manage Users</a>
                    <a href="<?php echo APP_URL; ?>/admin/categories" class="btn btn-outline">Manage Categories</a>
                </div>
            </div>

        </main>
    </div>

    <script src="<?php echo APP_URL; ?>/public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
