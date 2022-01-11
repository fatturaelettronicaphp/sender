<?php

namespace FatturaElettronicaPhp\Sender;

use FatturaElettronicaPhp\Sender\Exceptions\CannotSendDigitalDocumentException;

interface DigitalDocumentSenderInterface
{
    /** @throws CannotSendDigitalDocumentException **/
    public function send(string $xml): void;
}
