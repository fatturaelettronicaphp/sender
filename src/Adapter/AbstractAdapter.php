<?php

namespace FatturaElettronicaPhp\Sender\Adapter;

use FatturaElettronicaPhp\Sender\Config;
use FatturaElettronicaPhp\Sender\Contracts\HttpAdapterInterface;
use FatturaElettronicaPhp\Sender\Contracts\SupportsDifferentEnvironmentsInterface;
use FatturaElettronicaPhp\Sender\Exceptions\CannotFindClientException;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidCredentialsException;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidEnvironmentException;
use FatturaElettronicaPhp\Sender\Exceptions\QuotaExceededException;
use FatturaElettronicaPhp\Sender\Exceptions\RequestException;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

abstract class AbstractAdapter implements HttpAdapterInterface
{
    private ?ClientInterface $client = null;
    private ?RequestFactoryInterface $requestFactory = null;
    private ?RequestFactoryInterface $streamFactory = null;
    protected Config $config;

    public function __construct(array $config = [], ?ClientInterface $client = null, ?RequestFactoryInterface $requestFactory = null, ?StreamFactoryInterface $streamFactory = null)
    {
        $this->config = new Config($config);

        if ($this instanceof SupportsDifferentEnvironmentsInterface) {
            $this->setEnvironment($config['environment'] ?? throw new InvalidEnvironmentException($this));
        }
    }

    public function setClient(ClientInterface $client, ?RequestFactoryInterface $requestFactory = null, ?StreamFactoryInterface $streamFactory = null): static
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory ?? $this->requestFactory;
        $this->streamFactory = $streamFactory ?? $this->streamFactory;

        return $this;
    }

    public function withConfig(Config $config): static
    {
        $this->config = $config;

        return $this;
    }
    public function getConfig(): Config
    {
        return $this->config;
    }

    protected function createRequest(string $method, string $uri): RequestInterface
    {
        return $this->requestFactory()->createRequest($method, $uri);
    }

    protected function createBody(array $data): StreamInterface
    {
        return $this->streamFactory()->createStream(http_build_query($data));
    }

    protected function httpClient(): ClientInterface
    {
        $client = $this->client ?? Psr18ClientDiscovery::find();
        if ($client === null) {
            throw new CannotFindClientException();
        }

        return $this->client = $client;
    }

    protected function requestFactory(): RequestFactoryInterface
    {
        $this->requestFactory = $this->requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();

        return $this->requestFactory;
    }

    protected function streamFactory(): StreamFactoryInterface
    {
        $this->streamFactory = $this->streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();

        return $this->streamFactory;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidCredentialsException
     * @throws QuotaExceededException
     * @throws RequestException
     */
    protected function sendRequest(RequestInterface $request)
    {
        $response = $this->sendHttpRequest($request);

        $statusCode = $response->getStatusCode();
        if (401 === $statusCode) {
            throw new InvalidCredentialsException();
        }

        if (429 === $statusCode) {
            throw new QuotaExceededException();
        }

        if ($statusCode >= 300) {
            throw RequestException::create($request, $response);
        }

        return (string)$response->getBody();
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function sendHttpRequest(RequestInterface $request): ResponseInterface
    {
        return $this->httpClient()->sendRequest($request);
    }
}
