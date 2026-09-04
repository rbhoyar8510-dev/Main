<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$fields = [
    'site_name'    => ['साईट नाव', 'Site Name'],
    'phone'        => ['दूरध्वनी', 'Phone'],
    'email'        => ['ई-मेल', 'Email'],
    'address'      => ['पत्ता', 'Address'],
    'office_hours' => ['कार्यालयीन वेळ', 'Office Hours'],
    'population'   => ['लोकसंख्या', 'Population'],
    'households'   => ['कुटुंब संख्या', 'Households'],
    'wards'        => ['वॉर्ड संख्या', 'Wards'],
    'water_schedule'        => ['पाणीपुरवठा वेळापत्रक', 'Water Supply Schedule'],
    'emergency_police'      => ['पोलीस क्रमांक', 'Police Number'],
    'emergency_ambulance'   => ['अ‍ॅम्ब्युलन्स क्रमांक', 'Ambulance Number'],
    'emergency_electricity' => ['वीज (MSEB) क्रमांक', 'Electricity (MSEB) Number'],
    'emergency_gp'          => ['ग्रामपंचायत हेल्पलाईन', 'Gram Panchayat Helpline'],
];

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("
        INSERT INTO settings (setting_key, value_mr, value_en) VALUES (?,?,?)
        ON DUPLICATE KEY UPDATE value_mr = VALUES(value_mr), value_en = VALUES(value_en)
    ");
    foreach ($fields as $key => $labels) {
        $stmt->execute([$key, trim($_POST[$key . '_mr'] ?? ''), trim($_POST[$key . '_en'] ?? '')]);
    }
    header('Location: settings.php?saved=1');
    exit;
}

$current = [];
if ($pdo) {
    foreach ($pdo->query("SELECT setting_key, value_mr, value_en FROM settings")->fetchAll() as $r) {
        $current[$r['setting_key']] = $r;
    }
}

$pageTitle = tr('साईट सेटिंग्ज', 'Site Settings');
require_once __DIR__ . '/includes/layout_head.php';
?>

<?php if (isset($_GET['saved'])): ?>
  <div class="note-box" style="border-left-color:var(--green);">✅ <?php echo tr('सेटिंग्ज जतन झाल्या.', 'Settings saved.'); ?></div>
<?php endif; ?>
<?php if (!$pdo): ?>
  <div class="note-box" style="border-left-color:var(--maroon);"><?php echo tr('डेटाबेस जोडलेला नाही.', 'Database is not connected.'); ?></div>
<?php endif; ?>

<div class="card" style="max-width:700px;">
  <p style="color:var(--ink-soft);font-size:.88rem;margin-top:0;"><?php echo tr('या सर्व माहिती संपूर्ण संकेतस्थळावर (मुखपृष्ठ, संपर्क पान इ.) आपोआप वापरल्या जातात.', 'All this information is automatically used across the whole website (homepage, contact page, etc.).'); ?></p>
  <form method="post" class="form-card">
    <?php foreach ($fields as $key => $labels): ?>
      <label style="margin-top:16px;border-top:1px dashed var(--line);padding-top:14px;"><?php echo tr($labels[0], $labels[1]); ?> (<?php echo tr('मराठी', 'Marathi'); ?>)</label>
      <input type="text" name="<?php echo $key; ?>_mr" value="<?php echo htmlspecialchars($current[$key]['value_mr'] ?? ''); ?>">
      <label><?php echo tr($labels[0], $labels[1]); ?> (<?php echo tr('इंग्रजी', 'English'); ?>)</label>
      <input type="text" name="<?php echo $key; ?>_en" value="<?php echo htmlspecialchars($current[$key]['value_en'] ?? ''); ?>">
    <?php endforeach; ?>
    <button type="submit" class="btn solid" style="margin-top:18px;"><?php echo tr('सर्व सेटिंग्ज जतन करा', 'Save All Settings'); ?></button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
