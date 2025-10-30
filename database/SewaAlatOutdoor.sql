-- 1. Membuat dan Menggunakan Database
CREATE DATABASE IF NOT EXISTS SewaAlatOutdoor CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE SewaAlatOutdoor;

-- 2. Tabel Pengguna (Admin & User)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password, `role`)
VALUES ('admin', 'admin123', 'admin');

-- 3. Tabel Kategori
CREATE TABLE IF NOT EXISTS categories (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO categories (nama_kategori) VALUES
('Tenda'),
('Alat Masak'),
('Alat Tidur'),
('Tas & Carrier'),
('Elektronik'),
('Perlengkapan Lainnya');

-- 4. Tabel Item (Alat)
CREATE TABLE IF NOT EXISTS items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  id_kategori INT NULL DEFAULT NULL,
  description TEXT,
  image VARCHAR(255) NULL DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  CONSTRAINT fk_items_categories
    FOREIGN KEY (id_kategori)
    REFERENCES categories(id_kategori)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

INSERT INTO items (title, id_kategori, description, image) VALUES
(
  'Tenda Dome 2 Orang',
  (SELECT id_kategori FROM categories WHERE nama_kategori = 'Tenda'),
  'Tenda dome 2 orang ringan dengan bahan waterproof. Pemasangan cepat & praktis. Ideal untuk pendakian singkat atau camping ceria.',
  'tenda_camping.jpeg'
),
(
  'Kompor Portable',
  (SELECT id_kategori FROM categories WHERE nama_kategori = 'Alat Masak'),
  'Kompor portable mini yang ringkas dan hemat tempat. Api stabil dan mudah diatur. Wajib untuk memasak di gunung. Termasuk hard case.',
  'kompor_portable.jpeg'
),
(
  'Sleeping Bag',
  (SELECT id_kategori FROM categories WHERE nama_kategori = 'Alat Tidur'),
  'Sleeping bag bahan dacron tebal, menjaga suhu tubuh tetap hangat. Model tikar, bisa dibuka lebar jadi selimut. Nyaman dan ringan.',
  'sleeping_bag.jpeg'
),
(
  'Headlamp LED',
  (SELECT id_kategori FROM categories WHERE nama_kategori = 'Elektronik'),
  'Headlamp LED terang dengan 3 mode (terang, redup, strobo). Tali elastis nyaman & bisa disesuaikan. Baterai awet untuk navigasi malam hari.',
  'head_lamp_LED.jpeg'
),
(
  'Flysheet 3x4 Meter',
  (SELECT id_kategori FROM categories WHERE nama_kategori = 'Perlengkapan Lainnya'),
  'Flysheet 3x4 meter serbaguna. Bahan waterproof untuk pelindung tenda, dapur darurat, atau bivak. Ringan dan mudah dilipat.',
  'flysheet.jpeg'
),
(
  'Matras Gulung',
  (SELECT id_kategori FROM categories WHERE nama_kategori = 'Alat Tidur'),
  'Matras gulung standar berbahan spons. Efektif menahan dingin dari tanah. Alas tidur yang simpel, ringan, dan mudah dibersihkan.',
  'matras_gulung.jpeg'
),
(
  'Tas Carrier 60L',
  (SELECT id_kategori FROM categories WHERE nama_kategori = 'Tas & Carrier'),
  'Tas carrier 60L dengan backsystem busa tebal yang nyaman. Banyak kompartemen untuk barang. Cocok untuk pendakian 3-4 hari.',
  NULL -- Ganti NULL dengan 'namafile.jpeg' jika Anda sudah punya gambarnya
);


-- 5. Tabel Penyewaan (Booking)
CREATE TABLE IF NOT EXISTS rentals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  item_id INT,
  tanggal_sewa DATE NOT NULL,
  tanggal_kembali DATE NOT NULL,
  status ENUM('dipinjam', 'dikembalikan') DEFAULT 'dipinjam',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL, 
  FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE SET NULL
);

-- 6. Menampilkan semua tabel
SHOW TABLES;