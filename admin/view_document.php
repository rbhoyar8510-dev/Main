<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$id = (int) ($_GET['id'] ?? 0);
if (!$pdo || !$id) { http_response_code(404); exit('Not found'); }

$stmt = $pdo->prepare("SELECT file_path, doc_label FROM application_documents WHERE id = ?");
$stmt->execute([$id]);
$doc = $stmt->fetch();
if (!$doc) { http_response_code(404); exit('Not found'); }

$fullPath = __DIR__ . '/../' . $doc['file_path'];
if (!is_file($fullPath)) { http_response_code(404); exit('File missing'); }

$ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
$mime = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'][$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="' . basename($doc['file_path']) . '"');
readfile($fullPath);
exit;
