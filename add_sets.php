<?php
require 'db.php';

try {
    // Check if sets already exist to avoid duplicates (optional, based on name)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = ?");

    $sets = [
        [
            'name' => 'ชุดนักรบประหยัด',
            'price' => 15900.00,
            'description' => 'จุดเริ่มต้นที่สมบูรณ์แบบสำหรับการเล่นเกม 1080p คุ้มค่าคุ้มราคา',
            'image_url' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&q=80&w=1000',
            'category' => 'ชุดคอมพิวเตอร์'
        ],
        [
            'name' => 'ชุดโปรสตรีมเมอร์',
            'price' => 32500.00,
            'description' => 'ประสิทธิภาพสูงสำหรับการเล่นเกมและสตรีมมิ่งพร้อมกัน มี RTX ในตัว',
            'image_url' => 'https://images.unsplash.com/photo-1587202314342-653197259d64?auto=format&fit=crop&q=80&w=1000',
            'category' => 'ชุดคอมพิวเตอร์'
        ],
        [
            'name' => 'ชุดเทพเจ้าขั้นสูงสุด',
            'price' => 89000.00,
            'description' => 'การเล่นเกม 4K Ultra ที่ไม่มีใครเทียบได้ สิ่งที่ดีที่สุดที่เงินซื้อได้',
            'image_url' => 'https://images.unsplash.com/photo-1603481588234-583d71241512?auto=format&fit=crop&q=80&w=1000',
            'category' => 'ชุดคอมพิวเตอร์'
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