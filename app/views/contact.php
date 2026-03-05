<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <!-- Header -->
    <?php $this->component('header'); ?>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        <!-- Flash Messages -->
        <?php if (hasFlashMessage()):
    $msg = getFlashMessage(); ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $msg['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
                <?php echo $msg['message']; ?>
            </div>
        <?php
endif; ?>

        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-center mb-8">Contact Us</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Contact Information -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h2 class="text-xl font-bold mb-6 border-b pb-2">Get in Touch</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">Our Location</h3>
                                <p class="text-gray-600">123 Main Street, City<br>Country, 12345</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">Phone Number</h3>
                                <p class="text-gray-600">0123 456 789</p>
                                <p class="text-gray-600">Mon-Fri, 9am-6pm</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">Email Address</h3>
                                <p class="text-gray-600">contact@volley.com</p>
                                <p class="text-gray-600">support@volley.com</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h2 class="text-xl font-bold mb-6 border-b pb-2">Send us a Message</h2>
                    
                    <form action="<?php echo APP_URL; ?>/contact-submit" method="POST" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                            <input type="text" id="name" name="name" required class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" id="subject" name="subject" required class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea id="message" name="message" rows="5" required class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-600 resize-none"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 rounded-md hover:bg-red-700 transition">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php $this->component('footer'); ?>

    <!-- Scripts -->
    <script src="<?php echo APP_URL; ?>/public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
