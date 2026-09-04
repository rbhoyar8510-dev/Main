<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$editRow = null;
$editDocsText = '';

function parseDocsText($text) {
    $out = [];
    foreach (preg_split('/\r?\n/', trim($text)) as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $parts = array_map('trim', explode('|', $line));
        if (count($parts) >= 2) $out[] = [$parts[0], $parts[1]];
    }
    return $out;
}

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['act'] ?? '';
    if ($act === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $title_mr = trim($_POST['title_mr']);
        $title_en = trim($_POST['title_en']);
        $sort_order = (int) $_POST['sort_order'];
        $docs = parseDocsText($_POST['docs_text'] ?? '');

        if ($id) {
            $pdo->prepare("UPDATE certificates SET title_mr=?, title_en=?, sort_order=? WHERE id=?")->execute([$title_mr, $title_en, $sort_order, $id]);
            $pdo->prepare("DELETE FROM certificate_docs WHERE certificate_id=?")->execute([$id]);
        } else {
            $pdo->prepare("INSERT INTO certificates (title_mr, title_en, sort_order) VALUES (?,?,?)")->execute([$title_mr, $title_en, $sort_order]);
            $id = $pdo->lastInsertId();
        }
        $docStmt = $pdo->prepare("INSERT INTO certificate_docs (certificate_id, doc_mr, doc_en, sort_order) VALUES (?,?,?,?)");
        foreach ($docs as $i => $d) { $docStmt->execute([$id, $d[0], $d[1], $i + 1]); }
    } elseif ($act === 'delete') {
        try {
            $pdo->prepare("DELETE FROM certificates WHERE id=?")->execute([(int) $_POST['id']]);
        } catch (PDOException $e) {
            header('Location: certificates.php?err=inuse');
            exit;
        }
    }
    header('Location: certificates.php');
    exit;
}

if ($pdo && isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE id=?");
    $stmt->execute([(int) $_GET['edit']]);
    $editRow = $stmt->fetch();
    if ($editRow) {
        $docStmt = $pdo->prepare("SELECT doc_mr, doc_en FROM certificate_docs WHERE certificate_id=? ORDER BY sort_order ASC");
        $docStmt->execute([$editRow['id']]);
        $lines = [];
        foreach ($docStmt->fetchAll() as $d) { $lines[] = $d['doc_mr'] . ' | ' . $d['doc_en']; }
        $editDocsText = implode("\n", $lines);
    }
}

$rows = $pdo ? $pdo->query("SELECT * FROM certificates ORDER BY sort_order ASC")->fetchAll() : [];
$pageTitle = tr('दाखले व्यवस्थापन', 'Manage Certificates');
require_once __DIR__ . '/includes/layout_head.php';
?>

<?php if (isset($_GET['err']) && $_GET['err'] === 'inuse'): ?>
  <div class="note-box" style="border-left-color:var(--maroon);"><?php echo tr('हा दाखला काढता येत नाही कारण त्यासाठी अगोदरच अर्ज सादर झाले आहेत.', 'This certificate cannot be deleted because applications already exist for it.'); ?></div>
<?php endif; ?>

<div class="admin-table-wrap" style="margin-bottom:24px;">
  <table>
    <thead><tr><th><?php echo tr('क्रम', 'Order'); ?></th><th><?php echo tr('दाखला', 'Certificate'); ?></th><th></th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td><?php echo $r['sort_order']; ?></td>
        <td><b><?php echo htmlspecialchars($r['title_mr']); ?></b><br><span style="font-size:.82rem;color:var(--ink-soft);"><?php echo htmlspecialchars($r['title_en']); ?></span></td>
        <td>
          <a class="icon-link" href="?edit=<?php echo $r['id']; ?>"><?php echo tr('संपादित करा', 'Edit'); ?></a> |
          <form method="post" style="display:inline;" onsubmit="return confirm('<?php echo tr('नक्की काढायचे? यासोबतचे सर्व अर्जही प्रभावित होतील.', 'Delete this certificate? Related applications will be affected.'); ?>');">
            <input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?php echo $r['id']; ?>">
            <button type="submit" class="icon-link" style="background:none;border:none;color:var(--maroon);cursor:pointer;"><?php echo tr('काढा', 'Delete'); ?></button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="3" style="text-align:center;color:var(--ink-soft);"><?php echo tr('कोणतीही नोंद नाही.', 'No records.'); ?></td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<div class="card" style="max-width:640px;">
  <h3><?php echo $editRow ? tr('दाखला संपादित करा', 'Edit Certificate') : tr('नवीन दाखला जोडा', 'Add New Certificate'); ?></h3>
  <form method="post" class="form-card">
    <input type="hidden" name="act" value="save">
    <input type="hidden" name="id" value="<?php echo $editRow['id'] ?? ''; ?>">
    <label><?php echo tr('दाखल्याचे नाव (मराठी)', 'Certificate Name (Marathi)'); ?></label>
    <input type="text" name="title_mr" value="<?php echo htmlspecialchars($editRow['title_mr'] ?? ''); ?>" required>
    <label><?php echo tr('दाखल्याचे नाव (इंग्रजी)', 'Certificate Name (English)'); ?></label>
    <input type="text" name="title_en" value="<?php echo htmlspecialchars($editRow['title_en'] ?? ''); ?>" required>
    <label><?php echo tr('क्रम', 'Sort Order'); ?></label>
    <input type="number" name="sort_order" value="<?php echo htmlspecialchars($editRow['sort_order'] ?? count($rows) + 1); ?>">
    <label><?php echo tr('आवश्यक कागदपत्रे (प्रत्येक ओळीत: मराठी | इंग्रजी)', 'Required Documents (one per line: Marathi | English)'); ?></label>
    <textarea name="docs_text" rows="6" placeholder="आधार कार्ड प्रत | Aadhar card copy"><?php echo htmlspecialchars($editDocsText); ?></textarea>
    <div class="action-btns">
      <button type="submit" class="btn solid"><?php echo tr('जतन करा', 'Save'); ?></button>
      <?php if ($editRow): ?><a href="certificates.php" class="btn"><?php echo tr('रद्द करा', 'Cancel'); ?></a><?php endif; ?>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
