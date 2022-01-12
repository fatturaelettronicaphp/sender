<?php

use FatturaElettronicaPhp\Sender\Adapter\Acube\AcubeAdapter;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidCredentialsException;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidEnvironmentException;

it('cannot send without valid login details', function () {
    $sender = new AcubeAdapter([
        'environment' => AcubeAdapter::ENV_SANDBOX,
    ]);
    $sender->send('SOME XML');
})->throws(InvalidCredentialsException::class);

it('cannot send without valid environment', function () {
    $sender = new AcubeAdapter([
        'email' => 'PIPPO',
        'password' => 'PLUTO',
    ]);
    $sender->send('SOME XML');
})->throws(InvalidEnvironmentException::class);
