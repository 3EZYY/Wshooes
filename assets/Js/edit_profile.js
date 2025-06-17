document.addEventListener('DOMContentLoaded', function() {
    // Handle profile image upload
    const profileImageInput = document.getElementById('profile_image');
    const profileImagePreview = document.querySelector('.profile-pic-upload img');

    if (profileImageInput && profileImagePreview) {
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Preview the image
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImagePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);

                // Auto submit the form when image is selected
                this.closest('form').submit();
            }
        });
    }

    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const fullName = document.getElementById('full_name').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!fullName) {
                e.preventDefault();
                alert('Please enter your full name');
                return;
            }
            
            if (!email) {
                e.preventDefault();
                alert('Please enter your email');
                return;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return;
            }
        });
    }
});

// Email validation helper
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}