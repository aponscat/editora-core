<?php declare(strict_types=1);

namespace Omatech\Editora\Application\CreateInstance;

use Omatech\Editora\Domain\Data\Contracts\InstanceRepositoryInterface;

final class CreateInstanceCommandHandler
{
    public function __invoke(CreateInstanceCommand $command)
    {
        $command->cms->createInstance($command->instance);
    }
}
