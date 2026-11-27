<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';


require_once __DIR__ . '/../src/logger.php';
require_once __DIR__ . '/../src/repositories/ContactMessageRepo.php';
require_once __DIR__ . '/../src/services/ContactMessageService.php';

use App\Logger;

$logger = new Logger(__DIR__ . '/../logs/app.log');

$pdo = require __DIR__ . '/../config/db.php';
if (!($pdo instanceof PDO) && isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
    $pdo = $GLOBALS['pdo'];
}

$recipient = 'kontakt@startconnect.de';

$fields = [
    'first_name' => ['Vorname', 'text', 'given-name', 80, true],
    'last_name'  => ['Nachname', 'text', 'family-name', 80, true],
    'email'      => ['E-Mail', 'email', 'email', 190, true],
    'phone'      => ['Telefonnummer', 'tel', 'tel', 40, false],
    'subject'    => ['Betreff', 'text', 'off', 120, false],
    'message'    => ['Nachricht', 'textarea', 'off', 3000, true],
];

$form = array_fill_keys(array_keys($fields), '');
$errors = [];
$formError = '';
$sent = ($_SESSION['contact_sent'] ?? false) === true;

unset($_SESSION['contact_sent']);
$_SESSION['contact_token'] ??= bin2hex(random_bytes(32));

function contactEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['contact_token'], $token)) {
        $formError = 'Das Formular ist abgelaufen. Bitte lade die Seite neu.';
    }

    foreach ($fields as $name => [$label, $type, $autocomplete, $maxLength, $required]) {
        $value = $_POST[$name] ?? '';
        $value = is_string($value) ? trim($value) : '';

        if (strlen($value) > $maxLength * 4) {
            $errors[$name] = 'Die Eingabe ist zu lang.';
            continue;
        }

        $form[$name] = $value;

        if ($required && $value === '') {
            $errors[$name] = 'Bitte fülle dieses Feld aus.';
        } elseif (preg_match('/\A.{0,' . $maxLength . '}\z/us', $value) !== 1) {
            $errors[$name] = 'Bitte kürze die Eingabe auf maximal ' . $maxLength . ' Zeichen.';
        }
    }

    if ($form['email'] !== '' && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Bitte gib eine gültige E-Mail-Adresse ein.';
    }

    if ($errors === [] && $formError === '') {
        if (!($pdo instanceof PDO)) {
            $logger->error('Database PDO instance missing in public/kontakt.php');
            $formError = 'Datenbankverbindung nicht verfügbar. Bitte versuche es später erneut.';
        } else {
            $service = new ContactMessageService(new ContactMessageRepo($pdo, $logger));
            $userId = isset($_SESSION['user_id']) && is_int($_SESSION['user_id'])
                ? $_SESSION['user_id']
                : null;
            $companyId = isset($_GET['company_id']) && ctype_digit((string) $_GET['company_id'])
                ? (int) $_GET['company_id']
                : null;

            $result = $service->submitContactForm($form, $errors, $userId, $companyId);

            if ($result['ok']) {
                $_SESSION['contact_sent'] = true;
                $_SESSION['contact_token'] = bin2hex(random_bytes(32));
                header('Location: kontakt.php#kontaktformular', true, 303);
                exit;
            }

            $formError = $result['error'] !== ''
                ? $result['error']
                : 'Deine Nachricht konnte nicht gespeichert werden. Bitte versuche es später erneut.';
        }
    }
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Frag ein kostenloses Erstgespräch mit dem StartConnect-Team an. Für Gründerteams und Investoren.">
    <title>Kontakt · StartConnect</title>

    <link rel="stylesheet" href="index.css">
