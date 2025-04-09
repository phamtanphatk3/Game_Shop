<?php
session_start();
include '../config.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['reset_email'];
    $otp = $_POST['otp'];

    // Kiểm tra mã OTP
    $sql = "SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires > ?";
    $stmt = $conn->prepare($sql);
    $time = time();
    $stmt->bind_param("sii", $email, $otp, $time);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "❌ Mã OTP không hợp lệ hoặc đã hết hạn!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhập mã OTP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        let timeLeft = 60;
        function countdown() {
            document.getElementById("timer").innerText = timeLeft + " giây";
            if (timeLeft <= 0) {
                document.getElementById("resendBtn").disabled = false;
                document.getElementById("timer").innerText = "Hết thời gian!";
            } else {
                timeLeft--;
                setTimeout(countdown, 1000);
            }
        }
        window.onload = countdown;
    </script>
</head>
<body>
<div class="container mt-5 text-center">
    <h2>🔐 Nhập mã OTP</h2>
    <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
    
    <form method="POST">
        <input type="text" name="otp" class="form-control mb-2" placeholder="Nhập mã OTP" required>
        <button type="submit" class="btn btn-primary w-100">Xác nhận</button>
    </form>

    <p id="timer" class="mt-3 text-danger">60 giây</p>
    <form method="POST" action="send_reset_email.php">
        <input type="hidden" name="email" value="<?php echo $_SESSION['reset_email']; ?>">
        <button type="submit" id="resendBtn" class="btn btn-secondary" disabled>Gửi lại mã</button>
    </form>
</div>
</body>
</html>
