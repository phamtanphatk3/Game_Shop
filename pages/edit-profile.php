<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: pages/login.php");
    exit();
}

// Tạo thư mục uploads nếu chưa tồn tại
$upload_dir = __DIR__ . '/uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($username, $email, $avatar);
$stmt->fetch();
$stmt->close();

$success_message = '';

// Xử lý cập nhật hồ sơ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = trim($_POST['username']);
    $newEmail = trim($_POST['email']);
    $has_changes = false;
    
    if (!empty($newUsername) && !empty($newEmail)) {
        $updateStmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $updateStmt->bind_param("ssi", $newUsername, $newEmail, $userId);
        if ($updateStmt->execute()) {
            $username = $newUsername;
            $email = $newEmail;
            $_SESSION['username'] = $newUsername;
            $has_changes = true;
        }
        $updateStmt->close();
    }

    // Cập nhật ảnh đại diện
    if (isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {
        $file_name = time() . "_" . basename($_FILES["avatar"]["name"]);
        $target_file = $upload_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $relative_path = "uploads/" . $file_name;

        $allowed_types = ["jpg", "jpeg", "png"];
        if (in_array($imageFileType, $allowed_types) && getimagesize($_FILES["avatar"]["tmp_name"])) {
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                // Xóa ảnh cũ nếu tồn tại
                if (!empty($avatar)) {
                    $old_file = __DIR__ . '/uploads/' . basename($avatar);
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }

                $updateAvatarStmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $updateAvatarStmt->bind_param("si", $relative_path, $userId);
                if ($updateAvatarStmt->execute()) {
                    $avatar = $relative_path;
                    $_SESSION['avatar'] = $relative_path;
                    $has_changes = true;
                }
                $updateAvatarStmt->close();
            }
        }
    }

    if ($has_changes) {
        echo "<script>
            alert('Lưu Thành Công!!!');
            window.location.href = 'profile.php';
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa hồ sơ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Style cho video background */
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
            border-radius: 15px;
            padding: 30px;
            color: white;
        }

        h2 {
            color: #f6d365;
            font-size: 2.5rem;
            text-shadow: 0 0 15px rgba(246, 211, 101, 0.5);
            margin-bottom: 30px;
            animation: fireAnimation 3s ease infinite;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(147, 112, 219, 0.2);
            color: white;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #f6d365;
            box-shadow: 0 0 15px rgba(246, 211, 101, 0.3);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        label {
            color: #f6d365;
            font-weight: bold;
            margin-bottom: 8px;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.3);
        }

        .btn-custom {
            border: none !important;
            padding: 12px 30px;
            border-radius: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            margin: 5px;
        }

        .btn-success.btn-custom {
            background: linear-gradient(45deg, #28a745, #20c997);
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
        }

        .btn-light.btn-custom {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.3);
            color: white;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
        }

        .btn-success.btn-custom:hover {
            box-shadow: 0 0 20px rgba(40, 167, 69, 0.5);
        }

        .btn-light.btn-custom:hover {
            box-shadow: 0 0 20px rgba(106, 17, 203, 0.5);
        }

        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px;
            border: 3px solid rgba(147, 112, 219, 0.3);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
            transition: all 0.3s ease;
            position: relative;
        }

        .avatar-preview:hover {
            transform: scale(1.05);
            border-color: #f6d365;
            box-shadow: 0 0 30px rgba(246, 211, 101, 0.5);
        }

        .avatar-preview img {
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
        <source src="../video/Wuthering Waves.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <h2 class="text-center">✏️ Chỉnh Sửa Hồ Sơ</h2>
                    
                    <div class="avatar-preview">
                        <?php if (!empty($avatar)): ?>
                            <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar">
                        <?php else: ?>
                            <div class="default-avatar">
                                <?php echo strtoupper(substr($username, 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <form action="edit-profile.php" method="post" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="username">👤 Tên người dùng:</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="email">📧 Email:</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="avatar">🖼️ Chọn ảnh đại diện mới:</label>
                            <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success btn-custom">
                                💾 Lưu Thay Đổi
                            </button>
                            <a href="profile.php" class="btn btn-light btn-custom">
                                ⬅️ Quay Lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
