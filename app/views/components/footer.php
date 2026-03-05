<?php
// Footer Component
?>
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- About -->
        <div>
            <h3 class="text-lg font-bold mb-4">About Volley</h3>
            <p class="text-gray-400">E-Commerce Volley is a leading multi-vendor platform bringing quality products and reliable sellers together.</p>
        </div>

        <!-- Quick Links -->
        <div>
            <h3 class="text-lg font-bold mb-4">Quick Links</h3>
            <ul class="space-y-2 text-gray-400">
                <li><a href="<?php echo APP_URL; ?>" class="hover:text-red-600">Home</a></li>
                <li><a href="<?php echo APP_URL; ?>/categories" class="hover:text-red-600">Categories</a></li>
                <li><a href="<?php echo APP_URL; ?>/products" class="hover:text-red-600">Products</a></li>
                <li><a href="<?php echo APP_URL; ?>/contact" class="hover:text-red-600">Contact Us</a></li>
            </ul>
        </div>

        <!-- Policies -->
        <div>
            <h3 class="text-lg font-bold mb-4">Policies</h3>
            <ul class="space-y-2 text-gray-400">
                <li><a href="#" class="hover:text-red-600">Privacy Policy</a></li>
                <li><a href="#" class="hover:text-red-600">Terms & Conditions</a></li>
                <li><a href="#" class="hover:text-red-600">Return & Refund</a></li>
                <li><a href="#" class="hover:text-red-600">Shipping & Delivery</a></li>
            </ul>
        </div>

        <!-- Contact -->
        <div>
            <h3 class="text-lg font-bold mb-4">Contact Info</h3>
            <ul class="space-y-2 text-gray-400">
                <li><strong>Email:</strong> contact@volley.com</li>
                <li><strong>Phone:</strong> 0123 456 789</li>
                <li><strong>Address:</strong> 123 Main St, City</li>
                <li class="pt-4 flex gap-4">
                    <a href="#" class="text-red-600 hover:text-white"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-red-600 hover:text-white"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-red-600 hover:text-white"><i class="fab fa-instagram"></i></a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Copyright -->
    <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
        <p>&copy; 2026 E-Commerce Volley. All rights reserved.</p>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTopBtn" class="back-to-top" title="Lên đầu trang">
    <i class="fas fa-arrow-up"></i>
</button>
