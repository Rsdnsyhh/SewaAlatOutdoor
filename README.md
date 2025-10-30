# Nama  : Rusdiansyah
# NIM   : 2409106013
# Kelas : A'24

# Sistem Informasi Sewa Alat Outdoor & Camping

Ini adalah aplikasi web sederhana yang dibangun menggunakan **PHP Native** dan database **MySQL** (via PDO) untuk mengelola sistem penyewaan alat-alat outdoor dan camping.

Aplikasi ini memiliki dua hak akses utama:
1.  **User (Pengguna):** Dapat melihat katalog, mendaftar, login, dan melakukan pemesanan (booking).
2.  **Admin:** Memiliki dashboard untuk mengelola seluruh data master (alat, kategori, dan pesanan).

## Fitur Utama

### Untuk User (Pengguna)
-   Autentikasi (Login & Register).
-   Melihat katalog produk dengan gambar dan deskripsi.
-   Melihat halaman detail untuk setiap produk.
-   Melakukan pemesanan alat dengan memilih tanggal sewa dan kembali.
-   Fitur Mode Gelap (Dark Mode) yang tersimpan di *local storage*.

### Untuk Admin
-   Login khusus untuk administrator.
-   Dashboard statistik (total alat, pesanan aktif, total user).
-   Manajemen Alat (CRUD - Tambah, Lihat, Edit, Hapus).
-   Upload gambar produk saat menambah/mengedit alat.
-   Manajemen Pesanan (Melihat daftar pesanan, menandai sebagai "Selesai", menghapus pesanan).

## Teknologi yang Digunakan
-   **Backend:** PHP Native (prosedural & OOP-lite)
-   **Database:** MySQL (Koneksi menggunakan PDO)
-   **Frontend:** HTML5, CSS3, JavaScript (vanilla)
-   **Server:** Direkomendasikan menggunakan Laragon atau XAMPP.

## Panduan Instalasi (Cara Menjalankan)

1.  **Unduh Proyek:**
    Pastikan semua file proyek berada dalam satu folder (misal: `sewa-outdoor`).

2.  **Server Lokal:**
    Jalankan server lokal Anda (misal: **Laragon** atau **XAMPP**).

3.  **Database:**
    -   Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
    -   Buat database baru dengan nama `SewaAlatOutdoor`.
    -   Pilih database `SewaAlatOutdoor`, lalu klik tab **Import**.
    -   Pilih file `SewaAlatOutdoor.sql` yang ada di dalam proyek ini dan klik "Go" atau "Kirim".

4.  **Konfigurasi Koneksi:**
    -   Buka file `config/config.php`.
    -   Pastikan pengaturan database Anda sudah benar (terutama `$db_name`, `$db_user`, dan `$db_pass`).
    ```php
    $db_host = 'localhost';
    $db_name = 'SewaAlatOutdoor';
    $db_user = 'root'; // Default Laragon/XAMPP
    $db_pass = '';     // Default Laragon/XAMPP (kosong)
    ```

5.  **Jalankan Aplikasi:**
    -   Buka proyek Anda di browser.
    -   Aplikasi akan otomatis mengarah ke halaman katalog di `user/catalog.php`.

## Akun Demo

Untuk mengakses halaman admin, gunakan akun berikut:

-   **Username:** `admin`
-   **Password:** `admin123`

