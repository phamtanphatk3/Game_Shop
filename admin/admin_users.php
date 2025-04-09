<?php
include '../config.php';
session_start();

// Kiểm tra nếu chưa đăng nhập hoặc không phải admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Kiểm tra nếu chưa xác minh mật khẩu
if (!isset($_SESSION['verified_admin']) || $_SESSION['verified_admin'] !== true) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = trim($_POST['password']);
        
        // Lấy mật khẩu hash của admin từ database
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        
        // Kiểm tra mật khẩu
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['verified_admin'] = true;
        } else {
            $error = "❌ Mật khẩu không đúng!";
        }
    }

    // Nếu chưa xác minh, hiển thị form nhập mật khẩu
    if (!isset($_SESSION['verified_admin']) || $_SESSION['verified_admin'] !== true) {
        ?>
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <title>Xác Minh Admin</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <style>
                body {
                    margin: 0;
                    height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background: linear-gradient(-45deg, #1d2b64, #f8cdda, #1d2b64, #a18cd1);
                    background-size: 400% 400%;
                    animation: gradientBG 15s ease infinite;
                    color: white;
                }
                @keyframes gradientBG {
                    0% { background-position: 0% 50%; }
                    50% { background-position: 100% 50%; }
                    100% { background-position: 0% 50%; }
                }
                .card {
                    background: rgba(255, 255, 255, 0.1);
                    backdrop-filter: blur(10px);
                    border-radius: 10px;
                    padding: 20px;
                    text-align: center;
                }
                .form-control, .btn {
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
            <div class="card shadow-lg" style="width: 350px;">
                <h2 class="text-warning">🔐 Xác Minh Admin</h2>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nhập mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Xác Minh</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Nếu đã xác minh, hiển thị trang quản lý tài khoản
$sql = "SELECT id, username, email, role FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý tài khoản</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            background: linear-gradient(-45deg, #1d2b64, #f8cdda, #1d2b64, #a18cd1);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: white;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
            max-width: 900px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 10px;
            margin-top: 50px;
            color: white;
        }
        .table th {
            background-color: rgba(0, 123, 255, 0.8);
            color: white;
            text-align: center;
        }
        .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .btn-group .btn:hover {
            transform: scale(1.05);
            transition: 0.2s;
        }
    </style>
</head>
<body>
    <div class="container shadow-lg">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-warning">👤 Quản lý tài khoản</h2>
            <a href="../index.php" class="btn btn-secondary">🏠 Trang chủ</a>
        </div>
        
        <table class="table table-bordered text-center text-white">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên người dùng</th>
                    <th>Email</th>
                    <th>Quyền</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <span class="badge bg-<?php echo ($row['role'] == 'admin') ? 'danger' : 'success'; ?>">
                                <?php echo ucfirst($row['role']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">✏ Sửa</a>
                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">🗑 Xóa</a>
                                <a href="password_reset.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm" onclick="return confirm('Bạn có chắc muốn đặt lại mật khẩu?')">🔑 Reset</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>

        </table>
    </div>
</body>
</html>