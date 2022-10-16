<?php

namespace http;


/**
 * Class Request
 * @package http
 */
class Request extends RequestAddition
{
    use MessageTrait;

    protected static $validMethods = [
        'GET'     => true,    
        'POST'    => true,
        'PUT'     => true,
        'DELETE'  => true,
        'OPTIONS' => true,
    ];
    
    protected $env;    
    protected $serverParams;
    protected $method;
    protected $uri;
    protected $cookieParams;
    protected $uploadedFiles;
    protected $requestTarget;
    protected $uriObject;
    protected $queryParams;
    protected $bodyParsed  = false;
    protected $bodyParsers = [];
    protected $attributes;
   
    /**
    * Конструктор
    */ 
    public function __construct($storage, $env = [])
    {
        $this->storage = $storage;
        $this->env = $env;
        $this->serverParams = $_SERVER;
        $this->setEnvHeaders($_SERVER);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = new Uri($storage);
        $this->registerBodyParsers();
    }
    
    /**
    * Создает новый объект Request
    *
    * @return object 
    */
    public function newRequest(
                               $uri           = null, 
                         array $headers       = null, 
                         array $cookies       = null,
                         array $serverParams  = null,
                               $body          = null, 
                         array $uploadedFiles = []
    ) {
        $new = new static($this->storage);

        if (isset($serverParams['SERVER_PROTOCOL'])) {
            $protocolVersion = $serverParams['SERVER_PROTOCOL'];
        } else {
            $protocolVersion = $this->env['SERVER_PROTOCOL'];
        }

        $new->protocolVersion = str_replace('HTTP/', '', $protocolVersion);
        
        if (null === $this->method) {
            $this->method = $this->env['REQUEST_METHOD'];
        }   
        
        $new->method = $new->filterMethod($this->method);
        
        if (is_string($uri)) {
            $new->uri = new Uri($uri);
        } elseif ($uri instanceof Uri) {
            $new->uri = $uri;
        } else {
            $new->uri = new Uri($this->storage);
        }
        
        if (null === $headers) {
            $headers = getallheaders();
            $new->setHeaders($headers);
        } else {
            $new->setEnvHeaders($this->env);
        }
        
        if (null !== $serverParams) {
            $new->serverParams = $serverParams;
        } else {
            $new->serverParams = $this->env;        
        }
        
        if (null !== $cookies) {
            $new->cookieParams = $cookies;
        }
        
        if (null !== $body && $body instanceof Stream) {
            $new->body = $body;
        } else {
            $new->body = new Stream('php://input', 'r');
        }
     
        $new->uploadedFiles = $uploadedFiles;
        return $new;   
    }
    
    /**
    * Возвращает цель запроса
    *
    * @return string
    */
    public function getRequestTarget()
    {
        if (!empty($this->requestTarget)) {
            return $this->requestTarget;
        }
     
        if (empty($this->uriObject)) {
            return '/';
        }
        
        $target = $this->uriObject->getPath();
        $query  = $this->uriObject->getQuery();
        
        if (!empty($query)) {
            $target .= '?' . $query;
        }
      
        if (empty($target)) {
            $target = '/';
        }
     
        return $target;
    }

    /**
    * Возвращает новый объект с установленной целью запроса.
    *
    * @param mixed $requestTarget
    *
    * @return object
    */
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new \InvalidArgumentException(ABC_HTTP_INVALID_TARGET);
        }
        $clone = clone $this;
        $clone->target = $requestTarget;
        return clone $this;
    }

    /**
    * Возвращает HTTP метод для запроса.
    *
    * @return string 
    */
    public function getMethod()
    {
        return $this->method;
    }

    /**
    * Возвращает новый объект с установленным HTTP методом.
    *
    * @param string $method 
    *
    * @return object
    */
    public function withMethod($method)
    {
        $clone = clone $this;
        $clone->method = $this->filterMethod($method);
        return $clone;
    }

    /**
    * Возвращает объект Uri.
    *
    * @return object
    */
    public function getUri()
    {
        return $this->uri; 
    }

    /**
    * Возвращает новый объект с установленным Uri
    *
    * @param UriInterface $uri
    * @param bool $preserveHost
    *
    * @return static
    */
    public function withUri($uri, $preserveHost = false)
    {
        if (!$uri instanceof Uri) {
            throw new \InvalidArgumentException(ABC_HTTP_OTHER_OBJECT);
        }
        
        $clone = clone $this;
        
        if (!$preserveHost) {
         
            if ($uri->getHost() !== '') {
                $new->host = $uri->getHost();
            }
            
        } else {
         
            if ($uri->getHost() !== '' && (!$this->hasHeader('host') || $this->getHeaderLine('host') === '')) {
                $new->host = $uri->getHost();
            }
        }
        
        $clone->uri = $uri;
        return $clone;
    }
    
