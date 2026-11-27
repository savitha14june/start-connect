<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';

$investorId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($investorId === false) {
    header('Location: login.php', true, 302);
    exit;
}

if (($_SESSION['user_role'] ?? '') !== 'investor') {
    http_response_code(403);
    echo 'Dieses Dashboard ist für Investor-Konten. <a href="index.php">Zur Startseite</a>';
    exit;
}

header('Cache-Control: private, no-store');

function dashboardEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function dashboardEuro(string|int|float $value): string
{
    return number_format((float) $value, 2, ',', '.') . ' €';
}

function dashboardQuery(PDO $pdo, string $sql, array $params): PDOStatement
{
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    return $statement;
}

$e = 'dashboardEscape';
$search = $_GET['suche'] ?? '';
$search = is_string($search) && strlen($search) <= 400 ? trim($search) : '';
$category = $_GET['branche'] ?? '';
$category = is_string($category) ? $category : '';
$status = $_GET['status'] ?? '';
$status = is_string($status) ? $status : '';

$statusLabels = ['approved' => 'Bestätigt', 'pending' => 'Ausstehend', 'rejected' => 'Abgelehnt'];
$projects = $favorites = $recommendations = $categories = [];
$summary = ['approved_total' => '0', 'pending_total' => '0', 'approved_count' => 0];
$loadError = false;

