<?php
session_start();
include '../config.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    // Kiểm tra email đã tồn tại chưa
    $check_email_sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "❌ Email đã tồn tại!";
    } else {
        // Thêm người dùng mới
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $success = "✅ Đăng ký thành công! Đang chuyển hướng...";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
                  </script>";
        } else {
            $error = "❌ Lỗi khi đăng ký!";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        body {
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(76, 0, 159, 0.7), rgba(28, 0, 91, 0.4));
            z-index: -1;
        }
        .card {
            background: rgba(28, 0, 91, 0.4) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
        }
        .card h2 {
            color: #f6d365;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
        }
        .form-label {
            color: #f6d365;
            font-weight: bold;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(147, 112, 219, 0.2);
            color: #fff;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #f6d365;
            color: #fff;
            box-shadow: 0 0 15px rgba(246, 211, 101, 0.3);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
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
        .text-center span {
            color: #f6d365;
        }
        .loading-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(28, 0, 91, 0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            overflow: hidden;
        }
        .loading-title {
            color: #f6d365;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            letter-spacing: 2px;
        }
        .loading-bar-container {
            width: 60%;
            max-width: 500px;
            height: 35px;
            background: rgba(28, 0, 91, 0.4);
            border: 3px solid rgba(147, 112, 219, 0.2);
            border-radius: 20px;
            position: relative;
            box-shadow: 0 0 15px rgba(76, 0, 159, 0.3);
            padding: 3px;
            overflow: visible;
        }
        .loading-bar {
            width: 0%;
            height: 100%;
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            border-radius: 15px;
            transition: width 2s cubic-bezier(0.1, 0.7, 1.0, 0.1);
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.7);
            position: relative;
        }
        .loading-character {
            position: absolute;
            top: -80px;
            left: calc(100% - 25px);
            width: 80px;
            height: 80px;
            background-image: url('../images/changli.png');
            background-size: contain;
            background-repeat: no-repeat;
            transform-origin: bottom;
            z-index: 2;
            filter: drop-shadow(2px 4px 6px rgba(0, 0, 0, 0.5));
            animation: float 1s ease-in-out infinite alternate;
        }
        @keyframes float {
            0% {
                transform: translateY(0px) rotate(0deg);
            }
            100% {
                transform: translateY(-10px) rotate(5deg);
            }
        }
        .loading-character::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            width: 2px;
            height: 30px;
            background: linear-gradient(to bottom, rgba(246, 211, 101, 0.8), transparent);
            transform: translateX(-50%);
            animation: stretchLine 1s ease-in-out infinite alternate;
        }
        @keyframes stretchLine {
            0% {
                height: 30px;
                opacity: 0.8;
            }
            100% {
                height: 40px;
                opacity: 0.4;
            }
        }
        .loading-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.2) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            animation: shine 1.5s infinite;
        }
        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .loading-percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            font-size: 16px;
            z-index: 1;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        }
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border-color: rgba(40, 167, 69, 0.3);
            color: #f6d365;
        }
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border-color: rgba(220, 53, 69, 0.3);
            color: #f6d365;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">
    <video class="video-background" autoplay loop muted playsinline>
        <source src="../images/video/background.mp4" type="video/mp4">
    </video>
    <div class="overlay"></div>

    <div class="loading-container">
        <div class="loading-title">ĐANG XỬ LÝ</div>
        <div class="loading-bar-container">
            <div class="loading-bar">
                <div class="loading-character"></div>
            </div>
            <div class="loading-percentage">0%</div>
        </div>
    </div>

    <div class="card p-4 shadow-lg" style="width: 350px; border-radius: 10px;">
        <h2 class="text-center">🔑 Đăng Ký</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" id="registerForm">
            <div class="mb-3">
                <label class="form-label">👤 Tên người dùng</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">📧 Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">🔒 Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">🔐 Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">💫 Đăng ký</button>
            <div class="text-center mt-3">
                <span>Đã có tài khoản?</span>
                <a href="login.php" class="text-decoration-none">Đăng nhập</a>
            </div>
        </form>
    </div>

    <script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const loadingContainer = document.querySelector('.loading-container');
        const loadingBar = document.querySelector('.loading-bar');
        const percentage = document.querySelector('.loading-percentage');
        const character = document.querySelector('.loading-character');
        
        loadingContainer.style.display = 'flex';
        
        let progress = 0;
        const interval = setInterval(() => {
            progress += 1;
            loadingBar.style.width = progress + '%';
            percentage.textContent = progress + '%';
            
            character.style.left = `calc(${progress}% - 40px)`;
            
            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    this.submit();
                }, 500);
            }
        }, 20);
    });
    </script>
</body>
</html>
