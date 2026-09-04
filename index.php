<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = SITE_NAME_MR . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';

$sarpanch = null;
if ($pdo) {
    $stmt = $pdo->query("SELECT * FROM members ORDER BY sort_order ASC LIMIT 1");
    $sarpanch = $stmt->fetch();
}
?>

<section style="max-width:760px;">

  <!-- Water supply schedule alert -->
  <div class="info-card accent-blue">
    <div class="info-card-head">
      <h3>🚰 <?php echo tr('दैनिक नळ पाणीपुरवठा वेळापत्रक व अलर्ट', 'Daily Water Supply Schedule & Alerts'); ?></h3>
      <a href="notices.php" class="info-pill-btn"><?php echo tr('आजचे अपडेट', "Today's Update"); ?></a>
    </div>
    <p style="margin:8px 0 0;font-size:.88rem;color:var(--ink-soft);"><?php echo setting('water_schedule', 'सोमवार, बुधवार, शुक्रवार - सकाळी ६ ते ८ वाजेपर्यंत नळपुरवठा राहील.', 'Water supply on Monday, Wednesday, Friday - 6 AM to 8 AM.'); ?></p>
  </div>

  <!-- Track application -->
  <div class="info-card accent-blue-top">
    <h3 style="margin-bottom:12px;">🔍 <?php echo tr('थेट अर्ज स्थिती ट्रॅक करा (Track by Token / Barcode)', 'Track Application Status (Track by Token / Barcode)'); ?></h3>
    <form method="post" action="track.php" style="display:flex;gap:10px;flex-wrap:wrap;">
      <input type="text" name="application_number" placeholder="<?php echo tr('टोकन किंवा बारकोड क्रमांक टाका', 'Enter Token or Barcode Number'); ?>" style="flex:1;min-width:180px;padding:10px 12px;border:1.5px solid var(--line);border-radius:6px;font-family:inherit;" required>
      <button type="submit" class="btn solid"><?php echo tr('शोधा', 'Search'); ?></button>
    </form>
  </div>

  <!-- Login / Register / Officer tabbed card -->
  <?php if (isset($_SESSION['user_id'])): ?>
    <div class="info-card accent-green-top" style="text-align:center;">
      <p><?php echo tr('आपण लॉगिन आहात', 'You are logged in'); ?>, <b><?php echo htmlspecialchars($_SESSION['user_name']); ?></b>.</p>
      <a href="dashboard.php" class="btn solid"><?php echo tr('माझे अर्ज पहा', 'View My Applications'); ?></a>
    </div>
  <?php else: ?>
    <?php require __DIR__ . '/includes/portal_card.php'; ?>
  <?php endif; ?>

  <!-- Scheme info -->
  <div class="info-card">
    <h3>📋 <?php echo tr('शासकीय योजना व पात्रता माहिती कक्ष', 'Government Schemes & Eligibility Info'); ?></h3>
    <p style="margin:8px 0 10px;font-size:.88rem;color:var(--ink-soft);"><?php echo tr('गावात राबवल्या जाणाऱ्या योजनांची यादी व पात्रता तपासा.', 'View the list of schemes running in the village and check eligibility.'); ?></p>
    <a href="yojana.php" class="card-link"><?php echo tr('योजना पहा →', 'View Schemes →'); ?></a>
  </div>

  <!-- Gallery -->
  <div class="info-card">
    <h3>🖼️ <?php echo tr('ग्रामपंचायत फोटो व विकास गॅलरी', 'Gram Panchayat Photo & Development Gallery'); ?></h3>
    <div class="gallery-grid" style="margin-top:10px;">
      <div class="gallery-box">📷<br><?php echo tr('लवकरच उपलब्ध होईल', 'Coming soon'); ?></div>
      <div class="gallery-box">📷<br><?php echo tr('लवकरच उपलब्ध होईल', 'Coming soon'); ?></div>
    </div>
  </div>

  <!-- Emergency numbers -->
  <div class="info-card">
    <h3>🚨 <?php echo tr('आपत्कालीन व शासकीय मदत क्रमांक', 'Emergency & Government Helpline Numbers'); ?></h3>
    <div class="emergency-grid" style="margin-top:10px;">
      <div class="e-item police">🚓 <?php echo tr('पोलीस', 'Police'); ?>:<b><?php echo setting('emergency_police', '112 / 100', '112 / 100'); ?></b></div>
      <div class="e-item ambulance">🚑 <?php echo tr('अ‍ॅम्ब्युलन्स', 'Ambulance'); ?>:<b><?php echo setting('emergency_ambulance', '108 / 102', '108 / 102'); ?></b></div>
      <div class="e-item electricity">⚡ <?php echo tr('वीज (MSEB)', 'Electricity (MSEB)'); ?>:<b><?php echo setting('emergency_electricity', '1912', '1912'); ?></b></div>
      <div class="e-item gp">🏛️ <?php echo tr('ग्रामपंचायत', 'Gram Panchayat'); ?>:<b><?php echo setting('emergency_gp', '07185-XXXXXX', '07185-XXXXXX'); ?></b></div>
    </div>
  </div>

  <!-- Office bearers preview -->
  <div class="info-card">
    <h3>🏛️ <?php echo tr('ग्रामपंचायत पदाधिकारी मंडळ', 'Gram Panchayat Office Bearers'); ?></h3>
    <?php if ($sarpanch): ?>
      <div class="bearer-mini" style="margin-top:10px;">
        <div class="av">👤</div>
        <div>
          <div style="font-weight:700;"><?php echo htmlspecialchars($sarpanch['name']); ?></div>
          <div style="font-size:.82rem;color:var(--ink-soft);"><?php echo tr($sarpanch['position_mr'], $sarpanch['position_en']); ?></div>
        </div>
      </div>
    <?php else: ?>
      <p style="font-size:.85rem;color:var(--ink-soft);margin-top:8px;"><?php echo tr('माहिती लवकरच उपलब्ध होईल.', 'Information coming soon.'); ?></p>
    <?php endif; ?>
    <a href="members.php" class="card-link"><?php echo tr('संपूर्ण यादी पहा →', 'View full list →'); ?></a>
  </div>

  <!-- Quick links to other pages -->
  <div class="grid" style="margin-top:6px;">
    <div class="card">
      <h3><?php echo tr('दाखले', 'Certificates'); ?></h3>
      <p><?php echo tr('रहिवासी, उत्पन्न, जन्म-मृत्यू व इतर सर्व दाखल्यांची यादी.', 'List of residence, income, birth-death and all other certificates.'); ?></p>
      <a href="dakhle.php" class="card-link"><?php echo tr('पहा →', 'View →'); ?></a>
    </div>
    <div class="card">
      <h3><?php echo tr('दाखला पडताळणी', 'Verify Certificate'); ?></h3>
      <p><?php echo tr('जारी केलेला दाखला अस्सल आहे की नाही ते तपासा.', 'Check whether an issued certificate is genuine.'); ?></p>
      <a href="verify.php" class="card-link"><?php echo tr('पडताळणी करा →', 'Verify →'); ?></a>
    </div>
    <div class="card">
      <h3><?php echo tr('संपर्क', 'Contact'); ?></h3>
      <p><?php echo tr('ग्रामपंचायत कार्यालयाचा पत्ता, दूरध्वनी व नकाशा.', "Office address, phone number and map."); ?></p>
      <a href="contact.php" class="card-link"><?php echo tr('पहा →', 'View →'); ?></a>
    </div>
  </div>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
