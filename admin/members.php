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
        $data = [
            trim($_POST['position_mr']), trim($_POST['position_en']),
            trim($_POST['name']), trim($_POST['ward']), (int) $_POST['sort_order'],
        ];
        if ($id) {
            $pdo->prepare("UPDATE members SET position_mr=?, position_en=?, name=?, ward=?, sort_order=? WHERE id=?")
                ->execute([...$data, $id]);
        } else {
            $pdo->prepare("INSERT INTO members (position_mr, position_en, name, ward, sort_order) VALUES (?,?,?,?,?)")
                ->execute($data);
        }
    } elseif ($act === 'delete') {
        $pdo->prepare("DELETE FROM members WHERE id=?")->execute([(int) $_POST['id']]);
    }
    header('Location: members.php');
    exit;
}

if ($pdo && isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id=?");
    $stmt->execute([(int) $_GET['edit']]);
    $editRow = $stmt->fetch();
}

$rows = $pdo ? $pdo->query("SELECT * FROM members ORDER BY sort_order ASC")->fetchAll() : [];

$pageTitle = tr('सरपंच व सदस्य व्यवस्थापन', 'Manage Members');
require_once __DIR__ . '/includes/layout_head.php';
?>

<div class="admin-table-wrap" style="margin-bottom:24px;">
  <table>
    <thead><tr><th><?php echo tr('क्रम', 'Order'); ?></th><th><?php echo tr('पद', 'Position'); ?></th><th><?php echo tr('नाव', 'Name'); ?></th><th><?php echo tr('वॉर्ड', 'Ward'); ?></th><th></th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td><?php echo $r['sort_order']; ?></td>
        <td><?php echo htmlspecialchars($r['position_mr']) . ' / ' . htmlspecialchars($r['position_en']); ?></td>
        <td><?php echo htmlspecialchars($r['name']); ?></td>
        <td><?php echo htmlspecialchars($r['ward']); ?></td>
        <td>
          <a class="icon-link" href="?edit=<?php echo $r['id']; ?>"><?php echo tr('संपादित करा', 'Edit'); ?></a> |
          <form method="post" style="display:inline;" onsubmit="return confirm('<?php echo tr('नक्की काढायचे?', 'Delete this record?'); ?>');">
            <input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?php echo $r['id']; ?>">
            <button type="submit" class="icon-link" style="background:none;border:none;color:var(--maroon);cursor:pointer;"><?php echo tr('काढा', 'Delete'); ?></button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="5" style="text-align:center;color:var(--ink-soft);"><?php echo tr('कोणतीही नोंद नाही.', 'No records.'); ?></td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<div class="card" style="max-width:560px;">
  <h3><?php echo $editRow ? tr('सदस्य संपादित करा', 'Edit Member') : tr('नवीन सदस्य जोडा', 'Add New Member'); ?></h3>
  <form method="post" class="form-card">
    <input type="hidden" name="act" value="save">
    <input type="hidden" name="id" value="<?php echo $editRow['id'] ?? ''; ?>">
    <label><?php echo tr('पद (मराठी)', 'Position (Marathi)'); ?></label>
    <input type="text" name="position_mr" value="<?php echo htmlspecialchars($editRow['position_mr'] ?? ''); ?>" required>
    <label><?php echo tr('पद (इंग्रजी)', 'Position (English)'); ?></label>
    <input type="text" name="position_en" value="<?php echo htmlspecialchars($editRow['position_en'] ?? ''); ?>" required>
    <label><?php echo tr('नाव', 'Name'); ?></label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($editRow['name'] ?? ''); ?>" required>
    <label><?php echo tr('वॉर्ड क्र.', 'Ward No.'); ?></label>
    <input type="text" name="ward" value="<?php echo htmlspecialchars($editRow['ward'] ?? '—'); ?>">
    <label><?php echo tr('क्रम (यादीतील स्थान)', 'Sort Order'); ?></label>
    <input type="number" name="sort_order" value="<?php echo htmlspecialchars($editRow['sort_order'] ?? count($rows) + 1); ?>">
    <div class="action-btns">
      <button type="submit" class="btn solid"><?php echo tr('जतन करा', 'Save'); ?></button>
      <?php if ($editRow): ?><a href="members.php" class="btn"><?php echo tr('रद्द करा', 'Cancel'); ?></a><?php endif; ?>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
