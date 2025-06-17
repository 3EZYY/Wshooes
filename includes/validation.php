<?php
/**
 * Validation Functions for Wshooes E-commerce
 * Contains validation functions for form inputs
 */

/**
 * Validate registration form data
 */
function validate_registration($data) {
    $errors = [];
    
    // Username validation
    if (empty($data['username'])) {
        $errors[] = "Username harus diisi";
    } elseif (strlen($data['username']) < 3) {
        $errors[] = "Username minimal 3 karakter";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
        $errors[] = "Username hanya boleh berisi huruf, angka, dan underscore";
    }
    
    // Email validation
    if (empty($data['email'])) {
        $errors[] = "Email harus diisi";
    } elseif (!validate_email($data['email'])) {
        $errors[] = "Format email tidak valid";
    }
    
    // Password validation
    if (empty($data['password'])) {
        $errors[] = "Password harus diisi";
    } elseif (strlen($data['password']) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    
    // Confirm password validation
    if (empty($data['confirm_password'])) {
        $errors[] = "Konfirmasi password harus diisi";
    } elseif ($data['password'] !== $data['confirm_password']) {
        $errors[] = "Password dan konfirmasi password tidak cocok";
    }
    
    // Full name validation
    if (empty($data['full_name'])) {
        $errors[] = "Nama lengkap harus diisi";
    } elseif (strlen($data['full_name']) < 2) {
        $errors[] = "Nama lengkap minimal 2 karakter";
    }
    
    // Phone validation
    if (empty($data['phone'])) {
        $errors[] = "Nomor telepon harus diisi";
    } elseif (!validate_phone($data['phone'])) {
        $errors[] = "Format nomor telepon tidak valid";
    }
    
    // Address validation
    if (empty($data['address'])) {
        $errors[] = "Alamat harus diisi";
    } elseif (strlen($data['address']) < 10) {
        $errors[] = "Alamat minimal 10 karakter";
    }
    
    // Terms validation
    if (empty($data['terms'])) {
        $errors[] = "Anda harus menyetujui syarat dan ketentuan";
    }
    
    return $errors;
}

/**
 * Validate login form data
 */
function validate_login($data) {
    $errors = [];
    
    // Username/Email validation
    if (empty($data['username'])) {
        $errors[] = "Username atau email harus diisi";
    }
    
    // Password validation
    if (empty($data['password'])) {
        $errors[] = "Password harus diisi";
    }
    
    return $errors;
}

/**
 * Validate product form data
 */
function validate_product($data) {
    $errors = [];
    
    // Name validation
    if (empty($data['name'])) {
        $errors[] = "Nama produk harus diisi";
    }
    
    // Price validation
    if (empty($data['price'])) {
        $errors[] = "Harga produk harus diisi";
    } elseif (!is_numeric($data['price']) || $data['price'] <= 0) {
        $errors[] = "Harga produk harus berupa angka positif";
    }
    
    // Description validation
    if (empty($data['description'])) {
        $errors[] = "Deskripsi produk harus diisi";
    }
    
    // Category validation
    if (empty($data['category_id'])) {
        $errors[] = "Kategori produk harus dipilih";
    }
    
    return $errors;
}

/**
 * Validate profile update data
 */
function validate_profile_update($data) {
    $errors = [];
    
    // Full name validation
    if (empty($data['full_name'])) {
        $errors[] = "Nama lengkap harus diisi";
    }
    
    // Email validation
    if (empty($data['email'])) {
        $errors[] = "Email harus diisi";
    } elseif (!validate_email($data['email'])) {
        $errors[] = "Format email tidak valid";
    }
    
    // Phone validation
    if (!empty($data['phone']) && !validate_phone($data['phone'])) {
        $errors[] = "Format nomor telepon tidak valid";
    }
    
    return $errors;
}

/**
 * Validate password change data
 */
function validate_password_change($data) {
    $errors = [];
    
    // Current password validation
    if (empty($data['current_password'])) {
        $errors[] = "Password saat ini harus diisi";
    }
    
    // New password validation
    if (empty($data['new_password'])) {
        $errors[] = "Password baru harus diisi";
    } elseif (strlen($data['new_password']) < 6) {
        $errors[] = "Password baru minimal 6 karakter";
    }
    
    // Confirm new password validation
    if (empty($data['confirm_new_password'])) {
        $errors[] = "Konfirmasi password baru harus diisi";
    } elseif ($data['new_password'] !== $data['confirm_new_password']) {
        $errors[] = "Password baru dan konfirmasi tidak cocok";
    }
    
    return $errors;
}
