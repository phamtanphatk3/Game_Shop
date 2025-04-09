<?php
$servername = "localhost"; // Tên máy chủ (mặc định là localhost)
$username = "root"; // Tên đăng nhập MySQL (mặc định là root)
$password = ""; // Mật khẩu MySQL (mặc định là rỗng)
$database = "game_shop"; // Tên cơ sở dữ liệu

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kết nối đến MySQL
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập mã hóa ký tự
$conn->set_charset("utf8mb4");
?>
