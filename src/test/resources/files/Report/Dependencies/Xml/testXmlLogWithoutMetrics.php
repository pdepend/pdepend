<?php

namespace Report\Dependencies\Xml;

abstract class AbstractBase
{

}

interface SomeInterface
{

}

class Used implements SomeInterface
{

}

class Base extends AbstractBase
{
    public function __construct(Used $used)
    {
        $this->used = $used;
    }
}
