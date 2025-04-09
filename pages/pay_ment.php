<?php
include '../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần đăng nhập để thanh toán! <a href='login.php'>Đăng nhập</a>";
    exit;
}

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $user_id = $_SESSION['user_id'];

    // Lấy thông tin đơn hàng
    $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $total_price = $order['total_price'];
    } else {
        echo "Không tìm thấy đơn hàng hợp lệ!";
        exit;
    }
} else {
    echo "Không tìm thấy đơn hàng!";
    exit;
}
?>

<h2>Thanh toán đơn hàng</h2>
<p>Số tiền cần thanh toán: <?php echo number_format($total_price, 0, ',', '.'); ?> VNĐ</p>

<form action="process_payment.php" method="POST">
    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
    <label for="payment_method">Chọn phương thức thanh toán:</label>
    <select name="payment_method" required>
        <option value="momo">Momo</option>
        <option value="zalopay">ZaloPay</option>
        <option value="bank">Ngân hàng</option>
        <option value="card">Thẻ tín dụng</option>
    </select>
    <button type="submit">Thanh toán</button>
</form>
