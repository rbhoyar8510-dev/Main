<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
$pageTitle = tr('ग्रामपंचायत दाखले', 'Gram Panchayat Certificates') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';

// Sample fallback data (used only if the database is not connected yet)
$certificates = [
    [
        'title_mr' => 'रहिवासी (निवास) दाखला', 'title_en' => 'Residence Certificate',
        'docs' => [
            ['आधार कार्ड प्रत', 'Aadhar card copy'],
            ['रेशन कार्ड प्रत', 'Ration card copy'],
            ['विहित नमुन्यातील अर्ज', 'Application in prescribed format'],
        ],
    ],
    [
        'title_mr' => 'उत्पन्न दाखला', 'title_en' => 'Income Certificate',
        'docs' => [
            ['आधार कार्ड प्रत', 'Aadhar card copy'],
            ['उत्पन्नाचा स्वयंघोषणापत्र', 'Self-declaration of income'],
            ['रहिवासी पुरावा', 'Proof of residence'],
        ],
    ],
    [
        'title_mr' => 'कुटुंब (फॅमिली) दाखला', 'title_en' => 'Family Certificate',
        'docs' => [
            ['रेशन कार्ड प्रत', 'Ration card copy'],
            ['सर्व सदस्यांचे आधार कार्ड', 'Aadhar of all family members'],
        ],
    ],
    [
        'title_mr' => 'वारस दाखला', 'title_en' => 'Heir Certificate',
        'docs' => [
            ['मृत्यू दाखला प्रत', 'Death certificate copy'],
            ['वारसांचे आधार कार्ड', 'Aadhar cards of heirs'],
            ['प्रतिज्ञापत्र', 'Affidavit'],
        ],
    ],
    [
        'title_mr' => 'जन्म दाखला', 'title_en' => 'Birth Certificate',
        'docs' => [
            ['रुग्णालय जन्म नोंद', 'Hospital birth record'],
            ['पालकांचे आधार कार्ड', "Parents' Aadhar cards"],
        ],
    ],
    [
        'title_mr' => 'मृत्यू दाखला', 'title_en' => 'Death Certificate',
        'docs' => [
            ['रुग्णालय / वैद्यकीय मृत्यू नोंद', 'Hospital/medical death record'],
            ['मृताचे आधार कार्ड', "Deceased's Aadhar card"],
        ],
    ],
    [
        'title_mr' => 'विवाह दाखला (नोंद असल्यास)', 'title_en' => 'Marriage Certificate (if registered)',
        'docs' => [
            ['विवाह नोंदणी पुरावा', 'Marriage registration proof'],
            ['दोन्ही पक्षांचे आधार कार्ड', 'Aadhar cards of both parties'],
        ],
    ],
    [
        'title_mr' => 'अविवाहित दाखला', 'title_en' => 'Unmarried Certificate',
        'docs' => [
            ['आधार कार्ड प्रत', 'Aadhar card copy'],
            ['स्वयंघोषणापत्र / प्रतिज्ञापत्र', 'Self-declaration/affidavit'],
        ],
    ],
    [
        'title_mr' => 'विधवा दाखला', 'title_en' => 'Widow Certificate',
        'docs' => [
            ['पतीचा मृत्यू दाखला', "Husband's death certificate"],
            ['आधार कार्ड प्रत', 'Aadhar card copy'],
        ],
    ],
    [
        'title_mr' => 'निराधार दाखला', 'title_en' => 'Destitute (Nirashrit) Certificate',
        'docs' => [
            ['उत्पन्नाचा दाखला', 'Income certificate'],
            ['स्वयंघोषणापत्र', 'Self-declaration'],
        ],
    ],
    [
        'title_mr' => 'घरपट्टी / मालमत्ता दाखला', 'title_en' => 'House Tax / Property Certificate',
        'docs' => [
            ['घरपट्टी पावती', 'House tax receipt'],
            ['मालमत्ता नोंद उतारा', 'Property register extract'],
        ],
    ],
    [
        'title_mr' => 'घर क्रमांक दाखला', 'title_en' => 'House Number Certificate',
        'docs' => [
            ['घरपट्टी पावती', 'House tax receipt'],
            ['आधार कार्ड प्रत', 'Aadhar card copy'],
        ],
    ],
    [
        'title_mr' => 'बांधकाम / एनओसी दाखला (लागू असल्यास)', 'title_en' => 'Construction / NOC Certificate (if applicable)',
        'docs' => [
            ['जागेचा ७/१२ किंवा मालमत्ता उतारा', '7/12 extract or property record'],
            ['बांधकाम आराखडा', 'Construction plan'],
        ],
    ],
    [
        'title_mr' => 'पाणी जोडणी दाखला', 'title_en' => 'Water Connection Certificate',
        'docs' => [
            ['घरपट्टी पावती', 'House tax receipt'],
            ['आधार कार्ड प्रत', 'Aadhar card copy'],
        ],
    ],
    [
        'title_mr' => 'रहिवास व चारित्र्य शिफारस', 'title_en' => 'Residence & Character Recommendation',
        'docs' => [
            ['आधार कार्ड प्रत', 'Aadhar card copy'],
            ['पोलीस पडताळणी (आवश्यक असल्यास)', 'Police verification (if required)'],
        ],
    ],
];

