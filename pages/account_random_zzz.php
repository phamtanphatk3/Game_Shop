<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "game_shop"; 

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql = "SELECT * FROM accounts WHERE type='RANDOM' AND game_type='zzz'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài Khoản Random Zenless Zone Zero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
            color: #fff; 
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            padding: 20px;
        }
        .card { 
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px; 
            overflow: hidden; 
            transition: all 0.3s ease; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .card-img-container {
            position: relative;
            overflow: hidden;
        }
        .card-img-top { 
            height: 200px; 
            object-fit: cover;
            transition: all 0.5s ease;
        }
        .card:hover .card-img-top {
            transform: scale(1.1);
        }
        .overlay { 
            position: absolute; 
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
        }
        .card:hover .overlay {
            opacity: 1;
        }
        .btn-overlay {
            background: linear-gradient(45deg, #ff8c00, #ff3d00);
            color: white;
            border: none;
            padding: 10px 25px;
            font-size: 16px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }
        .card:hover .btn-overlay {
            transform: translateY(0);
        }
        .btn-overlay:hover {
            background: linear-gradient(45deg, #ff3d00, #ff8c00);
            color: white;
            box-shadow: 0 0 15px rgba(255, 61, 0, 0.5);
        }
        .price {
            font-size: 24px; 
            font-weight: bold; 
            background: linear-gradient(45deg, #ffd700, #ffa500);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 15px 0;
        }
        .btn-custom { 
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: white; 
            border: none;
            border-radius: 25px; 
            font-size: 16px;
            padding: 12px 25px;
            transition: all 0.3s ease;
        }
        .btn-custom:hover { 
            background: linear-gradient(45deg, #2575fc, #6a11cb);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 117, 252, 0.4);
            color: white;
        }
        .page-title {
            color: #ffd700;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 40px;
        }
        .btn-home {
            background: linear-gradient(45deg, #2575fc, #6a11cb);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 30px;
            font-size: 18px;
            transition: all 0.3s ease;
            margin-top: 40px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-home:hover {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 117, 252, 0.4);
            color: white;
        }
        .card-body {
            padding: 20px;
        }
        .username {
            color: #ffd700;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .description {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            margin-bottom: 15px;
            height: 40px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="page-title">🎮 Tài Khoản Random Zenless Zone Zero 🎮</h1>
        
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-img-container">
                            <img src="<?= !empty($row['image']) ? '../images/' . htmlspecialchars($row['image']) : '../images/default.png' ?>" 
                                 class="card-img-top" 
                                 alt="Account Image">
                            <div class="overlay">
                                <a href="chitiet.php?id=<?= $row['id'] ?>" class="btn-overlay">
                                    <i class="fas fa-search"></i> Xem Chi Tiết
                                </a>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="username"><?= htmlspecialchars($row['username']) ?></h5>
                            <?php if (!empty($row['description'])): ?>
                                <p class="description"><?= htmlspecialchars($row['description']) ?></p>
                            <?php endif; ?>
                            <p class="price"><?= number_format($row['price']) ?> VNĐ</p>
                            <a href="mua.php?id=<?= $row['id'] ?>" class="btn btn-custom w-100">
                                <i class="fas fa-shopping-cart"></i> Mua Ngay
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="text-center">
            <a href="../index.php" class="btn-home">
                <i class="fas fa-home"></i> Quay về trang chủ
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?> 