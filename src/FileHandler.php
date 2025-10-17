<?php

namespace LazarusPhp\OpenFileHandler;

use LazarusPhp\OpenFileHandler\CoreFiles\FileHandlerCore;
use LazarusPhp\OpenFileHandler\Traits\Structure;
use LazarusPhp\OpenFileHandler\Traits\Permissions;

class FileHandler extends FileHandlerCore
{

    // use Structure;
    // use Permissions;

    // Create Mode
    public static function create($directory="")
    {
        self::generateRootDir($directory);
        return new static;
    }

    public static function prefix(string $prefix,callable $prefixClass)
    {
        if(is_callable($prefix))
        {
            // Call the Structure Class here
        }
    }

    // Delete Mode
 

   



    public static function addFile(string $filename,$data)
    {
        $supportedFiles = ["php","txt","tpl","class","env","json"];

        $extension = pathinfo($filename)["extension"];

        if(in_array($extension,$supportedFiles))
        {
            if(substr($filename,0,1) === DIRECTORY_SEPARATOR){
                $filename = self::useDirectory($filename);
                if(!file_exists($filename))
                {
                    return file_put_contents($filename,$data);
                }
            }
        }
        else
        {
            echo "Unsupported filetype";
        }
    }

    public static function createDirectory(string $path,int $mode = 0755, bool $recursive = true)
    {
        // Detect if  directory Exists
        $path = self::useDirectory($path);

        if(self::validMode($mode) === true)
        {
        // Create Folder if it doesnt exist
            if(self::hasDirectory($path) === false)
            {
                   $oldUmask = umask(0);
                    if (!mkdir($path,$mode,$recursive) && !self::hasDirectory($path)) {
                        umask($oldUmask);
                        throw new \RuntimeException("Failed to create directory: {$path}");
                    }
                    umask($oldUmask);
                    chmod($path, $mode);
            }
        }
        else
        {
            echo "Mode invalid";
        }
    }

    public static function listAll($path, $basePath = null)
{
    // On first call, set the base path for relative calculations
    if ($basePath === null) {
        $basePath = rtrim($path, DIRECTORY_SEPARATOR);
    }

    // Avoid double prefixing
    if (!empty(self::$directory) && strpos($path, self::$directory) !== 0) {
        $path = rtrim(self::$directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    if (self::hasDirectory($path) === false) {
        return ["folders" => [], "files" => []];
    }

    $result = ["folders" => [], "files" => []];

    $items = @scandir($path); // use @ to safely handle unreadable dirs
    foreach ($items ?: [] as $item) {

        if (self::withDots($item)) {
            continue;
        }

        $ds = DIRECTORY_SEPARATOR;
        $fullpath = rtrim($path, $ds) . $ds . ltrim($item, $ds);

        if (self::hasFile($fullpath) === true) {
            $result["files"][] = $fullpath;
        } elseif (self::hasDirectory($fullpath) === true) {
            $result["folders"][] = $fullpath;

            // Recursively list contents of subdirectories
            $subdir = self::listAll($fullpath, $basePath);

            // Ensure recursion always returns an array with both keys
            $subFolders = $subdir["folders"] ?? [];
            $subFiles = $subdir["files"] ?? [];

            $result["folders"] = array_merge($result["folders"], $subFolders);
            $result["files"] = array_merge($result["files"], $subFiles);
        }
    }

    return $result;
}

   public static function deleteDirectory($directory)
{
    // List all directories and files (returns ['folders'=>[], 'files'=>[]])
    $paths = self::listAll($directory);

    $paths = array_reverse($paths);
    $ds = DIRECTORY_SEPARATOR;

    foreach($paths["files"] as $file)
    {
        if(self::hasFile($file))
        {
            if(!unlink($file))
            {
                // Output Error to why it failed
            }
        }
    }
    foreach($paths["folders"] as $folder)
    {
        if(self::hasDirectory($folder));
        {
            if(!rmdir($folder))
            {
            // Output Error here
            }
        }
    }

    // Redeclare Directrory;
    $directory = self::useDirectory($directory);
    if(self::hasDirectory($directory))
    {
        if(!rmdir($directory))
        {
            // Output error handler message here 
        }
    }
    else
    {
        // Output directory not found error handler;
    }
    return true;
}


}