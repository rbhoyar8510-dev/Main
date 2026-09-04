<?php
// $currentPage is set in header.php before this file is included.
$navItems = [
    'index.php'    => ['मुख्यपृष्ठ', 'Home'],
    'about.php'    => ['ग्रामपंचायत विषयी', 'About'],
    'members.php'  => ['सरपंच व सदस्य', 'Sarpanch & Members'],
    'yojana.php'   => ['योजना', 'Schemes'],
    'notices.php'  => ['सूचना/परिपत्रके', 'Notices'],
    'dakhle.php'   => ['दाखले', 'Certificates'],
    'track.php'    => ['अर्ज ट्रॅक करा', 'Track Application'],
    'verify.php'   => ['दाखला पडताळणी', 'Verify Certificate'],
    'contact.php'  => ['संपर्क', 'Contact'],
];
?>
<nav>
  <div class="nav-inner">
    <?php foreach ($navItems as $file => $labels): ?>
      <a href="<?php echo $file; ?>" class="<?php echo ($currentPage === $file) ? 'active' : ''; ?>">
        <?php echo tr($labels[0], $labels[1]); ?>
      </a>
    <?php endforeach; ?>
    <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="login.php" class="<?php echo ($currentPage === 'login.php') ? 'active' : ''; ?>" style="color:var(--gold);">
        <?php echo tr('लॉगिन / नोंदणी', 'Login / Register'); ?>
      </a>
    <?php else: ?>
      <a href="dashboard.php" class="<?php echo ($currentPage === 'dashboard.php') ? 'active' : ''; ?>" style="color:var(--gold);">
        📋 <?php echo tr('माझे अर्ज', 'My Applications'); ?>
      </a>
    <?php endif; ?>
  </div>
</nav>
