# 👟 Wshooes - E-Commerce Platform

<div align="center">

![Wshooes Logo](assets/images/logo.png)

**Wshooes** adalah platform e-commerce modern yang dirancang khusus untuk penjualan sepatu berkualitas tinggi. Dengan interface yang elegan dan fitur-fitur canggih, Wshooes memberikan pengalaman berbelanja online yang tak terlupakan.

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)

</div>

---

## 🌟 Fitur Utama

### 🛍️ **Untuk Pelanggan**
- **Katalog Produk Lengkap** - Jelajahi koleksi sepatu terbaru dengan filter dan pencarian canggih
- **Detail Produk Interaktif** - Lihat gambar HD, deskripsi detail, dan ulasan pelanggan
- **Keranjang Belanja Pintar** - Sistem keranjang yang responsif dengan kalkulasi otomatis
- **Checkout Aman** - Proses pembayaran yang aman dengan multiple payment gateway
- **Profil Pengguna** - Kelola informasi pribadi, alamat, dan riwayat pesanan
- **Wishlist** - Simpan produk favorit untuk pembelian di kemudian hari
- **Order Tracking** - Lacak status pesanan real-time
- **Review & Rating** - Berikan ulasan dan rating untuk produk

### 👨‍💼 **Untuk Admin**
- **Dashboard Analytics** - Statistik penjualan, produk, dan pengguna dalam grafik interaktif
- **Manajemen Produk** - CRUD lengkap untuk produk, kategori, dan stok
- **Manajemen Pesanan** - Monitor dan update status pesanan pelanggan
- **Manajemen Pengguna** - Kelola data pelanggan dan admin
- **Laporan Penjualan** - Generate laporan penjualan harian, bulanan, dan tahunan

---

## 🛠️ Teknologi yang Digunakan

### **Backend**
- ![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white) **PHP 8.0+** - Server-side logic
- ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white) **MySQL** - Database management
- **PHPMailer** - Email notification system

