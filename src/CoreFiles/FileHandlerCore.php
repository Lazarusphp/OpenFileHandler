<?php
namespace LazarusPhp\OpenFileHandler\CoreFiles;

use LazarusPhp\OpenFileHandler\Traits\Permissions;
use LazarusPhp\OpenFileHandler\Traits\Structure;

class FileHandlerCore
{

    protected static $directory = "";
    private static $prefix = "";
    // use Permissions;
    // use Structure;

    protected static function generateRootDir($directory)
    {
        if(!empty($directory))
        {
            if(self::hasDirectory($directory) && self::writable($directory)){
            self::$directory = $directory;
            }
        }
    }

    protected static function generatePrefix($prefix)
    {
        self::$prefix = $prefix;
    }


    // Check File Structure For Dots.
    protected static function removeDots($path)
    {
        if($path === "." || $path === "..")
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    protected static function useDirectory($directory)
    {
        return (empty(self::$directory)) ? $directory: self::$directory.$directory;
    }

    protected static function hasDirectory($path)
    {
        return is_dir($path) ? true : false;
    }

    protected static function hasPrefix($prefix)
    {
        self::$prefix = $prefix;
    }

    protected static function hasFile($path)
    {
        return (is_file($path)) ? true : false;
    }

    protected static function fileExists($path)
    {
        return (file_exists($path)) ? true : false;
    }

    // Structure 

    
    

    // Validate if path is readable
    protected static function readable(string $path): bool
    {
        return is_readable($path) ? true : false;
    }


}