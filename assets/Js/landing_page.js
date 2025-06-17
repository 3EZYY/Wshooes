// Mobile Menu Toggle
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const mobileMenu = document.getElementById('mobile-menu');

mobileMenuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});

// Theme Toggle
const themeToggle = document.getElementById('theme-toggle');
const html = document.documentElement;

themeToggle.addEventListener('click', () => {
    html.classList.toggle('dark');
    
    // Update theme icon
    const icon = themeToggle.querySelector('i');
    if (html.classList.contains('dark')) {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
    }
});

// Check for saved theme preference
if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    html.classList.add('dark');
}

// Modal Handling
const authBtn = document.getElementById('auth-btn');
const loginModal = document.getElementById('login-modal');
const signupModal = document.getElementById('signup-modal');
const closeLogin = document.getElementById('close-login');
const closeSignup = document.getElementById('close-signup');
const switchToSignup = document.getElementById('switch-to-signup');
const switchToLogin = document.getElementById('switch-to-login');
const continueShopping = document.getElementById('continue-shopping');

function toggleModal(modal) {
    if (modal.classList.contains('invisible')) {
        // Show modal
        modal.classList.remove('invisible');
        modal.classList.add('visible');
        setTimeout(() => modal.classList.remove('opacity-0'), 50);
    } else {
        // Hide modal
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.remove('visible');
            modal.classList.add('invisible');
        }, 300);
    }
}

authBtn.addEventListener('click', () => toggleModal(loginModal));
mobileMenu.querySelector('button').addEventListener('click', () => toggleModal(loginModal));

closeLogin.addEventListener('click', () => toggleModal(loginModal));
closeSignup.addEventListener('click', () => toggleModal(signupModal));

switchToSignup.addEventListener('click', () => {
    toggleModal(loginModal);
    setTimeout(() => toggleModal(signupModal), 300);
});

switchToLogin.addEventListener('click', () => {
    window.location.href = '/Wshooes/pages/login.php';
});

// Close modals when clicking outside
[loginModal, signupModal].forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            toggleModal(modal);
        }
    });
});

// Form Submission
const loginForm = document.getElementById('login-form');
const signupForm = document.getElementById('signup-form');

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    
    try {
        const response = await fetch('/Wshooes/auth/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password }),
        });
        
        const data = await response.json();
        
        if (response.ok) {
            // Login successful
            alert('Login successful!');
            toggleModal(loginModal);
            
            // Update UI for logged-in user
            updateUIForLoggedInUser(data.user);
            
            // Redirect to appropriate page if needed
            // window.location.href = '/Wshooes/pages/user_profile.php';
        } else {
            // Login failed
            alert(data.error || 'Login failed. Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});

signupForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const username = document.getElementById('signup-name').value;
    const email = document.getElementById('signup-email').value;
    const password = document.getElementById('signup-password').value;
    const confirmPassword = document.getElementById('signup-confirm').value;
    
    // Basic validation
    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }
    
    try {
        const response = await fetch('/Wshooes/auth/signup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, email, password }),
        });
        
        const data = await response.json();
        
        if (response.ok) {
            // Signup successful
            alert('Registration successful!');
            window.location.href = '/Wshooes/pages/login.php';
        } else {
            // Signup failed
            alert(data.error || 'Registration failed. Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});

// Function to update UI for logged-in user
function updateUIForLoggedInUser(user) {
    const authBtn = document.getElementById('auth-btn');
    if (user) {
        // Update button to show user's name or profile
        authBtn.innerHTML = `<span class="flex items-center">
            <i class="fas fa-user mr-2"></i>
            ${user.username}
        </span>`;
        
        // You could also update other UI elements here
        // For example, show/hide certain navigation items
    }
}

// Cart Handling
const cartBtn = document.getElementById('cart-btn');
const cartSidebar = document.getElementById('cart-sidebar');
const closeCart = document.getElementById('close-cart');

function toggleCart() {
    cartSidebar.classList.toggle('translate-x-full');
}

cartBtn.addEventListener('click', toggleCart);
closeCart.addEventListener('click', toggleCart);
continueShopping.addEventListener('click', toggleCart);

// Quantity buttons in cart
document.querySelectorAll('#cart-sidebar button').forEach(btn => {
    if (btn.textContent === '+' || btn.textContent === '-') {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const span = btn.parentElement.querySelector('span');
            let quantity = parseInt(span.textContent);
            
            if (btn.textContent === '+' && quantity < 10) {
                quantity++;
            } else if (btn.textContent === '-' && quantity > 1) {
                quantity--;
            }
            
            span.textContent = quantity;
        });
    }
});

// Animate elements when they come into view
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade-in');
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.animate-fade-in').forEach(el => {
    observer.observe(el);
});