/*-----------------------------------------------------    
        ServerRequest
-------------------------------------------------------*/
    /**
    * Получает параметры сервера ($_SERVER).
    *
    * @return array
    */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
    * Получает куки ($_COOKIE).
    *
    * @return array
    */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
    * Возвращает новый объект с установленными куками ($_COOKIE).
    *
    * @param array $cookies 
    *
    * @return static
    */
    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;
        $clone->cookieParams = $cookies;
        return $clone;
    }

    /**
    * Возвращает параметры query string ($_GET) в виде массива.
    *
    * @return array
    */
    public function getQueryParams()
    {
        if (!empty($this->queryParams) && is_array($this->queryParams)) {
            return $this->queryParams;
        }
     
        if (empty($this->uri)) {
            return [];
        }
      
        parse_str($this->uri->getQuery(), $this->queryParams);
        return $this->queryParams;
    }

    /**
    * Возвращает новый объект с установленной query string ($_GET).
    *
    * @param array $query 
    *
    * @return object
    */
    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $clone->queryParams = $query;
        return $clone;
    }

    /**
    * Возвращает нормализованные данные для загрузки файлов ($_FILES).
    *
    * @return array 
    */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
    * Возвращает новый объект с указанными загруженными файлами ($_FILES).
    *
    * @param array $uploadedFiles
    *
    * @return object
    */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;
        return $clone;
    }

    /**
    * Возвращает все параметры из body ($_POST).
    *
    * @return null|array|object 
    */
    public function getParsedBody()
    {
        if ($this->bodyParsed !== false) {
            return $this->bodyParsed;
        }

        if (!$this->body) {
            return null;
        }
    
        $mediaType = $this->getMediaType();
        $parts = explode('+', $mediaType);
        
        if (count($parts) >= 2) {
            $mediaType = 'application/' . $parts[count($parts)-1];
        }

        if (isset($this->bodyParsers[$mediaType]) === true) {
            $body = (string)$this->getBody();
            $parsed = $this->bodyParsers[$mediaType]($body);

            if (!is_null($parsed) && !is_object($parsed) && !is_array($parsed)) {
                throw new RuntimeException(HTTP_INVALID_BODY);
            }
            $this->bodyParsed = $parsed;
            return $this->bodyParsed;
        }

        return null;
    }

    /**
    * Возвращает новый объект с указанным параметром body ($_POST).
    *
    * @param null|array|object $data 
    *
    * @return object
    */
    public function withParsedBody($data)
    {
        $clone = clone $this;
        $clone->bodyParsed = $data;
        return $clone;
    }

    /**
    * Возвращает все атрибуты текщего запроса.
    *
    * @return array 
    */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
    * Возвращает один атрибут по указанному имени.
    *
    * @param string $name 
    * @param mixed $default
    *
    * @return mixed
    */
    public function getAttribute($name, $default = null)
    {
        if (!isset($this->attributes[$name])) {
            return $default;
        }
        
        return $this->attributes[$name];
    }

    /**
    * Возвращает объект, в котором добавлен указанный атрибут.
    *
    * @param string $name
    * @param mixed $value 
    *
    * @return object
    */
    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        return $clone;
    }

    /**
    * Возвращает объект, в котором удален указанный атрибут текущего запроса.
    *
    * @param string $name
    *
    * @return object
    */
    public function withoutAttribute($name)
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);
        return clone $this;
    }
    
/*-----------------------------------------------------    
        Хэлперы
-------------------------------------------------------*/    

    /**
    * Валидация HTTP методов
    *
    * @param  null|string $method
    *
    * @return null|string
    */
    protected function filterMethod($method)
    {
        if ($method === null) {
            return $method;
        }
     
        if (!is_string($method)) {
            throw new \InvalidArgumentException($method . ABC_HTTP_NO_METHOD);
        }
     
        $method = strtoupper($method);
        
        if (!isset(self::$validMethods[$method])) {
            throw new InvalidMethodException($this, $method);
        }
     
        return $method;
    }
    
    /**
     * Получает медиатип, если он есть
     *
     * @return string|null 
     */
    protected function getMediaType()
    {
        $contentType = $this->getContentType();
        
        if ($contentType) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
            return strtolower($contentTypeParts[0]);
        }
    } 

    /**
     * Получает тип контента.
     *
     * @return string|null 
     */
    protected function getContentType()
    {
        return $this->getHeader('Content-Type');
    }
    
    /**
    * Регистрация парсеров
    *
    * @param  null|string $method
    *
    * @return null|string
    */
    protected function registerBodyParsers()
    {    
        $this->registerMediaTypeParser('application/json', function ($input) {
            $result = json_decode($input, true);
            if (!is_array($result)) {
                return null;
            }
            return $result;
        });

        $this->registerMediaTypeParser('application/xml', function ($input) {
            $backup = libxml_disable_entity_loader(true);
            $backup_errors = libxml_use_internal_errors(true);
            $result = simplexml_load_string($input);
            libxml_disable_entity_loader($backup);
            libxml_clear_errors();
            libxml_use_internal_errors($backup_errors);
            if ($result === false) {
                return null;
            }
            return $result;
        });

        $this->registerMediaTypeParser('text/xml', function ($input) {
            $backup = libxml_disable_entity_loader(true);
            $backup_errors = libxml_use_internal_errors(true);
            $result = simplexml_load_string($input);
            libxml_disable_entity_loader($backup);
            libxml_clear_errors();
            libxml_use_internal_errors($backup_errors);
            if ($result === false) {
                return null;
            }
            return $result;
        });

        $this->registerMediaTypeParser('application/x-www-form-urlencoded', function ($input) {
            parse_str($input, $data);
            return $data;
        });
    }
    
    /**
    * Регистрация парсера
    *
    * @param string   $mediaType 
    * @param callable $callable
    */
    protected function registerMediaTypeParser($mediaType, callable $callable)
    {
        if ($callable instanceof Closure) {
            $callable = $callable->bindTo($this);
        }
        $this->bodyParsers[(string)$mediaType] = $callable;
    }
}
