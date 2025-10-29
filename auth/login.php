<?php
session_start();
require '../config/config.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/catalog.php");
    }
    exit();
}

$error = '';
$success = '';

if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    if ($user['role'] == 'admin') {
                        header("Location: ../admin/dashboard.php");
                    } else {
                        header("Location: ../user/catalog.php");
                    }
                    exit();
                
                } else if ($user['password'] === 'admin123' && $password === 'admin123' && $user['role'] == 'admin') {
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update_stmt->execute([$new_hash, $user['id']]);
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    header("Location: ../admin/dashboard.php");
                    exit();

                } else {
                    $error = "Username atau password salah!";
                }
            } else {
                $error = "Username atau password salah!";
            }
        } catch (PDOException $e) {
            $error = "Terjadi masalah saat login. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Sewa Outdoor</title>
    <link rel="stylesheet" href="../public/css/style.css"> 
    <style>
        :root {
            --primary-color: #0C2340; --secondary-color: #CD7F32; --light-color: #F5F5F0;
        }
        body {
            font-family: sans-serif; display: flex; justify-content: center; align-items: center;
            height: 100vh; margin: 0; background-color: var(--light-color);
        }
        .login-wrapper {
            display: flex; width: 900px; max-width: 90%; min-height: 500px;
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
        .success-message {
            color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb;
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
            <p>Masuk untuk mengelola inventaris atau memulai sewa.</p>
        </div>
        <div class="login-form-container">
            <h2>Login</h2>
            
            <?php if ($error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            
            <form action="login.php" method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group password-wrapper">
                    <input type="password" id="passwordLogin" name="password" placeholder="Password" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility('passwordLogin')">
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
                <button type="submit" class="btn-login">Login</button>
            </form>
            
            <p class="login-link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </p>
        </div>
    </div>
    <script src="../public/js/script.js"></script>
</body>
</html>