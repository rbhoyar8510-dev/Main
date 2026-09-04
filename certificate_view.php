<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$pdo || !$id) { http_response_code(404); exit('Not found'); }

$stmt = $pdo->prepare("
    SELECT a.*, c.title_mr, c.title_en
    FROM applications a JOIN certificates c ON c.id = a.certificate_id
    WHERE a.id = ? AND a.status = 'approved'
");
$stmt->execute([$id]);
$app = $stmt->fetch();

if (!$app) { http_response_code(404); exit(tr('प्रमाणपत्र सापडले नाही किंवा मंजूर नाही.', 'Certificate not found or not approved.')); }

// Access control: owning citizen OR any logged-in admin can view/print
$isOwner = isLoggedInUser() && (int) $app['user_id'] === (int) $_SESSION['user_id'];
$isAdmin = isLoggedInAdmin();
if (!$isOwner && !$isAdmin) {
    header('Location: login.php');
    exit;
}

$docStmt = $pdo->prepare("SELECT doc_label FROM application_documents WHERE application_id = ?");
$docStmt->execute([$id]);
$docs = $docStmt->fetchAll();

$certTitle = tr($app['title_mr'], $app['title_en']);
$issueDate = date('d/m/Y', strtotime($app['updated_at']));
$verifyUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/verify.php';
$barcodeImg = 'https://barcodeapi.org/api/128/' . urlencode($app['certificate_number']);

$pageTitle = tr('प्रमाणपत्र', 'Certificate') . ' - ' . $app['certificate_number'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="cert-actions">
  <button onclick="window.print()" class="btn solid">🖨️ <?php echo tr('प्रिंट करा', 'Print'); ?></button>
  <a href="<?php echo $isAdmin ? 'admin/applications.php' : 'dashboard.php'; ?>" class="btn"><?php echo tr('← मागे', '← Back'); ?></a>
</div>

<div class="cert-sheet">
  <div class="cert-top">
    <div class="cert-logos">
      <img src="assets/mh-gp.png" alt="Maharashtra Shasan">
      <img src="assets/gp-logo.png" alt="Gram Panchayat Tirri">
    </div>
    <div class="cert-barcode">
      <img src="<?php echo htmlspecialchars($barcodeImg); ?>" alt="barcode">
      <div class="num"><?php echo htmlspecialchars($app['certificate_number']); ?></div>
    </div>
  </div>

  <div class="cert-office-title"><?php echo tr('ग्रामपंचायत कार्यालय तिर्री', 'Gram Panchayat Office, Tirri'); ?></div>
  <div style="text-align:center;font-size:.85rem;color:#555;margin-bottom:10px;"><?php echo tr('तालुका पवनी, जिल्हा भंडारा, महाराष्ट्र', 'Taluka Pawani, District Bhandara, Maharashtra'); ?></div>

  <div class="cert-meta">
    <div><?php echo tr('क्रमांक', 'No.'); ?> : <b><?php echo htmlspecialchars($app['certificate_number']); ?></b></div>
    <div><?php echo tr('जिल्हा', 'District'); ?> : <?php echo tr('भंडारा', 'Bhandara'); ?></div>
  </div>

  <div class="cert-body">
    <p style="font-weight:700;text-decoration:underline;text-align:center;"><?php echo htmlspecialchars($certTitle); ?></p>
    <p>
      <?php echo tr(
        'दाखला देण्यात येतो की, <b>' . htmlspecialchars($app['applicant_name']) . '</b> राहणार <b>' . htmlspecialchars($app['applicant_address']) . '</b> हे ग्रामपंचायत तिर्री, तालुका पवनी, जिल्हा भंडारा येथील रहिवासी असून त्यांनी सादर केलेल्या अर्जानुसार व खालील कागदपत्रांच्या आधारे त्यांना "' . htmlspecialchars($certTitle) . '" हा दाखला देण्यात येत आहे.',
        'This is to certify that <b>' . htmlspecialchars($app['applicant_name']) . '</b>, residing at <b>' . htmlspecialchars($app['applicant_address']) . '</b>, is a resident of Gram Panchayat Tirri, Taluka Pawani, District Bhandara. Based on the application submitted and the documents listed below, this "' . htmlspecialchars($certTitle) . '" is hereby issued.'
      ); ?>
    </p>
    <?php if ($app['purpose']): ?>
      <p><?php echo tr('कारण', 'Purpose'); ?>: <?php echo htmlspecialchars($app['purpose']); ?></p>
    <?php endif; ?>
  </div>

  <div class="cert-doclist">
    <?php echo tr('सादर केलेल्या दस्तऐवज / पुराव्याचे तपशील', 'Details of Documents / Proof Submitted'); ?>:
    <ol>
      <?php foreach ($docs as $d): ?>
        <li><?php echo htmlspecialchars($d['doc_label']); ?></li>
      <?php endforeach; ?>
    </ol>
  </div>

  <div class="cert-sign-row">
    <div style="font-size:.85rem;">
      <?php echo tr('स्थळ', 'Place'); ?> : <?php echo tr('तिर्री', 'Tirri'); ?><br>
      <?php echo tr('दिनांक', 'Date'); ?> : <?php echo $issueDate; ?>
    </div>
    <div class="cert-sign-box">
      <div style="font-size:1.4rem;">✅</div>
      <div class="valid"><?php echo tr('स्वाक्षरी वैध / Signature valid', 'Signature valid'); ?></div>
      <div style="margin-top:6px;font-weight:700;"><?php echo tr('सरपंच / ग्रामसेवक', 'Sarpanch / Gram Sevak'); ?></div>
      <div style="font-size:.8rem;color:#555;"><?php echo tr('ग्रामपंचायत तिर्री', 'Gram Panchayat Tirri'); ?></div>
    </div>
  </div>

  <div class="cert-footer-note">
    <?php echo tr(
      'माहिती तंत्रज्ञान (आयटी) अधिनियम, २००० नुसार डिजिटल स्वाक्षरी असलेला हा दस्तऐवज कायदेशीररित्या वैध आहे.',
      'This document, bearing a digital signature under the Information Technology Act, 2000, is legally valid.'
    ); ?><br>
    <?php echo tr('पडताळणीसाठी', 'For verification, visit'); ?>: <?php echo htmlspecialchars($verifyUrl); ?> —
    <?php echo tr('दाखला क्रमांक टाका', 'enter certificate number'); ?>: <b><?php echo htmlspecialchars($app['certificate_number']); ?></b>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
