<?php declare(strict_types=1);

namespace Omatech\Editora\Application\CreateInstance;

use Omatech\Editora\Domain\Data\Contracts\InstanceRepositoryInterface;

final class CreateInstanceCommandHandler
{
    private InstanceRepositoryInterface $instanceRepository;

    public function __construct(InstanceRepositoryInterface $instanceRepository)
    {
        $this->instanceRepository = $instanceRepository;
    }

    public function __invoke(CreateInstanceCommand $command)
    {

    }
}
