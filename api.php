<?php
require 'db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? null;

if (!$action) {
    header('Location: admin.php');
    exit;
}

try {
    if ($action === 'create') {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, category, image_url, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['price'],
            $_POST['category'],
            $_POST['image_url'],
            $_POST['description']
        ]);
        header('Location: admin.php');

    } elseif ($action === 'update') {
        $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, category=?, image_url=?, description=? WHERE id=?");
        $stmt->execute([
            $_POST['name'],
            $_POST['price'],
            $_POST['category'],
            $_POST['image_url'],
            $_POST['description'],
            $_POST['id']
        ]);
        header('Location: admin.php');

    } elseif ($action === 'delete') {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: admin.php');
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>