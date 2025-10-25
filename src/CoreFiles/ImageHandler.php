<?php
namespace LazarusPhp\OpenHandler\CoreFiles;

use LazarusPhp\OpenHandler\Interfaces\ImageInterface;

class ImageHandler  extends HandlerCore implements ImageInterface
{




    public function upload($path)
    {
        if($this->hasDirectory($path))
        {
            echo "Directory Exists";
        }
    }

}