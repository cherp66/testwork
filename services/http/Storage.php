<?php

namespace http;


/**
 * Class Storage
 * @package http
 */
class Storage
{
    private $names  = [];
    private $data   = [];
    

    /**
    * Добавляет данные в хранилище (осторожно, перезапись!)
    *
    * @param string $name
    * @param mixed $value
    * @param string $key
    * 
    */ 
    public function add($name, $value, $key = null)
    {
        if (!empty($key)) {
            $this->names[$key][strtolower($name)] = $name;        
            $this->data[$key][$name] = $value;
        } else {
            $this->names[strtolower($name)] = $name;
            $this->data[$name] = $value;
        }
    }
    
    /**
    * Добавляет данные в хранилище из массива (осторожно, перезапись!)
    *
    * @param string $key
    * @param string $name
    * @param mixed $value
    * 
    */ 
    public function addArray(array $array, $key = null)
    {
        foreach ($array as $name => $value) {
           $this->add($name, $value, $key);
        }
    }
    
    /**
    * Возвращает из хранилища данные по указанному ключу и имени
    *
    * @param string $key
    * @param string $name
    * 
    * @return mixed
    */ 
    public function get($name, $key = null)
    {
        if ($this->has($name, $key)) {
        
            if (null === $key) {
                $name  = $this->names[strtolower($name)];
                $value = $this->data[$name];
            } else {
                $name  = $this->names[$key][strtolower($name)];
                $value = $this->data[$key][$name];
            }
            
            return $value;
        }
        
        return null;
    } 
    
    /**
    * Возвращает из хранилища данные по указанному имени.
    * Если не найдено, вернет дефолтное значение
    *
    * @param string $key
    * @param string $name
    * 
    * @return mixed
    */ 
    public function getByName($name, $default = null)
    {
        if ($this->has($name)) {
            $name  = $this->names[strtolower($name)];
            return $this->data[$name];
        } 
      
        return $default;
    } 
    
    /**
    * Возвращает из хранилища все данные по указанному ключу
    *
    * @param string $key
    * 
    * @return mixed
    */ 
    public function all($key = null)
    {
        if (null === $key) {
            return $this->data;
        }
     
        $values = [];
        
        foreach ($this->names[$key] as $name) {   
            $values[$name] = $this->data[$key][$name];
        }
        
        return $values;
    } 

    /**
    * Проверяет наличие данных по ключу и имени
    *
    * @param string $key
    * @param string $name
    * 
    * @return bool
    */ 
    public function has($name, $key = null)
    {  
        if (null !== $key) {
            return isset($this->names[$key][strtolower($name)]);
        }
     
        return isset($this->names[strtolower($name)]);
    } 

    /**
    * Добавляет данные к уже существующим
    *
    * @param string $key
    * @param string $name
    * @param mixed $value
    * 
    */ 
    public function merge($name, $value, $key = null)
    {
        $current = [];
        if ($this->has($name, $key)) {
            $current = $this->get($name, $key);
            $value   = array_merge($current, $value);
        }
        
        $this->add($name, $value, $key);
    }
    
    /**
    * Удаляет из хранилища данные по указанному ключу и имени
    *
    * @param string $key
    * @param string $name
    * 
    */ 
    public function delete($name, $key = null)
    {
        if ($this->has($name, $key)) {
         
            if (null !== $key) {
                $name = $this->names[$key][strtolower($name)];
                unset($this->data[$key][$name]);
                unset($this->names[$key][strtolower($name)]);
            } else {
                $name = $this->names[strtolower($name)];
                unset($this->data[$name]);
                unset($this->names[strtolower($name)]);
            }
        }
    }
}
