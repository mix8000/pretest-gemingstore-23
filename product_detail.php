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
    echo "Product not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($product['name']) ?> - Gaming Store
    </title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="logo">GEMING STORE</div>
        <div class="nav-links">
            <a href="index.php">Back to Shop</a>
            <a href="cart.php">Cart</a>
        </div>
    </nav>

    <div class="container" style="padding-top: 4rem;">
        <div style="display: flex; gap: 3rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;">
                <img src="<?= htmlspecialchars($product['image_url']) ?>"
                    style="width: 100%; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 0 30px rgba(0,0,0,0.5);">
            </div>
            <div style="flex: 1; min-width: 300px;">
                <span class="product-category" style="font-size: 1.2rem;">
                    <?= htmlspecialchars($product['category']) ?>
                </span>
                <h1
                    style="font-size: 3rem; margin-bottom: 1rem; color: white; text-shadow: 0 0 20px rgba(255,255,255,0.2);">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>
                <p class="product-price" style="font-size: 2rem; color: var(--neon-green); margin-bottom: 2rem;">$
                    <?= number_format($product['price'], 2) ?>
                </p>

                <div
                    style="background: rgba(255,255,255,0.03); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 2rem; line-height: 1.6; color: var(--text-muted);">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </div>

                <form action="api.php" method="POST" style="max-width: 300px;">
                    <input type="hidden" name="action" value="add_to_cart">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                    <div style="margin-bottom: 1rem;">
                        <label style="color: white; margin-bottom: 0.5rem; display: block;">Quantity</label>
                        <input type="number" name="quantity" value="1" min="1" class="form-control"
                            style="width: 100px;">
                    </div>

                    <button type="submit" class="btn-neon">ADD TO ACQUIRE</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>