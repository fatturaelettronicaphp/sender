<?php

namespace FatturaElettronicaPhp\Sender\Exceptions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class RequestException extends RuntimeException
{
    public RequestInterface $request;
    public ResponseInterface $response;

    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(RequestInterface $request, ResponseInterface $response): self
    {
        return (new self((string) $request->getUri(), $response->getStatusCode()))
            ->withRequest($request)
            ->withResponse($response);
    }

    private function withRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    public function withResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }
}
