<?php
declare(strict_types=1);

require_once __DIR__ . '/../repositories/ContactMessageRepo.php';

final class ContactMessageService
{
    public function __construct(
        private readonly ContactMessageRepo $repo
    ) {}

    /**
     * @param array<string, string> $form
     * @param array<string, string> $errors
     * @return array{ok: bool, id: ?int, error: string}
     */
    public function submitContactForm(
        array $form,
        array $errors,
        ?int $userId = null,
        ?int $companyId = null
    ): array {
        if ($errors !== []) {
            return [
                'ok'    => false,
                'id'    => null,
                'error' => 'Bitte prüfe die markierten Felder.',
            ];
        }

        $firstName = trim($form['first_name'] ?? '');
        $lastName  = trim($form['last_name'] ?? '');
        $fullName  = mb_substr(trim($firstName . ' ' . $lastName), 0, 150, 'UTF-8');

        $email   = mb_substr(trim($form['email'] ?? ''), 0, 190, 'UTF-8');
        $phone   = trim($form['phone'] ?? '');
        $subject = trim($form['subject'] ?? '');
        $subject = $subject !== '' ? $subject : 'Allgemeine Anfrage';
        $subject = mb_substr($subject, 0, 200, 'UTF-8');

        $messageBody = trim($form['message'] ?? '');
        if ($phone !== '') {
            $messageBody .= "\n\nTelefonnummer: " . $phone;
        }

        try {
            $id = $this->repo->create([
                'user_id'    => $userId,
                'company_id' => $companyId,
                'name'       => $fullName,
                'email'      => $email,
                'subject'    => $subject,
                'message'    => $messageBody,
                'status'     => 'open',
            ]);

            return [
                'ok'    => true,
                'id'    => $id,
                'error' => '',
            ];
        } catch (Throwable) {
            return [
                'ok'    => false,
                'id'    => null,
                'error' => 'Deine Nachricht konnte nicht gespeichert werden. Bitte versuche es später erneut.',
            ];
        }
    }
}