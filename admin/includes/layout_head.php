<?php
// Expects $pageTitle (admin page heading) to be set before include.
$adminNav = [
    'index.php'        => ['डॅशबोर्ड', 'Dashboard'],
    'applications.php'  => ['अर्ज व्यवस्थापन', 'Applications'],
    'members.php'       => ['सरपंच व सदस्य', 'Members'],
    'schemes.php'       => ['योजना', 'Schemes'],
    'notices.php'       => ['सूचना', 'Notices'],
    'certificates.php'  => ['दाखले', 'Certificates'],
    'settings.php'      => ['सेटिंग्ज', 'Settings'],
];
$currentAdminPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?> - Admin</title>
<link rel="icon" href="../assets/gp-logo.png">
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Devanagari:wght@600;700&family=Noto+Sans+Devanagari:wght@400;500;600&family=Lora:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
</head>
<body class="lang-<?php echo $lang; ?>">
<div class="admin-shell">
  <div class="admin-sidebar">
    <div class="ab"><img src="../assets/gp-logo.png" alt="logo" style="width:36px;height:36px;border-radius:50%;vertical-align:middle;margin-right:8px;background:#fff;"><?php echo tr('ग्रा.पं. तिर्री', 'GP Tirri'); ?><br><span style="font-size:.7rem;opacity:.8;"><?php echo tr('प्रशासक विभाग', 'Admin Panel'); ?></span></div>
    <?php foreach ($adminNav as $file => $labels): ?>
      <a href="<?php echo $file; ?>" class="<?php echo $currentAdminPage === $file ? 'active' : ''; ?>"><?php echo tr($labels[0], $labels[1]); ?></a>
    <?php endforeach; ?>
    <a href="logout.php" style="margin-top:14px;border-top:1px solid rgba(255,255,255,.15);padding-top:14px;"><?php echo tr('लॉगआऊट', 'Logout'); ?></a>
    <a href="../index.php" style="opacity:.75;">← <?php echo tr('संकेतस्थळ पहा', 'View Website'); ?></a>
  </div>
  <div class="admin-main">
    <div class="admin-topbar">
      <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
      <div style="display:flex;align-items:center;gap:14px;">
        <div class="lang-toggle" style="font-size:.75rem;">
          <a href="?lang=mr" class="<?php echo $lang === 'mr' ? 'active' : ''; ?>">मराठी</a>
          <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
        </div>
        <div style="font-size:.85rem;color:var(--ink-soft);">👤 <?php echo htmlspecialchars($_SESSION['admin_name'] ?? ''); ?></div>
      </div>
    </div>
