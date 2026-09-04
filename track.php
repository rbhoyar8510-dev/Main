<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$result = null;
$searched = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searched = true;
    $number = trim($_POST['application_number'] ?? '');
    if ($pdo && $number !== '') {
        $stmt = $pdo->prepare("
            SELECT a.application_number, a.status, a.certificate_number, a.created_at, a.updated_at, a.admin_remarks,
                   c.title_mr, c.title_en
            FROM applications a
            JOIN certificates c ON c.id = a.certificate_id
            WHERE a.application_number = ?
        ");
        $stmt->execute([$number]);
        $result = $stmt->fetch();
    }
}

$pageTitle = tr('अर्ज ट्रॅक करा', 'Track Application') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';
?>
<section style="max-width:560px;">
  <div class="section-head">
    <span class="eyebrow"><?php echo tr('अर्ज स्थिती', 'Application Status'); ?></span>
    <h2><?php echo tr('अर्ज ट्रॅक करा', 'Track Your Application'); ?></h2>
    <p><?php echo tr('अर्ज सादर करताना मिळालेला अर्ज क्रमांक टाका.', 'Enter the application number you received when you submitted your application.'); ?></p>
  </div>

  <form method="post" class="form-card" style="flex-direction:row;gap:10px;flex-wrap:wrap;">
    <input type="text" name="application_number" placeholder="<?php echo tr('टोकन किंवा बारकोड क्रमांक टाका', 'Enter Token or Barcode Number'); ?>" value="<?php echo htmlspecialchars($_POST['application_number'] ?? ''); ?>" style="flex:1;min-width:200px;" required>
    <button type="submit" class="btn solid"><?php echo tr('तपासा', 'Check Status'); ?></button>
  </form>

  <?php if ($searched): ?>
    <?php if ($result): ?>
      <div class="card" style="margin-top:20px;">
        <h3><?php echo tr($result['title_mr'], $result['title_en']); ?></h3>
        <p><?php echo tr('अर्ज क्रमांक', 'Application No.'); ?>: <b><?php echo htmlspecialchars($result['application_number']); ?></b></p>
        <p><?php echo tr('सादर दिनांक', 'Submitted On'); ?>: <?php echo date('d M Y', strtotime($result['created_at'])); ?></p>
        <p><?php echo tr('स्थिती', 'Status'); ?>:
          <span class="status-badge status-<?php echo $result['status']; ?>">
            <?php echo tr(
              $result['status']==='approved' ? 'मंजूर' : ($result['status']==='rejected' ? 'नाकारले' : 'प्रलंबित'),
              ucfirst($result['status'])
            ); ?>
          </span>
        </p>
        <?php if ($result['status'] === 'approved'): ?>
          <p><?php echo tr('दाखला क्रमांक', 'Certificate Number'); ?>: <b style="color:var(--green-dark);"><?php echo htmlspecialchars($result['certificate_number']); ?></b></p>
          <p style="font-size:.85rem;"><?php echo tr('या क्रमांकाने "दाखला पडताळणी" पानावर सत्यता तपासता येईल.', 'You can verify authenticity of this number on the "Verify Certificate" page.'); ?></p>
        <?php endif; ?>
        <?php if ($result['status'] === 'rejected' && $result['admin_remarks']): ?>
          <p><?php echo tr('कारण', 'Reason'); ?>: <?php echo htmlspecialchars($result['admin_remarks']); ?></p>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="note-box" style="border-left-color:var(--maroon);margin-top:16px;">
        <?php echo tr('या क्रमांकाचा अर्ज सापडला नाही.', 'No application found with this number.'); ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
