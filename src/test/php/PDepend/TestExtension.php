<?php

namespace PDepend;

use PDepend\DependencyInjection\Extension;

class TestExtension extends Extension
{
    public function getName()
    {
        return 'test';
    }
}