try {
    $pdo = require __DIR__ . '/../config/db.php';

    $summary = dashboardQuery($pdo, <<<'SQL'
        SELECT
            COALESCE(SUM(CASE WHEN status = 'approved' THEN investment_amount ELSE 0 END), 0) AS approved_total,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN investment_amount ELSE 0 END), 0) AS pending_total,
            COUNT(DISTINCT CASE WHEN status = 'approved' THEN company_id END) AS approved_count
        FROM investments
        WHERE investor_user_id = :investor
        SQL, ['investor' => $investorId])->fetch(PDO::FETCH_ASSOC);

    $categories = dashboardQuery($pdo, <<<'SQL'
        SELECT DISTINCT c.category
        FROM investments i
        JOIN companies c ON c.id = i.company_id
        WHERE i.investor_user_id = :investor
        ORDER BY c.category
        SQL, ['investor' => $investorId])->fetchAll(PDO::FETCH_COLUMN);

    $category = in_array($category, $categories, true) ? $category : '';
    $status = isset($statusLabels[$status]) ? $status : '';

    $conditions = ['i.investor_user_id = :investor'];
    $params = ['investor' => $investorId];
    if ($search !== '') {
        $conditions[] = 'LOCATE(LOWER(:search), LOWER(c.name)) > 0';
        $params['search'] = $search;
    }
    if ($category !== '') {
        $conditions[] = 'c.category = :category';
        $params['category'] = $category;
    }

    $sql = <<<'SQL'
        SELECT c.id, c.name, c.category,
            SUM(CASE WHEN i.status = 'approved' THEN i.investment_amount ELSE 0 END) AS approved_amount,
            SUM(CASE WHEN i.status = 'pending' THEN i.investment_amount ELSE 0 END) AS pending_amount,
            SUM(CASE WHEN i.status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count,
            COUNT(*) AS request_count,
            MAX(i.created_at) AS latest
        FROM investments i
        JOIN companies c ON c.id = i.company_id
        SQL;
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
    $sql .= ' GROUP BY c.id, c.name, c.category';
    if ($status !== '') {
        $sql .= ' HAVING SUM(CASE WHEN i.status = :status THEN 1 ELSE 0 END) > 0';
        $params['status'] = $status;
    }
    $sql .= ' ORDER BY latest DESC, c.id DESC';
    $projects = dashboardQuery($pdo, $sql, $params)->fetchAll(PDO::FETCH_ASSOC);

    $favorites = dashboardQuery($pdo, <<<'SQL'
        SELECT c.id, c.name, c.tagline, c.category
        FROM favorite_companies f
        JOIN companies c ON c.id = f.company_id
        WHERE f.user_id = :favorite_user AND c.status = 'approved'
          AND NOT EXISTS (
            SELECT 1 FROM investments i
            WHERE i.company_id = c.id AND i.investor_user_id = :investment_user
              AND i.status IN ('pending', 'approved')
          )
        ORDER BY f.created_at DESC, c.id DESC
        SQL, ['favorite_user' => $investorId, 'investment_user' => $investorId])->fetchAll(PDO::FETCH_ASSOC);

    $recommendations = dashboardQuery($pdo, <<<'SQL'
        SELECT c.id, c.name, c.tagline, c.category,
            EXISTS (
                SELECT 1 FROM favorite_companies f
                JOIN companies preferred ON preferred.id = f.company_id
                WHERE f.user_id = :interest_user AND preferred.category = c.category
            ) OR EXISTS (
                SELECT 1 FROM investments i
                JOIN companies preferred ON preferred.id = i.company_id
                WHERE i.investor_user_id = :portfolio_user AND i.status IN ('pending', 'approved')
                  AND preferred.category = c.category
            ) AS matching_category
        FROM companies c
        WHERE c.status = 'approved'
          AND NOT EXISTS (
              SELECT 1 FROM investments i
              WHERE i.company_id = c.id AND i.investor_user_id = :exclude_investments
          )
          AND NOT EXISTS (
              SELECT 1 FROM favorite_companies f
              WHERE f.company_id = c.id AND f.user_id = :exclude_favorites
          )
        ORDER BY matching_category DESC, c.created_at DESC, c.id DESC
        LIMIT 6
        SQL, [
            'interest_user' => $investorId, 'portfolio_user' => $investorId,
            'exclude_investments' => $investorId, 'exclude_favorites' => $investorId,
        ])->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    error_log('Investor dashboard: ' . $error->getMessage());
    http_response_code(503);
    $loadError = true;
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Investoren-Dashboard · StartConnect</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <a class="skip-link" href="#inhalt">Zum Inhalt springen</a>
    <?php require __DIR__ . '/../includes/header.php'; ?>
    <main id="inhalt" class="idash">
        <div class="idash-hero">
            <div class="container">
                <h1>Investoren-Dashboard</h1>
                <p>Deine Investitionen, gespeicherten Startups und neue Profile auf einen Blick.</p>
            </div>
        </div>

        <div class="container idash-main">
            <?php if ($loadError): ?>
                <p class="idash-section idash-error" role="alert">Dein Dashboard konnte gerade nicht geladen werden. Bitte versuche es später erneut.</p>
            <?php else: ?>
                <dl class="idash-stats" aria-label="Übersicht deiner Investitionen">
                    <div class="idash-stat"><dt>Bestätigte Investitionen</dt><dd><?= dashboardEuro($summary['approved_total']) ?></dd></div>
                    <div class="idash-stat"><dt>Startups mit bestätigter Investition</dt><dd><?= (int) $summary['approved_count'] ?></dd></div>
                    <div class="idash-stat"><dt>Ausstehende Anträge</dt><dd><?= dashboardEuro($summary['pending_total']) ?></dd></div>
                </dl>

                <section class="idash-section" id="portfolio" aria-labelledby="portfolio-title">
                    <h2 id="portfolio-title">Meine Investitionen</h2>
                    <p>Die Beträge sind pro Startup zusammengefasst. Die Kennzahlen oben zeigen dein gesamtes Portfolio.</p>
                    <div class="idash-panel">
                        <form class="idash-filter" method="get" action="dashboard-investoren.php#portfolio">
                            <label>Startup suchen
                                <input type="search" name="suche" value="<?= $e($search) ?>" maxlength="100" placeholder="Name eingeben">
                            </label>
                            <label>Branche
                                <select name="branche">
                                    <option value="">Alle Branchen</option>
                                    <?php foreach ($categories as $option): ?>
                                        <option value="<?= $e($option) ?>"<?= $category === $option ? ' selected' : '' ?>><?= $e($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label>Status
                                <select name="status">
                                    <option value="">Alle Status</option>
                                    <?php foreach ($statusLabels as $key => $label): ?>
                                        <option value="<?= $e($key) ?>"<?= $status === $key ? ' selected' : '' ?>><?= $e($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <button class="button button-dark" type="submit">Filtern</button>
                        </form>

                        <?php if ($search !== '' || $category !== '' || $status !== ''): ?>
                            <p><a href="dashboard-investoren.php#portfolio">Filter zurücksetzen</a></p>
                        <?php endif; ?>

                        <?php if ($projects === []): ?>
                            <p>Für diese Auswahl gibt es keine Einträge.</p>
                        <?php else: ?>
                            <div class="idash-table-wrap" tabindex="0" role="region" aria-label="Tabelle der Investitionen">
                                <table class="idash-table">
                                    <thead><tr>
                                        <th scope="col">Startup</th><th scope="col">Branche</th>
                                        <th scope="col">Bestätigt</th><th scope="col">Ausstehend</th>
                                        <th scope="col">Anträge</th>
                                    </tr></thead>
                                    <tbody>
                                    <?php foreach ($projects as $project): ?>
                                        <tr>
                                            <th scope="row"><a href="company-details.php?id=<?= (int) $project['id'] ?>"><?= $e($project['name']) ?></a></th>
                                            <td><?= $e($project['category']) ?></td>
                                            <td class="idash-money"><?= dashboardEuro($project['approved_amount']) ?></td>
                                            <td class="idash-money"><?= dashboardEuro($project['pending_amount']) ?></td>
                                            <td>
                                                <?= (int) $project['request_count'] ?> gesamt
                                                <?php if ((int) $project['rejected_count'] > 0): ?>
                                                    · <?= (int) $project['rejected_count'] ?> abgelehnt
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <?php foreach ([
                    ['section' => 'merkliste', 'title' => 'Meine Merkliste', 'description' => 'Gespeicherte Startups ohne bestätigte Investition oder offenen Antrag.', 'items' => $favorites, 'empty' => 'Hier erscheinen die passenden Startups, die du gespeichert hast.'],
                    ['section' => 'entdecken', 'title' => 'Neue Startups entdecken', 'description' => 'Profile aus deinen bisherigen Branchen stehen zuerst. Innerhalb der Gruppen erscheinen neuere Profile zuerst.', 'items' => $recommendations, 'empty' => 'Aktuell gibt es keine weiteren passenden Profile.'],
                ] as $group): ?>
                    <section class="idash-section" aria-labelledby="<?= $e($group['section']) ?>-title">
                        <h2 id="<?= $e($group['section']) ?>-title"><?= $e($group['title']) ?></h2>
                        <p><?= $e($group['description']) ?></p>
                        <?php if ($group['items'] === []): ?>
                            <div class="idash-panel">
                                <p><?= $e($group['empty']) ?></p>
                                <a href="index.php#startups">Startups ansehen</a>
                            </div>
                        <?php else: ?>
                            <div class="idash-cards">
                                <?php foreach ($group['items'] as $company): ?>
                                    <article class="idash-card">
                                        <span class="idash-tag"><?= $e($company['category']) ?></span>
                                        <h3><?= $e($company['name']) ?></h3>
                                        <p><?= $e($company['tagline']) ?></p>
                                        <a href="company-details.php?id=<?= (int) $company['id'] ?>">
                                            Profil ansehen <span class="visually-hidden" aria-hidden="true">↗</span>
                                        </a>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <?php require __DIR__ . '/../includes/footer.html'; ?>
</body>
</html>
