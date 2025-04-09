<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>
<h1>Trang Quản Trị</h1>
<p>Chào mừng Admin!</p>
