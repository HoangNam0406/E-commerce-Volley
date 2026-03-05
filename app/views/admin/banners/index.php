<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banners - Admin Dashboard</title>
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
                <a href="<?php echo APP_URL; ?>/admin/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg">
                    <i class="fas fa-home w-6"></i> Dashboard
                </a>
                <a href="<?php echo APP_URL; ?>/admin/users" class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg">
                    <i class="fas fa-users w-6"></i> Users
                </a>
                <a href="<?php echo APP_URL; ?>/admin/categories" class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg">
                    <i class="fas fa-list w-6"></i> Categories
                </a>
                <a href="<?php echo APP_URL; ?>/admin/banners" class="block px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium">
                    <i class="fas fa-images w-6"></i> Banners
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Manage Banners</h1>
                <a href="<?php echo APP_URL; ?>/admin/banner-add" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    <i class="fas fa-plus mr-2"></i> Add Banner
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
                                <th class="px-6 py-3 text-left font-medium">Title</th>
                                <th class="px-6 py-3 text-left font-medium">Link</th>
                                <th class="px-6 py-3 text-center font-medium">Position</th>
                                <th class="px-6 py-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($data['banners'])): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No banners found.</td>
                                </tr>
                            <?php
else:
    foreach ($data['banners'] as $banner): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <img src="<?php echo APP_URL; ?>/public/images/<?php echo $banner['image_path']; ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>" class="h-16 w-32 object-cover rounded">
                                    </td>
                                    <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($banner['title']); ?></td>
                                    <td class="px-6 py-4 text-gray-500 text-sm"><?php echo htmlspecialchars($banner['link']); ?></td>
                                    <td class="px-6 py-4 text-center"><?php echo $banner['position']; ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="<?php echo APP_URL; ?>/admin/banner-edit/<?php echo $banner['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/admin/banner-delete/<?php echo $banner['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this banner?')">
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
            
            <!-- Pagination logic here if needed -->
        </main>
    </div>
</body>
</html>
