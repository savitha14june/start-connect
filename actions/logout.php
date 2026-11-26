<?php

declare(strict_types=1);

session_start();

// Session-Daten löschen
$_SESSION = [];

// Session-Cookie löschen
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Session beenden
session_destroy();

// Zur Abmeldeseite weiterleiten
header('Location: /start-connect/public/logout.php');
exit;