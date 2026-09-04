<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
$pageTitle = tr('सरपंच व सदस्य यादी', 'Sarpanch & Members') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';

// Sample fallback data (used only if the database is not connected yet)
$members = [
    ['position_mr' => 'सरपंच',      'position_en' => 'Sarpanch',     'name' => '—', 'ward' => '—'],
    ['position_mr' => 'उपसरपंच',    'position_en' => 'Up-Sarpanch',  'name' => '—', 'ward' => '—'],
    ['position_mr' => 'सदस्य',      'position_en' => 'Member',       'name' => '—', 'ward' => '१ / 1'],
    ['position_mr' => 'सदस्य',      'position_en' => 'Member',       'name' => '—', 'ward' => '२ / 2'],
    ['position_mr' => 'सदस्य',      'position_en' => 'Member',       'name' => '—', 'ward' => '३ / 3'],
    ['position_mr' => 'ग्रामसेवक',  'position_en' => 'Gram Sevak',   'name' => '—', 'ward' => '—'],
];

// If the database is connected, load real data from the `members` table instead
if ($pdo) {
    try {
        $dbMembers = $pdo->query("SELECT position_mr, position_en, name, ward FROM members ORDER BY sort_order ASC")->fetchAll();
        if ($dbMembers) {
            $members = $dbMembers;
        }
    } catch (PDOException $e) {
        // table not created yet - keep using fallback sample data above
    }
}
?>

<section id="members">
  <div class="section-head">
    <span class="eyebrow"><?php echo tr('पदाधिकारी', 'Office Bearers'); ?></span>
    <h2><?php echo tr('सरपंच व सदस्य यादी', 'Sarpanch & Members List'); ?></h2>
  </div>
  <div class="block">
  <table>
    <thead>
      <tr>
        <th><?php echo tr('पद', 'Position'); ?></th>
        <th><?php echo tr('नाव', 'Name'); ?></th>
        <th><?php echo tr('वॉर्ड क्र.', 'Ward No.'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($members as $m): ?>
      <tr>
        <td><?php echo tr($m['position_mr'], $m['position_en']); ?></td>
        <td><?php echo htmlspecialchars($m['name']); ?></td>
        <td><?php echo htmlspecialchars($m['ward']); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="note-box">
    <?php echo tr(
      '📝 ही यादी "members" या डेटाबेस टेबलमधून येते. नावे बदलण्यासाठी phpMyAdmin मध्ये त्या टेबलमध्ये संपादन करा (डेटाबेस जोडलेला नसल्यास नमुना माहिती दिसते).',
      '📝 This list is pulled from the "members" database table. Edit that table in phpMyAdmin to update names (sample data shows if the database isn\'t connected yet).'
    ); ?>
  </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
