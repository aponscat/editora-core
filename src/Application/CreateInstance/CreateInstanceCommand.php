<?php declare(strict_types=1);

namespace Omatech\Editora\Application\CreateInstance;

use Omatech\Editora\Application\Cms;
use Omatech\Editora\Domain\Data\Instance;

final class CreateInstanceCommand
{
    public Cms $cms;
    public Instance $instance;

    public function __construct(array $data)
    {
        assert($data['cms'] instanceof Cms);
        assert($data['instance'] instanceof Instance);
        $this->cms = $data['cms'];
        $this->instance = $data['instance'];
    }
}
