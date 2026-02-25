<?php
require 'db.php';
require 'auth_check.php';
checkLogin('admin'); // Enforce Admin Login

// Fetch products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();

// Fetch orders
$stmt = $pdo->query("SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id ORDER BY created_at DESC");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบหลังบ้าน - ร้านเกม</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Oswald:wght@300;400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .section-title {
            margin-top: 3rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5rem;
            color: var(--neon-green);
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="logo">แผงควบคุมผู้ดูแล</div>
        <div class="nav-links">
            <a href="index.php">ดูหน้าร้าน</a>
            <a href="api.php?action=logout" style="color: #ff3333;">ออกจากระบบ</a>
        </div>
    </nav>

    <div class="container">

        <!-- Orders Section -->
        <h2 class="section-title">รายการสั่งซื้อของลูกค้า</h2>
        <?php if (empty($orders)): ?>
            <p style="color: var(--text-muted);">ยังไม่มีรายการสั่งซื้อ</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>รหัสสั่งซื้อ</th>
                        <th>ลูกค้า</th>
                        <th>ยอดรวม</th>
                        <th>วันที่สั่งซื้อ</th>
                        <th>สินค้า</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#
                                <?= $order['id'] ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($order['username']) ?>
                            </td>
                            <td style="color: var(--neon-green); font-weight: bold;">$
                                <?= number_format($order['total_price'], 2) ?>
                            </td>
                            <td>
                                <?= $order['created_at'] ?>
                            </td>
                            <td>
                                <?php
                                $stmtItems = $pdo->prepare("SELECT order_items.*, products.name FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_id = ?");
                                $stmtItems->execute([$order['id']]);
                                $items = $stmtItems->fetchAll();
                                foreach ($items as $item) {
                                    echo "<div>" . htmlspecialchars($item['name']) . " x" . $item['quantity'] . "</div>";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>


        <!-- Products Section -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top: 3rem;">
            <h2 class="section-title" style="margin-top: 0; border: none;">คลังสินค้า</h2>
            <button onclick="document.getElementById('add-form').scrollIntoView()"
                class="btn btn-primary">เพิ่มสินค้าใหม่</button>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ไอดี</th>
                    <th>รูปภาพ</th>
                    <th>ชื่อสินค้า</th>
                    <th>ราคา</th>
                    <th>หมวดหมู่</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>#<?= $product['id'] ?></td>
                        <td><img src="<?= htmlspecialchars($product['image_url']) ?>" alt=""
                                style="width:50px; height:50px; object-fit:cover; border-radius:4px;"></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td>$<?= number_format($product['price'], 2) ?></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td>
                            <a href="?edit=<?= $product['id'] ?>#add-form"
                                style="color:var(--neon-blue); margin-right:1rem;"><i class="fas fa-edit"></i> แก้ไข</a>
                            <a href="api.php?action=delete&id=<?= $product['id'] ?>"
                                onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?')" style="color:red;"><i
                                    class="fas fa-trash"></i> ลบออก</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Add/Edit Form -->
        <div id="add-form"
            style="margin-top:4rem; margin-bottom: 4rem; max-width:600px; padding:2rem; background:var(--card-bg); border-radius:12px; border:var(--glass-border);">
            <h3><?= isset($_GET['edit']) ? 'แก้ไขข้อมูลสินค้า' : 'เพิ่มสินค้าใหม่' ?></h3>

            <?php
            $editMode = false;
            if (isset($_GET['edit'])) {
                $editId = $_GET['edit'];
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$editId]);
                $editProduct = $stmt->fetch();
                if ($editProduct)
                    $editMode = true;
            }
            ?>

            <form action="api.php" method="POST" style="margin-top:1.5rem;">
                <input type="hidden" name="action" value="<?= $editMode ? 'update' : 'create' ?>">
                <?php if ($editMode): ?>
                    <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>ชื่อสินค้า</label>
                    <input type="text" name="name" class="form-control" required
                        value="<?= $editMode ? htmlspecialchars($editProduct['name']) : '' ?>">
                </div>

                <div class="form-group">
                    <label>ราคา ($)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required
                        value="<?= $editMode ? $editProduct['price'] : '' ?>">
                </div>

                <div class="form-group">
                    <label>หมวดหมู่</label>
                    <input type="text" name="category" class="form-control" required
                        value="<?= $editMode ? htmlspecialchars($editProduct['category']) : '' ?>">
                </div>

                <div class="form-group">
                    <label>ลิ้งก์รูปภาพ</label>
                    <input type="text" name="image_url" class="form-control" required
                        value="<?= $editMode ? htmlspecialchars($editProduct['image_url']) : '' ?>">
                </div>

                <div class="form-group">
                    <label>รายละเอียด</label>
                    <textarea name="description" class="form-control"
                        rows="4"><?= $editMode ? htmlspecialchars($editProduct['description']) : '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top:1rem;">
                    <?= $editMode ? 'อัปเดตข้อมูลสินค้า' : 'เพิ่มสินค้าลงระบบ' ?>
                </button>
                <?php if ($editMode): ?>
                    <a href="admin.php" style="margin-left:1rem; color:var(--text-muted); text-decoration:none;">ยกเลิก</a>
                <?php endif; ?>
            </form>
        </div>

    </div>

    <?php include 'footer.php'; ?>
</body>

</html>