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
    $username = $_POST['username'];
    $password = $_POST['password'];
    $price = $_POST['price'];
    $type = $_POST['type'];
    $game_type = $_POST['game_type'];
    $image = $_POST['image'];
    $description = $_POST['description'];

    // Chuẩn bị câu lệnh SQL với prepared statement
    $sql = "INSERT INTO accounts (username, password, price, type, game_type, image, description) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssss", $username, $password, $price, $type, $game_type, $image, $description);

    if ($stmt->execute()) {
        header("Location: manage_accounts.php?game=" . $game_type . "&success=1");
    } else {
        header("Location: manage_accounts.php?game=" . $game_type . "&error=1");
    }

    $stmt->close();
}

$conn->close();
?>
