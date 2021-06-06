<?php declare(strict_types=1);

namespace Omatech\Editora\Application;

class CmsCommand
{
    private array $data=[];

    public function __construct(array $data)
    {
        $this->validate($data);
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function validate($data)
    {
        
    }

}
