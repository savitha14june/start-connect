<?php
declare(strict_types=1);

require_once __DIR__ . '/../repositories/CompanySignupRepo.php';

final class CompanySignupService
{
    public function __construct(
        private readonly CompanySignupRepo $repo
    ) {}

    /**
     * @param array<string, string> $input
     * @return array{ok: bool, errors: array<string, string>, formError: string, userId: ?int, companyId: ?int}
     */
    public function register(array $input): array
    {
        $errors = [];

        $founderName     = trim($input['founder_name'] ?? '');
        $email           = strtolower(trim($input['email'] ?? ''));
        $password        = (string) ($input['password'] ?? '');
        $passwordConfirm = (string) ($input['password_confirm'] ?? '');
        $phone           = trim($input['telephone'] ?? '');
        $bio             = trim($input['profile_bio'] ?? '');

        $companyName     = trim($input['company_name'] ?? '');
        $tagline         = trim($input['tagline'] ?? '');
        $description     = trim($input['description'] ?? '');
        $category        = trim($input['category'] ?? '');
        $address         = trim($input['address'] ?? '');
        $taxNumber       = trim($input['tax_number'] ?? '');
        $valuationRaw    = trim($input['company_valuation'] ?? '');
        $fundingGoalRaw  = trim($input['funding_goal'] ?? '');
        $minInvestRaw    = trim($input['minimum_investment'] ?? '');
        $returnPctRaw    = trim($input['profit_return_pct'] ?? '');
        $iban            = strtoupper(preg_replace('/\s+/', '', trim($input['iban'] ?? '')) ?? '');
        $accountHolder   = trim($input['account_holder'] ?? '');

        if ($founderName === '') {
            $errors['founder_name'] = 'Bitte gib deinen Namen an.';
        } elseif (mb_strlen($founderName, 'UTF-8') > 150) {
            $errors['founder_name'] = 'Maximal 150 Zeichen erlaubt.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Bitte gib eine gültige E-Mail-Adresse ein.';
        } elseif (mb_strlen($email, 'UTF-8') > 190) {
            $errors['email'] = 'Maximal 190 Zeichen erlaubt.';
        }

        if (strlen($password) < 8) {
            $errors['password'] = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Die Passwörter stimmen nicht überein.';
        }

        if ($companyName === '') {
            $errors['company_name'] = 'Bitte gib den Unternehmensnamen an.';
        } elseif (mb_strlen($companyName, 'UTF-8') > 150) {
            $errors['company_name'] = 'Maximal 150 Zeichen erlaubt.';
        }

        if ($tagline === '') {
            $errors['tagline'] = 'Bitte gib eine kurze Kernaussage (Tagline) an.';
        } elseif (mb_strlen($tagline, 'UTF-8') > 255) {
            $errors['tagline'] = 'Maximal 255 Zeichen erlaubt.';
        }

        if ($description === '') {
            $errors['description'] = 'Bitte beschreibe euer Startup kurz.';
        }

        if ($category === '') {
            $errors['category'] = 'Bitte gib eine Branche an.';
        } elseif (mb_strlen($category, 'UTF-8') > 100) {
            $errors['category'] = 'Maximal 100 Zeichen erlaubt.';
        }

        $valuation = $this->parseDecimal($valuationRaw, 'company_valuation', $errors);
        $fundingGoal = $this->parseDecimal($fundingGoalRaw, 'funding_goal', $errors);
        $minInvestment = $this->parseDecimal($minInvestRaw, 'minimum_investment', $errors);
        $profitReturnPct = $this->parseDecimal($returnPctRaw, 'profit_return_pct', $errors);

        if ($errors !== []) {
            return [
                'ok'        => false,
                'errors'    => $errors,
                'formError' => 'Bitte prüfe die markierten Felder.',
                'userId'    => null,
                'companyId' => null,
            ];
        }

        try {
            if ($this->repo->emailExists($email)) {
                return [
                    'ok'        => false,
                    'errors'    => ['email' => 'Diese E-Mail-Adresse ist bereits registriert.'],
                    'formError' => 'Bitte prüfe die markierten Felder.',
                    'userId'    => null,
                    'companyId' => null,
                ];
            }

            $created = $this->repo->createFounderAndCompany(
                [
                    'name'          => $founderName,
                    'email'         => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'role'          => 'founder',
                    'address'       => $address !== '' ? mb_substr($address, 0, 255, 'UTF-8') : null,
                    'telephone'     => $phone !== '' ? mb_substr($phone, 0, 50, 'UTF-8') : null,
                    'profile_bio'   => $bio !== '' ? $bio : null,
                ],
                [
                    'name'               => $companyName,
                    'tagline'            => $tagline,
                    'description'        => $description,
                    'category'           => $category,
                    'address'            => $address !== '' ? mb_substr($address, 0, 255, 'UTF-8') : null,
                    'telephone'          => $phone !== '' ? mb_substr($phone, 0, 50, 'UTF-8') : null,
                    'tax_number'         => $taxNumber !== '' ? mb_substr($taxNumber, 0, 100, 'UTF-8') : null,
                    'company_valuation'  => $valuation,
                    'funding_goal'       => $fundingGoal,
                    'minimum_investment' => $minInvestment,
                    'profit_return_pct'  => $profitReturnPct,
                    'iban'               => $iban !== '' ? mb_substr($iban, 0, 50, 'UTF-8') : null,
                    'account_holder'     => $accountHolder !== '' ? mb_substr($accountHolder, 0, 150, 'UTF-8') : ($founderName !== '' ? $founderName : null),
                    'status'             => 'pending',
                ]
            );

            return [
                'ok'        => true,
                'errors'    => [],
                'formError' => '',
                'userId'    => $created['user_id'],
                'companyId' => $created['company_id'],
            ];
        } catch (Throwable) {
            return [
                'ok'        => false,
                'errors'    => [],
                'formError' => 'Die Registrierung konnte nicht abgeschlossen werden. Bitte versuche es später erneut.',
                'userId'    => null,
                'companyId' => null,
            ];
        }
    }

    /**
     * @param array<string, string> $errors
     */
    private function parseDecimal(string $raw, string $field, array &$errors): ?string
    {
        if ($raw === '') {
            return null;
        }

        $normalized = str_replace([' ', ','], ['', '.'], $raw);
        if (!is_numeric($normalized) || (float) $normalized < 0) {
            $errors[$field] = 'Bitte gib einen gültigen positiven Wert ein.';
            return null;
        }

        return number_format((float) $normalized, 2, '.', '');
    }
}