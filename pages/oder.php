<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần đăng nhập để mua tài khoản game! <a href='login.php'>Đăng nhập</a>";
    exit;
}

if (isset($_GET['id'])) {
    $game_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM game_accounts WHERE id = ? AND status = 'available'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $game = $result->fetch_assoc();
        $total_price = $game['price'];

        // Tạo đơn hàng
        $insert_sql = "INSERT INTO orders (user_id, game_account_id, total_price, order_type, status) VALUES (?, ?, ?, 'buy', 'pending')";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iid", $user_id, $game_id, $total_price);
        
        if ($insert_stmt->execute()) {
            echo "Đặt hàng thành công! Vui lòng thanh toán để hoàn tất giao dịch.";
        } else {
            echo "Lỗi khi đặt hàng!";
        }
    } else {
        echo "Tài khoản game không tồn tại hoặc đã bán!";
    }
} else {
    echo "Không tìm thấy tài khoản game!";
}
?>
