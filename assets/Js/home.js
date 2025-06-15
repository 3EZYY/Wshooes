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
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
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
        
        function openModal(modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal(modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        authBtn.addEventListener('click', () => openModal(loginModal));
        mobileMenu.querySelector('button').addEventListener('click', () => openModal(loginModal));
        
        closeLogin.addEventListener('click', () => closeModal(loginModal));
        closeSignup.addEventListener('click', () => closeModal(signupModal));
        
        switchToSignup.addEventListener('click', () => {
            closeModal(loginModal);
            openModal(signupModal);
        });
        
        switchToLogin.addEventListener('click', () => {
            closeModal(signupModal);
            openModal(loginModal);
        });
        
        // Close modals when clicking outside
        [loginModal, signupModal].forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal(modal);
                }
            });
        });
        
        // Form Submission
        document.getElementById('login-form').addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Login functionality would be implemented here');
            closeModal(loginModal);
        });
        
        document.getElementById('signup-form').addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Signup functionality would be implemented here');
            closeModal(signupModal);
        });
        
        // Cart Handling
        const cartBtn = document.getElementById('cart-btn');
        const cartSidebar = document.getElementById('cart-sidebar');
        const closeCart = document.getElementById('close-cart');
        
        cartBtn.addEventListener('click', () => {
            cartSidebar.style.transform = 'translateX(0)';
            document.body.style.overflow = 'hidden';
        });
        
        closeCart.addEventListener('click', () => {
            cartSidebar.style.transform = 'translateX(100%)';
            document.body.style.overflow = '';
        });
        
        continueShopping.addEventListener('click', () => {
            cartSidebar.style.transform = 'translateX(100%)';
            document.body.style.overflow = '';
        });
        
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