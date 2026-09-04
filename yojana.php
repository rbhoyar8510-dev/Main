<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
$pageTitle = tr('शासकीय योजना', 'Government Schemes') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';

// Sample fallback data (used only if the database is not connected yet)
$schemes = [
    [
        'title_mr' => 'प्रधानमंत्री आवास योजना (ग्रामीण)', 'title_en' => 'PM Awas Yojana (Gramin)',
        'desc_mr' => 'बेघर व कच्च्या घरातील कुटुंबांना पक्के घर बांधण्यासाठी अनुदान.',
        'desc_en' => 'Housing assistance for homeless families and those living in kutcha houses.'
    ],
    [
        'title_mr' => 'जल जीवन मिशन', 'title_en' => 'Jal Jeevan Mission',
        'desc_mr' => 'प्रत्येक घरापर्यंत नळाद्वारे शुद्ध पिण्याचे पाणी पोहोचवणे.',
        'desc_en' => 'Providing piped drinking water connection to every household.'
    ],
    [
        'title_mr' => 'स्वच्छ भारत मिशन (ग्रामीण)', 'title_en' => 'Swachh Bharat Mission (Gramin)',
        'desc_mr' => 'वैयक्तिक व सार्वजनिक शौचालय बांधकाम व स्वच्छता जनजागृती.',
        'desc_en' => 'Construction of individual/public toilets and sanitation awareness.'
    ],
    [
        'title_mr' => 'महात्मा गांधी राष्ट्रीय ग्रामीण रोजगार हमी योजना (मनरेगा)', 'title_en' => 'MGNREGA',
        'desc_mr' => 'ग्रामीण कुटुंबांना १०० दिवसांच्या रोजगाराची हमी.',
        'desc_en' => 'Guarantees 100 days of wage employment to rural households.'
    ],
    [
        'title_mr' => 'प्रधानमंत्री मातृ वंदना योजना', 'title_en' => 'PM Matru Vandana Yojana',
        'desc_mr' => 'गरोदर व स्तनदा मातांना आर्थिक सहाय्य.',
        'desc_en' => 'Financial assistance for pregnant and lactating mothers.'
    ],
    [
        'title_mr' => 'संजय गांधी निराधार अनुदान योजना', 'title_en' => 'Sanjay Gandhi Niradhar Yojana',
        'desc_mr' => 'निराधार, वृद्ध व विधवा व्यक्तींना मासिक अनुदान.',
        'desc_en' => 'Monthly pension for destitute, elderly and widowed persons.'
    ],
];

// If the database is connected, load real data from the `schemes` table instead
if ($pdo) {
    try {
        $dbSchemes = $pdo->query("SELECT title_mr, title_en, desc_mr, desc_en FROM schemes ORDER BY sort_order ASC")->fetchAll();
        if ($dbSchemes) {
            $schemes = $dbSchemes;
        }
    } catch (PDOException $e) {
        // table not created yet - keep using fallback sample data above
    }
}
?>

<section id="yojana">
  <div class="section-head">
    <span class="eyebrow"><?php echo tr('कल्याणकारी योजना', 'Welfare Schemes'); ?></span>
    <h2><?php echo tr('शासकीय योजना', 'Government Schemes'); ?></h2>
    <p><?php echo tr('गावात राबवल्या जाणाऱ्या प्रमुख शासकीय योजना. अधिक माहितीसाठी ग्रामपंचायत कार्यालयाशी संपर्क साधा.', 'Key government schemes implemented in the village. Contact the Gram Panchayat office for more details.'); ?></p>
  </div>
  <div class="grid">
    <?php foreach ($schemes as $s): ?>
    <div class="card">
      <h3><?php echo tr($s['title_mr'], $s['title_en']); ?></h3>
      <p><?php echo tr($s['desc_mr'], $s['desc_en']); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
