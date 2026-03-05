<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - E-Commerce Volley</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body class="bg-gray-50">
    <?php $this->component('header'); ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Checkout</h1>

        <?php if (hasFlashMessage()): 
            $msg = getFlashMessage(); ?>
            <div class="alert alert-<?php echo $msg['type']; ?> mb-6">
                <?php echo $msg['message']; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Shipping Information</h2>
                    
                    <form action="<?php echo APP_URL; ?>/order-place" method="POST" id="checkoutForm">
                        <input type="hidden" name="selected_cart_ids" id="selectedCartIdsInput" value="">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="shipping_name" class="form-control" value="<?php echo $user['full_name'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="shipping_phone" class="form-control" value="<?php echo $user['phone_number'] ?? ''; ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Shipping Address</label>
                            <textarea name="shipping_address" class="form-control" rows="3" required><?php echo $user['address'] ?? ''; ?></textarea>
                        </div>

                        <h2 class="text-xl font-bold my-6">Payment Method</h2>

                        <div class="space-y-3 mb-6">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:border-red-600">
                                <input type="radio" name="payment_method" value="cod" checked class="mr-3">
                                <div>
                                    <p class="font-semibold">Cash on Delivery (COD)</p>
                                    <p class="text-sm text-gray-600">Pay when item is delivered</p>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:border-red-600">
                                <input type="radio" name="payment_method" value="vnpay" class="mr-3">
                                <div>
                                    <p class="font-semibold">VNPay (QR Code)</p>
                                    <p class="text-sm text-gray-600">Pay online via QR code</p>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="w-full btn btn-primary py-3 text-lg">Place Order</button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-white rounded-lg p-6 sticky top-24">
                    <h2 class="text-xl font-bold mb-4">Order Summary</h2>

                    <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
                        <div id="checkoutItemsContainer">
                            <?php 
                            $subtotal = 0;
                            $itemsByGroup = [];
                            
                            // Group items by seller
                            foreach ($cartItems as $item):
                                if (!isset($itemsByGroup[$item['seller_id']])) {
                                    $itemsByGroup[$item['seller_id']] = [];
                                }
                                $itemsByGroup[$item['seller_id']][] = $item;
                            endforeach;
                            
                            // Display items by seller
                            foreach ($itemsByGroup as $sellerId => $items):
                            ?>
                                <div class="pb-4 border-b" data-seller-id="<?php echo $sellerId; ?>">
                                    <p class="font-semibold text-sm text-gray-600 mb-2">Order #<?php echo $sellerId; ?></p>
                                    <?php foreach ($items as $item):
                                        $itemTotal = $item['current_price'] * $item['quantity'];
                                        $subtotal += $itemTotal;
                                    ?>
                                        <div class="flex justify-between text-sm mb-2 checkout-item" data-cart-id="<?php echo $item['cart_id']; ?>" data-price="<?php echo $item['current_price']; ?>" data-quantity="<?php echo $item['quantity']; ?>">
                                            <span><?php echo substr($item['name'], 0, 30); ?>... × <?php echo $item['quantity']; ?></span>
                                            <span><?php echo formatCurrency($itemTotal); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6 border-t pt-4">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span id="checkoutSubtotal"><?php echo formatCurrency($cartTotal); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg border-t pt-3">
                            <span>Total:</span>
                            <span class="text-red-600" id="checkoutTotal"><?php echo formatCurrency($cartTotal); ?></span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 text-center">
                        Platform fee & transaction fee will be calculated after payment
                    </p>
                </div>
            </div>
        </div>
    </main>

    <?php $this->component('footer'); ?>
    <script src="<?php echo APP_URL; ?>/public/js/main.js"></script>
    <script>
        // Initialize checkout with selected items
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Checkout page loaded');
            console.log('APP_URL:', window.APP_URL);
            
            const selectedCartIdsJSON = localStorage.getItem('selectedCartIds');
            console.log('selectedCartIds from localStorage:', selectedCartIdsJSON);
            
            const selectedCartIds = JSON.parse(selectedCartIdsJSON || '[]');
            console.log('Parsed selectedCartIds:', selectedCartIds);
            
            if (selectedCartIds.length === 0) {
                // If no selected items, show error and redirect
                alert('Please select items from your cart');
                window.location.href = window.APP_URL + '/cart';
                return;
            }

            // Update form with selected cart IDs
            document.getElementById('selectedCartIdsInput').value = JSON.stringify(selectedCartIds);

            // Filter checkout items to only show selected ones
            filterCheckoutItems(selectedCartIds);

            // Setup form submission
            document.getElementById('checkoutForm').addEventListener('submit', function(e) {
                // Update the hidden input with current selected cart IDs
                document.getElementById('selectedCartIdsInput').value = JSON.stringify(selectedCartIds);
            });
        });

        /**
         * Filter checkout items to show only selected cart items
         */
        function filterCheckoutItems(selectedCartIds) {
            console.log('filterCheckoutItems called with:', selectedCartIds);
            
            const checkoutItems = document.querySelectorAll('.checkout-item');
            console.log('Found checkout-item elements:', checkoutItems.length);
            
            let selectedTotal = 0;
            const selectedItemCartIds = [];

            checkoutItems.forEach((item, index) => {
                const cartId = parseInt(item.dataset.cartId);
                const isSelected = selectedCartIds.includes(cartId);
                
                console.log(`Item ${index}: cart_id=${cartId}, isSelected=${isSelected}, price=${item.dataset.price}, qty=${item.dataset.quantity}`);
                
                if (!isSelected) {
                    item.classList.add('hidden');
                } else {
                    item.classList.remove('hidden');
                    const price = parseFloat(item.dataset.price);
                    const quantity = parseInt(item.dataset.quantity);
                    selectedTotal += price * quantity;
                    selectedItemCartIds.push(cartId);
                }
            });

            console.log('Selected Total (raw):', selectedTotal);

            // Update summary totals
            const checkoutSubtotal = document.getElementById('checkoutSubtotal');
            const checkoutTotal = document.getElementById('checkoutTotal');
            
            if (checkoutSubtotal && typeof formatCurrency === 'function') {
                const formatted = formatCurrency(selectedTotal);
                console.log('Formatted currency:', formatted);
                checkoutSubtotal.textContent = formatted;
            } else {
                console.log('checkoutSubtotal not found or formatCurrency not defined');
            }
            
            if (checkoutTotal && typeof formatCurrency === 'function') {
                checkoutTotal.textContent = formatCurrency(selectedTotal);
            }

            // Hide empty seller groups
            const sellerGroups = document.querySelectorAll('[data-seller-id]');
            console.log('Seller groups found:', sellerGroups.length);
            
            sellerGroups.forEach(group => {
                const visibleItems = group.querySelectorAll('.checkout-item:not(.hidden)').length;
                console.log('Seller', group.dataset.sellerId, 'has', visibleItems, 'visible items');
                group.style.display = visibleItems > 0 ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>
