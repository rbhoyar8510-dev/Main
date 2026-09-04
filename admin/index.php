<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$stats = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'members' => 0, 'notices' => 0];
if ($pdo) {
    foreach (['pending', 'approved', 'rejected'] as $s) {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM applications WHERE status = ?");
        $stmt->execute([$s]);
        $stats[$s] = (int) $stmt->fetch()['c'];
    }
    $stats['members'] = (int) $pdo->query("SELECT COUNT(*) AS c FROM members")->fetch()['c'];
    $stats['notices'] = (int) $pdo->query("SELECT COUNT(*) AS c FROM notices")->fetch()['c'];
    $recent = $pdo->query("
        SELECT a.id, a.application_number, a.status, a.applicant_name, a.created_at, c.title_mr, c.title_en
        FROM applications a JOIN certificates c ON c.id = a.certificate_id
        ORDER BY a.created_at DESC LIMIT 6
    ")->fetchAll();
} else {
    $recent = [];
}

$pageTitle = tr('डॅशबोर्ड', 'Dashboard');
require_once __DIR__ . '/includes/layout_head.php';
?>

<?php if (!$pdo): ?>
  <div class="note-box" style="border-left-color:var(--maroon);"><?php echo tr('डेटाबेस जोडलेला नाही. includes/db.php तपासा.', 'Database not connected. Check includes/db.php.'); ?></div>
<?php endif; ?>

<div class="stat-cards">
  <div class="sc"><b><?php echo $stats['pending']; ?></b><span><?php echo tr('प्रलंबित अर्ज', 'Pending Applications'); ?></span></div>
  <div class="sc"><b><?php echo $stats['approved']; ?></b><span><?php echo tr('मंजूर अर्ज', 'Approved Applications'); ?></span></div>
  <div class="sc"><b><?php echo $stats['rejected']; ?></b><span><?php echo tr('नाकारलेले अर्ज', 'Rejected Applications'); ?></span></div>
  <div class="sc"><b><?php echo $stats['members']; ?></b><span><?php echo tr('सदस्य नोंदी', 'Member Records'); ?></span></div>
  <div class="sc"><b><?php echo $stats['notices']; ?></b><span><?php echo tr('सूचना', 'Notices'); ?></span></div>
</div>

<h3 style="color:var(--green-dark);font-size:1.1rem;margin-bottom:10px;"><?php echo tr('अलीकडील अर्ज', 'Recent Applications'); ?></h3>
<div class="admin-table-wrap">
  <table>
    <thead><tr>
      <th><?php echo tr('अर्ज क्र.', 'App No.'); ?></th>
      <th><?php echo tr('नाव', 'Name'); ?></th>
      <th><?php echo tr('दाखला', 'Certificate'); ?></th>
      <th><?php echo tr('दिनांक', 'Date'); ?></th>
      <th><?php echo tr('स्थिती', 'Status'); ?></th>
      <th></th>
    </tr></thead>
    <tbody>
      <?php if (!$recent): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--ink-soft);"><?php echo tr('कोणतेही अर्ज नाहीत.', 'No applications yet.'); ?></td></tr>
      <?php endif; ?>
      <?php foreach ($recent as $r): ?>
      <tr>
        <td><?php echo htmlspecialchars($r['application_number']); ?></td>
        <td><?php echo htmlspecialchars($r['applicant_name']); ?></td>
        <td><?php echo tr($r['title_mr'], $r['title_en']); ?></td>
        <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
        <td><span class="status-badge status-<?php echo $r['status']; ?>"><?php echo ucfirst($r['status']); ?></span></td>
        <td><a class="icon-link" href="application_view.php?id=<?php echo (int)$r['id']; ?>"><?php echo tr('पहा →', 'View →'); ?></a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
