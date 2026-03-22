<?php
require_once __DIR__ . '/admin-auth.php';
requireAdmin();

$db = getDB();

// Handle approve / deny actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
    $user_id = (int) $_POST['user_id'];
    $action  = $_POST['action'];

    if ($action === 'approve') {
        // Generate password-set token
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 86400 * 7); // 7 days

        $db->prepare('UPDATE users SET status = "approved", approved_at = NOW(), token = :token, token_exp = :exp WHERE id = :id')
           ->execute([':token' => $token, ':exp' => $expires, ':id' => $user_id]);

        // Get user info for email
        $stmt = $db->prepare('SELECT first_name, email FROM users WHERE id = :id');
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch();

        if ($user) {
            $link    = SITE_URL . '/secure-access/auth/reset-password.php?token=' . $token;
            $subject = 'Your Access Has Been Approved — Rubiq Financial Partners';
            $body    = "Hi {$user['first_name']},\n\n"
                     . "Your request to access Rubiq Financial Partners' resources has been approved.\n\n"
                     . "Click the link below to set your password and sign in:\n\n"
                     . "$link\n\n"
                     . "This link expires in 7 days.\n\n"
                     . "— Rubiq Financial Partners";

            $headers = 'From: ' . FROM_NAME . ' <' . FROM_EMAIL . ">\r\n"
                     . "Content-Type: text/plain; charset=UTF-8\r\n";

            @mail($user['email'], $subject, $body, $headers);
        }
    } elseif ($action === 'deny') {
        $db->prepare('UPDATE users SET status = "denied" WHERE id = :id')
           ->execute([':id' => $user_id]);
    } elseif ($action === 'delete') {
        $db->prepare('DELETE FROM users WHERE id = :id')
           ->execute([':id' => $user_id]);
    }

    header('Location: index.php?tab=' . ($_POST['tab'] ?? 'pending'));
    exit;
}

// Fetch users
$tab = $_GET['tab'] ?? 'pending';

$pending  = $db->query('SELECT * FROM users WHERE status = "pending" ORDER BY created_at DESC')->fetchAll();
$approved = $db->query('SELECT * FROM users WHERE status = "approved" ORDER BY approved_at DESC')->fetchAll();
$denied   = $db->query('SELECT * FROM users WHERE status = "denied" ORDER BY created_at DESC')->fetchAll();

