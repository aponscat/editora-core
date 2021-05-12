<?php

namespace Omatech\Editora\Adapters;

interface MediaAdapterInterface
{
    public function exists(string $path): bool;
    public function put(string $path, $content);
    public function get(string $path);
}
