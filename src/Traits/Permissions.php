<?php
namespace LazarusPhp\OpenFileHandler\Traits;

trait Permissions
{
    /**
     * Directory prefix used by methods in this trait.
     * Ensure it's defined to avoid "Undefined property" errors.
     */

        protected static function apacheUid($path)
    {
        // Build full path using configured directory if provided
        $fullPath = (self::$directory === "") ? $path : rtrim(self::$directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);

        // Ensure file exists and is readable
        if (!file_exists($fullPath)) {
            return null;
        }

        // Suppress warnings from fileowner for unexpected permission issues
        $owner = @fileowner($fullPath);
        if ($owner === false || $owner === null) {
            return null;
        }

        // Prefer posix_getpwuid when available, otherwise return owner id object
        if (function_exists('posix_getpwuid')) {
            $stats = posix_getpwuid($owner);
            if ($stats === false || $stats === null) {
                return null;
            }
            return (object) $stats;
        }

        return (object) ['uid' => $owner];
    }

}