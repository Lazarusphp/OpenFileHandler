<?php
namespace LazarusPhp\OpenHandler;

use ReflectionClass;

class ErrorHandler
{


    private static $errors = [];
    

    public static function setError($method,string $type,string $message)
    {
         if (!is_array(self::$errors)) {
            self::$errors = [];
        }


        // Otherwise, append a new error entry
        self::$errors[] = [
            "type" => $type,
            "message"  => $message,
        ];
    }

    public static function countErrors()
    {
        if(count(self::$errors) === 0)
        {
            return true;
        }
        return false;
    }

    public static function getErrors()
    {
        return self::$errors;
    }

}