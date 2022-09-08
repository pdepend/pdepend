<?php

class Foo
{
    public function bar()
    {
        $class = 'stdClass';

        $object1 = new $class;

        $object2 = new ($class);

        $object3 = new ('stdClass');

        return [$object1, $object2, $object3];
    }
}
