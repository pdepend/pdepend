<?php

namespace PDepend;

use PHPUnit_Framework_MockObject_MockBuilder;

class MockBuilder extends PHPUnit_Framework_MockObject_MockBuilder
{
    public function getMock()
    {
        if (version_compare(phpversion(), '7.4.0-dev', '<')) {
            return parent::getMock();
        }

        return @parent::getMock();
    }
}
