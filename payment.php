<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    header('Location: index.php');
    exit;
}

// Fetch order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    echo "ไม่พบรายการสั่งซื้อ";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน - Pixel Power</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Oswald:wght@300;400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo">Pixel Power</div>
        <div class="nav-links">
            <a href="index.php">หน้าแรก</a>
            <a href="cart.php">ตะกร้า</a>
            <a href="profile.php" style="color: var(--neon-blue);">โปรไฟล์ [
                <?php echo htmlspecialchars($_SESSION['username']); ?>]
            </a>
        </div>
    </nav>

    <div class="container" style="max-width: 600px; margin-top: 3rem;">
        <div class="login-container" style="max-width: 100%;">
            <h1 style="color: var(--neon-blue); text-align: center; margin-bottom: 2rem;">ชำระเงิน</h1>

            <div
                style="background: rgba(0,0,0,0.3); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid var(--neon-green);">
                <h3 style="color: var(--neon-green);">เลขที่คำสั่งซื้อ: #
                    <?php echo $order['id']; ?>
                </h3>
                <p>จำนวนเงินที่ต้องชำระ: <strong style="color: #fff; font-size: 1.2rem;">$
                        <?php echo number_format($order['total_price'], 2); ?>
                    </strong></p>
            </div>

            <div style="margin-bottom: 2rem;">
                <h3 style="color: #fff; margin-bottom: 1rem;">ช่องทางการชำระเงิน</h3>
                <p style="color: var(--text-muted);">ธนาคารกสิกรไทย (K-Bank)<br>
                    ชื่อบัญชี: บจก. พิกเซล พาวเวอร์<br>
                    เลขที่บัญชี: 123-4-56789-0</p>
            </div>

            <form action="api.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_slip">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

                <div class="form-group">
                    <label style="color: var(--text-muted); display: block; margin-bottom: 0.5rem;">แนบหลักฐานการโอนเงิน
                        (Slip)</label>
                    <input type="file" name="slip" class="form-control" accept="image/*" required
                        style="padding: 0.5rem;">
                </div>

                <button type="submit" class="btn-neon"
                    style="width: 100%; padding: 1.2rem; margin-top: 1.5rem; display: block; position: relative; z-index: 10; cursor: pointer;">
                    <i class="fas fa-paper-plane" style="margin-right: 10px;"></i> แจ้งชำระเงิน
                </button>
            </form>

            <?php if (isset($_GET['error'])): ?>
                <p style="color: #ff5555; text-align: center; margin-top: 1rem;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>