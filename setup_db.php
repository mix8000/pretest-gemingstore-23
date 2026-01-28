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

    // Insert dummy data if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        $dummyData = [
            ['Neon Cyber Headsset', 1500.00, 'High fidelity audio with neon glow.', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1000&auto=format&fit=crop', 'Gear'],
            ['Mecha Keyboard X', 2500.00, 'Mechanical switches with RGB matrix.', 'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?q=80&w=1000&auto=format&fit=crop', 'Gear'],
            ['Quantum Mouse', 900.00, 'Zero latency wireless mouse.', 'https://images.unsplash.com/photo-1527814050087-3793815479db?q=80&w=1000&auto=format&fit=crop', 'Gear']
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