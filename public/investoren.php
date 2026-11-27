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
    <meta name="description" content="Für Investoren bei StartConnect: Startup-Angebote verstehen, Beteiligungsanteile berechnen und Risiken einschätzen.">
    <title>Für Investoren · StartConnect</title>
    <link rel="stylesheet" href="index.css">

    <style>
        :root {
            --ink: #122923;
            --forest: #102d25;
            --surface: #fffefa;
            --background: #f5f6ef;
            --muted: #43594f;
            --line: #cddbd0;
            --accent: #dcfa87;
            --radius: 1.25rem;
            font-family: "Avenir Next", "Segoe UI", system-ui, sans-serif;
            color: var(--ink);
            background: var(--background);
            line-height: 1.6;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body, h1, h2, h3, p, dl, dd { margin: 0; }
        a { color: inherit; }
        input, button { font: inherit; }
        :focus-visible { outline: 3px solid #227a5d; outline-offset: 4px; }
        section[id] { scroll-margin-top: 1rem; }

        .container { width: min(100% - 2rem, 72rem); margin-inline: auto; }
        .skip-link { position: absolute; top: -5rem; left: 1rem; z-index: 2; padding: .5rem 1rem; background: var(--accent); }
        .skip-link:focus { top: 1rem; }

        header { background: var(--surface); border-bottom: 1px solid var(--line); }
        .header-inner, .footer-inner, nav {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .85rem 1.5rem;
        }
        .header-inner, .footer-inner { justify-content: space-between; padding-block: 1.1rem; }
        .brand { font-size: 1.3rem; font-weight: 800; letter-spacing: -.045em; text-decoration: none; }
        .brand-mark {
            display: inline-grid;
            place-items: center;
            width: 1.9rem;
            height: 1.9rem;
            border-radius: .6rem;
            background: var(--forest);
            color: var(--accent);
            margin-right: .35rem;
        }
        nav a { font-size: .93rem; font-weight: 650; text-decoration: none; }
        nav a:hover, nav [aria-current] { text-decoration: underline; text-underline-offset: .3em; }

        .hero, .signup { background: var(--forest); color: var(--surface); }
        .hero { background-image: radial-gradient(ellipse at 90% 20%, #244e3d, transparent 55%); }
        .hero-inner, .example-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, .85fr);
            align-items: center;
            gap: clamp(1.5rem, 5vw, 5rem);
        }
        .hero-inner { padding-block: clamp(3.75rem, 9vw, 8rem); }
        .kicker {
            color: var(--accent);
            font-size: .8rem;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }
        h1, h2 {
            font-family: Georgia, "Times New Roman", serif;
            font-weight: 500;
            letter-spacing: -.047em;
            line-height: 1.05;
        }
        h1 { font-size: clamp(3rem, 7.4vw, 6.5rem); text-wrap: balance; }
        h1 em { color: var(--accent); }
        h2 { font-size: clamp(2rem, 4.4vw, 3.7rem); }
        h3 { font-size: 1.14rem; line-height: 1.3; }

        .hero-copy, .signup p { max-width: 54ch; margin-top: 1rem; color: #dfebe3; }
        .minimum {
            padding: clamp(1.25rem, 4vw, 2.5rem);
            border: 1px solid #7a9b8a;
            border-radius: var(--radius);
            background: #1b3d31;
        }
        .minimum span { display: block; font-weight: 700; }
        .minimum strong { display: block; font: 500 clamp(3.5rem, 8vw, 6rem)/1.05 Georgia, serif; color: var(--accent); }
        .minimum p { margin-top: .55rem; color: #dfebe3; }

        main.button {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            min-height: 2.85rem;
            margin-top: 1.35rem;
            padding: .65rem 1.15rem;
            border: 1px solid transparent;
            border-radius: 100vw;
            background: var(--accent);
            color: var(--ink);
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }
        main .button:hover { background: #efffb8; }
        .hero :focus-visible, .signup :focus-visible { outline-color: var(--accent); }
        .signup .button { color: var(--ink); }
        .signup details { margin: 0; padding: 0; border: 0; }
        .signup summary.button { list-style: none; }
        .signup summary.button::-webkit-details-marker { display: none; }

        .section { padding-block: clamp(2.8rem, 7vw, 5.5rem); }
        .intro { max-width: 62ch; margin-top: .85rem; color: var(--muted); }
        .steps {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .85rem;
            margin: 1.6rem 0 0;
            padding: 0;
            list-style: none;
            counter-reset: step;
        }
        .steps li, .calculator {
            padding: clamp(1rem, 3vw, 1.6rem);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--surface);
        }
        .steps li { counter-increment: step; }
        .steps li::before {
            content: counter(step, decimal-leading-zero);
            display: block;
            margin-bottom: .8rem;
            color: #316449;
            font-size: .84rem;
            font-weight: 800;
        }
        .steps p { margin-top: .5rem; color: var(--muted); }

        .example-section { background: #e8efe5; border-block: 1px solid var(--line); }
        .calculator fieldset { display: grid; gap: .85rem; margin: 1rem 0 0; padding: 0; border: 0; }
        .calculator label { display: grid; gap: .3rem; font-weight: 700; }
        .calculator input {
            width: 100%;
            min-height: 2.85rem;
            padding: .45rem .65rem;
            border: 1px solid #7b9184;
            border-radius: .55rem;
            color: var(--ink);
            background: #fff;
        }
        .result {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            justify-content: space-between;
            gap: .5rem 1rem;
            margin-top: 1rem;
            padding: .8rem;
            border-radius: .65rem;
            background: #edf5e6;
            font-weight: 700;
        }
        output { font-size: 1.25rem; font-variant-numeric: tabular-nums; }
        .note { margin-top: .85rem; color: var(--muted); font-size: .9rem; }

        .faq h2 { margin-bottom: 1.25rem; }
        .faq details { padding: .9rem 0; border-top: 1px solid var(--line); }
        .faq summary { cursor: pointer; font-weight: 750; }
        .faq details p { max-width: 70ch; margin-top: .45rem; color: var(--muted); }
        .signup .container { text-align: center; }
        .signup p { margin-inline: auto; }
        footer { background: var(--surface); font-size: .9rem; }

        @media (max-width: 54rem) {
            .steps { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 38rem) {
            .hero-inner, .example-grid, .steps { grid-template-columns: 1fr; }
            nav { gap: .45rem .95rem; }
        }
    </style>
</head>
<body>
    <a class="skip-link" href="#inhalt">Zum Inhalt springen</a>

    <?php require __DIR__ . '/../includes/header.php'; ?>

    <main id="inhalt">
        <section class="hero" aria-labelledby="hero-titel">
            <div class="container hero-inner">
                <div>
                    <p class="kicker">Investoren</p>
                    <h1 id="hero-titel">In Startups <em>investieren.</em></h1>
                    <p class="hero-copy">
                        Lerne Gründerteams kennen, prüfe ihre Finanzierungsangebote
                        und sieh, welchen Unternehmensanteil dein Investment im Beispiel ergeben würde.
                    </p>
                    <a class="button" href="#ablauf">So funktioniert’s <span aria-hidden="true">↓</span></a>
                </div>

                <aside class="minimum" aria-label="Mindestanlage">
                    <span>Vorgesehener Mindestbetrag</span>
                    <strong>200 €</strong>
                    <p>Pro Investment. Die Bedingungen findest du im jeweiligen Angebot.</p>
                </aside>
            </div>
        </section>

        <section id="ablauf" class="container section" aria-labelledby="ablauf-titel">
            <h2 id="ablauf-titel">So funktioniert eine Beteiligung</h2>
            <ol class="steps">
                <li>
                    <h3>Startups entdecken</h3>
                    <p>Filtere nach Branche und lies, welche Idee das Team verfolgt und wofür es Kapital sucht.</p>
                </li>
                <li>
                    <h3>Angebot prüfen</h3>
                    <p>Jedes Startup hat eine eigene Bewertung. Prüfe, wie sie zustande kommt und welche Rechte mit den Anteilen verbunden sind.</p>
                </li>
                <li>
                    <h3>Betrag wählen</h3>
                    <p>Wähle einen Betrag ab 200 €. Beim selben Anteilspreis führt ein höherer Betrag zu einem größeren Anteil.</p>
                </li>
                <li>
                    <h3>Unterlagen lesen</h3>
                    <p>Prüfe vor deiner Entscheidung die Vertragsbedingungen, mögliche Kosten und die Risiken des Angebots.</p>
                </li>
            </ol>
        </section>

        <section class="example-section" aria-labelledby="rechner-titel">
            <div class="container section example-grid">
                <div>
                    <h2 id="rechner-titel">Was bekomme ich für mein Investment?</h2>
                    <p class="intro">
                        Bei einer direkten Beteiligung zum einheitlichen Anteilspreis teilst du
                        deinen Betrag durch die Unternehmensbewertung <strong>nach der Finanzierungsrunde</strong>.
                        Das Ergebnis mal 100 ergibt den Anteil in Prozent.
                    </p>
                    <p class="intro">
                        Beispiel: 1.000 € bei einer Bewertung von 1.000.000 € entsprechen 0,10 %.
                        Der tatsächliche Anteil steht im Beteiligungsvertrag.
                    </p>
                </div>

                <div class="calculator">
                    <h3>Unverbindliches Rechenbeispiel</h3>
                    <fieldset aria-label="Werte für das Rechenbeispiel">
                        <label for="betrag">
                            Dein Investment in Euro
                            <input id="betrag" type="number" min="200" step="1" value="1000" inputmode="numeric">
                        </label>
                        <label for="bewertung">
                            Bewertung nach der Runde in Euro
                            <input id="bewertung" type="number" min="200" step="1" value="1000000" inputmode="numeric">
                        </label>
                    </fieldset>
                    <p class="result">
                        <span>Beispielhafter Anteil</span>
                        <output id="anteil" for="betrag bewertung" aria-live="polite">0,10 %</output>
                    </p>
                    <p class="note" id="rechner-hinweis">Die Berechnung dient zur Orientierung.</p>
                    <noscript><p class="note">Aktiviere JavaScript, um eigene Beträge zu berechnen.</p></noscript>
                </div>
            </div>
        </section>

        <section id="fragen" class="container section faq" aria-labelledby="fragen-titel">
            <h2 id="fragen-titel">Häufige Fragen</h2>

            <details>
                <summary>Welche Rechte bekomme ich mit meinem Anteil?</summary>
                <p>Die Rechte hängen von der Beteiligungsform und dem Vertrag ab. Prüfe, ob dir etwa Stimmrechte, Informationsrechte oder ein Anspruch auf Ausschüttungen zustehen.</p>
            </details>
            <details>
                <summary>Kann sich mein Anteil später ändern?</summary>
                <p>Ja. Wenn das Startup später zusätzliche Anteile ausgibt, kann dein prozentualer Anteil sinken.</p>
            </details>
            <details>
                <summary>Kann ich mein Investment jederzeit verkaufen?</summary>
                <p>Ein Verkauf kann schwierig sein. Ob und wie du deine Beteiligung übertragen kannst, hängt von den Vertragsbedingungen und davon ab, ob sich ein Käufer findet.</p>
            </details>
            <details>
                <summary>Kann ich mein Geld verlieren?</summary>
                <p>Ja. Wenn ein Startup scheitert, kannst du deine gesamte Investition verlieren. Eine Rendite ist nicht garantiert.</p>
            </details>
        </section>

        <section class="signup" id="registrierung" aria-labelledby="registrierung-titel">
            <div class="container section">
                <h2 id="registrierung-titel">Dein Einstieg als Investor</h2>
                <p>Mit einem Konto kannst du Gründerteams kennenlernen und bei Interesse eine Kontaktanfrage stellen.</p>

                <?php if ($registrationAvailable): ?>
                    <a class="button" href="register.php">Registrieren <span aria-hidden="true">↗</span></a>
                <?php else: ?>
                    <details>
                        <summary class="button">Registrieren <span aria-hidden="true">↗</span></summary>
                        <p>Die Registrierung ist in dieser Projektversion noch nicht verfügbar.</p>
                    </details>
                <?php endif; ?>
            </div>
        </section>
    </main>

    
    <?php require __DIR__ . '/../includes/footer.html'; ?>

    <script>
        const investment = document.querySelector('#betrag');
        const valuation = document.querySelector('#bewertung');
        const result = document.querySelector('#anteil');
        const hint = document.querySelector('#rechner-hinweis');
        const formatPercent = new Intl.NumberFormat('de-DE', {
            style: 'percent',
            minimumFractionDigits: 2,
            maximumFractionDigits: 4
        });

        function updateShare() {
            const amount = investment.valueAsNumber;
            const postMoney = valuation.valueAsNumber;

            if (!Number.isFinite(amount) || amount < 200) {
                result.value = '–';
                hint.textContent = 'Gib einen Investmentbetrag von mindestens 200 € ein.';
                return;
            }

            if (!Number.isFinite(postMoney) || postMoney < amount) {
                result.value = '–';
                hint.textContent = 'Die Bewertung muss mindestens so hoch sein wie dein Investment.';
                return;
            }

            result.value = formatPercent.format(amount / postMoney);
            hint.textContent = 'Die Berechnung dient zur Orientierung.';
        }

        investment.addEventListener('input', updateShare);
        valuation.addEventListener('input', updateShare);
    </script>
</body>
</html>