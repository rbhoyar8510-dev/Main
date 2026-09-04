<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

// Quick inline approve/reject handling (posts back to this same page)
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($id && in_array($action, ['approve', 'reject'], true)) {
        if ($action === 'approve') {
            $certNumber = generateCertificateNumber();
            $pdo->prepare("UPDATE applications SET status='approved', certificate_number=? WHERE id=? AND status='pending'")->execute([$certNumber, $id]);
        } else {
            $pdo->prepare("UPDATE applications SET status='rejected' WHERE id=? AND status='pending'")->execute([$id]);
        }
    }
    header('Location: applications.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
    exit;
}

$filter = $_GET['status'] ?? 'all';
$allowed = ['all', 'pending', 'approved', 'rejected'];
if (!in_array($filter, $allowed, true)) $filter = 'all';
$token = trim($_GET['token'] ?? '');

$applications = [];
if ($pdo) {
    $sql = "
        SELECT a.id, a.application_number, a.status, a.applicant_name, a.applicant_mobile, a.certificate_number, a.created_at, c.title_mr, c.title_en
        FROM applications a JOIN certificates c ON c.id = a.certificate_id
        WHERE 1=1
    ";
    $params = [];
    if ($filter !== 'all') { $sql .= " AND a.status = ?"; $params[] = $filter; }
    if ($token !== '') { $sql .= " AND a.application_number LIKE ?"; $params[] = '%' . $token . '%'; }
    $sql .= " ORDER BY a.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $applications = $stmt->fetchAll();

    // fetch documents for all shown applications in one go
    $docsByApp = [];
    if ($applications) {
        $ids = array_column($applications, 'id');
        $in = implode(',', array_fill(0, count($ids), '?'));
        $docStmt = $pdo->prepare("SELECT application_id, id, doc_label FROM application_documents WHERE application_id IN ($in)");
        $docStmt->execute($ids);
        foreach ($docStmt->fetchAll() as $d) {
            $docsByApp[$d['application_id']][] = $d;
        }
    }
}

$pageTitle = tr('अर्ज व्यवस्थापन', 'Applications');
require_once __DIR__ . '/includes/layout_head.php';
?>

<form method="get" class="token-search">
  <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter); ?>">
  <span>🔍</span>
  <input type="text" name="token" placeholder="<?php echo tr('टोकन/अर्ज क्रमांकाने शोधा...', 'Search by Token / Application No...'); ?>" value="<?php echo htmlspecialchars($token); ?>">
  <button type="submit" class="btn solid"><?php echo tr('शोधा', 'Search'); ?></button>
  <?php if ($token): ?><a href="?status=<?php echo $filter; ?>" class="btn"><?php echo tr('साफ करा', 'Clear'); ?></a><?php endif; ?>
</form>

<div class="filter-tabs">
  <a href="?status=all" class="<?php echo $filter==='all'?'active':''; ?>"><?php echo tr('सर्व', 'All'); ?></a>
  <a href="?status=pending" class="<?php echo $filter==='pending'?'active':''; ?>"><?php echo tr('प्रलंबित', 'Pending'); ?></a>
  <a href="?status=approved" class="<?php echo $filter==='approved'?'active':''; ?>"><?php echo tr('मंजूर', 'Approved'); ?></a>
  <a href="?status=rejected" class="<?php echo $filter==='rejected'?'active':''; ?>"><?php echo tr('नाकारलेले', 'Rejected'); ?></a>
</div>

<div class="admin-table-wrap">
  <table>
    <thead><tr>
      <th><?php echo tr('टोकन', 'Token'); ?></th>
      <th><?php echo tr('अर्जदार', 'Applicant'); ?></th>
      <th><?php echo tr('दाखला', 'Certificate'); ?></th>
      <th><?php echo tr('कागदपत्रे', 'Documents'); ?></th>
      <th><?php echo tr('स्थिती', 'Status'); ?></th>
      <th><?php echo tr('कारवाई', 'Action'); ?></th>
    </tr></thead>
    <tbody>
      <?php if (!$applications): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--ink-soft);"><?php echo tr('कोणतेही अर्ज सापडले नाहीत.', 'No applications found.'); ?></td></tr>
      <?php endif; ?>
      <?php foreach ($applications as $a): ?>
      <tr>
        <td><b style="color:var(--green-dark);"><?php echo htmlspecialchars($a['application_number']); ?></b><br>
            <span style="font-size:.78rem;color:var(--ink-soft);"><?php echo date('d M Y', strtotime($a['created_at'])); ?></span></td>
        <td><?php echo htmlspecialchars($a['applicant_name']); ?><br>
            <span style="font-size:.78rem;color:var(--ink-soft);">📞 <?php echo htmlspecialchars($a['applicant_mobile']); ?></span></td>
        <td><?php echo tr($a['title_mr'], $a['title_en']); ?></td>
        <td>
          <?php foreach (($docsByApp[$a['id']] ?? []) as $d): ?>
            <a href="view_document.php?id=<?php echo (int)$d['id']; ?>" target="_blank" class="doc-pill has"><?php echo htmlspecialchars($d['doc_label']); ?></a>
          <?php endforeach; ?>
          <?php if (empty($docsByApp[$a['id']])): ?><span style="font-size:.78rem;color:var(--ink-soft);"><?php echo tr('काही नाही', 'None'); ?></span><?php endif; ?>
        </td>
        <td>
          <span class="status-badge status-<?php echo $a['status']; ?>"><?php echo ucfirst($a['status']); ?></span>
          <?php if ($a['certificate_number']): ?><br><span style="font-size:.75rem;color:var(--ink-soft);"><?php echo htmlspecialchars($a['certificate_number']); ?></span><?php endif; ?>
        </td>
        <td>
          <?php if ($a['status'] === 'pending'): ?>
            <form method="post" style="display:inline;">
              <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
              <button type="submit" name="action" value="approve" class="mini-btn approve"><?php echo tr('मंजूर', 'Approve'); ?></button>
            </form>
            <form method="post" style="display:inline;">
              <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
              <button type="submit" name="action" value="reject" class="mini-btn reject" onclick="return confirm('<?php echo tr('नक्की नाकारायचे?', 'Reject this application?'); ?>');"><?php echo tr('नाकारा', 'Reject'); ?></button>
            </form>
          <?php endif; ?>
          <a href="https://wa.me/91<?php echo htmlspecialchars($a['applicant_mobile']); ?>?text=<?php echo urlencode(tr(
              'नमस्कार ' . $a['applicant_name'] . ', आपला अर्ज (' . $a['application_number'] . ') सद्यस्थिती: ' . $a['status'],
              'Hello ' . $a['applicant_name'] . ', your application (' . $a['application_number'] . ') status: ' . $a['status']
          )); ?>" target="_blank" class="mini-btn whatsapp">WhatsApp</a>
          <?php if ($a['status'] === 'approved'): ?>
            <a href="../certificate_view.php?id=<?php echo $a['id']; ?>" target="_blank" class="mini-btn print">🖨️ <?php echo tr('प्रिंट', 'Print'); ?></a>
          <?php endif; ?>
          <a href="application_view.php?id=<?php echo $a['id']; ?>" class="icon-link" style="display:block;margin-top:4px;"><?php echo tr('तपशील →', 'Details →'); ?></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
