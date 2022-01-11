<?php

namespace FatturaElettronicaPhp\Sender\Tests;

use FatturaElettronicaPhp\Sender\DigitalDocumentSenderInterface;
use FatturaElettronicaPhp\Sender\HasEnvironments;
use FatturaElettronicaPhp\Sender\MakesHttpRequests;
use FatturaElettronicaPhp\Sender\SupportsDifferentEnvironmentsInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Mock\Client;
use Psr\Http\Message\ResponseInterface;

class ExampleSender implements DigitalDocumentSenderInterface, SupportsDifferentEnvironmentsInterface
{
    use HasEnvironments;
    use MakesHttpRequests;

    public const ENV_TEST = 'test';
    public const ENV_PRODUCTION = 'production';
    private ?ResponseInterface $response = null;

    public function __construct()
    {
        $this->environments = [
            self::ENV_TEST,
            self::ENV_PRODUCTION,
        ];

        $client = (new Client());
        $client->setDefaultResponse(Psr17FactoryDiscovery::findResponseFactory()->createResponse(200));
        $this->setHttpClient($client);
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
}
