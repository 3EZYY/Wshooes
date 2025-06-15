 // Change main product image when thumbnail is clicked
        function changeMainImage(element) {
            const mainImage = document.getElementById('main-image');
            mainImage.src = element.src;
            
            // Remove border from all thumbnails
            const thumbnails = document.querySelectorAll('img[alt="Wshoes Sneaker Thumbnail"]');
            thumbnails.forEach(thumb => {
                thumb.classList.remove('border-indigo-400');
                thumb.classList.add('border-gray-200');
            });
            
            // Add border to selected thumbnail
            element.classList.remove('border-gray-200');
            element.classList.add('border-indigo-400');
        }
        
        // Select color option
        function selectColor(element, color) {
            const colorOptions = document.querySelectorAll('.color-option');
            colorOptions.forEach(option => {
                option.classList.remove('selected-color');
            });
            
            element.classList.add('selected-color');
            
            // In a real application, you would update the product image based on color selection
            console.log('Selected color:', color);
        }
        
        // Select size option
        function selectSize(element, size) {
            const sizeOptions = document.querySelectorAll('.size-option');
            sizeOptions.forEach(option => {
                option.classList.remove('selected-size');
            });
            
            element.classList.add('selected-size');
            
            console.log('Selected size:', size);
        }
        
        // Quantity controls
        let quantity = 1;
        
        function increaseQuantity() {
            quantity++;
            document.getElementById('quantity').textContent = quantity;
        }
        
        function decreaseQuantity() {
            if (quantity > 1) {
                quantity--;
                document.getElementById('quantity').textContent = quantity;
            }
        }
        
        // Toggle wishlist heart
        const heartIcon = document.querySelector('.heart-icon');
        heartIcon.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        });