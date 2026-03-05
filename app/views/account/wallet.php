<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wallet - E-Commerce Volley</title>
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
                        <a href="<?php echo APP_URL; ?>/orders" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">My Orders</a>
                        <a href="<?php echo APP_URL; ?>/account/wishlist" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Wishlist</a>
                    <?php
elseif ($_SESSION['role'] === 'seller'): ?>
                        <a href="<?php echo APP_URL; ?>/seller/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Seller Dashboard</a>
                    <?php
endif; ?>
                    <a href="<?php echo APP_URL; ?>/account/wallet" class="block px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium">My Wallet</a>
                </nav>
            </aside>

            <!-- Wallet Content -->
            <div class="flex-1">
                <h1 class="text-2xl font-bold mb-6 text-gray-800">My E-Wallet</h1>

                <!-- Balance Card -->
                <div class="bg-gradient-to-r from-red-600 to-red-800 rounded-xl shadow-lg p-8 text-white mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-red-100 mb-2">Available Balance</p>
                            <h2 class="text-4xl font-bold"><?php echo formatCurrency($wallet['balance'] ?? 0); ?></h2>
                        </div>
                        <div class="text-6xl text-white opacity-20">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h2 class="text-lg font-bold text-gray-800">Transaction History</h2>
                    </div>

                    <?php if (empty($transactions)): ?>
                        <div class="p-8 text-center text-gray-500">
                            <i class="fas fa-exchange-alt text-4xl mb-4 text-gray-300"></i>
                            <p>No transactions found.</p>
                        </div>
                    <?php
else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead class="bg-gray-50 text-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-medium">Date</th>
                                        <th class="px-6 py-3 text-left font-medium">Type</th>
                                        <th class="px-6 py-3 text-left font-medium">Description</th>
                                        <th class="px-6 py-3 text-right font-medium">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($transactions as $tx): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-gray-500">
                                                <?php echo date('Y-m-d H:i', strtotime($tx['created_at'])); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php
        $types = [
            'deposit' => ['bg-green-100 text-green-800', 'Deposit'],
            'withdrawal' => ['bg-yellow-100 text-yellow-800', 'Withdrawal'],
            'payment' => ['bg-blue-100 text-blue-800', 'Payment'],
            'refund' => ['bg-purple-100 text-purple-800', 'Refund'],
            'commission' => ['bg-indigo-100 text-indigo-800', 'Sale Earning']
        ];
        $typeData = $types[$tx['transaction_type']] ?? ['bg-gray-100 text-gray-800', $tx['transaction_type']];
?>
                                                <span class="px-2 py-1 text-xs rounded-full <?php echo $typeData[0]; ?>">
                                                    <?php echo $typeData[1]; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-700">
                                                <?php echo htmlspecialchars($tx['reference']); ?>
                                            </td>
                                            <td class="px-6 py-4 text-right font-bold <?php echo in_array($tx['transaction_type'], ['withdrawal', 'payment']) ? 'text-red-500' : 'text-green-500'; ?>">
                                                <?php echo in_array($tx['transaction_type'], ['withdrawal', 'payment']) ? '-' : '+'; ?>
                                                <?php echo formatCurrency($tx['amount']); ?>
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
