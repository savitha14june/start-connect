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