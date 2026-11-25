<?php

declare(strict_types=1);

$host = '127.0.0.1';
$dbName = 'startupconnect_db';
$username = 'root';
$password = ''; 
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log('DB Connection Error: ' . $e->getMessage());
    http_response_code(500);
 exit('Verbindung zur Datenbank fehlgeschlagen.');}

return $pdo;