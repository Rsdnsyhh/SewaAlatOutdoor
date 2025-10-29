<?php
session_start();
// Path diubah: keluar satu level (../) lalu masuk ke config/
require '../config/config.php'; // Memuat koneksi database

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        // Path diubah: ../admin/
        header("Location: ../admin/dashboard.php");
    } else {
        // Path diubah: ../user/catalog.php (halaman utama)
        header("Location: ../user/catalog.php");
    }
    exit();
}

$error = '';
$username_value = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $username_value = $username; // Simpan untuk pre-fill

    try {
        // --- Validasi Sisi Server ---
        if (empty($username) || empty($password) || empty($password_confirm)) {
            $error = "Semua kolom wajib diisi!";
        } elseif ($password !== $password_confirm) {
            $error = "Password dan Konfirmasi Password tidak cocok!";
        } elseif (strlen($password) < 6) {
            $error = "Password minimal harus 6 karakter.";
        } else {
            // Cek apakah username sudah ada
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Username '{$username}' sudah terdaftar. Silakan pilih nama lain.";
            } else {
                // --- Pendaftaran Berhasil ---
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$username, $hashed_password]);
                
                $_SESSION['register_success'] = 'Registrasi berhasil! Silakan login.';
                // Path tetap "login.php" (benar, karena di folder yang sama)
                header("Location: login.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan database. Silakan coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Sewa Outdoor</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        /* (CSS internal Anda tidak berubah) */
        :root {
            --primary-color: #0C2340; --secondary-color: #CD7F32; --light-color: #F5F5F0;
        }
        body {
            font-family: sans-serif; display: flex; justify-content: center; align-items: center;
            min-height: 100vh; margin: 0; background-color: var(--light-color); padding: 20px;
        }
        .login-wrapper {
            display: flex; width: 900px; max-width: 90%; min-height: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border-radius: 12px; overflow: hidden;
        }
        .login-branding {
            background-color: var(--primary-color); color: var(--light-color); padding: 40px;
            flex: 1; display: flex; flex-direction: column; justify-content: center; text-align: center;
        }
        .login-branding h1 { font-size: 2.5em; line-height: 1.2; color: var(--light-color); margin: 0; }
        .login-branding p { font-size: 1.1em; margin-top: 10px; color: rgba(245, 245, 240, 0.8); }
        .login-form-container {
            background-color: #ffffff; padding: 40px; flex: 1; display: flex;
            flex-direction: column; justify-content: center;
        }
        .login-form-container h2 { color: var(--primary-color); text-align: center; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        .form-group input {
            width: 100%; padding: 14px 12px; border: 1px solid #ddd;
            border-radius: 8px; font-size: 1em;
        }
        .form-group input:focus {
            outline: none; border-color: var(--secondary-color);
            box-shadow: 0 0 5px rgba(205, 127, 50, 0.5);
        }
        .btn-login {
            width: 100%; padding: 14px; background-color: var(--primary-color);
            color: var(--light-color); border: none; border-radius: 8px; cursor: pointer;
            font-weight: bold; font-size: 1.1em; transition: background-color 0.3s ease;
        }
        .btn-login:hover { background-color: var(--secondary-color); }
        .error-message {
            color: #e74c3c; background-color: #fdd; border: 1px solid #e74c3c;
            padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 20px;
        }
        .login-link {
            text-align: center; margin-top: 20px; font-size: 0.95em;
        }
        .login-link a { color: var(--secondary-color); text-decoration: none; font-weight: bold; }
        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; min-height: auto; }
            .login-branding, .login-form-container { padding: 30px; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-branding">
            <h1>Sistem Sewa Alat Outdoor & Camping</h1>
            <p>Buat akun untuk memulai petualangan Anda.</p>
        </div>
        <div class="login-form-container">
            <h2>Registrasi Akun Baru</h2>
            
            <?php if ($error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username (cth: petualang123)" required value="<?php echo htmlspecialchars($username_value); ?>">
                </div>
                
                <div class="form-group password-wrapper">
                    <input type="password" id="passwordRegister1" name="password" placeholder="Password" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility('passwordRegister1')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16" style="display: none;">
                            <path d="m10.79 12.912 4.392-5.529a.75.75 0 0 0-.001-1.002.75.75 0 0 0-1.126.164l-.619.774-1.888-2.902a.75.75 0 1 0-1.25.834l.89 1.372a2.75 2.75 0 0 0-4.612 1.344.75.75 0 0 0 .75.751h1.5a.75.75 0 0 0 0-1.5H6.4l.86-1.32a.75.75 0 1 0-1.248-.832l-2.702 4.151A5.003 5.003 0 0 0 2 8c0 .968.305 1.863.834 2.6.24.331.52.647.833.943"/>
                            <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.83Zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.83 2.83ZM9.6 16H8a.75.75 0 0 1 0-1.5h1.6a.75.75 0 0 1 0 1.5Z"/>
                        </svg>
                    </span>
                </div>

                <div class="form-group password-wrapper">
                    <input type="password" id="passwordRegister2" name="password_confirm" placeholder="Konfirmasi Password" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility('passwordRegister2')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16" style="display: none;">
                            <path d="m10.79 12.912 4.392-5.529a.75.75 0 0 0-.001-1.002.75.75 0 0 0-1.126.164l-.619.774-1.888-2.902a.75.75 0 1 0-1.25.834l.89 1.372a2.75 2.75 0 0 0-4.612 1.344.75.75 0 0 0 .75.751h1.5a.75.75 0 0 0 0-1.5H6.4l.86-1.32a.75.75 0 1 0-1.248-.832l-2.702 4.151A5.003 5.003 0 0 0 2 8c0 .968.305 1.863.834 2.6.24.331.52.647.833.943"/>
                            <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.83Zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.83 2.83ZM9.6 16H8a.75.75 0 0 1 0-1.5h1.6a.75.75 0 0 1 0 1.5Z"/>
                        </svg>
                    </span>
                </div>
                
                <button type="submit" class="btn-login">Daftar</button>
            </form>
            
            <p class="login-link">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </p>
        </div>
    </div>
    <script src="../public/js/script.js"></script>
</body>
</html>