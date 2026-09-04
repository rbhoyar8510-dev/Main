<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$result = null;
$searched = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searched = true;
    $certNumber = trim($_POST['certificate_number'] ?? '');
    if ($pdo && $certNumber !== '') {
        $stmt = $pdo->prepare("
            SELECT a.certificate_number, a.applicant_name, a.updated_at,
                   c.title_mr, c.title_en
            FROM applications a
            JOIN certificates c ON c.id = a.certificate_id
            WHERE a.certificate_number = ? AND a.status = 'approved'
        ");
        $stmt->execute([$certNumber]);
        $result = $stmt->fetch();
    }
}

$pageTitle = tr('दाखला पडताळणी', 'Verify Certificate') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';
?>
<section style="max-width:560px;">
  <div class="section-head">
    <span class="eyebrow"><?php echo tr('सत्यता तपासणी', 'Authenticity Check'); ?></span>
    <h2><?php echo tr('दाखला पडताळणी', 'Verify a Certificate'); ?></h2>
    <p><?php echo tr('दाखल्यावरील दाखला क्रमांक टाकून तो अस्सल आहे की नाही ते तपासा.', "Enter the certificate number printed on the certificate to check whether it is genuine."); ?></p>
  </div>

  <form method="post" class="form-card" style="flex-direction:row;gap:10px;flex-wrap:wrap;">
    <input type="text" name="certificate_number" placeholder="GPT/2026/XXXXXX" value="<?php echo htmlspecialchars($_POST['certificate_number'] ?? ''); ?>" style="flex:1;min-width:200px;" required>
    <button type="submit" class="btn solid"><?php echo tr('पडताळणी करा', 'Verify'); ?></button>
  </form>

  <?php if ($searched): ?>
    <?php if ($result): ?>
      <div class="card" style="margin-top:20px;border-color:var(--green);">
        <h3 style="color:var(--green-dark);">✅ <?php echo tr('अस्सल दाखला', 'Genuine Certificate'); ?></h3>
        <p><?php echo tr('दाखला प्रकार', 'Certificate Type'); ?>: <b><?php echo tr($result['title_mr'], $result['title_en']); ?></b></p>
        <p><?php echo tr('धारकाचे नाव', 'Holder Name'); ?>: <b><?php echo htmlspecialchars($result['applicant_name']); ?></b></p>
        <p><?php echo tr('दाखला क्रमांक', 'Certificate Number'); ?>: <b><?php echo htmlspecialchars($result['certificate_number']); ?></b></p>
        <p><?php echo tr('जारी दिनांक', 'Issued On'); ?>: <?php echo date('d M Y', strtotime($result['updated_at'])); ?></p>
        <p style="font-size:.8rem;color:var(--ink-soft);"><?php echo tr('हा दाखला ग्रामपंचायत तिर्री द्वारे अधिकृतपणे जारी करण्यात आला आहे.', 'This certificate was officially issued by Gram Panchayat Tirri.'); ?></p>
      </div>
    <?php else: ?>
      <div class="note-box" style="border-left-color:var(--maroon);margin-top:16px;">
        ⚠️ <?php echo tr('हा दाखला क्रमांक अवैध आहे किंवा सापडला नाही. कृपया क्रमांक तपासा किंवा ग्रामपंचायत कार्यालयाशी संपर्क साधा.', 'This certificate number is invalid or not found. Please check the number or contact the Gram Panchayat office.'); ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
