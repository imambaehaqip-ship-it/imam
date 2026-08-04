/**
 * Cart JavaScript
 * Mamz Clothing - Fashion Marketplace
 */

document.addEventListener('DOMContentLoaded', function() {
    initCart();
});

function initCart() {
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const quantity = this.dataset.quantity || 1;
            const ukuran = this.dataset.ukuran || '';
            const warna = this.dataset.warna || '';
            
            addToCart(productId, quantity, ukuran, warna, this);
        });
    });
    
    // Update quantity buttons
    const updateQtyButtons = document.querySelectorAll('.update-qty');
    updateQtyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.dataset.cartId;
            const action = this.dataset.action;
            const quantityInput = document.querySelector(`#qty-${cartId}`);
            let quantity = parseInt(quantityInput.value);
            
            if (action === 'increase') {
                quantity++;
            } else if (action === 'decrease') {
                quantity--;
            }
            
            if (quantity >= 1) {
                updateCartQuantity(cartId, quantity);
            }
        });
    });
    
    // Remove from cart buttons
    const removeFromCartButtons = document.querySelectorAll('.remove-from-cart');
    removeFromCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.dataset.cartId;
            confirmAction('Apakah Anda yakin ingin menghapus produk ini dari keranjang?', function() {
                removeFromCart(cartId);
            });
        });
    });
    
    // Clear cart button
    const clearCartButton = document.querySelector('.clear-cart');
    if (clearCartButton) {
        clearCartButton.addEventListener('click', function() {
            confirmAction('Apakah Anda yakin ingin mengosongkan keranjang?', function() {
                window.location.href = this.href;
            }.bind(this));
        });
    }
}

/**
 * Add to Cart
 */
function addToCart(productId, quantity, ukuran, warna, button) {
    // Show loading state
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }
    
    const data = {
        product_id: productId,
        quantity: quantity,
        ukuran: ukuran,
        warna: warna
    };
    
    ajaxRequest('ajax/cart.php', 'POST', data, function(response) {
        if (response.success) {
            showAlert('success', response.message);
            
            // Update cart count
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(element => {
                element.textContent = response.cart_count;
                element.classList.add('bounce');
                setTimeout(() => element.classList.remove('bounce'), 500);
            });
            
            // Fly to cart animation
            flyToCart(button);
        } else {
            showAlert('error', response.message);
        }
        
        // Reset button
        if (button) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-shopping-cart"></i> Tambah ke Keranjang';
        }
    });
}

/**
 * Update Cart Quantity
 */
function updateCartQuantity(cartId, quantity) {
    const data = {
        action: 'update',
        cart_id: cartId,
        quantity: quantity
    };
    
    ajaxRequest('ajax/cart.php', 'POST', data, function(response) {
        if (response.success) {
            // Update subtotal
            const subtotalElement = document.querySelector(`#subtotal-${cartId}`);
            const priceElement = document.querySelector(`#price-${cartId}`);
            if (subtotalElement && priceElement) {
                const price = parseFloat(priceElement.dataset.price);
                const subtotal = price * quantity;
                subtotalElement.textContent = formatPrice(subtotal);
            }
            
            // Update total
            const totalElement = document.querySelector('#cart-total');
            if (totalElement) {
                totalElement.textContent = formatPrice(response.cart_total);
            }
            
            // Update quantity input
            const quantityInput = document.querySelector(`#qty-${cartId}`);
            if (quantityInput) {
                quantityInput.value = quantity;
            }
        } else {
            showAlert('error', response.message);
        }
    });
}

/**
 * Remove from Cart
 */
function removeFromCart(cartId) {
    const data = {
        cart_id: cartId,
        action: 'remove'
    };
    
    ajaxRequest('ajax/cart.php', 'POST', data, function(response) {
        if (response.success) {
            showAlert('success', response.message);
            
            // Remove cart item from DOM
            const cartItem = document.querySelector(`#cart-item-${cartId}`);
            if (cartItem) {
                cartItem.style.opacity = '0';
                setTimeout(() => cartItem.remove(), 300);
            }
            
            // Update cart count and total
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(element => {
                element.textContent = response.cart_count;
            });
            
            const totalElement = document.querySelector('#cart-total');
            if (totalElement) {
                totalElement.textContent = formatPrice(response.cart_total);
            }
            
            // Check if cart is empty
            const cartItems = document.querySelectorAll('.cart-item');
            if (cartItems.length === 0) {
                location.reload();
            }
        } else {
            showAlert('error', response.message);
        }
    });
}

/**
 * Fly to Cart Animation
 */
function flyToCart(button) {
    if (!button) return;
    
    const cartIcon = document.querySelector('.cart-icon');
    if (!cartIcon) return;
    
    const buttonRect = button.getBoundingClientRect();
    const cartRect = cartIcon.getBoundingClientRect();
    
    const flyElement = document.createElement('div');
    flyElement.style.cssText = `
        position: fixed;
        width: 20px;
        height: 20px;
        background: var(--primary);
        border-radius: 50%;
        z-index: 9999;
        top: ${buttonRect.top}px;
        left: ${buttonRect.left}px;
        transition: all 0.6s ease-in-out;
    `;
    
    document.body.appendChild(flyElement);
    
    setTimeout(() => {
        flyElement.style.top = cartRect.top + 'px';
        flyElement.style.left = cartRect.left + 'px';
        flyElement.style.transform = 'scale(0)';
    }, 10);
    
    setTimeout(() => {
        flyElement.remove();
        
        // Bounce animation on cart icon
        cartIcon.classList.add('bounce');
        setTimeout(() => cartIcon.classList.remove('bounce'), 500);
    }, 600);
}
