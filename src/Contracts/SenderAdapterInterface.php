<?php

namespace FatturaElettronicaPhp\Sender\Contracts;

use FatturaElettronicaPhp\Sender\Config;
use FatturaElettronicaPhp\Sender\Exceptions\CannotSendDigitalDocumentException;
use FatturaElettronicaPhp\Sender\Result;

interface SenderAdapterInterface
{
    /**
     * Sends a Document to the Adapter
     *
     * @param string $xml The XML file contents.
     * @param Config|null $config An optional set of configs. Can be used to overwrite the current adapter settings.
     * @throws CannotSendDigitalDocumentException
     * @return Result
     */
    public function send(string $xml, ?Config $config = null): Result;
}
