<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedInUser()) { header('Location: dashboard.php'); exit; }

$errors = [];
$full_name = $mobile = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $mobile    = trim($_POST['mobile'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if ($full_name === '') $errors[] = tr('पूर्ण नाव आवश्यक आहे.', 'Full name is required.');
    if (!preg_match('/^[0-9]{10}$/', $mobile)) $errors[] = tr('वैध १० अंकी मोबाईल क्रमांक टाका.', 'Enter a valid 10-digit mobile number.');
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = tr('वैध ई-मेल टाका किंवा रिकामे ठेवा.', 'Enter a valid email or leave it blank.');
    if (strlen($password) < 6) $errors[] = tr('पासवर्ड किमान ६ अक्षरी असावा.', 'Password must be at least 6 characters.');
    if ($password !== $confirm) $errors[] = tr('पासवर्ड जुळत नाही.', 'Passwords do not match.');

    if (!$errors && $pdo) {
        try {
            $chk = $pdo->prepare("SELECT id FROM users WHERE mobile = ?");
            $chk->execute([$mobile]);
            if ($chk->fetch()) {
                $errors[] = tr('हा मोबाईल क्रमांक आधीच नोंदणीकृत आहे.', 'This mobile number is already registered.');
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (full_name, mobile, email, password_hash) VALUES (?,?,?,?)");
                $stmt->execute([$full_name, $mobile, $email ?: null, password_hash($password, PASSWORD_DEFAULT)]);
                $_SESSION['user_id']   = $pdo->lastInsertId();
                $_SESSION['user_name'] = $full_name;
                header('Location: dashboard.php');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = tr('डेटाबेस उपलब्ध नाही. कृपया नंतर प्रयत्न करा.', 'Database not available. Please try again later.');
        }
    } elseif (!$pdo) {
        $errors[] = tr('डेटाबेस जोडलेला नाही.', 'Database is not connected.');
    }
}

$pageTitle = tr('नोंदणी', 'Register') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';
?>
<section style="max-width:480px;">
  <div class="card" style="border-top:4px solid var(--green);">
    <h3 style="text-align:center;color:var(--green-dark);"><?php echo tr('नवीन खाते बनवा', 'Create New Account'); ?></h3>

    <?php if ($errors): ?>
      <div class="note-box" style="border-left-color:var(--maroon);">
        <?php foreach ($errors as $e) echo '⚠️ ' . htmlspecialchars($e) . '<br>'; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="form-card">
      <label><?php echo tr('पूर्ण नाव', 'Full Name'); ?></label>
      <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>

      <label><?php echo tr('१० अंकी मोबाईल नंबर', '10-digit Mobile Number'); ?></label>
      <input type="text" name="mobile" maxlength="10" placeholder="९८७६५४३२१०" value="<?php echo htmlspecialchars($mobile); ?>" required>

      <label><?php echo tr('ई-मेल (पर्यायी)', 'Email (optional)'); ?></label>
      <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">

      <label><?php echo tr('पासवर्ड टाका', 'Enter Password'); ?></label>
      <input type="password" name="password" required>

      <label><?php echo tr('पासवर्ड पुन्हा टाका', 'Confirm Password'); ?></label>
      <input type="password" name="confirm_password" required>

      <button type="submit" class="btn solid" style="margin-top:12px;"><?php echo tr('नोंदणी करा', 'Register'); ?></button>
    </form>
    <p style="margin-top:14px;font-size:.9rem;text-align:center;">
      <?php echo tr('आधीच खाते आहे का?', 'Already have an account?'); ?>
      <a href="login.php" style="color:var(--maroon);font-weight:600;"><?php echo tr('लॉगिन करा', 'Login'); ?></a>
    </p>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
