<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

$industries = [
    'climate' => 'Climate Tech',
    'health' => 'Health Tech',
    'logistics' => 'Logistik',
];

$startups = [
    [
        'name' => 'Solara Grid',
        'industry' => 'climate',
        'initials' => 'SG',
        'location' => 'Hannover',
        'idea' => 'Software, mit der Gewerbebetriebe ihren Solarstrom gezielter nutzen und Lastspitzen reduzieren.',
        'team' => 'Ein Gründungsteam aus Energietechnik, Softwareentwicklung und B2B-Vertrieb.',
        'funding' => '450.000 €',
    ],
    [
        'name' => 'CarePilot',
        'industry' => 'health',
        'initials' => 'CP',
        'location' => 'Hamburg',
        'idea' => 'Eine digitale Übergabe für ambulante Pflegeteams, die Informationen aus dem Arbeitsalltag bündelt.',
        'team' => 'Ein Team mit Erfahrung in ambulanter Pflege, Produktentwicklung und UX-Design.',
        'funding' => '300.000 €',
    ],
    [
        'name' => 'Kreislogistik',
        'industry' => 'logistics',
        'initials' => 'KL',
        'location' => 'Bremen',
        'idea' => 'Eine Plattform, über die regionale Händler freie Lieferkapazitäten teilen und Retouren bündeln.',
        'team' => 'Ein Team aus Logistikplanung, Webentwicklung und Handel.',
        'funding' => '250.000 €',
    ],
];

$filter = $_GET['branche'] ?? '';
$selectedIndustry = is_string($filter) && isset($industries[$filter]) ? $filter : '';

$visibleStartups = array_filter(
    $startups,
    static fn(array $startup): bool =>
        $selectedIndustry === '' || $startup['industry'] === $selectedIndustry
);

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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

    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="./index.php" aria-label="StartConnect – Startseite">
                <span class="brand-icon" aria-hidden="true">↗</span>
                StartConnect<span class="brand-dot">.</span>
            </a>
            <nav class="main-nav" aria-label="Hauptnavigation">
                <a href="#startups">Startups</a>
                <a href="#ablauf">So funktioniert’s</a>
            </nav>
            <a class="button button-outline header-cta" href="#startups">Startups entdecken <span aria-hidden="true">↗</span></a>
        </div>
    </header>

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
                    <p>Beispielprofile mit Geschäftsidee, Team und Finanzierungsbedarf.</p>
                </div>
                <form class="filter" method="get" action="index.php#startups">
                    <label for="industry">Branche</label>
                    <div class="filter-controls">
                        <select id="industry" name="branche">
                            <option value="">Alle Branchen</option>
                            <?php foreach ($industries as $key => $industry): ?>
                                <option value="<?= escape($key) ?>"<?= $key === $selectedIndustry ? ' selected' : '' ?>><?= escape($industry) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="button button-dark" type="submit">Filtern</button>
                    </div>
                </form>
            </div>

            <p class="result-count">
                <?= count($visibleStartups) ?> <?= count($visibleStartups) === 1 ? 'Profil' : 'Profile' ?>
                <?php if ($selectedIndustry !== ''): ?>
                    <span>in <?= escape($industries[$selectedIndustry]) ?></span>
                <?php endif; ?>
            </p>

            <div class="startup-grid">
                <?php foreach ($visibleStartups as $startup): ?>
                    <article class="startup-card">
                        <div class="card-top">
                            <span class="startup-logo startup-logo-<?= escape($startup['industry']) ?>" aria-hidden="true"><?= escape($startup['initials']) ?></span>
                            <span class="industry"><?= escape($industries[$startup['industry']]) ?></span>
                        </div>
                        <h3><?= escape($startup['name']) ?></h3>
                        <p class="location"><?= escape($startup['location']) ?></p>
                        <p class="startup-idea"><?= escape($startup['idea']) ?></p>
                        <dl class="funding">
                            <dt>Finanzierungsbedarf</dt>
                            <dd><?= escape($startup['funding']) ?></dd>
                        </dl>
                        <details>
                            <summary>Team kennenlernen</summary>
                            <p><?= escape($startup['team']) ?></p>
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

    <footer class="site-footer">
        <div class="container footer-inner">
            <a class="brand" href="./index.php"><span class="brand-icon" aria-hidden="true">↗</span>StartConnect<span class="brand-dot">.</span></a>
            <p>Abschlussprojekt · Startups und Investoren</p>
        </div>
    </footer>
</body>
</html>

