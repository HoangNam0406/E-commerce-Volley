<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
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
                <a href="<?php echo APP_URL; ?>/admin/users" class="block px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium">
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
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Manage Users</h1>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">ID</th>
                                <th class="px-6 py-3 text-left font-medium">Name</th>
                                <th class="px-6 py-3 text-left font-medium">Email</th>
                                <th class="px-6 py-3 text-center font-medium">Role</th>
                                <th class="px-6 py-3 text-center font-medium">Status</th>
                                <th class="px-6 py-3 text-right font-medium">Joined Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($data['users'])): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No users found.</td>
                                </tr>
                            <?php
else:
    foreach ($data['users'] as $user): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">#<?php echo $user['id']; ?></td>
                                    <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : ($user['role'] === 'seller' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'); ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo ucfirst($user['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-500">
                                        <?php echo date('Y-m-d', strtotime($user['created_at'])); ?>
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
