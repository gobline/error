<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Error;

use Psr\Log\LoggerInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ErrorCatcher
{
    private $errorHandler;
    private $logger;
    private $logLevel = [];
    private $defaultLogLevel = 'error';

    public function __construct()
    {
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
        register_shutdown_function([$this, 'shutdownFunction']);
    }

    public function setErrorHandler(ErrorHandlerInterface $errorHandler)
    {
        $this->errorHandler = $errorHandler;

        return $this;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function handleException(\Exception $e)
    {
        if (isset($this->logger)) {
            $errorCode = $e->getCode() === 0 ? 500 : $e->getCode();
            $logLevel = isset($this->logLevel[$errorCode]) ? $this->logLevel[$errorCode] : $this->defaultLogLevel;
            $this->logger->log($logLevel, $e->getMessage(), ['exception' => $e]);
        }

        if (isset($this->errorHandler)) {
            $this->errorHandler->handle($e);
        } else {
            throw $e;
        }
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if (0 === error_reporting()) {
            return; // error was suppressed with the @-operator
        }

        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public function shutdownFunction()
    {
        if (0 === error_reporting()) {
            return; // error was suppressed with the @-operator
        }

        $error = error_get_last();

        if ($error['type'] === E_ERROR) {
            $this->handleException(new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        }
    }

    public function setDefaultLogLevel($logLevel)
    {
        if ((string) $logLevel === '') {
            throw new \InvalidArgumentException('$logLevel cannot be empty');
        }

        $this->defaultLogLevel = $logLevel;

        return $this;
    }

    public function setLogLevel($errorCode, $logLevel)
    {
        if (!ctype_digit((string) $errorCode)) {
            throw new \InvalidArgumentException('$errorCode must be an integer');
        }

        if ((string) $logLevel === '') {
            throw new \InvalidArgumentException('$logLevel cannot be empty');
        }

        $this->logLevel[(int) $errorCode] = $logLevel;

        return $this;
    }
}
