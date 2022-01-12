<?php

namespace FatturaElettronicaPhp\Sender\Contracts;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

interface HttpAdapterInterface extends SenderAdapterInterface
{
    public function setClient(ClientInterface $client, ?RequestFactoryInterface $requestFactory = null, ?StreamFactoryInterface $streamFactory = null): static;
}
