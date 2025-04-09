<?php
include '../config.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin_users.php");
    exit;
}

$id = $_GET['id'];
$sql = "SELECT username, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username, $email, $role);
$stmt->fetch();
$stmt->close();

// Cập nhật tài khoản
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_role = $_POST['role'];

    $update_sql = "UPDATE users SET role = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_role, $id);

    if ($update_stmt->execute()) {
        header("Location: admin_users.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa tài khoản</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Chỉnh sửa tài khoản</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tên người dùng</label>
                <input type="text" class="form-control" value="<?php echo $username; ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?php echo $email; ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Quyền</label>
                <select name="role" class="form-control">
                    <option value="user" <?php if ($role == 'user') echo 'selected'; ?>>User</option>
                    <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Lưu thay đổi</button>
            <a href="admin_users.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>
</html>
