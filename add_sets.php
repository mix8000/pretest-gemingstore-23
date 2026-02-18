<?php
require 'db.php';

try {
    // Check if sets already exist to avoid duplicates (optional, based on name)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = ?");

    $sets = [
        [
            'name' => 'Budget Warrior Set',
            'price' => 15900.00,
            'description' => 'Perfect starting point for 1080p gaming. value for money.',
            'image_url' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&q=80&w=1000',
            'category' => 'Computer Set'
        ],
        [
            'name' => 'Pro Streamer Set',
            'price' => 32500.00,
            'description' => 'High performance for gaming and streaming simultaneously. RTX inside.',
            'image_url' => 'https://images.unsplash.com/photo-1587202314342-653197259d64?auto=format&fit=crop&q=80&w=1000',
            'category' => 'Computer Set'
        ],
        [
            'name' => 'Ultimate God Tier',
            'price' => 89000.00,
            'description' => 'Uncompromised 4K Ultra gaming. The best money can buy.',
            'image_url' => 'https://images.unsplash.com/photo-1603481588234-583d71241512?auto=format&fit=crop&q=80&w=1000',
            'category' => 'Computer Set'
        ]
    ];

    $insertBase = "INSERT INTO products (name, price, description, image_url, category) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $pdo->prepare($insertBase);

    foreach ($sets as $set) {
        // Check existence
        $stmt->execute([$set['name']]);
        if ($stmt->fetchColumn() == 0) {
            $insertStmt->execute([
                $set['name'],
                $set['price'],
                $set['description'],
                $set['image_url'],
                $set['category']
            ]);
            echo "Added: " . $set['name'] . "<br>";
        } else {
            echo "Skipped (Exists): " . $set['name'] . "<br>";
        }
    }

    echo "Done adding sets.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>