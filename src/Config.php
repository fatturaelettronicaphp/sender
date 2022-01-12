<?php

namespace FatturaElettronicaPhp\Sender;

class Config
{
    public const OPTION_ENVIRONMENT = 'environment';

    private array $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    public function extend(array $config): Config
    {
        return new Config(array_merge($this->config, $config));
    }

    public function withDefaults(array $defaults): Config
    {
        return new Config($this->config + $defaults);
    }
}
