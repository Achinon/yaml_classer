<?php

namespace Achinon\YamlClasserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class YamlClasserBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}