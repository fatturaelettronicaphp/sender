<?php

namespace FatturaElettronicaPhp\Sender;

use FatturaElettronicaPhp\Sender\Exceptions\HttpClientRequiredException;
use Http\Discovery\Exception\NotFoundException;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait MakesHttpRequests
{
    private ?ClientInterface $httpClient = null;

    public function setHttpClient(ClientInterface $client): self
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * @throws HttpClientRequiredException
     */
    public function httpClient(): ClientInterface
    {
        if ($this->httpClient === null) {
            try {
                $this->httpClient = HttpClientDiscovery::find();
            } catch (NotFoundException $e) {
                throw new HttpClientRequiredException("An http client is required. Try installing guzzlehttp/guzzle for example");
            }
        }

        return $this->httpClient;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function sendHttpRequest(RequestInterface $request): ResponseInterface
    {
        return $this->httpClient()->sendRequest($request);
    }
}
