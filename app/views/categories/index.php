<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <!-- Header -->
    <?php $this->component('header'); ?>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        <h1 class="text-3xl font-bold mb-6">All Categories</h1>
        
        <?php if (empty($categories)): ?>
            <p class="text-gray-500">No categories found.</p>
        <?php
else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($categories as $category): ?>
                    <a href="<?php echo APP_URL; ?>/category/<?php echo $category['id']; ?>" class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <?php if (!empty($category['image'])): ?>
                            <div class="h-48 overflow-hidden bg-gray-100 flex items-center justify-center">
                                <img src="<?php echo APP_URL; ?>/public/images/categories/<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-full h-full object-cover">
                            </div>
                        <?php
        else: ?>
                            <div class="h-48 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-folder text-4xl text-gray-400"></i>
                            </div>
                        <?php
        endif; ?>
                        <div class="p-4">
                            <h2 class="text-xl font-semibold mb-2 text-gray-800"><?php echo htmlspecialchars($category['name']); ?></h2>
                            <?php if (!empty($category['description'])): ?>
                                <p class="text-gray-600 text-sm line-clamp-2"><?php echo htmlspecialchars($category['description']); ?></p>
                            <?php
        endif; ?>
                        </div>
                    </a>
                <?php
    endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="mt-8 flex justify-center">
                    <nav class="flex space-x-2">
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" 
                               class="px-4 py-2 border rounded-md <?php echo $pagination['current_page'] == $i ? 'bg-red-600 text-white' : 'hover:bg-gray-100 text-gray-700'; ?>">
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
    </main>

    <!-- Footer -->
    <?php $this->component('footer'); ?>

    <!-- Scripts -->
    <script src="<?php echo APP_URL; ?>/public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
