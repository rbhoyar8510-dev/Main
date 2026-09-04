<?php
/**
 * DB CONNECTION DIAGNOSTIC TOOL
 * Visit this file in your browser (e.g. yourdomain.com/db_test.php) to see
 * EXACTLY why the database isn't connecting. Delete this file once your
 * site is working - it is not linked from anywhere and is safe to remove.
 */
require_once __DIR__ . '/includes/config.php';

$DB_HOST = 'sql304.free2host.eu.org';
$DB_NAME = 'usesr_42752856_grampanchayat_tirri';
$DB_USER = 'usesr_42752856';
$DB_PASS = '5496bb2057708fa';

// Pull the same values db.php would use, so this test matches your real config
$dbFile = file_get_contents(__DIR__ . '/includes/db.php');
preg_match("/\\\$DB_HOST\s*=\s*'([^']*)'/", $dbFile, $m1);
preg_match("/\\\$DB_NAME\s*=\s*'([^']*)'/", $dbFile, $m2);
preg_match("/\\\$DB_USER\s*=\s*'([^']*)'/", $dbFile, $m3);
preg_match("/\\\$DB_PASS\s*=\s*'([^']*)'/", $dbFile, $m4);
if ($m1) $DB_HOST = $m1[1];
if ($m2) $DB_NAME = $m2[1];
if ($m3) $DB_USER = $m3[1];
if ($m4) $DB_PASS = $m4[1];

echo "<pre style='font-family:monospace;padding:20px;background:#f4f4f4;'>";
echo "Testing connection with values currently in includes/db.php:\n";
echo "HOST: " . htmlspecialchars($DB_HOST) . "\n";
echo "NAME: " . htmlspecialchars($DB_NAME) . "\n";
echo "USER: " . htmlspecialchars($DB_USER) . "\n";
echo "PASS: " . (strlen($DB_PASS) ? str_repeat('*', strlen($DB_PASS)) : '(empty)') . "\n\n";

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✅ SUCCESS - the database connected correctly!\n\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found (" . count($tables) . "):\n";
    foreach ($tables as $t) echo " - $t\n";
    if (!$tables) echo "(none - did you import database/schema.sql yet?)\n";
} catch (PDOException $e) {
    echo "❌ FAILED - here is the exact error:\n\n";
    echo htmlspecialchars($e->getMessage()) . "\n\n";
    echo "Common fixes:\n";
    echo "- '1044 Access denied ... to database' -> wrong DB_NAME, or this DB_USER\n";
    echo "  doesn't have permission on it. Copy the exact name from your hosting panel.\n";
    echo "- '1045 Access denied for user ... (using password: YES/NO)' -> wrong\n";
    echo "  DB_USER or DB_PASS. Reset the password in your hosting panel and update db.php.\n";
    echo "- 'could not find driver' -> the PDO MySQL PHP extension isn't enabled on\n";
    echo "  this hosting - contact your host's support.\n";
    echo "- 'Unknown MySQL server host' -> wrong DB_HOST. Free hosts often need something\n";
    echo "  other than 'localhost', e.g. 'sql304.byetcluster.com' - check your hosting panel.\n";
}
echo "</pre>";
