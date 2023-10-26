<?php

namespace App\Exceptions;

use Exception;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class GuzzleRequestException extends RequestException
{
    protected $logger;

    public function __construct(
        $message = "",
        RequestInterface $request = null,
        ResponseInterface $response = null,
        Exception $previous = null,
        array $handlerContext = [],
        LoggerInterface $logger = null
    ) {
        parent::__construct($message, $request, $response, $previous, $handlerContext);

        $this->logger = $logger ?? app(LoggerInterface::class);
    }

    /**
     * Report the exception.
     */
    public function report(): bool
    {
        if ($this instanceof RequestException) {
            $message = str_replace(
                rtrim($this->getMessage()),
                (string) $this->getResponse()->getBody(),
                (string) $this
            );
            $this->logger->error($message);

            return true; // return true to indicate that exception has been reported and doesn't need further reporting
        }

        return false; // return false to fall back to the default exception handling
    }
}
