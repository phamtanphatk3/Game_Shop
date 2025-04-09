<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = "";

// Lấy username và số dư từ database
$sql = "SELECT username, balance FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $balance);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $transfer_type = $_POST['transfer_type'] ?? '';

    if ($payment_method === "card") {
        $card_provider = $_POST['card_provider'];
        $card_code = $_POST['card_code'];
        $card_serial = $_POST['card_serial'];
        $card_amount = $_POST['card_amount'];

        if (!empty($card_code) && !empty($card_serial) && $card_amount > 0) {
            $conn->begin_transaction();
            try {
                $sql = "INSERT INTO transactions (user_id, username, payment_method, card_provider, card_code, card_serial, amount, status) 
                        VALUES (?, ?, 'card', ?, ?, ?, ?, 'pending')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issssi", $user_id, $username, $card_provider, $card_code, $card_serial, $card_amount);

                if ($stmt->execute()) {
                    $conn->commit();
                    $success_message = "Giao dịch đang chờ duyệt. Vui lòng đợi admin xác nhận.";
                } else {
                    throw new Exception("Lỗi khi thêm giao dịch.");
                }
                $stmt->close();
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Đã xảy ra lỗi. Vui lòng thử lại!";
            }
        } else {
            $error_message = "Vui lòng nhập đầy đủ thông tin thẻ!";
        }
    } elseif ($payment_method === "bank_transfer") {
        $amount = $_POST['amount'];
        if ($amount > 0) {
            $conn->begin_transaction();
            try {
                $sql = "INSERT INTO transactions (user_id, amount, payment_method, transfer_type, status) 
                        VALUES (?, ?, 'bank_transfer', ?, 'pending')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ids", $user_id, $amount, $transfer_type);

                if ($stmt->execute()) {
                    $conn->commit();
                    $success_message = "Giao dịch đang chờ duyệt. Vui lòng đợi admin xác nhận.";
                } else {
                    throw new Exception("Lỗi khi thêm giao dịch.");
                }
                $stmt->close();
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Đã xảy ra lỗi. Vui lòng thử lại!";
            }
        } else {
            $error_message = "Số tiền nạp phải lớn hơn 0!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nạp Tiền</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            object-fit: cover;
        }

        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(76, 0, 159, 0.7), rgba(28, 0, 91, 0.4));
            z-index: -1;
        }

        body {
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: transparent;
            min-height: 100vh;
        }

        .container {
            margin-top: 50px;
            margin-bottom: 50px;
            animation: fadeInUp 0.5s ease-out;
        }

        .card {
            background: rgba(28, 0, 91, 0.4) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
            transition: all 0.3s ease;
            color: white;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 30px rgba(76, 0, 159, 0.5);
        }

        h2, h4 {
            color: #f6d365;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
            margin-bottom: 30px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(147, 112, 219, 0.2);
            color: white;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #f6d365;
            box-shadow: 0 0 10px rgba(246, 211, 101, 0.3);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-control option {
            background: rgba(28, 0, 91, 0.9);
            color: white;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-warning {
            background: linear-gradient(45deg, #f6d365, #fda085);
            border: none;
            box-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
        }

        .btn-warning:hover {
            box-shadow: 0 0 15px rgba(246, 211, 101, 0.7);
        }

        .text-warning {
            color: #f6d365 !important;
        }

        .alert {
            background: rgba(28, 0, 91, 0.4);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 112, 219, 0.2);
            color: white;
        }

        .alert-success {
            border-color: rgba(40, 167, 69, 0.5);
        }

        .alert-danger {
            border-color: rgba(220, 53, 69, 0.5);
        }

        #bank_details, #qr_code {
            background: rgba(28, 0, 91, 0.3);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid rgba(147, 112, 219, 0.2);
            margin-top: 20px;
        }

        #bank_details ul {
            list-style: none;
            padding-left: 0;
        }

        #bank_details ul li {
            margin: 10px 0;
        }

        #bank_details strong {
            color: #f6d365;
            text-shadow: 0 0 5px rgba(246, 211, 101, 0.3);
        }

        #qr_code img {
            border: 2px solid rgba(147, 112, 219, 0.2);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        #qr_code img:hover {
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.5);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <video class="video-background" autoplay muted loop playsinline>
        <source src="../video/Wuthering Waves.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>

    <div class="container">
        <h2 class="text-center">💰 Nạp Tiền Vào Tài Khoản 💰</h2>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"> <?php echo $success_message; ?> </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"> <?php echo $error_message; ?> </div>
        <?php endif; ?>

        <div class="card p-4">
            <h4>
                <a href="../index.php" class="text-warning text-decoration-none">
                    <i class="fas fa-home"></i>
                </a>
                Xin chào, <?php echo htmlspecialchars($username); ?>!
            </h4>
            <form method="POST">
                <!-- Chọn phương thức nạp tiền -->
                <div class="mb-3">
                    <label class="form-label">Hình thức nạp tiền</label>
                    <select class="form-control" id="payment_method" name="payment_method" required onchange="togglePaymentOptions()">
                        <option value="card">Nạp bằng thẻ điện thoại</option>
                        <option value="bank_transfer">Nạp bằng chuyển khoản</option>
                    </select>
                </div>

                <!-- Form nhập thông tin thẻ cào -->
                <!-- Form nhập thông tin thẻ cào -->
                <div id="card_payment" class="mb-3">
                    <label class="form-label">Nhà mạng</label>
                    <select class="form-control" name="card_provider">
                        <option value="viettel">Viettel</option>
                        <option value="mobifone">Mobifone</option>
                        <option value="vinaphone">Vinaphone</option>
                    </select>

                    <label class="form-label mt-2">Mệnh giá</label>
                    <select class="form-control" name="card_amount">
                        <?php
                        $amounts = [10000, 20000, 50000, 100000, 200000, 500000];
                        foreach ($amounts as $amount) {
                            echo "<option value='$amount'>" . number_format($amount, 0, ',', '.') . " VND</option>";
                        }
                        ?>
                    </select>

                    <label class="form-label mt-2">Mã thẻ</label>
                    <input type="text" class="form-control" name="card_code" placeholder="Nhập mã thẻ">

                    <label class="form-label mt-2">Số seri</label>
                    <input type="text" class="form-control" name="card_serial" placeholder="Nhập số seri">
                </div>


                <!-- Chọn phương thức chuyển khoản -->
                <div id="bank_transfer_payment" class="mb-3" style="display: none;">
                    <label class="form-label">Chọn phương thức chuyển khoản</label>
                    <select class="form-control" id="transfer_type" name="transfer_type" onchange="toggleTransferMethod()">
                        <option value="bank">Chuyển khoản qua STK</option>
                        <option value="qr">Chuyển khoản qua QR</option>
                    </select>

                    <label class="form-label mt-3">Số tiền cần nạp (VND)</label>
                    <select class="form-control" name="amount">
                        <?php
                        for ($i = 10000; $i <= 500000; $i *= 2) {
                            echo "<option value='$i'>" . number_format($i, 0, ',', '.') . " VND</option>";
                        }
                        ?>
                    </select>

                    <!-- Thông tin chuyển khoản qua STK -->
                    <div id="bank_details" class="mt-3">
                        <p class="text-warning">Vui lòng chuyển khoản theo thông tin sau:</p>
                        <ul>
                            <li>Ngân hàng: <strong>ACB</strong></li>
                            <li>Số tài khoản: <strong>23897247</strong></li>
                            <li>Chủ tài khoản: <strong>HÀ THANH TÀITÀI</strong></li>
                            <li>Nội dung chuyển khoản: <strong>NAPTIEN [TÊN ĐĂNG NHẬP]</strong></li>
                        </ul>
                    </div>

                    <!-- Mã QR thanh toán -->
                    <div id="qr_code" class="mt-3 text-center" style="display: none;">
                        <p class="text-warning">Quét mã QR để thanh toán:</p>
                        <img src="../images/ACB.jpg" alt="QR Code" class="img-fluid" style="width: 350px; height: 450px;">
                    </div>

                </div>

                <button type="submit" class="btn btn-warning w-100">Nạp Tiền</button>
            </form>
        </div>
    </div>

    <script>
        function togglePaymentOptions() {
            var method = document.getElementById("payment_method").value;
            document.getElementById("card_payment").style.display = (method === "card") ? "block" : "none";
            document.getElementById("bank_transfer_payment").style.display = (method === "bank_transfer") ? "block" : "none";
        }

        function toggleTransferMethod() {
            var transferType = document.getElementById("transfer_type").value;
            document.getElementById("bank_details").style.display = (transferType === "bank") ? "block" : "none";
            document.getElementById("qr_code").style.display = (transferType === "qr") ? "block" : "none";
        }
    </script>
</body>
</html>
