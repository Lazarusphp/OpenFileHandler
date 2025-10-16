<?php
namespace LazarusPhp\OpenFileHandler\Traits;

trait Structure
{


    protected static function withDots($path)
    {
        return ($path === "." || $path === "..") ? true : false;
    }

    // Validate if path is Writeable
    protected static function writable(string $path): bool
    {
        return is_writable($path) ? true : false;
    }

    // Validate if path is readable
    protected static function readable(string $path): bool
    {
        return is_readable($path) ? true : false;
    }

}