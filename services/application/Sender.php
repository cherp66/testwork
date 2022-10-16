<?php
namespace application;

/**
 * Class Sender
 * @package services
 */
class Sender
{
    protected $response;

    /**
     * @param $response
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @param string $body
     * @param int $status
     * @return mixed
     */
    public function asJson(array $body = [], $status = 200)
    {
        $response = $this->response->withStatus($status);
        $body = json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->response = $response->write($body);
    }

    /**
     * @param string $body
     * @return false
     */
    public function answer($body, $status = 200)
    {
        $response = $this->response->withStatus($status);
        $response = $response->write($body);
        $this->display($response);
    }

    /**
     * @param string $body
     * @return false
     */
    public function notFound($body = '404 Not found')
    {
        $response = $this->response->withStatus(404);
        $response = $response->write($body);
        $this->display($response);
    }

    /**
     * Разбирает Response, формирует и отправляет HTTP ответ сервера
     *
     * @param ResponseInterface $response
     */
    public function display($response = null)
    {
        $response = !empty($response) ? $response : $this->response;
        $size = $response->getBody()->getSize();
        if (null !== $size) {
            $response = $response->withHeader('Content-Length', (string)$size);
        }
        self::sendHeaders($response);
        self::sendBody($response);
    }

    /**
     * @param ResponseInterface $response
     */
    protected function sendHeaders($response)
    {
        if (!headers_sent()) {
            header(sprintf(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }
        }
    }

    /**
     * @param ResponseInterface $response
     */
    protected function sendBody($response)
    {
        $body = $response->getBody();
        if ($body->isSeekable()) {
            $body->rewind();
        }

        $chunkSize = 4096;
        $amountToRead = (int)$response->getHeaderLine('Content-Length');

        while ($amountToRead > 0 && !$body->eof()) {
            $data = $body->read(min($chunkSize, $amountToRead));
            print($data);
            $amountToRead -= mb_strlen($data, 'utf-8');
            if (connection_status() != CONNECTION_NORMAL) {
                break;
            }
        }
        exit();
    }
}
