<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$editRow = null;

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['act'] ?? '';
    if ($act === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $data = [trim($_POST['notice_date']), trim($_POST['title_mr']), trim($_POST['title_en']), trim($_POST['desc_mr']), trim($_POST['desc_en']), (int) $_POST['sort_order']];
        if ($id) {
            $pdo->prepare("UPDATE notices SET notice_date=?, title_mr=?, title_en=?, desc_mr=?, desc_en=?, sort_order=? WHERE id=?")->execute([...$data, $id]);
        } else {
            $pdo->prepare("INSERT INTO notices (notice_date, title_mr, title_en, desc_mr, desc_en, sort_order) VALUES (?,?,?,?,?,?)")->execute($data);
        }
    } elseif ($act === 'delete') {
        $pdo->prepare("DELETE FROM notices WHERE id=?")->execute([(int) $_POST['id']]);
    }
    header('Location: notices.php');
    exit;
}

if ($pdo && isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM notices WHERE id=?");
    $stmt->execute([(int) $_GET['edit']]);
    $editRow = $stmt->fetch();
}

$rows = $pdo ? $pdo->query("SELECT * FROM notices ORDER BY sort_order DESC")->fetchAll() : [];
$pageTitle = tr('सूचना व्यवस्थापन', 'Manage Notices');
require_once __DIR__ . '/includes/layout_head.php';
?>

<div class="admin-table-wrap" style="margin-bottom:24px;">
  <table>
    <thead><tr><th><?php echo tr('दिनांक', 'Date'); ?></th><th><?php echo tr('सूचना', 'Notice'); ?></th><th></th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td><?php echo htmlspecialchars($r['notice_date']); ?></td>
        <td><b><?php echo htmlspecialchars($r['title_mr']); ?></b><br><span style="font-size:.82rem;color:var(--ink-soft);"><?php echo htmlspecialchars($r['title_en']); ?></span></td>
        <td>
          <a class="icon-link" href="?edit=<?php echo $r['id']; ?>"><?php echo tr('संपादित करा', 'Edit'); ?></a> |
          <form method="post" style="display:inline;" onsubmit="return confirm('<?php echo tr('नक्की काढायचे?', 'Delete this record?'); ?>');">
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

<div class="card" style="max-width:600px;">
  <h3><?php echo $editRow ? tr('सूचना संपादित करा', 'Edit Notice') : tr('नवीन सूचना जोडा', 'Add New Notice'); ?></h3>
  <form method="post" class="form-card">
    <input type="hidden" name="act" value="save">
    <input type="hidden" name="id" value="<?php echo $editRow['id'] ?? ''; ?>">
    <label><?php echo tr('दिनांक (उदा. १२ सप्टें २०२६)', 'Date (e.g. 12 Sep 2026)'); ?></label>
    <input type="text" name="notice_date" value="<?php echo htmlspecialchars($editRow['notice_date'] ?? date('Y')); ?>" required>
    <label><?php echo tr('शीर्षक (मराठी)', 'Title (Marathi)'); ?></label>
    <input type="text" name="title_mr" value="<?php echo htmlspecialchars($editRow['title_mr'] ?? ''); ?>" required>
    <label><?php echo tr('शीर्षक (इंग्रजी)', 'Title (English)'); ?></label>
    <input type="text" name="title_en" value="<?php echo htmlspecialchars($editRow['title_en'] ?? ''); ?>" required>
    <label><?php echo tr('तपशील (मराठी)', 'Description (Marathi)'); ?></label>
    <textarea name="desc_mr" rows="2" required><?php echo htmlspecialchars($editRow['desc_mr'] ?? ''); ?></textarea>
    <label><?php echo tr('तपशील (इंग्रजी)', 'Description (English)'); ?></label>
    <textarea name="desc_en" rows="2" required><?php echo htmlspecialchars($editRow['desc_en'] ?? ''); ?></textarea>
    <label><?php echo tr('क्रम (मोठा क्रम आधी दिसतो)', 'Sort Order (higher shows first)'); ?></label>
    <input type="number" name="sort_order" value="<?php echo htmlspecialchars($editRow['sort_order'] ?? count($rows) + 1); ?>">
    <div class="action-btns">
      <button type="submit" class="btn solid"><?php echo tr('जतन करा', 'Save'); ?></button>
      <?php if ($editRow): ?><a href="notices.php" class="btn"><?php echo tr('रद्द करा', 'Cancel'); ?></a><?php endif; ?>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
