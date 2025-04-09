<?php
session_start();
session_unset(); // Xóa toàn bộ biến session
session_destroy(); // Hủy session

// Chuyển hướng về trang chủ
header("Location: ../index.php");
exit();
?>
