<?php

namespace FatturaElettronicaPhp\Sender\Exceptions;

use FatturaElettronicaPhp\Sender\Contracts\SupportsDifferentEnvironmentsInterface;
use RuntimeException;

class InvalidEnvironmentException extends RuntimeException
{
    public function __construct(SupportsDifferentEnvironmentsInterface $adapter)
    {
        $class = get_class($adapter);
        $envs = implode(",", $adapter->environments());

        parent::__construct("Configuration key `environment` is required for {$class}. Available environments are: {$envs}");
    }
}
