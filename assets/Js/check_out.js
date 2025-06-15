document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                // Remove selected class from all methods in the same group
                const parent = this.parentElement;
                parent.querySelectorAll('.payment-method').forEach(m => {
                    m.classList.remove('selected');
                    m.querySelector('div').classList.replace('border-primary', 'border-gray-300');
                    m.querySelector('div > div').classList.replace('bg-primary', 'bg-transparent');
                });
                
                // Add selected class to clicked method
                this.classList.add('selected');
                this.querySelector('div').classList.replace('border-gray-300', 'border-primary');
                this.querySelector('div > div').classList.replace('bg-transparent', 'bg-primary');
            });
        });
        
        // Format credit card input
        document.getElementById('card-number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '');
            if (value.length > 0) {
                value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
            }
            e.target.value = value;
        });
        
        // Format expiry date input
        document.getElementById('expiry-date').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });