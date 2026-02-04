<?php
session_start();

function checkLogin($role = 'customer')
{
    if (!isset($_SESSION['user_id'])) {
        if ($role === 'admin') {
            header('Location: login_admin.php');
        } else {
            header('Location: login.php');
        }
        exit;
    }

    if ($role === 'admin' && $_SESSION['role'] !== 'admin') {
        echo "Access Denied. Admins only.";
        exit;
    }
}
?>