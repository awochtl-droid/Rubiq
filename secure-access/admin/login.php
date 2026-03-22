<?php
require_once __DIR__ . '/admin-auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (checkAdminLogin($_POST['email'] ?? '', $_POST['password'] ?? '')) {
        header('Location: index.php');
        exit;
    }
    $error = 'Invalid credentials.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login | Rubiq Financial Partners</title>
  <meta name="robots" content="noindex, nofollow" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; margin: 0;
           min-height: 100vh; display: flex; align-items: center; justify-content: center;
           background: #0F1724; }
    .auth-input {
      width: 100%; padding: 0.75rem 1rem; border: 1px solid rgba(26,94,168,0.20);
      border-radius: 4px; font-size: 0.9rem; font-family: 'Inter', sans-serif;
      color: #0F1724; background: white; outline: none;
    }
    .auth-input:focus { border-color: #1A5EA8; box-shadow: 0 0 0 3px rgba(26,94,168,0.08); }
  </style>
</head>
<body>
  <div style="width:100%;max-width:380px;background:white;border-radius:8px;padding:2.5rem 2rem;
              box-shadow:0 4px 24px rgba(0,0,0,0.4);">
    <h1 style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;
               text-align:center;color:#0F1724;margin-bottom:2rem;">Admin Login</h1>

    <?php if ($error): ?>
      <p style="color:#dc2626;font-size:0.8125rem;text-align:center;margin-bottom:1rem;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
      <div style="margin-bottom:1.25rem;">
        <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                      text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">Email</label>
        <input type="email" name="email" class="auth-input" required autocomplete="email" />
      </div>
      <div style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                      text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">Password</label>
        <input type="password" name="password" class="auth-input" required autocomplete="current-password" />
      </div>
      <button type="submit" style="width:100%;padding:0.75rem;border:none;border-radius:4px;cursor:pointer;
                                    background:#1A5EA8;color:white;font-weight:600;font-size:0.875rem;
                                    letter-spacing:0.04em;text-transform:uppercase;">
        Sign In
      </button>
    </form>
  </div>
</body>
</html>
