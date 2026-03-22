<?php
session_start();
require_once __DIR__ . '/../db/config.php';

$error   = '';
$success = false;
$mode    = 'request'; // 'request' or 'reset'
$token   = $_GET['token'] ?? '';

// If token is present, we're in reset mode
if ($token) {
    $mode = 'reset';
    $db   = getDB();
    $stmt = $db->prepare('SELECT id, email FROM users WHERE token = :token AND token_exp > NOW() AND status = "approved"');
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = 'This link has expired or is invalid. Please request a new password reset.';
        $mode  = 'request';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle password reset submission
    if (isset($_POST['action']) && $_POST['action'] === 'reset') {
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';
        $token    = $_POST['token'] ?? '';

        if (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
            $mode  = 'reset';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
            $mode  = 'reset';
        } else {
            $db   = getDB();
            $stmt = $db->prepare('SELECT id FROM users WHERE token = :token AND token_exp > NOW()');
            $stmt->execute([':token' => $token]);
            $user = $stmt->fetch();

            if ($user) {
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $db->prepare('UPDATE users SET password = :pw, token = NULL, token_exp = NULL WHERE id = :id')
                   ->execute([':pw' => $hash, ':id' => $user['id']]);
                $success = true;
                $mode    = 'done';
            } else {
                $error = 'This link has expired. Please request a new password reset.';
                $mode  = 'request';
            }
        }
    }

    // Handle reset request submission
    if (isset($_POST['action']) && $_POST['action'] === 'request') {
        $email = strtolower(trim($_POST['email'] ?? ''));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $db   = getDB();
            $stmt = $db->prepare('SELECT id, first_name FROM users WHERE email = :email AND status = "approved"');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            // Always show success (don't reveal if email exists)
            $success = true;

            if ($user) {
                $token   = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour

                $db->prepare('UPDATE users SET token = :token, token_exp = :exp WHERE id = :id')
                   ->execute([':token' => $token, ':exp' => $expires, ':id' => $user['id']]);

                $link    = SITE_URL . '/secure-access/auth/reset-password.php?token=' . $token;
                $subject = 'Password Reset — Rubiq Financial Partners';
                $body    = "Hi {$user['first_name']},\n\n"
                         . "We received a request to reset your password. Click the link below to set a new one:\n\n"
                         . "$link\n\n"
                         . "This link expires in 1 hour.\n\n"
                         . "If you didn't request this, you can safely ignore this email.\n\n"
                         . "— Rubiq Financial Partners";

                $headers = 'From: ' . FROM_NAME . ' <' . FROM_EMAIL . ">\r\n"
                         . "Content-Type: text/plain; charset=UTF-8\r\n";

                @mail($email, $subject, $body, $headers);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password | Rubiq Financial Partners</title>
  <meta name="robots" content="noindex, nofollow" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: { extend: { colors: { rubiq: {
        blue: '#1A5EA8', bluedark: '#133F73', bluelight: '#2A74C8',
        gold: '#C49A5A', goldlight: '#D4AE6A', golddark: '#9E7A32',
        ink: '#0F1724', inksoft: '#1C2D44', slate: '#2C3E55',
        mist: '#F4F6F9', parchment: '#FAF8F4',
      }}, fontFamily: {
        display: ['Playfair Display', 'Georgia', 'serif'],
        body: ['Inter', 'system-ui', 'sans-serif'],
      }}}
    }
  </script>
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; margin: 0; }
    .auth-input {
      width: 100%; padding: 0.75rem 1rem; border: 1px solid rgba(26,94,168,0.20);
      border-radius: 4px; font-size: 0.9rem; font-family: 'Inter', sans-serif;
      color: #0F1724; background: white; transition: border-color 0.18s ease; outline: none;
    }
    .auth-input:focus { border-color: #1A5EA8; box-shadow: 0 0 0 3px rgba(26,94,168,0.08); }
    .auth-input::placeholder { color: #94a3b8; }
    .btn-gold {
      width: 100%; padding: 0.875rem; border: none; border-radius: 4px; cursor: pointer;
      background: linear-gradient(135deg, #C49A5A 0%, #D4AE6A 100%);
      color: #0F1724; font-family: 'Inter', sans-serif; font-weight: 600;
      font-size: 0.875rem; letter-spacing: 0.04em; text-transform: uppercase;
      transition: opacity 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
    }
    .btn-gold:hover { opacity: 0.92; transform: translateY(-1px); box-shadow: 0 4px 20px rgba(196,154,74,0.40); }
    .btn-gold:focus-visible { outline: 2px solid #1A5EA8; outline-offset: 2px; }
  </style>
</head>
<body style="min-height:100vh;display:flex;flex-direction:column;">

  <div style="position:fixed;inset:0;z-index:-1;
              background:linear-gradient(160deg,#0F1724 0%,#133F73 50%,#1A5EA8 100%);">
    <div style="position:absolute;top:-10%;right:-5%;width:55%;height:110%;
                background:radial-gradient(ellipse,rgba(196,154,74,0.12) 0%,transparent 60%);"></div>
  </div>

  <div style="padding:2rem 2rem 0;text-align:center;">
    <a href="/" style="display:inline-block;">
      <img src="/Brand_assets/Rubiq Financial Partners Logo - FINAL2 - Horizontal -White.png"
           alt="Rubiq Financial Partners" style="height:32px;width:auto;" />
    </a>
  </div>

  <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:2rem;">
    <div style="width:100%;max-width:420px;background:white;border-radius:8px;
                box-shadow:0 4px 24px rgba(15,23,36,0.25),0 16px 64px rgba(15,23,36,0.15);
                padding:2.5rem 2.25rem;">

      <?php if ($mode === 'done'): ?>
        <div style="text-align:center;">
          <div style="width:56px;height:56px;border-radius:50%;margin:0 auto 1.5rem;
                      background:rgba(196,154,74,0.10);border:1px solid rgba(196,154,74,0.25);
                      display:flex;align-items:center;justify-content:center;">
            <svg width="24" height="24" fill="none" stroke="#C49A5A" stroke-width="2" viewBox="0 0 24 24">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
          </div>
          <h1 style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:#0F1724;margin-bottom:0.75rem;">
            Password Updated
          </h1>
          <p style="font-size:0.9rem;color:#64748b;margin-bottom:2rem;">
            Your password has been set. You can now sign in.
          </p>
          <a href="login.php" class="btn-gold" style="display:inline-block;text-decoration:none;text-align:center;">
            Sign In
          </a>
        </div>

      <?php elseif ($mode === 'reset'): ?>
        <h1 style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;
                   color:#0F1724;text-align:center;margin-bottom:0.5rem;">Set Your Password</h1>
        <p style="font-size:0.875rem;color:#64748b;text-align:center;margin-bottom:2rem;">
          Choose a password for your account.
        </p>

        <?php if ($error): ?>
          <div style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.20);
                      border-radius:4px;padding:0.75rem 1rem;margin-bottom:1.5rem;">
            <p style="font-size:0.8125rem;color:#dc2626;margin:0;"><?= htmlspecialchars($error) ?></p>
          </div>
        <?php endif; ?>

        <form method="POST" action="">
          <input type="hidden" name="action" value="reset" />
          <input type="hidden" name="token" value="<?= htmlspecialchars($token ?: ($_POST['token'] ?? '')) ?>" />
          <div style="margin-bottom:1.25rem;">
            <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                          text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">New Password</label>
            <input type="password" name="password" class="auth-input" placeholder="Minimum 8 characters"
                   required minlength="8" autocomplete="new-password" />
          </div>
          <div style="margin-bottom:1.75rem;">
            <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                          text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">Confirm Password</label>
            <input type="password" name="confirm" class="auth-input" placeholder="Re-enter password"
                   required minlength="8" autocomplete="new-password" />
          </div>
          <button type="submit" class="btn-gold">Set Password</button>
        </form>

      <?php elseif ($success): ?>
        <div style="text-align:center;">
          <h1 style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:#0F1724;margin-bottom:0.75rem;">
            Check Your Email
          </h1>
          <p style="font-size:0.9rem;color:#64748b;line-height:1.65;margin-bottom:2rem;">
            If an account with that email exists, we've sent a password reset link. It expires in 1 hour.
          </p>
          <a href="login.php" style="font-size:0.8125rem;color:#1A5EA8;font-weight:600;text-decoration:none;">
            ← Back to Sign In
          </a>
        </div>

      <?php else: ?>
        <h1 style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;
                   color:#0F1724;text-align:center;margin-bottom:0.5rem;">Reset Password</h1>
        <p style="font-size:0.875rem;color:#64748b;text-align:center;margin-bottom:2rem;">
          Enter your email and we'll send a reset link.
        </p>

        <?php if ($error): ?>
          <div style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.20);
                      border-radius:4px;padding:0.75rem 1rem;margin-bottom:1.5rem;">
            <p style="font-size:0.8125rem;color:#dc2626;margin:0;"><?= htmlspecialchars($error) ?></p>
          </div>
        <?php endif; ?>

        <form method="POST" action="">
          <input type="hidden" name="action" value="request" />
          <div style="margin-bottom:1.75rem;">
            <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                          text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">Email</label>
            <input type="email" name="email" class="auth-input" placeholder="you@example.com"
                   required autocomplete="email" />
          </div>
          <button type="submit" class="btn-gold">Send Reset Link</button>
        </form>

        <div style="margin-top:2rem;text-align:center;">
          <a href="login.php" style="font-size:0.8125rem;color:#1A5EA8;font-weight:600;text-decoration:none;">
            ← Back to Sign In
          </a>
        </div>
      <?php endif; ?>

    </div>
  </div>

  <div style="text-align:center;padding:1.5rem 2rem;">
    <p style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin:0;">
      &copy; <?= date('Y') ?> Rubiq Financial Partners, LLC. All rights reserved.
    </p>
  </div>

</body>
</html>
