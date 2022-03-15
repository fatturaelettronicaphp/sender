<?php

namespace FatturaElettronicaPhp\Sender\Adapter\Acube;

use FatturaElettronicaPhp\Sender\Adapter\AbstractAdapter;
use FatturaElettronicaPhp\Sender\Adapter\HasEnvironments;
use FatturaElettronicaPhp\Sender\Config;
use FatturaElettronicaPhp\Sender\Contracts\ProvidesConfigurationKeys;
use FatturaElettronicaPhp\Sender\Contracts\SenderAdapterInterface;
use FatturaElettronicaPhp\Sender\Contracts\SupportsDifferentEnvironmentsInterface;
use FatturaElettronicaPhp\Sender\Exceptions\CannotSendDigitalDocumentException;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidCredentialsException;
use FatturaElettronicaPhp\Sender\Result;

class AcubeAdapter extends AbstractAdapter implements SenderAdapterInterface, SupportsDifferentEnvironmentsInterface, ProvidesConfigurationKeys
{
    use HasEnvironments;

    public const ENV_SANDBOX = 'sandbox';
    public const ENV_PRODUCTION = 'production';

    private const SANDBOX_AUTH_URL = 'https://common-sandbox.api.acubeapi.com/login';
    private const SANDBOX_INVOICE_URL = 'https://api-sandbox.acubeapi.com/invoices';
    private const INVOICE_URL = 'https://api.acubeapi.com/invoices';
    private const AUTH_URL = 'https://common.api.acubeapi.com/login';
    private const SIMPLIFIED_INVOICE_ADDITIONAL_ENDPOINT = 'simplified';

    public function send(string $xml, ?Config $config = null): Result
    {
        if ($config) {
            $this->config = $this->config->extend($config->toArray());
        }

        $request = $this->createRequest('POST', $this->invoiceUrl())
            ->withHeader('Content-Type', 'application/xml')
            ->withHeader('Authorization', 'Bearer ' . $this->login())
            ->withBody(
                $this->streamFactory()->createStream($xml)
            );

        $response = $this->sendRequest($request);
        $result = json_decode($response, true);

        if (! $result || ! isset($result['uuid'])) {
            throw new CannotSendDigitalDocumentException($response);
        }

        return new Result($result);
    }

    private function login(): string
    {
        if ($this->config->get('email') === null || $this->config->get('password') === null) {
            throw new InvalidCredentialsException("`email` and `password` configuration keys are required");
        }

        $request = $this->createRequest('POST', $this->authUrl())
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory()->createStream(json_encode([
                'email' => $this->config->get('email'),
                'password' => $this->config->get('password'),
            ])));

        $response = $this->sendRequest($request);
        $result = json_decode($response, true);

        if ($result === null) {
            throw new InvalidCredentialsException();
        }

        return $result['token'] ?? throw new InvalidCredentialsException("Cannot retrieve login token");
    }

    private function authUrl(): string
    {
        if ($this->environment() === self::ENV_PRODUCTION) {
            return self::AUTH_URL;
        }

        return self::SANDBOX_AUTH_URL;
    }

    private function invoiceUrl(): string
    {
        $invoice_url = self::SANDBOX_INVOICE_URL;
        if ($this->environment() === self::ENV_PRODUCTION) {
            $invoice_url = self::INVOICE_URL;
        }

        if ($this->simplified()) {
            $invoice_url .= '/'. self::SIMPLIFIED_INVOICE_ADDITIONAL_ENDPOINT;
        }

        return $invoice_url;
    }

    public function simplified(): bool
    {
        return $this->config->get('simplified', false);
    }

    public function environments(): array
    {
        return [
            self::ENV_PRODUCTION,
            self::ENV_SANDBOX,
        ];
    }

    public function configKeys(): array
    {
        return [
            'email',
            'password',
            'environment',
            'simplified',
        ];
    }
}