## Struktur Folder
```
SEWAALATOUTDOOR/
│
├── admin/                          # Folder khusus untuk halaman admin
│   ├── items/                      # Manajemen data alat/produk
│   │   ├── items_action.php        # Handler aksi CRUD items
│   │   ├── items_create.php        # Form tambah item baru
│   │   ├── items_detail.php        # Detail item
│   │   ├── items_edit.php          # Form edit item
│   │   └── items_list.php          # Daftar semua item
│   │
│   ├── rentals/                    # Manajemen pesanan/rental
│   │   ├── dashboard_home.php      # Dashboard utama admin
│   │   └── dashboard.php           # Halaman dashboard alternatif
│   │
│   └── auth/                       # Autentikasi admin
│       ├── login.php               # Halaman login admin
│       ├── logout.php              # Proses logout admin
│       └── register.php            # Halaman register admin (opsional)
│
├── config/                         # Konfigurasi aplikasi
│   └── config.php                  # Konfigurasi database & koneksi PDO
│
├── database/                       # File database
│   └── SewaAlatOutdoor.sql         # Dump database SQL
│
├── public/                         # Folder asset publik
│   ├── css/                        # File stylesheet
│   │   └── style.css               # Style utama aplikasi
│   │
│   └── images/                     # Folder gambar
│       └── products/               # Gambar produk yang diupload
│           ├── flysheet.jpeg
│           ├── head_lamp_LED.jpeg
│           ├── kompor_portable.jpeg
│           ├── matras_gulung.jpeg
│           ├── sleeping_bag.jpeg
│           └── tenda_camping.jpeg
│
├── uploads/                        # Folder upload (cadangan/alternatif)
│
├── js/                             # File JavaScript
│   └── script.js                   # Script untuk dark mode & interaksi
│
└── user/                           # Folder khusus untuk halaman user
    ├── booking/                    # Modul booking/pemesanan
    │   ├── catalog.php             # Halaman katalog produk
    │   ├── detail.php              # Detail produk
    │   └── index.php               # Halaman booking
    │
    └── README.md                   # File dokumentasi ini
```

---

## Penjelasan Struktur

### **admin/**
Semua halaman yang hanya bisa diakses oleh administrator.
-   **items/**: Kelola data alat outdoor (Create, Read, Update, Delete)
-   **rentals/**: Dashboard dan manajemen pesanan
-   **auth/**: Sistem autentikasi untuk admin

### **user/**
Halaman yang dapat diakses oleh pengguna umum.
-   **booking/**: Katalog, detail produk, dan proses pemesanan
-   Pengguna harus login untuk melakukan booking

### **config/**
Berisi konfigurasi koneksi database menggunakan PDO.

### **database/**
File SQL untuk membuat database dan tabel yang diperlukan.

### **public/**
Asset statis seperti CSS, gambar produk, dan file JavaScript.

---

## Cara Penggunaan

### Untuk User (Pengguna):
1.  Buka halaman utama atau katalog (`user/booking/catalog.php`)
2.  Daftar akun baru melalui halaman register
3.  Login dengan akun yang telah dibuat
4.  Pilih produk dari katalog
5.  Klik "Detail" untuk melihat informasi lengkap
6.  Klik "Sewa Sekarang" dan isi formulir booking
7.  Pilih tanggal sewa dan tanggal kembali
8.  Konfirmasi pesanan

### Untuk Admin:
1.  Akses halaman login admin (`admin/auth/login.php`)
2.  Login dengan akun admin (username: `admin`, password: `admin123`)
3.  Akses dashboard untuk melihat statistik
4.  Kelola data alat melalui menu **Items**:
    -   Tambah alat baru dengan upload gambar
    -   Edit informasi alat yang sudah ada
    -   Hapus alat yang tidak diperlukan
5.  Kelola pesanan melalui menu **Rentals**:
    -   Lihat semua pesanan yang masuk
    -   Update status pesanan (Active/Completed)
    -   Hapus pesanan yang sudah selesai

---

## Fitur Keamanan

-   **Password Hashing**: Password disimpan dengan `password_hash()` PHP
-   **Prepared Statements**: Semua query menggunakan PDO prepared statements untuk mencegah SQL Injection
-   **Session Management**: Sistem autentikasi menggunakan PHP Session
-   **Role-Based Access**: Pemisahan hak akses antara User dan Admin

---

## Catatan Pengembangan

-   Aplikasi ini dibuat untuk keperluan pembelajaran dan portfolio
-   Struktur kode menggunakan pendekatan prosedural dengan sedikit OOP
-   Belum menggunakan framework (PHP Native)
-   Untuk production, disarankan menambahkan:
    -   CSRF Protection
    -   Rate Limiting
    -   File Upload Validation yang lebih ketat
    -   HTTPS/SSL
    -   Environment variables untuk konfigurasi sensitif

---

## Troubleshooting

**Error: "Connection failed"**
-   Pastikan MySQL/MariaDB sudah berjalan
-   Periksa konfigurasi di `config/config.php`
-   Pastikan database `SewaAlatOutdoor` sudah di-import

**Error: "Call to undefined function password_hash()"**
-   Pastikan menggunakan PHP versi 5.5 atau lebih baru

**Gambar produk tidak muncul:**
-   Periksa path folder `public/images/products/`
-   Pastikan file gambar ada di folder tersebut
-   Periksa permission folder (chmod 755)

---