### **Frontend**
- ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white) **HTML5** - Markup structure
- ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white) **CSS3** - Styling and animations
- ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black) **JavaScript** - Interactive functionality
- ![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=flat&logo=bootstrap&logoColor=white) **Bootstrap 5.3** - Responsive framework
- ![FontAwesome](https://img.shields.io/badge/Font%20Awesome-339AF0?style=flat&logo=fontawesome&logoColor=white) **Font Awesome** - Icons

### **Architecture Pattern**
- **MVC (Model-View-Controller)** - Clean code architecture
- **OOP (Object-Oriented Programming)** - Modular and maintainable code
- **Singleton Pattern** - Database connection management

---

## 📁 Struktur Proyek

```
Wshooes/
├── 📁 admin/                    # Admin panel
│   ├── dashboard.php            # Dashboard analytics
│   ├── products.php             # Product management
│   ├── orders.php               # Order management
│   └── users.php                # User management
├── 📁 api/                      # API endpoints
├── 📁 assets/                   # Static assets
│   ├── 📁 css/                  # Stylesheets
│   ├── 📁 js/                   # JavaScript files
│   ├── 📁 images/               # Images and media
│   └── 📁 uploads/              # User uploads
├── 📁 auth/                     # Authentication
│   ├── login.php                # Login page
│   ├── signup.php               # Registration page
│   └── logout.php               # Logout handler
├── 📁 config/                   # Configuration files
│   ├── connection.php           # Database connection
│   └── config.php               # App configuration
├── 📁 controllers/              # Business logic
│   ├── AuthController.php       # Authentication logic
│   ├── ProductController.php    # Product operations
│   ├── CartController.php       # Shopping cart logic
│   └── OrderController.php      # Order processing
├── 📁 models/                   # Data models
│   ├── User.php                 # User model
│   ├── Product.php              # Product model
│   ├── Cart.php                 # Cart model
│   └── Order.php                # Order model
├── 📁 includes/                 # Helper functions
│   ├── functions.php            # Utility functions
│   ├── validation.php           # Input validation
│   └── auth.php                 # Authentication helpers
├── 📁 pages/                    # User-facing pages
│   ├── landing_page.php         # Homepage
│   ├── all_product.php          # Product catalog
│   ├── detail_product.php       # Product details
│   ├── cart_page.php            # Shopping cart
│   ├── checkout.php             # Checkout process
│   └── user_profile.php         # User profile
├── 📁 database/                 # Database files
│   └── wshooes_db.sql           # Database schema
└── 📁 templates/                # Reusable templates
    ├── header.php               # Site header
    └── footer.php               # Site footer
```

---

## ⚡ Instalasi & Setup

### **Persyaratan Sistem**
- PHP 8.0 atau lebih tinggi
- MySQL 8.0 atau lebih tinggi
- Web server (Apache/Nginx)
- Composer (untuk dependency management)

### **Langkah Instalasi**

1. **Clone Repository**
   ```bash
   git clone https://github.com/yourusername/wshooes.git
   cd wshooes
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Setup Database**
   ```bash
   # Import database schema
   mysql -u root -p < database/wshooes_db.sql
   ```

4. **Konfigurasi Database**
   ```php
   // config/connection.php
   private $host = "localhost";
   private $db_name = "wshooes_db";
   private $username = "your_username";
   private $password = "your_password";
   ```

5. **Setup Permissions**
   ```bash
   chmod 755 assets/uploads/
   chmod 755 assets/uploads/profile_pictures/
   ```

6. **Jalankan Aplikasi**
   - Akses melalui: `http://localhost/Wshooes`
   - Admin panel: `http://localhost/Wshooes/admin`

---

## 🎨 Screenshots

<div align="center">

### 🏠 Homepage
![Homepage](docs/screenshots/homepage.png)

### 🛍️ Product Catalog
![Product Catalog](docs/screenshots/catalog.png)

### 🛒 Shopping Cart
![Shopping Cart](docs/screenshots/cart.png)

### 📊 Admin Dashboard
![Admin Dashboard](docs/screenshots/admin.png)

</div>

---

## 🔐 Keamanan

Wshooes menerapkan standar keamanan tinggi:

- **Password Hashing** - Menggunakan PHP `password_hash()` dengan algoritma bcrypt
- **SQL Injection Protection** - Prepared statements untuk semua query database
- **XSS Prevention** - Input sanitization dan output escaping
- **CSRF Protection** - Token validation untuk form submissions
- **Session Security** - Secure session management
- **Input Validation** - Comprehensive server-side validation

---

## 📈 Performance

- **Optimized Database** - Indexed queries dan efficient schema design
- **Caching System** - Smart caching untuk performa optimal
- **Image Optimization** - Compressed images dengan lazy loading
- **Minified Assets** - CSS dan JavaScript yang dioptimalkan
- **Responsive Design** - Mobile-first approach untuk semua device

---

## 🚀 Fitur Mendatang

- [ ] **Multi-language Support** - Dukungan bahasa Indonesia dan Inggris
- [ ] **PWA (Progressive Web App)** - Instalasi seperti aplikasi mobile
- [ ] **Live Chat Support** - Customer service real-time
- [ ] **Social Media Integration** - Login dengan Facebook/Google
- [ ] **Advanced Analytics** - Google Analytics integration
- [ ] **Push Notifications** - Notifikasi promo dan update pesanan
- [ ] **AI Recommendation** - Rekomendasi produk berbasis AI
- [ ] **Multiple Payment Gateway** - Integrasi dengan berbagai payment method

---

## 🤝 Kontribusi

Kami menyambut kontribusi dari developer lain! Berikut cara berkontribusi:

1. **Fork** repository ini
2. **Create** feature branch (`git checkout -b feature/AmazingFeature`)
3. **Commit** perubahan (`git commit -m 'Add some AmazingFeature'`)
4. **Push** ke branch (`git push origin feature/AmazingFeature`)
5. **Open** Pull Request

---

## 📝 License

Proyek ini dilisensikan under the **MIT License** - lihat file [LICENSE](LICENSE) untuk detail.

---

## 👨‍💻 Developer

<div align="center">

**Dibuat dengan ❤️ oleh Tim Wshooes**

[![GitHub](https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white)](https://github.com/yourusername)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](https://linkedin.com/in/yourprofile)
[![Email](https://img.shields.io/badge/Email-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:your.email@example.com)

</div>

---

## 📞 Support

Jika Anda memiliki pertanyaan atau membutuhkan bantuan:

- **Email**: support@wshooes.com
- **Documentation**: [docs.wshooes.com](https://docs.wshooes.com)
- **Issues**: [GitHub Issues](https://github.com/yourusername/wshooes/issues)

---

<div align="center">

**⭐ Jangan lupa berikan star jika proyek ini membantu Anda! ⭐**

[![Made with Love](https://img.shields.io/badge/Made%20with-❤️-red.svg)](https://github.com/yourusername/wshooes)

</div>