</head>
<body>
    <a class="skip-link" href="#inhalt">Zum Inhalt springen</a>

    <?php require __DIR__ . '/../includes/header.php'; ?>

    <main id="inhalt">
        <section class="contact-hero" aria-labelledby="kontakt-titel">
            <div class="container">
                <p class="eyebrow">Kontakt &amp; Beratung</p>
                <h1 id="kontakt-titel">Lass uns über deinen <em>nächsten Schritt</em> sprechen.</h1>
                <p class="contact-lead">
                    Ob du ein Startup aufbaust oder nach Projekten suchst:
                    Unsere Experten nehmen sich Zeit für deine Fragen.
                    Das Erstgespräch ist kostenlos und unverbindlich.
                </p>
            </div>
        </section>

        <section class="container contact-section" aria-labelledby="beratung-titel">
            <h2 id="beratung-titel">Kostenloses Erstgespräch</h2>

            <div class="contact-grid">
                <article class="contact-card">
                    <h3>Für Investoren</h3>
                    <p>Wir helfen dir, dich auf StartConnect zu orientieren und ein Gespräch mit einem Gründerteam vorzubereiten.</p>
                    <ul>
                        <li>Startups nach Branche und Finanzierungsphase einordnen</li>
                        <li>Unternehmensbewertung und Beteiligungsbegriffe verstehen</li>
                        <li>Fragen für das erste Gespräch mit dem Team vorbereiten</li>
                    </ul>
                    <a class="button button-dark contact-email-button"
                       href="<?= contactEscape('mailto:' . $recipient . '?subject=' . rawurlencode('Kostenloses Erstgespräch für Investoren')) ?>">
                       E-Mail als Investor schreiben <span aria-hidden="true">↗</span>
                    </a>
                </article>

                <article class="contact-card">
                    <h3>Für Startups</h3>
                    <p>Wir helfen dir, deine Geschäftsidee klar vorzustellen und dich auf Gespräche mit Investoren vorzubereiten.</p>
                    <ul>
                        <li>Geschäftsidee und Team verständlich präsentieren</li>
                        <li>Finanzierungsbedarf und Mittelverwendung erklären</li>
                        <li>Startup-Profil und Pitch vorbereiten</li>
                    </ul>
                    <a class="button button-dark contact-email-button"
                    href="<?= contactEscape('mailto:' . $recipient . '?subject=' . rawurlencode('Kostenloses Erstgespräch für Startups')) ?>">
                    E-Mail als Gründer schreiben <span aria-hidden="true">↗</span>
                   </a>
                </article>
            </div>
        </section>

        <section class="contact-form-section" id="kontaktformular" aria-labelledby="formular-titel">
            <div class="container contact-section">
                <h2 id="formular-titel">Schreib uns</h2>
                <p class="contact-note">
                    Sag uns kurz, welches Thema du besprechen möchtest.
                    Wir melden uns, um einen Termin zu vereinbaren. Felder mit * sind Pflichtfelder.
                </p>

                <?php if ($sent): ?>
                    <p class="contact-feedback contact-success" role="status">
                        Danke! Deine Nachricht wurde gespeichert.
                    </p>
                <?php elseif ($formError !== '' || $errors !== []): ?>
                    <p class="contact-feedback contact-error" role="alert">
                        <?= contactEscape($formError !== '' ? $formError : 'Bitte prüfe die markierten Felder.') ?>
                    </p>
                <?php endif; ?>

                <form class="contact-form" method="post" action="kontakt.php#kontaktformular">
                    <input type="hidden" name="token" value="<?= contactEscape($_SESSION['contact_token']) ?>">

                    <?php foreach ($fields as $name => [$label, $type, $autocomplete, $maxLength, $required]): ?>
                        <?php
                        $wide = in_array($name, ['subject', 'message'], true);
                        $invalid = isset($errors[$name]);
                        ?>

                        <div class="contact-field<?= $wide ? ' contact-wide' : '' ?>">
                            <label for="<?= $name ?>">
                                <?= $label ?>
                                <?php if ($required): ?>
                                    <span aria-hidden="true">*</span>
                                <?php else: ?>
                                    <small>(optional)</small>
                                <?php endif; ?>
                            </label>

                            <?php if ($type === 'textarea'): ?>
                                <textarea id="<?= $name ?>" name="<?= $name ?>" rows="6"
                                          maxlength="<?= $maxLength ?>" required
                                          <?= $invalid ? 'aria-invalid="true" aria-describedby="' . $name . '-error"' : '' ?>><?= contactEscape($form[$name]) ?></textarea>
                            <?php else: ?>
                                <input id="<?= $name ?>" name="<?= $name ?>" type="<?= $type ?>"
                                       autocomplete="<?= $autocomplete ?>" maxlength="<?= $maxLength ?>"
                                       value="<?= contactEscape($form[$name]) ?>"
                                       <?= $required ? 'required' : '' ?>
                                       <?= $invalid ? 'aria-invalid="true" aria-describedby="' . $name . '-error"' : '' ?>>
                            <?php endif; ?>

                            <?php if ($invalid): ?>
                                <p id="<?= $name ?>-error" class="contact-error">
                                    <?= contactEscape($errors[$name]) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <div class="contact-wide">
                        <button class="button button-dark" type="submit">
                            Nachricht senden <span aria-hidden="true">↗</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.html'; ?>
</body>
</html>