<?php declare(strict_types=1);

namespace Omatech\Editora\Application\CreateInstance;

use Omatech\Editora\Application\CommandHandler;
use Omatech\Editora\Application\Command;

final class CreateInstanceCommandHandler extends CommandHandler
{
    public function __invoke(Command $command)
    {
        $instance=$this->Cms()->createInstanceFromArray($command->getData());
        $this->Cms()->createInstance($instance);
    }
}
