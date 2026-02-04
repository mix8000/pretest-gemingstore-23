<?php
require 'db.php';
session_start();

$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();

    foreach ($products as $product) {
        $qty = $_SESSION['cart'][$product['id']];
        $line_total = $product['price'] * $qty;
        $total_price += $line_total;
        $product['qty'] = $qty;
        $product['line_total'] = $line_total;
        $cart_items[] = $product;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Gaming Store</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="logo">GEMING STORE</div>
        <div class="nav-links">
            <a href="index.php">Continue Shopping</a>
        </div>
    </nav>

    <div class="container">
        <section class="hero" style="padding: 2rem 0;">
            <h1>Command Center LOOT</h1>
        </section>

        <?php if (empty($cart_items)): ?>
            <div style="text-align: center; color: var(--text-muted); padding: 3rem;">
                <h2>Your inventory is empty.</h2>
                <a href="index.php" class="btn btn-primary" style="margin-top: 1rem;">BROWSE GEAR</a>
            </div>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Gear</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>"
                                    style="width: 50px; height: 50px; object-fit: cover; vertical-align: middle; margin-right: 10px; border-radius: 4px;">
                                <?= htmlspecialchars($item['name']) ?>
                            </td>
                            <td>$
                                <?= number_format($item['price'], 2) ?>
                            </td>
                            <td>
                                <?= $item['qty'] ?>
                            </td>
                            <td>$
                                <?= number_format($item['line_total'], 2) ?>
                            </td>
                            <td>
                                <a href="api.php?action=remove_from_cart&product_id=<?= $item['id'] ?>"
                                    style="color: #ff5555; text-decoration: none;">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 2rem;">
                <h2 style="color: var(--neon-green); margin-bottom: 1rem; text-align: right;">TOTAL:
                    $<?= number_format($total_price, 2) ?></h2>
                <form action="api.php" method="POST" style="max-width: 400px; margin-left: auto;">
                    <input type="hidden" name="action" value="checkout">

                    <div class="form-group">
                        <label
                            style="color: var(--text-muted); display: block; margin-bottom: 0.5rem; text-align: left;">Shipping
                            Address</label>
                        <textarea name="address" class="form-control" rows="3" required
                            placeholder="Enter delivery coordinates..."></textarea>
                    </div>

                    <button type="submit" class="btn-neon" style="width: 100%; padding: 1rem;">INITIALIZE CHECKOUT</button>
                    <?php if (isset($_GET['error'])): ?>
                        <p style="color: red; margin-top: 0.5rem; text-align: center;"><?= htmlspecialchars($_GET['error']) ?>
                        </p>
                    <?php endif; ?>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>