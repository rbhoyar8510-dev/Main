<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedInUser()) { header('Location: dashboard.php'); exit; }

$loginErrors = [];
$loginMobile = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mobile'])) {
    $loginMobile = trim($_POST['mobile'] ?? '');
    $password    = $_POST['password'] ?? '';

    if (!$pdo) {
        $loginErrors[] = tr('डेटाबेस जोडलेला नाही.', 'Database is not connected.');
    } else {
        $stmt = $pdo->prepare("SELECT id, full_name, password_hash FROM users WHERE mobile = ?");
        $stmt->execute([$loginMobile]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $loginErrors[] = tr('चुकीचा मोबाईल क्रमांक किंवा पासवर्ड.', 'Incorrect mobile number or password.');
        }
    }
}

$pageTitle = tr('नागरिक लॉगिन', 'Citizen Login') . ' | ' . SITE_NAME_EN;
require_once __DIR__ . '/includes/header.php';
?>
<section>
  <?php require __DIR__ . '/includes/portal_card.php'; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
