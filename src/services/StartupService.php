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
                'name' => $name,
                'initials' => $this->makeInitials($name),
                'category' => (string) ($row['category'] ?? ''),
                'address' => (string) ($row['address'] ?? ''),
                'summary' => $summary,
                'funding' => $this->formatEur($row['funding_goal'] ?? null),
                'team' => $team,
            ];
        }

        return $cards;
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