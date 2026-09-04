<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$id      = (int) ($_POST['id'] ?? 0);
$action  = $_POST['action'] ?? '';
$remarks = trim($_POST['remarks'] ?? '');

if ($pdo && $id && in_array($action, ['approve', 'reject'], true)) {
    if ($action === 'approve') {
        $certNumber = generateCertificateNumber();
        $stmt = $pdo->prepare("UPDATE applications SET status='approved', admin_remarks=?, certificate_number=? WHERE id=? AND status='pending'");
        $stmt->execute([$remarks, $certNumber, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE applications SET status='rejected', admin_remarks=? WHERE id=? AND status='pending'");
        $stmt->execute([$remarks, $id]);
    }
}

header('Location: application_view.php?id=' . $id . '&done=1');
exit;
