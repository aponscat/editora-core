<?php declare(strict_types=1);

namespace Omatech\Editora\Application;

class Command
{
    private array $data=[];

    public function __construct(?array $data)
    {
        $this->validate($data);
        $this->data = $data;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function validate(?array $data)
    {

    }

}
