<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireUserLogin();

$applications = [];
if ($pdo) {
    $stmt = $pdo->prepare("
        SELECT a.id, a.application_number, a.status, a.certificate_number, a.created_at,
               c.title_mr, c.title_en
        FROM applications a
        JOIN certificates c ON c.id = a.certificate_id
        WHERE a.user_id = ?
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $applications = $stmt->fetchAll();
}

$pageTitle = tr('माझे अर्ज', 'My Applications') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';
?>
<section>
  <div class="section-head" style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:14px;">
    <div>
      <span class="eyebrow"><?php echo tr('स्वागत आहे', 'Welcome'); ?>, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
      <h2><?php echo tr('माझे दाखला अर्ज', 'My Certificate Applications'); ?></h2>
    </div>
    <a href="dakhle.php" class="btn solid"><?php echo tr('+ नवीन अर्ज करा', '+ Apply for New Certificate'); ?></a>
  </div>

  <div class="block">
  <?php if (!$pdo): ?>
    <div class="note-box" style="border-left-color:var(--maroon);"><?php echo tr('डेटाबेस जोडलेला नाही.', 'Database is not connected.'); ?></div>
  <?php elseif (!$applications): ?>
    <p style="color:var(--ink-soft);"><?php echo tr('अद्याप कोणताही अर्ज केलेला नाही.', 'You have not submitted any applications yet.'); ?></p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th><?php echo tr('अर्ज क्रमांक', 'Application No.'); ?></th>
          <th><?php echo tr('दाखला प्रकार', 'Certificate Type'); ?></th>
          <th><?php echo tr('दिनांक', 'Date'); ?></th>
          <th><?php echo tr('स्थिती', 'Status'); ?></th>
          <th><?php echo tr('दाखला क्रमांक', 'Certificate No.'); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($applications as $a): ?>
        <tr>
          <td><?php echo htmlspecialchars($a['application_number']); ?></td>
          <td><?php echo tr($a['title_mr'], $a['title_en']); ?></td>
          <td><?php echo date('d M Y', strtotime($a['created_at'])); ?></td>
          <td><span class="status-badge status-<?php echo $a['status']; ?>">
              <?php echo tr(
                $a['status']==='approved' ? 'मंजूर' : ($a['status']==='rejected' ? 'नाकारले' : 'प्रलंबित'),
                ucfirst($a['status'])
              ); ?>
          </span></td>
          <td><?php echo $a['certificate_number'] ? htmlspecialchars($a['certificate_number']) : '—'; ?></td>
          <td><?php if ($a['status'] === 'approved'): ?><a href="certificate_view.php?id=<?php echo (int)$a['id']; ?>" class="icon-link" target="_blank">📄 <?php echo tr('पहा/प्रिंट', 'View/Print'); ?></a><?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <div class="note-box" style="margin-top:20px;">
    <?php echo tr(
      '📝 अर्जाची स्थिती ट्रॅक करण्यासाठी वरील अर्ज क्रमांक वापरा, किंवा लॉगिनशिवाय ट्रॅक करण्यासाठी "अर्ज ट्रॅक करा" पान वापरा.',
      '📝 Use the application number above to track your application, or use the "Track Application" page to check status without logging in.'
    ); ?>
  </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
