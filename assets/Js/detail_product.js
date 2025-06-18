// Detail Product JavaScript Functions
let selectedQuantity = 1;
let selectedColor = '';
let selectedSize = '';

// Change main product image when thumbnail is clicked
function changeImage(src) {
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.src = src;
    }
    
    // Update thumbnail active state
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumb => {
        thumb.classList.remove('active');
        if (thumb.src === src) {
            thumb.classList.add('active');
        }
    });
}

// Select color option
function selectColor(element) {
    const colorOptions = document.querySelectorAll('.color-option');
    colorOptions.forEach(option => {
        option.classList.remove('active');
    });
    
    element.classList.add('active');
    
    // Get color from background class
    const classList = element.classList;
    if (classList.contains('bg-blue-600')) selectedColor = 'Blue';
    else if (classList.contains('bg-gray-800')) selectedColor = 'Black';
    else if (classList.contains('bg-red-600')) selectedColor = 'Red';
    else if (classList.contains('bg-green-600')) selectedColor = 'Green';
    
    console.log('Selected color:', selectedColor);
}

// Select size option
function selectSize(element) {
    const sizeOptions = document.querySelectorAll('.size-btn');
    sizeOptions.forEach(option => {
        option.classList.remove('active');
    });
    
    element.classList.add('active');
    selectedSize = element.textContent.trim();
    
    console.log('Selected size:', selectedSize);
}

// Quantity management
function increaseQty() {
    if (selectedQuantity < 10) {
        selectedQuantity++;
        document.getElementById('quantity').textContent = selectedQuantity;
    }
}

function decreaseQty() {
    if (selectedQuantity > 1) {
        selectedQuantity--;
        document.getElementById('quantity').textContent = selectedQuantity;
    }
}

// Add to cart function
function addToCart() {
    if (!selectedSize) {
        showNotification('Please select a size', 'warning');
        return;
    }
    
    const cartItem = {
        quantity: selectedQuantity,
        color: selectedColor || 'Default',
        size: selectedSize
    };
    
    // Simulate adding to cart
    showNotification(`Added ${selectedQuantity} item(s) to cart!`, 'success');
    
    // Update cart counter
    updateCartCounter(selectedQuantity);
    
    console.log('Cart item:', cartItem);
}

// Update cart counter in navigation
function updateCartCounter(addedQuantity) {
    const cartCounter = document.querySelector('.fa-shopping-cart').nextElementSibling;
    if (cartCounter) {
        const currentCount = parseInt(cartCounter.textContent) || 0;
        cartCounter.textContent = currentCount + addedQuantity;
    }
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium transition-all duration-300 transform translate-x-full`;
    
    // Set background color based on type
    switch(type) {
        case 'success':
            notification.classList.add('bg-green-600');
            break;
        case 'warning':
            notification.classList.add('bg-yellow-600');
            break;
        case 'error':
            notification.classList.add('bg-red-600');
            break;
        default:
            notification.classList.add('bg-blue-600');
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for buttons
    const addToCartBtn = document.querySelector('.btn-primary');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', addToCart);
    }
    
    // Favourite button functionality
    const favouriteBtn = document.querySelector('.btn-secondary');
    if (favouriteBtn) {
        favouriteBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                this.style.background = 'rgba(239, 68, 68, 0.2)';
                this.style.borderColor = '#ef4444';
                showNotification('Added to favourites!', 'success');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                this.style.background = 'rgba(59, 130, 246, 0.1)';
                this.style.borderColor = 'rgba(59, 130, 246, 0.3)';
                showNotification('Removed from favourites', 'info');
            }
        });
    }
    
    // Set default selections
    const firstColor = document.querySelector('.color-option');
    if (firstColor) {
        firstColor.classList.add('active');
        selectedColor = 'Blue'; // Default to first color
    }
    
    // Add smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Image zoom effect on hover
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        mainImage.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
    
    console.log('Detail product page initialized');
}); 