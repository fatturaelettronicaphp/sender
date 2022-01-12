<?php

namespace FatturaElettronicaPhp\Sender\Adapter\Aruba;

use FatturaElettronicaPhp\Sender\Adapter\AbstractAdapter;
use FatturaElettronicaPhp\Sender\Adapter\HasEnvironments;
use FatturaElettronicaPhp\Sender\Contracts\SenderAdapterInterface;
use FatturaElettronicaPhp\Sender\Contracts\SupportsDifferentEnvironmentsInterface;
use FatturaElettronicaPhp\Sender\Exceptions\CannotSendDigitalDocumentException;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidCredentialsException;

class ArubaAdapter extends AbstractAdapter implements SenderAdapterInterface, SupportsDifferentEnvironmentsInterface
{
    use HasEnvironments;

    public const ENV_DEMO = 'demo';
    public const ENV_PRODUCTION = 'production';

    private const DEMO_AUTH_URL = 'https://demoauth.fatturazioneelettronica.aruba.it';
    private const DEMO_INVOICE_URL = 'https://demows.fatturazioneelettronica.aruba.it';
    private const INVOICE_URL = 'https://auth.fatturazioneelettronica.aruba.it';
    private const AUTH_URL = 'https://ws.fatturazioneelettronica.aruba.it';

    public function send(string $xml): void
    {
        $request = $this->createRequest('POST', $this->invoiceUrl())
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('charset', 'UTF8-8')
            ->withHeader('Authorization', 'Bearer ' . $this->login())
            ->withBody(
                $this->streamFactory()->createStream($xml)
            );

        $response = $this->sendRequest($request);
        $result = json_decode($response, true);

        if ($result !== true) {
            throw new CannotSendDigitalDocumentException($response);
        }
    }

    private function login(): string
    {
        $request = $this->createRequest('POST', $this->authUrl())
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($this->createBody([
                'username' => $this->config->get('username'),
                'password' => $this->config->get('password'),
                'grant_type' => 'password',
            ]));

        $response = $this->sendRequest($request);
        $result = json_decode($response, true);

        if ($result === null) {
            throw new InvalidCredentialsException();
        }

        return $result['token'] ?? throw new InvalidCredentialsException();
    }

    private function authUrl(): string
    {
        if ($this->environment() === self::ENV_PRODUCTION) {
            return self::AUTH_URL;
        }

        return self::DEMO_AUTH_URL;
    }

    private function invoiceUrl(): string
    {
        if ($this->environment() === self::ENV_PRODUCTION) {
            return self::INVOICE_URL;
        }

        return self::DEMO_INVOICE_URL;
    }

    public function environments(): array
    {
        return [
            self::ENV_PRODUCTION,
            self::ENV_DEMO,
        ];
    }
}
