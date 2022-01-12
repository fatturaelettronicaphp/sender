<?php

namespace FatturaElettronicaPhp\Sender\Exceptions;

use RuntimeException;

class CannotFindClientException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("Cannot find a valid implementation of Psr\Http\Client\ClientInterface. Either provide one as an argument or install any of the available implementations: https://packagist.org/providers/psr/http-client-implementation");
    }
}
