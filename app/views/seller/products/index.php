<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - Seller Dashboard</title>
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
                <a href="<?php echo APP_URL; ?>/seller/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg">
                    <i class="fas fa-home w-6"></i> Dashboard
                </a>
                <a href="<?php echo APP_URL; ?>/seller/products" class="block px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium">
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
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">My Products</h1>
                <a href="<?php echo APP_URL; ?>/seller/product-add" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    <i class="fas fa-plus mr-2"></i> Add Product
                </a>
            </div>

            <?php if (hasFlashMessage()):
    $msg = getFlashMessage(); ?>
                <div class="p-4 mb-6 rounded-lg <?php echo $msg['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <?php echo $msg['message']; ?>
                </div>
            <?php
endif; ?>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-700 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">Image</th>
                                <th class="px-6 py-3 text-left font-medium">Name</th>
                                <th class="px-6 py-3 text-left font-medium">Category</th>
                                <th class="px-6 py-3 text-right font-medium">Price</th>
                                <th class="px-6 py-3 text-center font-medium">Stock</th>
                                <th class="px-6 py-3 text-center font-medium">Status</th>
                                <th class="px-6 py-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($data['products'])): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">You haven't added any products yet.</td>
                                </tr>
                            <?php
else:
    foreach ($data['products'] as $product): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <?php if (!empty($product['image'])): ?>
                                            <?php $imageSrc = !empty($product['image']) ? $product['image'] : 'default.png'; ?>
                                            <img src="<?php echo APP_URL; ?>/public/images/<?php echo $imageSrc; ?>" class="w-12 h-12 rounded object-cover" onerror="this.src='<?php echo APP_URL; ?>/public/images/default.png';">
                                        <?php
        else: ?>
                                            <div class="w-12 h-12 bg-gray-200 text-gray-400 rounded flex items-center justify-center">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php
        endif; ?>
                                    </td>
                                    <td class="px-6 py-4 font-medium">
                                        <a href="<?php echo APP_URL; ?>/product/<?php echo $product['id']; ?>" class="text-blue-600 hover:underline" target="_blank">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500"><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <?php if ($product['discount_percentage'] > 0): ?>
                                            <span class="text-sm text-gray-500 line-through block"><?php echo formatCurrency($product['price']); ?></span>
                                            <span class="text-red-600 font-bold"><?php echo formatCurrency($product['price'] * (1 - $product['discount_percentage'] / 100)); ?></span>
                                            <span class="text-xs bg-red-100 text-red-800 px-1 rounded block mt-1">-<?php echo $product['discount_percentage']; ?>%</span>
                                        <?php
        else: ?>
                                            <span class="font-medium"><?php echo formatCurrency($product['price']); ?></span>
                                        <?php
        endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="<?php echo $product['stock_quantity'] > 10 ? 'text-green-600' : ($product['stock_quantity'] > 0 ? 'text-yellow-600' : 'text-red-600'); ?>">
                                            <?php echo $product['stock_quantity']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo $product['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo ucfirst($product['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="<?php echo APP_URL; ?>/seller/product-edit/<?php echo $product['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/seller/product-delete/<?php echo $product['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php
    endforeach;
endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($data['pagination']['total_pages'] > 1): ?>
                <div class="mt-6 flex justify-center">
                    <nav class="flex gap-2">
                        <?php for ($i = 1; $i <= $data['pagination']['total_pages']; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="px-4 py-2 rounded border <?php echo $i === $data['pagination']['current_page'] ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php
    endfor; ?>
                    </nav>
                </div>
            <?php
endif; ?>

        </main>
    </div>
</body>
</html>
