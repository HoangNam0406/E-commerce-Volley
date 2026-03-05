<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Seller Dashboard</title>
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
            <div class="flex items-center mb-6">
                <a href="<?php echo APP_URL; ?>/seller/products" class="text-gray-500 hover:text-red-600 mr-4">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Add New Product</h1>
            </div>

            <?php if (hasFlashMessage()):
    $msg = getFlashMessage(); ?>
                <div class="p-4 mb-6 rounded-lg <?php echo $msg['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <?php echo $msg['message']; ?>
                </div>
            <?php
endif; ?>

            <div class="bg-white rounded-lg shadow max-w-3xl p-6">
                <form action="<?php echo APP_URL; ?>/seller/product-create" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select name="category_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                                <option value="">Select a category</option>
                                <?php foreach ($data['categories'] as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php
endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price (VND) *</label>
                            <input type="number" name="price" min="0" step="1000" required class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                            <input type="number" name="stock_quantity" min="0" required class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="5" class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>

                    <div class="pt-4 border-t">
                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">Add Product</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