// If the database is connected, load real data from `certificates` + `certificate_docs` tables instead
if ($pdo) {
    try {
        $certRows = $pdo->query("SELECT id, title_mr, title_en FROM certificates ORDER BY sort_order ASC")->fetchAll();
        if ($certRows) {
            $docStmt = $pdo->prepare("SELECT doc_mr, doc_en FROM certificate_docs WHERE certificate_id = ? ORDER BY sort_order ASC");
            $dbCertificates = [];
            foreach ($certRows as $row) {
                $docStmt->execute([$row['id']]);
                $docs = [];
                foreach ($docStmt->fetchAll() as $d) {
                    $docs[] = [$d['doc_mr'], $d['doc_en']];
                }
                $dbCertificates[] = [
                    'id'       => $row['id'],
                    'title_mr' => $row['title_mr'],
                    'title_en' => $row['title_en'],
                    'docs'     => $docs,
                ];
            }
            $certificates = $dbCertificates;
        }
    } catch (PDOException $e) {
        // tables not created yet - keep using fallback sample data above
    }
}
?>

<section id="dakhle">
  <div class="section-head">
    <span class="eyebrow"><?php echo tr('नागरी सेवा', 'Citizen Services'); ?></span>
    <h2><?php echo tr('ग्रामपंचायत दाखले', 'Gram Panchayat Certificates'); ?></h2>
    <p><?php echo tr(
      'खालील यादीतून आवश्यक दाखला शोधा. प्रत्येक दाखल्यासाठी लागणारी कागदपत्रे नमुना स्वरूपात दिली आहेत.',
      'Search for the certificate you need below. Required documents shown are indicative — please confirm at the office.'
    ); ?></p>
  </div>

  <div class="search-box">
    <span>🔍</span>
    <input id="certSearch" type="text" placeholder="<?php echo tr('दाखला शोधा...', 'Search certificate...'); ?>" oninput="filterCerts()">
  </div>

  <div class="grid" id="certGrid">
    <?php foreach ($certificates as $i => $c): ?>
    <div class="cert-card">
      <span class="num"><?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?></span>
      <h3><?php echo tr($c['title_mr'], $c['title_en']); ?></h3>
      <ul class="req">
        <?php foreach ($c['docs'] as $d): ?>
          <li><?php echo tr($d[0], $d[1]); ?></li>
        <?php endforeach; ?>
      </ul>
      <?php $cid = $c['id'] ?? ($i + 1); ?>
      <a href="<?php echo isset($_SESSION['user_id']) ? 'apply.php?cert_id=' . $cid : 'login.php'; ?>" class="btn" style="margin-top:12px;font-size:.8rem;padding:7px 14px;">
        <?php echo tr('अर्ज करा', 'Apply Now'); ?>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
  <p class="cert-empty" id="certEmpty"><?php echo tr('कोणताही दाखला सापडला नाही.', 'No certificate found.'); ?></p>

  <div class="note-box" style="margin-top:22px;">
    <?php echo tr(
      '📝 वरील कागदपत्रांची यादी सर्वसाधारण नमुना आहे. अचूक व अद्ययावत यादीसाठी ग्रामपंचायत कार्यालयाशी संपर्क साधा.',
      '📝 The document lists above are a general guide. Please confirm the exact, up-to-date requirements at the Gram Panchayat office.'
    ); ?>
  </div>
</section>

<script>
  function filterCerts(){
    var q = document.getElementById('certSearch').value.trim().toLowerCase();
    var cards = document.querySelectorAll('#certGrid .cert-card');
    var visibleCount = 0;
    cards.forEach(function(card){
      var match = card.textContent.toLowerCase().indexOf(q) !== -1;
      card.style.display = match ? '' : 'none';
      if (match) visibleCount++;
    });
    document.getElementById('certEmpty').style.display = visibleCount === 0 ? 'block' : 'none';
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
