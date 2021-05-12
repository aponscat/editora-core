<?php

namespace Omatech\Editora\Adapters;

class TestMediaAdapter implements MediaAdapterInterface
{
    private $files=[];

    public function __construct()
    {
    }

    public function exists(string $path): bool
    {
        return array_key_exists($path, $this->files);
    }

    public function put(string $path, $content)
    {
        $this->files[$path]=$content;
    }

    public function get(string $path)
    {
        return $this->files[$path];
    }
}
