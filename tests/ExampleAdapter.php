<?php

namespace FatturaElettronicaPhp\Sender\Tests;

use FatturaElettronicaPhp\Sender\Adapter\AbstractAdapter;
use FatturaElettronicaPhp\Sender\Adapter\HasEnvironments;
use FatturaElettronicaPhp\Sender\Contracts\SenderAdapterInterface;
use FatturaElettronicaPhp\Sender\Contracts\SupportsDifferentEnvironmentsInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Mock\Client;
use Psr\Http\Message\ResponseInterface;

class ExampleAdapter extends AbstractAdapter implements SenderAdapterInterface, SupportsDifferentEnvironmentsInterface
{
    use HasEnvironments;

    public const ENV_TEST = 'test';
    public const ENV_PRODUCTION = 'production';
    private ?ResponseInterface $response = null;

    public function __construct()
    {
        parent::__construct([
            'environment' => self::ENV_TEST,
        ]);

        $client = (new Client());
        $fakeResponse = Psr17FactoryDiscovery::findResponseFactory()->createResponse(200);

        $client->setDefaultResponse($fakeResponse);
        $this->setClient($client);
    }

    public function send(string $xml): void
    {
        $request = Psr17FactoryDiscovery::findRequestFactory()->createRequest("POST", "https://example.com/invoice/send");

        $this->response = $this->sendHttpRequest($request);
    }

    public function sent(): bool
    {
        return $this->response !== null && $this->response->getStatusCode() === 200;
    }

    public function environments(): array
    {
        return [
            self::ENV_TEST,
            self::ENV_PRODUCTION,
        ];
    }
}
