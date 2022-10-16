<?php

namespace http;


/**
 * Class Response
 * @package http
 */
class Response extends ResponseAddition
{
    use MessageTrait;
    
    protected static $messages = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Model Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Model',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ]; 

    protected $statusCode;
    protected $reasonPhrase;
    
    /**
    * Конструктор
    *
    * @param string|resource|object $stream 
    * @param int $status
    * @param array $headers
    */
    public function __construct(
        $storage,
        $body = 'php://temp',
        $status = 200,
        array $headers = []
    ) {
        $this->storage = $storage;
     
        if (!is_string($body) && !is_resource($body) && !$body instanceof Stream) {
            throw new \InvalidArgumentException(ABC_HTTP_INVALID_STREAM);
        }
     
        if (null !== $status) {
            $this->validateStatus($status);
        }
        
        $this->protocolVersion = '1.1';
        $this->setHeaders($headers);
        $this->body = ($body instanceof Stream) ? $body : new Stream(fopen('php://temp', 'r+'));        
        $this->statusCode = $status ? (int)$status : 200;
    }
    
    /**
    * Возвращает статус-код ответа.
    *
    * @return int.
    */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
    * Возвращает новый объект с новым статус-кодом.
    *
    * @param int $code 
    * @param string $reasonPhrase 
    *
    * @return static
    */
    public function withStatus($code, $reasonPhrase = '')
    {
        $clone = clone $this;
        $clone->validateStatus($code);
        $clone->statusCode = (int)$code;
        $clone->reasonPhrase = $reasonPhrase;
        return $clone;
    }

    /**
     * Получает причину статус-кода.
     *
     * @return string 
     */
    public function getReasonPhrase()
    {   
        if (empty($this->reasonPhrase)
            && isset(self::$messages[$this->statusCode])
        ) {
            $this->reasonPhrase = self::$messages[$this->statusCode];
            return $this->reasonPhrase;            
        }
     
        return '';
    }
    
    /**
     * Проверяет статус-код.
     *
     * @param int|string $code
     */
    private function validateStatus($code)
    {
        if (!is_numeric($code)
            || is_float($code)
            || $code < 100
            || $code >= 600
        ) {
            throw new \InvalidArgumentException(ABC_HTTP_INVALID_STREAM);
        }
    }
}
