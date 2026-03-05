<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Seller Dashboard</title>
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
                <h1 class="text-2xl font-bold text-gray-800">Edit Product: <?php echo htmlspecialchars($data['product']['name']); ?></h1>
            </div>

            <?php if (hasFlashMessage()):
    $msg = getFlashMessage(); ?>
                <div class="p-4 mb-6 rounded-lg <?php echo $msg['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <?php echo $msg['message']; ?>
                </div>
            <?php
endif; ?>

            <div class="bg-white rounded-lg shadow max-w-3xl p-6">
                <form action="<?php echo APP_URL; ?>/seller/product-update/<?php echo $data['product']['id']; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($data['product']['name']); ?>" required class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select name="category_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                                <?php foreach ($data['categories'] as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $data['product']['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php
endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price (VND) *</label>
                            <input type="number" name="price" value="<?php echo $data['product']['price']; ?>" min="0" step="1000" required class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                            <input type="number" name="stock_quantity" value="<?php echo $data['product']['stock_quantity']; ?>" min="0" required class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount Percentage (0-100)</label>
                            <input type="number" name="discount_percentage" value="<?php echo $data['product']['discount_percentage']; ?>" min="0" max="100" class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>

                        <div class="md:col-span-2 grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Discount Start Date</label>
                                <input type="datetime-local" name="discount_start_date" value="<?php echo $data['product']['discount_start_date'] ? date('Y-m-d\TH:i', strtotime($data['product']['discount_start_date'])) : ''; ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Discount End Date</label>
                                <input type="datetime-local" name="discount_end_date" value="<?php echo $data['product']['discount_end_date'] ? date('Y-m-d\TH:i', strtotime($data['product']['discount_end_date'])) : ''; ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                            <?php if ($data['product']['image']): ?>
                                <div class="mb-3">
                                    <img src="<?php echo APP_URL; ?>/public/images/<?php echo $data['product']['image']; ?>" alt="Current image" class="max-w-xs h-32 object-cover rounded">
                                    <p class="text-sm text-gray-500 mt-2">Current image</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="5" class="w-full px-4 py-2 border rounded-lg focus:ring-red-500 focus:border-red-500"><?php echo htmlspecialchars($data['product']['description'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="pt-4 border-t flex gap-4">
                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">Update Product</button>
                        <a href="<?php echo APP_URL; ?>/seller/products" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
