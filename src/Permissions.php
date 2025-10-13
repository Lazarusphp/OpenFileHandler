<?php

namespace Lazarusphp\FileHandler;

use App\System\Core\Functions;

abstract class Permissions
{
    protected $directory;

    public function __construct(string $directory)
    {
        if ($this->hasDirectory($directory) === true && $this->isReadable($directory) === true) {
            if (!$this->directory) {
                $this->directory = $directory;
            }
        }
    }



    // Whitelist for mode
    protected function validMode(int $mode)
    {
        $modes = [0600, 0644, 0664, 0700, 0755, 0777];

        if (in_array($mode, $modes)) {
            return true;
        } else {
            return false;
        }
    }

    protected function apacheUid($path)
    {
        $owner = fileowner($this->directory);
        $stats = posix_getpwuid($owner);
        Functions::dd($stats);
        return (object) $stats;
    }

    // Add Image filename.


    // Check if Readable and Writable

    protected  function isWriteable(string $path = ""): bool
    {
        $path = (!empty($path)) ? $path : $this->directory;
        return is_writable($path) ? true : false;
    }

    protected function isReadable(string $path = ""): bool
    {
        $path = (!empty($path)) ? $path : $this->directory;
        return is_readable($path) ? true : false;
    }

    // End CHeck for read and write

    // Check if path directory or file location exists

    protected  function hasFile(string $name): bool
    {

        $name = "{$this->directory}/$name";
        return (file_exists($name) && is_file($name)) ? true : false;
    }

    protected function hasDirectory(string $name = ""): bool
    {
        if (empty($name)) {
            $name = $this->directory;
        }

        // normalize trailing slash
        $name = rtrim($name, DIRECTORY_SEPARATOR);

        // Use instance isReadable (not static) so overrides work and proper path is used
        return (is_dir($name) && $this->isReadable($name)) ? true : false;
    }



    // Create Directory and SetPermissions

}
