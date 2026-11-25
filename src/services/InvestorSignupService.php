<?php
declare(strict_types=1);

require_once __DIR__ . '/../repositories/InvestorSignupRepo.php';

final class InvestorSignupService
{
    public function __construct(
        private readonly InvestorSignupRepo $repo
    ) {}

    /**
     * @param array<string, string> $input
     * @return array{ok: bool, errors: array<string, string>, formError: string, userId: ?int}
     */
    public function register(array $input): array
    {
        $errors = [];

        $name            = trim($input['name'] ?? '');
        $email           = strtolower(trim($input['email'] ?? ''));
        $password        = (string) ($input['password'] ?? '');
        $passwordConfirm = (string) ($input['password_confirm'] ?? '');
        $address         = trim($input['address'] ?? '');
        $telephone       = trim($input['telephone'] ?? '');
        $profileBio      = trim($input['profile_bio'] ?? '');

        if ($name === '') {
            $errors['name'] = 'Bitte gib deinen Namen an.';
        } elseif (mb_strlen($name, 'UTF-8') > 150) {
            $errors['name'] = 'Maximal 150 Zeichen erlaubt.';
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

        if ($errors !== []) {
            return [
                'ok'        => false,
                'errors'    => $errors,
                'formError' => 'Bitte prüfe die markierten Felder.',
                'userId'    => null,
            ];
        }

        try {
            if ($this->repo->emailExists($email)) {
                return [
                    'ok'        => false,
                    'errors'    => ['email' => 'Diese E-Mail-Adresse ist bereits registriert.'],
                    'formError' => 'Bitte prüfe die markierten Felder.',
                    'userId'    => null,
                ];
            }

            $userId = $this->repo->createInvestor([
                'name'          => $name,
                'email'         => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role'          => 'investor',
                'address'       => $address !== '' ? mb_substr($address, 0, 255, 'UTF-8') : null,
                'telephone'     => $telephone !== '' ? mb_substr($telephone, 0, 50, 'UTF-8') : null,
                'profile_bio'   => $profileBio !== '' ? $profileBio : null,
            ]);

            return [
                'ok'        => true,
                'errors'    => [],
                'formError' => '',
                'userId'    => $userId,
            ];
        } catch (Throwable) {
            return [
                'ok'        => false,
                'errors'    => [],
                'formError' => 'Die Registrierung konnte nicht abgeschlossen werden. Bitte versuche es später erneut.',
                'userId'    => null,
            ];
        }
    }
}