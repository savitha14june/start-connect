<?php
declare(strict_types=1);

class UserService
{
    private UserRepo $userRepo;
    private \App\Logger $logger;

    public function __construct(UserRepo $userRepo, \App\Logger $logger)
    {
        $this->userRepo = $userRepo;
        $this->logger = $logger;
    }

    public function login(string $email, string $password): ?array
    {
        // Benutzer anhand der E-Mail suchen
        $user = $this->userRepo->findByEmail($email);

        // Login fehlgeschlagen
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->logger->warning('Login fehlgeschlagen', [
                'email' => $email
            ]);

            return null;
        }

        // Login erfolgreich
        $this->logger->info('Login erfolgreich', [
            'user_id' => $user['id'],
            'email' => $user['email']
        ]);

        return $user;
    }
}
/*
declare(strict_types=1);

class UserService
{
    private UserRepo $userRepo;
    private \App\Logger $logger;

    public function __construct(UserRepo $userRepo, \App\Logger $logger)
    {
        $this->userRepo = $userRepo;
        $this->logger = $logger;
    }

    public function login(string $email, string $password): ?array
    {
        // Benutzer anhand der E-Mail suchen
        $user = $this->userRepo->findByEmail($email);

        // Login fehlgeschlagen
        if (!$user || $password !== (string) $user['password_hash']) {
            $this->logger->warning('Login fehlgeschlagen', [
                'email' => $email
            ]);

            return null;
        }

        // Login erfolgreich
        $this->logger->info('Login erfolgreich', [
            'user_id' => $user['id'],
            'email' => $user['email']
        ]);

        return $user;
    }
}

*/