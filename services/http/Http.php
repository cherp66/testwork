<?php
 
namespace http;

use http\language\En;
En::set();

/**
 * Class Http
 * @package http
 */
class Http
{
    protected static $request;
    protected static $response;
    protected static $storage;

    public function __construct(array $config = null, array $env = null)
    {
        $language = '\http\language\\';
        $language .= !empty($config['debug']['language']) ? $config['debug']['language'] : 'En';
        $language::set();
    }

    /**
    * Инициализирует и возвращает объект Request.
    */
    public static function getRequest()
    {
        if (empty(self::$request)) {
            self::$request = (new Request(self::getStorage(), Environment::get()))->newRequest()
                ->withCookieParams($_COOKIE)
                ->withQueryParams($_GET)
                ->withParsedBody($_POST)
                ->withUploadedFiles(Uploader::normalizeFiles($_FILES)) ;
        }
        return self::$request;
    }

    /**
    * Инициализирует и возвращает объект Response.
    */
    public static function getResponse(
        $body = 'php://temp',
        $status  = 200,
        array $headers = []
    ) {
        if (empty(self::$response)) {
            self::$response = new Response(self::getStorage(), $body, $status, $headers);
        }
        return self::$response;
    }

    /**
     * Возвращает объект Storage.
     */
    public static function getStorage()
    {
        if (empty(self::$storage)) {
            self::$storage = new Storage();
        }

        return self::$storage;
    }

    /**
    * Инициализирует и возвращает объект Stream.
    */
    public static function createStream($stream, $mode = 'r')
    {
        return new Stream($stream, $mode);
    }

    /**
    * Инициализирует и возвращает объект UploadedFile.
    */
    public static function createUploadedFile(
        $file = null,
        $name = null,
        $type = null,
        $size = null,
        $error = UPLOAD_ERR_OK
    ) {
        return new UploadedFile($file, $name, $type, $size, $error);
    }
}
