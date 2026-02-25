<?php
require 'db.php';

try {
    // Create Products Table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        description TEXT,
        image_url TEXT,
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
        // Try adding payment_slip column first if it doesn't exist
        $pdo->exec("ALTER TABLE orders ADD IF NOT EXISTS payment_slip TEXT");
        // Ensure it is TEXT if it already existed as VARCHAR
        $pdo->exec("ALTER TABLE orders MODIFY COLUMN payment_slip TEXT");
        echo "Orders table: payment_slip column is ready (TEXT).<br>";
    } catch (Exception $e) {
        echo "Notice for Orders table: " . $e->getMessage() . "<br>";
    }

    try {
        // Modify existing image_url to TEXT in products table
        $pdo->exec("ALTER TABLE products MODIFY COLUMN image_url TEXT");
        echo "Products table: image_url column optimized to TEXT.<br>";
    } catch (Exception $e) {
        echo "Notice for Products table: " . $e->getMessage() . "<br>";
    }

    try {
        // Try modifying status column
        $pdo->exec("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'paid', 'completed', 'cancelled') DEFAULT 'pending'");
        echo "Updated order status enum.<br>";
    } catch (Exception $e) {
        echo "Error updating status enum: " . $e->getMessage() . "<br>";
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
            [
                'หูฟังนีออนไซเบอร์ (Neon Cyber Pro)',
                1500.00,
                "ระบบเสียงเซอร์ราวด์ 7.1 สมจริงที่สุดสำหรับการเล่นเกม\n- ไดรเวอร์ขนาด 50 มม. ให้เสียงเบสที่หนักแน่นและชัดเจน\n- แสงไฟ RGB Neon ปรับแต่งได้ 16.8 ล้านสี\n- ไมโครโฟนตัดเสียงรบกวน (Noise Cancelling) ระดับโปร\n- ฟองน้ำรองหูแบบเมมโมรี่โฟม ใส่สบายตลอดการเล่นเกมที่ยาวนาน",
                'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1000&auto=format&fit=crop',
                'อุปกรณ์เกียร์'
            ],
            [
                'คีย์บอร์ดเมชา X (Mechanical Mecha-X)',
                2500.00,
                "คีย์บอร์ดแมคคานิคอลสำหรับการแข่งขันอีสปอร์ตระดับโลก\n- เลือกสวิตช์ได้: Blue/Red/Brown Switch ตามสไตล์ของคุณ\n- โครงสร้างอลูมิเนียมเกรดอากาศยาน แข็งแรงทนทาน\n- Full Anti-Ghosting กดพร้อมกันได้ทุกปุ่ม\n- พร้อมที่พักข้อมือแบบถอดออกได้",
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?q=80&w=1000&auto=format&fit=crop',
                'อุปกรณ์เกียร์'
            ],
            [
                'เมาส์ควอนตัม (Quantum Wireless Mouse)',
                900.00,
                "เมาส์ไร้สายความไวสูง 25,600 DPI แม่นยำทุกการสะบัดเมาส์\n- เทคโนโลยีไร้สายความเร็วสูง 1ms ไม่มีความหน่วง\n- น้ำหนักเบาเพียง 63 กรัม เพื่อความรวดเร็วสูงสุด\n- แบตเตอรี่ใช้งานได้นานถึง 70 ชั่วโมงต่อการชาร์จหนึ่งครั้ง\n- เซนเซอร์ออปติคอลระดับไฮเอนด์",
                'https://images.unsplash.com/photo-1527814050087-3793815479db?q=80&w=1000&auto=format&fit=crop',
                'อุปกรณ์เกียร์'
            ],
            [
                'คอมพิวเตอร์เซ็ต: TITAN-X ULTIMATE (Extreme Gaming Build)',
                45000.00,
                "ชุดคอมพิวเตอร์ระดับ Ultra Hi-End ที่ออกแบบมาเพื่อการเล่นเกม 4K และการสตรีมมิ่งที่ลื่นไหลที่สุด\n\n[สเปกเครื่องขั้นเทพ]\n- CPU: Intel Core i9-14900K (24 Cores, 32 Threads) ตัวแรงที่สุด\n- GPU: NVIDIA GeForce RTX 4090 24GB GDDR6X (Ray Tracing Gen 3)\n- RAM: 64GB (32GBx2) DDR5 6400MHz RGB สุดพรีเมียม\n- Mainboard: ASUS ROG Maximus Z790 Dark Hero\n- Storage: 2TB NVMe Gen5 SSD (Speed 12,000MB/s)\n- PSU: 1200W 80+ Platinum Fully Modular\n- Case: HYTE Y70 Touch พร้อมจอสัมผัสแสดงผลแบบเรียลไทม์\n- Cooling: ระบบระบายความร้อนด้วยน้ำแบบเปิด (Custom Water Cooling) พร้อมไฟ RGB Sync ทั้งระบบ\n\n[บริการสุดพิเศษ]\n- บริการประกอบและจัดแต่งสายไฟอย่างมืออาชีพ (Cable Management)\n- ติดตั้ง Windows 11 Pro แท้พร้อมใช้งาน\n- รับประกันอุปกรณ์รายชิ้นสูงสุด 5-10 ปี\n- ฟรี! ประกันแบบ Onsite Service ถึงบ้าน 3 ปีเต็ม",
                'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?q=80&w=1000&auto=format&fit=crop',
                'Computer Set'
            ],
            [
                'เก้าอี้เกมมิ่ง Omega Throne',
                5500.00,
                "นั่งสบายเหมือนนั่งบนบัลลังก์ พร้อมลุยทุกสมรภูมิ\n- หนัง PU เกรดพรีเมียม ระบายอากาศได้ดี\n- ปรับเอนได้ถึง 155 องศา พร้อมล็อคตำแหน่ง\n- พนักพิงแขนแบบ 4D ปรับได้ทุกทิศทาง\n- โครงเหล็กแข็งแรงพิเศษ รองรับน้ำหนักถึง 150 กก.",
                'https://images.unsplash.com/photo-1598550476439-6847785fce6e?q=80&w=1000&auto=format&fit=crop',
                'เฟอร์นิเจอร์'
            ]
        ];

        $insert = $pdo->prepare("INSERT INTO products (name, price, description, image_url, category) VALUES (?, ?, ?, ?, ?)");
        foreach ($dummyData as $item) {
            $insert->execute($item);
        }
        echo "Expanded dummy data inserted.<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>