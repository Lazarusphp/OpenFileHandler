<?php
namespace LazarusPhp\OpenHandler;

use LazarusPhp\OpenFileHandler\CoreFiles\HandlerCore;
use LazarusPhp\OpenFileHandler\Interfaces\PermissionsInterface;
use LazarusPhp\OpenHandler\CoreFiles\Handler;
use LazarusPhp\OpenHandler\Interfaces\HandlerInterface;

class OpenHandler
{
    private static HandlerInterface $handlerInterface;
    private static $flag = [];

    public static function create($directory)
    {

        if (class_exists(Handler::class)) {
            self::$handlerInterface = new Handler($directory);
            self::$handlerInterface->setDirectory($directory);
            return self::$handlerInterface;
        }
    }

    
}
