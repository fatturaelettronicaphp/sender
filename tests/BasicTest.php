<?php

use FatturaElettronicaPhp\Sender\Tests\ExampleSender;

it('can manage an example Sender', function () {
    $sender = new ExampleSender();
    $sender->send('SOME XML');

    expect($sender->sent())->toBeTrue();
});
