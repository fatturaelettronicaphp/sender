<?php

namespace FatturaElettronicaPhp\Sender\Adapter;

trait HasEnvironments
{
    protected string $environment;

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
