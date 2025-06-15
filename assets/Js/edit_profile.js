
        document.addEventListener('DOMContentLoaded', function() {
            const profilePicInput = document.querySelector('.profile-pic-upload input');
            const profilePicImg = document.querySelector('.profile-pic-upload img');
            
            profilePicInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(event) {
                        profilePicImg.src = event.target.result;
                    }
                    
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
            
            // Set default country
            document.getElementById('country').value = 'US';
        });