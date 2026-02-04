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

        if ($password !== $confirm_password) {
            echo "Passwords do not match.";
            exit;
        }

        // Check if user exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            echo "Username already taken.";
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'customer')");
        $stmt->execute([$username, $hashed_password]);

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

            if ($role_target === 'admin') {
                if ($user['role'] === 'admin') {
                    header('Location: admin.php');
                } else {
                    echo "Access Denied. You are not an admin.";
                    session_destroy();
                }
            } else {
                header('Location: index.php');
            }
        } else {
            echo "Invalid username or password.";
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