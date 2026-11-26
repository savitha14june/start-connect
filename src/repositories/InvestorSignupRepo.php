<?php
declare(strict_types=1);

require_once __DIR__ . '/../logger.php';

use App\Logger;

final class InvestorSignupRepo
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly Logger $logger
    ) {}

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * @param array{
     *   name: string,
     *   email: string,
     *   password_hash: string,
     *   role: string,
     *   address: ?string,
     *   telephone: ?string,
     *   profile_bio: ?string
     * } $data
     */
    public function createInvestor(array $data): int
    {
        $sql = 'INSERT INTO users (
                    name,
                    email,
                    password_hash,
                    role,
                    address,
                    telephone,
                    profile_bio
                ) VALUES (
                    :name,
                    :email,
                    :password_hash,
                    :role,
                    :address,
                    :telephone,
                    :profile_bio
                )';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'name'          => $data['name'],
                'email'         => $data['email'],
                'password_hash' => $data['password_hash'],
                'role'          => $data['role'],
                'address'       => $data['address'],
                'telephone'     => $data['telephone'],
                'profile_bio'   => $data['profile_bio'],
            ]);

            $id = (int) $this->pdo->lastInsertId();

            $this->logger->info('Registered investor account', [
                'user_id' => $id,
                'email'   => $data['email'],
            ]);

            return $id;
        } catch (Throwable $e) {
            $this->logger->error('Failed to register investor account', [
                'email'     => $data['email'],
                'exception' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}