<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "game_shop"; 

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra nếu có ID được truyền vào
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Không tìm thấy tài khoản!");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM accounts WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Tài khoản không tồn tại!");
}

$account = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Tài Khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: #121212; 
            color: #fff; 
            font-family: 'Arial', sans-serif; 
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .card { 
            background: #1e1e1e; 
            border-radius: 12px; 
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.1);
            padding: 20px;
            text-align: center;
        }
        .card img { 
            width: 100%; 
            height: auto; 
            border-radius: 12px;
            border-bottom: 3px solid #ff8c00;
        }
        .price {
            font-size: 24px; 
            font-weight: bold; 
            color: #ffcc00;
        }
        .description {
            font-size: 18px;
            font-weight: bold;
            color: #00ffff;
            text-shadow: 2px 2px 5px rgba(0, 255, 255, 0.5);
            background: rgba(0, 255, 255, 0.1);
            padding: 10px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 15px;
        }
        .btn-custom { 
            background: linear-gradient(135deg, #ff8c00, #ff3d00); 
            color: white; 
            border-radius: 8px; 
            font-size: 18px;
            transition: 0.3s;
            padding: 12px 20px;
            display: block;
            margin-top: 15px;
            text-decoration: none;
        }
        .btn-custom:hover { 
            transform: scale(1.05); 
            background: linear-gradient(135deg, #ff3d00, #ff8c00); 
        }
        .btn-back {
            margin-top: 20px;
            display: block;
            text-align: center;
            font-size: 18px;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border-radius: 8px;
            text-decoration: none;
        }
        .btn-back:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <img src="<?= !empty($account['image']) ? '../images/' . htmlspecialchars($account['image']) : '../images/default.png' ?>" alt="Account Image">
            <h2 class="text-warning mt-3"><?= htmlspecialchars($account['username']) ?></h2>
            <p class="price"><?= number_format($account['price']) ?> VNĐ</p>
            <p class="description"><?= nl2br(htmlspecialchars($account['description'])) ?></p>
            <a href="mua.php?id=<?= $account['id'] ?>" class="btn-custom">🛒 Mua Ngay</a>
            <a href="account_vip_genshin.php" class="btn-back">⬅ Quay lại</a>
        </div>
    </div>
</body>
</html>
