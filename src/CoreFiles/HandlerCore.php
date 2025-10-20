<?php
namespace LazarusPhp\OpenHandler\CoreFiles;

 class HandlerCore
{

    protected static $directory = "";
    protected static $prefix = "";




    /**
     * Require Access Token in order to Continue Prevent Calling methods directly;
     * 
     */
    /**
     * Detect if directory exists;
     * @property string $path
     * @return bool
     */

    protected static function hasDirectory(string $path):bool
   {
        return is_dir($path) ? true : false;
   }

//    Detect if file exists return bool

   /**
    * Detect if is a file
    * @property string $path;
    * @return bool 
    */
     protected static function hasFile(string $path):bool
    {
        return (is_file($path)) ? true : false;
    }

    /**
     * Detect if file exists
     * @property string $path;
     * @return bool
     */
     protected static function fileExists(string $path):bool
    {
        return (file_exists($path)) ? true : false;
    }

    /**
     * this method will be used to detect the directories and any prefix Attched to the file
     */

     protected static function useDirectory($directory)
    {
        $root = self::$directory;
        $prefix = self::$prefix ?? "";
        return $root.$prefix.$directory;
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



    /**
     *  Detect if the Structure contains parent directorys
     *  @property string $path
     *  @return bool
     */
     protected static function withDots($path):bool
    {
        return ($path === "." || $path === "..") ? true : false;
    }

    /**
     * Detect if directory is writable
     * @property string $path
     * @return bool
     */
     protected static function writable(string $path): bool
    {
        return is_writable($path) ? true : false;
    }

     protected static function readable(string $path):bool
    {
        return is_readable($path) ? true : false;
    }


    // Crud Elements

     protected static function addDirectory(string $path,int $mode = 0755, bool $recursive = true)
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


    protected function listAll(string $path, bool $recursive = false):array
    {
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

            if (self::withDots($item))  continue;

            $ds = DIRECTORY_SEPARATOR;
            $fullpath = rtrim($path, $ds) . $ds . ltrim($item, $ds);

            if (self::hasFile($fullpath) === true) {
                $result["files"][] = $fullpath;
            } elseif (self::hasDirectory($fullpath) === true) {
                $result["folders"][] = $fullpath;

                // Check if Request is set as recursive;
               
                    // Recursively list contents of subdirectories
                    $subdir = self::listAll($fullpath,$recursive);

                    // Ensure recursion always returns an array with both keys
                    $subFolders = $subdir["folders"] ?? [];
                    $subFiles = $subdir["files"] ?? [];

                    $result["folders"] = array_merge($result["folders"], $subFolders);
                    $result["files"] = array_merge($result["files"], $subFiles);
            }
        }

        // Return result;
        return $result;
    }


    protected function addFile(string $filename, int|string|array $data,int $flags=0)
    {
        $supportedFiles = ["php","txt","tpl","class","env","json"];
        $extension = pathinfo($filename)["extension"];
        if(in_array($extension,$supportedFiles))
        {
            if(substr($filename,0,1) === DIRECTORY_SEPARATOR){
                $filename = self::useDirectory($filename);
                if(!file_exists($filename))
                {
                    $output = $data;
                    if(file_put_contents($filename,$output,$flags))
                    {
                        return true;
                    }
                    // Ensure all code paths return a value
                    return false;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            // Error Here
            return false;
        }
    }



    protected function deleteData(string $path): bool
    {
        $path = self::useDirectory($path);
        if (self::hasFile($path)) {
            return unlink($path);
        } elseif (self::hasDirectory($path)) {
            $items = $this->listAll($path, true);
            foreach ($items['files'] as $file) {
                @unlink($file);
            }
            foreach (array_reverse($items['folders']) as $folder) {
                @rmdir($folder);
            }
            return @rmdir($path);
        }
        return false;
    }

}