# Rubiq Secure Access — Deployment Guide

## Overview
PHP + MySQL login system with admin-approved registration.
Visitors must request access → you approve → they set a password → they can view gated pages.

---

## Step 1: Create MySQL Database on GoDaddy

1. Log in to your GoDaddy hosting → cPanel
2. Go to **MySQL Databases**
3. Create a new database (e.g., `rubiq_auth`)
4. Create a new database user with a strong password
5. Add the user to the database → grant **ALL PRIVILEGES**
6. Note down: database name, username, password

## Step 2: Run the Database Schema

1. In cPanel, open **phpMyAdmin**
2. Select your new database from the left sidebar
3. Click **Import** tab
4. Choose file: `secure-access/db/setup.sql`
5. Click **Go** — this creates the users, sessions, and admin_users tables

## Step 3: Configure

1. Edit `secure-access/db/config.php`
2. Fill in your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');
   define('DB_USER', 'your_database_user');
   define('DB_PASS', 'your_database_pass');
   ```
3. Update `NOTIFY_EMAIL` to the email that should receive new registration alerts
4. Update `FROM_EMAIL` to your sending email address

## Step 4: Upload

Upload the entire `secure-access/` folder to your website root on GoDaddy
(same level as `index.html` and `Pages/`).

## Step 5: Create Your Admin Account

1. Visit: `https://www.rubiqfinancial.com/secure-access/admin/setup-admin.php`
2. Enter your admin email and a strong password (min 10 characters)
3. Click "Create Admin Account"
4. **DELETE `setup-admin.php` from the server immediately after**

## Step 6: Gate Pages (when ready)

To protect any page:

1. Rename it from `.html` to `.php` (e.g., `resources.html` → `resources.php`)
2. Add this as the **very first line** of the file:
   ```php
   <?php include $_SERVER['DOCUMENT_ROOT'] . '/secure-access/auth/gate.php'; ?>
   ```
3. Update any internal links pointing to that page (change `.html` to `.php`)

That's it. The gate checks for a valid session — if not logged in, redirects to login.

---

## How It Works

### For visitors:
- Hit a gated page → redirected to login
- No account? → "Request Access" form (name, email, company, phone)
- See confirmation: "We'll notify you when access is approved"

### For you (admin):
- Get email notification when someone requests access
- Visit `/secure-access/admin/` → log in
- See pending requests → one-click **Approve** or **Deny**
- Approval sends user an email with a link to set their password

### Data collected per registration:
- First name, last name
- Email address
- Company/firm (optional)
- Phone (optional)
- Date requested, date approved, last login

---

## File Structure

```
secure-access/
├── SETUP.md              ← This file
├── auth/
│   ├── gate.php          ← Include at top of any protected page
│   ├── login.php         ← User login page
│   ├── register.php      ← Request access form
│   ├── logout.php        ← Destroys user session
│   └── reset-password.php ← Forgot password + set password flow
├── admin/
│   ├── admin-auth.php    ← Admin auth helper
│   ├── login.php         ← Admin login
│   ├── index.php         ← Dashboard (approve/deny/revoke users)
│   ├── logout.php        ← Admin logout
│   └── setup-admin.php   ← One-time setup (DELETE after use)
└── db/
    ├── config.php        ← Database credentials + settings
    └── setup.sql         ← Database schema (run once in phpMyAdmin)
```

## Security Notes

- All passwords are hashed with bcrypt (cost 12)
- Sessions use 128-character cryptographic tokens
- Password reset links expire in 1 hour
- Admin and user sessions are separate
- SQL injection protected via PDO prepared statements
- XSS protected via htmlspecialchars() on all output
- All auth pages are noindex/nofollow
- Consider moving `db/config.php` above the web root in production
