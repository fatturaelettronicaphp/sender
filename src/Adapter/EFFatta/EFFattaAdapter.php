<?php

namespace FatturaElettronicaPhp\Sender\Adapter\EFFatta;

use FatturaElettronicaPhp\Sender\Adapter\AbstractAdapter;
use FatturaElettronicaPhp\Sender\Config;
use FatturaElettronicaPhp\Sender\Contracts\ProvidesConfigurationKeys;
use FatturaElettronicaPhp\Sender\Contracts\SenderAdapterInterface;
use FatturaElettronicaPhp\Sender\Exceptions\CannotSendDigitalDocumentException;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidCredentialsException;
use FatturaElettronicaPhp\Sender\Result;

class EFFattaAdapter extends AbstractAdapter implements SenderAdapterInterface, ProvidesConfigurationKeys
{
    private const INVOICE_URL = 'https://fattura.effatta.it/webservice/RestAPI.asmx/sendXML';
    private const AUTH_URL = 'https://fattura.effatta.it/webservice/RestAPI.asmx/login';

    public function send(string $xml, ?Config $config = null): Result
    {
        if ($config) {
            $this->config = $this->config->extend($config->toArray());
        }

        $token = $this->login();
        $fileName = $this->config->get('filename', uniqid() . '.xml');

        $request = $this->createRequest('POST', self::INVOICE_URL)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                $this->streamFactory()->createStream(json_encode([
                    'token' => $token,
                    'idMittente' => $this->config->get('idMittente', ''),
                    'dataUserId' => $this->config->get('dataUserId', ''),
                    'nomeFile' => $fileName,
                    'base64File' => base64_encode($xml),
                ]))
            );

        $response = $this->sendRequest($request);

        $result = json_decode($response, true);

        if ($result !== true) {
            throw new CannotSendDigitalDocumentException($response);
        }

        return new Result([]);
    }

    private function login(): string
    {
        $request = $this->createRequest('POST', self::AUTH_URL)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory()->createStream(json_encode([
                'username' => $this->config->get('username'),
                'password' => $this->config->get('password'),
                'source' => $this->config->get('source', 'FatturaElettronicaSender'),
            ])));

        $response = $this->sendRequest($request);
        $result = json_decode($response, true);

        if ($result === null) {
            throw new InvalidCredentialsException();
        }

        $result = json_decode($result['d'] ?? '', true);
        if ($result === null) {
            throw new InvalidCredentialsException();
        }

        return $result['token'] ?? throw new InvalidCredentialsException();
    }

    public function configKeys(): array
    {
        return [
            'username',
            'password',
            'source',
            'filename',
            'idMittente',
            'dataUserId',
        ];
    }
}
