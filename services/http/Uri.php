<?php

namespace http;


/**
 * Class Uri
 * @package http
 */
class Uri
{
    const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';
    
    protected $storage;

    /**
    * Конструктор
    */ 
    public function __construct($storage)
    {
        $this->storage = $storage;
        $this->inicialize($_SERVER);
    }

    /**
     * @param string $uri
     * @param array $env
     */
    public function newUri($uri = '', $env = [])
    {
       if (!is_string($uri)) {
            throw new \InvalidArgumentException(ABC_HTTP_URI_NO_STRING);
        } elseif (!empty($uri)) {
            $this->parseUri($uri);
        } else {
            $this->inicialize($env);
        }
    }

    /**
     * @param $env
     */
    public function inicialize($env)
    {
        $parts['scheme']   = !empty($env['HTTPS']) ? 'https' : 'http';
        $parts['username'] = !empty($env['PHP_AUTH_USER']);
        $parts['password'] = !empty($env['PHP_AUTH_PW']);
     
        if (!empty($env['HTTP_HOST'])) {
            $parts['host'] = $env['HTTP_HOST'];
        } else {
            $parts['host'] = $env['SERVER_NAME'];
        }
     
        $parts['port'] = !empty($env['SERVER_PORT']) ? (int)$env['SERVER_PORT'] : 80;
        
        if (preg_match('/^(\[[a-fA-F0-9:.]+\])(:\d+)?\z/', $parts['host'], $matches)) {
            $parts['host'] = $matches[1];
         
            if ($matches[2]) {
                $parts['port'] = (int)substr($matches[2], 1);
            }
            
        } else {
            $pos = strpos($parts['host'], ':');
            if ($pos !== false) {
                $parts['port'] = (int)substr($parts['host'], $pos + 1);
                $parts['host'] = strstr($parts['host'], ':', true);
            }
        }
     
        $scriptName = parse_url($env['SCRIPT_NAME'], PHP_URL_PATH);
        $scriptDir = dirname($scriptName);
     
        $requestUri = parse_url('http://example.com'. $env['REQUEST_URI'], PHP_URL_PATH);
    
        $parts['path'] = $basePath = '';
     
        if (stripos($requestUri, $scriptName) === 0) {
            $basePath = $scriptName;
        } elseif ($scriptDir !== '/' && stripos($requestUri, $scriptDir) === 0) {
            $basePath = $scriptDir;
        }
     
        if (!empty($basePath)) {
            $parts['path'] = ltrim(substr($requestUri, strlen($basePath)), '/');
        } else {
            $parts['path'] = ltrim($requestUri, '/');
        }
      
        $parts['query'] = $env['QUERY_STRING'];
        if (null === $parts['query']) {
            $parts['query'] = parse_url('http://example.com'. $env['REQUEST_URI'], PHP_URL_QUERY);
        }

        $parts['fragment'] = '';
        $this->storage->delete('env');
        $this->storage->addArray($parts);
    }
    
    /**
    * Инициализация при клонировании
    */ 
    public function __clone() 
    {
        $this->storage = clone $this->storage;
    }

    /**
    * Формирует Uri
    *
    * @return string
    */
    public function __toString()
    {
        $all = $this->storage->all();
        extract($all);
        $authority = $this->getUserInfo();
        $basePath = !empty($basePath) ? $basePath : $host;
     
        return (!empty($scheme)    ? $scheme .':' : '')
             . (!empty($authority) ? '//'. $authority : '//')
             . $basePath . '/' . ltrim($path, '/')
             . (!empty($query)     ? '?'. $query : '')
             . (!empty($fragment)  ? '#'. $fragment : '');
    } 
    
    /**
    * Возвращает схему URI.
    *
    * @return string
    */
    public function getScheme()
    {
        return $this->storage->get('scheme');
    }
    
