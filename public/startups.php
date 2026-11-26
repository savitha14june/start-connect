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

    <style>
        .founder-page {
            --founder-dark: #102c24;
            --founder-accent: #dcfa87;
            --founder-paper: #fffefa;
            --founder-muted: #43594f;
            --founder-border: #d2ded3;
        }

        .founder-page h1 {
            max-width: 15ch;
            font-size: clamp(2.8rem, 6vw, 5.4rem);
            text-wrap: balance;
        }

        .founder-page h2 { text-wrap: balance; }

        .founder-hero {
            padding-block: clamp(3.5rem, 8vw, 7rem);
            background: radial-gradient(ellipse at 85% 20%, #295542, transparent 65%),
                        var(--founder-dark);
            color: var(--founder-paper);
        }

        .founder-hero-grid, .founder-profile-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, .8fr);
            align-items: center;
            gap: clamp(1.5rem, 5vw, 4rem);
        }

        .founder-hero h1 em { color: var(--founder-accent); }

        .founder-lead {
            max-width: 54ch;
            margin-top: 1.2rem;
            color: #e1eee6;
        }

        .founder-button { margin-top: 1.4rem; }

        .founder-preview {
            padding: clamp(1.25rem, 4vw, 2rem);
            border: 1px solid #719181;
            border-radius: 1.25rem;
            background: #1c3e32;
        }

        .founder-preview .founder-label {
            margin-bottom: .5rem;
            color: var(--founder-accent);
            font-size: .82rem;
            font-weight: 750;
        }

        .founder-preview p { margin-top: .55rem; color: #e1eee6; }

        .founder-preview dl { margin-top: 1rem; }

        .founder-preview dl > div {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: .25rem 1rem;
            padding-block: .7rem;
            border-top: 1px solid #719181;
        }

        .founder-preview dd { font-weight: 750; }
        .founder-section { padding-block: clamp(2.8rem, 6vw, 5rem); }

        .founder-steps {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin: 1.5rem 0 0;
            padding: 0;
            list-style: none;
            counter-reset: founder-step;
        }

        .founder-steps li {
            counter-increment: founder-step;
            padding: 1.35rem;
            border: 1px solid var(--founder-border);
            border-radius: 1rem;
            background: var(--founder-paper);
        }

        .founder-steps li::before {
            content: counter(founder-step, decimal-leading-zero);
            display: block;
            margin-bottom: .8rem;
            color: #32684e;
            font-size: .85rem;
            font-weight: 800;
        }

        .founder-steps p, .founder-text, .founder-faq details p {
            margin-top: .55rem;
            color: var(--founder-muted);
        }

        .founder-profile { background: #eaf0e6; }

        .founder-checklist {
            margin: 0;
            padding: 1.15rem 1.15rem 1.15rem 2.3rem;
            border: 1px solid var(--founder-border);
            border-radius: 1rem;
            background: var(--founder-paper);
        }

        .founder-checklist li + li { margin-top: .6rem; }

        .founder-faq h2 { margin-bottom: 1.3rem; }
        .founder-faq details { padding-block: .85rem; }
        .founder-faq summary { font-size: 1rem; }

        .founder-signup {
            background: var(--founder-dark);
            color: var(--founder-paper);
            text-align: center;
        }

        .founder-signup p {
            max-width: 55ch;
            margin: .8rem auto 0;
            color: #e1eee6;
        }

        .founder-signup details { padding: 0; border: 0; }
        .founder-signup summary.button { list-style: none; }
        .founder-signup summary.button::-webkit-details-marker { display: none; }
        .founder-hero :focus-visible, .founder-signup :focus-visible {
            outline-color: var(--founder-accent);
        }

        @media (max-width: 43rem) {
            .founder-hero-grid, .founder-profile-grid, .founder-steps {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <a class="skip-link" href="#inhalt">Zum Inhalt springen</a>

    <?php require __DIR__ . '/../includes/header.html'; ?>

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