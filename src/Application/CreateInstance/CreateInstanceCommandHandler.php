<?php declare(strict_types=1);

namespace Omatech\Editora\Application\CreateInstance;

use Omatech\Editora\Application\Cms;
use Omatech\Editora\Application\CmsCommandHandler;
use Omatech\Editora\Application\CmsCommand;

final class CreateInstanceCommandHandler extends CmsCommandHandler
{
    public function __invoke(CmsCommand $command)
    {
        $instance=$this->Cms()->createArrayInstance($command->getData());
        $this->Cms()->createInstance($instance);
    }
}
