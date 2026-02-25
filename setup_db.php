<?php
require 'db.php';

try {
    // Create Products Table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        description TEXT,
        image_url VARCHAR(255),
        category VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql);
    echo "Table 'products' created successfully.<br>";

    // Create Users Table
    $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'customer') DEFAULT 'customer',
        email VARCHAR(100),
        mobile VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlUsers);
    echo "Table 'users' created successfully.<br>";

    // Create Orders Table
    $sqlOrders = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_price DECIMAL(10, 2) NOT NULL,
        address TEXT,
        payment_slip VARCHAR(255),
        status ENUM('pending', 'paid', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlOrders);
    echo "Table 'orders' updated successfully with payment_slip.<br>";

    // Upgrade table if exists
    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_slip VARCHAR(255)");
        $pdo->exec("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'paid', 'completed', 'cancelled') DEFAULT 'pending'");
        echo "Orders table optimized for payments.<br>";
    } catch (Exception $e) { /* Ignore */
    }

    // Create Order Items Table
    $sqlOrderItems = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlOrderItems);
    echo "Table 'order_items' created successfully.<br>";

    // Upgrade table if exists (Add email/mobile)
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(100)");
        echo "Added email column.<br>";
    } catch (Exception $e) { /* Ignore if exists */
    }

    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN mobile VARCHAR(20)");
        echo "Added mobile column.<br>";
    } catch (Exception $e) { /* Ignore if exists */
    }

    // Insert Default Admin if not exists
    $stmtUser = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmtUser->execute();
    if ($stmtUser->fetchColumn() == 0) {
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdmin = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
        $insertAdmin->execute(['admin', $adminPass]);
        echo "Default admin user created (admin/admin123).<br>";
    }

    // Insert dummy data if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        $dummyData = [
            ['หูฟังนีออนไซเบอร์', 1500.00, 'เสียงความคมชัดสูงพร้อมแสงนีออน', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1000&auto=format&fit=crop', 'อุปกรณ์'],
            ['คีย์บอร์ดเมชา X', 2500.00, 'สวิตช์เชิงกลพร้อมไฟ RGB', 'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?q=80&w=1000&auto=format&fit=crop', 'อุปกรณ์'],
            ['เมาส์ควอนตัม', 900.00, 'เมาส์ไร้สายความหน่วงต่ำมาก', 'https://images.unsplash.com/photo-1527814050087-3793815479db?q=80&w=1000&auto=format&fit=crop', 'อุปกรณ์']
        ];

        $insert = $pdo->prepare("INSERT INTO products (name, price, description, image_url, category) VALUES (?, ?, ?, ?, ?)");
        foreach ($dummyData as $item) {
            $insert->execute($item);
        }
        echo "Dummy data inserted.<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>