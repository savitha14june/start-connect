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
    $logger->error('Database PDO instance missing in public/index.php');
    http_response_code(500);
    exit('Datenbankverbindung nicht verfügbar.');
}

$service = new StartupService(new StartupRepo($pdo, $logger));
$categories = $service->getCategories();
$selectedCategory = $service->resolveSelectedCategory($_GET['branche'] ?? '', $categories);
$companies = $service->getCompanyCards($selectedCategory !== '' ? $selectedCategory : null);

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
    <meta name="description" content="StartConnect bringt Gründerteams und Investoren zusammen. Entdecke Startup-Profile nach Branche, Team und Finanzierungsbedarf.">
    <title>StartConnect – Startups und Investoren zusammenbringen</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <a class="skip-link" href="#main">Zum Inhalt springen</a>

    <?php require __DIR__ . '/../includes/header.html'; ?>

    <main id="main">
        <section class="hero" aria-labelledby="hero-title">
            <div class="container hero-inner">
                <div class="hero-copy">
                    <p class="eyebrow"><span aria-hidden="true"></span> Für Gründerteams und Investoren</p>
                    <h1 id="hero-title">Gute Ideen brauchen <em>passende Partner.</em></h1>
                    <p>Entdecke Startups nach Branche, lerne ihre Teams kennen und finde heraus, welche Projekte zu deinem Investmentfokus passen.</p>
                    <div class="hero-actions">
                        <a class="button button-primary" href="#startups">Startups entdecken <span aria-hidden="true">↗</span></a>
                        <a class="text-link" href="#ablauf">Für Gründerteams <span aria-hidden="true">↓</span></a>
                    </div>
                </div>

                <div class="hero-art" aria-hidden="true">
                    <div class="orbit orbit-outer"></div>
                    <div class="orbit orbit-inner"></div>
                    <div class="orbit-center">↗</div>
                    <div class="orbit-label label-top"><span></span> Gründerteams</div>
                    <div class="orbit-label label-bottom"><span></span> Investoren</div>
                    <i class="orbit-point point-one"></i>
                    <i class="orbit-point point-two"></i>
                    <i class="orbit-point point-three"></i>
                </div>
            </div>
        </section>

        <section id="startups" class="directory container" aria-labelledby="directory-title">
            <div class="section-heading">
                <div>
                    <h2 id="directory-title">Startups entdecken</h2>
                    <p>Unternehmensprofile aus der Datenbank mit Branche, Team und Finanzierungsbedarf.</p>
                </div>
                <form class="filter" method="get" action="index.php#startups">
                    <label for="industry">Branche</label>
                    <div class="filter-controls">
                        <select id="industry" name="branche">
                            <option value="">Alle Branchen</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= escape($category) ?>"<?= $category === $selectedCategory ? ' selected' : '' ?>>
                                    <?= escape($category) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="button button-dark" type="submit">Filtern</button>
                    </div>
                </form>
            </div>

            <p class="result-count">
                <?= count($companies) ?> <?= count($companies) === 1 ? 'Profil' : 'Profile' ?>
                <?php if ($selectedCategory !== ''): ?>
                    <span>in <?= escape($selectedCategory) ?></span>
                <?php endif; ?>
            </p>

            <div class="startup-grid">
                <?php foreach ($companies as $company): ?>
                    <article class="startup-card">
                        <div class="card-top">
                            <span class="startup-logo" aria-hidden="true"><?= escape($company['initials']) ?></span>
                            <span class="industry"><?= escape($company['category']) ?></span>
                        </div>
                        <h3><?= escape($company['name']) ?></h3>
                        <p class="location"><?= escape($company['address']) ?></p>
                        <p class="startup-idea"><?= escape($company['summary']) ?></p>
                        <dl class="funding">
                            <dt>Finanzierungsbedarf</dt>
                            <dd><?= escape($company['funding']) ?></dd>
                        </dl>
                        <details>
                            <summary>Team kennenlernen</summary>
                            <p><?= escape($company['team']) ?></p>
                        </details>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="ablauf" class="process" aria-labelledby="process-title">
            <div class="container process-inner">
                <h2 id="process-title">So funktioniert StartConnect</h2>
                <div class="process-grid">
                    <article>
                        <span class="step-icon" aria-hidden="true">✎</span>
                        <h3>Profil erstellen</h3>
                        <p>Gründerteams stellen ihre Geschäftsidee, ihr Team und ihren Finanzierungsbedarf vor.</p>
                    </article>
                    <article>
                        <span class="step-icon" aria-hidden="true">⌕</span>
                        <h3>Projekte entdecken</h3>
                        <p>Investoren filtern nach Branche und lesen die Profile, die zu ihrem Fokus passen.</p>
                    </article>
                    <article>
                        <span class="step-icon" aria-hidden="true">↗</span>
                        <h3>Kontakt aufnehmen</h3>
                        <p>Investoren senden bei Interesse eine Anfrage. Gründerteams verwalten diese in ihrem Benutzerbereich.</p>
                    </article>
                </div>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.html'; ?>
</body>
</html>