<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../src/logger.php';
require_once __DIR__ . '/../src/repositories/InvestorSignupRepo.php';
require_once __DIR__ . '/../src/services/InvestorSignupService.php';

use App\Logger;

$logger = new Logger(__DIR__ . '/../logs/app.log');

$pdo = require __DIR__ . '/../config/db.php';
if (!($pdo instanceof PDO) && isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
    $pdo = $GLOBALS['pdo'];
}

$fields = [
    'name'             => ['Vollständiger Name', 'text', 'name', 150, true],
    'email'            => ['E-Mail-Adresse', 'email', 'email', 190, true],
    'password'         => ['Passwort (mind. 8 Zeichen)', 'password', 'new-password', 120, true],
    'password_confirm' => ['Passwort wiederholen', 'password', 'new-password', 120, true],
    'telephone'        => ['Telefonnummer', 'tel', 'tel', 50, false],
    'address'          => ['Adresse / Standort', 'text', 'street-address', 255, false],
    'profile_bio'      => ['Investmentfokus & Kurzprofil', 'textarea', 'off', 1500, false],
];

$form = array_fill_keys(array_keys($fields), '');
$errors = [];
$formError = '';
$registered = ($_SESSION['investor_signup_success'] ?? false) === true;

unset($_SESSION['investor_signup_success']);
$_SESSION['signup_investor_token'] ??= bin2hex(random_bytes(32));

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['signup_investor_token'], $token)) {
        $formError = 'Das Formular ist abgelaufen. Bitte lade die Seite neu.';
    }

    foreach ($fields as $name => [$label, $type, $autocomplete, $maxLength, $required]) {
        $raw = $_POST[$name] ?? '';
        $value = is_string($raw) ? trim($raw) : '';
        if ($type !== 'password') {
            $form[$name] = $value;
        }
    }

    if ($formError === '') {
        if (!($pdo instanceof PDO)) {
            $logger->error('Database PDO instance missing in public/signup-investor.php');
            $formError = 'Datenbankverbindung nicht verfügbar. Bitte versuche es später erneut.';
        } else {
            $service = new InvestorSignupService(new InvestorSignupRepo($pdo, $logger));
            $result = $service->register($_POST);

            if ($result['ok']) {
                $_SESSION['investor_signup_success'] = true;
                $_SESSION['signup_investor_token'] = bin2hex(random_bytes(32));
                header('Location: signup-investor.php#registrierung', true, 303);
                exit;
            }

            $errors = $result['errors'];
            $formError = $result['formError'];
        }
    }
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Registriere dein Investorenkonto auf StartConnect und entdecke passende Startups.">
    <title>Als Investor registrieren · StartConnect</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <a class="skip-link" href="#inhalt">Zum Inhalt springen</a>

    <?php require __DIR__ . '/../includes/header.php'; ?>

    <main id="inhalt">
        <section class="contact-hero" aria-labelledby="investor-signup-title">
            <div class="container">
                <p class="eyebrow">Für Investoren</p>
                <h1 id="investor-signup-title">Investorenkonto <em>erstellen</em> und Startups entdecken.</h1>
                <p class="contact-lead">
                    Lege dein Investorenprofil an, um spannende Gründungen zu verfolgen und Kontakt aufzunehmen.
                </p>
            </div>
        </section>

        <section class="contact-form-section" id="registrierung" aria-labelledby="form-heading">
            <div class="container contact-section">
                <h2 id="form-heading">Deine Registrierung</h2>
                <p class="contact-note">Felder mit * sind Pflichtfelder.</p>

                <?php if ($registered): ?>
                    <p class="contact-feedback contact-success" role="status">
                        Danke! Dein Investorenkonto wurde angelegt. Du kannst dich jetzt einloggen.
                    </p>
                <?php elseif ($formError !== ''): ?>
                    <p class="contact-feedback contact-error" role="alert">
                        <?= h($formError) ?>
                    </p>
                <?php endif; ?>

                <form class="contact-form" method="post" action="signup-investor.php#registrierung">
                    <input type="hidden" name="token" value="<?= h($_SESSION['signup_investor_token']) ?>">

                    <?php foreach ($fields as $name => [$label, $type, $autocomplete, $maxLength, $required]): ?>
                        <?php
                        $wide = $name === 'profile_bio';
                        $invalid = isset($errors[$name]);
                        $displayValue = $type === 'password' ? '' : $form[$name];
                        ?>
                        <div class="contact-field<?= $wide ? ' contact-wide' : '' ?>">
                            <label for="<?= h($name) ?>">
                                <?= h($label) ?>
                                <?php if ($required): ?>
                                    <span aria-hidden="true">*</span>
                                <?php else: ?>
                                    <small>(optional)</small>
                                <?php endif; ?>
                            </label>

                            <?php if ($type === 'textarea'): ?>
                                <textarea id="<?= h($name) ?>" name="<?= h($name) ?>" rows="5"
                                          maxlength="<?= $maxLength ?>" <?= $required ? 'required' : '' ?>
                                          <?= $invalid ? 'aria-invalid="true" aria-describedby="' . h($name) . '-error"' : '' ?>><?= h($displayValue) ?></textarea>
                            <?php else: ?>
                                <input id="<?= h($name) ?>" name="<?= h($name) ?>" type="<?= h($type) ?>"
                                       autocomplete="<?= h($autocomplete) ?>" maxlength="<?= $maxLength ?>"
                                       value="<?= h($displayValue) ?>" <?= $required ? 'required' : '' ?>
                                       <?= $invalid ? 'aria-invalid="true" aria-describedby="' . h($name) . '-error"' : '' ?>>
                            <?php endif; ?>

                            <?php if ($invalid): ?>
                                <p id="<?= h($name) ?>-error" class="contact-error"><?= h($errors[$name]) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <div class="contact-wide">
                        <button class="button button-dark" type="submit">
                            Als Investor registrieren <span aria-hidden="true">↗</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.html'; ?>
</body>
</html>