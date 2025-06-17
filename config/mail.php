<?php

// Email configuration
return [
    'host' => 'smtp.gmail.com',
    'username' => 'wshooes.support@gmail.com', // Email yang akan digunakan untuk mengirim email
    'password' => 'abcd efgh ijkl mnop', // App Password dari Google (16 digit)
    'port' => 587,
    'from_email' => 'wshooes.support@gmail.com', // Harus sama dengan username
    'from_name' => 'Wshooes Support',
    'smtp_secure' => 'tls',
    'smtp_auth' => true,
    'smtp_debug' => 2,
    'smtp_options' => [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]
];

/* 
LANGKAH SETUP EMAIL:
1. Aktifkan 2-Step Verification di akun Gmail Anda:
   - Buka https://myaccount.google.com/security
   - Cari "2-Step Verification" dan aktifkan

2. Buat App Password:
   - Buka https://myaccount.google.com/apppasswords
   - Pilih "Select app" -> "Other (Custom name)"
   - Beri nama "Wshooes"
   - Klik "Generate"
   - Salin 16-digit password yang muncul (format: xxxx xxxx xxxx xxxx)
   - Tempel password tersebut di 'password' di atas (PENTING: masukkan dengan spasi seperti yang ditampilkan)

3. Ganti email:
   - Ganti 'wshooes.support@gmail.com' dengan email Gmail Anda
   - Pastikan menggantinya di kedua tempat (username dan from_email)
   - Email harus sama persis di kedua tempat

4. Troubleshooting:
   - Jika mendapat error "Username and Password not accepted", pastikan:
     a. 2-Step Verification sudah diaktifkan
     b. App Password sudah dibuat dengan benar
     c. Password dimasukkan dengan format yang benar (dengan spasi)
     d. Email yang digunakan adalah email yang sama dengan yang digunakan untuk membuat App Password
   - Jika masih bermasalah, coba buat App Password baru
*/