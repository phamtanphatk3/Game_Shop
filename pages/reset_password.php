<?php
session_start();
include '../config.php';

$error = $success = "";

// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra token
if (!isset($_GET['token'])) {
    die("Token không hợp lệ!");
}

$token = $_GET['token'];

// Debug: In ra token để kiểm tra
error_log("Token received: " . $token);

// Kiểm tra token trong database
$stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? LIMIT 1");
if (!$stmt) {
    error_log("SQL Error: " . $conn->error);
    die("Lỗi truy vấn: " . $conn->error);
}

$stmt->bind_param("s", $token);
if (!$stmt->execute()) {
    error_log("Execute Error: " . $stmt->error);
    die("Lỗi thực thi truy vấn: " . $stmt->error);
}

$result = $stmt->get_result();
error_log("Number of rows found: " . $result->num_rows);

if ($result->num_rows === 0) {
    die('<div style="text-align: center; padding: 20px; background: rgba(0,0,0,0.5); color: white;">
        <h3>❌ Liên kết không hợp lệ!</h3>
        <p>Token không tồn tại trong hệ thống.</p>
        <a href="forgot_password.php" style="color: #f6d365; text-decoration: none;">← Yêu cầu link mới</a>
    </div>');
}

$user = $result->fetch_assoc();
error_log("User data: " . print_r($user, true));

// Kiểm tra thời gian hết hạn
if ($user['reset_token_expires'] && strtotime($user['reset_token_expires']) < time()) {
    // Xóa token hết hạn
    $stmt = $conn->prepare("UPDATE users SET reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    
    die('<div style="text-align: center; padding: 20px; background: rgba(0,0,0,0.5); color: white;">
        <h3>⏰ Liên kết đã hết hạn!</h3>
        <p>Vui lòng yêu cầu gửi lại link mới để đặt lại mật khẩu.</p>
        <a href="forgot_password.php" style="color: #f6d365; text-decoration: none;">← Yêu cầu link mới</a>
    </div>');
}

// Xử lý form đặt lại mật khẩu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Kiểm tra mật khẩu
    if (strlen($password) < 6) {
        $error = "❌ Mật khẩu phải có ít nhất 6 ký tự!";
    } elseif ($password !== $confirm_password) {
        $error = "❌ Mật khẩu xác nhận không khớp!";
    } else {
        // Cập nhật mật khẩu mới
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user['id']);
        
        if ($stmt->execute()) {
            $success = "✅ Đặt lại mật khẩu thành công! Vui lòng đăng nhập lại.";
        } else {
            $error = "❌ Có lỗi xảy ra. Vui lòng thử lại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt Lại Mật Khẩu - Shop Game</title>
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
                        <h2 class="card-title text-center mb-4">🔑 Đặt Lại Mật Khẩu</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $success; ?>
                            </div>
                            <div class="text-center mt-3">
                                <a href="login.php" class="btn btn-primary">Đăng Nhập Ngay</a>
                            </div>
                        <?php else: ?>
                            <form method="POST" onsubmit="showLoading()">
                                <div class="mb-3">
                                    <label for="password" class="form-label">🔒 Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Nhập mật khẩu mới" required minlength="6">
                                </div>
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">🔒 Xác nhận mật khẩu</label>
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 mb-3">
                                    Đặt Lại Mật Khẩu
                                </button>
                                <div class="text-center">
                                    <a href="login.php" class="back-to-login">← Quay lại đăng nhập</a>
                                </div>
                            </form>
                        <?php endif; ?>
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
