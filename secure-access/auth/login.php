<?php
session_start();
require_once __DIR__ . '/../db/config.php';

$error = '';
$return_url = isset($_GET['return']) ? $_GET['return'] : '/Pages/resources.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter your email and password.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare('SELECT id, first_name, password, status FROM users WHERE email = :email');
        $stmt->execute([':email' => strtolower($email)]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Invalid email or password.';
        } elseif ($user['status'] === 'pending') {
            $error = 'Your access request is still pending approval. We\'ll email you when it\'s ready.';
        } elseif ($user['status'] === 'denied') {
            $error = 'Your access request was not approved. Please contact us if you believe this is an error.';
        } else {
            // Create session
            $session_id = bin2hex(random_bytes(64));
            $expires    = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);

            $stmt = $db->prepare('
                INSERT INTO sessions (id, user_id, expires_at, ip_address)
                VALUES (:id, :uid, :exp, :ip)
            ');
            $stmt->execute([
                ':id'  => $session_id,
                ':uid' => $user['id'],
                ':exp' => $expires,
                ':ip'  => $_SERVER['REMOTE_ADDR'] ?? '',
            ]);

            // Update last login
            $db->prepare('UPDATE users SET last_login = NOW() WHERE id = :id')
               ->execute([':id' => $user['id']]);

            $_SESSION['session_id'] = $session_id;
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['first_name'];

            $return = isset($_POST['return_url']) ? $_POST['return_url'] : $return_url;
            header('Location: ' . $return);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In | Rubiq Financial Partners</title>
  <meta name="robots" content="noindex, nofollow" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            rubiq: {
              blue: '#1A5EA8', bluedark: '#133F73', bluelight: '#2A74C8',
              gold: '#C49A5A', goldlight: '#D4AE6A', golddark: '#9E7A32',
              ink: '#0F1724', inksoft: '#1C2D44', slate: '#2C3E55',
              mist: '#F4F6F9', parchment: '#FAF8F4',
            }
          },
          fontFamily: {
            display: ['Playfair Display', 'Georgia', 'serif'],
            body: ['Inter', 'system-ui', 'sans-serif'],
          },
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; margin: 0; }
    .auth-input {
      width: 100%; padding: 0.75rem 1rem; border: 1px solid rgba(26,94,168,0.20);
      border-radius: 4px; font-size: 0.9rem; font-family: 'Inter', sans-serif;
      color: #0F1724; background: white; transition: border-color 0.18s ease;
      outline: none;
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
    .btn-gold:active { transform: translateY(0); }
    .btn-gold:focus-visible { outline: 2px solid #1A5EA8; outline-offset: 2px; }
  </style>
</head>
<body style="min-height:100vh;display:flex;flex-direction:column;">

  <!-- Background -->
  <div style="position:fixed;inset:0;z-index:-1;
              background:linear-gradient(160deg,#0F1724 0%,#133F73 50%,#1A5EA8 100%);">
    <div style="position:absolute;top:-10%;right:-5%;width:55%;height:110%;
                background:radial-gradient(ellipse,rgba(196,154,74,0.12) 0%,transparent 60%);"></div>
    <div style="position:absolute;bottom:-20%;left:-5%;width:45%;height:90%;
                background:radial-gradient(ellipse,rgba(42,116,200,0.15) 0%,transparent 65%);"></div>
  </div>

  <!-- Logo -->
  <div style="padding:2rem 2rem 0;text-align:center;">
    <a href="/" style="display:inline-block;">
      <img src="/Brand_assets/Rubiq Financial Partners Logo - FINAL2 - Horizontal -White.png"
           alt="Rubiq Financial Partners" style="height:32px;width:auto;" />
    </a>
  </div>

  <!-- Card -->
  <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:2rem;">
    <div style="width:100%;max-width:420px;background:white;border-radius:8px;
                box-shadow:0 4px 24px rgba(15,23,36,0.25),0 16px 64px rgba(15,23,36,0.15);
                padding:2.5rem 2.25rem;">

      <h1 style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;
                 color:#0F1724;text-align:center;margin-bottom:0.5rem;">
        Sign In
      </h1>
      <p style="font-size:0.875rem;color:#64748b;text-align:center;margin-bottom:2rem;line-height:1.5;">
        Access exclusive resources and insights.
      </p>

      <?php if ($error): ?>
        <div style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.20);
                    border-radius:4px;padding:0.75rem 1rem;margin-bottom:1.5rem;">
          <p style="font-size:0.8125rem;color:#dc2626;margin:0;line-height:1.5;">
            <?= htmlspecialchars($error) ?>
          </p>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <input type="hidden" name="return_url" value="<?= htmlspecialchars($return_url) ?>" />

        <div style="margin-bottom:1.25rem;">
          <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                        text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">Email</label>
          <input type="email" name="email" class="auth-input" placeholder="you@example.com"
                 required autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        </div>

        <div style="margin-bottom:0.75rem;">
          <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                        text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">Password</label>
          <input type="password" name="password" class="auth-input" placeholder="Your password"
                 required autocomplete="current-password" />
        </div>

        <div style="text-align:right;margin-bottom:1.5rem;">
          <a href="reset-password.php" style="font-size:0.8125rem;color:#1A5EA8;text-decoration:none;
                                               font-weight:500;transition:opacity 0.18s;"
             onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
            Forgot password?
          </a>
        </div>

        <button type="submit" class="btn-gold">Sign In</button>
      </form>

      <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid rgba(26,94,168,0.10);text-align:center;">
        <p style="font-size:0.8125rem;color:#64748b;margin:0;">
          Don't have access?
          <a href="register.php<?= $return_url !== '/Pages/resources.php' ? '?return=' . urlencode($return_url) : '' ?>"
             style="color:#1A5EA8;font-weight:600;text-decoration:none;transition:opacity 0.18s;"
             onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
            Request Access
          </a>
        </p>
      </div>

    </div>
  </div>

  <!-- Footer -->
  <div style="text-align:center;padding:1.5rem 2rem;">
    <p style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin:0;">
      &copy; <?= date('Y') ?> Rubiq Financial Partners, LLC. All rights reserved.
    </p>
  </div>

</body>
</html>
