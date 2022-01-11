<?php

namespace FatturaElettronicaPhp\Sender;

interface SupportsDifferentEnvironmentsInterface
{
    public function environments(): array;

    public function environment(): string;

    public function setEnvironment(string $environment): self;
}
