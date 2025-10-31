<?php
namespace LazarusPhp\OpenHandler;

use ReflectionClass;

class ErrorHandler
{


    private static $errors = [];

    public static function setError(array $values)
    {
         if (!is_array(self::$errors)) {
            self::$errors = [];
        }

        // Otherwise, append a new error entry
        self::$errors[] = [
            "class"  => (!in_array($values["class"],self::$errors)) ? $values["class"] : "",
            "type" => $values["type"],
            "message"  => $values["message"],
        ];
    }


    public static function getErrors()
    {
        return self::$errors;
    }

}