<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "game_shop"; 

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $price = $_POST['price'];
    $type = $_POST['type'];
    $game_type = $_POST['game_type'];
    $image = $_POST['image'];
    $description = $_POST['description'];

    // Chuẩn bị câu lệnh SQL với prepared statement
    $sql = "UPDATE accounts SET username=?, password=?, price=?, type=?, game_type=?, image=?, description=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssssi", $username, $password, $price, $type, $game_type, $image, $description, $id);

    if ($stmt->execute()) {
        header("Location: manage_accounts.php?game=" . $game_type);
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
} else if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM accounts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();
    
    if (!$account) {
        header("Location: manage_accounts.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛠 Sửa tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
            color: white;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 500px;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .form-select option {
            background: #1a1a2e;
            color: white;
        }
        .btn {
            transition: 0.3s;
            border-radius: 8px;
        }
        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.2);
        }
        h2 {
            text-shadow: 2px 2px 5px rgba(255, 255, 255, 0.3);
        }
        label {
            margin-top: 15px;
            margin-bottom: 5px;
            color: #f6d365;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-warning text-center mb-4">✏️ Sửa tài khoản</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Tên tài khoản</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($account['username']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Mật khẩu</label>
                <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($account['password']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Giá tiền (VNĐ)</label>
                <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($account['price']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Hình ảnh (tên file)</label>
                <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($account['image']) ?>">
            </div>

            <div class="mb-3">
                <label>Loại tài khoản</label>
                <select name="type" class="form-select">
                    <option value="VIP" <?= $account['type'] == 'VIP' ? 'selected' : '' ?>>VIP</option>
                    <option value="STARTER" <?= $account['type'] == 'STARTER' ? 'selected' : '' ?>>STARTER</option>
                    <option value="REROLL" <?= $account['type'] == 'REROLL' ? 'selected' : '' ?>>REROLL</option>
                    <option value="RANDOM" <?= $account['type'] == 'RANDOM' ? 'selected' : '' ?>>RANDOM</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Loại game</label>
                <select name="game_type" class="form-select">
                    <option value="genshin" <?= $account['game_type'] == 'genshin' ? 'selected' : '' ?>>Genshin Impact</option>
                    <option value="honkai" <?= $account['game_type'] == 'honkai' ? 'selected' : '' ?>>Honkai: Star Rail</option>
                    <option value="zzz" <?= $account['game_type'] == 'zzz' ? 'selected' : '' ?>>Zenless Zone Zero</option>
                    <option value="www" <?= $account['game_type'] == 'www' ? 'selected' : '' ?>>Wuthering Waves</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Mô tả tài khoản</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($account['description'] ?? '') ?></textarea>
            </div>

            <input type="hidden" name="id" value="<?= $account['id'] ?>">

            <div class="mt-4">
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-save-fill"></i> Lưu thay đổi
                </button>
                <a href="manage_accounts.php?game=<?= htmlspecialchars($account['game_type']) ?>" class="btn btn-secondary w-100 mt-2">
                    <i class="bi bi-arrow-left-circle"></i> Quay lại
                </a>
            </div>
        </form>
    </div>
</body>
</html>
