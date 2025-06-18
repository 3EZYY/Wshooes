INSERT INTO categories (name, slug, description, is_active) VALUES
('Sneakers', 'sneakers', 'Koleksi sneakers stylish untuk gaya kasual', TRUE),
('Casual', 'casual', 'Sepatu kasual nyaman untuk aktivitas sehari-hari', TRUE),
('Sport', 'sport', 'Sepatu olahraga berkualitas tinggi untuk performa maksimal', TRUE),
('Formal', 'formal', 'Sepatu formal untuk acara resmi dan bisnis', TRUE),
('Boots', 'boots', 'Koleksi boots untuk berbagai aktivitas outdoor', TRUE)
ON DUPLICATE KEY UPDATE 
    description = VALUES(description),
    is_active = VALUES(is_active);


SET @sneakers_id = (SELECT id FROM categories WHERE slug = 'sneakers');
SET @casual_id = (SELECT id FROM categories WHERE slug = 'casual');
SET @sport_id = (SELECT id FROM categories WHERE slug = 'sport');
SET @formal_id = (SELECT id FROM categories WHERE slug = 'formal');
SET @boots_id = (SELECT id FROM categories WHERE slug = 'boots');


INSERT INTO products (name, description, price, stock, category_id, brand, sizes, colors, main_image, status, is_featured, created_at) VALUES
('Nike Air Max 270', 'Sepatu sneakers dengan teknologi Air Max yang memberikan kenyamanan maksimal sepanjang hari.', 1890000, 15, @sneakers_id, 'Nike', '39,40,41,42,43,44', 'Black,White,Red', 'nike-air-max-270.jpg', 'active', 1, NOW()),
('Adidas Ultraboost 22', 'Sneakers dengan teknologi Boost untuk kenyamanan dan performa yang optimal.', 2100000, 12, @sneakers_id, 'Adidas', '39,40,41,42,43,44,45', 'Black,White,Blue', 'adidas-ultraboost-22.jpg', 'active', 1, NOW()),
('Converse Chuck Taylor All Star', 'Sneakers klasik yang timeless dengan desain ikonik yang cocok untuk segala gaya.', 890000, 20, @sneakers_id, 'Converse', '36,37,38,39,40,41,42,43', 'Black,White,Red,Navy', 'converse-chuck-taylor.jpg', 'active', 0, NOW()),
('Vans Old Skool', 'Sneakers skate klasik dengan strip samping yang iconic dan kualitas premium.', 1250000, 18, @sneakers_id, 'Vans', '38,39,40,41,42,43,44', 'Black,White,Checkered', 'vans-old-skool.jpg', 'active', 1, NOW());


INSERT INTO products (name, description, price, stock, category_id, brand, sizes, colors, main_image, status, is_featured, created_at) VALUES
('Clarks Desert Boot', 'Sepatu casual premium dengan bahan suede berkualitas tinggi untuk gaya yang elegan.', 1650000, 10, @casual_id, 'Clarks', '40,41,42,43,44', 'Brown,Black,Grey', 'clarks-desert-boot.jpg', 'active', 1, NOW()),
('Hush Puppies Loafer', 'Sepatu loafer yang nyaman dengan desain klasik untuk aktivitas sehari-hari.', 1320000, 14, @casual_id, 'Hush Puppies', '39,40,41,42,43,44', 'Brown,Black,Navy', 'hush-puppies-loafer.jpg', 'active', 0, NOW()),
('Skechers Go Walk', 'Sepatu walking yang sangat ringan dan nyaman untuk berjalan jauh.', 980000, 22, @casual_id, 'Skechers', '38,39,40,41,42,43,44,45', 'Black,Grey,Navy', 'skechers-go-walk.jpg', 'active', 1, NOW()),
('Timberland Oxford', 'Sepatu oxford casual dengan kualitas premium dan daya tahan yang excellent.', 1950000, 8, @casual_id, 'Timberland', '40,41,42,43,44,45', 'Brown,Black', 'timberland-oxford.jpg', 'active', 0, NOW());


INSERT INTO products (name, description, price, stock, category_id, brand, sizes, colors, main_image, status, is_featured, created_at) VALUES
('Nike Air Zoom Pegasus', 'Sepatu running dengan teknologi Zoom Air untuk performa lari yang maksimal.', 1750000, 16, @sport_id, 'Nike', '39,40,41,42,43,44,45', 'Black,White,Blue', 'nike-pegasus.jpg', 'active', 1, NOW()),
('Adidas Alphabounce', 'Sepatu training serbaguna dengan teknologi Bounce untuk berbagai aktivitas olahraga.', 1450000, 13, @sport_id, 'Adidas', '39,40,41,42,43,44', 'Black,Grey,Red', 'adidas-alphabounce.jpg', 'active', 1, NOW()),
('Puma Future Rider', 'Sepatu olahraga retro-modern dengan cushioning yang excellent untuk aktivitas sport.', 1280000, 19, @sport_id, 'Puma', '38,39,40,41,42,43,44', 'White,Black,Blue', 'puma-future-rider.jpg', 'active', 0, NOW()),
('New Balance 990v5', 'Sepatu running premium dengan teknologi ENCAP untuk stabilitas dan kenyamanan.', 2250000, 7, @sport_id, 'New Balance', '40,41,42,43,44,45', 'Grey,Navy,Black', 'newbalance-990v5.jpg', 'active', 1, NOW());


INSERT INTO products (name, description, price, stock, category_id, brand, sizes, colors, main_image, status, is_featured, created_at) VALUES
('Bally Oxford Classic', 'Sepatu formal oxford premium dengan kulit berkualitas tinggi untuk acara bisnis.', 3200000, 6, @formal_id, 'Bally', '40,41,42,43,44', 'Black,Brown', 'bally-oxford.jpg', 'active', 1, NOW()),
('Cole Haan Wingtip', 'Sepatu formal wingtip dengan detail brogue yang elegan dan sophisticated.', 2800000, 9, @formal_id, 'Cole Haan', '39,40,41,42,43,44', 'Black,Brown', 'colehaan-wingtip.jpg', 'active', 0, NOW());


INSERT INTO products (name, description, price, stock, category_id, brand, sizes, colors, main_image, status, is_featured, created_at) VALUES
('Dr. Martens 1460', 'Boots iconic dengan sol AirWair yang tahan lama dan style yang timeless.', 2450000, 11, @boots_id, 'Dr. Martens', '38,39,40,41,42,43,44', 'Black,Cherry Red', 'drmartens-1460.jpg', 'active', 1, NOW()),
('Timberland 6-Inch Premium', 'Boots waterproof premium untuk aktivitas outdoor dengan perlindungan maksimal.', 2650000, 8, @boots_id, 'Timberland', '40,41,42,43,44,45', 'Wheat,Black', 'timberland-6inch.jpg', 'active', 1, NOW());
