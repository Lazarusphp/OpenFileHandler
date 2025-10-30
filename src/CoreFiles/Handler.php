<?php

namespace LazarusPhp\OpenHandler\CoreFiles;

use Exception;
use LazarusPhp\OpenFileHandler\Permissions;
use LazarusPhp\OpenHandler\Interfaces\HandlerInterface;
use LazarusPhp\OpenHandler\Interfaces\ImageInterface;
use LogicException;

class Handler extends HandlerCore implements HandlerInterface
{

    /**
     * @propertyy array $allowedHelpers
     * Lists allowed Helpers
     */
    protected array $allowedHelpers = ["hasFile",
    "hasDirectory","validMode","fileExists","filePath",
    "writable","readable","withDots"];

    /**
     * @property array $allowedMethods
     * Sets List of allowed  values for array.
     */
    protected array $allowedMethods = [
        "generateDirectory","generateList","generateFile","generateDelete",""];

    /**
     * @method __construct()
     * Used to set Directory on instantiation if a value is presented.
     * @param $directory
     * 
     * */
    public function __construct(string $directory="")
    {
        // Empty Constructor 
        if($directory !== "")
        {
            $this->setDirectory($directory);
        }

        parent::__construct();
    }


    /**
     * @method setDirectory
     * @param string $directory
     * can be used seperatly to override the directory set in constructor.
     * sets @property self::$directory  if directory exists.
     */
    public function setDirectory(string $directory="./")
    {
            if ($this->hasDirectory($directory) && self::writable($directory)) {
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
    public function directory(string $path, int $mode = 0755, bool $recursive = true)
    {
            return $this->generateDirectory($path, $mode, $recursive);     
     }

    /**
     * Delete a file or directory at the specified path.
     * @param string $path
     * @return bool
     */
    
    public function delete(string $path)
    {
        return  $this->generateDelete($path);
    }

    /**
     * Add a file with data at the specified filename.
     * @param string $filename
     * @param mixed $data
     * @return bool|int
     */

    public function file(string $path,array | int | string $data,$flags=0)
    {
        // $path = $this->filePath($path);

        //    return $this->generateFile($path,$data);
        
    }
    

    /**
     * List all directories and files
     * @property string $path
     * @property bool $recursive
     * @return array
     */
     

    public function list(string $path,$recursive=true,$files=true)
    {
        $path = self::$prefix !== "" ? $this->filePath($path) : $path;
        return $this->generateList($path,$recursive);
    }


    public function prefix(string $path, callable $handler, array $middleware = [])
    {
        return $this->generatePrefix($path, $handler, $middleware=[]);
    }



    public function breadcrumb()
    {
        return $this->generateBreadcrumb();
    }

    /**
     * @method upload
     * @param string $path
     * @param callable $image
     * @return @method $this->imageHandler()->upload($path,$image);
     */
    public function upload(string $path,callable $image)
    {
        // return $this->imageHandler->upload($path,$image);
    }
}