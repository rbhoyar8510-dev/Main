<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
$pageTitle = tr('ग्रामपंचायती विषयी', 'About') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';
?>

<section id="about">
  <div class="section-head">
    <span class="eyebrow"><?php echo tr('परिचय', 'Introduction'); ?></span>
    <h2><?php echo tr('ग्रामपंचायती विषयी', 'About the Gram Panchayat'); ?></h2>
  </div>
  <div class="block">
  <div class="about-wrap">
    <div class="col">
      <p>
        <?php echo tr(
          'ग्रामपंचायत तिर्री ही महाराष्ट्रातील भंडारा जिल्ह्यातील पवनी तालुक्यात, मिन्शी पोस्ट अंतर्गत येणारी ग्रामपंचायत आहे. गावातील नागरिकांना प्रशासकीय सेवा, विविध दाखले, शासकीय योजनांची माहिती व सूचना वेळेवर उपलब्ध व्हाव्यात या उद्देशाने ही अधिकृत संकेतस्थळ तयार करण्यात आले आहे.',
          'Gram Panchayat Tirri is a village council located under Post Minshi, Taluka Pawani, in Bhandara district of Maharashtra. This official website has been created so that residents can access administrative services, certificates, government scheme information and notices in one convenient place.'
        ); ?>
      </p>
      <p>
        <?php echo tr(
          'ग्रामपंचायत कार्यालयामार्फत स्वच्छता, पाणीपुरवठा, रस्ते, सार्वजनिक आरोग्य, शिक्षण सहाय्य तसेच विविध कल्याणकारी योजनांची अंमलबजावणी केली जाते.',
          'The Gram Panchayat office oversees sanitation, water supply, roads, public health, education support, and the implementation of various welfare schemes for the village.'
        ); ?>
      </p>
    </div>
    <div class="col">
      <div class="card">
        <h3><?php echo tr('पत्ता', 'Address'); ?></h3>
        <p><?php echo setting('address', SITE_ADDRESS_MR, SITE_ADDRESS_EN); ?></p>
      </div>
      <div class="stat-row">
        <div class="stat"><b><?php echo setting('population', '—', '—'); ?></b><span><?php echo tr('लोकसंख्या', 'Population'); ?></span></div>
        <div class="stat"><b><?php echo setting('households', '—', '—'); ?></b><span><?php echo tr('कुटुंब संख्या', 'Households'); ?></span></div>
        <div class="stat"><b><?php echo setting('wards', '—', '—'); ?></b><span><?php echo tr('वॉर्ड संख्या', 'Wards'); ?></span></div>
      </div>
      <p style="font-size:.8rem;color:var(--ink-soft);margin-top:10px;">
        <?php echo tr('* अचूक आकडेवारीसाठी ग्रामपंचायत कार्यालयाशी संपर्क साधा.', '* Contact the Gram Panchayat office for exact figures.'); ?>
      </p>
    </div>
  </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
