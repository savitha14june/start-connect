<?php

declare(strict_types=1);

class UserRepo
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // البحث عن المستخدم باستخدام البريد الإلكتروني
    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM users WHERE email = :email LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    // البحث عن المستخدم باستخدام ID
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, name, email, role, address, telephone, profile_bio, created_at
                FROM users
                WHERE id = :id
                LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    // إنشاء مستخدم جديد
    public function create(array $data): int
    {
        $sql = 'INSERT INTO users
                (name, email, password_hash, role, address, telephone, profile_bio)
                VALUES
                (:name, :email, :password_hash, :role, :address, :telephone, :profile_bio)';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password_hash' => $data['password_hash'],
            'role'          => $data['role'] ?? 'investor',
            'address'       => $data['address'] ?? null,
            'telephone'     => $data['telephone'] ?? null,
            'profile_bio'   => $data['profile_bio'] ?? null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}