<?php
require 'db.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "ไม่พบสินค้า";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($product['name']) ?> - ร้านเกม
    </title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="logo">ร้านเกม</div>
        <div class="nav-links">
            <a href="index.php">หน้าแรก</a>
            <a href="cart.php">ตะกร้า (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" style="color: var(--neon-blue);">โปรไฟล์ [<?php echo htmlspecialchars($_SESSION['username']); ?>]</a>
            <?php else: ?>
                <a href="login.php" class="btn-admin" style="display:inline-block; border-radius:4px;">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container" style="padding-top: 2rem; min-height: 80vh;">
        <!-- Breadcrumbs -->
        <div style="margin-bottom: 2rem; font-size: 0.9rem; color: var(--text-muted);">
            <a href="index.php" style="color: var(--neon-blue); text-decoration: none;">หน้าแรก</a> / 
            <a href="index.php?category=<?= urlencode($product['category']) ?>" style="color: var(--neon-blue); text-decoration: none;"><?= htmlspecialchars($product['category']) ?></a> / 
            <?= htmlspecialchars($product['name']) ?>
        </div>

        <div style="display: flex; gap: 4rem; flex-wrap: wrap; background: rgba(255,255,255,0.02); padding: 3rem; border-radius: 20px; border: var(--glass-border);">
            <div style="flex: 1.2; min-width: 400px;">
                <div style="position: sticky; top: 120px;">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>"
                        style="width: 100%; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 0 50px rgba(0,0,0,0.6);"
                        alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
            </div>
            
            <div style="flex: 1; min-width: 350px; display: flex; flex-direction: column;">
                <span class="product-category" style="font-size: 1.1rem; margin-bottom: 1rem;">
                    <?= htmlspecialchars($product['category']) ?>
                </span>
                <h1 style="font-size: 3.5rem; margin-bottom: 1.5rem; color: white; line-height: 1.1; text-transform: none; font-family: 'Inter'; font-weight: 700;">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>
                
                <div style="display: flex; align-items: baseline; gap: 1rem; margin-bottom: 2rem;">
                    <span style="font-size: 2.5rem; color: var(--neon-green); font-weight: 700;">$<?= number_format($product['price'], 2) ?></span>
                    <span style="color: var(--text-muted); text-decoration: line-through; font-size: 1.2rem;">$<?= number_format($product['price'] * 1.2, 2) ?></span>
                </div>

                <div style="margin-bottom: 3rem;">
                    <h4 style="color: white; margin-bottom: 1rem; font-family: 'Orbitron'; font-size: 0.9rem; letter-spacing: 1px;">ข้อมูลรายละเอียดสินค้า</h4>
                    <div style="line-height: 1.8; color: var(--text-main); font-size: 1.05rem; background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 10px; border-left: 3px solid var(--neon-blue);">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </div>
                </div>

                <div style="margin-top: auto; background: rgba(255,255,255,0.03); padding: 2rem; border-radius: 15px; border: 1px solid rgba(255,255,255,0.05);">
                    <form action="api.php" method="POST">
                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                        <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                            <label style="color: white; font-weight: 600;">จำนวน:</label>
                            <input type="number" name="quantity" value="1" min="1" class="form-control"
                                style="width: 100px; text-align: center; border-radius: 30px; background: rgba(255,255,255,0.05);">
                        </div>

                        <button type="submit" class="btn-neon" style="padding: 1.2rem; border-radius: 50px; font-size: 1.1rem;">
                            <i class="fas fa-shopping-cart" style="margin-right: 10px;"></i> เพิ่มลงในคลังแสง
                        </button>
                    </form>
                    
                    <p style="margin-top: 1.5rem; font-size: 0.85rem; color: var(--text-muted); text-align: center;">
                        <i class="fas fa-truck" style="color: var(--neon-blue);"></i> จัดส่งภายใน 3-5 วันทำการ | รับประกันสินค้า 1 ปี
                    </p>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>