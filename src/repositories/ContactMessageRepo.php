<?php
declare(strict_types=1);

require_once __DIR__ . '/../logger.php';

use App\Logger;

final class ContactMessageRepo
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly Logger $logger
    ) {}

    /**
     * @param array{
     *   user_id: ?int,
     *   company_id: ?int,
     *   name: string,
     *   email: string,
     *   subject: string,
     *   message: string,
     *   status: string
     * } $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO contact_messages (
                    user_id,
                    company_id,
                    name,
                    email,
                    subject,
                    message,
                    status
                ) VALUES (
                    :user_id,
                    :company_id,
                    :name,
                    :email,
                    :subject,
                    :message,
                    :status
                )';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'user_id'    => $data['user_id'],
                'company_id' => $data['company_id'],
                'name'       => $data['name'],
                'email'      => $data['email'],
                'subject'    => $data['subject'],
                'message'    => $data['message'],
                'status'     => $data['status'],
            ]);

            $id = (int) $this->pdo->lastInsertId();

            $this->logger->info('Saved contact message to database', [
                'contact_message_id' => $id,
                'email'              => $data['email'],
                'status'             => $data['status'],
            ]);

            return $id;
        } catch (Throwable $e) {
            $this->logger->error('Failed to save contact message', [
                'email'     => $data['email'],
                'exception' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}