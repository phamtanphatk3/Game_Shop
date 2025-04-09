<?php
include '../config.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $new_password = "123456"; // Mật khẩu mới mặc định
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashed_password, $user_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Mật khẩu đã được đặt lại thành 123456'); window.location.href='../pages/login.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi đặt lại mật khẩu'); window.location.href='../pages/login.php';</script>";
    }
}
?>
