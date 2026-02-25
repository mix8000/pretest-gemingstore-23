<?php
require 'db.php';
require 'auth_check.php';
checkLogin('customer');

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของฉัน - ร้านเกม</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="logo">โปรไฟล์ผู้เล่น</div>
        <div class="nav-links">
            <a href="index.php">ร้านค้า</a>
            <a href="cart.php">ตะกร้า</a>
            <a href="api.php?action=logout" style="color: #ff3333;">ออกจากระบบ</a>
        </div>
    </nav>

    <div class="container" style="padding-top: 4rem;">
        <div style="display: flex; gap: 2rem;">
            <div
                style="flex: 1; min-width: 300px; background: rgba(255,255,255,0.03); padding: 2rem; border-radius: 12px; height: fit-content;">
                <h2 style="color: white; margin-bottom: 1rem;">ข้อมูลผู้เล่น</h2>
                <p style="color: var(--text-muted); margin-bottom: 0.5rem;">ชื่อผู้ใช้</p>
                <div style="font-size: 1.2rem; margin-bottom: 1.5rem; color: var(--neon-blue);">
                    <?= htmlspecialchars($_SESSION['username']) ?>
                </div>

                <p style="color: var(--text-muted); margin-bottom: 0.5rem;">ตำแหน่ง</p>
                <div style="font-size: 1.2rem; margin-bottom: 1.5rem; color: var(--neon-green);">ระดับ:
                    <?= strtoupper($_SESSION['role']) ?>
                </div>
            </div>

            <div style="flex: 2;">
                <h2 style="color: white; margin-bottom: 1.5rem;">ประวัติการสั่งซื้อ</h2>
                <?php if (empty($orders)): ?>
                    <p style="color: var(--text-muted);">ยังไม่มีประวัติการสั่งซื้อ</p>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div
                            style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;">
                            <div
                                style="display: flex; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.5rem;">
                                <span style="color: var(--neon-purple); font-weight: bold;">รายการสั่งซื้อ #
                                    <?= $order['id'] ?>
                                </span>
                                <span style="color: var(--text-muted);">
                                    <?= $order['created_at'] ?>
                                </span>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <?php
                                $stmtItems = $pdo->prepare("SELECT order_items.*, products.name FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_id = ?");
                                $stmtItems->execute([$order['id']]);
                                $items = $stmtItems->fetchAll();
                                foreach ($items as $item) {
                                    echo "<div style='display:flex; justify-content:space-between; margin-bottom: 0.3rem; color: #ddd;'>";
                                    echo "<span>" . htmlspecialchars($item['name']) . " x" . $item['quantity'] . "</span>";
                                    echo "<span>$" . number_format($item['price'], 2) . "</span>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                            <div style="text-align: right; color: var(--neon-green); font-weight: bold; font-size: 1.1rem;">
                                ยอดรวมทั้งหมด: $
                                <?= number_format($order['total_price'], 2) ?>
                            </div>
                            <?php if (!empty($order['address'])): ?>
                                <div
                                    style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-muted); border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.5rem;">
                                    <strong>จุดส่งสินค้า:</strong>
                                    <?= htmlspecialchars($order['address']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>