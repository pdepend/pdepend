<?php
interface pkg2FooI extends pkg1FooI {

}

abstract class pkg2Bar extends pkg1Bar {
    public static function doIt(Bar $foo = null)
    {
        $foo = new pkg1Foobar();
    }
}

class pkg2Foobar extends pkg1Bar {
    public $bar = null;
    protected static $manager = null;
    
    /**
     * Command manager singleton method which returns a configured instance
     * or <b>null</b>.
     *
     * @return pkg2Foobar
     */
    public static function get()
    {
        return self::$manager;
    }
}

class pkg2Barfoo extends pkg1Bar implements pkg1FooI {
    private $foo = null;
}