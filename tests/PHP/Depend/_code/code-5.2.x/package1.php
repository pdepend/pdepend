<?php
/**
 * @package package1
 */
interface pkg1FooI {
    
}

/**
 * @package package1
 */
class pkg1Foo implements pkg1FooI {
    public $foo = null;
}

/**
 * @package package1
 */
abstract class pkg1Bar {
    abstract function xyz(pkg2Bar $x);
}

/**
 * @package package1
 */
class pkg1Foobar extends pkg1Bar implements pkg3FooI {
    private $foo = null;
}

/**
 * @package package1
 */
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

/**
 * @package package1
 */
//function foobar2($x){     pkg2Barfoo::xyz($x);}
