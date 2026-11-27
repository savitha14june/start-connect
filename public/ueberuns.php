<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
?>





<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Lerne StartConnect kennen , das Gründerteams und interessierte Investoren zusammenbringt.">
    <title>Über uns · StartConnect</title>

    <link rel="stylesheet" href="index.css">

    <style>
        .about-page {
            --about-dark: #102c24;
            --about-accent: #dcfa87;
            --about-paper: #fffefa;
            --about-muted: #43594f;
            --about-border: #d2ded3;
        }

        .about-page h1 {
            max-width: 15ch;
            font-size: clamp(2.75rem, 7vw, 5.5rem);
            text-wrap: balance;
        }

        .about-page h2 { text-wrap: balance; }

        .about-hero {
            padding-block: clamp(3.5rem, 9vw, 7rem);
            background: radial-gradient(ellipse at 90% 20%, #295542, transparent 65%),
                        var(--about-dark);
            color: var(--about-paper);
        }

        .about-hero-grid, .about-mission-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, .8fr);
            align-items: center;
            gap: clamp(1.5rem, 5vw, 4rem);
        }

        .about-hero h1 em { color: var(--about-accent); }

        .about-lead {
            max-width: 52ch;
            margin-top: 1.2rem;
            color: #e0ebe3;
        }

        .about-hero-panel {
            padding: clamp(1.25rem, 4vw, 2.25rem);
            border: 1px solid #789587;
            border-radius: 1.25rem;
            background: #1a3c31;
        }

        .about-hero-panel h2 {
            color: var(--about-accent);
            font-size: clamp(1.75rem, 3vw, 2.6rem);
        }

        .about-hero-panel p {
            margin-top: .7rem;
            color: #e0ebe3;
        }

        .about-section { padding-block: clamp(2.75rem, 7vw, 5rem); }

        .about-mission { background: #eaf0e6; }

        .about-mission p, .about-principles p {
            color: var(--about-muted);
        }

        .about-mission p + p { margin-top: .85rem; }

        .about-principles {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .about-principles article {
            padding: clamp(1.1rem, 3vw, 1.6rem);
            border: 1px solid var(--about-border);
            border-radius: 1rem;
            background: var(--about-paper);
        }

        .about-principles p { margin-top: .6rem; }

        .about-cta {
            background: var(--about-dark);
            color: var(--about-paper);
        }

        .about-cta p {
            max-width: 56ch;
            margin-top: .85rem;
            color: #e0ebe3;
        }

        .about-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            margin-top: 1.4rem;
        }

        .about-actions .button-outline {
            border-color: #789587;
            color: var(--about-paper);
        }

        .about-actions .button-outline:hover { background: #244a3c; }
        .about-cta :focus-visible { outline-color: var(--about-accent); }

        .site-header [aria-current="page"] {
            text-decoration: underline;
            text-underline-offset: .25em;
        }

        @media (max-width: 43rem) {
            .about-hero-grid, .about-mission-grid, .about-principles {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <a class="skip-link" href="#inhalt">Zum Inhalt springen</a>

    <?php require __DIR__ . '/../includes/header.php'; ?>

    <main id="inhalt" class="about-page">
        <section class="about-hero" aria-labelledby="about-titel">
            <div class="container about-hero-grid">
                <div>
                    <p class="eyebrow">Über StartConnect</p>
                    <h1 id="about-titel">Der erste Schritt zu <em>passenden Partnern.</em></h1>
                    <p class="about-lead">
                        StartConnect bringt Gründerteams und interessierte Investoren zusammen.
                        Wir möchten es leichter machen, Startup-Ideen zu entdecken und
                        ein erstes Gespräch zu beginnen.
                    </p>
                </div>

                <aside class="about-hero-panel" aria-labelledby="projekt-titel">
                    <h2 id="projekt-titel">Passende Finanzierungspartner finden</h2>
                    <p>
                        Wir prüfen die Identität der Gründerteams, bevor wir ihre Profile
                        veröffentlichen. Investoren sehen auf einen Blick, wer hinter einer
                        Geschäftsidee steht, wofür das Team Kapital sucht und welche
                        Informationen zum Angebot vorliegen.
                    </p>
                </aside>
            </div>
        </section>

        <section class="about-mission" aria-labelledby="idee-titel">
            <div class="container about-section about-mission-grid">
                <h2 id="idee-titel">Warum wir StartConnect entwickeln</h2>

                <div>
                    <p>
                        Gründerteams brauchen einen Ort, an dem sie ihre Geschäftsidee,
                        ihr Team und ihren Finanzierungsbedarf verständlich vorstellen können.
                        Investoren möchten schnell erkennen, welche Projekte zu ihrem Fokus passen.
                    </p>
                    <p>
                        StartConnect bündelt diese Informationen in Startup-Profilen.
                        Über den Branchenfilter finden Investoren relevante Projekte.
                        Bei Interesse können sie eine Kontaktanfrage an das Gründerteam stellen.
                    </p>
                </div>
            </div>
        </section>

        <section class="container about-section" aria-labelledby="prinzipien-titel">
            <h2 id="prinzipien-titel">Was uns bei der Plattform wichtig ist</h2>

            <div class="about-principles">
                <article>
                    <h3>Verständliche Profile</h3>
                    <p>
                        Geschäftsidee, Team und Finanzierungsbedarf stehen an einem Ort.
                        So bekommen Interessierte schnell einen ersten Überblick.
                    </p>
                </article>

                <article>
                    <h3>Gezielte Suche</h3>
                    <p>
                        Investoren filtern nach Branche und können sich auf die
                        Projekte konzentrieren, die zu ihren Interessen passen.
                    </p>
                </article>

                <article>
                    <h3>Kontakt auf Augenhöhe</h3>
                    <p>
                        Eine Anfrage eröffnet das Gespräch. Gründerteams entscheiden,
                        mit wem sie den Austausch vertiefen möchten.
                    </p>
                </article>
            </div>
        </section>

        <section class="about-cta" aria-labelledby="entdecken-titel">
            <div class="container about-section">
                <h2 id="entdecken-titel">Entdecke StartConnect</h2>
                <p>
                    Finde heraus, wie du dein Startup vorstellst oder wie du als
                    Investor ein Projekt kennenlernst.
                </p>

                <div class="about-actions">
                    <a class="button button-primary" href="startups.php">
                        Für Startups <span aria-hidden="true">↗</span>
                    </a>
                    <a class="button button-outline" href="investoren.php">Für Investoren</a>
                </div>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.html'; ?>
</body>
</html>