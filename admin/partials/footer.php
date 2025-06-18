        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Common Admin Scripts -->
    <script>
        // Auto-refresh dashboard every 5 minutes
        if (window.location.pathname.includes('dashboard.php')) {
            setInterval(() => {
                window.location.reload();
            }, 300000); // 5 minutes
        }

        // Confirm delete actions
        document.querySelectorAll('[onclick*="confirm"]').forEach(element => {
            element.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to perform this action?')) {
                    e.preventDefault();
                }
            });
        });

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const messages = document.querySelectorAll('.bg-green-100, .bg-red-100, .bg-yellow-100');
            messages.forEach(message => {
                message.style.transition = 'opacity 0.5s ease-out';
                message.style.opacity = '0';
                setTimeout(() => {
                    message.remove();
                }, 500);
            });
        }, 5000);

        // Add loading state to forms
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                }
            });
        });

        // Table row hover effects
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8fafc';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });

        // Format currency inputs
        document.querySelectorAll('input[type="number"][step]').forEach(input => {
            if (input.step.includes('1000') || input.step.includes('10000')) {
                input.addEventListener('input', function() {
                    // Add thousand separators for display
                    let value = this.value.replace(/,/g, '');
                    if (value) {
                        this.value = parseInt(value).toLocaleString('id-ID');
                    }
                });
                
                input.addEventListener('blur', function() {
                    // Remove separators for form submission
                    this.value = this.value.replace(/,/g, '');
                });
            }
        });

        // Responsive sidebar toggle for mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.fixed.inset-y-0.left-0');
            const main = document.querySelector('main.ml-64');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                main.classList.add('ml-64');
            } else {
                sidebar.classList.add('-translate-x-full');
                main.classList.remove('ml-64');
            }
        }

        // Add mobile menu button if screen is small
        if (window.innerWidth < 768) {
            const header = document.querySelector('main');
            if (header) {
                const mobileBtn = document.createElement('button');
                mobileBtn.innerHTML = '<i class="fas fa-bars"></i>';
                mobileBtn.className = 'md:hidden fixed top-4 left-4 z-50 bg-gray-900 text-white p-2 rounded';
                mobileBtn.onclick = toggleSidebar;
                document.body.appendChild(mobileBtn);
            }
        }
    </script>
</body>
</html>
