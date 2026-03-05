<?php
// Header Component
?>
<header class="bg-white shadow-md sticky top-0 z-40">
    <script>
        window.APP_URL = "<?php echo APP_URL; ?>";
    </script>
    <nav class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <!-- Logo -->
        <a href="<?php echo APP_URL; ?>" class="flex items-center text-2xl font-bold text-red-600">
            <i class="fas fa-volleyball-ball mr-2" aria-hidden="true"></i>
            Volley
        </a>

        <!-- Navigation Menu -->
        <div class="hidden md:flex gap-6 items-center ml-6">
            <a href="<?php echo APP_URL; ?>" class="text-gray-700 hover:text-red-600 transition">Home</a>
            <a href="<?php echo APP_URL; ?>/categories" class="text-gray-700 hover:text-red-600 transition">Categories</a>
            <a href="<?php echo APP_URL; ?>/products" class="text-gray-700 hover:text-red-600 transition">Products</a>
            <a href="<?php echo APP_URL; ?>/contact" class="text-gray-700 hover:text-red-600 transition">Contact</a>
        </div>

        <!-- Search Bar -->
        <form action="<?php echo APP_URL; ?>/search" method="GET" class="hidden md:flex w-72 mx-8">
            <input type="text" name="q" placeholder="Search products..." class="w-full px-4 py-2 border rounded-l-lg">
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-r-lg hover:bg-red-700">Search</button>
        </form>

        <!-- Right Menu -->
        <div class="flex gap-4 items-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Cart Icon -->
                <a href="<?php echo APP_URL; ?>/cart" class="relative text-gray-700 hover:text-red-600">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span id="cart-count" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                </a>

                <!-- Account Dropdown -->
                <div class="relative" id="account-menu">
                    <div class="flex items-center gap-2 cursor-pointer" id="account-trigger">
                        <?php if ($_SESSION['avatar']): ?>
                            <img src="<?php echo APP_URL; ?>/public/images/<?php echo $_SESSION['avatar']; ?>" alt="avatar" class="w-8 h-8 rounded-full">
                        <?php
    else: ?>
                            <i class="fas fa-user text-xl text-gray-700"></i>
                        <?php
    endif; ?>
                        <span class="text-gray-700"><?php echo $_SESSION['full_name'] ?? $_SESSION['username']; ?></span>
                    </div>

                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden" id="account-dropdown">
                        <a href="<?php echo APP_URL; ?>/account/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                        
                        <?php if ($_SESSION['role'] === 'customer'): ?>
                            <a href="<?php echo APP_URL; ?>/orders" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Orders</a>
                            <a href="<?php echo APP_URL; ?>/account/wishlist" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Wishlist</a>
                            <a href="<?php echo APP_URL; ?>/account/wallet" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Wallet</a>
                        <?php
    elseif ($_SESSION['role'] === 'seller'): ?>
                            <a href="<?php echo APP_URL; ?>/seller/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Seller Dashboard</a>
                            <a href="<?php echo APP_URL; ?>/seller/products" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Products</a>
                            <a href="<?php echo APP_URL; ?>/seller/orders" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Orders</a>
                            <a href="<?php echo APP_URL; ?>/account/wallet" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Wallet</a>
                        <?php
    elseif ($_SESSION['role'] === 'admin'): ?>
                            <a href="<?php echo APP_URL; ?>/admin/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                        <?php
    endif; ?>

                        <a href="<?php echo APP_URL; ?>/logout" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 border-t">Logout</a>
                    </div>
                </div>
            <?php
else: ?>
                <a href="<?php echo APP_URL; ?>/login" class="text-gray-700 hover:text-red-600">Login</a>
                <a href="<?php echo APP_URL; ?>/register" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Register</a>
            <?php
endif; ?>
        </div>
    </nav>
</header>
