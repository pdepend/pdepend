<?php
class Foo
{
    public function bar()
    {
        return $this->biz(default: 'bar');
    }

    public function biz($key = 'main', $default = null)
    {

    }
}
