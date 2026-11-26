<?php
declare(strict_types=1);

require_once __DIR__ . '/../logger.php';

use App\Logger;

final class CompanySignupRepo
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
     * } $userData
     * @param array{
     *   name: string,
     *   tagline: string,
     *   description: string,
     *   category: string,
     *   address: ?string,
     *   telephone: ?string,
     *   tax_number: ?string,
     *   company_valuation: ?string,
     *   funding_goal: ?string,
     *   minimum_investment: ?string,
     *   profit_return_pct: ?string,
     *   iban: ?string,
     *   account_holder: ?string,
     *   status: string
     * } $companyData
     * @return array{user_id: int, company_id: int}
     */
    public function createFounderAndCompany(array $userData, array $companyData): array
    {
        $this->pdo->beginTransaction();

        try {
            $userSql = 'INSERT INTO users (
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

            $userStmt = $this->pdo->prepare($userSql);
            $userStmt->execute([
                'name'          => $userData['name'],
                'email'         => $userData['email'],
                'password_hash' => $userData['password_hash'],
                'role'          => $userData['role'],
                'address'       => $userData['address'],
                'telephone'     => $userData['telephone'],
                'profile_bio'   => $userData['profile_bio'],
            ]);

            $userId = (int) $this->pdo->lastInsertId();

            $companySql = 'INSERT INTO companies (
                               owner_user_id,
                               name,
                               tagline,
                               description,
                               category,
                               address,
                               telephone,
                               tax_number,
                               company_valuation,
                               funding_goal,
                               raised_amount,
                               minimum_investment,
                               profit_return_pct,
                               iban,
                               account_holder,
                               status
                           ) VALUES (
                               :owner_user_id,
                               :name,
                               :tagline,
                               :description,
                               :category,
                               :address,
                               :telephone,
                               :tax_number,
                               :company_valuation,
                               :funding_goal,
                               0.00,
                               :minimum_investment,
                               :profit_return_pct,
                               :iban,
                               :account_holder,
                               :status
                           )';

            $companyStmt = $this->pdo->prepare($companySql);
            $companyStmt->execute([
                'owner_user_id'      => $userId,
                'name'               => $companyData['name'],
                'tagline'            => $companyData['tagline'],
                'description'        => $companyData['description'],
                'category'           => $companyData['category'],
                'address'            => $companyData['address'],
                'telephone'          => $companyData['telephone'],
                'tax_number'         => $companyData['tax_number'],
                'company_valuation'  => $companyData['company_valuation'],
                'funding_goal'       => $companyData['funding_goal'],
                'minimum_investment' => $companyData['minimum_investment'],
                'profit_return_pct'  => $companyData['profit_return_pct'],
                'iban'               => $companyData['iban'],
                'account_holder'     => $companyData['account_holder'],
                'status'             => $companyData['status'],
            ]);

            $companyId = (int) $this->pdo->lastInsertId();
            $this->pdo->commit();

            $this->logger->info('Registered company and founder account', [
                'user_id'    => $userId,
                'company_id' => $companyId,
                'email'      => $userData['email'],
            ]);

            return [
                'user_id'    => $userId,
                'company_id' => $companyId,
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $this->logger->error('Failed to register company and founder account', [
                'email'     => $userData['email'],
                'company'   => $companyData['name'],
                'exception' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}