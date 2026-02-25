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
    <title>Pixel Power</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <div class="logo">Pixel Power</div>
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
        <!-- New Hero Banner -->
        <div class="hero-banner">
            <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2000&auto=format&fit=crop"
                alt="Gaming Banner">
            <div class="hero-content-overlay">
                <p style="color: var(--neon-green); font-family: 'Orbitron'; margin-bottom: 0.5rem;">WELCOME TO THE NEXT
                    LEVEL</p>
                <h2>ศูนย์รวมอุปกรณ์เกมมิ่งเกียร์</h2>
                <p>ยกระดับประสิทธิภาพการเล่นเกมด้วยเทคโนโลยีระดับโปร</p>
            </div>
        </div>

        <section class="hero" style="padding-top: 0;">
            <form method="GET"
                style="margin-bottom: 3rem; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                <input type="text" name="search" placeholder="ค้นหาสินค้า..." value="<?= htmlspecialchars($search) ?>"
                    style="padding: 0.8rem; width: 400px; border-radius: 30px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.5); color: white; padding-left: 1.5rem;">
                <button type="submit" class="btn-neon"
                    style="width: auto; margin-top: 0; padding: 0.8rem 2.5rem; border-radius: 30px;">ค้นหา</button>
            </form>

            <div class="category-pills">
                <a href="index.php" class="category-pill <?= empty($category) ? 'active' : '' ?>">ทั้งหมด</a>
                <?php foreach ($cats as $c): ?>
                    <a href="index.php?category=<?= urlencode($c) ?>&search=<?= urlencode($search) ?>"
                        class="category-pill <?= $category === $c ? 'active' : '' ?>">
                        <?= htmlspecialchars($c) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="product-grid" id="products">
            <?php if (empty($products)): ?>
                <p style="grid-column: 1/-1; text-align: center; color: var(--text-muted);">
                    ไม่พบสินค้าที่ตรงกับการค้นหาของคุณ</p>
            <?php else: ?>
                <?php foreach ($products as $index => $product): ?>
                    <div class="product-card">
                        <?php if ($index % 3 == 0): ?>
                            <span class="badge badge-sale">ลดราคา</span>
                        <?php elseif ($index == 0): ?>
                            <span class="badge badge-new">ใหม่</span>
                        <?php endif; ?>

                        <img src="<?= htmlspecialchars($product['image_url']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <div class="product-info" style="flex-grow: 1; display: flex; flex-direction: column;">
                            <span class="product-category"><?= htmlspecialchars($product['category']) ?></span>
                            <h3 style="margin-bottom: 1rem; line-height: 1.4;"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="product-price" style="margin-top: auto; color: var(--neon-green);">
                                $<?= number_format($product['price'], 2) ?></p>
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-primary">ดูรายละเอียด</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    </div>

    <?php include 'footer.php'; ?>

</body>

</html>