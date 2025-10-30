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
    
    
    private function validateHelper(string $method):bool
    {
       return (in_array($method,$this->allowedHelpers)) ? true : false;
    }

    private function validateMethod(string $method):bool
    {
       return (in_array($method,$this->allowedMethods)) ? true : false;
    }


    public function setHelper($method)
    {
        // Reject with error if method isnt in the array
        if(count($this->allowedHelpers) >= 1){
            if($this->hasMethod($method) === true)
            {
                if($this->validateHelper($method) === false)
                {
                    echo "helper Name : $method Cannot be used in class : " . static::class  . "<br>";
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
                    echo "Method Name : $method Cannot be used in class : " . static::class  . "<br>";
                }
            }
        }
    }
}