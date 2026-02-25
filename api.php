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

    } elseif ($action === 'register') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];

        // Validation
        if ($password !== $confirm_password) {
            header('Location: register.php?error=รหัสผ่านไม่ตรงกัน');
            exit;
        }

        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)) {
            header('Location: register.php?error=รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร และประกอบด้วยตัวพิมพ์ใหญ่และตัวพิมพ์เล็ก');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: register.php?error=รูปแบบอีเมลไม่ถูกต้อง');
            exit;
        }

        // Check if user exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            header('Location: register.php?error=ชื่อผู้ใช้นี้ถูกใช้งานแล้ว');
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, email, mobile) VALUES (?, ?, 'customer', ?, ?)");
        $stmt->execute([$username, $hashed_password, $email, $mobile]);

        // Auto login or redirect to login
        header('Location: login.php?registered=1');

    } elseif ($action === 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role_target = $_POST['role_target'] ?? 'customer'; // 'admin' or 'customer'

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: index.php');
            }
        } else {
            header('Location: login.php?error=ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
        }

    } elseif ($action === 'add_to_cart') {
        session_start();
        $product_id = $_POST['product_id'];
        $quantity = (int) $_POST['quantity'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }

        // Redirect back to the previous page with a success flag
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        if (strpos($referer, '?') !== false) {
            header("Location: $referer&added=1");
        } else {
            header("Location: $referer?added=1");
        }

    } elseif ($action === 'remove_from_cart') {
        session_start();
        $product_id = $_GET['product_id'];
        unset($_SESSION['cart'][$product_id]);
        header('Location: cart.php');

    } elseif ($action === 'checkout') {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php?error=กรุณาเข้าสู่ระบบก่อนดำเนินการชำระเงิน');
            exit;
        }

        if (empty($_SESSION['cart'])) {
            header('Location: index.php');
            exit;
        }

        $address = $_POST['address'] ?? '';
        if (empty($address)) {
            header('Location: cart.php?error=กรุณาระบุที่อยู่สำหรับจัดส่ง');
            exit;
        }

        // Calculate total and save order
        $total_price = 0;
        $order_items = [];

        foreach ($_SESSION['cart'] as $pid => $qty) {
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$pid]);
            $price = $stmt->fetchColumn();
            $total_price += $price * $qty;
            $order_items[] = ['pid' => $pid, 'qty' => $qty, 'price' => $price];
        }

        // Insert Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, address) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $total_price, $address]);
        $order_id = $pdo->lastInsertId();

        // Insert Order Items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($order_items as $item) {
            $stmt->execute([$order_id, $item['pid'], $item['qty'], $item['price']]);
        }

        // Clear Cart
        unset($_SESSION['cart']);
        header("Location: payment.php?order_id=$order_id");

    } elseif ($action === 'upload_slip') {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        $order_id = $_POST['order_id'];
        $slip = $_FILES['slip'];

        if ($slip['error'] === 0) {
            $upload_dir = 'uploads/slips/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = pathinfo($slip['name'], PATHINFO_EXTENSION);
            $file_name = 'slip_' . $order_id . '_' . time() . '.' . $file_ext;
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($slip['tmp_name'], $target_file)) {
                $stmt = $pdo->prepare("UPDATE orders SET payment_slip = ?, status = 'paid' WHERE id = ? AND user_id = ?");
                $stmt->execute([$target_file, $order_id, $_SESSION['user_id']]);
                header('Location: index.php?order_success=1');
                exit;
            } else {
                header("Location: payment.php?order_id=$order_id&error=failed_to_upload");
                exit;
            }
        } else {
            header("Location: payment.php?order_id=$order_id&error=invalid_file");
            exit;
        }

    } elseif ($action === 'complete_order') {
        session_start();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: login.php');
            exit;
        }

        $order_id = $_GET['order_id'];
        $stmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
        $stmt->execute([$order_id]);
        header('Location: admin.php');
        exit;

    } elseif ($action === 'logout') {
        session_start();
        session_destroy();
        header('Location: index.php');
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>