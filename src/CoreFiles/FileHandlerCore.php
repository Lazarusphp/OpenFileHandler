<?php
namespace LazarusPhp\OpenFileHandler\CoreFiles;

class FileHandlerCore
{

    protected static $directory = "";

    public function __construct($directory)
    {
        self::$directory = $directory;        
    }


    protected static function validMode(int $mode)
    {
        $modes = [0600, 0644, 0664, 0700, 0755, 0777];
        if (in_array($mode, $modes)) {
            return true;
        } else {
            return false;
        }
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

    protected static function hasFile($path)
    {
        return (is_file($path)) ? true : false;
    }

    protected static function fileExists($path)
    {
        return (file_exists($path)) ? true : false;
    }
}