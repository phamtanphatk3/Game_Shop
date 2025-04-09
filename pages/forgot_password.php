<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/send_email.php';

if (!isset($conn) || !$conn) {
    die("Không thể kết nối đến cơ sở dữ liệu!");
}

$error = $success = "";

// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    // Debug log
    error_log("Processing reset password request for email: " . $email);
    
    // Kiểm tra xem email đã được gửi gần đây chưa
    if (isset($_SESSION['last_email_sent']) && (time() - $_SESSION['last_email_sent']) < 60) {
        $error = "❌ Vui lòng đợi 1 phút trước khi gửi lại email!";
        error_log("Rate limit hit for email: " . $email);
    } else {
        try {
            // Kiểm tra email có tồn tại trong DB không
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            if (!$stmt) {
                throw new Exception("Lỗi truy vấn: " . $conn->error);
            }
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Tạo token ngẫu nhiên
                $token = bin2hex(random_bytes(32));
                error_log("Token generated successfully");

                // Lưu token vào DB với thời gian hết hạn
                $expires = date('Y-m-d H:i:s', time() + (15 * 60)); // Hết hạn sau 15 phút
                
                // Sửa lại tên cột thành reset_token_expires
                $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
                if (!$update_stmt) {
                    throw new Exception("Lỗi truy vấn cập nhật: " . $conn->error);
                }
                $update_stmt->bind_param("sss", $token, $expires, $email);
                
                if (!$update_stmt->execute()) {
                    throw new Exception("Lỗi thực thi truy vấn: " . $update_stmt->error);
                }

                // Gửi email đặt lại mật khẩu
                if (sendResetEmail($email, $token)) {
                    $_SESSION['last_email_sent'] = time();
                    error_log("Reset email sent successfully to: " . $email);
                    $success = "✅ Kiểm tra email để đặt lại mật khẩu.";
                } else {
                    throw new Exception("Không thể gửi email. Kiểm tra lại SMTP!");
                }
            } else {
                $error = "❌ Email không tồn tại!";
            }
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            $error = "❌ " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên Mật Khẩu - Shop Game</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #000;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            position: relative;
        }

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

        .card {
            background: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
            border: none;
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
        }

        .card-title {
            color: #f6d365;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(147, 112, 219, 0.2);
            color: #fff;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(147, 112, 219, 0.5);
            color: #fff;
            box-shadow: 0 0 10px rgba(147, 112, 219, 0.3);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .btn-primary {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            border: none;
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.5);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(106, 17, 203, 0.7);
        }

        .alert {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: none;
            color: #fff;
        }

        .alert-success {
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            border-left: 4px solid #dc3545;
        }

        .back-to-login {
            color: #f6d365;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-to-login:hover {
            color: #fda085;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
        }

        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-text {
            color: #f6d365;
            font-size: 24px;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <video class="video-background" autoplay muted loop playsinline>
        <source src="../images/video/background.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>

    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-text">ĐANG XỬ LÝ...</div>
    </div>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4">🔒 Quên Mật Khẩu</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" onsubmit="showLoading()">
                            <div class="mb-4">
                                <label for="email" class="form-label">📧 Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                Gửi Liên Kết Đặt Lại Mật Khẩu
                            </button>
                            <div class="text-center">
                                <a href="login.php" class="back-to-login">← Quay lại đăng nhập</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loadingScreen').style.display = 'flex';
        }
    </script>
</body>
</html>
