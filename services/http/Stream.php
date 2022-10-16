<?php

namespace http;


/**
 * Class Stream
 * @package http
 */
class Stream
{
    const FSTAT_MODE_S_IFIFO = 0010000;

    protected static $modes = [
        'readable' => ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        'writable' => ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'],
    ];

    protected $stream;
    protected $meta;
    protected $readable;
    protected $writable;
    protected $seekable;
    protected $size;
    protected $isPipe;
    
    /**
    * Конструктор.
    *
    * @param resource $stream 
    * @param string $mode 
    */
    public function __construct($stream, $mode = 'r')
    {
        if (is_resource($stream)) {
            $this->stream = $stream;
        } elseif (is_string($stream)) {
            $this->stream = fopen($stream, $mode);
        } else {
            throw new \InvalidArgumentException(HTTP_INVALID_STREAM);
        }
    }
    
    /**
    * Поток, как строка
    */
    public function __toString()
    {
        
        if (false === is_resource($this->stream)) {
            return '';
        }
      
        try {
            $this->rewind();
            return $this->getContents();
        } catch (\RuntimeException $e) {
            return '';
        }
    }
    
    /**
    * Прикрепляет новый ресурс к текущему объекту.
    *
    * @param resource $newStream 
    *
    */
    public function attach($newStream)
    {
        if (false === is_resource($newStream)) {
            throw new \InvalidArgumentException(HTTP_NO_RESOURCE);
        }
     
        if (true === is_resource($this->stream)) {
            $this->detach();
        }
     
        $this->stream = $newStream;
    }
    
    /**
    * Закрывает поток и основные ресурсы.
    *
    * @return void
    */
    public function close()
    {
        if (true === is_resource($this->stream)) {
        
            if ($this->isPipe()) {
                pclose($this->stream);
            } else {
                fclose($this->stream);
            }
        }
     
        $this->detach();
    }

    /**
    * Удаляет ресурсы из потока.
    *
    * @return resource|null
    */
    public function detach()
    {
        $oldResource = $this->stream;
        $this->stream   = null;
        $this->meta     = null;
        $this->readable = null;
        $this->writable = null;
        $this->seekable = null;
        $this->size     = null;
        $this->isPipe   = null;
      
        return $oldResource;
    }

    /**
    * Получает размер потока, если он известен.
    *
    * @return int|null 
    */
    public function getSize()
    {
        if (empty($this->size) && true === is_resource($this->stream)) {
            $stats = fstat($this->stream);
            $this->size = isset($stats['size']) && !$this->isPipe() ? $stats['size'] : null;
        }
     
        return $this->size;
    }

    /**
    * Возвращает текущую позицию курсора файла
    *
    * @return int 
    */
    public function tell()
    {
        if (false === is_resource($this->stream) || false === ($position = ftell($this->stream)) || $this->isPipe()) {
            throw new \RuntimeException(HTTP_NO_POINTER);
            return false;
        }
     
        return $position;
    }

    /**
    * Возвращает true, если указатель находится в конце потока.
    *
    * @return bool
    */
    public function eof()
    {
        return feof($this->stream);
    }

    /**
    * Проверяет, доступен ли поток и возвращает его.
    *
    * @return bool
    */
    public function isSeekable()
    {
        if (null === $this->seekable) {
            $this->seekable = false;
            
            if (true === is_resource($this->stream)) {
                $seekable = $this->getMetadata('seekable');
                $this->seekable = (!$this->isPipe() && $seekable);
            }
        }
     
        return $this->seekable;
    }

    /**
    * Ищет позицию в потоке.
    *
    * @param int $offset 
    * @param int $whence 
    *
    * @return bool
    */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->isSeekable() || fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException(HTTP_NO_POINTER);
        }
    }

    /**
    * Возвращает на начало потока.
    * 
    *
    *
    */
    public function rewind()
    {
        if (!$this->isSeekable() || rewind($this->stream) === false) {
            throw new \RuntimeException(HTTP_NO_REWIND);
        }
    }

    /**
    * Проверяет, доступен ли поток для записи.
    *
    * @return bool
    */
    public function isWritable()
    {
        if ($this->writable === null) {
            $this->writable = false;
            
            if (true === is_resource($this->stream)) {;
                $meta = $this->getMetadata(true);
                
                foreach (self::$modes['writable'] as $mode) {
                
                    if (strpos($meta['mode'], $mode) === 0) {
                        $this->writable = true;
                        break;
                    }
                }
            }
        }
     
        return $this->writable;
    }
    
    /**
    * Проверяет, доступен ли поток для чтения
    *
    * @return bool
    */
    public function isReadable()
    {
        if (null === $this->readable) {
        
            if ($this->isPipe()) {
                $this->readable = true;
            } else {
                $this->readable = false;
                
                if (true === is_resource($this->stream)) {
                    $meta = $this->getMetadata(true);
                    
                    foreach (self::$modes['readable'] as $mode) {
                    
                        if (strpos($meta['mode'], $mode) === 0) {
                            $this->readable = true;
                            break;
                        }
                    }
                }
            }
        }
     
        return $this->readable;
    }

    /**
    * Записывает данные в поток.
    *
    * @param string $string 
    *
    * @return bool
    */
    public function write($string)
    {    
        if (!$this->isWritable() || false === ($written = fwrite($this->stream, $string))) {
            throw new \RuntimeException(ABC_HTTP_NO_WRITE);
        }
     
        $this->size = null;
        return $written;
    }
    
    /**
    * Читает данные из потока
    *
    * @param int $length 
    * @return string 
    */
    public function read($length)
    {
        if (!$this->isReadable() || ($data = fread($this->stream, $length)) === false) {
            throw new \RuntimeException(ABC_HTTP_NO_READ);
        }
        
        return $data;
    }

    /**
    * Возвращает оставшееся содержимое в виде строки
    *
    * @return string
    */
    public function getContents()
    {
        if (!$this->isReadable() || false === ($contents = stream_get_contents($this->stream, -1))) {
            throw new \RuntimeException(ABC_HTTP_NO_CONTENT);
        }
        $this->rewind();
        return $contents;
    }

    /**
    * Получает метаданные потока как ассоциативный массив, 
    * или ключу.
    *
    * @param string $key Specific metadata to retrieve.
    *
    * @return mixed 
    */
    public function getMetadata($key = null)
    {
        $this->meta = stream_get_meta_data($this->stream);
        
        if (true === $key) {
            return $this->meta;
        }
     
        return isset($this->meta[$key]) ? $this->meta[$key] : null;
    }
    
    /**
     * Проверяет, является ли поток каналом.
     *
     * @return bool
     */
    public function isPipe()
    {
        if (null === $this->isPipe) {
            $this->isPipe = false;
            
            if (true === is_resource($this->stream)) {
                $mode = fstat($this->stream)['mode'];
                $this->isPipe = ($mode & self::FSTAT_MODE_S_IFIFO) !== 0;
            }
        }
     
        return $this->isPipe;
    }
}
