<?php declare(strict_types=1);

namespace Omatech\Editora\Application\CreateInstance;

use Omatech\Editora\Application\Cms;
use Omatech\Editora\Application\CmsCommandHandler;
use Omatech\Editora\Application\CmsCommand;

final class CreateInstanceCommand extends CmsCommand
{
    public function validate($data)
    {
        if (!isset($data['metadata']))
        {
            throw new \Exception("No metadata found in Instance array");
        }
        if (!isset($data['metadata']['key']))
        {
            throw new \Exception("No key found in Instance array");
        }
        if (!isset($data['metadata']['class']))
        {
            throw new \Exception("No class found in Instance array");            
        }
    }
}