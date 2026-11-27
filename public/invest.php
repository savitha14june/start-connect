<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
session_start();

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../src/logger.php';
require_once __DIR__ . '/../src/repositories/StartupRepo.php';
require_once __DIR__ . '/../src/repositories/InvestmentRepo.php';
require_once __DIR__ . '/../src/services/InvestmentService.php';

use App\Logger;

$logger = new Logger(__DIR__ . '/../logs/app.log');

$pdo = require __DIR__ . '/../config/db.php';
if (!($pdo instanceof PDO) && isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
    $pdo = $GLOBALS['pdo'];
}

if (!($pdo instanceof PDO)) {
    $logger->error('Database PDO instance missing in public/invest.php');
    http_response_code(500);
    exit('Datenbankverbindung nicht verfügbar.');
}

$rawId = $_GET['company_id'] ?? $_POST['company_id'] ?? '';
$companyId = is_string($rawId) && ctype_digit($rawId) ? (int) $rawId : 0;

$startupRepo = new StartupRepo($pdo, $logger);
$company = $companyId > 0 ? $startupRepo->findApprovedCompanyById($companyId) : null;

$fields = [
    'investor_name'     => ['Vollständiger Name', 'text', 'name', 150, true],
    'investor_email'    => ['E-Mail-Adresse', 'email', 'email', 190, true],
    'investor_phone'    => ['Telefonnummer', 'tel', 'tel', 50, false],
    'investor_address'  => ['Adresse / Standort', 'text', 'street-address', 255, false],
    'investment_amount' => ['Geplanter Anlagebetrag (€)', 'text', 'off', 20, true],
    'notes'             => ['Nachricht / Investmentfokus / Fragen', 'textarea', 'off', 2000, false],
];

$form = array_fill_keys(array_keys($fields), '');
$errors = [];
$formError = '';
$submitted = ($_SESSION['invest_success'] ?? false) === true;
$submittedAmount = (string) ($_SESSION['invest_amount'] ?? '');

unset($_SESSION['invest_success'], $_SESSION['invest_amount']);
$_SESSION['invest_token'] ??= bin2hex(random_bytes(32));

