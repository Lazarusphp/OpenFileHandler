<?php
namespace LazarusPhp\OpenHandler\Interfaces;

interface HandlerInterface
{
    public function list(string $directory,bool $recursive=true,$files=true);
    public function directory(string $path,int $mode=0755, bool $recursive=true);
    public function delete(string $path);
    public function file(string $path, int|string|array $data,int $flags=0);
    public function prefix(string $path, callable $handler,array $middleware=[]);
    public function setDirectory(string $directory="");
    public function upload(string $path,callable $handler);
    public function breadcrumb();
}

