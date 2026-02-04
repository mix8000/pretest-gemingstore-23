<?php
require 'db.php';

try {
    echo "<h1>User Debug Info</h1>";

    // Check connection
    echo "<p>Database connection successful.</p>";

    // List all users
    $stmt = $pdo->query("SELECT id, username, role, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Username</th><th>Role</th><th>Created At</th><th>Action</th></tr>";

    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>{$user['created_at']}</td>";
        echo "<td>-</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Check specifically for 'admin'
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        echo "<h2>Admin User Status</h2>";
        echo "Admin user found.<br>";
        echo "Role: " . $admin['role'] . "<br>";

        // Test password 'admin123'
        if (password_verify('admin123', $admin['password'])) {
            echo "<p style='color:green'>Password 'admin123' MATCHES.</p>";
        } else {
            echo "<p style='color:red'>Password 'admin123' DOES NOT MATCH.</p>";
            echo "<form method='POST'>
                    <input type='hidden' name='reset_admin' value='1'>
                    <button type='submit'>Reset Admin Password to 'admin123'</button>
                  </form>";
        }
    } else {
        echo "<h2 style='color:red'>Admin user NOT found.</h2>";
        echo "<form method='POST'>
                <input type='hidden' name='create_admin' value='1'>
                <button type='submit'>Create Admin User (admin / admin123)</button>
              </form>";
    }

    // Handle Actions
    if (isset($_POST['reset_admin'])) {
        $newPass = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'")->execute([$newPass]);
        echo "<p style='color:green'><b>Admin password reset to 'admin123'. Refresh page to verify.</b></p>";
    }

    if (isset($_POST['create_admin'])) {
        $pass = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, password, role) VALUES ('admin', ?, 'admin')")->execute([$pass]);
        echo "<p style='color:green'><b>Admin user created. Refresh page to verify.</b></p>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>