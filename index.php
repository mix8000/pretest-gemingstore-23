<?php
require 'db.php';
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeonGrid | Gaming Store</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav class="navbar">
        <div class="logo">NEON<span style="color:white">GRID</span></div>
        <div class="nav-links">
            <a href="index.php">Store</a>
            <a href="admin.php" class="btn-admin">Admin Panel</a>
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
                        <a href="#" class="btn btn-primary">ADD TO CART</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>

</html>