function updateQuantity(itemId, change) {
            const quantityElement = document.getElementById(`${itemId}-quantity`);
            let quantity = parseInt(quantityElement.textContent);
            quantity += change;
            
            // Ensure quantity doesn't go below 1
            if (quantity < 1) quantity = 1;
            
            quantityElement.textContent = quantity;
            updateTotals();
        }
        
        // Function to remove item
        function removeItem(itemId) {
            if (confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
                const itemElement = document.getElementById(itemId);
                if (itemElement) {
                    itemElement.remove();
                    updateTotals();
                }
            }
        }
        
        // Function to update totals (simplified for demo)
        function updateTotals() {
            // In a real app, you would calculate this based on all items in cart
            const subtotal = 4696000; // This would be calculated
            const shipping = 25000;
            const total = subtotal + shipping;
            
            document.getElementById('subtotal').textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
            document.getElementById('total').textContent = `Rp ${total.toLocaleString('id-ID')}`;
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateTotals();
        });