<?php

namespace Lazarusphp\FileHandler;

use App\System\Core\Functions;
use App\System\Core\Structure\StructurePermissions;

class FileHandler Extends Permissions
{

    

    /**
     * Structure Class 
     * Designed to handle files and directorys including creation and deletion
     * 
     *
     * @var array
     */


    // Add Supported filetypes


    public function __construct(string $directory)
    {
        $this->directory = null;
        parent::__construct($directory);
        
    }
    
    
    public function addFile(string $filename,$data)
    {
        if(substr($filename,0,1) === DIRECTORY_SEPARATOR){
            $filename = $this->directory.$filename;
            if(!file_exists($filename))
            {
                return file_put_contents($filename,$data);
            }
        }
    }

    public function createDirectory(string $path,int $mode = 0755, bool $recursive = true)
    {
        // Detect if  directory Exists
        $path = (!empty($this->directory)) ? $this->directory.$path : $path;

        if($this->validMode($mode) === true)
        {
        // Create Folder if it doesnt exist
            if($this->hasDirectory($path) === false)
            {
                   $oldUmask = umask(0);
                    if (!mkdir($path,$mode,$recursive) && !is_dir($path)) {
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

    public function listFolders($path, $basePath = null)
{
    // On first call, set the base path for relative calculations
    if ($basePath === null) {
        $basePath = rtrim($path, DIRECTORY_SEPARATOR);
    }

    // Avoid double prefixing
    if (!empty($this->directory) && strpos($path, $this->directory) !== 0) {
        $path = rtrim($this->directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    if ($this->hasDirectory($path) === false) {
        return ["folders" => [], "files" => []];
    }

    $result = ["folders" => [], "files" => []];

    $items = @scandir($path); // use @ to safely handle unreadable dirs
    foreach ($items ?: [] as $item) {

        if ($item === '.' || $item === '..') {
            continue;
        }

        $ds = DIRECTORY_SEPARATOR;
        $fullpath = rtrim($path, $ds) . $ds . ltrim($item, $ds);

        if (is_file($fullpath)) {
            $result["files"][] = $fullpath;
        } elseif (is_dir($fullpath)) {
            $result["folders"][] = $fullpath;

            // Recursively list contents of subdirectories
            $subdir = $this->listFolders($fullpath, $basePath);

            // Ensure recursion always returns an array with both keys
            $subFolders = $subdir["folders"] ?? [];
            $subFiles = $subdir["files"] ?? [];

            $result["folders"] = array_merge($result["folders"], $subFolders);
            $result["files"] = array_merge($result["files"], $subFiles);
        }
    }

    return $result;
}

   public function deleteDirectory($directory)
{
    // List all directories and files (returns ['folders'=>[], 'files'=>[]])
    $paths = $this->listFolders($directory);

    $paths = array_reverse($paths);
    $ds = DIRECTORY_SEPARATOR;

    foreach($paths["files"] as $file)
    {
        if(is_file($file))
        {
            if(unlink($file))
            {
                echo "deleted File : $file";
            }
        }
    }
    foreach($paths["folders"] as $folder)
    {
        if(is_dir($folder));
        {
            if(rmdir($folder))
            {
                echo "Deleted Folder : $folder" ;
            }
        }
    }

    if(is_dir($this->directory.$directory))
    {
        if(rmdir("{$this->directory}$directory"))
        {
            echo "Deleted Directory";
        }
    }

    return true;
}


}