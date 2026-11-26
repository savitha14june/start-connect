<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../src/logger.php';
require_once __DIR__ . '/../src/repositories/StartupRepo.php';
require_once __DIR__ . '/../src/services/StartupService.php';

use App\Logger;

$logger = new Logger(__DIR__ . '/../logs/app.log');

$pdo = require __DIR__ . '/../config/db.php';
if (!($pdo instanceof PDO) && isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
    $pdo = $GLOBALS['pdo'];
}

if (!($pdo instanceof PDO)) {
    $logger->error('Database PDO instance missing in public/company-details.php');
    http_response_code(500);
    exit('Datenbankverbindung nicht verfügbar.');
}

$rawId = $_GET['id'] ?? '';
$companyId = is_string($rawId) && ctype_digit($rawId) ? (int) $rawId : 0;

$service = new StartupService(new StartupRepo($pdo, $logger));
$company = $service->getCompanyDetails($companyId);

if ($company === null) {
    http_response_code(404);
}

function escape(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $company !== null ? escape($company['tagline'] ?: $company['name']) : 'Unternehmensprofil auf StartConnect' ?>">
    <title><?= $company !== null ? escape($company['name']) . ' · StartConnect' : 'Unternehmen nicht gefunden · StartConnect' ?></title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <a class="skip-link" href="#main">Zum Inhalt springen</a>

    <?php require __DIR__ . '/../includes/header.html'; ?>

    <main id="main">
        <?php if ($company === null): ?>
            <section class="container company-detail-empty">
                <h1>Unternehmen nicht gefunden</h1>
                <p>Das angeforderte Startup-Profil existiert nicht oder wurde noch nicht freigegeben.</p>
                <a class="button button-dark" href="index.php#startups">Zurück zur Übersicht</a>
            </section>
        <?php else: ?>
            <section class="contact-hero" aria-labelledby="company-title">
                <div class="container">
                    <a class="company-back-link" href="index.php#startups">← Zurück zu allen Startups</a>
                    <div class="company-detail-header">
                        <span class="startup-logo company-detail-logo" aria-hidden="true"><?= escape($company['initials']) ?></span>
                        <div>
                            <span class="industry"><?= escape($company['category']) ?></span>
                            <h1 id="company-title"><?= escape($company['name']) ?></h1>
                            <?php if ($company['tagline'] !== ''): ?>
                                <p class="contact-lead"><?= escape($company['tagline']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <section class="container contact-section company-detail-grid" aria-label="Unternehmensdetails">
                <div class="company-detail-main">
                    <article class="contact-card">
                        <h2>Geschäftsidee &amp; Vision</h2>
                        <p class="company-description"><?= nl2br(escape($company['description'])) ?></p>
                    </article>

                    <article class="contact-card">
                        <h2>Gründerteam</h2>
                        <h3><?= escape($company['founder_name']) ?></h3>
                        <p><?= nl2br(escape($company['founder_bio'])) ?></p>
                    </article>
                </div>

                <aside class="company-detail-sidebar">
                    <article class="contact-card">
                        <h3>Finanzierungsdaten</h3>

                        <div class="funding-progress" aria-label="Finanzierungsfortschritt <?= escape($company['progress_pct']) ?> Prozent">
                            <div class="funding-progress-bar" style="width: <?= escape($company['progress_pct']) ?>%;"></div>
                        </div>
                        <p class="funding-progress-label">
                            <strong><?= escape($company['raised_amount']) ?></strong> von <?= escape($company['funding_goal']) ?> finanziert (<?= escape($company['progress_pct']) ?> %)
                        </p>

                        <dl class="company-metrics">
                            <div>
                                <dt>Finanzierungsziel</dt>
                                <dd><?= escape($company['funding_goal']) ?></dd>
                            </div>
                            <div>
                                <dt>Unternehmensbewertung</dt>
                                <dd><?= escape($company['company_valuation']) ?></dd>
                            </div>
                            <div>
                                <dt>Mindestinvestment</dt>
                                <dd><?= escape($company['minimum_investment']) ?></dd>
                            </div>
                            <div>
                                <dt>Erwartete Rendite</dt>
                                <dd><?= escape($company['profit_return_pct']) ?></dd>
                            </div>
                            <div>
                                <dt>Standort</dt>
                                <dd><?= escape($company['address']) ?></dd>
                            </div>
                            <div>
                                <dt>Telefon</dt>
                                <dd><?= escape($company['telephone']) ?></dd>
                            </div>
                        </dl>

                        <a class="button button-dark company-cta-btn"
                           href="<?= escape('invest.php?company_id=' . rawurlencode($company['id'])) ?>">
                            Interessiert <span aria-hidden="true">↗</span>
                        </a>
                    </article>
                </aside>
            </section>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../includes/footer.html'; ?>
</body>
</html>