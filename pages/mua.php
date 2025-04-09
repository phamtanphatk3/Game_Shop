<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "game_shop";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    $error = "Bạn cần đăng nhập để thực hiện giao dịch.";
} else {
    $user_id = $_SESSION['user_id']; // Lấy ID người dùng từ session

    // Kiểm tra xem có tài khoản nào được chọn không
    if (!isset($_GET['id'])) {
        $error = "Không có tài khoản nào được chọn.";
    } else {
        $account_id = intval($_GET['id']); // Đảm bảo ID là số nguyên

        // Lấy thông tin tài khoản game từ bảng `accounts`
        $sql = "SELECT * FROM accounts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $account = $result->fetch_assoc();

        if (!$account) {
            $error = "Tài khoản không tồn tại!";
        } else {
            $account_price = $account['price']; // Giá của tài khoản

            // Lấy số dư của người dùng từ bảng `users`
            $sql = "SELECT balance FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (!$user) {
                $error = "Không tìm thấy người dùng.";
            } else {
                $user_balance = $user['balance']; // Số dư tài khoản

                // Kiểm tra nếu số dư không đủ
                if ($user_balance < $account_price) {
                    $error = "❌ Bạn không đủ tiền để mua tài khoản này! <br>💰 Vui lòng <a href='nap_tien.php' class='text-warning'>nạp thêm tiền</a> để tiếp tục.";
                } else {
                    // Trừ tiền từ số dư của người dùng
                    $new_balance = $user_balance - $account_price;
                    $sql = "UPDATE users SET balance = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("di", $new_balance, $user_id);
                    $stmt->execute();

                    // Lưu giao dịch vào bảng `transactions`
                    $sql = "INSERT INTO transactions (user_id, account_username, account_password, amount, status, created_at) 
                            VALUES (?, ?, ?, ?, 'completed', NOW())";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("issd", $user_id, $account['username'], $account['password'], $account_price);
                    $stmt->execute();

                    // Xóa tài khoản khỏi bảng `accounts` sau khi mua
                    $sql = "DELETE FROM accounts WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $account_id);
                    $stmt->execute();

                    // Thông báo thành công
                    $success = "✅ Mua tài khoản thành công! <br> 💵 Bạn đã trừ <strong>" . number_format($account_price) . " VNĐ</strong>.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giao Dịch Mua Tài Khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background: #121212; color: #fff; font-family: 'Arial', sans-serif; }
        .container { margin-top: 50px; max-width: 600px; }
        .alert { font-size: 18px; padding: 15px; border-radius: 8px; }
        .fade-out {
            animation: fadeOut 3s forwards;
        }
        @keyframes fadeOut {
            0% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center text-warning">🛒 Giao Dịch Mua Tài Khoản</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center fade-out">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success text-center fade-out">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-primary">🏠 Quay về trang chủ</a>
            <a href="history.php" class="btn btn-secondary">📜 Xem lịch sử giao dịch</a>
        </div>
    </div>

    <script>
        // Tự động ẩn thông báo sau 3 giây
        setTimeout(() => {
            let alerts = document.querySelectorAll('.fade-out');
            alerts.forEach(alert => {
                alert.style.display = 'none';
            });
        }, 3000);
    </script>
</body>
</html>
