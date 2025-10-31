<?php
namespace LazarusPhp\OpenHandler\CoreFiles;

use LazarusPhp\OpenHandler\Interfaces\ImageInterface;

class ImageHandler  extends HandlerCore
{

    protected array $allowedHelpers = [];
    protected array $allowedMethods = [""];


    public function __construct()
    {
        parent::__construct(__CLASS__);
    }


    public function upload(string $path)
    {
        $path = (string) $this->filePath($path);
        if($this->hasDirectory($path))
        {
            echo "Directory Exists";
        }
    }

}