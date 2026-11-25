<?php

declare(strict_types=1);

namespace App;

final class Logger
{
    public function __construct(
        private readonly string $logFile
    ) {}

    /**
     * @param array<string, mixed> $context
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $timestamp = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $contextJson = $context !== []
            ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            : '';

        $line = sprintf(
            "[%s] [%s] %s%s%s",
            $timestamp,
            strtoupper($level),
            $message,
            $contextJson,
            PHP_EOL
        );

        file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }
}