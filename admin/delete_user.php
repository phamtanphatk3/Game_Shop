<?php
include '../config.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin_users.php");
    exit;
}

$id = $_GET['id'];

// Không cho phép admin tự xóa mình
if ($id == $_SESSION['user_id']) {
    header("Location: admin_users.php");
    exit;
}

// Xóa tài khoản
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin_users.php");
    exit;
}
?>
