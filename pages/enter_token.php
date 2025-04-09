<?php
session_start();
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $token = $_POST['token'];

    // Kiểm tra token hợp lệ
    $sql = "SELECT email FROM password_resets WHERE email = ? AND token = ? AND expires > ?";
    $stmt = $conn->prepare($sql);
    $expires = time();
    $stmt->bind_param("sii", $email, $token, $expires);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['reset_email'] = $email;
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "❌ Mã xác thực không hợp lệ hoặc đã hết hạn!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận mã</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">🔑 Nhập mã xác nhận</h2>
    <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
    
    <form method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
        <input type="text" name="token" class="form-control mb-2" placeholder="Nhập mã xác thực" required>
        <button type="submit" class="btn btn-primary w-100">Xác nhận</button>
    </form>
</div>
</body>
</html>
