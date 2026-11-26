<?php
declare(strict_types=1);

require_once __DIR__ . '/../logger.php';

use App\Logger;

final class InvestmentRepo
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly Logger $logger
    ) {}

    /**
     * @param array{
     *   company_id: int,
     *   investor_user_id: ?int,
     *   investor_name: string,
     *   investor_email: string,
     *   investor_phone: ?string,
     *   investor_address: ?string,
     *   investment_amount: string,
     *   notes: ?string,
     *   status: string
     * } $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO investments (
                    company_id,
                    investor_user_id,
                    investor_name,
                    investor_email,
                    investor_phone,
                    investor_address,
                    investment_amount,
                    notes,
                    status
                ) VALUES (
                    :company_id,
                    :investor_user_id,
                    :investor_name,
                    :investor_email,
                    :investor_phone,
                    :investor_address,
                    :investment_amount,
                    :notes,
                    :status
                )';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'company_id'        => $data['company_id'],
                'investor_user_id'  => $data['investor_user_id'],
                'investor_name'     => $data['investor_name'],
                'investor_email'    => $data['investor_email'],
                'investor_phone'    => $data['investor_phone'],
                'investor_address'  => $data['investor_address'],
                'investment_amount' => $data['investment_amount'],
                'notes'             => $data['notes'],
                'status'            => $data['status'],
            ]);

            $id = (int) $this->pdo->lastInsertId();

            $this->logger->info('Saved investment interest', [
                'investment_id'     => $id,
                'company_id'        => $data['company_id'],
                'investor_email'    => $data['investor_email'],
                'investment_amount' => $data['investment_amount'],
            ]);

            return $id;
        } catch (Throwable $e) {
            $this->logger->error('Failed to save investment interest', [
                'company_id'     => $data['company_id'],
                'investor_email' => $data['investor_email'],
                'exception'      => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
