<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedInAdmin()) { header('Location: index.php'); exit; }

$errors = [];
if ($pdo) {
    $count = (int) $pdo->query("SELECT COUNT(*) AS c FROM admins")->fetch()['c'];
    if ($count === 0) { header('Location: setup.php'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$pdo) {
        $errors[] = tr('डेटाबेस जोडलेला नाही.', 'Database is not connected.');
    } else {
        $stmt = $pdo->prepare("SELECT id, full_name, password_hash FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            header('Location: index.php');
            exit;
        } else {
            $errors[] = tr('चुकीचे युजरनेम किंवा पासवर्ड.', 'Incorrect username or password.');
        }
    }
}
$pageTitle = tr('प्रशासक लॉगिन', 'Admin Login');
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
<body class="lang-mr" style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:linear-gradient(135deg,#FBF3DE,var(--cream));">
<section style="max-width:400px;width:100%;">
  <div class="section-head" style="text-align:center;">
    <div style="display:flex;justify-content:center;gap:6px;margin-bottom:8px;">
      <img src="../assets/mh-gp.png" alt="Maharashtra Shasan" style="width:34px;height:34px;object-fit:contain;">
      <img src="../assets/gp-logo.png" alt="Gram Panchayat Tirri" style="width:34px;height:34px;object-fit:contain;">
    </div>
    <h2 style="color:var(--gold-dark);"><?php echo htmlspecialchars($pageTitle); ?></h2>
  </div>
  <?php if (isset($_GET['created'])): ?><div class="note-box" style="border-left-color:var(--green);">✅ <?php echo tr('खाते तयार झाले. आता लॉगिन करा.', 'Account created. Please log in.'); ?></div><?php endif; ?>
  <?php if ($errors): ?><div class="note-box" style="border-left-color:var(--maroon);"><?php foreach ($errors as $e) echo '⚠️ '.htmlspecialchars($e).'<br>'; ?></div><?php endif; ?>
  <form method="post" class="form-card" style="border-color:var(--gold-dark);">
    <label><?php echo tr('युजरनेम', 'Username'); ?></label>
    <input type="text" name="username" required>
    <label><?php echo tr('पासवर्ड', 'Password'); ?></label>
    <input type="password" name="password" required>
    <button type="submit" class="btn solid" style="margin-top:12px;background:var(--gold-dark);border-color:var(--gold-dark);"><?php echo tr('लॉगिन', 'Login'); ?></button>
  </form>
  <p style="margin-top:14px;text-align:center;"><a href="../login.php" style="font-size:.85rem;color:var(--ink-soft);">← <?php echo tr('मुख्य संकेतस्थळावर परत जा', 'Back to main website'); ?></a></p>
</section>
</body>
</html>
