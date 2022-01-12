<?php

use FatturaElettronicaPhp\Sender\Adapter\EFFatta\EFFattaAdapter;
use FatturaElettronicaPhp\Sender\Exceptions\RequestException;

it('cannot send without valid login details', function () {
    $sender = new EFFattaAdapter([
        'username' => 'PIPPO',
        'password' => 'PLUTO',
    ]);
    $sender->send('SOME XML', 'somefilename.xml');
})->throws(RequestException::class);
