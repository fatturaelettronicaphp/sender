<?php

namespace FatturaElettronicaPhp\Sender;

trait HasEnvironments
{
    protected array $environments = [];
    protected string $environment;

    public function environments(): array
    {
        return $this->environments;
    }

    public function setEnvironment(string $environment): self
    {
        $this->environment = $environment;

        return $this;
    }

    public function environment(): string
    {
        return $this->environment;
    }
}
