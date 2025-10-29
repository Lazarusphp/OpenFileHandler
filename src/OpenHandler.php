<?php
namespace LazarusPhp\OpenHandler;

use LazarusPhp\OpenFileHandler\CoreFiles\HandlerCore;
use LazarusPhp\OpenFileHandler\Interfaces\PermissionsInterface;
use LazarusPhp\OpenHandler\CoreFiles\Handler;
use LazarusPhp\OpenHandler\Interfaces\HandlerInterface;

class OpenHandler
{
    private static HandlerInterface $handlerInterface;

    public static function create(string $directory,$customhandler ="")
    
    {
        $handler =( empty($customhandler)) ? Handler::class : $customhandler;
        if (class_exists($handler)) {
            $handler = explode("::",$handler);
            self::$handlerInterface = new $handler[0]($directory);
            self::$handlerInterface->setDirectory($directory);
            return self::$handlerInterface;
        } 
    }

    
}
