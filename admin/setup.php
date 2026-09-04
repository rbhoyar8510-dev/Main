<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

if (!$pdo) {
    die(tr('डेटाबेस जोडलेला नाही. आधी database/schema.sql इम्पोर्ट करा व includes/db.php सेट करा.', 'Database not connected. Import database/schema.sql first and configure includes/db.php.'));
}

// Lock this page once at least one admin account exists.
$count = (int) $pdo->query("SELECT COUNT(*) AS c FROM admins")->fetch()['c'];
if ($count > 0) {
    header('Location: login.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($username === '' || $full_name === '') $errors[] = tr('सर्व माहिती भरा.', 'Please fill all fields.');
    if (strlen($password) < 6) $errors[] = tr('पासवर्ड किमान ६ अक्षरी असावा.', 'Password must be at least 6 characters.');
    if ($password !== $confirm) $errors[] = tr('पासवर्ड जुळत नाही.', 'Passwords do not match.');

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO admins (username, full_name, password_hash) VALUES (?,?,?)");
        $stmt->execute([$username, $full_name, password_hash($password, PASSWORD_DEFAULT)]);
        header('Location: login.php?created=1');
        exit;
    }
}
$pageTitle = tr('पहिले प्रशासक खाते तयार करा', 'Create First Admin Account');
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<link rel="icon" href="../assets/gp-logo.png">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
</head>
<body class="lang-mr" style="display:flex;align-items:center;justify-content:center;min-height:100vh;">
<section style="max-width:440px;width:100%;">
  <div class="section-head"><h2><?php echo htmlspecialchars($pageTitle); ?></h2>
  <p style="color:var(--ink-soft);font-size:.88rem;"><?php echo tr('ही पायरी फक्त एकदाच दिसते - पहिले प्रशासक खाते तयार झाल्यावर हे पान बंद होईल.', 'This step only appears once — this page locks itself after the first admin account is created.'); ?></p></div>
  <?php if ($errors): ?><div class="note-box" style="border-left-color:var(--maroon);"><?php foreach ($errors as $e) echo '⚠️ '.htmlspecialchars($e).'<br>'; ?></div><?php endif; ?>
  <form method="post" class="form-card">
    <label><?php echo tr('युजरनेम', 'Username'); ?></label>
    <input type="text" name="username" required>
    <label><?php echo tr('पूर्ण नाव', 'Full Name'); ?></label>
    <input type="text" name="full_name" required>
    <label><?php echo tr('पासवर्ड', 'Password'); ?></label>
    <input type="password" name="password" required>
    <label><?php echo tr('पासवर्ड पुन्हा टाका', 'Confirm Password'); ?></label>
    <input type="password" name="confirm_password" required>
    <button type="submit" class="btn solid" style="margin-top:12px;"><?php echo tr('खाते तयार करा', 'Create Account'); ?></button>
  </form>
</section>
</body>
</html>
