<?php

declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';

$loginError = $_SESSION['login_error'] ?? null;

unset($_SESSION['login_error']);

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - StartConnect</title>

    <link rel="stylesheet" href="index.css">
</head>

<body>

    <main class="login-page">

        <div class="login-container">

            <div class="login-card">

                <div class="login-heading">

                     <a class="brand" href="index.php" aria-label="StartConnect – Startseite">
                      <span class="brand-icon" aria-hidden="true">↗</span>
                          StartConnect
                    </a>

                    <h1>Willkommen zurück!</h1>

                    <p>Melden Sie sich bei Ihrem Konto an.</p>

                </div>

                <?php if ($loginError): ?>
                    <div class="login-error" role="alert">
                        <?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form
                    class="login-form"
                    action="/start-connect/actions/login.php"
                    method="POST"
                >

                    <div class="form-group">

                        <label for="email">E-Mail-Adresse</label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="name@example.com"
                            required
                            autocomplete="email"
                        >

                    </div>

                    <div class="form-group">

                        <label for="password">Passwort</label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Passwort eingeben"
                            required
                            autocomplete="current-password"
                        >

                    </div>

                    <button type="submit" class="button button-primary">
                        Einloggen ↗
                    </button>

                </form>

                <div class="login-footer">
                    Noch kein Konto?
                    <a href="signup-company.php">Als Unternehmen registrieren</a>
                    <a href="signup-investor.php">Als Investor registrieren</a>
                        Jetzt registrieren
                    </a>
                </div>

            </div>

        </div>

    </main>

</body>
</html>