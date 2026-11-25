<?php

declare(strict_types=1);

session_start();

// Nur POST-Anfragen erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /start-connect/public/login.php');
    exit;
}

// Formulardaten lesen
$email = trim((string) ($_POST['email'] ?? ''));
$loginPassword = (string) ($_POST['password'] ?? '');

// Eingaben prüfen
if ($email === '' || $loginPassword === '') {
    $_SESSION['login_error'] = 'Bitte E-Mail-Adresse und Passwort eingeben.';
    header('Location: /start-connect/public/login.php');
    exit;
}

try {
    // Datenbankverbindung
    $pdo = require __DIR__ . '/../config/db.php';

    // UserRepo laden
    require_once __DIR__ . '/../src/repositories/UserRepo.php';

    $userRepo = new UserRepo($pdo);

    // Benutzer anhand der E-Mail suchen
    $user = $userRepo->findByEmail($email);

    // Login prüfen
    if (!$user || $loginPassword !== (string) $user['password_hash']) {
        $_SESSION['login_error'] = 'E-Mail-Adresse oder Passwort ist ungültig.';
        header('Location: /start-connect/public/login.php');
        exit;
    }

    // Session-ID erneuern
    session_regenerate_id(true);

    // Benutzerdaten speichern
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = (string) $user['name'];
    $_SESSION['user_role'] = (string) $user['role'];

    // Weiterleitung nach Rolle
    $redirectUrl = match ($user['role']) {
        'founder' => '/start-connect/public/dashboard-founder.php',
        'admin'   => '/start-connect/public/dashboard-admin.php',
        default   => '/start-connect/public/startups.php',
    };

    header('Location: ' . $redirectUrl);
    exit;

} catch (Throwable $e) {
    error_log('Login error: ' . $e->getMessage());

    $_SESSION['login_error'] = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
    header('Location: /start-connect/public/login.php');
    exit;
}