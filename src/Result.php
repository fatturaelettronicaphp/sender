<?php

namespace FatturaElettronicaPhp\Sender;

class Result
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}
