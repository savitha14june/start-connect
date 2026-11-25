<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id'], $_SESSION['user_role'])) {
    header('Location: /start-connect/public/login.php');
    exit;
}

if ($_SESSION['user_role'] !== 'founder') {
    header('Location: /start-connect/public/index.php');
    exit;
}

$pdo = require __DIR__ . '/../config/db.php';

$founderId = (int) $_SESSION['user_id'];
$founderName = (string) ($_SESSION['user_name'] ?? 'Gründer/in');

// جلب الشركات المملوكة لهذا المؤسس
$stmtCompanies = $pdo->prepare(
    'SELECT id, name, tagline, category, funding_goal, raised_amount, status, created_at
     FROM companies
     WHERE owner_user_id = :owner_user_id
     ORDER BY created_at DESC'
);
$stmtCompanies->execute(['owner_user_id' => $founderId]);
$companies = $stmtCompanies->fetchAll();

// جلب رسائل وطلبات التواصل المرتبطة بشركات هذا المؤسس
$stmtMessages = $pdo->prepare(
    'SELECT cm.id, cm.name AS sender_name, cm.email AS sender_email, cm.subject,
            cm.message, cm.status, cm.created_at, c.name AS company_name
     FROM contact_messages cm
     INNER JOIN companies c ON c.id = cm.company_id
     WHERE c.owner_user_id = :owner_user_id
     ORDER BY cm.created_at DESC'
);
$stmtMessages->execute(['owner_user_id' => $founderId]);
$messages = $stmtMessages->fetchAll();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Founder Dashboard – Start-Connect</title>
    <link rel="stylesheet" href="/start-connect/public/assets/css/style.css">
</head>
<body>
    <main class="dashboard-container">
        <header class="dashboard-header">
            <h1>Founder Dashboard</h1>
            <p>Willkommen, <?= htmlspecialchars($founderName, ENT_QUOTES, 'UTF-8') ?>!</p>
            <nav>
                <a href="/start-connect/public/index.php">Startseite</a>
                <a href="/start-connect/public/login.php">Login-Seite</a>
            </nav>
        </header>

        <section class="dashboard-section">
            <h2>Meine Startups (<?= count($companies) ?>)</h2>

            <?php if (empty($companies)): ?>
                <p>Es sind noch keine Unternehmen für Ihr Konto hinterlegt.</p>
            <?php else: ?>
                <table border="1" cellpadding="8" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Kategorie</th>
                            <th>Finanzierungsziel</th>
                            <th>Bereits eingesammelt</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companies as $company): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $company['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $company['category'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= number_format((float) ($company['funding_goal'] ?? 0), 2, ',', '.') ?> €</td>
                                <td><?= number_format((float) ($company['raised_amount'] ?? 0), 2, ',', '.') ?> €</td>
                                <td><?= htmlspecialchars((string) $company['status'], ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <section class="dashboard-section">
            <h2>Eingehende Kontaktanfragen / Nachrichten (<?= count($messages) ?>)</h2>

            <?php if (empty($messages)): ?>
                <p>Aktuell liegen keine Nachrichten für Ihre Startups vor.</p>
            <?php else: ?>
                <table border="1" cellpadding="8" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Startup</th>
                            <th>Absender</th>
                            <th>Betreff</th>
                            <th>Nachricht</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $msg['company_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?= htmlspecialchars((string) $msg['sender_name'], ENT_QUOTES, 'UTF-8') ?><br>
                                    <small><?= htmlspecialchars((string) $msg['sender_email'], ENT_QUOTES, 'UTF-8') ?></small>
                                </td>
                                <td><?= htmlspecialchars((string) $msg['subject'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $msg['message'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $msg['status'], ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>