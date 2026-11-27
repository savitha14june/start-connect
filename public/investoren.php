<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';

if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'investor') {
    header('Location: dashboard-investoren.php', true, 302);
    exit;
}

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

</head>
<body>
    <a class="skip-link" href="#inhalt">Zum Inhalt springen</a>

    <?php require __DIR__ . '/../includes/header.php'; ?>

    <main id="inhalt" class="investor-page">
        <section class="hero" aria-labelledby="hero-titel">
            <div class="container hero-inner">
                <div>
                    <p class="kicker">Investoren</p>
                    <h1 id="hero-titel">In Startups <em>investieren.</em></h1>
                    <p class="hero-copy">
                        Lerne Gründerteams kennen, prüfe ihre Finanzierungsangebote
                        und sieh, welchen Unternehmensanteil dein Investment im Beispiel ergeben würde.
                    </p>
                    <a class="button button-primary" href="#ablauf">So funktioniert’s <span aria-hidden="true">↓</span></a>
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
                    <a class="button button-primary" href="register.php">Registrieren <span aria-hidden="true">↗</span></a>
                <?php else: ?>
                    <details>
                        <summary class="button button-primary">Registrieren <span aria-hidden="true">↗</span></summary>
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