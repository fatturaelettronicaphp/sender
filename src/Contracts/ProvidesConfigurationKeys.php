<?php

namespace FatturaElettronicaPhp\Sender\Contracts;

interface ProvidesConfigurationKeys
{
    public function configKeys(): array;
}
