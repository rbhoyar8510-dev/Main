<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$id = (int) ($_GET['id'] ?? 0);
$application = null;
$documents = [];

if ($pdo && $id) {
    $stmt = $pdo->prepare("
        SELECT a.*, c.title_mr, c.title_en, u.mobile AS user_mobile, u.email AS user_email
        FROM applications a
        JOIN certificates c ON c.id = a.certificate_id
        JOIN users u ON u.id = a.user_id
        WHERE a.id = ?
    ");
    $stmt->execute([$id]);
    $application = $stmt->fetch();
    if ($application) {
        $docStmt = $pdo->prepare("SELECT id, doc_label, file_path FROM application_documents WHERE application_id = ?");
        $docStmt->execute([$id]);
        $documents = $docStmt->fetchAll();
    }
}

$pageTitle = tr('अर्ज तपशील', 'Application Details');
require_once __DIR__ . '/includes/layout_head.php';

if (!$application) {
    echo '<div class="note-box" style="border-left-color:var(--maroon);">' . tr('अर्ज सापडला नाही.', 'Application not found.') . '</div>';
    require_once __DIR__ . '/includes/layout_foot.php';
    exit;
}

if (isset($_GET['done'])) {
    echo '<div class="note-box" style="border-left-color:var(--green);">✅ ' . tr('स्थिती अद्ययावत झाली.', 'Status updated.') . '</div>';
}
?>

<div class="card" style="max-width:720px;">
  <h3><?php echo tr($application['title_mr'], $application['title_en']); ?></h3>
  <p><?php echo tr('अर्ज क्रमांक', 'Application No.'); ?>: <b><?php echo htmlspecialchars($application['application_number']); ?></b></p>
  <p><?php echo tr('अर्जदाराचे नाव', 'Applicant Name'); ?>: <?php echo htmlspecialchars($application['applicant_name']); ?></p>
  <p><?php echo tr('मोबाईल', 'Mobile'); ?>: <?php echo htmlspecialchars($application['applicant_mobile']); ?></p>
  <p><?php echo tr('ई-मेल (खाते)', 'Email (account)'); ?>: <?php echo htmlspecialchars($application['user_email']); ?></p>
  <p><?php echo tr('पत्ता', 'Address'); ?>: <?php echo htmlspecialchars($application['applicant_address']); ?></p>
  <p><?php echo tr('कारण', 'Purpose'); ?>: <?php echo htmlspecialchars($application['purpose'] ?: '—'); ?></p>
  <p><?php echo tr('सादर दिनांक', 'Submitted On'); ?>: <?php echo date('d M Y, h:i A', strtotime($application['created_at'])); ?></p>
  <p><?php echo tr('सद्य स्थिती', 'Current Status'); ?>: <span class="status-badge status-<?php echo $application['status']; ?>"><?php echo ucfirst($application['status']); ?></span></p>
  <?php if ($application['certificate_number']): ?>
    <p><?php echo tr('दाखला क्रमांक', 'Certificate Number'); ?>: <b style="color:var(--green-dark);"><?php echo htmlspecialchars($application['certificate_number']); ?></b></p>
  <?php endif; ?>
  <?php if ($application['admin_remarks']): ?>
    <p><?php echo tr('प्रशासक शेरा', 'Admin Remarks'); ?>: <?php echo htmlspecialchars($application['admin_remarks']); ?></p>
  <?php endif; ?>

  <h4 style="margin:16px 0 6px;color:var(--green-dark);"><?php echo tr('अपलोड केलेली कागदपत्रे', 'Uploaded Documents'); ?></h4>
  <div class="doc-list">
    <?php if (!$documents): ?>
      <span style="color:var(--ink-soft);font-size:.88rem;"><?php echo tr('कोणतीही कागदपत्रे नाहीत.', 'No documents uploaded.'); ?></span>
    <?php endif; ?>
    <?php foreach ($documents as $d): ?>
      <a href="view_document.php?id=<?php echo (int)$d['id']; ?>" target="_blank">📄 <?php echo htmlspecialchars($d['doc_label']); ?></a>
    <?php endforeach; ?>
  </div>

  <?php if ($application['status'] === 'pending'): ?>
    <form method="post" action="application_action.php" class="form-card" style="margin-top:18px;">
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <label><?php echo tr('शेरा (नाकारल्यास कारण नमूद करा)', 'Remarks (mention reason if rejecting)'); ?></label>
      <textarea name="remarks" rows="2"></textarea>
      <div class="action-btns">
        <button type="submit" name="action" value="approve" class="btn solid"><?php echo tr('✅ मंजूर करा', '✅ Approve'); ?></button>
        <button type="submit" name="action" value="reject" class="btn reject"><?php echo tr('❌ नाकारा', '❌ Reject'); ?></button>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
