<?php
declare(strict_types=1);

session_start();


$recipient = 'kontakt@startconnect.de';
$sender = 'kontakt@eure-domain.de';

$fields = [
    'first_name' => ['Vorname', 'text', 'given-name', 80, true],
    'last_name'  => ['Nachname', 'text', 'family-name', 80, true],
    'email'      => ['E-Mail', 'email', 'email', 254, true],
    'phone'      => ['Telefonnummer', 'tel', 'tel', 40, false],
    'subject'    => ['Betreff', 'text', 'off', 120, false],
    'message'    => ['Nachricht', 'textarea', 'off', 3000, true],
];

$form = array_fill_keys(array_keys($fields), '');
$errors = [];
$formError = '';
$sent = ($_SESSION['contact_sent'] ?? false) === true;

unset($_SESSION['contact_sent']);
$_SESSION['contact_token'] ??= bin2hex(random_bytes(32));

function contactEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['contact_token'], $token)) {
        $formError = 'Das Formular ist abgelaufen. Bitte lade die Seite neu.';
    }

    foreach ($fields as $name => [$label, $type, $autocomplete, $maxLength, $required]) {
        $value = $_POST[$name] ?? '';
        $value = is_string($value) ? trim($value) : '';

        if (strlen($value) > $maxLength * 4) {
            $errors[$name] = 'Die Eingabe ist zu lang.';
            continue;
        }

        $form[$name] = $value;

        if ($required && $value === '') {
            $errors[$name] = 'Bitte fülle dieses Feld aus.';
        } elseif (preg_match('/\A.{0,' . $maxLength . '}\z/us', $value) !== 1) {
            $errors[$name] = 'Bitte kürze die Eingabe auf maximal ' . $maxLength . ' Zeichen.';
        }
    }

    if ($form['email'] !== '' && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Bitte gib eine gültige E-Mail-Adresse ein.';
    }

    if ($errors === [] && $formError === '') {
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)
            || !filter_var($sender, FILTER_VALIDATE_EMAIL)) {
            $formError = 'Der E-Mail-Versand ist noch nicht eingerichtet.';
        } else {
            $subject = $form['subject'] !== '' ? $form['subject'] : 'Allgemeine Anfrage';
            $phone = $form['phone'] !== '' ? $form['phone'] : 'Keine Angabe';

            $body = implode("\n", [
                'Neue Anfrage über StartConnect',
                '',
                'Vorname: ' . $form['first_name'],
                'Nachname: ' . $form['last_name'],
                'E-Mail: ' . $form['email'],
                'Telefon: ' . $phone,
                'Betreff: ' . $subject,
                '',
                $form['message'],
            ]);

            $headers = [
                'From' => $sender,
                'Reply-To' => $form['email'],
                'Content-Type' => 'text/plain; charset=UTF-8',
            ];

            if (mail($recipient, 'Neue Kontaktanfrage - StartConnect', $body, $headers)) {
                $_SESSION['contact_sent'] = true;
                $_SESSION['contact_token'] = bin2hex(random_bytes(32));
                header('Location: kontakt.php#kontaktformular', true, 303);
                exit;
            }

            $formError = 'Deine Nachricht konnte nicht gesendet werden. Bitte versuche es später erneut.';
        }
    }
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Frag ein kostenloses Erstgespräch mit dem StartConnect-Team an. Für Gründerteams und Investoren.">
    <title>Kontakt · StartConnect</title>

    <link rel="stylesheet" href="index.css">

    <style>
        .contact-hero {
            padding-block: clamp(3.25rem, 8vw, 6.5rem);
            background: radial-gradient(ellipse at 90% 15%, #295542, transparent 65%),
                        #102c24;
            color: #fffefa;
        }

        .contact-hero h1 {
            max-width: 17ch;
            font-size: clamp(2.75rem, 7vw, 5.5rem);
            text-wrap: balance;
        }

        .contact-hero h1 em { color: #dcfa87; }

        .contact-hero .contact-lead {
            max-width: 56ch;
            margin-top: 1rem;
            color: #e1eee6;
        }

        .contact-section { padding-block: clamp(2.75rem, 6vw, 5rem); }

        .contact-grid, .contact-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .contact-card, .contact-form {
            padding: clamp(1.2rem, 4vw, 2rem);
            border: 1px solid #d2ded3;
            border-radius: 1.25rem;
            background: #fffefa;
        }

        .contact-card h3 { font-size: 1.5rem; }
        .contact-card p { margin-top: .65rem; }
        .contact-card p, .contact-card li, .contact-note { color: #43594f; }

        .contact-card ul {
            margin: .85rem 0 0;
            padding-left: 1.2rem;
        }

        .contact-card li + li { margin-top: .3rem; }

        .contact-form-section { background: #eaf0e6; }
        .contact-form { max-width: 48rem; }
        .contact-note { max-width: 64ch; margin-top: .8rem; }

        .contact-field { display: grid; align-content: start; gap: .35rem; }
        .contact-wide { grid-column: 1 / -1; }
        .contact-field label { font-weight: 700; }
        .contact-field small { color: #43594f; font-weight: 400; }

        .contact-field input,
        .contact-field textarea {
            width: 100%;
            min-height: 2.85rem;
            padding: .65rem .8rem;
            border: 1px solid #81958a;
            border-radius: .55rem;
            background: #fff;
            color: #102c24;
            font: inherit;
        }

        .contact-field textarea { resize: vertical; }
        .contact-field [aria-invalid="true"] { border-color: #a42318; }

        .contact-feedback {
            max-width: 48rem;
            margin-top: 1rem;
            padding: .75rem 1rem;
            border-radius: .65rem;
            background: #fffefa;
        }

        .contact-error { color: #a42318; }
        .contact-success { color: #166534; }

        .site-header [aria-current="page"] {
            text-decoration: underline;
            text-underline-offset: .25em;
        }

        @media (max-width: 42rem) {
            .contact-grid, .contact-form { grid-template-columns: 1fr; }
        }
        .contact-card {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.contact-card ul {
    margin-bottom: 1.25rem;
}

.contact-email-button {
    margin-top: auto;
    max-width: 100%;
    white-space: normal;
}
    </style>
</head>
<body>
    <a class="skip-link" href="#inhalt">Zum Inhalt springen</a>

    <?php require __DIR__ . '/../includes/header.html'; ?>

    <main id="inhalt">
        <section class="contact-hero" aria-labelledby="kontakt-titel">
            <div class="container">
                <p class="eyebrow">Kontakt &amp; Beratung</p>
                <h1 id="kontakt-titel">Lass uns über deinen <em>nächsten Schritt</em> sprechen.</h1>
                <p class="contact-lead">
                    Ob du ein Startup aufbaust oder nach Projekten suchst:
                    Unsere Experten nehmen sich Zeit für deine Fragen.
                    Das Erstgespräch ist kostenlos und unverbindlich.
                </p>
            </div>
        </section>

        <section class="container contact-section" aria-labelledby="beratung-titel">
            <h2 id="beratung-titel">Kostenloses Erstgespräch</h2>

            <div class="contact-grid">
                <article class="contact-card">
                    <h3>Für Investoren</h3>
                    <p>Wir helfen dir, dich auf StartConnect zu orientieren und ein Gespräch mit einem Gründerteam vorzubereiten.</p>
                    <ul>
                        <li>Startups nach Branche und Finanzierungsphase einordnen</li>
                        <li>Unternehmensbewertung und Beteiligungsbegriffe verstehen</li>
                        <li>Fragen für das erste Gespräch mit dem Team vorbereiten</li>
                    </ul>
                    <a class="button button-dark contact-email-button"
                       href="<?= contactEscape('mailto:' . $recipient . '?subject=' . rawurlencode('Kostenloses Erstgespräch für Investoren')) ?>">
                       E-Mail als Investor schreiben <span aria-hidden="true">↗</span>
                    </a>
                </article>

                <article class="contact-card">
                    <h3>Für Startups</h3>
                    <p>Wir helfen dir, deine Geschäftsidee klar vorzustellen und dich auf Gespräche mit Investoren vorzubereiten.</p>
                    <ul>
                        <li>Geschäftsidee und Team verständlich präsentieren</li>
                        <li>Finanzierungsbedarf und Mittelverwendung erklären</li>
                        <li>Startup-Profil und Pitch vorbereiten</li>
                    </ul>
                    <a class="button button-dark contact-email-button"
                    href="<?= contactEscape('mailto:' . $recipient . '?subject=' . rawurlencode('Kostenloses Erstgespräch für Startups')) ?>">
                    E-Mail als Gründer schreiben <span aria-hidden="true">↗</span>
                   </a>
                </article>
            </div>
        </section>

        <section class="contact-form-section" id="kontaktformular" aria-labelledby="formular-titel">
            <div class="container contact-section">
                <h2 id="formular-titel">Schreib uns</h2>
                <p class="contact-note">
                    Sag uns kurz, welches Thema du besprechen möchtest.
                    Wir melden uns, um einen Termin zu vereinbaren. Felder mit * sind Pflichtfelder.
                </p>

                <?php if ($sent): ?>
                    <p class="contact-feedback contact-success" role="status">
                        Danke! Deine Nachricht wurde zum Versand angenommen.
                    </p>
                <?php elseif ($formError !== '' || $errors !== []): ?>
                    <p class="contact-feedback contact-error" role="alert">
                        <?= contactEscape($formError !== '' ? $formError : 'Bitte prüfe die markierten Felder.') ?>
                    </p>
                <?php endif; ?>

                <form class="contact-form" method="post" action="kontakt.php#kontaktformular">
                    <input type="hidden" name="token" value="<?= contactEscape($_SESSION['contact_token']) ?>">

                    <?php foreach ($fields as $name => [$label, $type, $autocomplete, $maxLength, $required]): ?>
                        <?php
                        $wide = in_array($name, ['subject', 'message'], true);
                        $invalid = isset($errors[$name]);
                        ?>

                        <div class="contact-field<?= $wide ? ' contact-wide' : '' ?>">
                            <label for="<?= $name ?>">
                                <?= $label ?>
                                <?php if ($required): ?>
                                    <span aria-hidden="true">*</span>
                                <?php else: ?>
                                    <small>(optional)</small>
                                <?php endif; ?>
                            </label>

                            <?php if ($type === 'textarea'): ?>
                                <textarea id="<?= $name ?>" name="<?= $name ?>" rows="6"
                                          maxlength="<?= $maxLength ?>" required
                                          <?= $invalid ? 'aria-invalid="true" aria-describedby="' . $name . '-error"' : '' ?>><?= contactEscape($form[$name]) ?></textarea>
                            <?php else: ?>
                                <input id="<?= $name ?>" name="<?= $name ?>" type="<?= $type ?>"
                                       autocomplete="<?= $autocomplete ?>" maxlength="<?= $maxLength ?>"
                                       value="<?= contactEscape($form[$name]) ?>"
                                       <?= $required ? 'required' : '' ?>
                                       <?= $invalid ? 'aria-invalid="true" aria-describedby="' . $name . '-error"' : '' ?>>
                            <?php endif; ?>

                            <?php if ($invalid): ?>
                                <p id="<?= $name ?>-error" class="contact-error">
                                    <?= contactEscape($errors[$name]) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <div class="contact-wide">
                        <button class="button button-dark" type="submit">
                            Nachricht senden <span aria-hidden="true">↗</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.html'; ?>
</body>
</html>