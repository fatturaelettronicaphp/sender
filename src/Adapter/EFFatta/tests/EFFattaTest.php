<?php

use FatturaElettronicaPhp\Sender\Adapter\EFFatta\EFFattaAdapter;
use FatturaElettronicaPhp\Sender\Exceptions\RequestException;

$shouldSkip = ! isset($TEST_AUTHS['effatta']);

it('cannot send without valid login details', function () {
    $sender = new EFFattaAdapter([
        'username' => 'PIPPO',
        'password' => 'PLUTO',
    ]);
    $sender->send('SOME XML', new \FatturaElettronicaPhp\Sender\Config(['fileName' => 'somefilename.xml']));
})->throws(RequestException::class);
