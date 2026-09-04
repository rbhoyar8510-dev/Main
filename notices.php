<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
$pageTitle = tr('सूचना व परिपत्रके', 'Notices & Circulars') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';

// Sample fallback data (used only if the database is not connected yet)
$notices = [
    [
        'date' => '2026',
        'title_mr' => 'ग्रामसभेची सूचना', 'title_en' => 'Gram Sabha Notice',
        'desc_mr' => 'आगामी ग्रामसभेची तारीख व वेळ लवकरच जाहीर केली जाईल.',
        'desc_en' => 'Date and time of the upcoming Gram Sabha will be announced soon.'
    ],
    [
        'date' => '2026',
        'title_mr' => 'घरपट्टी भरणा सूचना', 'title_en' => 'House Tax Payment Notice',
        'desc_mr' => 'चालू आर्थिक वर्षाची घरपट्टी वेळेत भरण्याचे आवाहन.',
        'desc_en' => 'Residents are requested to pay house tax for the current financial year on time.'
    ],
];

// If the database is connected, load real data from the `notices` table instead
if ($pdo) {
    try {
        $dbNotices = $pdo->query("SELECT notice_date AS date, title_mr, title_en, desc_mr, desc_en FROM notices ORDER BY sort_order DESC")->fetchAll();
        if ($dbNotices) {
            $notices = $dbNotices;
        }
    } catch (PDOException $e) {
        // table not created yet - keep using fallback sample data above
    }
}
?>

<section id="notices">
  <div class="section-head">
    <span class="eyebrow"><?php echo tr('ताज्या घडामोडी', 'Latest Updates'); ?></span>
    <h2><?php echo tr('सूचना व परिपत्रके', 'Notices & Circulars'); ?></h2>
  </div>

  <?php foreach ($notices as $n): ?>
  <div class="block notice-block">
    <div class="notice">
      <div class="date"><b><?php echo htmlspecialchars($n['date']); ?></b></div>
      <div class="content">
        <h3><?php echo tr($n['title_mr'], $n['title_en']); ?></h3>
        <p><?php echo tr($n['desc_mr'], $n['desc_en']); ?></p>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <div class="note-box">
    <?php echo tr(
      '📝 ही नमुना सूचना आहे. वास्तविक सूचना/परिपत्रके notices.php फाईलमधील $notices अ‍ॅरेमध्ये अद्ययावत करा.',
      '📝 These are sample notices — update the real ones in the $notices array inside notices.php.'
    ); ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
