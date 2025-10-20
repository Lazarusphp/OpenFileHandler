<?php

namespace LazarusPhp\OpenHandler\CoreFiles;

use Exception;
use LazarusPhp\OpenHandler\Interfaces\HandlerInterface;

class Handler extends HandlerCore implements HandlerInterface
{


    
    public function __construct()
    {
        // Empty Constructor   
    }

    public function setDirectory($directory="./")
    {
            if (self::hasDirectory($directory) && self::writable($directory)) {
                // Create the directory
                self::$directory = $directory;
            } else {
                // Trigger Error
                trigger_error("$directory cannot be found or is not writable");
            }

    }

    /**
     * Add a directory at the specified path.
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function directory(string $path, int $mode = 0755, bool $recursive = true): bool
    {
        try {
            self::addDirectory($path, $mode, $recursive);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete a file or directory at the specified path.
     * @param string $path
     * @return bool
     */
    
    public function delete($path)
    {
        try{
            self::deleteData($path);
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
        
    }

    /**
     * Add a file with data at the specified filename.
     * @param string $filename
     * @param mixed $data
     * @return bool|int
     */

    public function file(string $path,array | int | string $data,$flags=0)
    {
        $path = self::useDirectory($path);

        try
        {
            self::addFile($path,$data);
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }
    

    /**
     * List all directories and files
     * @property string $path
     * @property bool $recursive
     * @return array
     */
     

    public function list($path,$recursive=false)
    {
         $path = self::$prefix !== "" ? self::useDirectory($path) : $path;
       
        if(self::hasDirectory($path)){
        return $this->listAll($path,$recursive);
        }
    }

       public function prefix(string $path, callable $handler, array $middleware = [])
    {


        
        if(self::$prefix === ""){
        self::$prefix = $path;
        }

        // Add MiddleWare Option here
        

        if (is_callable($handler)) {
            $class = new self();
            $handler($class,$path);
        }
        // Reset Prefix to start a new one
        self::$prefix = "";
        return null;
    }

}





//     

//    public static function deleteDirectory($directory)
// {
//     // List all directories and files (returns ['folders'=>[], 'files'=>[]])
//     $paths = self::listAll($directory);

//     $paths = array_reverse($paths);
//     $ds = DIRECTORY_SEPARATOR;

//     foreach($paths["files"] as $file)
//     {
//         if(self::hasFile($file))
//         {
//             if(!unlink($file))
//             {
//                 // Output Error to why it failed
//             }
//         }
//     }
//     foreach($paths["folders"] as $folder)
//     {
//         if(self::hasDirectory($folder));
//         {
//             if(!rmdir($folder))
//             {
//             // Output Error here
//             }
//         }
//     }
