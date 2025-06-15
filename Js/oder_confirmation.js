// Simple animation for confirmation checkmark
        document.addEventListener('DOMContentLoaded', function() {
            const checkmark = document.querySelector('.fa-check-circle');
            checkmark.classList.add('animate-bounce');
            
            setTimeout(() => {
                checkmark.classList.remove('animate-bounce');
            }, 2000);
        });