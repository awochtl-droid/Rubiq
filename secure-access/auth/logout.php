<?php
session_start();
require_once __DIR__ . '/../db/config.php';

// Delete session from database
if (!empty($_SESSION['session_id'])) {
    $db = getDB();
    $db->prepare('DELETE FROM sessions WHERE id = :id')
       ->execute([':id' => $_SESSION['session_id']]);
}

// Destroy PHP session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

header('Location: /');
exit;
