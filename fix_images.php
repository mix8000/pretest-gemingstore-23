<?php
require 'db.php';

try {
    $updates = [
        'Pro Streamer Set' => 'https://images.unsplash.com/photo-1598550476439-6847785fcea6?w=800&q=80',
        'Ultimate God Tier' => 'https://images.unsplash.com/photo-1624705002806-5d72df19c3ad?w=800&q=80',
        'Budget Warrior Set' => 'https://images.unsplash.com/photo-1587831990711-23ca6441447b?w=800&q=80'
    ];

    $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE name = ?");

    foreach ($updates as $name => $url) {
        $stmt->execute([$url, $name]);
        if ($stmt->rowCount() > 0) {
            echo "Updated image for: $name<br>";
        } else {
            echo "No change/Not found for: $name<br>";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>