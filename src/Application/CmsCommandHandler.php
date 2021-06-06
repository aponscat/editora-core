<?php declare(strict_types=1);

namespace Omatech\Editora\Application;

use Omatech\Editora\Application\Cms;
use Omatech\Editora\Application\CmsCommand;

abstract class CmsCommandHandler
{

    private Cms $cms;

    public function __construct (Cms $cms)
    {
        $this->cms=$cms;
    }

    public function Cms()
    {
        return $this->cms;
    }

    public abstract function __invoke(CmsCommand $command);
}
