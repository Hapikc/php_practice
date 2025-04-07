<?php

namespace Src;

class Request
{
    protected array $body;
    protected array $files;
    public string $method;
    public array $headers;

    public function __construct()
    {
        $this->body = $_POST;
        $this->files = $_FILES;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders() ?? [];
    }

    public function all(): array
    {
        return $this->body + $this->files;
    }

    public function set($field, $value): void
    {
        $this->body[$field] = $value;
    }

    public function get($field, $default = null)
    {
        return $this->body[$field] ?? $default;
    }

    public function files(): array
    {
        return $this->files;
    }

    // Новые методы для работы с файлами
    public function hasFile(string $name): bool
    {
        return isset($this->files[$name]) &&
            $this->files[$name]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public function file(string $name): ?array
    {
        return $this->files[$name] ?? null;
    }

    public function __get($key)
    {
        return $this->get($key);
    }
}