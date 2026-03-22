<?php
/**
 * One-time admin account setup.
 *
 * USAGE:
 * 1. Upload the secure-access folder to your server
 * 2. Run setup.sql in phpMyAdmin
 * 3. Visit: https://www.rubiqfinancial.com/secure-access/admin/setup-admin.php
 * 4. Set your admin email and password
 * 5. DELETE THIS FILE from the server immediately after
 */

require_once __DIR__ . '/../db/config.php';

$done  = false;
$error = '';

// Check if admin already exists
$db    = getDB();
$count = $db->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();

if ($count > 0) {
    die('<h2>Admin account already exists.</h2><p>Delete this file from the server.</p>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (strlen($password) < 10) {
        $error = 'Password must be at least 10 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $db->prepare('INSERT INTO admin_users (email, password) VALUES (:email, :pw)')
           ->execute([':email' => $email, ':pw' => $hash]);
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html><head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Admin Setup</title>
  <style>
    body { font-family: system-ui, sans-serif; max-width: 420px; margin: 4rem auto; padding: 0 1rem; }
    input { display: block; width: 100%; padding: 0.6rem; margin-bottom: 1rem; font-size: 1rem;
            border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    button { padding: 0.75rem 2rem; background: #1A5EA8; color: white; border: none;
             border-radius: 4px; font-size: 1rem; cursor: pointer; }
    .warn { background: #fef2f2; border: 1px solid #fecaca; padding: 1rem; border-radius: 4px;
            color: #991b1b; margin-bottom: 1rem; }
    .ok { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 1rem; border-radius: 4px; color: #166534; }
  </style>
</head><body>
  <h1>Admin Account Setup</h1>

  <?php if ($done): ?>
    <div class="ok">
      <p><strong>Admin account created.</strong></p>
      <p>You can now <a href="login.php">sign in to the admin dashboard</a>.</p>
      <p><strong style="color:#991b1b;">DELETE this file (setup-admin.php) from the server immediately.</strong></p>
    </div>
  <?php else: ?>
    <?php if ($error): ?>
      <div class="warn"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <label>Admin Email</label>
      <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? ADMIN_EMAIL) ?>" />
      <label>Password (min 10 characters)</label>
      <input type="password" name="password" required minlength="10" />
      <label>Confirm Password</label>
      <input type="password" name="confirm" required minlength="10" />
      <button type="submit">Create Admin Account</button>
    </form>
  <?php endif; ?>
</body></html>
