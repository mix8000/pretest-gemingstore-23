<?php
require 'db.php';
session_start();

// Fetch products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Store</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

    <nav class="navbar">
        <div class="logo">GEMING STORE</div>
        <div class="nav-links">
            <a href="#">Home</a>
            <a href="#products">Gear</a>
            <a href="cart.php">CART (
                <?php
                echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                ?>
                )</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span style="color: var(--neon-green); margin-left: 20px;">[
                    <?php echo htmlspecialchars($_SESSION['username']); ?> ]</span>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php" style="color: var(--neon-purple);">ADMIN PANEL</a>
                <?php endif; ?>
                <a href="api.php?action=logout" style="color: #ff5555;">LOGOUT</a>
            <?php else: ?>
                <a href="login.php" class="btn-admin" style="display:inline-block; border-radius:4px;">LOGIN</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <section class="hero">
            <h1>Level Up Your Gear</h1>
            <p>Premium gaming equipment for the elite. Cyber-enhanced performance.</p>
        </section>

        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>"
                        alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                    <div class="product-info">
                        <span class="product-category"><?= htmlspecialchars($product['category']) ?></span>
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
                        <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-primary">VIEW GEAR</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>

</html>