<?php
declare(strict_types=1);

require_once __DIR__ . '/../repositories/InvestmentRepo.php';

final class InvestmentService
{
    public function __construct(
        private readonly InvestmentRepo $repo
    ) {}

    /**
     * @param array<string, string> $input
     * @param array<string, mixed> $company
     * @return array{ok: bool, errors: array<string, string>, formError: string, investmentId: ?int}
     */
    public function submitInvestment(
        array $input,
        array $company,
        ?int $investorUserId = null
    ): array {
        $errors = [];

        $companyId = (int) ($company['id'] ?? 0);
        $name = trim($input['investor_name'] ?? '');
        $email = strtolower(trim($input['investor_email'] ?? ''));
        $phone = trim($input['investor_phone'] ?? '');
        $address = trim($input['investor_address'] ?? '');
        $amountRaw = trim($input['investment_amount'] ?? '');
        $notes = trim($input['notes'] ?? '');

        if ($companyId <= 0) {
            return [
                'ok'           => false,
                'errors'       => [],
                'formError'    => 'Ungültiges Unternehmen ausgewählt.',
                'investmentId' => null,
            ];
        }

        if ($name === '') {
            $errors['investor_name'] = 'Bitte gib deinen vollständigen Namen an.';
        } elseif (mb_strlen($name, 'UTF-8') > 150) {
            $errors['investor_name'] = 'Maximal 150 Zeichen erlaubt.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['investor_email'] = 'Bitte gib eine gültige E-Mail-Adresse ein.';
        } elseif (mb_strlen($email, 'UTF-8') > 190) {
            $errors['investor_email'] = 'Maximal 190 Zeichen erlaubt.';
        }

        if ($phone !== '' && mb_strlen($phone, 'UTF-8') > 50) {
            $errors['investor_phone'] = 'Maximal 50 Zeichen erlaubt.';
        }

        if ($address !== '' && mb_strlen($address, 'UTF-8') > 255) {
            $errors['investor_address'] = 'Maximal 255 Zeichen erlaubt.';
        }

        $normalizedAmount = str_replace([' ', ','], ['', '.'], $amountRaw);
        $amountFloat = is_numeric($normalizedAmount) ? (float) $normalizedAmount : 0.0;
        $minInvest = isset($company['minimum_investment']) && $company['minimum_investment'] !== null && $company['minimum_investment'] !== ''
            ? (float) $company['minimum_investment']
            : 0.0;

        if ($amountRaw === '' || !is_numeric($normalizedAmount) || $amountFloat <= 0) {
            $errors['investment_amount'] = 'Bitte gib einen gültigen Betrag größer als 0 € an.';
        } elseif ($amountFloat > 999999999.99) {
            $errors['investment_amount'] = 'Der Betrag ist zu hoch.';
        } elseif ($minInvest > 0 && $amountFloat < $minInvest) {
            $errors['investment_amount'] = 'Der Betrag liegt unter dem Mindestinvestment von '
                . number_format($minInvest, 0, ',', '.') . ' €.';
        }

        if ($notes !== '' && mb_strlen($notes, 'UTF-8') > 2000) {
            $errors['notes'] = 'Maximal 2000 Zeichen erlaubt.';
        }

        if ($errors !== []) {
            return [
                'ok'           => false,
                'errors'       => $errors,
                'formError'    => 'Bitte prüfe die markierten Felder.',
                'investmentId' => null,
            ];
        }

        try {
            $investmentId = $this->repo->create([
                'company_id'        => $companyId,
                'investor_user_id'  => $investorUserId,
                'investor_name'     => $name,
                'investor_email'    => $email,
                'investor_phone'    => $phone !== '' ? $phone : null,
                'investor_address'  => $address !== '' ? $address : null,
                'investment_amount' => number_format($amountFloat, 2, '.', ''),
                'notes'             => $notes !== '' ? $notes : null,
                'status'            => 'pending',
            ]);

            return [
                'ok'           => true,
                'errors'       => [],
                'formError'    => '',
                'investmentId' => $investmentId,
            ];
        } catch (Throwable) {
            return [
                'ok'           => false,
                'errors'       => [],
                'formError'    => 'Deine Investitionsanfrage konnte nicht gespeichert werden. Bitte versuche es später erneut.',
                'investmentId' => null,
            ];
        }
    }
}