<?php
declare(strict_types=1);

require_once __DIR__ . '/../repositories/StartupRepo.php';

final class StartupService
{
    public function __construct(private readonly StartupRepo $repo)
    {
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->repo->getApprovedCategories();
    }

    public function resolveSelectedCategory(mixed $rawFilter, array $availableCategories): string
    {
        if (!is_string($rawFilter)) {
            return '';
        }

        return in_array($rawFilter, $availableCategories, true) ? $rawFilter : '';
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getCompanyCards(?string $selectedCategory = null): array
    {
        $rows = $this->repo->findApprovedCompanies($selectedCategory);
        $cards = [];

        foreach ($rows as $row) {
            $name = (string) ($row['name'] ?? '');
            $description = trim((string) ($row['description'] ?? ''));
            $tagline = trim((string) ($row['tagline'] ?? ''));
            $summary = $description !== '' ? $description : $tagline;

            $founderName = trim((string) ($row['founder_name'] ?? ''));
            $founderBio = trim((string) ($row['founder_bio'] ?? ''));
            $team = $founderName;
            if ($founderBio !== '') {
                $team .= ($team !== '' ? ' – ' : '') . $founderBio;
            }
            if ($team === '') {
                $team = 'Teamangaben folgen.';
            }

            $cards[] = [
                'id'       => (string) ($row['id'] ?? ''),
                'name'     => $name,
                'initials' => $this->makeInitials($name),
                'category' => (string) ($row['category'] ?? ''),
                'address'  => (string) ($row['address'] ?? ''),
                'summary'  => $summary,
                'funding'  => $this->formatEur($row['funding_goal'] ?? null),
                'team'     => $team,
            ];
        }

        return $cards;
    }

    /**
     * @return array<string, string>|null
     */
    public function getCompanyDetails(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $row = $this->repo->findApprovedCompanyById($id);
        if ($row === null) {
            return null;
        }

        $name = (string) ($row['name'] ?? '');
        $founderName = trim((string) ($row['founder_name'] ?? ''));
        $founderBio = trim((string) ($row['founder_bio'] ?? ''));

        $goal = (float) ($row['funding_goal'] ?? 0);
        $raised = (float) ($row['raised_amount'] ?? 0);
        $progressPct = $goal > 0 ? min(100, (int) round(($raised / $goal) * 100)) : 0;

        $returnPct = $row['profit_return_pct'] !== null && $row['profit_return_pct'] !== ''
            ? number_format((float) $row['profit_return_pct'], 1, ',', '.') . ' %'
            : 'Auf Anfrage';

        return [
            'id'                 => (string) ($row['id'] ?? ''),
            'name'               => $name,
            'initials'           => $this->makeInitials($name),
            'tagline'            => trim((string) ($row['tagline'] ?? '')),
            'description'        => trim((string) ($row['description'] ?? '')),
            'category'           => (string) ($row['category'] ?? ''),
            'address'            => trim((string) ($row['address'] ?? '')) ?: 'Standort auf Anfrage',
            'telephone'          => trim((string) ($row['telephone'] ?? '')) ?: 'Nicht angegeben',
            'company_valuation'  => $this->formatEur($row['company_valuation'] ?? null),
            'funding_goal'       => $this->formatEur($row['funding_goal'] ?? null),
            'raised_amount'      => $this->formatEur($row['raised_amount'] ?? null),
            'minimum_investment' => $this->formatEur($row['minimum_investment'] ?? null),
            'profit_return_pct'  => $returnPct,
            'progress_pct'       => (string) $progressPct,
            'founder_name'       => $founderName !== '' ? $founderName : 'Gründerteam',
            'founder_bio'        => $founderBio !== '' ? $founderBio : 'Weitere Informationen zum Team folgen in Kürze.',
        ];
    }

    private function makeInitials(string $name): string
    {
        $parts = preg_split('/\s+/u', trim($name)) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            if ($part !== '') {
                $initials .= mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8');
            }
        }

        return $initials !== '' ? $initials : 'SC';
    }

    private function formatEur(mixed $amount): string
    {
        if ($amount === null || $amount === '') {
            return 'Auf Anfrage';
        }

        return number_format((float) $amount, 0, ',', '.') . ' €';
    }
}