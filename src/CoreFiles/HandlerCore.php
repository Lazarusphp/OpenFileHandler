<?php

namespace LazarusPhp\OpenHandler\CoreFiles;

use App\System\Core\Functions;
use Exception;
use BadMethodCallException;
use LazarusPhp\OpenHandler\ErrorHandler;
use LazarusPhp\OpenHandler\Permissions;
use LazarusPhp\OpenHandler\Traits\Blacklist;
use ReflectionClass;

/**
 * @abstract class HandlerCore
 * Cannot be called statically or as a new Instantiation.
 * is required only for use with OpenHandler Handler class.
 */

abstract class HandlerCore
{
    use Blacklist;
    protected static $directory = "";
    protected static $prefix = "";
    protected $permissions;
    protected array $restricted = [];
    private $method = __FUNCTION__;
    private $classname;


    public function __construct()
    {
        $this->permissions = new Permissions();
    }

    public function classname()
    {
        return static::class;
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
        if (method_exists(self::class, $name)) {
            echo "Class Exists";
        }

        trigger_error("Method : $name cannot be found or does not exist", E_USER_WARNING);
    }

    // Helper methods 

    /**
     * @method hasDirectory
     * @property string $path
     * @return void
     * Helper function to detect if a directory exists.
     */
    protected function hasDirectory(string $path)
    {
        return is_dir($path) ? true : false;
    }

    //    Detect if file exists return bool

    /**
     * @method hasFile
     * Detect if is a file
     * @property string $path;
     * @return bool 
     */
    protected function hasFile(string $path)
    {
        if ($this->loadMethod(__FUNCTION__)) {
            return (string) (is_file($path)) ? true : false;
        }
    }

    /**
     * Detect if file exists
     * @property string $path;
     * @return bool
     */
    protected function fileExists(string $path)
    {
        if ($this->loadMethod(__FUNCTION__)) {
            return (file_exists($path)) ? true : false;
        }
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
        if ($this->loadMethod(__FUNCTION__)) {
            $root = self::$directory;
            $prefix = self::$prefix ?? "";
            $directory = $directory;

            return (string) $root . $prefix . $directory;
        }
    }

    /**
     * @method validMode
     * @property int $mode
     * Detrermines if the correct mode for directory creation is valid
     */
    protected function validMode(int $mode)
    {

        if ($this->loadMethod(__FUNCTION__)) {
            $modes = [0600, 0644, 0664, 0700, 0755, 0777];
            if (in_array($mode, $modes)) {
                return true;
            } else {
                return false;
            }
        }
    }



    /**
     *  Detect if the Structure contains parent directorys
     *  @property string $path
     *  @return bool
     */
    protected function withDots($path)
    {
        if ($this->loadMethod(__FUNCTION__) === true) {
            return ($path === "." || $path === "..") ? true : false;
        }
    }

    /**
     * Detect if directory is writable
     * @property string $path
     * @return bool
     */
    protected function writable(string $path)
    {
        if ($this->loadMethod(__FUNCTION__) === true) {
            return is_writable($path) ? true : false;
        }
    }

    protected  function readable(string $path)
    {
        if ($this->loadMethod(__FUNCTION__)) {
            return is_readable($path) ? true : false;
        }
    }


    // Generative Methods.

    /**
     * @method generateDirectory
     * @property string $path
     * @property int $mode
     * @property boot $recursive
     */
    protected function generateDirectory(string $path, int $mode = 0755, bool $recursive = true)
    {

        if ($this->loadMethod(__FUNCTION__) === true) {
            // Detect if  directory Exists
            $path = (!empty($path)) ? $this->filePath($path) : "";
            if ($this->validMode($mode) === true) {
                // Create Folder if it doesnt exist
                if ($this->hasDirectory($path) === false) {
                    $oldUmask = umask(0);
                    if (!mkdir($path, $mode, $recursive) && !$this->hasDirectory($path)) {
                        umask($oldUmask);
                        throw new \RuntimeException("Failed to create directory: {$path}");
                    }
                    umask($oldUmask);
                    chmod($path, $mode);
                }
            } else {
                echo "Mode invalid";
            }
        }
    }

    protected function generatePrefix(string $path, callable $handler, array $middleware = [])
    {
        // $method = __FUNCTION__;
        // $this->setRestrict();
        if ($this->loadMethod(__FUNCTION__) === true) {
            if (self::$prefix === "") {
                self::$prefix = $path;
            }
            // Add MiddleWare Option here

            if (is_callable($handler)) {
                $class = new Handler();
                $handler($class, $path);
                self::$prefix = "";
            }
            // Reset Prefix to start a new oneself
            return null;
        }
    }

    protected function generateList(string $path, bool $recursive = true, $files = true)
    {
        if ($this->loadMethod(__FUNCTION__)) {
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

                if ($files === true) {
                    if ($this->hasFile($fullpath) === true) {
                        $result["files"][] = $fullpath;
                    }
                }

                if ($recursive === true) {
                    if ($this->hasDirectory($fullpath) === true) {
                        $result["folders"][] = $fullpath;

                        // Check if Request is set as recursive;

                        // Recursively list contents of subdirectories
                        $subdir = $this->generateList($fullpath, $recursive);

                        // Ensure recursion always returns an array with both keys
                        $subFolders = $subdir["folders"] ?? [];
                        $subFiles = $subdir["files"] ?? [];

                        $result["folders"] = array_merge($result["folders"], $subFolders);
                        ($files === true) ? $result["files"] = array_merge($result["files"], $subFiles) : false;
                    }
                }
            }

            // Return result;
            return $result;
        }
    }





    protected function generateFile(string $filename, int|string|array $data, int $flags = 0)
    {

        if ($this->loadMethod(__FUNCTION__)) {
            $supportedFiles = ["php", "txt", "tpl", "class", "env", "json"];
            $extension = pathinfo($filename)["extension"];
            if (in_array($extension, $supportedFiles)) {
                if (substr($filename, 0, 1) === DIRECTORY_SEPARATOR) {
                    $filename = (string) $this->filePath($filename);
                    if (!file_exists((string) $filename)) {
                        $output = $data;
                        if (file_put_contents((string) $filename, $output, $flags)) {
                            return true;
                        }
                        // Ensure all code paths return a value
                        return false;
                    } else {
                        return false;
                    }
                }
            } else {
                // Error Here
                return false;
            }
        }
    }

    protected function generateDelete(string $path)
    {

        if ($this->loadMethod(__FUNCTION__)) {
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

    protected function generateUpload(string $path, string $name)
    {

        if ($this->loadMethod(__FUNCTION__)) {
            $path = (string) $this->filePath($path);

            if ($this->hasDirectory($path)) {
                $ds = DIRECTORY_SEPARATOR;
                if (isset($_FILES[$name])) {
                    $files = $_FILES[$name];
                    // Check if name is in array
                    if (is_array($files["name"])) {
                        foreach ($files["name"] as $index => $name) {
                            if (!isset($files["tmp_name"][$index])) {
                                continue;
                            }

                            $tmp_name = $files["tmp_name"][$index];
                            $safename = basename($name);
                            $destination = $path . $ds . uniqid('img_', true) . "_$safename";
                            if (move_uploaded_file($tmp_name, $destination)) {
                                echo "Uploaded files";
                            } else {
                                echo "failed to upload";
                            }
                        }
                    }
                }
            } else {
                echo "Directory Does not exist";
            }
        }
    }
}
