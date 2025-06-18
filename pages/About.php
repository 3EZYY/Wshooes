<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Wshooes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/Wshooes/assets/css/about.css">
    <style>
        .gradient-bg {
            background: linear-gradient(to right, #1e40af, #1e3a8a);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .timeline-gradient {
            background: linear-gradient(to bottom, #1e40af, #1e3a8a);
        }
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="gradient-bg shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-shoe-prints text-2xl"></i>
                    <h1 class="text-2xl font-bold">Wshooes</h1>
                </div>
                <nav>
                    <ul class="flex space-x-6">
                        <li><a href="/Wshooes/" class="hover:text-purple-200 transition"><i class="fas fa-home mr-1"></i> Home</a></li>
                        <li><a href="/Wshooes/pages/about.php" class="hover:text-purple-200 transition"><i class="fas fa-info-circle mr-1"></i> About</a></li>
                        <li><a href="/Wshooes/pages/all_product.php" class="hover:text-purple-200 transition"><i class="fas fa-shopping-bag mr-1"></i> Shop</a></li>
                        <li><a href="/Wshooes/?page=contact" class="hover:text-purple-200 transition"><i class="fas fa-envelope mr-1"></i> Contact</a></li>
                        <li><a href="/Wshooes/pages/terms_privacy.php" class="hover:text-purple-200 transition"><i class="fas fa-shield-alt mr-1"></i> Terms & Privacy</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <!-- Hero Section -->
            <section class="gradient-bg py-20">
                <div class="container mx-auto px-4 text-center">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-200 to-purple-200">Cerita Kami, Kenyamanan Anda</h1>
                    <p class="text-xl text-blue-200 max-w-3xl mx-auto">
                        Dari awal yang sederhana hingga menjadi pemimpin dalam inovasi sepatu, Wshooes terus melangkah maju sejak 2023.
                    </p>
                </div>
            </section>

            <!-- About Section -->
            <section class="py-16 bg-gray-800">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col lg:flex-row items-center gap-12">
                        <div class="lg:w-1/2">
                            <div class="relative rounded-xl overflow-hidden shadow-lg">
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-900/60 to-blue-800/60 mix-blend-multiply"></div>
                                <img src="https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1025&q=80" 
                                     alt="Workshop Wshooes" 
                                     class="w-full h-full object-cover transform hover:scale-105 transition duration-500">
                            </div>
                        </div>
                        <div class="lg:w-1/2">
                            <h2 class="text-3xl font-bold text-blue-200 mb-6">Siapa Kami</h2>
                            <p class="text-gray-300 mb-6">
                                Didirikan pada tahun 2023, Wshooes lahir dari semangat dan dedikasi tim muda yang berfokus pada inovasi sepatu. 
                                Berawal dari sebuah ide untuk menciptakan sepatu yang tidak hanya stylish tapi juga nyaman, kami telah berkembang 
                                menjadi brand yang dikenal akan kualitas dan desainnya yang modern.
                            </p>
                            <p class="text-gray-300 mb-6">
                                Saat ini, kami beroperasi di Indonesia dengan visi untuk menjadi brand sepatu terpercaya yang mengutamakan 
                                kenyamanan dan gaya. Setiap pasang sepatu Wshooes dibuat dengan perhatian khusus terhadap detail dan kualitas.
                            </p>
                            <div class="glass-card p-6 rounded-lg border-l-4 border-blue-500">
                                <p class="text-blue-200 italic">
                                    "Misi kami sederhana: menciptakan sepatu yang membuat orang bangga memakainya. Bukan hanya karena 
                                    penampilannya, tapi karena kenyamanan yang dirasakan dari langkah pertama hingga terakhir."
                                </p>
                                <p class="text-blue-300 mt-2 font-medium">- Tim Wshooes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Timeline Section -->
            <section class="py-16 bg-gray-900">
                <div class="container mx-auto px-4">
                    <h2 class="text-3xl font-bold text-center text-blue-200 mb-12">Perjalanan Kami</h2>
                    
                    <div class="relative max-w-3xl mx-auto">
                        <div class="absolute left-1/2 h-full w-1 bg-gradient-to-b from-purple-500 to-blue-800 transform -translate-x-1/2"></div>
                        
                        <div class="space-y-8">
                            <!-- Timeline Item 1 -->
                            <div class="relative pl-8 md:pl-0 md:flex justify-between items-center">
                                <div class="md:w-5/12 mb-4 md:mb-0">
                                    <div class="bg-white p-6 rounded-lg shadow-md">
                                        <h3 class="text-xl font-bold text-purple-700 mb-2">2023</h3>
                                        <p class="text-gray-600">Wshooes didirikan dengan semangat inovasi dan kreativitas</p>
                                    </div>
                                </div>
                                <div class="hidden md:block md:w-2/12 text-center">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-600 to-blue-800 flex items-center justify-center text-white mx-auto">
                                        <i class="fas fa-shoe-prints"></i>
                                    </div>
                                </div>
                                <div class="md:w-5/12"></div>
                            </div>
                            
                            <!-- Timeline Item 2 -->
                            <div class="relative pl-8 md:pl-0 md:flex justify-between items-center">
                                <div class="md:w-5/12"></div>
                                <div class="hidden md:block md:w-2/12 text-center">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-600 to-blue-800 flex items-center justify-center text-white mx-auto">
                                        <i class="fas fa-store"></i>
                                    </div>
                                </div>
                                <div class="md:w-5/12 mb-4 md:mb-0">
                                    <div class="bg-white p-6 rounded-lg shadow-md">
                                        <h3 class="text-xl font-bold text-purple-700 mb-2">2023</h3>
                                        <p class="text-gray-600">Peluncuran website e-commerce dan koleksi perdana</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Timeline Item 3 -->
                            <div class="relative pl-8 md:pl-0 md:flex justify-between items-center">
                                <div class="md:w-5/12 mb-4 md:mb-0">
                                    <div class="bg-white p-6 rounded-lg shadow-md">
                                        <h3 class="text-xl font-bold text-purple-700 mb-2">2023</h3>
                                        <p class="text-gray-600">Pengembangan koleksi dan peningkatan layanan pelanggan</p>
                                    </div>
                                </div>
                                <div class="hidden md:block md:w-2/12 text-center">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-600 to-blue-800 flex items-center justify-center text-white mx-auto">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                </div>
                                <div class="md:w-5/12"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Vision & Mission Section -->
            <section class="py-16 bg-gray-800">
                <div class="container mx-auto px-4">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-blue-200 mb-4">Visi & Misi Kami</h2>
                        <div class="w-24 h-1 bg-gradient-to-r from-purple-500 to-blue-800 mx-auto"></div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                        <div class="bg-gradient-to-br from-purple-50 to-blue-50 p-8 rounded-xl border border-purple-100">
                            <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-blue-800 rounded-full flex items-center justify-center text-white mb-6 mx-auto">
                                <i class="fas fa-eye text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-center text-gray-800 mb-4">Visi Kami</h3>
                            <p class="text-gray-600 text-center">
                                Menjadi brand sepatu terpercaya di Indonesia yang menghadirkan inovasi dalam kenyamanan dan gaya, 
                                memberdayakan setiap langkah dengan percaya diri.
                            </p>
                        </div>
                        
                        <div class="bg-gradient-to-br from-purple-50 to-blue-50 p-8 rounded-xl border border-purple-100">
                            <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-blue-800 rounded-full flex items-center justify-center text-white mb-6 mx-auto">
                                <i class="fas fa-bullseye text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-center text-gray-800 mb-4">Misi Kami</h3>
                            <p class="text-gray-600 text-center">
                                Menciptakan sepatu berkualitas tinggi dengan mengutamakan kenyamanan, menggunakan material terbaik, 
                                dan memberikan pengalaman berbelanja yang memuaskan.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Values Section -->
            <section class="py-16 bg-gray-900">
                <div class="container mx-auto px-4">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-blue-200 mb-4">Nilai-Nilai Kami</h2>
                        <div class="w-24 h-1 bg-gradient-to-r from-purple-500 to-blue-800 mx-auto"></div>
                    </div>
                    
                    <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                        <div class="value-card bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                            <div class="w-14 h-14 bg-gradient-to-r from-purple-600 to-blue-800 rounded-lg flex items-center justify-center text-white mb-6">
                                <i class="fas fa-heart text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Kenyamanan Utama</h3>
                            <p class="text-gray-600">
                                Kami memperhatikan setiap detail untuk memastikan sepatu kami nyaman dipakai, karena kenyamanan adalah prioritas utama.
                            </p>
                        </div>
                        
                        <div class="value-card bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                            <div class="w-14 h-14 bg-gradient-to-r from-purple-600 to-blue-800 rounded-lg flex items-center justify-center text-white mb-6">
                                <i class="fas fa-globe-americas text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Kualitas Terbaik</h3>
                            <p class="text-gray-600">
                                Kami berkomitmen menggunakan material berkualitas dan proses produksi yang teliti untuk hasil terbaik.
                            </p>
                        </div>
                        
                        <div class="value-card bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                            <div class="w-14 h-14 bg-gradient-to-r from-purple-600 to-blue-800 rounded-lg flex items-center justify-center text-white mb-6">
                                <i class="fas fa-lightbulb text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Inovasi</h3>
                            <p class="text-gray-600">
                                Kami terus berinovasi dalam desain dan teknologi untuk menciptakan sepatu yang memenuhi kebutuhan modern.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Team Section -->
            <section class="py-16 bg-gray-800">
                <div class="container mx-auto px-4">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-blue-200 mb-4">Meet Our Leadership</h2>
                        <div class="w-24 h-1 bg-gradient-to-r from-purple-500 to-blue-800 mx-auto"></div>
                    </div>
                    
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                        <div class="team-card glass-card rounded-xl shadow-lg overflow-hidden transition duration-300 hover:transform hover:scale-105">
                            <div class="h-64 overflow-hidden relative">
                                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-blue-900/30 z-10"></div>
                                <img src="/Wshooes/assets/images/faza.jpg" alt="faza" class="w-full h-full object-cover">
                            </div>
                            <div class="p-6 bg-gray-800">
                                <h3 class="text-xl font-bold text-blue-200">Faza</h3>
                                <p class="text-blue-400 font-medium mb-2">Lead Developer</p>
                                <p class="text-gray-300 text-sm">
                                    Backend specialist with expertise in PHP and database management. Leads the technical architecture of Wshooes.
                                </p>
                                <div class="flex space-x-3 mt-4">
                                    <a href="#" class="text-blue-400 hover:text-blue-200"><i class="fab fa-linkedin"></i></a>
                                    <a href="#" class="text-blue-400 hover:text-blue-200"><i class="fab fa-github"></i></a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="team-card glass-card rounded-xl shadow-lg overflow-hidden transition duration-300 hover:transform hover:scale-105">
                            <div class="h-64 overflow-hidden relative">
                                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-blue-900/30 z-10"></div>
                                <img src="/Wshooes/assets/images/fredi.jpg" alt="Fredi" class="w-full h-full object-cover">
                            </div>
                            <div class="p-6 bg-gray-800">
                                <h3 class="text-xl font-bold text-blue-200">Fredi</h3>
                                <p class="text-blue-400 font-medium mb-2">Frontend Developer</p>
                                <p class="text-gray-300 text-sm">
                                    UI/UX specialist focused on creating beautiful and responsive user interfaces. Expert in modern web technologies.
                                </p>
                                <div class="flex space-x-3 mt-4">
                                    <a href="#" class="text-blue-400 hover:text-blue-200"><i class="fab fa-linkedin"></i></a>
                                    <a href="#" class="text-blue-400 hover:text-blue-200"><i class="fab fa-github"></i></a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="team-card glass-card rounded-xl shadow-lg overflow-hidden transition duration-300 hover:transform hover:scale-105">
                            <div class="h-64 overflow-hidden relative">
                                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-blue-900/30 z-10"></div>
                                <img src="/Wshooes/assets/images/rafif.jpg" alt="Rafif" class="w-full h-full object-cover">
                            </div>
                            <div class="p-6 bg-gray-800">
                                <h3 class="text-xl font-bold text-blue-200">Rafif</h3>
                                <p class="text-blue-400 font-medium mb-2">Full Stack Developer</p>
                                <p class="text-gray-300 text-sm">
                                    Versatile developer with expertise in both frontend and backend technologies. Specializes in system integration.
                                </p>
                                <div class="flex space-x-3 mt-4">
                                    <a href="#" class="text-blue-400 hover:text-blue-200"><i class="fab fa-linkedin"></i></a>
                                    <a href="#" class="text-blue-400 hover:text-blue-200"><i class="fab fa-github"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="gradient-bg py-16">
                <div class="container mx-auto px-4 text-center">
                    <h2 class="text-3xl font-bold mb-6">Siap Melangkah dengan Nyaman?</h2>
                    <p class="text-xl text-blue-200 max-w-2xl mx-auto mb-8">
                        Bergabunglah dengan ribuan pelanggan yang telah merasakan kenyamanan Wshooes.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#" class="px-8 py-3 bg-white text-purple-700 rounded-lg font-bold hover:bg-gray-100 transition shadow-lg">
                            Belanja Sekarang
                        </a>
                        <a href="#" class="px-8 py-3 border border-white text-white rounded-lg font-bold hover:bg-white hover:bg-opacity-10 transition">
                            Temukan Kami
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer id="contact" class="bg-gray-900 text-white py-12">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-xl font-bold mb-4 flex items-center">
                            <i class="fas fa-shoe-prints mr-2"></i> Wshooes
                        </h3>
                        <p class="text-gray-400 mb-4">Step into comfort, step into style.</p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><a href="/Wshooes/" class="text-gray-400 hover:text-white transition">Home</a></li>
                            <li><a href="/Wshooes/pages/about.php" class="text-gray-400 hover:text-white transition">About Us</a></li>
                            <li><a href="/Wshooes/pages/all_product.php" class="text-gray-400 hover:text-white transition">Products</a></li>
                            <li><a href="/Wshooes/pages/categories.php" class="text-gray-400 hover:text-white transition">Collections</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Customer Service</h4>
                        <ul class="space-y-2">
                            <li><a href="/Wshooes/?page=contact" class="text-gray-400 hover:text-white transition">Contact Us</a></li>
                            <li><a href="/Wshooes/pages/faq.php" class="text-gray-400 hover:text-white transition">FAQs</a></li>
                            <li><a href="/Wshooes/pages/shipping.php" class="text-gray-400 hover:text-white transition">Shipping & Returns</a></li>
                            <li><a href="/Wshooes/pages/size-guide.php" class="text-gray-400 hover:text-white transition">Size Guide</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Stay Updated</h4>
                        <p class="text-gray-400 mb-4">Subscribe to our newsletter for the latest updates and offers.</p>
                        <div class="flex">
                            <input type="email" placeholder="Your email" class="px-4 py-2 rounded-l-lg w-full focus:outline-none text-gray-800">
                            <button class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-r-lg transition">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                    <p>&copy; 2023 Wshooes. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Smooth scroll to contact section
        document.addEventListener('DOMContentLoaded', function() {
            const contactLinks = document.querySelectorAll('a[href*="contact"]');
            contactLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('contact').scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html>