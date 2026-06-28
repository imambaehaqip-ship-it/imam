/**
 * Wishlist JavaScript
 * Mamz Clothing - Fashion Marketplace
 */

document.addEventListener('DOMContentLoaded', function() {
    initWishlist();
});

function initWishlist() {
    // Add to wishlist buttons
    const addToWishlistButtons = document.querySelectorAll('.add-to-wishlist');
    addToWishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            toggleWishlist(productId, this);
        });
    });
    
    // Remove from wishlist buttons
    const removeFromWishlistButtons = document.querySelectorAll('.remove-from-wishlist');
    removeFromWishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            confirmAction('Apakah Anda yakin ingin menghapus produk ini dari wishlist?', function() {
                removeFromWishlist(productId, this);
            }.bind(this));
        });
    });
}

/**
 * Toggle Wishlist
 */
function toggleWishlist(productId, button) {
    const data = {
        product_id: productId
    };
    
    ajaxRequest('/ajax/wishlist.php', 'POST', data, function(response) {
        if (response.success) {
            showAlert('success', response.message);
            
            // Toggle button state
            if (button) {
                button.classList.toggle('active');
                const icon = button.querySelector('i');
                if (icon) {
                    if (button.classList.contains('active')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    }
                }
            }
        } else {
            showAlert('error', response.message);
        }
    });
}

/**
 * Remove from Wishlist
 */
function removeFromWishlist(productId, button) {
    const data = {
        product_id: productId,
        action: 'remove'
    };
    
    ajaxRequest('/ajax/wishlist.php', 'POST', data, function(response) {
        if (response.success) {
            showAlert('success', response.message);
            
            // Remove wishlist item from DOM
            const wishlistItem = document.querySelector(`#wishlist-item-${productId}`);
            if (wishlistItem) {
                wishlistItem.style.opacity = '0';
                setTimeout(() => wishlistItem.remove(), 300);
            }
            
            // Check if wishlist is empty
            const wishlistItems = document.querySelectorAll('.wishlist-item');
            if (wishlistItems.length === 0) {
                location.reload();
            }
        } else {
            showAlert('error', response.message);
        }
    });
}
