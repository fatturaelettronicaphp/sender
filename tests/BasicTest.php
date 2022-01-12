<?php

use FatturaElettronicaPhp\Sender\Tests\ExampleAdapter;

it('can manage an example Sender', function () {
    $sender = new ExampleAdapter();
    $sender->send('SOME XML');

    expect($sender->sent())->toBeTrue();
});
