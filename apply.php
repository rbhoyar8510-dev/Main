<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireUserLogin();

$allCertificates = [];
if ($pdo) {
    $rows = $pdo->query("SELECT id, title_mr, title_en FROM certificates ORDER BY sort_order ASC")->fetchAll();
    $docStmt = $pdo->prepare("SELECT doc_mr, doc_en FROM certificate_docs WHERE certificate_id = ? ORDER BY sort_order ASC");
    foreach ($rows as $r) {
        $docStmt->execute([$r['id']]);
        $allCertificates[$r['id']] = [
            'title' => tr($r['title_mr'], $r['title_en']),
            'docs'  => array_map(fn($d) => tr($d['doc_mr'], $d['doc_en']), $docStmt->fetchAll()),
        ];
    }
}

$preselect = (int) ($_GET['cert_id'] ?? 0);
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $certId = (int) ($_POST['certificate_id'] ?? 0);
    $applicant_name    = trim($_POST['applicant_name'] ?? '');
    $applicant_mobile  = trim($_POST['applicant_mobile'] ?? '');
    $applicant_address = trim($_POST['applicant_address'] ?? '');
    $purpose           = trim($_POST['purpose'] ?? '');

    if (!isset($allCertificates[$certId])) $errors[] = tr('कृपया दाखला निवडा.', 'Please select a certificate.');
    if ($applicant_name === '') $errors[] = tr('नाव आवश्यक आहे.', 'Name is required.');
    if (!preg_match('/^[0-9]{10}$/', $applicant_mobile)) $errors[] = tr('वैध १० अंकी मोबाईल क्रमांक टाका.', 'Enter a valid 10-digit mobile number.');
    if ($applicant_address === '') $errors[] = tr('पत्ता आवश्यक आहे.', 'Address is required.');

    $uploadDir = __DIR__ . '/uploads/documents/';
    $allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];
    $maxSize = 2 * 1024 * 1024;
    $uploadedFiles = [];

    if (!$errors) {
        $docs = $allCertificates[$certId]['docs'];
        foreach ($docs as $i => $label) {
            $field = 'doc_' . $i;
            if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
                $errors[] = $label . ' - ' . tr('फाईल आवश्यक आहे.', 'file is required.');
                continue;
            }
            $file = $_FILES[$field];
            if ($file['error'] !== UPLOAD_ERR_OK) { $errors[] = tr('फाईल अपलोड त्रुटी: ', 'File upload error: ') . $label; continue; }
            if ($file['size'] > $maxSize) { $errors[] = $label . ' - ' . tr('फाईल २ MB पेक्षा मोठी आहे.', 'file is larger than 2MB.'); continue; }
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt, true)) { $errors[] = $label . ' - ' . tr('फक्त PDF/JPG/PNG फाईल्स चालतील.', 'only PDF/JPG/PNG files are allowed.'); continue; }
            $uploadedFiles[] = ['label' => $label, 'tmp' => $file['tmp_name'], 'ext' => $ext];
        }
    }

    if (!$errors && $pdo) {
        try {
            if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
            $appNumber = generateApplicationNumber();
            $stmt = $pdo->prepare("INSERT INTO applications (application_number, user_id, certificate_id, applicant_name, applicant_mobile, applicant_address, purpose, status) VALUES (?,?,?,?,?,?,?, 'pending')");
            $stmt->execute([$appNumber, $_SESSION['user_id'], $certId, $applicant_name, $applicant_mobile, $applicant_address, $purpose]);
            $applicationId = $pdo->lastInsertId();
            $docStmtIns = $pdo->prepare("INSERT INTO application_documents (application_id, doc_label, file_path) VALUES (?,?,?)");
            foreach ($uploadedFiles as $uf) {
                $filename = 'APP' . $applicationId . '_' . bin2hex(random_bytes(4)) . '.' . $uf['ext'];
                move_uploaded_file($uf['tmp'], $uploadDir . $filename);
                $docStmtIns->execute([$applicationId, $uf['label'], 'uploads/documents/' . $filename]);
            }
            $success = true;
            $successNumber = $appNumber;
        } catch (PDOException $e) {
            $errors[] = tr('अर्ज सादर करताना त्रुटी आली.', 'An error occurred while submitting the application.');
        }
    } elseif (!$pdo) {
        $errors[] = tr('डेटाबेस जोडलेला नाही.', 'Database is not connected.');
    }
}