$counts = [
    'pending'  => count($pending),
    'approved' => count($approved),
    'denied'   => count($denied),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Access Management | Rubiq Admin</title>
  <meta name="robots" content="noindex, nofollow" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; margin: 0; background: #F4F6F9; color: #0F1724; }
    .tab-btn {
      padding: 0.5rem 1.25rem; border-radius: 100px; font-size: 0.8125rem; font-weight: 500;
      border: 1px solid transparent; cursor: pointer; background: transparent; color: #64748b;
      transition: all 0.18s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }
    .tab-btn:hover { background: rgba(26,94,168,0.06); color: #0F1724; }
    .tab-btn.active { background: #1A5EA8; color: white; border-color: #1A5EA8; }
    .badge {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 20px; height: 20px; border-radius: 100px; font-size: 0.6875rem; font-weight: 700;
      padding: 0 5px;
    }
    .badge-gold { background: rgba(196,154,74,0.15); color: #9E7A32; }
    .badge-white { background: rgba(255,255,255,0.25); color: white; }
    .user-row {
      display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: center;
      padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(26,94,168,0.08);
      transition: background 0.15s;
    }
    .user-row:hover { background: rgba(26,94,168,0.02); }
    .user-row:last-child { border-bottom: none; }
    .action-btn {
      padding: 0.4rem 1rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;
      letter-spacing: 0.04em; text-transform: uppercase; cursor: pointer; border: none;
      transition: opacity 0.18s;
    }
    .action-btn:hover { opacity: 0.85; }
    .btn-approve { background: #16a34a; color: white; }
    .btn-deny { background: #dc2626; color: white; }
    .btn-delete { background: #e2e8f0; color: #64748b; }
    @media (max-width: 767px) {
      .user-row { grid-template-columns: 1fr; gap: 0.5rem; }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header style="background:#0F1724;padding:1rem 2rem;display:flex;align-items:center;justify-content:space-between;">
    <div style="display:flex;align-items:center;gap:1.5rem;">
      <span style="font-family:'Playfair Display',serif;font-size:1.125rem;font-weight:700;color:white;">
        Rubiq Admin
      </span>
      <span style="font-size:0.75rem;color:rgba(255,255,255,0.40);">Access Management</span>
    </div>
    <a href="logout.php" style="font-size:0.8125rem;color:rgba(255,255,255,0.50);text-decoration:none;
                                 transition:color 0.18s;" onmouseover="this.style.color='white'"
       onmouseout="this.style.color='rgba(255,255,255,0.50)'">Sign Out</a>
  </header>

  <div style="max-width:960px;margin:2rem auto;padding:0 1.5rem;">

    <!-- Stats bar -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem;">
      <div style="background:white;border-radius:8px;padding:1.25rem 1.5rem;
                  box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <p style="font-size:0.6875rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;
                  color:#C49A5A;margin-bottom:0.25rem;">Pending</p>
        <p style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#0F1724;margin:0;">
          <?= $counts['pending'] ?>
        </p>
      </div>
      <div style="background:white;border-radius:8px;padding:1.25rem 1.5rem;
                  box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <p style="font-size:0.6875rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;
                  color:#16a34a;margin-bottom:0.25rem;">Approved</p>
        <p style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#0F1724;margin:0;">
          <?= $counts['approved'] ?>
        </p>
      </div>
      <div style="background:white;border-radius:8px;padding:1.25rem 1.5rem;
                  box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <p style="font-size:0.6875rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;
                  color:#64748b;margin-bottom:0.25rem;">Denied</p>
        <p style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#0F1724;margin:0;">
          <?= $counts['denied'] ?>
        </p>
      </div>
    </div>

    <!-- Tabs -->
    <div style="display:flex;gap:0.5rem;margin-bottom:1.5rem;">
      <a href="?tab=pending" class="tab-btn <?= $tab === 'pending' ? 'active' : '' ?>">
        Pending
        <?php if ($counts['pending']): ?>
          <span class="badge <?= $tab === 'pending' ? 'badge-white' : 'badge-gold' ?>"><?= $counts['pending'] ?></span>
        <?php endif; ?>
      </a>
      <a href="?tab=approved" class="tab-btn <?= $tab === 'approved' ? 'active' : '' ?>">Approved</a>
      <a href="?tab=denied" class="tab-btn <?= $tab === 'denied' ? 'active' : '' ?>">Denied</a>
    </div>

    <!-- User list -->
    <div style="background:white;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,0.06);overflow:hidden;">

      <?php
        $users = $tab === 'approved' ? $approved : ($tab === 'denied' ? $denied : $pending);

        if (empty($users)):
      ?>
        <div style="padding:3rem;text-align:center;">
          <p style="font-size:0.9rem;color:#94a3b8;">No <?= $tab ?> users.</p>
        </div>
      <?php else: ?>
        <?php foreach ($users as $u): ?>
          <div class="user-row">
            <div>
              <p style="font-weight:600;margin:0 0 2px;">
                <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?>
              </p>
              <p style="font-size:0.8125rem;color:#64748b;margin:0;">
                <?= htmlspecialchars($u['email']) ?>
              </p>
            </div>
            <div>
              <p style="font-size:0.8125rem;color:#94a3b8;margin:0;">
                <?= $u['company'] ? htmlspecialchars($u['company']) : '—' ?>
              </p>
              <p style="font-size:0.75rem;color:#94a3b8;margin:2px 0 0;">
                <?= date('M j, Y', strtotime($u['created_at'])) ?>
                <?php if ($u['last_login']): ?>
                  &middot; Last login: <?= date('M j, Y', strtotime($u['last_login'])) ?>
                <?php endif; ?>
              </p>
            </div>
            <div style="display:flex;gap:0.5rem;">
              <?php if ($tab === 'pending'): ?>
                <form method="POST" style="margin:0;">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>" />
                  <input type="hidden" name="tab" value="pending" />
                  <button type="submit" name="action" value="approve" class="action-btn btn-approve">Approve</button>
                </form>
                <form method="POST" style="margin:0;">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>" />
                  <input type="hidden" name="tab" value="pending" />
                  <button type="submit" name="action" value="deny" class="action-btn btn-deny">Deny</button>
                </form>
              <?php elseif ($tab === 'denied'): ?>
                <form method="POST" style="margin:0;">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>" />
                  <input type="hidden" name="tab" value="denied" />
                  <button type="submit" name="action" value="approve" class="action-btn btn-approve">Approve</button>
                </form>
                <form method="POST" style="margin:0;" onsubmit="return confirm('Permanently delete this user?');">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>" />
                  <input type="hidden" name="tab" value="denied" />
                  <button type="submit" name="action" value="delete" class="action-btn btn-delete">Delete</button>
                </form>
              <?php else: ?>
                <form method="POST" style="margin:0;" onsubmit="return confirm('Revoke this user\'s access?');">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>" />
                  <input type="hidden" name="tab" value="approved" />
                  <button type="submit" name="action" value="deny" class="action-btn btn-deny">Revoke</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>

  </div>

</body>
</html>
