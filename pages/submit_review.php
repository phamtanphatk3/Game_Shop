<?php
include '../config.php'; // Kết nối database
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần đăng nhập để gửi đánh giá!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $game_account_id = $_POST['game_account_id'] ?? NULL;
    $service_id = $_POST['service_id'] ?? NULL;
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Kiểm tra điểm đánh giá hợp lệ
    if ($rating < 1 || $rating > 5) {
        echo "Điểm đánh giá không hợp lệ!";
        exit;
    }

    // Thêm đánh giá vào bảng reviews
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, game_account_id, service_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $user_id, $game_account_id, $service_id, $rating, $comment);

    if ($stmt->execute()) {
        echo "Đánh giá của bạn đã được gửi!";

        // Cập nhật điểm trung bình cho tài khoản game
        if ($game_account_id) {
            $update_avg = $conn->prepare("
                UPDATE game_accounts 
                SET average_rating = (SELECT AVG(rating) FROM reviews WHERE game_account_id = ?) 
                WHERE id = ?");
            $update_avg->bind_param("ii", $game_account_id, $game_account_id);
            $update_avg->execute();
        }

        // Cập nhật điểm trung bình cho dịch vụ cày thuê
        if ($service_id) {
            $update_avg = $conn->prepare("
                UPDATE services 
                SET average_rating = (SELECT AVG(rating) FROM reviews WHERE service_id = ?) 
                WHERE id = ?");
            $update_avg->bind_param("ii", $service_id, $service_id);
            $update_avg->execute();
        }
    } else {
        echo "Lỗi khi gửi đánh giá!";
    }
}
?>
