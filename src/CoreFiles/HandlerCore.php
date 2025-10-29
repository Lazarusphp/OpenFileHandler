<?php
namespace LazarusPhp\OpenHandler\CoreFiles;

use App\System\Core\Functions;
use Exception;
use LazarusPhp\OpenHandler\Traits\Whitelist;
use BadMethodCallException;
use LazarusPhp\OpenHandler\Permissions;

/**
 * @abstract class HandlerCore
 * Cannot be called statically or as a new Instantiation.
 * is required only for use with OpenHandler Handler class.
 */

abstract class HandlerCore
{
    use Whitelist;
    protected static $directory = "";
    protected static $prefix = "";
    protected $permissions;

    public function __construct()
    {
        $this->permissions = new Permissions();
    }

    /**
     * Require Access Token in order to Continue Prevent Calling methods directly;
     * 
     */
    /**
     * Detect if directory exists;
     * @property string $path
     * @return bool
     */


    /**
     * @method __call
     * Detects if a dynamic method has been created and rejects it.
     */
    public function __call($name, $arguments)
    {
        if($this->hasMethod($name)===false)
        {
            throw new BadMethodCallException("Method $name does not exist in ".get_class($this));   
            exit();
        }
    }

    /**
     * @method hasDirectory
     * @property string $path
     * @return void
     * Helper function to detect if a directory exists.
     */
    protected function hasDirectory(string $path):bool
    {
        $this->setWhitelist(__FUNCTION__);
        return is_dir($path) ? true : false;
    }

//    Detect if file exists return bool

   /**
    * @method hasFile
    * Detect if is a file
    * @property string $path;
    * @return bool 
    */
     protected function hasFile(string $path):bool
    {
        $this->setWhitelist(__FUNCTION__);
        return (string) (is_file($path)) ? true : false;
    }

    /**
     * Detect if file exists
     * @property string $path;
     * @return bool
     */
     protected function fileExists(string $path):bool
    {
        $this->setWhitelist(__FUNCTION__);
        return (file_exists($path)) ? true : false;
    }

    /**
     * @method hasDirectory
     * @property string $path
     * @method $this->whitelist() does a check for whitelisted values set within handler.
     * @return void
     * Helper function to detect if a directory exists.
     */
    protected function filePath(string $directory)
    {
        $this->setWhitelist(__FUNCTION__);
        $root = self::$directory;
        $prefix = self::$prefix ?? "";
        $directory = $directory;
    
        return (string) $root.$prefix.$directory;

    }

    /**
     * @method validMode
     * @property int $mode
     * Detrermines if the correct mode for directory creation is valid
     */
     protected function validMode(int $mode)
    {
        $this->setWhitelist(__FUNCTION__);
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
     protected function withDots($path):bool
    {
        $this->setWhitelist(__FUNCTION__);
        return ($path === "." || $path === "..") ? true : false;
    }

    /**
     * Detect if directory is writable
     * @property string $path
     * @return bool
     */
     protected function writable(string $path): bool
    {
        $this->setWhitelist(__FUNCTION__);
        return is_writable($path) ? true : false;
    }

     protected  function readable(string $path):bool
    {
        $this->setWhitelist(__FUNCTION__);
        return is_readable($path) ? true : false;
    }


    // Crud Elements

    /**
     * @method generateDirectory
     * @property string $path
     * @property int $mode
     * @property boot $recursive
     */
     protected function generateDirectory(string $path,int $mode = 0755, bool $recursive = true)
    {
        // Detect if  directory Exists
        $path = (!empty($path)) ? $this->filePath($path) : "";
        if($this->validMode($mode) === true)
        {
        // Create Folder if it doesnt exist
            if($this->hasDirectory($path) === false)
            {
                   $oldUmask = umask(0);
                    if (!mkdir($path,$mode,$recursive) && !$this->hasDirectory($path)) {
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

    protected function generatePrefix(string $path,callable $handler,array $middleware=[])
    {
        
        if(self::$prefix === ""){
        self::$prefix = $path;
        }
        // Add MiddleWare Option here

        if (is_callable($handler)) {
            $class = new Handler();
            $handler($class,$path);
            self::$prefix = "";
        }
        // Reset Prefix to start a new oneself
        return null;
    }

    protected function generateList(string $path, bool $recursive =true,$files=true):array
    {
       
        // Avoid double prefixing
        if (!empty(self::$directory) && strpos($path, self::$directory) !== 0) {
            $path = rtrim(self::$directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
        }


        if ($this->hasDirectory($path) === false) {
            return ["folders" => [], "files" => []];
        }

        $result = ["folders" => [], "files" => []];

        $items = @scandir($path); // use @ to safely handle unreadable dirs

        foreach ($items ?: [] as $item) {

            if ($this->withDots($item))  continue;

            $ds = DIRECTORY_SEPARATOR;
            $fullpath = (string) rtrim($path, $ds) . $ds . ltrim($item, $ds);

            if($files === true){
                if ($this->hasFile($fullpath) === true) {
                    $result["files"][] = $fullpath;
                }
            }
            
            if($recursive === true){
                if ($this->hasDirectory($fullpath) === true) {
                    $result["folders"][] = $fullpath;

                    // Check if Request is set as recursive;
                
                        // Recursively list contents of subdirectories
                        $subdir = $this->generateList($fullpath,$recursive);

                        // Ensure recursion always returns an array with both keys
                        $subFolders = $subdir["folders"] ?? [];
                        $subFiles = $subdir["files"] ?? [];

                        $result["folders"] = array_merge($result["folders"], $subFolders);
                        ($files ===true) ? $result["files"] = array_merge($result["files"], $subFiles) : false;
                }
            }
        }

        // Return result;
        return $result;
    }




    protected function generateBreadcrumb()
    {
       
        $path = ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["path"])) ? $_GET["path"] : self::$directory;
        return htmlspecialchars($path);
    }

    protected function generateFile(string $filename, int|string|array $data,int $flags=0)
    {
       
        $supportedFiles = ["php","txt","tpl","class","env","json"];
        $extension = pathinfo($filename)["extension"];
        if(in_array($extension,$supportedFiles))
        {
            if(substr($filename,0,1) === DIRECTORY_SEPARATOR){
                $filename = (string) $this->filePath($filename);
                if(!file_exists((string) $filename))
                {
                    $output = $data;
                    if(file_put_contents((string) $filename,$output,$flags))
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
    protected function generateDelete(string $path): bool
    {
        $path = (string) $this->filePath($path);

        if ($this->hasFile($path)) {
            return unlink($path);
        } elseif ($this->hasDirectory($path ?? '')) {
            $items = $this->generateList($path, true);
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