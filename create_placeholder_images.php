<?php
// Script untuk membuat gambar placeholder untuk produk
function createPlaceholderImage($width, $height, $text, $filename, $bg_color = '#f0f0f0', $text_color = '#666666') {
    // Create image
    $image = imagecreate($width, $height);
    
    // Convert hex colors to RGB
    $bg_rgb = hex2rgb($bg_color);
    $text_rgb = hex2rgb($text_color);
    
    // Allocate colors
    $bg = imagecolorallocate($image, $bg_rgb[0], $bg_rgb[1], $bg_rgb[2]);
    $text_color_resource = imagecolorallocate($image, $text_rgb[0], $text_rgb[1], $text_rgb[2]);
    
    // Fill background
    imagefill($image, 0, 0, $bg);
    
    // Add text
    $font_size = 12;
    $font_file = null; // Using default font
    
    // Calculate text position to center it
    $text_width = strlen($text) * 10; // Approximate width
    $text_height = 15;
    $x = ($width - $text_width) / 2;
    $y = ($height + $text_height) / 2;
    
    imagestring($image, 3, $x, $y - 10, $text, $text_color_resource);
    
    // Add shoe icon (simple representation)
    $shoe_color = imagecolorallocate($image, 0x33, 0x33, 0x33);
    
    // Simple shoe shape
    $shoe_x = $width / 2;
    $shoe_y = $height / 2 - 30;
    
    // Shoe sole
    imagefilledellipse($image, $shoe_x, $shoe_y + 20, 80, 20, $shoe_color);
    // Shoe upper
    imagefilledellipse($image, $shoe_x - 10, $shoe_y, 60, 30, $shoe_color);
    
    // Save image
    $filepath = __DIR__ . '/assets/uploads/products/' . $filename;
    imagejpeg($image, $filepath, 85);
    
    // Clean up
    imagedestroy($image);
    
    return file_exists($filepath);
}

function hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);
    
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    
    return array($r, $g, $b);
}

// Generate placeholder images
$images = [
    // Sneakers
    ['nike-air-max-270.jpg', 'Nike Air Max', '#4A90E2'],
    ['adidas-ultraboost-22.jpg', 'Adidas Ultraboost', '#000000'],
    ['converse-allstar.jpg', 'Converse All Star', '#DC143C'],
    
    // Casual
    ['vans-old-skool.jpg', 'Vans Old Skool', '#000000'],
    ['puma-suede-classic.jpg', 'Puma Suede', '#4A4A4A'],
    ['newbalance-574.jpg', 'New Balance', '#E74C3C'],
    
    // Sport
    ['nike-react-infinity.jpg', 'Nike React', '#FF6B35'],
    ['adidas-x-ghosted.jpg', 'Adidas X', '#00D4AA'],
    ['underarmour-hovr.jpg', 'Under Armour', '#1B1B1B']
];

echo "<h2>🖼️ Membuat Gambar Placeholder Produk</h2>";

foreach ($images as $img) {
    $success = createPlaceholderImage(400, 300, $img[1], $img[0], $img[2], '#FFFFFF');
    
    if ($success) {
        echo "✅ Gambar {$img[0]} berhasil dibuat<br>";
    } else {
        echo "❌ Gagal membuat gambar {$img[0]}<br>";
    }
}

echo "<br>✨ Semua gambar placeholder telah dibuat!";
?>

<br><br>
<a href="add_sample_products.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
    ➡️ Lanjut ke Tambah Produk
</a>
