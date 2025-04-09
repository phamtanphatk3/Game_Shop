<?php
include '../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần đăng nhập để thực hiện thanh toán! <a href='login.php'>Đăng nhập</a>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = intval($_POST['order_id']);
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];

    // Lấy thông tin đơn hàng
    $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $total_price = $order['total_price'];
        $game_account_id = $order['game_account_id'];
        $order_type = $order['order_type'];

        // Cập nhật thanh toán thành công
        $insert_payment_sql = "INSERT INTO payments (user_id, order_id, payment_method, amount, status) VALUES (?, ?, ?, ?, 'success')";
        $insert_payment_stmt = $conn->prepare($insert_payment_sql);
        $insert_payment_stmt->bind_param("iisd", $user_id, $order_id, $payment_method, $total_price);

        if ($insert_payment_stmt->execute()) {
            // Cập nhật trạng thái đơn hàng
            $update_order_sql = "UPDATE orders SET status = 'completed' WHERE id = ?";
            $update_order_stmt = $conn->prepare($update_order_sql);
            $update_order_stmt->bind_param("i", $order_id);
            $update_order_stmt->execute();

            // Cập nhật trạng thái tài khoản game
            if ($order_type == 'buy') {
                $update_game_sql = "UPDATE game_accounts SET status = 'sold' WHERE id = ?";
                $update_game_stmt = $conn->prepare($update_game_sql);
                $update_game_stmt->bind_param("i", $game_account_id);
                $update_game_stmt->execute();
            }

            // Lưu vào bảng giao dịch
            $insert_transaction_sql = "INSERT INTO transactions (user_id, order_id, game_account_id, transaction_type, amount, status) VALUES (?, ?, ?, ?, ?, 'completed')";
            $insert_transaction_stmt = $conn->prepare($insert_transaction_sql);
            $insert_transaction_stmt->bind_param("iiisd", $user_id, $order_id, $game_account_id, $order_type, $total_price);
            $insert_transaction_stmt->execute();

            echo "Thanh toán thành công! <a href='index.php'>Về trang chủ</a>";
        } else {
            echo "Lỗi khi xử lý thanh toán!";
        }
    } else {
        echo "Không tìm thấy đơn hàng hợp lệ!";
    }
}
?>
