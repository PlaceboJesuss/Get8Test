<?php

namespace Helpers\SubSystems;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class Logger implements LoggerInterface
{
    use LoggerTrait;

    private string $path;

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if (!isset($this->path)) {
            throw new Exception("Путь для логов не установлен");
        }
        $message = date('Y-m-d H:i:s.v') . " PID: " . getmypid() . " Level:" . $level . " " . $message;

        if (!empty($context)) {
            $message .= " " . print_r($context, true);
        }

        $message .= PHP_EOL;
        file_put_contents($this->path, $message, FILE_APPEND);
    }
}