$pageTitle = tr('नवीन दाखल्यासाठी अर्ज करा', 'Apply for New Certificate') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';
?>
<section style="max-width:640px;">
  <div class="card" style="border-top:4px solid var(--green);">
    <h3 style="text-align:center;color:var(--green-dark);"><?php echo tr('नवीन दाखल्यासाठी अर्ज करा', 'Apply for a New Certificate'); ?></h3>

    <?php if ($success): ?>
      <div class="note-box" style="border-left-color:var(--green);">
        ✅ <?php echo tr('आपला अर्ज यशस्वीरित्या सादर झाला आहे.', 'Your application has been submitted successfully.'); ?><br>
        <?php echo tr('अर्ज क्रमांक (टोकन)', 'Application Number (Token)'); ?>:
        <b style="font-size:1.1rem;color:var(--green-dark);"><?php echo htmlspecialchars($successNumber); ?></b><br>
        <?php echo tr('कृपया हा क्रमांक जपून ठेवा.', 'Please save this number.'); ?>
      </div>
      <a href="dashboard.php" class="btn solid"><?php echo tr('माझे अर्ज पहा', 'View My Applications'); ?></a>
    <?php else: ?>

      <?php if ($errors): ?>
        <div class="note-box" style="border-left-color:var(--maroon);"><?php foreach ($errors as $e) echo '⚠️ ' . htmlspecialchars($e) . '<br>'; ?></div>
      <?php endif; ?>

      <?php if (!$allCertificates): ?>
        <div class="note-box" style="border-left-color:var(--maroon);"><?php echo tr('डेटाबेस जोडलेला नाही किंवा कोणतेही दाखले उपलब्ध नाहीत.', 'Database not connected or no certificates available.'); ?></div>
      <?php else: ?>
      <form method="post" enctype="multipart/form-data" class="form-card" id="applyForm">
        <label><?php echo tr('अर्जदाराचे पूर्ण नाव', "Applicant's Full Name"); ?> *</label>
        <input type="text" name="applicant_name" value="<?php echo htmlspecialchars($_POST['applicant_name'] ?? $_SESSION['user_name']); ?>" required>

        <label><?php echo tr('मोबाईल नंबर (WhatsApp)', 'Mobile Number (WhatsApp)'); ?> *</label>
        <input type="text" name="applicant_mobile" maxlength="10" value="<?php echo htmlspecialchars($_POST['applicant_mobile'] ?? ''); ?>" required>

        <label><?php echo tr('संपूर्ण पत्ता', 'Full Address'); ?> *</label>
        <textarea name="applicant_address" rows="2" required><?php echo htmlspecialchars($_POST['applicant_address'] ?? ''); ?></textarea>

        <label><?php echo tr('कारण (पर्यायी)', 'Purpose (optional)'); ?></label>
        <textarea name="purpose" rows="2"><?php echo htmlspecialchars($_POST['purpose'] ?? ''); ?></textarea>

        <label><?php echo tr('दाखला निवडा', 'Select Certificate'); ?> *</label>
        <select name="certificate_id" id="certSelect" required>
          <option value=""><?php echo tr('-- दाखला निवडा --', '-- Select Certificate --'); ?></option>
          <?php foreach ($allCertificates as $id => $c): ?>
            <option value="<?php echo $id; ?>" <?php echo ($preselect === $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['title']); ?></option>
          <?php endforeach; ?>
        </select>

        <div id="docFields" style="margin-top:6px;"></div>

        <button type="submit" class="btn solid" style="margin-top:16px;"><?php echo tr('दाखला अर्ज सबमिट करा', 'Submit Application'); ?></button>
      </form>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<script>
  const certificates = <?php echo json_encode($allCertificates, JSON_UNESCAPED_UNICODE); ?>;
  const docFieldsEl = document.getElementById('docFields');
  const certSelect = document.getElementById('certSelect');

  function renderDocFields() {
    if (!docFieldsEl) return;
    docFieldsEl.innerHTML = '';
    const cert = certificates[certSelect.value];
    if (!cert) return;
    const heading = document.createElement('h4');
    heading.style.cssText = 'margin:16px 0 6px;font-size:1rem;color:var(--green-dark);';
    heading.textContent = <?php echo json_encode(tr('आवश्यक कागदपत्रे अपलोड करा (PDF/JPG/PNG, कमाल २MB)', 'Upload Required Documents (PDF/JPG/PNG, max 2MB)')); ?>;
    docFieldsEl.appendChild(heading);
    cert.docs.forEach((label, i) => {
      const lbl = document.createElement('label');
      lbl.textContent = (i + 1) + '. ' + label + ' *';
      const inp = document.createElement('input');
      inp.type = 'file';
      inp.name = 'doc_' + i;
      inp.accept = '.pdf,.jpg,.jpeg,.png';
      inp.required = true;
      docFieldsEl.appendChild(lbl);
      docFieldsEl.appendChild(inp);
    });
  }
  if (certSelect) {
    certSelect.addEventListener('change', renderDocFields);
    if (certSelect.value) renderDocFields();
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
