<?php
session_start();
include '../config.php';

// Kiểm tra bảng visitors có tồn tại không
$table_check = $conn->query("SHOW TABLES LIKE 'visitors'");
$visitors_table_exists = $table_check->num_rows > 0;

// Doanh thu tổng
$result = $conn->query("SELECT SUM(amount) AS total_revenue FROM transactions");
$row = $result->fetch_assoc();
$total_revenue = $row['total_revenue'] ?? 0;

// Số người truy cập
$total_visitors = 0;
if ($visitors_table_exists) {
    $result = $conn->query("SELECT COUNT(*) AS total_visitors FROM visitors");
    $row = $result->fetch_assoc();
    $total_visitors = $row['total_visitors'] ?? 0;
}

// Lấy dữ liệu doanh thu theo ngày
$query = "SELECT DATE(created_at) AS date, SUM(amount) AS daily_revenue 
          FROM transactions 
          GROUP BY DATE(created_at) 
          ORDER BY DATE(created_at)";

$result = $conn->query($query);
$dates = [];
$revenues = [];

while ($row = $result->fetch_assoc()) {
    $dates[] = $row['date'];
    $revenues[] = $row['daily_revenue'];
}

// Lấy dữ liệu doanh thu theo giờ trong ngày hôm nay
$today = date('Y-m-d');
$query = "SELECT HOUR(created_at) AS hour, SUM(amount) AS hourly_revenue 
          FROM transactions 
          WHERE DATE(created_at) = '$today' 
          GROUP BY HOUR(created_at) 
          ORDER BY HOUR(created_at)";

$result = $conn->query($query);
$hours = array_fill(0, 24, 0); // Tạo mảng 24 giờ (00-23) với giá trị mặc định là 0

while ($row = $result->fetch_assoc()) {
    $hours[$row['hour']] = $row['hourly_revenue']; // Gán doanh thu theo giờ
}

// Chuyển dữ liệu sang JSON để sử dụng trong JavaScript
$dates_json = json_encode($dates);
$revenues_json = json_encode($revenues);
$hours_json = json_encode(array_keys($hours));
$hourly_revenues_json = json_encode(array_values($hours));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống Kê Doanh Thu</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            background: transparent;
            min-height: 100vh;
        }

        .container {
            margin-top: 50px;
            animation: fadeInUp 0.5s ease-out;
        }

        h1 {
            color: #f6d365;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
            margin-bottom: 30px;
        }

        .card {
            background: rgba(28, 0, 91, 0.4) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 30px rgba(76, 0, 159, 0.5);
        }

        .bg-success, .bg-info {
            background: linear-gradient(45deg, #28a745, #20c997) !important;
            border: none;
        }

        .bg-info {
            background: linear-gradient(45deg, #17a2b8, #0dcaf0) !important;
        }

        .btn-warning {
            background: linear-gradient(45deg, #f6d365, #fda085);
            border: none;
            box-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(246, 211, 101, 0.7);
        }

        canvas {
            margin: 20px 0;
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

        .chart-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
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
        <h1 class="text-center">📊 Báo Cáo Doanh Thu 📊</h1>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card text-white text-center p-4">
                    <h3>💰 Tổng Doanh Thu</h3>
                    <h2><?php echo number_format($total_revenue, 2); ?> VNĐ</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white text-center p-4">
                    <h3>👥 Tổng Người Truy Cập</h3>
                    <h2><?php echo number_format($total_visitors); ?></h2>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card p-4">
                    <h3 class="text-center">📈 Doanh Thu Theo Ngày</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-4">
                    <h3 class="text-center">📊 Doanh Thu Theo Giờ</h3>
                    <canvas id="hourlyRevenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="../index.php" class="btn btn-warning btn-lg">🏠 Quay Lại Trang Chủ</a>
        </div>
    </div>

    <script>
        // Cập nhật style cho biểu đồ
        const chartOptions = {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#fff'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#fff'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#fff'
                    }
                }
            }
        };

        // Biểu đồ doanh thu theo ngày
        const dates = <?php echo $dates_json; ?>;
        const revenues = <?php echo $revenues_json; ?>;

        const ctx1 = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenues,
                    backgroundColor: 'rgba(246, 211, 101, 0.7)',
                    borderColor: 'rgba(246, 211, 101, 1)',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });

        // Biểu đồ doanh thu theo giờ
        const hours = <?php echo $hours_json; ?>;
        const hourlyRevenues = <?php echo $hourly_revenues_json; ?>;

        const ctx2 = document.getElementById('hourlyRevenueChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: hours.map(h => `${h}:00`),
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: hourlyRevenues,
                    backgroundColor: 'rgba(147, 112, 219, 0.7)',
                    borderColor: 'rgba(147, 112, 219, 1)',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });
    </script>
</body>
</html>
