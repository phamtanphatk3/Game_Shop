<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Truy vấn thông tin người dùng
$sql = "SELECT username, email, balance, avatar FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($username, $email, $balance, $avatar);
$stmt->fetch();
$stmt->close();

// Xử lý username để bỏ @gmail.com nếu là tài khoản Google
if ($username && strpos($username, '@gmail.com') !== false) {
    $display_name = substr($username, 0, strpos($username, '@gmail.com'));
} else {
    $display_name = $username;
}

// Đảm bảo balance không bị null
if ($balance === null) {
    $balance = 0;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ Sơ Cá Nhân</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: transparent;
        }

        .card {
            background: rgba(28, 0, 91, 0.4) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
            transition: all 0.3s ease;
            color: white;
            padding: 30px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 30px rgba(76, 0, 159, 0.5);
            background: rgba(28, 0, 91, 0.6) !important;
        }

        h2 {
            color: #f6d365;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
            margin-bottom: 30px;
            animation: fireAnimation 3s ease infinite;
        }

        .btn {
            transition: all 0.3s ease;
            padding: 10px 25px;
            border-radius: 20px;
            font-weight: bold;
            margin: 0 5px;
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

        .btn-light {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            border: none;
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.3);
            color: white;
        }

        .btn-light:hover {
            box-shadow: 0 0 15px rgba(106, 17, 203, 0.5);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
            border: none;
            box-shadow: 0 0 10px rgba(255, 107, 107, 0.5);
        }

        .btn-danger:hover {
            box-shadow: 0 0 15px rgba(255, 107, 107, 0.7);
        }

        p {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        p strong {
            color: #f6d365;
            text-shadow: 0 0 5px rgba(246, 211, 101, 0.3);
        }

        .balance {
            font-size: 1.3rem;
            color: #f6d365;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
        }

        .avatar-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 30px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid rgba(147, 112, 219, 0.3);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
            transition: all 0.3s ease;
        }

        .avatar-container:hover {
            transform: scale(1.05);
            border-color: #f6d365;
            box-shadow: 0 0 30px rgba(246, 211, 101, 0.5);
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .default-avatar {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            font-size: 50px;
            color: white;
        }

        /* Animation cho card */
        .card {
            animation: fadeInUp 0.5s ease-out;
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

        @keyframes fireAnimation {
            0% {
                text-shadow: 0 0 10px rgba(255, 107, 107, 0.5),
                           0 0 20px rgba(255, 107, 107, 0.3),
                           0 0 30px rgba(255, 107, 107, 0.2);
            }
            50% {
                text-shadow: 0 0 15px rgba(254, 202, 87, 0.5),
                           0 0 25px rgba(254, 202, 87, 0.3),
                           0 0 35px rgba(254, 202, 87, 0.2);
            }
            100% {
                text-shadow: 0 0 10px rgba(255, 159, 67, 0.5),
                           0 0 20px rgba(255, 159, 67, 0.3),
                           0 0 30px rgba(255, 159, 67, 0.2);
            }
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <video class="video-background" autoplay muted loop playsinline>
        <source src="../images/video/background.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center">👤 Hồ Sơ Cá Nhân</h2>
                <div class="card">
                    <div class="avatar-container">
                        <?php if (!empty($avatar) && file_exists('uploads/' . basename($avatar))): ?>
                            <img src="uploads/<?php echo htmlspecialchars(basename($avatar)); ?>" alt="Avatar" class="avatar-image">
                        <?php else: ?>
                            <div class="default-avatar">
                                <?php echo strtoupper(substr($display_name, 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <p><strong>Tên người dùng:</strong> <?php echo htmlspecialchars($display_name); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Số dư hiện tại:</strong> <span class="balance"><?php echo number_format($balance, 2); ?> VNĐ</span></p>

                    <div class="d-flex justify-content-center mt-4">
                        <a href="edit-profile.php" class="btn btn-warning">✏️ Chỉnh sửa hồ sơ</a>
                        <a href="../index.php" class="btn btn-light">🏠 Trang chủ</a>
                        <a href="logout.php" class="btn btn-danger">🚪 Đăng Xuất</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
