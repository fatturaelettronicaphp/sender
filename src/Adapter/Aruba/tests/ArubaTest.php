<?php

use FatturaElettronicaPhp\Sender\Adapter\Aruba\ArubaAdapter;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidCredentialsException;
use FatturaElettronicaPhp\Sender\Exceptions\InvalidEnvironmentException;

it('cannot send without valid login details', function () {
    $sender = new ArubaAdapter([
        'username' => 'PIPPO',
        'password' => 'PLUTO',
        'environment' => ArubaAdapter::ENV_DEMO,
    ]);
    $sender->send('SOME XML');
})->throws(InvalidCredentialsException::class);

it('cannot send without valid environment', function () {
    $sender = new ArubaAdapter([
        'username' => 'PIPPO',
        'password' => 'PLUTO',
    ]);
    $sender->send('SOME XML');
})->throws(InvalidEnvironmentException::class);
