<?php
require 'db.php';
require 'auth_check.php';
checkLogin('admin'); // Enforce Admin Login

// Fetch products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gaming Store</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

    <nav class="navbar">
        <div class="logo">ADMIN DASHBOARD</div>
        <div class="nav-links">
            <a href="index.php">View Store</a>
            <a href="api.php?action=logout" style="color: #ff3333;">LOGOUT</a>
        </div>
    </nav>

    <div class="container">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <h2>Product Management</h2>
            <button onclick="document.getElementById('add-form').scrollIntoView()" class="btn btn-primary">ADD NEW
                PRODUCT</button>
        </div>

        <!-- Product Table -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>#
                            <?= $product['id'] ?>
                        </td>
                        <td><img src="<?= htmlspecialchars($product['image_url']) ?>" alt=""
                                style="width:50px; height:50px; object-fit:cover; border-radius:4px;"></td>
                        <td>
                            <?= htmlspecialchars($product['name']) ?>
                        </td>
                        <td>$
                            <?= number_format($product['price'], 2) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($product['category']) ?>
                        </td>
                        <td>
                            <a href="?edit=<?= $product['id'] ?>#add-form"
                                style="color:var(--neon-blue); margin-right:1rem;">Edit</a>
                            <a href="api.php?action=delete&id=<?= $product['id'] ?>"
                                onclick="return confirm('Are you sure?')" style="color:red;">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Add/Edit Form -->
        <div id="add-form"
            style="margin-top:4rem; max-width:600px; padding:2rem; background:var(--card-bg); border-radius:12px; border:var(--glass-border);">
            <h3>
                <?= isset($_GET['edit']) ? 'Edit Product' : 'Add New Product' ?>
            </h3>

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
                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control" required
                        value="<?= $editMode ? htmlspecialchars($editProduct['name']) : '' ?>">
                </div>

                <div class="form-group">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required
                        value="<?= $editMode ? $editProduct['price'] : '' ?>">
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" required
                        value="<?= $editMode ? htmlspecialchars($editProduct['category']) : '' ?>">
                </div>

                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image_url" class="form-control" required
                        value="<?= $editMode ? htmlspecialchars($editProduct['image_url']) : '' ?>">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control"
                        rows="4"><?= $editMode ? htmlspecialchars($editProduct['description']) : '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top:1rem;">
                    <?= $editMode ? 'UPDATE PRODUCT' : 'ADD PRODUCT' ?>
                </button>
                <?php if ($editMode): ?>
                    <a href="admin.php" style="margin-left:1rem; color:var(--text-muted); text-decoration:none;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

    </div>

</body>

</html>