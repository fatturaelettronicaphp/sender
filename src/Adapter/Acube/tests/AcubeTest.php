<?php

use FatturaElettronicaPhp\Sender\Adapter\Acube\AcubeAdapter;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidCredentialsException;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidEnvironmentException;

/**
 * ./vendor/bin/pest --group=acube
 */
it('cannot send without valid login details', function () {
    $sender = new AcubeAdapter([
        'environment' => AcubeAdapter::ENV_SANDBOX,
    ]);
    $sender->send('SOME XML');
})->group('acube')->throws(InvalidCredentialsException::class);

it('cannot send without valid environment', function () {
    $sender = new AcubeAdapter([
        'email' => 'PIPPO',
        'password' => 'PLUTO',
    ]);
    $sender->send('SOME XML');
})->group('acube')->throws(InvalidEnvironmentException::class);

it('can send sample invoice', function () {
    $credentials = (array)$this->credentials->acube ?? null;
    if (! $credentials) {
        $this->markTestSkipped();
    }

    $sender = new AcubeAdapter(
        $credentials
    );

    $path_xml_example = __DIR__ . DIRECTORY_SEPARATOR . "samples/invoice_sample.xml";
    $xml = file_get_contents($path_xml_example);
    $result = $sender->send($xml);

    expect($result->get("uuid"))->toBeString();
})->group('acube');

it('can send multiline invoice', function () {
    $credentials = (array)$this->credentials->acube ?? null;
    if (! $credentials) {
        $this->markTestSkipped();
    }

    $sender = new AcubeAdapter(
        $credentials
    );
    $path_xml_example = __DIR__ . DIRECTORY_SEPARATOR . "samples/invoice_sample_multiline.xml";
    $xml = file_get_contents($path_xml_example);
    $result = $sender->send($xml);

    expect($result->get("uuid"))->toBeString();
})->group('acube');

it('can send simplified invoice', function () {
    $credentials = (array)$this->credentials->acube ?? null;
    if (! $credentials) {
        $this->markTestSkipped();
    }

    $sender = new AcubeAdapter(
        $credentials + [
            'simplified' => true,
        ],
    );
    $path_xml_example = __DIR__ . DIRECTORY_SEPARATOR . "samples/invoice_simplified_sample.xml";
    $xml = file_get_contents($path_xml_example);
    $result = $sender->send($xml);

    expect($result->get("uuid"))->toBeString();
})->group('acube');
