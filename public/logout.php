<?php

declare(strict_types=1);

session_start();

$isLoggedIn = isset($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Abmelden - StartConnect</title>

    <link rel="stylesheet" href="/start-connect/public/index.css">
</head>

<body>

    

    <main class="container">

        <section class="logout-page">

            <div class="logout-card">

                <div class="logout-icon">✓</div>

                <h1>Erfolgreich abgemeldet!</h1>

                <p>
                    Sie haben sich erfolgreich von Ihrem Konto abgemeldet.
                </p>

                <p>
                    Wir freuen uns, Sie bald wieder bei StartConnect begrüßen zu dürfen.
                </p>

                <a href="/start-connect/public/index.php"
                   class="button button-primary">
                    Zur Startseite
                </a>

                <a href="/start-connect/public/login.php"
                   class="button button-dark">
                    Wieder anmelden
                </a>

            </div>

        </section>

    </main>

</body>
</html>