<?php
/**
 * Gate — include this at the top of any page you want to protect.
 *
 * Usage: Add this as the VERY FIRST line of any .php page:
 *   <?php include __DIR__ . '/../secure-access/auth/gate.php'; ?>
 *
 * If the user is not logged in, they'll be redirected to the login page.
 * After login, they'll be sent back to the page they originally requested.
 */

session_start();
require_once __DIR__ . '/../db/config.php';

function isAuthenticated() {
    if (empty($_SESSION['session_id']) || empty($_SESSION['user_id'])) {
        return false;
    }

    $db = getDB();

    // Check session is valid and not expired
    $stmt = $db->prepare('
        SELECT s.user_id, u.status
        FROM sessions s
        JOIN users u ON u.id = s.user_id
        WHERE s.id = :sid
          AND s.expires_at > NOW()
          AND u.status = "approved"
    ');
    $stmt->execute([':sid' => $_SESSION['session_id']]);
    $row = $stmt->fetch();

    if (!$row) {
        // Session expired or user no longer approved — clean up
        unset($_SESSION['session_id'], $_SESSION['user_id']);
        return false;
    }

    return true;
}

if (!isAuthenticated()) {
    $return_url = $_SERVER['REQUEST_URI'];
    $login_url = '/secure-access/auth/login.php?return=' . urlencode($return_url);
    header('Location: ' . $login_url);
    exit;
}
