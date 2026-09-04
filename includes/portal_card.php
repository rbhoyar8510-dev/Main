<?php
// Shared partial. Expects (optionally) $loginErrors / $loginMobile from login.php.
// Uses $pdo, $lang, tr() from config.php/db.php already included by the parent page.
$loginErrors = $loginErrors ?? [];
$loginMobile = $loginMobile ?? '';
?>
<div class="card portal-tab-card">
  <div class="ptab-row">
    <button type="button" class="ptab active" data-tab="citizen">👤 <?php echo tr('नागरिक लॉगिन', 'Citizen Login'); ?></button>
    <button type="button" class="ptab" data-tab="officer">🏛️ <?php echo tr('अधिकारी लॉगिन', 'Officer Login'); ?></button>
  </div>

  <div id="panel-citizen" class="ptab-panel">
    <div class="psub-row">
      <button type="button" class="psub active" data-sub="login">🔑 <?php echo tr('लॉगिन करा', 'Login'); ?></button>
      <button type="button" class="psub" data-sub="register">📝 <?php echo tr('नवीन खाते बनवा', 'Create New Account'); ?></button>
    </div>

    <div id="sub-login" class="psub-panel">
      <?php if ($loginErrors): ?>
        <div class="note-box" style="border-left-color:var(--maroon);"><?php foreach ($loginErrors as $e) echo '⚠️ ' . htmlspecialchars($e) . '<br>'; ?></div>
      <?php endif; ?>
      <form method="post" action="login.php" class="form-card" style="border:none;padding:0;">
        <input type="text" name="mobile" maxlength="10" placeholder="<?php echo tr('१० अंकी मोबाईल नंबर', '10-digit Mobile Number'); ?>" value="<?php echo htmlspecialchars($loginMobile); ?>" required>
        <input type="password" name="password" placeholder="<?php echo tr('पासवर्ड टाका', 'Enter Password'); ?>" style="margin-top:10px;" required>
        <button type="submit" class="btn solid" style="margin-top:14px;"><?php echo tr('लॉगिन करा', 'Login'); ?></button>
      </form>
    </div>

    <div id="sub-register" class="psub-panel" style="display:none;">
      <form method="post" action="register.php" class="form-card" style="border:none;padding:0;">
        <input type="text" name="full_name" placeholder="<?php echo tr('पूर्ण नाव', 'Full Name'); ?>" required>
        <input type="text" name="mobile" maxlength="10" placeholder="<?php echo tr('१० अंकी मोबाईल नंबर', '10-digit Mobile Number'); ?>" style="margin-top:10px;" required>
        <input type="email" name="email" placeholder="<?php echo tr('ई-मेल (पर्यायी)', 'Email (optional)'); ?>" style="margin-top:10px;">
        <input type="password" name="password" placeholder="<?php echo tr('पासवर्ड टाका', 'Enter Password'); ?>" style="margin-top:10px;" required>
        <input type="password" name="confirm_password" placeholder="<?php echo tr('पासवर्ड पुन्हा टाका', 'Confirm Password'); ?>" style="margin-top:10px;" required>
        <button type="submit" class="btn solid" style="margin-top:14px;"><?php echo tr('नवीन खाते बनवा', 'Create Account'); ?></button>
      </form>
    </div>
  </div>

  <div id="panel-officer" class="ptab-panel" style="display:none;">
    <p style="font-size:.85rem;color:var(--ink-soft);margin-top:0;"><?php echo tr('ग्रामसेवक आणि सरपंच यांच्यासाठी अधिकृत पोर्टल.', 'Official portal for Gram Sevak and Sarpanch.'); ?></p>
    <form method="post" action="admin/login.php" class="form-card" style="border:none;padding:0;">
      <input type="text" name="username" placeholder="<?php echo tr('युजरनेम', 'Username'); ?>" required>
      <input type="password" name="password" placeholder="<?php echo tr('पासवर्ड', 'Password'); ?>" style="margin-top:10px;" required>
      <button type="submit" class="btn solid" style="margin-top:14px;background:var(--gold-dark);border-color:var(--gold-dark);"><?php echo tr('अधिकारी लॉगिन', 'Officer Login'); ?></button>
    </form>
  </div>
</div>

<script>
(function(){
  document.querySelectorAll('.ptab').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.querySelectorAll('.ptab').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      document.getElementById('panel-citizen').style.display = (btn.dataset.tab === 'citizen') ? '' : 'none';
      document.getElementById('panel-officer').style.display = (btn.dataset.tab === 'officer') ? '' : 'none';
    });
  });
  document.querySelectorAll('.psub').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.querySelectorAll('.psub').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      document.getElementById('sub-login').style.display = (btn.dataset.sub === 'login') ? '' : 'none';
      document.getElementById('sub-register').style.display = (btn.dataset.sub === 'register') ? '' : 'none';
    });
  });
})();
</script>
