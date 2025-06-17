document.addEventListener('DOMContentLoaded', function() {
    // Login form submission
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            
            try {
                const response = await fetch('/auth/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Login successful
                    showNotification('success', 'Login successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = '/pages/user_profile.php';
                    }, 1500);
                } else {
                    // Login failed
                    showNotification('error', data.error || 'Login failed');
                }
            } catch (error) {
                console.error('Login error:', error);
                showNotification('error', 'An error occurred. Please try again.');
            }
        });
    }
    
    // Signup form submission
    const signupForm = document.getElementById('signup-form');
    if (signupForm) {
        signupForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Mengambil semua field yang diperlukan oleh AuthController
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password')?.value || '';
            const full_name = document.getElementById('full_name')?.value || username; // Fallback ke username jika tidak ada
            const phone = document.getElementById('phone')?.value || '';
            const address = document.getElementById('address')?.value || '';
            const terms = document.getElementById('terms')?.checked || false;
            
            // Validasi dasar
            if (password && confirm_password && password !== confirm_password) {
                showNotification('error', 'Password dan konfirmasi password tidak cocok');
                return;
            }
            
            // Membuat form data untuk dikirim dengan metode POST biasa (bukan JSON)
            const formData = new FormData();
            formData.append('username', username);
            formData.append('email', email);
            formData.append('password', password);
            if (confirm_password) formData.append('confirm_password', confirm_password);
            if (full_name) formData.append('full_name', full_name);
            if (phone) formData.append('phone', phone);
            if (address) formData.append('address', address);
            if (terms) formData.append('terms', 'on');
            
            try {
                // Submit form langsung ke action form
                signupForm.submit();
            } catch (error) {
                console.error('Signup error:', error);
                showNotification('error', 'An error occurred. Please try again.');
            }
        });
    }
    
    // Notification helper function
    function showNotification(type, message) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } shadow-lg z-50`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Modal functionality
    const loginModal = document.getElementById('login-modal');
    const signupModal = document.getElementById('signup-modal');
    const closeLoginBtn = document.getElementById('close-login');
    const closeSignupBtn = document.getElementById('close-signup');
    
    // Open modals
    document.querySelectorAll('[data-modal="login"]').forEach(btn => {
        btn.addEventListener('click', () => {
            loginModal.classList.remove('opacity-0', 'invisible');
        });
    });
    
    document.querySelectorAll('[data-modal="signup"]').forEach(btn => {
        btn.addEventListener('click', () => {
            signupModal.classList.remove('opacity-0', 'invisible');
        });
    });
    
    // Close modals
    if (closeLoginBtn) {
        closeLoginBtn.addEventListener('click', () => {
            loginModal.classList.add('opacity-0', 'invisible');
        });
    }
    
    if (closeSignupBtn) {
        closeSignupBtn.addEventListener('click', () => {
            signupModal.classList.add('opacity-0', 'invisible');
        });
    }
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === loginModal) {
            loginModal.classList.add('opacity-0', 'invisible');
        }
        if (e.target === signupModal) {
            signupModal.classList.add('opacity-0', 'invisible');
        }
    });
});