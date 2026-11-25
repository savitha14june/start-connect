<?php
declare(strict_types=1);

require_once __DIR__ . '/../logger.php';

use App\Logger;

final class StartupRepo
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly Logger $logger
    ) {}

    /**
     * @return string[]
     */
    public function getApprovedCategories(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT DISTINCT category
                 FROM companies
                 WHERE category <> '' AND status = 'approved'
                 ORDER BY category ASC"
            );

            $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return is_array($categories) ? array_values(array_filter($categories, 'is_string')) : [];
        } catch (Throwable $e) {
            $this->logger->error('Failed to load company categories', [
                'exception' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findApprovedCompanies(?string $category = null): array
    {
        try {
            $sql = 'SELECT
                        c.id,
                        c.name,
                        c.tagline,
                        c.description,
                        c.category,
                        c.address,
                        c.funding_goal,
                        c.raised_amount,
                        c.profit_return_pct,
                        u.name AS founder_name,
                        u.profile_bio AS founder_bio
                    FROM companies c
                    LEFT JOIN users u ON u.id = c.owner_user_id
                    WHERE c.status = :status';

            $params = ['status' => 'approved'];

            if ($category !== null && $category !== '') {
                $sql .= ' AND c.category = :category';
                $params['category'] = $category;
            }

            $sql .= ' ORDER BY c.name ASC';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // $this->logger->info('Loaded approved companies', [
            //     'category' => $category ?? 'all',
            //     'count' => count($rows),
            // ]);

            return $rows;
        } catch (Throwable $e) {
            $this->logger->error('Failed to fetch approved companies', [
                'category' => $category ?? 'all',
                'exception' => $e->getMessage(),
            ]);
            return [];
        }
    }
}