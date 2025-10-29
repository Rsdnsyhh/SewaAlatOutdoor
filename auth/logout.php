<?php
session_start();

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Redirect ke halaman login (di folder auth/ yang sama)
header("Location: login.php");
exit();
?>