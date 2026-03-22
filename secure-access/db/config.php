<?php
/**
 * Database Configuration — Rubiq Secure Access
 *
 * SETUP INSTRUCTIONS (GoDaddy):
 * 1. Log in to your GoDaddy cPanel → MySQL Databases
 * 2. Create a new database (e.g., "rubiq_auth")
 * 3. Create a new database user with a strong password
 * 4. Add the user to the database with ALL PRIVILEGES
 * 5. Fill in the values below
 * 6. Run setup.sql in phpMyAdmin (cPanel → phpMyAdmin → select your DB → Import → choose setup.sql)
 *
 * IMPORTANT: In production, move this file ABOVE the web root if possible.
 * On GoDaddy, that would be one level above public_html.
 */

define('DB_HOST', 'localhost');          // GoDaddy usually uses localhost
define('DB_NAME', 'your_database_name'); // e.g., youraccount_rubiq_auth
define('DB_USER', 'your_database_user'); // e.g., youraccount_rubiquser
define('DB_PASS', 'your_database_pass'); // The password you set in cPanel

// Admin credentials for the admin dashboard
define('ADMIN_EMAIL', 'andreas@rubiqfinancial.com');
define('ADMIN_PASS_HASH', ''); // Will be set during setup — see SETUP.md

// Site settings
define('SITE_NAME', 'Rubiq Financial Partners');
define('SITE_URL', 'https://www.rubiqfinancial.com');
define('NOTIFY_EMAIL', 'andreas@rubiqfinancial.com'); // Gets notified on new registrations
define('SESSION_LIFETIME', 60 * 60 * 24 * 30);       // 30 days

// Email settings (GoDaddy supports PHP mail() by default)
define('FROM_EMAIL', 'noreply@rubiqfinancial.com');
define('FROM_NAME', 'Rubiq Financial Partners');

/**
 * Database connection helper
 */
function getDB() {
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        die('A system error occurred. Please try again later.');
    }
}
