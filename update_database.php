<?php
include 'config.php';

// Đọc nội dung file SQL
$sql = file_get_contents('update_users_table.sql');

// Thực thi câu lệnh SQL
if ($conn->multi_query($sql)) {
    echo "✅ Đã cập nhật cấu trúc bảng users thành công!";
} else {
    echo "❌ Lỗi: " . $conn->error;
}

$conn->close();
?> 