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

-- Admin default (username: admin / password: admin123)
-- (Akan di-hash otomatis oleh login.php saat login pertama)
INSERT INTO users (username, password, `role`)
VALUES ('admin', 'admin123', 'admin');

-- 3. Tabel Kategori
CREATE TABLE IF NOT EXISTS categories (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL UNIQUE
);

-- Isi data Kategori
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

-- Isi data contoh untuk 'items'
INSERT INTO items (title, id_kategori, description, image) VALUES
(
  'Tenda Dome 2 Orang',
  (SELECT id_kategori FROM categories WHERE nama_kategori = 'Tenda'),
  'Tenda ringan untuk 2 orang, cocok untuk camping singkat.',
  NULL
),
(
  'Kompor Portable',
  (SELECT id_kategori FROM categories WHERE nama_kategori = 'Alat Masak'),
  'Kompor kecil dengan tabung gas mini.',
  NULL
),
(
  'Sleeping Bag',
  (SELECT id_kategori FROM categories WHERE nama_kategori = 'Alat Tidur'),
  'Sleeping bag hangat, cocok untuk daerah dingin.',
  NULL
);
-- (Anda bisa tambahkan data lain dari skrip yang saya berikan sebelumnya)

-- 5. Tabel Penyewaan (Booking)
CREATE TABLE IF NOT EXISTS rentals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  item_id INT,
  tanggal_sewa DATE NOT NULL,
  tanggal_kembali DATE NOT NULL,
  status ENUM('dipinjam', 'dikembalikan') DEFAULT 'dipinjam',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  -- Jika user/item dihapus, data sewa tetap ada (diset NULL)
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL, 
  FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE SET NULL
);

-- 6. Menampilkan semua tabel
SHOW TABLES;