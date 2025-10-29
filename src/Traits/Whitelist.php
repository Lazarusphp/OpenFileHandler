<?php
namespace LazarusPhp\OpenHandler\Traits;

trait Whitelist
{

    protected array $allowedHelpers = [];
    protected array $allowedMethods = [];

    // Set the method based on __function__

    private function hasMethod(string $method)
    {
        return method_exists($this,$method) ? true : false;
    }

    public function validMethod(string $method)
    {
        if($this->hasMethod($method)=== true)
        {
            if(isset($this->allowedMethods[$method])){
                if(is_array($this->allowedMethods) && in_array($method,$this->allowedMethods))
                {
                    return true;
                }
            }
            return false;
        }
    }
    
    
    private function validHelper(string $method):bool
    {
       return (in_array($method,$this->allowedHelpers) && is_array($this->allowedHelpers)) ? true : false;
    }

    public function setMethodWhitelist(string|array $method)
    {
      $count = is_array($method) ? count($method) : 1;
        $args = is_array($method) ? $method[0] : $method;
        if($count > 1)
        {
            foreach($args as $method)
            {
                if($this->validMethod($method) === false)
                {
                    throw new \Exception("Access to method $method is denied.");
                }
            }
        }
        else
        {
            if($this->validMethod($args) === false)
            {
                throw new \Exception("Access to method $args is denied.");
            }
        }
    }

    protected function setWhitelist(string|array $method)
    {
        $count = is_array($method) ? count($method) : 1;
        $args = is_array($method) ? $method[0] : $method;
        if($count > 1)
        {
            foreach($args as $method)
            {
                if($this->validHelper($args) === false)
                {
                    throw new \Exception("Access to method Helper $args is denied.");
                }
            }
        }
        else
        {
            if($this->validHelper($args) === false)
            {
                throw new \Exception("Access to method $args is denied.");
            }
        }

    
    }

}