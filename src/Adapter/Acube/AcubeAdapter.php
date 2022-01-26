<?php

namespace FatturaElettronicaPhp\Sender\Adapter\Acube;

use FatturaElettronicaPhp\Sender\Adapter\AbstractAdapter;
use FatturaElettronicaPhp\Sender\Adapter\HasEnvironments;
use FatturaElettronicaPhp\Sender\Contracts\SenderAdapterInterface;
use FatturaElettronicaPhp\Sender\Contracts\SupportsDifferentEnvironmentsInterface;
use FatturaElettronicaPhp\Sender\Exceptions\CannotSendDigitalDocumentException;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidCredentialsException;

class AcubeAdapter extends AbstractAdapter implements SenderAdapterInterface, SupportsDifferentEnvironmentsInterface
{
    use HasEnvironments;

    public const ENV_SANDBOX = 'sandbox';
    public const ENV_PRODUCTION = 'production';

    private const SANDBOX_AUTH_URL = 'https://common-sandbox.api.acubeapi.com/login';
    private const SANDBOX_INVOICE_URL = 'https://api-sandbox.acubeapi.com/invoices';
    private const INVOICE_URL = 'https://api.acubeapi.com/invoices';
    private const AUTH_URL = 'https://common.api.acubeapi.com/login';
    private const SIMPLIFIED_INVOICE_ADDITIONAL_ENDPOINT = 'simplified';

    public function send(string $xml): string|bool
    {
        $invoice_url = $this->invoiceUrl();
        if ($this->config->get('is_simplified') === true) {
            $invoice_url .= '/'. self::SIMPLIFIED_INVOICE_ADDITIONAL_ENDPOINT;
        }
        $request = $this->createRequest('POST', $invoice_url)
            ->withHeader('Content-Type', 'application/xml')
            ->withHeader('Authorization', 'Bearer ' . $this->login())
            ->withBody(
                $this->streamFactory()->createStream($xml)
            );

        $response = $this->sendRequest($request);
        $result = json_decode($response, true);

        if (empty($result['uuid'])) {
            throw new CannotSendDigitalDocumentException($response);
        }

        return $result['uuid'] ?? false;
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

        return $invoice_url;
    }

    public function environments(): array
    {
        return [
            self::ENV_PRODUCTION,
            self::ENV_SANDBOX,
        ];
    }
}
