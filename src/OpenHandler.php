<?php
namespace LazarusPhp\OpenHandler;

use LazarusPhp\OpenFileHandler\CoreFiles\HandlerCore;
use LazarusPhp\OpenFileHandler\Interfaces\PermissionsInterface;
use LazarusPhp\OpenHandler\CoreFiles\Handler;
use LazarusPhp\OpenHandler\Interfaces\HandlerInterface;
use ReflectionClass;

class OpenHandler
{
    private static HandlerInterface $handlerInterface;


    private static  function reflection($classname)
    {
        return new ReflectionClass($classname);
    }

    public static function create(string $directory,array $handler =[Handler::class])
    {

        if(count($handler) === 1)
        {
            $handler = implode("",$handler);
        }
        else
        {
            trigger_error("Error Handler Must contain no more than one Class");
            return false;
        }

        

        $reflection = self::reflection($handler);
        if (class_exists($handler)) {
            if($reflection->isInstantiable()){
                self::$handlerInterface = new $handler($directory);
                self::$handlerInterface->setDirectory($directory);
                return self::$handlerInterface;
            }
            else
            {
                echo "Failed to instantiate new class";
            }
        } 
    }

    
}
