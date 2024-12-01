-- Örnek kullanıcı ekleme (şifre: 123456)
INSERT INTO users (name, email, password, is_admin) VALUES
('Admin User', 'admin@example.com', '$2y$10$8SOZkVDz1T5YV0a0xv7XpedHD7h3GQXgz9oIwRH0.A0YA4b1xKUyi', 1),
('Test User', 'test@example.com', '$2y$10$8SOZkVDz1T5YV0a0xv7XpedHD7h3GQXgz9oIwRH0.A0YA4b1xKUyi', 0);

-- Örnek işletme ekleme
INSERT INTO businesses (user_id, name, address, phone) VALUES
(2, 'Lezzet Dünyası', 'Atatürk Cad. No:123, İstanbul', '+90 555 123 4567'),
(2, 'Cafe Keyif', 'İstiklal Cad. No:456, İstanbul', '+90 555 765 4321');

-- Örnek menüler ekleme
INSERT INTO menus (business_id, name) VALUES
(1, 'Ana Menü'),
(1, 'Öğle Menüsü'),
(2, 'Kahvaltı Menüsü');

-- Örnek kategoriler ekleme
INSERT INTO categories (menu_id, name, sort_order) VALUES
(1, 'Başlangıçlar', 1),
(1, 'Ana Yemekler', 2),
(1, 'Tatlılar', 3),
(1, 'İçecekler', 4),
(2, 'Kahvaltılıklar', 1),
(2, 'İçecekler', 2);

-- Örnek menü öğeleri ekleme
INSERT INTO menu_items (category_id, name, description, price, is_available, sort_order) VALUES
-- Başlangıçlar
(1, 'Mercimek Çorbası', 'Geleneksel Türk mercimek çorbası', 45.00, 1, 1),
(1, 'Karışık Salata', 'Mevsim yeşillikleri ile hazırlanmış salata', 55.00, 1, 2),
(1, 'Humus', 'Nohut püresi, tahin ve zeytinyağı ile', 65.00, 1, 3),

-- Ana Yemekler
(2, 'Izgara Köfte', 'El yapımı ızgara köfte, pilav ve közlenmiş sebzeler ile', 120.00, 1, 1),
(2, 'Piliç Şiş', 'Marine edilmiş tavuk şiş, pilav ve ızgara sebzeler ile', 110.00, 1, 2),
(2, 'Karışık Izgara', 'Köfte, pirzola, tavuk ve dana eti', 180.00, 1, 3),

-- Tatlılar
(3, 'Künefe', 'Antep fıstıklı künefe, kaymak ile servis edilir', 85.00, 1, 1),
(3, 'Sütlaç', 'Geleneksel fırında sütlaç', 45.00, 1, 2),
(3, 'Baklava', 'Antep fıstıklı ev yapımı baklava', 75.00, 1, 3),

-- İçecekler
(4, 'Türk Kahvesi', 'Geleneksel Türk kahvesi', 30.00, 1, 1),
(4, 'Çay', 'Demlik çay', 15.00, 1, 2),
(4, 'Ayran', 'Ev yapımı ayran', 20.00, 1, 3),

-- Kahvaltılıklar
(5, 'Serpme Kahvaltı', 'Zengin kahvaltı tabağı, sınırsız çay ile', 150.00, 1, 1),
(5, 'Menemen', 'Geleneksel menemen, tereyağında hazırlanır', 65.00, 1, 2),
(5, 'Sahanda Yumurta', 'Tereyağında sahanda yumurta', 45.00, 1, 3),

-- Kahvaltı İçecekleri
(6, 'Taze Portakal Suyu', 'Günlük sıkılmış portakal suyu', 35.00, 1, 1),
(6, 'Bitki Çayı', 'Çeşitli bitki çayları', 30.00, 1, 2);
