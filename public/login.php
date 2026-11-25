<?php

declare(strict_types=1);

session_start();


$loginError = $_SESSION['login_error'] ?? null;

// 
unset($_SESSION['login_error']);

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - StartConnect</title>

    <link rel="stylesheet" href="/start-connect/public/assets/css/style.css">
</head>

<body>

    <main class="login-container">

        <h1>Login</h1>

        <?php if ($loginError): ?>
            <div class="error-message">
                <?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="/start-connect/actions/login.php" method="POST">

            <div class="form-group">
                <label for="email">E-Mail-Adresse</label>

                <input
                    type="email"
                    id="email"
                    name="email"
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
                    required
                    autocomplete="off"
                >
            </div>

            <button type="submit">
                Einloggen
            </button>

        </form>

    </main>

</body>
</html>