function escape(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function formatEur(mixed $amount): string
{
    if ($amount === null || $amount === '') {
        return 'Auf Anfrage';
    }

    return number_format((float) $amount, 0, ',', '.') . ' €';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $company !== null) {
    $token = $_POST['token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['invest_token'], $token)) {
        $formError = 'Das Formular ist abgelaufen. Bitte lade die Seite neu.';
    }

    foreach ($fields as $name => [$label, $type, $autocomplete, $maxLength, $required]) {
        $raw = $_POST[$name] ?? '';
        $form[$name] = is_string($raw) ? trim($raw) : '';
    }

    if ($formError === '') {
        $service = new InvestmentService(new InvestmentRepo($pdo, $logger));
        $investorUserId = isset($_SESSION['user_id']) && is_int($_SESSION['user_id'])
            ? $_SESSION['user_id']
            : null;

        $result = $service->submitInvestment($_POST, $company, $investorUserId);

        if ($result['ok']) {
            $_SESSION['invest_success'] = true;
            $_SESSION['invest_amount'] = $form['investment_amount'];
            $_SESSION['invest_token'] = bin2hex(random_bytes(32));
            header('Location: invest.php?company_id=' . rawurlencode((string) $companyId) . '#invest-formular', true, 303);
            exit;
        }

        $errors = $result['errors'];
        $formError = $result['formError'];
    }
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bekunde dein Interesse und gib deinen geplanten Investitionsbetrag auf StartConnect an.">
    <title><?= $company !== null ? 'Investieren in ' . escape((string) $company['name']) . ' · StartConnect' : 'Investitionsanfrage · StartConnect' ?></title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <a class="skip-link" href="#main">Zum Inhalt springen</a>

    <?php require __DIR__ . '/../includes/header.php'; ?>

    <main id="main">
        <?php if ($company === null): ?>
            <section class="container company-detail-empty">
                <h1>Unternehmen nicht gefunden</h1>
                <p>Bitte wähle ein freigegebenes Startup aus, um eine Investitionsanfrage zu stellen.</p>
                <a class="button button-dark" href="index.php#startups">Zurück zu den Startups</a>
            </section>
        <?php else: ?>
            <section class="contact-hero" aria-labelledby="invest-title">
                <div class="container">
                    <a class="company-back-link" href="<?= escape('company-details.php?id=' . rawurlencode((string) $company['id'])) ?>">
                        ← Zurück zum Unternehmensprofil
                    </a>
                    <p class="eyebrow">Investitionsanfrage</p>
                    <h1 id="invest-title">In <em><?= escape((string) $company['name']) ?></em> investieren.</h1>
                    <p class="contact-lead">
                        Gib deine Kontaktdaten und deinen gewünschten Anlagebetrag an.
                        Wir leiten deine unverbindliche Interessensbekundung strukturiert weiter.
                    </p>
                </div>
            </section>

            <section class="contact-form-section" id="invest-formular" aria-labelledby="invest-form-heading">
                <div class="container contact-section company-detail-grid">
                    <div>
                        <h2 id="invest-form-heading">Investorendaten &amp; Anlagebetrag</h2>
                        <p class="contact-note">Felder mit * sind Pflichtfelder.</p>

                        <?php if ($submitted): ?>
                            <p class="contact-feedback contact-success" role="status">
                                Danke! Deine Investitionsanfrage<?= $submittedAmount !== '' ? ' über ' . escape($submittedAmount) . ' €' : '' ?> für <?= escape((string) $company['name']) ?> wurde gespeichert.
                            </p>
                        <?php elseif ($formError !== ''): ?>
                            <p class="contact-feedback contact-error" role="alert">
                                <?= escape($formError) ?>
                            </p>
                        <?php endif; ?>

                        <form class="contact-form" method="post" action="<?= escape('invest.php?company_id=' . rawurlencode((string) $company['id']) . '#invest-formular') ?>">
                            <input type="hidden" name="token" value="<?= escape($_SESSION['invest_token']) ?>">
                            <input type="hidden" name="company_id" value="<?= escape((string) $company['id']) ?>">

                            <?php foreach ($fields as $name => [$label, $type, $autocomplete, $maxLength, $required]): ?>
                                <?php
                                $wide = in_array($name, ['investor_email', 'investment_amount', 'notes'], true);
                                $invalid = isset($errors[$name]);
                                ?>
                                <div class="contact-field<?= $wide ? ' contact-wide' : '' ?>">
                                    <label for="<?= escape($name) ?>">
                                        <?= escape($label) ?>
                                        <?php if ($required): ?>
                                            <span aria-hidden="true">*</span>
                                        <?php else: ?>
                                            <small>(optional)</small>
                                        <?php endif; ?>
                                    </label>

                                    <?php if ($type === 'textarea'): ?>
                                        <textarea id="<?= escape($name) ?>" name="<?= escape($name) ?>" rows="5"
                                                  maxlength="<?= $maxLength ?>" <?= $required ? 'required' : '' ?>
                                                  <?= $invalid ? 'aria-invalid="true" aria-describedby="' . escape($name) . '-error"' : '' ?>><?= escape($form[$name]) ?></textarea>
                                    <?php else: ?>
                                        <input id="<?= escape($name) ?>" name="<?= escape($name) ?>" type="<?= escape($type) ?>"
                                               autocomplete="<?= escape($autocomplete) ?>" maxlength="<?= $maxLength ?>"
                                               value="<?= escape($form[$name]) ?>" <?= $required ? 'required' : '' ?>
                                               <?= $invalid ? 'aria-invalid="true" aria-describedby="' . escape($name) . '-error"' : '' ?>>
                                    <?php endif; ?>

                                    <?php if ($invalid): ?>
                                        <p id="<?= escape($name) ?>-error" class="contact-error"><?= escape($errors[$name]) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>

                            <div class="contact-wide">
                                <button class="button button-dark" type="submit">
                                    Interesse absenden <span aria-hidden="true">↗</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <aside class="company-detail-sidebar">
                        <article class="contact-card">
                            <h3><?= escape((string) $company['name']) ?></h3>
                            <p><?= escape((string) ($company['tagline'] ?: $company['category'])) ?></p>

                            <dl class="company-metrics">
                                <div>
                                    <dt>Branche</dt>
                                    <dd><?= escape((string) ($company['category'] ?: '–')) ?></dd>
                                </div>
                                <div>
                                    <dt>Finanzierungsziel</dt>
                                    <dd><?= escape(formatEur($company['funding_goal'] ?? null)) ?></dd>
                                </div>
                                <div>
                                    <dt>Bisher eingeworben</dt>
                                    <dd><?= escape(formatEur($company['raised_amount'] ?? null)) ?></dd>
                                </div>
                                <div>
                                    <dt>Mindestinvestment</dt>
                                    <dd><?= escape(formatEur($company['minimum_investment'] ?? null)) ?></dd>
                                </div>
                                <div>
                                    <dt>Erwartete Rendite</dt>
                                    <dd><?= escape(($company['profit_return_pct'] ?? '') !== '' ? number_format((float) $company['profit_return_pct'], 1, ',', '.') . ' %' : 'Auf Anfrage') ?></dd>
                                </div>
                            </dl>
                        </article>
                    </aside>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../includes/footer.html'; ?>
</body>
</html>