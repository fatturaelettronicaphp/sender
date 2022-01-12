<?php

namespace FatturaElettronicaPhp\Sender;

use FatturaElettronicaPhp\Sender\Contracts\HttpAdapterInterface;
use FatturaElettronicaPhp\Sender\Contracts\SenderAdapterInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Sender
{
    private SenderAdapterInterface $adapter;

    public function __construct(SenderAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function withClient(ClientInterface $client, ?RequestFactoryInterface $requestFactory = null, ?StreamFactoryInterface $streamFactory = null): static
    {
        if (! $this->adapter instanceof HttpAdapterInterface) {
            return $this;
        }

        $this->adapter->setClient($client, $requestFactory, $streamFactory);

        return $this;
    }

    public function send(string $xml): void
    {
        $this->adapter->send($xml);
    }
}
