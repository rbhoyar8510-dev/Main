<?php
/**
 * Database connection (PDO/MySQL).
 * EDIT these 4 values to match your hosting/XAMPP database credentials.
 *
 * On shared hosting (InfinityFree, ByetCluster, Hostinger, etc.) your host
 * gives you the exact DB name/host/username/password in your hosting panel -
 * it usually looks like "usesr_42752856_grampanchayat_tirri", NOT the plain
 * name below. Copy those exact values from your hosting panel here.
 */
$DB_HOST = 'sql304.free2host.eu.org';
$DB_NAME = 'usesr_42752856_grampanchayat_tirri';   // <-- replace with YOUR actual DB name
$DB_USER = 'usesr_42752856';                        // <-- replace with YOUR actual DB username
$DB_PASS = '5496bb2057708fa';                                      // <-- replace with YOUR actual DB password

$pdo = null;
try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // No DB yet (or wrong credentials) - pages fall back to their built-in sample data below.
    $pdo = null;
}

// Load site settings (phone/email/address/etc.) from DB, with fallback to config.php constants.
$siteSettings = [];
if ($pdo) {
    try {
        $rows = $pdo->query("SELECT setting_key, value_mr, value_en FROM settings")->fetchAll();
        foreach ($rows as $r) {
            $siteSettings[$r['setting_key']] = ['mr' => $r['value_mr'], 'en' => $r['value_en']];
        }
    } catch (PDOException $e) {
        // settings table not created yet - ignore, fallback constants will be used
    }
}

/**
 * setting($key, $fallback_mr, $fallback_en)
 * Returns the DB value for the current language if available, else the given fallback.
 */
function setting($key, $fallback_mr, $fallback_en) {
    global $siteSettings;
    if (isset($siteSettings[$key])) {
        return tr($siteSettings[$key]['mr'], $siteSettings[$key]['en']);
    }
    return tr($fallback_mr, $fallback_en);
}
