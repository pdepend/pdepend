<?php
/**
 * @package package2
 */
interface pkg2FooI extends pkg1FooI {

}

/**
 * @package package2
 */
abstract class pkg2Bar extends pkg1Bar {
    public static function doIt(Bar $foo = null)
    {
        $foo = new pkg1Foobar();
    }
}

/**
 * @package package2
 */
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

/**
 * @package package2
 */
class pkg2Barfoo extends pkg1Bar implements pkg1FooI {
    private $foo = null;
}