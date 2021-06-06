<?php declare(strict_types=1);

namespace Omatech\Editora\Application;

use Omatech\Editora\Application\Contracts\CmsInterface;
use Omatech\Editora\Application\Command;

abstract class CommandHandler
{

    private CmsInterface $cms;

    public function __construct (CmsInterface $cms)
    {
        $this->cms=$cms;
    }

    public function Cms(): CmsInterface
    {
        return $this->cms;
    }

    public abstract function __invoke(Command $command);
}
