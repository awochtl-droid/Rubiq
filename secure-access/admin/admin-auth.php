<?php
/**
 * Admin authentication helper.
 * Include at the top of any admin page.
 */
session_start();
require_once __DIR__ . '/../db/config.php';

function requireAdmin() {
    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

function checkAdminLogin($email, $password) {
    $db   = getDB();
    $stmt = $db->prepare('SELECT id, password FROM admin_users WHERE email = :email');
    $stmt->execute([':email' => strtolower(trim($email))]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id']        = $admin['id'];
        return true;
    }
    return false;
}
