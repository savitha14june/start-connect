<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
$registrationAvailable = is_file(__DIR__ . '/register.php');
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Stelle dein Startup, dein Team und deinen Finanzierungsbedarf vor und komme mit passenden Investoren in Kontakt.">
    <title>Für Startups · StartConnect</title>

    <link rel="stylesheet" href="index.css">

</head>
<body>
    <a class="skip-link" href="#inhalt">Zum Inhalt springen</a>

    <?php require __DIR__ . '/../includes/header.php'; ?>

    <main id="inhalt" class="founder-page">
        <section class="founder-hero" aria-labelledby="startups-titel">
            <div class="container founder-hero-grid">
                <div>
                    <p class="eyebrow">Für Gründerteams</p>
                    <h1 id="startups-titel">Mach dein Startup <em>sichtbar.</em></h1>
                    <p class="founder-lead">
                        Zeig Investoren, welches Problem du löst, wer hinter deiner Idee
                        steht und wofür du Kapital suchst.
                    </p>
                    <a class="button button-primary founder-button" href="#registrierung">
                        Startup-Profil erstellen <span aria-hidden="true">↗</span>
                    </a>
                </div>

                <article class="founder-preview" aria-labelledby="beispiel-titel">
                    <p class="founder-label">So sieht ein Beispielprofil aus</p>
                    <h2 id="beispiel-titel">WärmeWerk</h2>
                    <p>Software, die Heizungen in Mehrfamilienhäusern an den tatsächlichen Bedarf anpasst.</p>
                    <dl>
                        <div><dt>Branche</dt><dd>Climate Tech</dd></div>
                        <div><dt>Standort</dt><dd>Hannover</dd></div>
                        <div><dt>Finanzierungsbedarf</dt><dd>480.000 €</dd></div>
                    </dl>
                </article>
            </div>
        </section>

        <section class="container founder-section" aria-labelledby="ablauf-titel">
            <h2 id="ablauf-titel">Vom Profil zum ersten Gespräch</h2>
            <ol class="founder-steps">
                <li>
                    <h3>Startup vorstellen</h3>
                    <p>Beschreibe euer Produkt, eure Zielgruppe und die Erfahrung, die ihr als Team mitbringt.</p>
                </li>
                <li>
                    <h3>Finanzierungsbedarf erklären</h3>
                    <p>Nenne den gesuchten Betrag und zeig, was ihr damit erreichen wollt, etwa einen Prototyp fertigstellen oder neue Kunden gewinnen.</p>
                </li>
                <li>
                    <h3>Kontaktanfragen verwalten</h3>
                    <p>Interessierte Investoren können euch eine Anfrage schicken. Im Benutzerbereich entscheidet ihr, mit wem ihr das Gespräch fortsetzen möchtet.</p>
                </li>
            </ol>
        </section>

        <section class="founder-profile" aria-labelledby="profil-titel">
            <div class="container founder-section founder-profile-grid">
                <div>
                    <h2 id="profil-titel">Was gehört in dein Profil?</h2>
                    <p class="founder-text">
                        Ein gutes Profil zeigt, wo euer Startup heute steht und
                        welchen nächsten Schritt ihr mit der Finanzierung plant.
                    </p>
                </div>

                <ul class="founder-checklist">
                    <li><strong>Geschäftsidee</strong> – Welches Problem löst ihr und für wen?</li>
                    <li><strong>Team</strong> – Wer übernimmt welche Rolle?</li>
                    <li><strong>Branche und Standort</strong> – Wo seid ihr tätig?</li>
                    <li><strong>Projektstand</strong> – Was habt ihr bereits erreicht?</li>
                    <li><strong>Finanzierung</strong> – Wie viel Kapital sucht ihr und wofür?</li>
                </ul>
            </div>
        </section>

        <section class="container founder-section founder-faq" aria-labelledby="fragen-titel">
            <h2 id="fragen-titel">Häufige Fragen</h2>

            <details>
                <summary>Was passiert bei einer Kontaktanfrage?</summary>
                <p>Ein Investor signalisiert Interesse an eurem Startup. Ihr könnt die Anfrage prüfen und über ein erstes Gespräch entscheiden.</p>
            </details>

            <details>
                <summary>Muss ich meinen gesamten Businessplan veröffentlichen?</summary>
                <p>Für das Profil reicht eine verständliche Zusammenfassung. Vertrauliche Unterlagen könnt ihr später gezielt im direkten Austausch teilen.</p>
            </details>

            <details>
                <summary>Führt eine Anfrage direkt zu einer Finanzierung?</summary>
                <p>Eine Anfrage öffnet den Kontakt. Über eine mögliche Beteiligung und ihre Bedingungen entscheidet ihr im weiteren Austausch.</p>
            </details>
        </section>

        <section id="registrierung" class="founder-signup" aria-labelledby="registrierung-titel">
            <div class="container founder-section">
                <h2 id="registrierung-titel">Stell dein Startup vor</h2>
                <p>Registriere dich als Gründer, um dein Startup-Profil anzulegen.</p>

                <?php if ($registrationAvailable): ?>
                    <a class="button button-primary founder-button" href="register.php">
                        Registrieren <span aria-hidden="true">↗</span>
                    </a>
                <?php else: ?>
                    <details>
                        <summary class="button button-primary founder-button">
                            Registrieren <span aria-hidden="true">↗</span>
                        </summary>
                        <p>Die Registrierung wird in dieser Projektversion noch eingerichtet.</p>
                    </details>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.html'; ?>
</body>
</html>