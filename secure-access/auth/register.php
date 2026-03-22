<?php
session_start();
require_once __DIR__ . '/../db/config.php';

$error   = '';
$success = false;
$return_url = isset($_GET['return']) ? $_GET['return'] : '/Pages/resources.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first   = trim($_POST['first_name'] ?? '');
    $last    = trim($_POST['last_name'] ?? '');
    $email   = strtolower(trim($_POST['email'] ?? ''));
    $company = trim($_POST['company'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');

    // Validation
    if (empty($first) || empty($last) || empty($email)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $db = getDB();

        // Check for existing user
        $stmt = $db->prepare('SELECT id, status FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $existing = $stmt->fetch();

        if ($existing) {
            if ($existing['status'] === 'pending') {
                $error = 'A request with this email is already pending approval.';
            } elseif ($existing['status'] === 'approved') {
                $error = 'An account with this email already exists. <a href="login.php" style="color:#1A5EA8;font-weight:600;">Sign in instead</a>.';
            } else {
                $error = 'This email has been previously denied access. Please contact us directly.';
            }
        } else {
            // Insert new registration
            $stmt = $db->prepare('
                INSERT INTO users (first_name, last_name, email, company, phone, status)
                VALUES (:first, :last, :email, :company, :phone, "pending")
            ');
            $stmt->execute([
                ':first'   => $first,
                ':last'    => $last,
                ':email'   => $email,
                ':company' => $company ?: null,
                ':phone'   => $phone ?: null,
            ]);

            // Notify admin
            $subject = 'New Access Request — ' . $first . ' ' . $last;
            $body    = "A new access request has been submitted.\n\n"
                     . "Name: $first $last\n"
                     . "Email: $email\n"
                     . "Company: " . ($company ?: 'Not provided') . "\n"
                     . "Phone: " . ($phone ?: 'Not provided') . "\n\n"
                     . "Review and approve at: " . SITE_URL . "/secure-access/admin/\n";

            $headers = 'From: ' . FROM_NAME . ' <' . FROM_EMAIL . ">\r\n"
                     . "Content-Type: text/plain; charset=UTF-8\r\n";

            @mail(NOTIFY_EMAIL, $subject, $body, $headers);

            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Request Access | Rubiq Financial Partners</title>
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
    .btn-gold:active { transform: translateY(0); }
    .btn-gold:focus-visible { outline: 2px solid #1A5EA8; outline-offset: 2px; }
  </style>
</head>
<body style="min-height:100vh;display:flex;flex-direction:column;">

  <div style="position:fixed;inset:0;z-index:-1;
              background:linear-gradient(160deg,#0F1724 0%,#133F73 50%,#1A5EA8 100%);">
    <div style="position:absolute;top:-10%;right:-5%;width:55%;height:110%;
                background:radial-gradient(ellipse,rgba(196,154,74,0.12) 0%,transparent 60%);"></div>
    <div style="position:absolute;bottom:-20%;left:-5%;width:45%;height:90%;
                background:radial-gradient(ellipse,rgba(42,116,200,0.15) 0%,transparent 65%);"></div>
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

      <?php if ($success): ?>

        <div style="text-align:center;">
          <div style="width:56px;height:56px;border-radius:50%;margin:0 auto 1.5rem;
                      background:rgba(196,154,74,0.10);border:1px solid rgba(196,154,74,0.25);
                      display:flex;align-items:center;justify-content:center;">
            <svg width="24" height="24" fill="none" stroke="#C49A5A" stroke-width="2" viewBox="0 0 24 24">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
          </div>
          <h1 style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;
                     color:#0F1724;margin-bottom:0.75rem;">
            Request Submitted
          </h1>
          <p style="font-size:0.9rem;line-height:1.65;color:#64748b;margin-bottom:2rem;">
            Thank you for your interest. We review access requests personally and will notify you
            by email once your access is approved.
          </p>
          <a href="/"
             style="display:inline-flex;align-items:center;gap:6px;font-size:0.8125rem;font-weight:600;
                    letter-spacing:0.04em;text-transform:uppercase;color:#1A5EA8;text-decoration:none;
                    transition:opacity 0.18s;"
             onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
            ← Return to Homepage
          </a>
        </div>

      <?php else: ?>

        <h1 style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;
                   color:#0F1724;text-align:center;margin-bottom:0.5rem;">
          Request Access
        </h1>
        <p style="font-size:0.875rem;color:#64748b;text-align:center;margin-bottom:2rem;line-height:1.5;">
          Tell us a bit about yourself. We'll review your request and send login credentials once approved.
        </p>

        <?php if ($error): ?>
          <div style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.20);
                      border-radius:4px;padding:0.75rem 1rem;margin-bottom:1.5rem;">
            <p style="font-size:0.8125rem;color:#dc2626;margin:0;line-height:1.5;">
              <?= $error ?>
            </p>
          </div>
        <?php endif; ?>

        <form method="POST" action="">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
            <div>
              <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                            text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">First Name *</label>
              <input type="text" name="first_name" class="auth-input" placeholder="First"
                     required value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" />
            </div>
            <div>
              <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                            text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">Last Name *</label>
              <input type="text" name="last_name" class="auth-input" placeholder="Last"
                     required value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" />
            </div>
          </div>

          <div style="margin-bottom:1.25rem;">
            <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                          text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">Email *</label>
            <input type="email" name="email" class="auth-input" placeholder="you@example.com"
                   required autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
          </div>

          <div style="margin-bottom:1.25rem;">
            <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                          text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">Company / Firm</label>
            <input type="text" name="company" class="auth-input" placeholder="Optional"
                   value="<?= htmlspecialchars($_POST['company'] ?? '') ?>" />
          </div>

          <div style="margin-bottom:1.75rem;">
            <label style="display:block;font-size:0.75rem;font-weight:600;letter-spacing:0.08em;
                          text-transform:uppercase;color:#64748b;margin-bottom:0.5rem;">Phone</label>
            <input type="tel" name="phone" class="auth-input" placeholder="Optional"
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" />
          </div>

          <button type="submit" class="btn-gold">Submit Request</button>
        </form>

        <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid rgba(26,94,168,0.10);text-align:center;">
          <p style="font-size:0.8125rem;color:#64748b;margin:0;">
            Already have access?
            <a href="login.php" style="color:#1A5EA8;font-weight:600;text-decoration:none;transition:opacity 0.18s;"
               onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
              Sign In
            </a>
          </p>
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
