<?php
interface pkg1FooI {
    
}

class pkg1Foo implements pkg1FooI {
    public $foo = null;
}

abstract class pkg1Bar {
    abstract function xyz(pkg2Bar $x);
}

class pkg1Foobar extends pkg1Bar implements pkg3FooI {
    private $foo = null;
}

class pkg1Barfoo extends pkg1Bar implements pkg1FooI {
    public function foo()
    {
        new pkg2Bar();//::doIt();
    }
    
    public function bar(pkg2FooI $fx)
    {
        new pkg2Bar();
    }
}
