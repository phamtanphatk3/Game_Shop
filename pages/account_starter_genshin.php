<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "game_shop"; 

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql = "SELECT * FROM accounts WHERE type='STARTER' AND game_type='genshin'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách tài khoản Starter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: #121212; 
            color: #fff; 
            font-family: 'Arial', sans-serif; 
        }
        .container {
            max-width: 1100px;
        }
        .card { 
            background: #1e1e1e; 
            border-radius: 12px; 
            overflow: hidden; 
            transition: 0.3s; 
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.1);
            position: relative;
        }
        .card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.2); 
        }
        .card-img-container {
            position: relative;
        }
        .card-img-top { 
            height: 200px; 
            object-fit: cover; 
            border-bottom: 2px solid #ff8c00;
            width: 100%;
        }
        .overlay { 
            position: absolute; 
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: 0.3s;
        }
        .card-img-container:hover .overlay {
            opacity: 1;
        }
        .btn-overlay {
            background: rgba(255, 140, 0, 0.9);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-overlay:hover {
            background: rgba(255, 61, 0, 0.9);
        }
        .price {
            font-size: 22px; 
            font-weight: bold; 
            color: #ffcc00;
        }
        .btn-custom { 
            background: linear-gradient(135deg, #ff8c00, #ff3d00); 
            color: white; 
            border-radius: 8px; 
            font-size: 18px;
            transition: 0.3s;
        }
        .btn-custom:hover { 
            transform: scale(1.05); 
            background: linear-gradient(135deg, #ff3d00, #ff8c00); 
        }
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        .btn-home {
            font-size: 18px;
            padding: 12px 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-warning text-center">⭐ Danh Sách Tài Khoản Starter ⭐</h2>
        <div class="row mt-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-img-container">
                            <img src="<?= !empty($row['image']) ? '../images/' . htmlspecialchars($row['image']) : '../images/default.png' ?>" 
                                 class="card-img-top" 
                                 alt="Account Image">
                            <div class="overlay">
                                <a href="chitiet.php?id=<?= $row['id'] ?>" class="btn-overlay">🔍 Xem Chi Tiết</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="text-warning"><?= htmlspecialchars($row['username']) ?></h5>
                            <p class="price"><?= number_format($row['price']) ?> VNĐ</p>
                            <a href="mua.php?id=<?= $row['id'] ?>" class="btn btn-custom w-100">🔹 Mua Ngay</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Nút quay về trang chủ -->
        <div class="btn-container">
            <a href="../index.php" class="btn btn-primary btn-home">🏠 Quay về trang chủ</a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?> 