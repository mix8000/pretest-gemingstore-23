<?php
require 'db.php';
session_start();

// Search and Filter logic
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if (!empty($search)) {
    $sql .= " AND name LIKE ?";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch categories for filter
$cats = $pdo->query("SELECT DISTINCT category FROM products")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ร้านเกม</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .toast {
            position: fixed;
            top: 100px;
            right: 20px;
            background: rgba(0, 255, 136, 0.9);
            color: black;
            padding: 1rem 2rem;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.4);
            z-index: 1000;
            display: none;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <?php if (isset($_GET['added'])): ?>
        <div id="toast" class="toast" style="display: block;">เพิ่มสินค้าลงในคลังแล้ว!</div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast');
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        </script>
    <?php endif; ?>

    <?php if (isset($_GET['order_success'])): ?>
        <div id="toast" class="toast" style="display: block; background: var(--neon-purple); color: white;">
            สั่งซื้อสินค้าสำเร็จแล้ว!</div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast');
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        </script>
    <?php endif; ?>

    <nav class="navbar">
        <div class="logo">ร้านเกม</div>
        <div class="nav-links">
            <a href="#">หน้าแรก</a>
            <a href="#products">สินค้า</a>
            <a href="cart.php">ตะกร้า (
                <?php
                echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                ?>
                )</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span style="color: var(--neon-green); margin-left: 20px;">[
                    <?php echo htmlspecialchars($_SESSION['username']); ?> ]</span>
                <a href="profile.php" style="color: var(--neon-blue);">โปรไฟล์</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php" style="color: var(--neon-purple);">สำหรับผู้ดูแล</a>
                <?php endif; ?>
                <a href="api.php?action=logout" style="color: #ff5555;">ออกจากระบบ</a>
            <?php else: ?>
                <a href="login.php" class="btn-admin" style="display:inline-block; border-radius:4px;">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <section class="hero">
            <h1>อัปเกรดอุปกรณ์ของคุณ</h1>
            <p>อุปกรณ์เกมระดับพรีเมี่ยมสำหรับคนพิเศษ ประสิทธิภาพเสริมไซเบอร์</p>

            <form method="GET"
                style="margin-top: 2rem; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                <input type="text" name="search" placeholder="ค้นหาสินค้า..." value="<?= htmlspecialchars($search) ?>"
                    style="padding: 0.8rem; width: 300px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.5); color: white;">

                <select name="category" onchange="this.form.submit()"
                    style="padding: 0.8rem; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.5); color: white;">
                    <option value="">ทุกหมวดหมู่</option>
                    <?php foreach ($cats as $c): ?>
                        <option value="<?= $c ?>" <?= $category === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn-neon"
                    style="width: auto; margin-top: 0; padding: 0.8rem 2rem;">ค้นหา</button>
            </form>
        </section>

        <div class="product-grid" id="products">
            <?php if (empty($products)): ?>
                <p style="grid-column: 1/-1; text-align: center; color: var(--text-muted);">
                    ไม่พบสินค้าที่ตรงกับการค้นหาของคุณ</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <div class="product-info">
                            <span class="product-category"><?= htmlspecialchars($product['category']) ?></span>
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-primary">ดูรายละเอียด</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>