<?php

use FatturaElettronicaPhp\Sender\Adapter\Acube\AcubeAdapter;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidCredentialsException;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidEnvironmentException;
use FatturaElettronicaPhp\Sender\Exceptions\RequestException;

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
it('can be sent - sample invoice', function () {
    $sender = new AcubeAdapter([
        'email' => 'YOUR_EMAIL',
        'password' => 'YOUR_PASSWORD',
        'environment' => AcubeAdapter::ENV_SANDBOX,
    ]);
    $path_xml_example = __DIR__. DIRECTORY_SEPARATOR."samples/invoice_sample.xml";
    $xml = file_get_contents($path_xml_example);
    $uid = $sender->send($xml);
    expect($uid)->toBeString();
})->group('acube');
it('can be sent - multiline invoice', function () {
    $sender = new AcubeAdapter([
        'email' => 'YOUR_EMAIL',
        'password' => 'YOUR_PASSWORD',
        'environment' => AcubeAdapter::ENV_SANDBOX,
    ]);
    $path_xml_example = __DIR__. DIRECTORY_SEPARATOR."samples/invoice_sample_multiline.xml";
    $xml = file_get_contents($path_xml_example);
    $uid = $sender->send($xml);
    expect($uid)->toBeString();
})->group('acube');
it('cannot be sent - simplified invoice', function () {
    $sender = new AcubeAdapter([
        'email' => 'YOUR_EMAIL',
        'password' => 'YOUR_PASSWORD',
        'environment' => AcubeAdapter::ENV_SANDBOX,
    ]);
    $path_xml_example = __DIR__. DIRECTORY_SEPARATOR."samples/invoice_simplified_sample.xml";
    $xml = file_get_contents($path_xml_example);
    $uuid = $sender->send($xml);

})->throws(RequestException::class)->group('acube');
it('can be sent - simplified invoice', function () {
    $sender = new AcubeAdapter([
        'email' => 'YOUR_EMAIL',
        'password' => 'YOUR_PASSWORD',
        'environment' => AcubeAdapter::ENV_SANDBOX,
        'is_simplified' => true,
    ]);
    $path_xml_example = __DIR__. DIRECTORY_SEPARATOR."samples/invoice_simplified_sample.xml";
    $xml = file_get_contents($path_xml_example);
    $uuid = $sender->send($xml);
    expect($uuid)->toBeString();
})->group('acube');
