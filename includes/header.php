<?php
// Expects $pageTitle to be set by the calling page before this include.
if (!isset($pageTitle)) {
    $pageTitle = SITE_NAME_MR . ' | ' . SITE_NAME_EN;
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<link rel="icon" href="assets/gp-logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Devanagari:wght@500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600&family=Lora:ital,wght@0,500;0,600;0,700;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="lang-<?php echo $lang; ?>">

<?php if (isset($_SESSION['user_id'])): ?>
<div class="user-strip">
  <div class="user-strip-inner">
    <span>👤 <?php echo tr('नागरिक', 'Citizen'); ?>: <b><?php echo htmlspecialchars($_SESSION['user_name']); ?></b></span>
    <a href="logout.php" class="logout-pill"><?php echo tr('लॉगआउट', 'Logout'); ?></a>
  </div>
</div>
<?php else: ?>
<div class="util-strip">
  <div class="util-strip-inner">
    <span><?php echo tr('ग्रामपंचायत ई-प्रशासन पोर्टल', 'Gram Panchayat e-Governance Portal'); ?></span>
    <span class="util-badge"><?php echo tr('डिजिटल तिर्री', 'Digital Tirri'); ?></span>
  </div>
</div>
<?php endif; ?>

<header class="gp-header">
  <div class="header-band">
    <div class="header-inner">
      <div class="brand">
        <div class="brand-logos">
          <img src="assets/mh-gp.png" alt="Maharashtra Shasan" class="logo-seal">
          <img src="assets/gp-logo.png" alt="Gram Panchayat Tirri" class="logo-main">
        </div>
        <div class="brand-text">
          <div class="brand-govt"><?php echo tr('महाराष्ट्र शासन', 'Government of Maharashtra'); ?></div>
          <div class="brand-mr"><?php echo SITE_NAME_MR; ?></div>
          <div class="brand-en"><?php echo tr('ग्रामपंचायत तिर्री - डिजिटल ई-सेवा केंद्र (ता. पवनी, जि. भंडारा)', 'Gram Panchayat Tirri - Digital e-Seva Center (Tal. Pawani, Dist. Bhandara)'); ?></div>
        </div>
      </div>
      <div class="lang-toggle" role="group" aria-label="Language selector">
        <a href="?lang=mr" class="<?php echo $lang === 'mr' ? 'active' : ''; ?>">मराठी</a>
        <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">English</a>
      </div>
    </div>
  </div>
  <?php include __DIR__ . '/nav.php'; ?>
</header>
