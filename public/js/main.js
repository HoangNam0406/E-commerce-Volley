// Header functionality
document.addEventListener('DOMContentLoaded', function () {
    // Initialize cart count
    updateCartCount();

    // Setup AJAX headers
    setupAjax();

    // Handle form validations
    setupFormValidations();

    // Banner auto-rotation (3s)
    setupBannerRotation();
});

/**
 * Setup Banner Rotation
 */
function setupBannerRotation() {
    const banners = document.querySelectorAll('.banner-slide');
    if (banners.length > 1) {
        let currentIdx = 0;
        setInterval(() => {
            // Fade out current
            banners[currentIdx].style.display = 'none';
            banners[currentIdx].classList.remove('active');

            // Increment index
            currentIdx = (currentIdx + 1) % banners.length;

            // Fade in next
            banners[currentIdx].style.display = 'block';
            banners[currentIdx].classList.add('active');
        }, 3000);
    }
}

/**
 * Update cart count in header
 */
function updateCartCount() {
    fetch(window.APP_URL + '/cart-count')
        .then(response => response.json())
        .then(data => {
            const cartBadge = document.getElementById('cart-count');
            if (cartBadge) {
                cartBadge.textContent = data.count;
            }
        })
        .catch(error => console.log('Cart count update failed'));
}

/**
 * Setup AJAX requests
 */
function setupAjax() {
    // Automatically include CSRF token in AJAX requests if available
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        document.addEventListener('ajax:beforeSend', function (e) {
            if (e.detail.xhr) {
                e.detail.xhr.setRequestHeader('X-CSRF-Token', csrfToken.getAttribute('content'));
            }
        });
    }
}

/**
 * Setup form validations
 */
function setupFormValidations() {
    const forms = document.querySelectorAll('form[novalidate]');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}

/**
 * Add to cart
 */
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch(window.APP_URL + '/cart-add', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                updateCartCount();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => showMessage('Error adding to cart', 'error'));
}

/**
 * Remove from cart
 */
function removeFromCart(cartId) {
    if (!confirm('Are you sure you want to remove this item?')) {
        return;
    }

    const formData = new FormData();
    formData.append('cart_id', cartId);

    fetch(window.APP_URL + '/cart-remove', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => showMessage('Error removing item', 'error'));
}

/**
 * Update cart quantity
 */
function updateCartQuantity(cartId, quantity) {
    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);

    fetch(window.APP_URL + '/cart-update', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => showMessage('Error updating quantity', 'error'));
}

/**
 * Add to wishlist
 */
function addToWishlist(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);

    fetch(window.APP_URL + '/account/add-wishlist', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                location.reload();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => showMessage('Error adding to wishlist', 'error'));
}

/**
 * Remove from wishlist
 */
function removeFromWishlist(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);

    fetch(window.APP_URL + '/account/remove-wishlist', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                location.reload();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => showMessage('Error removing from wishlist', 'error'));
}

/**
 * Show message
 */
function showMessage(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} fixed top-4 right-4 z-50`;
    alertDiv.textContent = message;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.remove();
    }, 4000);
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

/**
 * Confirm action
 */
function confirmAction(message = 'Are you sure?') {
    return confirm(message);
}

// -----------------------------------------------------------------------------
// Account dropdown click behavior (prevent hover-only flicker)
// -----------------------------------------------------------------------------
document.addEventListener('click', function (e) {
    const trigger = document.getElementById('account-trigger');
    const dropdown = document.getElementById('account-dropdown');

    if (!trigger || !dropdown) return;

    // clicking on the trigger toggles visibility
    if (trigger.contains(e.target)) {
        dropdown.classList.toggle('hidden');
        return;
    }

    // clicking outside closes the dropdown
    if (!dropdown.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

/**
 * Toggle select all items in cart
 */
function toggleSelectAll(checkbox) {
    const cartCheckboxes = document.querySelectorAll('.cart-item-checkbox');
    cartCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateSelectedTotal();
}

/**
 * Update selected total price
 */
function updateSelectedTotal() {
    const cartCheckboxes = document.querySelectorAll('.cart-item-checkbox:checked');
    let total = 0;
    let count = 0;

    cartCheckboxes.forEach(checkbox => {
        const price = parseFloat(checkbox.dataset.price);
        const quantity = parseInt(checkbox.dataset.quantity);
        total += price * quantity;
        count += 1;
    });

    // Format and display
    const selectedSubtotal = document.getElementById('selectedSubtotal');
    const selectedTotal = document.getElementById('selectedTotal');
    const selectedCount = document.getElementById('selectedCount');

    if (selectedSubtotal) {
        selectedSubtotal.textContent = formatCurrency(total);
    }
    if (selectedTotal) {
        selectedTotal.textContent = formatCurrency(total);
    }
    if (selectedCount) {
        selectedCount.textContent = count;
    }

    // Update select all checkbox
    const selectAllCheckbox = document.getElementById('selectAllCart');
    const totalCheckboxes = document.querySelectorAll('.cart-item-checkbox').length;
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = count === totalCheckboxes && totalCheckboxes > 0;
    }
}

/**
 * Proceed to checkout with selected items
 */
function proceedToCheckout() {
    const selectedCheckboxes = document.querySelectorAll('.cart-item-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        showMessage('Please select at least one item to checkout', 'error');
        return;
    }

    // Save selected cart IDs to localStorage
    const selectedCartIds = [];
    selectedCheckboxes.forEach(checkbox => {
        selectedCartIds.push(parseInt(checkbox.dataset.cartId));
    });

    localStorage.setItem('selectedCartIds', JSON.stringify(selectedCartIds));

    // Redirect to checkout
    window.location.href = window.APP_URL + '/order-checkout';
}
/**
 * Back to Top Button Functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('backToTopBtn');
    
    if (backToTopBtn) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        
        // Scroll to top when button is clicked
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});