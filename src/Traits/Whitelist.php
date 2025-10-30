<?php
namespace LazarusPhp\OpenHandler\Traits;
use ReflectionClass;

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
    
    
    private function validateHelper(string $method):bool
    {
       return (in_array($method,$this->allowedHelpers)) ? true : false;
    }

    private function validateMethod(string $method):bool
    {
       return (in_array($method,$this->allowedMethods)) ? true : false;
    }

    // Shorten the classname
    private function classname($class)
    {
        if(class_exists($class)){
            return new ReflectionClass($class)->getShortName();
        }
        else
        {
            $this->error($class,"Class $class does not exist");
        }
    }


    public function setHelper($method)
    {
        // Reject with error if method isnt in the array
        if(count($this->allowedHelpers) >= 1){
            if($this->hasMethod($method) === true)
            {
                if($this->validateHelper($method) === false)
                {
                    $this->error($method,"helper Name : $method Cannot be used in class : " .$this->classname(static::class));
                }
            }
        }
    }


    public function setMethod($method)
    {
        if(count($this->allowedMethods) >= 1){
            if($this->hasMethod($method) === true)
            {
                if($this->validateMethod($method) === false)
                {
                    $this->error($method,"Method Name : $method Cannot be used in class : " .$this->classname(static::class));
                }
            }
        }
    }

    public function error($method="",$error="")
    {
        if(!empty($method) && !empty($error))
        {
            if(!array_key_exists($method,$this->errors))
            {
                $this->errors[$method] = $error;
            }
        }
        else
        {
            return (object) $this->errors;
        }
    }

    public function hasErrors()
    {
        if(count($this->errors) >= 1)
        {
            return true;
        }
        return false;
    }
}