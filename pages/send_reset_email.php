<?php
session_start();
include '../config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['email']) || empty($_POST['email'])) {
        $error = "❌ Vui lòng nhập email!";
    } else {
        $email = trim($_POST['email']);

        // Kiểm tra định dạng email hợp lệ
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "❌ Email không hợp lệ!";
        } else {
            // Kiểm tra email có tồn tại không
            $sql = "SELECT email FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Tạo mã OTP (6 số ngẫu nhiên)
                $otp = rand(100000, 999999);
                $expires = time() + 600; // Hết hạn sau 10 phút

                // Đóng câu lệnh trước đó
                $stmt->close();

                // Lưu OTP vào DB
                $sql = "REPLACE INTO password_resets (email, token, expires) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("sii", $email, $otp, $expires);
                    if ($stmt->execute()) {
                        $stmt->close();

                        // Gửi email qua Gmail SMTP
                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'your-email@gmail.com';  // Cập nhật email của bạn
                            $mail->Password = 'your-app-password';    // Cập nhật mật khẩu ứng dụng Gmail
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port = 587;

                            $mail->setFrom('your-email@gmail.com', 'Happy Farm Support');
                            $mail->addAddress($email);
                            $mail->isHTML(true);
                            $mail->Subject = 'Mã xác nhận đặt lại mật khẩu';
                            $mail->Body = "<p>Xin chào,</p>
                                           <p>Mã OTP của bạn là: <strong>$otp</strong></p>
                                           <p>Mã này có hiệu lực trong <strong>10 phút</strong>. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                                           <p>Trân trọng,</p>
                                           <p><strong>Happy Farm Support</strong></p>";

                            $mail->send();

                            $_SESSION['reset_email'] = $email;
                            header("Location: enter_otp.php");
                            exit();
                        } catch (Exception $e) {
                            $error = "❌ Gửi email thất bại: " . $mail->ErrorInfo;
                        }
                    } else {
                        $error = "❌ Lỗi khi lưu OTP vào hệ thống!";
                    }
                } else {
                    $error = "❌ Lỗi truy vấn SQL!";
                }
            } else {
                $error = "❌ Email không tồn tại!";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quên Mật Khẩu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>🔒 Quên Mật Khẩu</h2>
    <?php if (!empty($error)) echo "<p class='text-danger'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p class='text-success'>$success</p>"; ?>

    <form method="POST">
        <input type="email" name="email" class="form-control mb-2" placeholder="Nhập email của bạn" required>
        <button type="submit" class="btn btn-primary w-100">Gửi mã OTP</button>
    </form>
</div>
</body>
</html>