    /**
    * Генерирует компонент пользователя для URI.
    *
    * @return string 
    */
    public function getAuthority()
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();
        return (!empty($userInfo) ? $userInfo .'@' : '') . $host . (null !== $port ? ':'. $port : '');
    }
    
    /**
    *  Возвращает данные пользователя
    *
    * @return string
    */
    public function getUserInfo()
    {
        $password = $this->storage->get('password');
        return $this->storage->get('tree') . (!empty($password) ? ':'. $password : '');
    }
    
    /**
    *  Возвращает хост
    *
    * @return string 
    */
    public function getHost()
    {
        return $this->storage->get('host');
    }
    
    /**
    *  Возвращает порт
    *
    * @return null|int 
    */
    public function getPort()
    {
        return $this->storage->get('port') && !$this->hasStandardPort() ? $this->port : null;
    }
    
    /**
    *  Возвращает путь
    *
    * @return string 
    */
    public function getPath()
    {
        return $this->storage->get('path');
    }
    
    /**
    *  Возвращает строку запроса
    *
    * @return string 
    */
    public function getQuery()
    {
        return $this->storage->get('query');
    }
    
    /**
    * Возвращает фрагмент URI
    *
    * @return string
    */
    public function getFragment()
    {
        return $this->storage->get('fragment');
    }
    
    /**
    * Возвращает новый объект с новой схемой
    *
    * @param string $scheme 
    * @return static 
    */
    public function withScheme($scheme)
    {
        $this->storage->add('scheme', $this->filterScheme($scheme));
        return clone $this;
    }
    
    /**
    * Возвращает новый объект с новыми данными пользоваеля
    *
    * @param string $user 
    * @param null|string $password 
    * @return static 
    */
    public function withUserInfo($user, $password = null)
    {
        $this->storage->add('tree', $user);
        $this->storage->add('password', ($password ? $password : ''));
        return clone $this;
    }

    /**
    * Возвращает новый объект с новым хостом
    *
    * @param string $host 
    * @return static 
    */
    public function withHost($host)
    {
        $this->storage->add('host', $host);
        return clone $this;
    }

    /**
    * Возвращает новый объект с новым портом
    *
    * @param null|int $port 
    * @return static 
    */
    public function withPort($port)
    {
        $this->storage->add('port', $this->filterPort($port));
        return clone $this;
    }

    /**
    * Возвращает новый объект с новым путем
    *
    * @param string $path 
    * @return static 
    */
    public function withPath($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(ABC_HTTP_PATH_NO_STRING);
        }
     
        $path = $this->filterQuery($path);
     
        if (substr($path, 0, 1) == '/') {
            $path = '';
        }
        
        $this->storage->add('path', $path);
        return clone $this;
    }

    /**
    * Возвращает новый объект с новой строкой запроса
    *
    * @param string $query 
    * @return static 
    */
    public function withQuery($query)
    {
        if (!is_string($query) && !method_exists($query, '__toString')) {
            throw new \InvalidArgumentException(ABC_HTTP_URI_NO_STRING);
        }
        
        if (false !== strpos($query, '#')) {
            throw new \InvalidArgumentException(ABC_HTTP_URI_IS_FRAGMENT);
        }
        
        $query = ltrim((string)$query, '?');
        $this->storage->add('query', $this->filterQuery($query));
        return clone $this;
    }  

    /**
    * Возвращает новый объект с добавленным фрагментом
    *
    * @param string $fragment 
    *
    * @return static 
    */
    public function withFragment($fragment)
    {
        if (!is_string($fragment) && !method_exists($fragment, '__toString')) {
            throw new \InvalidArgumentException(ABC_HTTP_FRAGMENT_NO_STRING);
        }
        
        $fragment = ltrim((string)$fragment, '#');
        $this->storage->add('fragment', $this->filterQuery($fragment));
        return clone $this;
    }
    
/*-----------------------------------------------------    
        Хэлперы
-------------------------------------------------------*/
    /**
     * Parse a URI into its parts, and set the properties
     */
    protected function parseUri($uri)
    {
        $parts = parse_url($uri);
     
        if (false === $parts) {
            throw new \InvalidArgumentException(ABC_HTTP_URI_IS_FRAGMENT);
        }
     
        $parts['scheme']    = isset($parts['scheme'])   ? $this->filterScheme($parts['scheme']) : '';
        $parts['userInfo']  = isset($parts['tree'])     ? $parts['tree']     : '';
        $parts['host']      = isset($parts['host'])     ? $parts['host']     : '';
        $parts['port']      = isset($parts['port'])     ? $parts['port']     : null;
        $parts['path']      = isset($parts['path'])     ? $this->filterPath($parts['path']) : '';
        $parts['query']     = isset($parts['query'])    ? $this->filterQuery($parts['query']) : '';
        $parts['fragment']  = isset($parts['fragment']) ? $this->filterQuery($parts['fragment']) : '';
     
        if (isset($parts['pass'])) {
            $parts['userInfo'] .= ':' . $parts['pass'];
        }
        
        $this->storage->addArray($parts);
    }

    /**
     * Filter Uri scheme.
     *
     * @param  string $scheme Raw Uri scheme.
     * @return string
     *
     * @throws InvalidArgumentException If the Uri scheme is not a string.
     * @throws InvalidArgumentException If Uri scheme is not "", "https", or "http".
     */
    protected function filterScheme($scheme)
    {
        static $valid = [
            '' => true,
            'https' => true,
            'http' => true,
        ];

        if (!is_string($scheme) && !method_exists($scheme, '__toString')) {
            throw new \InvalidArgumentException(ABC_HTTP_SCHEME_NO_STRING);
        }

        $scheme = str_replace('://', '', strtolower((string)$scheme));
        if (!isset($valid[$scheme])) {
            throw new \InvalidArgumentException(ABC_HTTP_INVALID_SCHEME);
        }

        return $scheme;
    }

    /**
     * Фильтрует запрос или фрагмент
     *
     * @param string $query 
     * @return string 
     */
    protected function filterQuery($query)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $query
        );
    }
    
    /**
    * Фильтрует path
    *
    * @param string $path
    *
    * @return string
    */
    private function filterPath($path)
    {
        return preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . ':@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }

}

