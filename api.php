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
            header('Location: register.php?error=Passwords do not match');
            exit;
        }

        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)) {
            header('Location: register.php?error=Password must be at least 8 chars, include 1 uppercase and 1 lowercase letter');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: register.php?error=Invalid email format');
            exit;
        }

        // Check if user exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            header('Location: register.php?error=Username already taken');
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
            header('Location: login.php?error=Invalid username or password');
        }

    } elseif ($action === 'logout') {
        session_start();
        session_destroy();
        header('Location: index.php');
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>