<?php

namespace FatturaElettronicaPhp\Sender\Contracts;

use FatturaElettronicaPhp\Sender\Exceptions\CannotSendDigitalDocumentException;

interface SenderAdapterInterface
{
    /** @throws CannotSendDigitalDocumentException **/
    public function send(string $xml): void;
}
