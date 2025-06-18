// Collection Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Animate collection cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe all collection cards
    const collectionCards = document.querySelectorAll('.collection-card');
    collectionCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
    
    // Product card hover effects
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            // Add subtle scale effect to image
            const image = this.querySelector('.product-image img');
            if (image) {
                image.style.transform = 'scale(1.05)';
                image.style.transition = 'transform 0.3s ease';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            // Reset image scale
            const image = this.querySelector('.product-image img');
            if (image) {
                image.style.transform = 'scale(1)';
            }
        });
    });
    
    // Smooth scroll for navigation
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Check if it's an anchor link
            if (href && href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // Add loading animation for product images
    const productImages = document.querySelectorAll('.product-image img');
    productImages.forEach(img => {
        // Add loading placeholder
        const placeholder = img.parentElement;
        placeholder.style.background = 'linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%)';
        placeholder.style.backgroundSize = '200% 100%';
        placeholder.style.animation = 'loading 1.5s infinite';
        
        img.addEventListener('load', function() {
            // Remove loading animation when image loads
            placeholder.style.background = '#f8f9fa';
            placeholder.style.animation = 'none';
            
            // Fade in the image
            this.style.opacity = '0';
            this.style.transition = 'opacity 0.3s ease';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 100);
        });
        
        img.addEventListener('error', function() {
            // Handle image load error
            placeholder.style.background = '#f8f9fa';
            placeholder.style.animation = 'none';
            placeholder.innerHTML = '<i class="fas fa-shoe-prints" style="font-size: 3rem; color: #ccc;"></i>';
        });
    });
    
    // Add click analytics (optional)
    const detailButtons = document.querySelectorAll('.btn-detail');
    detailButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Get product info for analytics
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('.product-name').textContent;
            const productPrice = productCard.querySelector('.product-price').textContent;
            
            // Log to console (in production, send to analytics service)
            console.log('Product viewed:', {
                name: productName,
                price: productPrice,
                timestamp: new Date().toISOString()
            });
            
            // Optional: Add small delay for visual feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1.05)';
            }, 100);
        });
    });
    
    // Add collection header counter animation
    const collectionHeaders = document.querySelectorAll('.collection-header');
    collectionHeaders.forEach(header => {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Find the corresponding products grid
                    const collectionCard = entry.target.closest('.collection-card');
                    const productsGrid = collectionCard.querySelector('.products-grid');
                    const productCards = productsGrid.querySelectorAll('.product-card');
                    
                    // Add counter if products exist
                    if (productCards.length > 0) {
                        const title = entry.target.querySelector('h2');
                        const originalText = title.textContent;
                        
                        if (!originalText.includes('(')) {
                            title.textContent = `${originalText} (${productCards.length})`;
                        }
                    }
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(header);
    });
    
    // Add keyboard navigation support
    document.addEventListener('keydown', function(e) {
        // Navigate with arrow keys when focusing on product cards
        const focused = document.activeElement;
        
        if (focused && focused.classList.contains('btn-detail')) {
            const productCards = Array.from(document.querySelectorAll('.product-card'));
            const currentCard = focused.closest('.product-card');
            const currentIndex = productCards.indexOf(currentCard);
            
            let nextIndex = -1;
            
            switch(e.key) {
                case 'ArrowRight':
                    nextIndex = (currentIndex + 1) % productCards.length;
                    break;
                case 'ArrowLeft':
                    nextIndex = currentIndex - 1 < 0 ? productCards.length - 1 : currentIndex - 1;
                    break;
                case 'ArrowDown':
                    // Move to next row (approximate)
                    nextIndex = Math.min(currentIndex + 4, productCards.length - 1);
                    break;
                case 'ArrowUp':
                    // Move to previous row (approximate)
                    nextIndex = Math.max(currentIndex - 4, 0);
                    break;
            }
            
            if (nextIndex >= 0 && nextIndex < productCards.length) {
                e.preventDefault();
                const nextButton = productCards[nextIndex].querySelector('.btn-detail');
                if (nextButton) {
                    nextButton.focus();
                }
            }
        }
    });
});

// Add CSS animation for loading effect
const style = document.createElement('style');
style.textContent = `
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    .product-card:focus-within {
        outline: 2px solid #007bff;
        outline-offset: 2px;
    }
`;
document.head.appendChild(style);

// Utility function to format price
function formatPrice(price) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(price);
}

// Utility function to debounce scroll events
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
