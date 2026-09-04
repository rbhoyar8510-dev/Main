<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
$pageTitle = tr('संपर्क', 'Contact') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';
?>

<section id="contact">
  <div class="section-head">
    <span class="eyebrow"><?php echo tr('आमच्याशी संपर्क साधा', 'Get in Touch'); ?></span>
    <h2><?php echo tr('संपर्क', 'Contact'); ?></h2>
  </div>
  <div class="contact-grid">
    <div class="block">
      <div class="contact-item">
        <div class="ic">📍</div>
        <div>
          <h4><?php echo tr('कार्यालयाचा पत्ता', 'Office Address'); ?></h4>
          <p><?php echo tr('ग्रामपंचायत कार्यालय, ', 'Gram Panchayat Office, ') . setting('address', SITE_ADDRESS_MR, SITE_ADDRESS_EN); ?></p>
        </div>
      </div>
      <div class="contact-item">
        <div class="ic">📞</div>
        <div>
          <h4><?php echo tr('दूरध्वनी', 'Phone'); ?></h4>
          <p><?php echo setting('phone', SITE_PHONE, SITE_PHONE); ?></p>
        </div>
      </div>
      <div class="contact-item">
        <div class="ic">✉️</div>
        <div>
          <h4><?php echo tr('ई-मेल', 'Email'); ?></h4>
          <p><?php echo setting('email', SITE_EMAIL, SITE_EMAIL); ?></p>
        </div>
      </div>
      <div class="contact-item">
        <div class="ic">🕘</div>
        <div>
          <h4><?php echo tr('कार्यालयीन वेळ', 'Office Hours'); ?></h4>
          <p><?php echo setting('office_hours', 'सोमवार ते शनिवार, स. १०:०० ते सा. ५:००', 'Monday to Saturday, 10:00 AM – 5:00 PM'); ?></p>
        </div>
      </div>
    </div>
    <div class="block" style="padding:6px;">
      <div class="map-box" style="margin:0;">
        <iframe src="https://www.google.com/maps?q=Tirri,+Pawani,+Bhandara,+Maharashtra&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
