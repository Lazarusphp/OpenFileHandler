<?php
namespace LazarusPhp\OpenHandler\Traits;

use LazarusPhp\OpenHandler\ErrorHandler;
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
            self::setError("Class","Invalid Class : $class does not exist");
        }
    }


    public function setHelper($method)
    {
        // Reject with error if method isnt in the array
        $message = "Invalid helper Name : $method Cannot be used in class : " .$this->classname(static::class);
        $values = ["type"=>"Helper","message"=>$message,"class"=>$this->classname];
        
        if(count($this->allowedHelpers) >= 1){
            if($this->hasMethod($method) === true)
            {
                if($this->validateHelper($method) === false)
                {
                    ErrorHandler::setError($values);
                }
            }
        }
    }

    public function setMethod($method)
    {
        $message = "Invalid Method Name : $method Cannot be used in class : " .$this->classname(static::class);
        $values = ["type"=>"method","message"=>$message,"class"=>$this->classname(static::class)];
        if(count($this->allowedMethods) >= 1){
            if($this->hasMethod($method) === true)
            {
                if($this->validateMethod($method) === false)
                {
                    ErrorHandler::setError($values);
                }
            }
        }
    }

}